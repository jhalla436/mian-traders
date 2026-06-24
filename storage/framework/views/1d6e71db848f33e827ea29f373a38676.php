<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title><?php echo e(config('app.name', 'Mian Traders')); ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <style>
    /* ═══════════════════════════════════════════════════
       MIAN TRADERS — DESIGN SYSTEM
       ═══════════════════════════════════════════════════ */

    /* ── Reset & Base ───────────────────────────── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Inter', system-ui, -apple-system, sans-serif;
      background: #f1f5f9;
      color: #0f172a;
      font-size: 14px;
      line-height: 1.5;
      -webkit-font-smoothing: antialiased;
    }
    select, input, button, textarea { font-family: inherit; font-size: inherit; }
    a { color: #4f46e5; text-decoration: none; }
    a:hover { color: #6366f1; }

    /* ── Layout ─────────────────────────────────── */
    .mt-app { display: flex; min-height: 100vh; }

    /* ── Sidebar ────────────────────────────────── */
    .mt-sidebar {
      width: 264px; min-width: 264px;
      background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
      color: #fff;
      position: sticky; top: 0; height: 100vh;
      display: flex; flex-direction: column;
      z-index: 2000; overflow: visible;
      transition: all 0.3s cubic-bezier(.4,0,.2,1);
    }
    .mt-sidebar.collapsed { width: 80px; min-width: 80px; }

    .mt-sidebar-toggle {
      width: 32px; height: 32px;
      background: #4f46e5; color: #fff;
      border-radius: 50%; border: 2.5px solid #fff;
      position: absolute; right: -16px; top: 36px;
      display: flex; align-items: center; justify-content: center;
      cursor: pointer; z-index: 2100; transition: all 0.3s cubic-bezier(.4,0,.2,1);
      box-shadow: 0 4px 14px rgba(0,0,0,0.3);
    }
    .mt-sidebar-toggle:hover { background: #4338ca; transform: scale(1.15); box-shadow: 0 6px 20px rgba(79, 70, 229, 0.5); }
    .mt-sidebar.collapsed .mt-sidebar-toggle { right: 50%; transform: translateX(50%); top: 72px; }
    .mt-sidebar.collapsed .mt-sidebar-toggle:hover { transform: translateX(50%) scale(1.15); }
    .mt-sidebar.collapsed .mt-sidebar-toggle svg { transform: rotate(180deg); }
    
    /* Subtle pulse to guide the user */
    @keyframes toggle-pulse {
      0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.6); }
      70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); }
      100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
    }
    .mt-sidebar-toggle { animation: toggle-pulse 2s infinite; }
    .mt-sidebar-toggle:hover { animation: none; }
    .mt-sidebar-inner {
      flex: 1; overflow-y: auto; overflow-x: hidden;
      padding: 0 14px 20px 14px;
      scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.12) transparent;
    }
    .mt-sidebar-inner::-webkit-scrollbar { width: 4px; }
    .mt-sidebar-inner::-webkit-scrollbar-track { background: transparent; }
    .mt-sidebar-inner::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.15); border-radius: 4px; }

    .mt-brand { padding: 20px 14px 12px 14px; display: flex; align-items: center; gap: 12px; flex-shrink: 0; transition: all 0.3s; }
    .mt-sidebar.collapsed .mt-brand { padding: 20px 0; justify-content: center; }
    .mt-brand-icon {
      width: 40px; height: 40px;
      background: linear-gradient(135deg, #6366f1, #a855f7);
      border-radius: 12px; display: flex; align-items: center; justify-content: center;
      font-weight: 800; font-size: 18px; color: #fff;
      box-shadow: 0 4px 14px rgba(99,102,241,0.4); flex-shrink: 0;
    }
    .mt-brand-text { font-size: 16px; font-weight: 700; letter-spacing: -0.3px; line-height: 1.2; white-space: nowrap; }
    .mt-sidebar.collapsed .mt-brand-text, 
    .mt-sidebar.collapsed .mt-brand-sub { display: none; }
    .mt-brand-sub { font-size: 11px; color: #94a3b8; font-weight: 400; margin-top: 1px; white-space: nowrap; }

    .mt-role-badge {
      display: inline-flex; align-items: center; gap: 5px;
      background: rgba(99,102,241,0.15); border: 1px solid rgba(99,102,241,0.3);
      color: #a5b4fc; font-size: 11px; font-weight: 600;
      padding: 4px 10px; border-radius: 20px; text-transform: capitalize; margin-bottom: 16px;
    }
    .mt-role-dot { width: 6px; height: 6px; background: #818cf8; border-radius: 50%; animation: pulse-dot 2s infinite; }
    @keyframes pulse-dot { 0%, 100% { opacity:1; transform:scale(1); } 50% { opacity:0.5; transform:scale(0.8); } }

    .mt-shop-card {
      background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08);
      border-radius: 12px; padding: 12px; margin-bottom: 20px; position: relative; overflow: hidden;
      transition: all 0.3s;
    }
    .mt-sidebar.collapsed .mt-shop-card { padding: 8px 4px; }
    .mt-shop-card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background: linear-gradient(90deg, #6366f1, #a855f7, #ec4899); }
    .mt-shop-label { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:1px; color:#64748b; margin-bottom:8px; }
    .mt-sidebar.collapsed .mt-shop-label,
    .mt-sidebar.collapsed .mt-shop-info { display: none; }
    .mt-shop-select {
      width:100%; padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,0.1);
      background: rgba(15,23,42,0.6); color:#e2e8f0; font-size:13px; font-weight:500;
      cursor:pointer; outline:none; appearance:none; -webkit-appearance:none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2394a3b8' viewBox='0 0 16 16'%3E%3Cpath d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
      background-repeat:no-repeat; background-position:right 10px center;
      transition: border-color 0.2s, box-shadow 0.2s;
    }
    .mt-sidebar.collapsed .mt-shop-select { padding: 8px 5px; background-position: center; color: transparent; }
    .mt-shop-select:focus { border-color: rgba(99,102,241,0.5); box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }
    .mt-shop-info { margin-top:8px; font-size:11px; color:#94a3b8; line-height:1.5; }
    .mt-shop-info b { color:#cbd5e1; }

    .mt-section-label { font-size:10px; font-weight:700; text-transform:uppercase; letter-spacing:1.2px; color:#475569; padding:16px 12px 8px 12px; }

    .mt-nav-link {
      display:flex; align-items:center; gap:10px; color:#94a3b8; text-decoration:none;
      padding:9px 12px; border-radius:10px; margin-bottom:2px;
      font-size:13px; font-weight:500; position:relative;
      transition: all 0.2s cubic-bezier(.4,0,.2,1); overflow:hidden;
      white-space: nowrap;
    }
    .mt-sidebar.collapsed .mt-nav-link { padding: 9px 0; justify-content: center; gap: 0; }
    .mt-sidebar.collapsed .mt-nav-link span { display: none; }
    .mt-nav-link:hover { color:#e2e8f0; background:rgba(255,255,255,0.06); transform:translateX(2px); }
    .mt-sidebar.collapsed .mt-nav-link:hover { transform: scale(1.05); }
    .mt-nav-link.active {
      color:#fff; background: linear-gradient(135deg, rgba(99,102,241,0.2), rgba(168,85,247,0.15));
      box-shadow: inset 0 0 0 1px rgba(99,102,241,0.25);
    }
    .mt-nav-link.active::before {
      content:''; position:absolute; left:0; top:50%; transform:translateY(-50%);
      width:3px; height:20px; background: linear-gradient(180deg, #6366f1, #a855f7); border-radius:0 4px 4px 0;
    }
    .mt-nav-link .mt-nav-icon { width:20px; height:20px; flex-shrink:0; opacity:0.6; transition:opacity 0.2s; }
    .mt-nav-link:hover .mt-nav-icon, .mt-nav-link.active .mt-nav-icon { opacity:1; }
    .mt-sidebar.collapsed .mt-role-badge,
    .mt-sidebar.collapsed .mt-section-label,
    .mt-sidebar.collapsed .mt-logout-btn span { display: none; }
    .mt-sidebar.collapsed .mt-logout-btn { padding: 10px 0; border: none; background: transparent; }

    .mt-sidebar-footer { padding:12px 14px; border-top:1px solid rgba(255,255,255,0.06); flex-shrink:0; }
    .mt-logout-btn {
      width:100%; padding:10px; border:1px solid rgba(255,255,255,0.08); border-radius:10px;
      background:rgba(255,255,255,0.04); color:#94a3b8; cursor:pointer;
      font-size:13px; font-weight:500; display:flex; align-items:center; justify-content:center; gap:8px;
      transition: all 0.2s ease;
    }
    .mt-logout-btn:hover { background:rgba(239,68,68,0.12); border-color:rgba(239,68,68,0.3); color:#fca5a5; }

    .mt-mobile-toggle {
      display:none; position:fixed; top:14px; left:14px; z-index:200;
      width:42px; height:42px; background: linear-gradient(135deg, #6366f1, #4f46e5);
      border:none; border-radius:12px; color:#fff; cursor:pointer;
      align-items:center; justify-content:center;
      box-shadow: 0 4px 14px rgba(99,102,241,0.4); transition: transform 0.2s;
    }
    .mt-mobile-toggle:hover { transform:scale(1.05); }
    .mt-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); backdrop-filter:blur(4px); z-index:90; }

    @media (max-width: 768px) {
      .mt-mobile-toggle { display:flex; }
      .mt-sidebar { position:fixed; transform:translateX(-100%); }
      .mt-sidebar.open { transform:translateX(0); }
      .mt-overlay.open { display:block; }
      .mt-main { padding-top:64px !important; }
    }

    /* ═══════════════════════════════════════════════════
       MAIN CONTENT AREA — Design System Classes
       ═══════════════════════════════════════════════════ */

    .mt-main { flex:1; padding:24px; min-width:0; }

    /* ── Page Header ───────────────────────────── */
    .page-header {
      background: #fff; padding: 16px 20px; border-radius: 14px; margin-bottom: 16px;
      display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap;
      border: 1px solid rgba(0,0,0,0.04);
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .page-title {
      font-size: 20px; font-weight: 800; color: #0f172a;
      letter-spacing: -0.3px; margin: 0;
    }
    .page-subtitle { font-size: 12px; color: #64748b; font-weight: 500; margin-top: 2px; }
    .page-actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }

    /* ── Cards ──────────────────────────────────── */
    .card {
      background: #fff; padding: 20px; border-radius: 14px;
      border: 1px solid rgba(0,0,0,0.04);
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      transition: box-shadow 0.2s ease;
    }
    .card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
    .card + .card { margin-top: 16px; }

    /* ── Stat Cards (Dashboard) ─────────────────── */
    .stat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
    @media (max-width: 1024px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .stat-grid { grid-template-columns: 1fr; } }

    .stat-card {
      background: #fff; padding: 20px; border-radius: 14px;
      border: 1px solid rgba(0,0,0,0.04);
      box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      position: relative; overflow: hidden;
      transition: all 0.25s ease;
    }
    .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .stat-card::before {
      content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
      background: linear-gradient(90deg, #6366f1, #a855f7);
      opacity: 0; transition: opacity 0.25s;
    }
    .stat-card:hover::before { opacity: 1; }
    .stat-label { font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
    .stat-value { font-size: 26px; font-weight: 900; margin-top: 8px; color: #0f172a; letter-spacing: -0.5px; }
    .stat-value.danger { color: #dc2626; }
    .stat-value.success { color: #16a34a; }
    .stat-link { display: inline-flex; align-items: center; gap: 4px; margin-top: 12px; font-size: 12px; font-weight: 600; color: #6366f1; transition: gap 0.2s; }
    .stat-link:hover { gap: 8px; color: #4f46e5; }

    /* ── Buttons ────────────────────────────────── */
    .btn {
      display: inline-flex; align-items: center; gap: 6px;
      padding: 9px 16px; border-radius: 10px; text-decoration: none;
      cursor: pointer; font-weight: 600; font-size: 13px;
      border: none; transition: all 0.2s ease; white-space: nowrap;
    }
    .btn-primary { background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff; }
    .btn-primary:hover { background: linear-gradient(135deg, #818cf8, #6366f1); transform:translateY(-1px); box-shadow:0 4px 12px rgba(99,102,241,0.35); color:#fff; }
    .btn-success { background: linear-gradient(135deg, #22c55e, #16a34a); color: #fff; }
    .btn-success:hover { background: linear-gradient(135deg, #4ade80, #22c55e); transform:translateY(-1px); box-shadow:0 4px 12px rgba(34,197,94,0.35); color:#fff; }
    .btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
    .btn-danger:hover { background: linear-gradient(135deg, #f87171, #ef4444); transform:translateY(-1px); box-shadow:0 4px 12px rgba(239,68,68,0.35); color:#fff; }
    .btn-secondary { background: #1e293b; color: #fff; }
    .btn-secondary:hover { background: #334155; transform:translateY(-1px); box-shadow:0 4px 12px rgba(30,41,59,0.25); color:#fff; }
    .btn-outline { background: transparent; border: 1px solid #e2e8f0; color: #475569; }
    .btn-outline:hover { border-color: #6366f1; color: #6366f1; background: rgba(99,102,241,0.04); }
    .btn-teal { background: linear-gradient(135deg, #14b8a6, #0d9488); color: #fff; }
    .btn-teal:hover { background: linear-gradient(135deg, #2dd4bf, #14b8a6); transform:translateY(-1px); box-shadow:0 4px 12px rgba(20,184,166,0.35); color:#fff; }
    .btn-sm { padding: 6px 12px; font-size: 12px; border-radius: 8px; }
    .btn-xs { padding: 4px 10px; font-size: 11px; border-radius: 6px; }

    /* ── Tables ─────────────────────────────────── */
    .table-card { background:#fff; border-radius:14px; border:1px solid rgba(0,0,0,0.04); box-shadow:0 1px 3px rgba(0,0,0,0.04); overflow:hidden; }
    .mt-table { width:100%; border-collapse:collapse; }
    .mt-table thead { background: #f8fafc; }
    .mt-table th {
      text-align: left; padding: 12px 16px; font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.6px; color: #64748b;
      border-bottom: 2px solid #e2e8f0;
    }
    .mt-table th.text-right { text-align: right; }
    .mt-table td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; color: #334155; font-size: 13px; vertical-align: middle; }
    .mt-table td.text-right { text-align: right; }
    .mt-table td.font-bold { font-weight: 700; color: #0f172a; }
    .mt-table td.font-semibold { font-weight: 600; }
    .mt-table td.text-danger { color: #dc2626; font-weight: 700; }
    .mt-table td.text-muted { color: #94a3b8; font-size: 12px; }
    .mt-table tbody tr { transition: background 0.15s; }
    .mt-table tbody tr:hover { background: #f8fafc; }
    .mt-table tbody tr:last-child td { border-bottom: none; }
    .mt-table .empty-row td { padding: 32px 16px; text-align: center; color: #94a3b8; font-size: 13px; }
    .mt-table .actions { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
    .mt-table .actions form { display: inline-flex; }

    /* ── Forms ──────────────────────────────────── */
    .mt-input, .mt-select, .mt-textarea {
      width: 100%; padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 10px;
      background: #fff; color: #0f172a; font-size: 13px; font-weight: 500;
      transition: border-color 0.2s, box-shadow 0.2s; outline: none;
    }
    .mt-input:focus, .mt-select:focus, .mt-textarea:focus {
      border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    .mt-input::placeholder { color: #94a3b8; font-weight: 400; }
    .mt-select {
      appearance: none; -webkit-appearance: none; cursor: pointer;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 12px center;
      padding-right: 32px;
    }
    .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    @media (max-width: 640px) { .form-grid { grid-template-columns: 1fr; } }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-label { font-size: 12px; font-weight: 600; color: #475569; letter-spacing: 0.2px; }
    .form-error { color: #dc2626; font-size: 11px; font-weight: 500; margin-top: 2px; }
    .form-stack { display: grid; gap: 16px; }
    .form-checkbox { display: flex; gap: 8px; align-items: center; cursor: pointer; font-size: 13px; color: #475569; }
    .form-checkbox input[type="checkbox"] {
      width: 18px; height: 18px; border-radius: 5px; accent-color: #6366f1; cursor: pointer;
    }

    /* ── Filter Bar ─────────────────────────────── */
    .filter-bar {
      background: #fff; padding: 16px 20px; border-radius: 14px; margin-bottom: 16px;
      border: 1px solid rgba(0,0,0,0.04); box-shadow: 0 1px 3px rgba(0,0,0,0.04);
      display: flex; gap: 12px; flex-wrap: wrap; align-items: flex-end;
    }
    .filter-group { display: flex; flex-direction: column; gap: 4px; }
    .filter-label { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }

    /* ── Tabs / Pills ──────────────────────────── */
    .mt-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
    .mt-tab {
      padding: 8px 16px; border-radius: 10px; text-decoration: none;
      font-size: 13px; font-weight: 600; transition: all 0.2s;
      border: 1.5px solid #e2e8f0; color: #64748b; background: #fff;
    }
    .mt-tab:hover { border-color: #6366f1; color: #6366f1; background: rgba(99,102,241,0.04); }
    .mt-tab.active { background: #0f172a; color: #fff; border-color: #0f172a; }

    /* ── Badges ─────────────────────────────────── */
    .badge {
      display: inline-flex; align-items: center; gap: 4px;
      padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
      text-transform: uppercase; letter-spacing: 0.3px;
    }
    .badge-success { background: #dcfce7; color: #15803d; }
    .badge-danger { background: #fee2e2; color: #b91c1c; }
    .badge-info { background: #e0e7ff; color: #4338ca; }
    .badge-gray { background: #f1f5f9; color: #64748b; }
    .badge-wa { background: #16a34a; color: #fff; padding: 2px 8px; border-radius: 999px; font-size: 11px; font-weight: 800; }

    /* ── Errors summary ─────────────────────────── */
    .errors-box {
      margin-top: 16px; background: linear-gradient(135deg, #fef2f2, #fee2e2);
      border: 1px solid #fca5a5; padding: 14px 18px; border-radius: 12px;
    }
    .errors-box b { color: #991b1b; }
    .errors-box ul { margin: 8px 0 0 18px; font-size: 13px; color: #7f1d1d; }

    /* ── Alerts ─────────────────────────────────── */
    .mt-alert {
      padding: 12px 16px; border-radius: 12px; margin-bottom: 16px;
      font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 10px;
      animation: slideDown 0.3s ease;
    }
    @keyframes slideDown { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
    .mt-alert-success { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #6ee7b7; color: #065f46; }
    .mt-alert-error { background: linear-gradient(135deg, #fef2f2, #fee2e2); border: 1px solid #fca5a5; color: #991b1b; }

    /* ── POS Layout (viewport-locked, no page scroll) ── */
    .pos-layout {
      display: flex; gap: 8px; align-items: stretch;
      height: calc(100vh - 48px); /* viewport minus mt-main padding */
      overflow: hidden;
    }
    .pos-sidebar {
      width: 160px; flex-shrink: 0;
      overflow-y: auto; overflow-x: hidden;
    }
    .pos-products {
      flex: 1.2; min-width: 0;
      display: flex; flex-direction: column;
      overflow: hidden;
    }
    .pos-products-inner {
      border: 1px solid #ccc; padding: 10px; background: #fff;
      display: flex; flex-direction: column;
      flex: 1; overflow: hidden; min-height: 0;
    }
    .pos-cart {
      flex: 1.5; min-width: 0;
      padding: 0 !important; border: 1px solid #ccc; background: #fff;
      display: flex; flex-direction: column;
      overflow: hidden;
    }
    @media (max-width: 1280px) {
      .pos-layout { flex-wrap: wrap; height: auto; overflow: auto; }
      .pos-products, .pos-cart { flex: 1 1 40%; max-height: 50vh; }
    }
    @media (max-width: 1024px) {
      .pos-layout { flex-direction: column; height: auto; overflow: auto; }
      .pos-cart { width: 100%; max-height: none; }
      .pos-products { max-height: none; }
      .pos-sidebar { width: 100%; }
    }

    .pos-product-list { border: 1px solid #ccc; margin-top: 8px; overflow-y: auto; flex: 1; min-height: 0; }
    .pos-product-header {
      display: grid; grid-template-columns: 1fr 55px 70px; gap: 0;
      background: #eee; padding: 3px 6px; font-size: 10px; font-weight: 700;
      text-transform: uppercase; color: #555;
      border-bottom: 1px solid #ccc;
      position: sticky; top: 0; z-index: 2;
    }
    .pos-product-row {
      display: grid; grid-template-columns: 1fr 55px 70px; gap: 0;
      padding: 3px 6px; border-bottom: 1px solid #eee; align-items: center;
    }
    .pos-product-row:hover { background: #f5f5f5; }
    .pos-product-name { font-weight: 700; color: #000; font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pos-product-meta { font-size: 9px; color: #888; margin-top: 0; }
    .pos-product-price { text-align: right; font-weight: 600; color: #333; font-size: 11px; }
    .pos-product-actions { text-align: right; display: flex; gap: 3px; justify-content: flex-end; flex-wrap: nowrap; }

    .cart-header {
      padding: 6px 10px; background: #eee; border-bottom: 1px solid #ccc;
      display: flex; justify-content: space-between; align-items: center;
      flex-shrink: 0;
    }
    .cart-section-title { font-size: 11px; font-weight: 700; color: #333; text-transform: uppercase; margin: 0; }
    .cart-items-wrap {
      padding: 4px 8px; flex: 1;
      overflow-y: auto; min-height: 0;
    }
    .pos-cart-body {
      display: flex; flex: 1; min-height: 0; overflow: hidden;
    }
    .pos-cart-checkout {
      width: 260px; flex-shrink: 0; padding: 10px;
      background: #fff; border-left: 1px solid #ccc;
      overflow-y: auto;
    }
    .pos-cart-checkout .form-stack { gap: 8px; }
    .pos-cart-checkout .form-grid { grid-template-columns: 1fr; gap: 8px; }
    .pos-cart-checkout .mt-input,
    .pos-cart-checkout .mt-textarea { padding: 7px 10px; font-size: 12px; }

    .pos-cart-row {
      padding: 2px 0; border-bottom: 1px solid #eee;
      display: flex; align-items: center; gap: 4px;
    }
    .pos-cart-row:last-child { border-bottom: none; }
    .pos-cart-row-name {
      font-weight: 700; color: #000; font-size: 10px; line-height: 1.2;
      white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
      flex: 1; min-width: 0;
    }
    .pos-cart-row-controls {
      display: flex; align-items: center; gap: 4px; flex-wrap: nowrap; flex-shrink: 0;
    }
    .pos-cart-inline-input {
      width: 36px; height: 22px; padding: 1px 3px; font-size: 11px; font-weight: 600;
      text-align: center; border: 1px solid #ccc;
      background: #fff; color: #000; outline: none;
    }
    .pos-cart-inline-input:focus { border-color: #333; }
    .pos-cart-profit {
      font-size: 9px; font-weight: 700; color: #080;
      padding: 1px 4px; white-space: nowrap;
    }
    .pos-cart-remove {
      width: 22px; height: 22px; display: flex; align-items: center; justify-content: center;
      border: 1px solid #c00; background: #fee; color: #c00;
      cursor: pointer; font-size: 14px; font-weight: 700;
      padding: 0; flex-shrink: 0;
    }
    .pos-cart-remove:hover { background: #c00; color: #fff; }

    .checkout-section { padding: 10px; background: #fff; border-top: 1px solid #ccc; }
    .checkout-header { margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px dashed #ccc; }

    /* ── Pagination ─────────────────────────────── */
    .pagination-wrap { margin-top: 16px; padding-top: 16px; border-top: 1px solid #f1f5f9; }
    nav[role=navigation] { display: flex; flex-wrap: wrap; gap: 4px; justify-content: center; }
    nav[role=navigation] span, nav[role=navigation] a {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 36px; height: 36px; padding: 0 10px;
      border-radius: 8px; font-size: 13px; font-weight: 600; text-decoration: none;
      border: 1px solid #e2e8f0; color: #475569; background: #fff;
      transition: all 0.15s;
    }
    nav[role=navigation] a:hover { border-color: #6366f1; color: #6366f1; background: rgba(99,102,241,0.04); }
    nav[role=navigation] span[aria-current] { background: #6366f1; color: #fff; border-color: #6366f1; }
    nav[role=navigation] span[aria-disabled] { opacity: 0.4; cursor: default; }
    /* Hide "Showing X to Y of Z results" */
    nav[role=navigation] > div > div:first-child,
    nav[role=navigation] .hidden { display: block; }
    nav[role=navigation] p.text-sm { font-size: 12px; color: #64748b; font-weight: 500; margin: 4px 8px; }

    /* ── Utility ────────────────────────────────── */
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .text-muted { color: #94a3b8; }
    .text-danger { color: #dc2626; }
    .text-success { color: #16a34a; }
    .font-bold { font-weight: 700; }
    .font-black { font-weight: 900; }
    .text-sm { font-size: 12px; }
    .text-xs { font-size: 11px; }
    .mt-0 { margin-top: 0; }
    .mb-0 { margin-bottom: 0; }
    .mb-16 { margin-bottom: 16px; }
    .gap-8 { gap: 8px; }
    .gap-12 { gap: 12px; }
    .flex { display: flex; }
    .flex-wrap { flex-wrap: wrap; }
    .items-center { align-items: center; }
    .items-end { align-items: flex-end; }
    .justify-between { justify-content: space-between; }
    .justify-end { justify-content: flex-end; }
    .inline-flex { display: inline-flex; }
    .ml-auto { margin-left: auto; }
    .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
    .whitespace-nowrap { white-space: nowrap; }
    .separator { border: none; border-top: 1px solid #e2e8f0; margin: 10px 0; }

    /* ── Total summary badge ───────────────────── */
    .total-badge {
      padding: 10px 16px; border-radius: 10px;
      background: linear-gradient(135deg, #0f172a, #1e1b4b);
      color: #fff; font-size: 14px; font-weight: 700;
      display: inline-flex; align-items: center; gap: 6px;
    }
  </style>
</head>

<body>
<?php
  $role = auth()->user()?->role ?? 'cashier';
  $canSeeCost = \App\Support\Authz::canSeeCost();
  $activeShopId = session('shop_id');
  $allowedShops = \App\Support\ShopContext::allowedShops();
  $activeShop = \App\Support\ShopContext::activeShop();
  $isRouteActive = function(string $routeName){
    try { return request()->routeIs($routeName); }
    catch (\Throwable $e) { return false; }
  };
?>

<?php if (! (request()->routeIs('mt.pos.*'))): ?>
<button class="mt-mobile-toggle" onclick="document.querySelector('.mt-sidebar').classList.toggle('open');document.querySelector('.mt-overlay').classList.toggle('open');" aria-label="Toggle menu">
  <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M4 7h14M4 12h14M4 17h14"/></svg>
</button>
<div class="mt-overlay" onclick="document.querySelector('.mt-sidebar').classList.remove('open');this.classList.remove('open');"></div>
<?php endif; ?>

<div class="mt-app">

  
  <?php if (! (request()->routeIs('mt.pos.*'))): ?>
  <aside class="mt-sidebar" id="appSidebar">
    <div class="mt-sidebar-toggle" id="sidebarToggle">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
    </div>
    <div class="mt-brand">
      <div class="mt-brand-icon">MT</div>
      <div>
        <div class="mt-brand-text"><?php echo e(config('app.name', 'Mian Traders')); ?></div>
        <div class="mt-brand-sub">Business Suite</div>
      </div>
    </div>

    <div class="mt-sidebar-inner">
      <div class="mt-role-badge"><span class="mt-role-dot"></span> <?php echo e($role); ?></div>

      <div class="mt-shop-card">
        <div class="mt-shop-label">Active Shop</div>
        <form method="POST" action="<?php echo e(route('mt.shops.switch')); ?>">
          <?php echo csrf_field(); ?>
          <select name="shop_id" onchange="this.form.submit()" class="mt-shop-select">
            <?php if($role === 'admin'): ?>
              <option value="all" <?php echo e($activeShopId === 'all' ? 'selected' : ''); ?>>All Shops</option>
            <?php endif; ?>
            <?php $__currentLoopData = $allowedShops; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($s->id); ?>" <?php echo e((string)$activeShopId === (string)$s->id ? 'selected' : ''); ?>><?php echo e($s->name ?? ('Shop #' . $s->id)); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </form>
        <?php if($activeShop): ?>
          <div class="mt-shop-info"><b><?php echo e($activeShop->name); ?></b><?php if($activeShop->owner_name): ?><br>Owner: <?php echo e($activeShop->owner_name); ?><?php endif; ?></div>
        <?php endif; ?>
      </div>

      <div class="mt-section-label">Main</div>
      <a href="<?php echo e(route('dashboard')); ?>" class="mt-nav-link<?php echo e($isRouteActive('dashboard') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
        <span>Dashboard</span></a>
      <a href="<?php echo e(route('mt.analytics.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.analytics.index') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18"/><path d="M18.7 8.3L12 15 7.3 10.3 3 14"/></svg>
        <span>Analytics</span></a>
      <a href="<?php echo e(route('mt.pos.index')); ?>" target="_blank" class="mt-nav-link<?php echo e($isRouteActive('mt.pos.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 10h20"/><path d="M12 10v10"/></svg>
        <span>POS</span></a>
      <a href="<?php echo e(route('mt.products.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.products.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><path d="M3.27 6.96L12 12.01l8.73-5.05M12 22.08V12"/></svg>
        <span>Products</span></a>

      <?php if(\Illuminate\Support\Facades\Route::has('mt.sales.index')): ?>
      <a href="<?php echo e(route('mt.sales.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.sales.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        <span>Sales</span></a>
      <?php endif; ?>
      <?php if(\Illuminate\Support\Facades\Route::has('mt.udhar.index')): ?>
      <a href="<?php echo e(route('mt.udhar.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.udhar.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 5c-1.5 0-2.8 1.4-3 2-3.5-1.5-11-.3-11 5 0 1.8 0 3 2 4.5V20h4v-2h3v2h4v-4c1-.5 1.7-1 2-2h2v-4h-2c0-1-.5-1.5-1-2"/><circle cx="15" cy="11" r="1"/></svg>
        <span>Udhar</span></a>
      <?php endif; ?>

      <?php if($canSeeCost): ?>
      <div class="mt-section-label">Inventory</div>
      <a href="<?php echo e(route('mt.products.import_form')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.products.import_form') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        <span>Import Price List</span></a>
      <a href="<?php echo e(route('mt.purchases.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.purchases.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
        <span>Purchases</span></a>
      <a href="<?php echo e(route('mt.company_orders.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.company_orders.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
        <span>Company Orders</span></a>
      <a href="<?php echo e(route('mt.categories.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.categories.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
        <span>Categories</span></a>
      <a href="<?php echo e(route('mt.companies.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.companies.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/><path d="M9 9h1M9 13h1M9 17h1"/></svg>
        <span>Companies</span></a>
      <a href="<?php echo e(route('mt.sheet_designs.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.sheet_designs.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
        <span>Sheet Designs</span></a>
      <a href="<?php echo e(route('mt.company_brochures.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.company_brochures.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
        <span>Brochures</span></a>
      <?php if(\Illuminate\Support\Facades\Route::has('mt.company_groups.index')): ?>
      <a href="<?php echo e(route('mt.company_groups.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.company_groups.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
        <span>Company Groups</span></a>
      <?php endif; ?>
      <?php if(\Illuminate\Support\Facades\Route::has('mt.stock_movements.index')): ?>
      <a href="<?php echo e(route('mt.stock_movements.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.stock_movements.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        <span>Stock Movements</span></a>
      <?php endif; ?>

      <div class="mt-section-label">Finance</div>
      <a href="<?php echo e(route('mt.discount_types.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.discount_types.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="9" r="2"/><circle cx="15" cy="15" r="2"/><line x1="19" y1="5" x2="5" y2="19"/></svg>
        <span>Discount Types</span></a>
      <a href="<?php echo e(route('mt.discount_rules.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.discount_rules.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        <span>Discount Rules</span></a>
      <a href="<?php echo e(route('mt.expenses.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.expenses.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
        <span>Expenses</span></a>
      <a href="<?php echo e(route('mt.recurring_expenses.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.recurring_expenses.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
        <span>Recurring Expenses</span></a>

      <?php if($role === 'admin'): ?>
      <div class="mt-section-label">Admin</div>
      <a href="<?php echo e(route('mt.shops.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.shops.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        <span>Shops</span></a>
      <a href="<?php echo e(route('mt.users.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.users.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        <span>Users</span></a>
      <a href="<?php echo e(route('mt.system_reset.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.system_reset.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5"/><path d="M14 11v5"/></svg>
        <span>Backup & Reset</span></a>
      <?php endif; ?>
      <?php endif; ?>

      <?php if(\Illuminate\Support\Facades\Route::has('mt.whatsapp.index')): ?>
      <div class="mt-section-label">Communication</div>
      <a href="<?php echo e(route('mt.whatsapp.index')); ?>" class="mt-nav-link<?php echo e($isRouteActive('mt.whatsapp.*') ? ' active' : ''); ?>">
        <svg class="mt-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
        <span>WhatsApp</span></a>
      <?php endif; ?>
    </div>

    <div class="mt-sidebar-footer">
      <?php if(\Illuminate\Support\Facades\Route::has('logout')): ?>
      <form method="POST" action="<?php echo e(route('logout')); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="mt-logout-btn">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
          <span>Sign Out</span></button>
      </form>
      <?php else: ?>
      <div style="color:#475569;font-size:11px;text-align:center;">Logout not available</div>
      <?php endif; ?>
    </div>
  </aside>
  <?php endif; ?>

  
  <div class="mt-main">
    <?php if(session()->has('success') && trim((string) session('success')) !== ''): ?>
      <div class="mt-alert mt-alert-success">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>
    <?php if(session()->has('error') && trim((string) session('error')) !== ''): ?>
      <div class="mt-alert mt-alert-error">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        <?php echo e(session('error')); ?>

      </div>
    <?php endif; ?>
    <?php echo $__env->yieldContent('content'); ?>
  </div>
</div>

<script>
  (function() {
    const sidebar = document.getElementById('appSidebar');
    const toggle = document.getElementById('sidebarToggle');
    if (!sidebar || !toggle) return;

    const isPosPage = window.location.pathname.includes('/pos');
    let isCollapsedPref = localStorage.getItem('sidebar_collapsed');
    let isCollapsed = false;

    // Logic: Default to collapsed on POS, otherwise use preference or default to false
    if (isPosPage) {
      isCollapsed = (isCollapsedPref === null) ? true : (isCollapsedPref === 'true');
    } else {
      isCollapsed = (isCollapsedPref === 'true');
    }

    if (isCollapsed) {
      sidebar.classList.add('collapsed');
    }

    toggle.addEventListener('click', function() {
      sidebar.classList.toggle('collapsed');
      localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed'));
    });
  })();
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\mian-traders\resources\views/mt/layouts/app.blade.php ENDPATH**/ ?>