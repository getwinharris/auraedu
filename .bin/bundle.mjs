#!/usr/bin/env node
import { execSync } from 'child_process';
import { existsSync, mkdirSync, cpSync, readFileSync, writeFileSync } from 'fs';
import { join, dirname } from 'path';
import { fileURLToPath } from 'url';

const ROOT = dirname(fileURLToPath(import.meta.url));
const PROJECT = join(ROOT, '..');
const DIST = join(PROJECT, '.dist');

function run(cmd, opts = {}) {
  console.log(`\n> ${cmd}`);
  execSync(cmd, { cwd: PROJECT, stdio: 'inherit', ...opts });
}

function bundleCSS() {
  console.log('Bundling CSS...');
  const cssDir = join(PROJECT, 'assets', 'css');
  const files = ['band.css'];
  const bundle = [];
  for (const f of files) {
    const path = join(cssDir, f);
    if (existsSync(path)) {
      bundle.push(`/* ${f} */\n${readFileSync(path, 'utf-8')}`);
    }
  }
  mkdirSync(join(DIST, 'css'), { recursive: true });
  writeFileSync(join(DIST, 'css', 'bundle.css'), bundle.join('\n\n'));
  console.log(`  wrote ${join(DIST, 'css', 'bundle.css')} (${bundle.join('\n\n').length} bytes)`);
}

function bundleJS() {
  console.log('Bundling JS...');
  const jsDir = join(PROJECT, 'assets', 'js');
  const dist = join(DIST, 'js');
  mkdirSync(dist, { recursive: true });
  if (existsSync(jsDir)) {
    cpSync(jsDir, dist, { recursive: true });
  }
  console.log(`  copied JS assets`);
}

function ttsDownload() {
  console.log('Downloading KittenTTS model...');
  const ttsDir = join(PROJECT, 'storage', 'kittentts');
  mkdirSync(ttsDir, { recursive: true });
  const url = 'https://huggingface.co/kittentts/model_quantized.onnx';
  console.log(`  Model: ${url}`);
  console.log('  Download manually from Hugging Face (requires auth).');
  console.log(`  Place model_quantized.onnx in ${ttsDir}/`);
}

function agentStart() {
  console.log('Starting cloud agent runtime...');
  const pidFile = join(PROJECT, '.tmp', 'agent.pid');
  mkdirSync(join(PROJECT, '.tmp'), { recursive: true });
  const logFile = join(PROJECT, '.tmp', 'agent.log');
  const cmd = `php ${join(PROJECT, 'cli', 'bapXaura')} agent:listen > ${logFile} 2>&1 & echo $!`;
  try {
    const pid = execSync(cmd, { cwd: PROJECT, shell: true }).toString().trim();
    writeFileSync(pidFile, pid);
    console.log(`  Agent runtime started (PID: ${pid})`);
    console.log(`  Log: ${logFile}`);
  } catch (e) {
    console.error('  Failed to start agent:', e.message);
  }
}

function agentStop() {
  const pidFile = join(PROJECT, '.tmp', 'agent.pid');
  if (!existsSync(pidFile)) {
    console.log('  No agent PID file found');
    return;
  }
  try {
    const pid = readFileSync(pidFile, 'utf-8').trim();
    execSync(`kill ${pid} 2>/dev/null || true`);
    console.log(`  Agent runtime stopped (PID: ${pid})`);
  } catch (e) {
    console.error('  Failed to stop:', e.message);
  }
}

function build() {
  bundleCSS();
  bundleJS();
  console.log('\nBuild complete.');
}

const cmd = process.argv[2] || 'build';
switch (cmd) {
  case 'css':
    bundleCSS();
    break;
  case 'js':
    bundleJS();
    break;
  case 'build':
    build();
    break;
  case 'tts-download':
    ttsDownload();
    break;
  case 'agent-start':
    agentStart();
    break;
  case 'agent-stop':
    agentStop();
    break;
  default:
    console.log(`Usage: node .bin/bundle.mjs [command]
Commands:
  css           Bundle CSS assets
  js            Bundle JS assets
  build         Bundle everything
  tts-download  Download KittenTTS model
  agent-start   Start cloud agent runtime
  agent-stop    Stop cloud agent runtime
`);
}
