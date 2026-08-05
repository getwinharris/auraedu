---
name: remotedb
description: Use when reading or writing hosted MySQL through the /remotedb HTTP API — querying records, upserting, deleting, or inspecting schema from outside the live server.
---

# remotedb

The HTTP bridge to hosted MySQL. Every behaviour below was verified against
production; the responses shown are real.

## When it is used

`DatabaseService::isRemote()` probes for a direct MySQL socket first:

| Environment | MySQL reachable | Path |
|---|---|---|
| Live Hostinger server | yes | direct PDO — never calls its own endpoint |
| Any dev machine | no | HTTP POST to `<APP_URL>/remotedb` |

`RemoteDbController` builds its store with `new DatabaseService(true)` (`forceDirect`),
so the bridge always terminates at MySQL and cannot recurse into itself. A localhost
`/remotedb` is meaningless — an instance only reaches for the bridge because it has no
local database.

## Endpoint and auth

```
POST https://auraedu.co.in/remotedb
Content-Type: application/json
```

Authentication is the **remote database password** (`REMOTE_DB_PASSWORD` in
`.env`, surfaced as the `remote_db_password` secret via `SecretService`), sent as
`password` in the body or as `Authorization: Bearer <password>`. There is no
separate token — that was removed precisely because a second secret drifts out of
sync.

> **Known gap:** this controller currently **fails open** — if `remote_db_password`
> is empty the request is allowed through. The upstream contract was to fail closed
> with a 503. Do not rely on an empty password as protection; configure
> `REMOTE_DB_PASSWORD` before enabling the endpoint.

```
wrong password → 401
correct        → 200
```

## Reading

Only `SELECT`, `SHOW`, `DESCRIBE` and `EXPLAIN` are accepted. Anything else is
rejected — writes must use the mutation actions below.

```bash
PASS=$(grep '^REMOTE_DB_PASSWORD=' .env | cut -d= -f2-)

curl -s -X POST https://auraedu.co.in/remotedb \
  -H 'Content-Type: application/json' \
  -d "{\"query\":\"SELECT COUNT(*) c FROM products\",\"password\":\"$PASS\"}"
# {"success":true,"data":[{"c":7}]}
```

Attempting a write through `query` is refused:

```
{"query":"UPDATE products SET id=id"}  →  {"error":"Only read queries are allowed"}
```

### Records are JSON in `_data`

Every collection stores a canonical `id` column plus a `_data` JSON blob. To read
fields, extract them:

```sql
SELECT id,
       JSON_UNQUOTE(JSON_EXTRACT(_data,'$.name'))  AS name,
       JSON_UNQUOTE(JSON_EXTRACT(_data,'$.price')) AS price
FROM products
```

Parameters use `?` placeholders with a `params` array.

## Writing

Three actions. All require `collection`, and all return `{"success":true}`.

### upsert — create or update one record

`record.id` is **required**; without it the call is rejected rather than silently
inserting a duplicate.

```bash
-d '{"action":"upsert","collection":"categories",
     "record":{"id":"cat-ring","slug":"ring","name":"Ring"},
     "password":"'"$PASS"'"}'
# {"success":true,"record":{...}}
```

Send the **whole record**, not a patch: the server merges over the existing row, so
omitted keys survive, but sending a partial record you built by hand risks losing
fields you never read.

### delete — remove one record by id

```bash
-d '{"action":"delete","collection":"categories","id":"cat-ring","password":"'"$PASS"'"}'
```

### replace — overwrite an entire collection

```bash
-d '{"action":"replace","collection":"categories","records":[...],"password":"'"$PASS"'"}'
# {"success":true,"count":2}
```

Destructive: it deletes every row and reinserts. Use `upsert` per record unless you
genuinely intend to replace the whole collection.

## Errors

| Response | Meaning |
|---|---|
| `Only read queries are allowed` | a write was sent through `query` |
| `Collection is required` | `collection` missing or non-alphabetic |
| `Record id is required.` | `upsert` without `record.id`, or `delete` without `id` |
| `Unsupported mutation action.` | action is not upsert/delete/replace |
| 400 `Only read queries are allowed` | `query` does not start with SELECT/SHOW/DESCRIBE/EXPLAIN |
| 401 | wrong or missing password |
| 500 `Query failed` | transport or query error — a failure is never returned as an empty `[]` |

`action=init` creates the MySQL tables from `storage/schema/collections.php`; it
is separate from the read/mutate verbs.

`DatabaseService` throws on transport failure, non-200 and malformed responses. It
never converts a failure into an empty array — a silent `[]` once made an empty
catalogue look like valid data.

## Rules

- Read before you write. Fetch the record, change what you mean to change, send it back.
- Bulk edits: prefer many `upsert` calls over one `replace`.
- The endpoint is **flaky under load**; a mutation can fail transiently. Retry with a
  short backoff and verify the final state with a read rather than trusting the
  response.
- Blog Markdown and image binaries are **not** in MySQL. Everything else admin-editable is.
- Never paste the password into a file, a commit, or a URL query string.

## Testing

Verify against production with a read before and after any write, and confirm the
count changed as expected. A mutation that returns `success` is not proof — the
endpoint has returned success while a concurrent failure left data unchanged.

## Keeping this skill current

Update when `RemoteDbController` gains or loses an action, when the authentication
source changes, or when the allowed read verbs change. The controller is the authority;
this file documents it.
