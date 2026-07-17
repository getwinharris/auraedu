<div class="admin-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:var(--space-sm);">
        <h2 style="font-size:1.1rem; margin:0;">bapXaura Terminal</h2>
        <div style="display:flex; gap:8px; align-items:center;">
            <span style="font-size:0.8rem; color:var(--color-text-muted);" id="ai-status">AI: <?= empty($modelConfig['api_key']) ? 'not configured' : 'ready' ?></span>
            <button class="btn btn-sm" onclick="clearTerminal()" style="font-size:0.8rem;">Clear</button>
            <label style="display:flex; align-items:center; gap:6px; font-size:0.8rem; cursor:pointer;">
                <input type="checkbox" id="tts-mute-toggle" onchange="ttsMuted = this.checked" style="width:16px;height:16px;">
                Mute TTS
            </label>
        </div>
    </div>
    <p style="margin:0 0 var(--space-md); color:var(--color-text-muted); font-size:0.85rem;">
        Run bapXaura commands, query the AI agent, or execute npm scripts.
        Prefix with <code>ai: </code> to chat with the agent.
    </p>
</div>

<input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
<div class="admin-card" style="padding:0;">
    <div id="terminal-output" style="background:#0a0a0a; color:#00d4a4; font-family:'SF Mono','Fira Code','Courier New',monospace; font-size:0.8rem; line-height:1.5; padding:var(--space-md); min-height:300px; max-height:500px; overflow-y:auto; white-space:pre-wrap; word-break:break-all;">
        <span style="color:#666;">╔══════════════════════════════════════════════╗</span>
        <span style="color:#666;">║  bapXaura Terminal — Admin CLI              ║</span>
        <span style="color:#666;">╚══════════════════════════════════════════════╝</span>
        <span style="color:#888;">Type a command or 'ai: &lt;message&gt;' to begin.</span>
    </div>
    <div style="display:flex; border-top:1px solid var(--color-border);">
        <span id="prompt-label" style="background:var(--color-surface); color:var(--color-accent); padding:8px 4px 8px 12px; font-family:monospace; font-size:0.85rem; white-space:nowrap;">bapX&gt;</span>
        <input type="text" id="terminal-input" placeholder="e.g. map, schema list, ai: how many users?" autofocus
               style="flex:1; border:none; outline:none; padding:8px; font-family:'SF Mono','Fira Code','Courier New',monospace; font-size:0.85rem; background:var(--color-canvas);"
               onkeydown="terminalKeydown(event)">
    </div>
</div>

<script>
let commandHistory = [];
let historyIndex = -1;
let ttsMuted = false;
let isRunning = false;

function escapeHtml(s) {
    return (s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function writeOutput(text, className = '') {
    const out = document.getElementById('terminal-output');
    const lines = (text || '').split('\n');
    for (const line of lines) {
        const div = document.createElement('div');
        div.textContent = line;
        if (className) div.className = className;
        out.appendChild(div);
    }
    out.scrollTop = out.scrollHeight;
}

function writeError(text) {
    writeOutput(text, 'terminal-error');
}

function clearTerminal() {
    const out = document.getElementById('terminal-output');
    out.innerHTML = '<span style="color:#666;">── cleared ──</span>';
}

async function terminalKeydown(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        if (isRunning) return;
        const input = document.getElementById('terminal-input');
        const cmd = input.value.trim();
        if (!cmd) return;
        input.value = '';
        historyIndex = -1;
        commandHistory.push(cmd);
        writeOutput('bapX> ' + cmd);
        isRunning = true;
        if (cmd.startsWith('ai:') || cmd.startsWith('ai ')) {
            await runAiCommand(cmd.substring(3).trim());
        } else if (cmd === 'clear' || cmd === 'cls') {
            clearTerminal();
        } else if (cmd === 'help') {
            writeOutput('Commands: any bapXaura command, ai: <message>, npm <args>, clear');
        } else {
            await runBapXCommand(cmd);
        }
        isRunning = false;
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        if (commandHistory.length === 0) return;
        historyIndex = Math.min(historyIndex + 1, commandHistory.length - 1);
        document.getElementById('terminal-input').value = commandHistory[commandHistory.length - 1 - historyIndex];
    } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        if (historyIndex <= 0) {
            historyIndex = -1;
            document.getElementById('terminal-input').value = '';
        } else {
            historyIndex--;
            document.getElementById('terminal-input').value = commandHistory[commandHistory.length - 1 - historyIndex];
        }
    } else if (e.key === 'l' && e.ctrlKey) {
        e.preventDefault();
        clearTerminal();
    }
}

async function runBapXCommand(cmd) {
    writeOutput('⏳ running...', 'terminal-pending');
    try {
        const resp = await fetch('/admin/terminal/run', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                command: cmd,
                _csrf: document.querySelector('input[name="_csrf"]')?.value || ''
            })
        });
        const data = await resp.json();
        if (data.error) {
            writeError('Error: ' + data.error);
        } else if (data.output) {
            writeOutput(data.output);
        }
        if (data.exit_code !== undefined && data.exit_code !== 0 && data.exit_code !== null) {
            writeOutput('exit code: ' + data.exit_code, 'terminal-exit');
        }
    } catch (err) {
        writeError('Network error: ' + err.message);
    }
}

async function runAiCommand(message) {
    writeOutput('⏳ thinking...', 'terminal-pending');
    try {
        const resp = await fetch('/admin/agent/ask', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(message) + '&_csrf=' + encodeURIComponent(document.querySelector('input[name="_csrf"]')?.value || '')
        });
        const data = await resp.json();
        if (data.error) {
            writeError('Error: ' + data.error);
        } else if (data.answer) {
            writeOutput(data.answer);
        }
    } catch (err) {
        writeError('Network error: ' + err.message);
    }
}
</script>
