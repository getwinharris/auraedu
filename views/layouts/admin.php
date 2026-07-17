<?php $_SESSION['csrf_token'] ??= bin2hex(random_bytes(16)); $csrf = $_SESSION['csrf_token']; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($pageTitle ?? 'Admin') ?> — AuraEdu</title>
<meta name="robots" content="noindex, nofollow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;450;500;600&display=swap" rel="stylesheet">
<link rel="manifest" href="/admin/manifest.json">
<meta name="theme-color" content="#010102">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="AuraEdu Admin">
<link rel="apple-touch-icon" href="/assets/images/auraedu-favicon.svg">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
<nav class="admin-nav" id="admin-nav">
  <div class="admin-nav__header">
    <div class="admin-nav__brand">
      <div class="admin-nav__brand-accent"></div>
      AuraEdu
    </div>
  </div>

  <div class="admin-nav__section">PRODUCT &amp; BUILD</div>
  <a href="/admin" class="admin-nav__link <?= ($_SERVER['REQUEST_URI'] === '/admin' ? 'admin-nav__link--active' : '') ?>">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg> Dashboard
  </a>
  <button type="button" class="admin-nav__link admin-nav__toggle" data-toggle="menu-catalog" aria-expanded="true">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg> Catalog
    <svg class="admin-nav__chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
  </button>
  <div id="menu-catalog" class="admin-submenu">
    <a href="/admin/products" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/products') === 0 ? 'admin-nav__link--active' : '') ?>">Products</a>
    <a href="/admin/categories" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/categories') === 0 ? 'admin-nav__link--active' : '') ?>">Categories</a>
    <a href="/admin/coupons" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/coupons') === 0 ? 'admin-nav__link--active' : '') ?>">Coupons</a>
  </div>

  <button type="button" class="admin-nav__link admin-nav__toggle" data-toggle="menu-services" aria-expanded="true">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 000 20 14.5 14.5 0 000-20"/><path d="M2 12h20"/></svg> Services
    <svg class="admin-nav__chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
  </button>
  <div id="menu-services" class="admin-submenu">
    <a href="/admin/appointments" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/appointments') === 0 ? 'admin-nav__link--active' : '') ?>">Sessions</a>
    <a href="/admin/temples" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/temples') === 0 ? 'admin-nav__link--active' : '') ?>">Hospital Facilities</a>
  </div>

  <button type="button" class="admin-nav__link admin-nav__toggle" data-toggle="menu-commerce" aria-expanded="true">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg> Commerce
    <svg class="admin-nav__chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
  </button>
  <div id="menu-commerce" class="admin-submenu">
    <a href="/admin/orders" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/orders') === 0 ? 'admin-nav__link--active' : '') ?>">Orders</a>
    <a href="/admin/shipping" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/shipping') === 0 ? 'admin-nav__link--active' : '') ?>">Shipping</a>
    <a href="/admin/tax-report" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/tax-report') === 0 ? 'admin-nav__link--active' : '') ?>">Tax Report</a>
    <a href="/admin/contact-submissions" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/contact-submissions') === 0 ? 'admin-nav__link--active' : '') ?>">Contacts</a>
    <a href="/admin/support-tickets" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/support-tickets') === 0 ? 'admin-nav__link--active' : '') ?>">Support</a>
    <a href="/admin/email-inbox" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/email-inbox') === 0 ? 'admin-nav__link--active' : '') ?>">Email Inbox</a>
    <a href="/admin/email-outbox" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/email-outbox') === 0 ? 'admin-nav__link--active' : '') ?>">Email Outbox</a>
  </div>

  <div class="admin-nav__section">Appearance</div>
  <button type="button" class="admin-nav__link admin-nav__toggle" data-toggle="menu-appearance" aria-expanded="true">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> Appearance
    <svg class="admin-nav__chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
  </button>
  <div id="menu-appearance" class="admin-submenu">
    <a href="/admin/media" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/media') === 0 ? 'admin-nav__link--active' : '') ?>">Media Library</a>
    <a href="/admin/appearance" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/appearance') === 0 ? 'admin-nav__link--active' : '') ?>">Logo &amp; Favicon</a>
  </div>

  <div class="admin-nav__section">Settings</div>
  <button type="button" class="admin-nav__link admin-nav__toggle" data-toggle="menu-settings" aria-expanded="true">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 00-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82.33 1.65 1.65 0 00-.33 1.82v.1a2 2 0 01-4 0v-.1a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 00-2.83-2.83l.06.06a1.65 1.65 0 00-1.82-.33H4a2 2 0 010-4h.1A1.65 1.65 0 006 13.4a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 0010 8.6V4a2 2 0 014 0v.1a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 002.83 2.83l-.06-.06A1.65 1.65 0 0018 10.6h.1a2 2 0 010 4z"/></svg> Settings
    <svg class="admin-nav__chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="6 9 12 15 18 9"/></svg>
  </button>
  <div id="menu-settings" class="admin-submenu">
    <a href="/admin/environment" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/environment') === 0 ? 'admin-nav__link--active' : '') ?>">Environment</a>
    <a href="/admin/integrations" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/integrations') === 0 ? 'admin-nav__link--active' : '') ?>">Integrations</a>
    <a href="/admin/settings" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/settings') === 0 ? 'admin-nav__link--active' : '') ?>">Site Settings</a>
    <a href="/admin/backups" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/backups') === 0 ? 'admin-nav__link--active' : '') ?>">Backups</a>
    <a href="/admin/audit-log" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/audit-log') === 0 ? 'admin-nav__link--active' : '') ?>">Audit Log</a>
  </div>

  <div class="admin-nav__divider"></div>

  <div class="admin-nav__section admin-nav__section--agents">AGENTS &amp; WORKSPACE</div>
  <a href="/admin/workspace" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/workspace') === 0 ? 'admin-nav__link--active' : '') ?>">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg> Workspace
  </a>
  <a href="/admin/agent" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/agent') === 0 ? 'admin-nav__link--active' : '') ?>">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 014 4v2a4 4 0 01-8 0V6a4 4 0 014-4z"/><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> AI Agent
  </a>
  <a href="/admin/terminal" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/terminal') === 0 ? 'admin-nav__link--active' : '') ?>">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="4 17 10 11 4 5"/><line x1="12" y1="19" x2="20" y2="19"/></svg> Terminal
  </a>
  <a href="/admin/developer/project-map" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/developer/project-map') === 0 ? 'admin-nav__link--active' : '') ?>">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg> Project Map
  </a>
  <a href="/admin/developer/workflow" class="admin-nav__link <?= (strpos($_SERVER['REQUEST_URI'], '/admin/developer/workflow') === 0 ? 'admin-nav__link--active' : '') ?>">
    <svg class="admin-nav__icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg> Workflow
  </a>

  <div class="admin-nav__footer">
    <div class="admin-nav__footer-avatar">A</div>
    <div class="admin-nav__footer-links">
      <a href="/" class="admin-nav__link">View Site</a>
      <a href="/logout" class="admin-nav__link">Logout</a>
    </div>
  </div>
</nav>

<div class="admin-main">
  <div class="admin-topbar">
    <div style="display:flex;align-items:center;gap:12px;">
      <button class="btn btn-ghost btn-sm" id="sidebarToggle" style="display:none;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
      <div class="admin-topbar__title"><?= e($pageTitle ?? 'Dashboard') ?></div>
      <button class="admin-topbar__search" id="cmdPaletteTrigger" title="Search (⌘K)">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <span style="flex:1;text-align:left;color:var(--color-ink-tertiary);">Search...</span>
        <kbd>⌘K</kbd>
      </button>
    </div>
    <div class="admin-topbar__actions">
      <a href="/" class="btn btn-ghost btn-sm" target="_blank">View Site</a>
      <a href="/logout" class="btn btn-ghost btn-sm">Logout</a>
    </div>
  </div>

  <div class="breadcrumb">
    <a href="/admin">Dashboard</a>
    <?php
    $__path = parse_url($_SERVER['REQUEST_URI'] ?? '/admin', PHP_URL_PATH);
    $__segments = array_values(array_filter(explode('/', $__path)));
    $__crumb = '';
    $__labelMap = [
      'products'=>'Products','categories'=>'Categories','coupons'=>'Coupons',
      'orders'=>'Orders','shipping'=>'Shipping','tax-report'=>'Tax Report',
      'appointments'=>'Sessions','temples'=>'Hospital Facilities',
      'settings'=>'Site Settings','integrations'=>'Integrations',
      'backups'=>'Backups','audit-log'=>'Audit Log',
      'contact-submissions'=>'Contacts','support-tickets'=>'Support',
      'email-inbox'=>'Email Inbox','email-outbox'=>'Email Outbox',
      'appearance'=>'Logo & Favicon','media'=>'Media Library',
      'environment'=>'Environment','developer'=>'Developer',
      'project-map'=>'Project Map',
    ];
    foreach ($__segments as $__i => $__seg):
      if ($__seg === 'admin' && $__i === 0) continue;
      $__crumb .= '/' . $__seg;
      $__label = $__labelMap[$__seg] ?? ucwords(str_replace(['-','_'], ' ', $__seg));
      if ($__i === count($__segments) - 1): ?>
        <span>/ <?= e($__label) ?></span>
      <?php else: ?>
        <a href="<?= e($__crumb) ?>">/ <?= e($__label) ?></a>
      <?php endif;
    endforeach; ?>
  </div>

  <div class="admin-content">
  <?php
  $__flash = $_SESSION['flash'] ?? null;
  if ($__flash):
    $__msg = is_array($__flash) ? $__flash['message'] : $__flash;
    $__type = is_array($__flash) ? ($__flash['type'] ?? 'info') : 'info';
    unset($_SESSION['flash']);
  ?>
  <div class="flash flash-<?= e($__type) ?>"><?= e($__msg) ?></div>
  <?php endif; ?>
  <?php require $viewFile; ?>
  </div>
</div>

<script>
'use strict';
var sidebar=document.getElementById('admin-nav'),toggle=document.getElementById('sidebarToggle');
if(toggle){toggle.style.display='inline-flex';toggle.addEventListener('click',function(){sidebar.style.display=sidebar.style.display==='flex'?'none':'flex';});}
document.querySelectorAll('[data-toggle]').forEach(function(b){b.addEventListener('click',function(){var m=document.getElementById(this.dataset.toggle);if(m){var e=this.getAttribute('aria-expanded')==='true';this.setAttribute('aria-expanded',String(!e));m.style.display=e?'none':'';}});});
if('serviceWorker'in navigator){navigator.serviceWorker.register('/admin/sw.js',{scope:'/admin/'}).catch(function(){});}
</script>
</body>
</html>
