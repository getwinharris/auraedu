# Session Memory

Continuous JSON conversation log at `.tmp/session.json`.

## Purpose

Captures every user input prompt and CTO reply as a continuous, append-only JSON array — like a WhatsApp conversation with timestamps and attachments.

## Format

```json
{
  "session_id": "ses_abc123",
  "created_at": "2026-07-16T12:00:00Z",
  "updated_at": "2026-07-16T12:00:05Z",
  "entries": [
    {"id": 1, "role": "user",     "content": "prompt text",   "timestamp": "...", "attachments": []},
    {"id": 2, "role": "cto",      "content": "response text", "timestamp": "...", "attachments": []}
  ]
}
```

Roles: `user` = input prompt, `cto` = handler response, `question` = tool question, `answer` = user reply.

## Commands

| Command | Description |
|---|---|
| `bapXaura memory init` | Initialize `.tmp/session.json` |
| `bapXaura memory add <role> <content>` | Append entry |
| `bapXaura memory list` | Show all entries (summary) |
| `bapXaura memory search <text>` | Search entries by content |
| `bapXaura memory last [n]` | Show last n entries |
| `bapXaura memory export` | Dump full JSON |
| `bapXaura memory export-jsonl` | Dump as JSONL (one JSON per line) |
| `bapXaura memory info` | Session stats |
| `bapXaura memory clear` | Erase session memory |

## Usage

1. At session start: `bapXaura memory init`
2. Log each user prompt: `bapXaura memory add user "<exact prompt>"`
3. Log each CTO response: `bapXaura memory add cto "<response summary>"`
4. Search later: `bapXaura memory search "<keyword>"`
5. Fork/export to new session: `bapXaura memory export > context.json`

## Important

- The JSON file is append-friendly and human-readable
- Attachments array can hold `{"name": "...", "path": "..."}` objects
- Previous sessions persist — `memory init` creates a fresh file
- Use `memory search` with exact user prompt text for reliable lookup
