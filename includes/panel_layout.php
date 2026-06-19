<?php
if (!defined('VATANPARVAR')) require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/notifications.php';

function vpy_panel_css() {
    return <<<CSS
:root{
    --bg:#FAF7F2;--primary:#0D6B4E;--primary-dark:#094D38;--primary-glow:rgba(13,107,78,0.18);
    --accent:#E8A838;--accent-soft:#F5D08A;--secondary:#B7C9B3;
    --dark:#1E1B18;--dark-soft:#3B362F;--muted:#7A6F62;--light:#FFFDF9;
    --glass:rgba(255,252,248,0.7);--glass-strong:rgba(255,252,248,0.88);
    --border:rgba(180,160,130,0.25);--border-strong:rgba(180,160,130,0.45);
    --shadow-sm:0 2px 8px rgba(30,27,24,0.04);--shadow:0 18px 40px rgba(30,27,24,0.08);
    --r-sm:14px;--r:22px;--r-lg:32px;--pill:100px;
    --t:0.4s cubic-bezier(0.4,0,0.2,1);
    --serif:"Playfair Display",Georgia,serif;
    --sans:"Manrope","Inter",-apple-system,BlinkMacSystemFont,system-ui,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent;-webkit-touch-callout:none}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%}
body{font-family:var(--sans);font-size:15px;line-height:1.55;color:var(--dark);background:var(--bg);min-height:100vh;-webkit-font-smoothing:antialiased;overflow-x:hidden;-webkit-text-size-adjust:100%;text-size-adjust:100%}
button,a,input,textarea,select,label,.btn,.s-link,.nav-link,.chip,.tab{-webkit-tap-highlight-color:transparent;outline:0}
button:focus,a:focus,input:focus,textarea:focus,select:focus{outline:0}
button:focus-visible,a:focus-visible{outline:2px solid var(--primary);outline-offset:2px;border-radius:8px}
input:focus-visible,textarea:focus-visible,select:focus-visible{outline:0}
button,.btn{user-select:none;-webkit-user-select:none}
::selection{background:rgba(13,107,78,0.18);color:var(--dark)}
body::before{content:"";position:fixed;inset:0;pointer-events:none;z-index:1;opacity:0.25;mix-blend-mode:multiply;background-image:url("data:image/svg+xml;utf8,<svg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3'/><feColorMatrix values='0 0 0 0 0.12 0 0 0 0 0.10 0 0 0 0 0.08 0 0 0 0.4 0'/></filter><rect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/></svg>")}
.bg-mesh{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
.blob{position:absolute;border-radius:50%;filter:blur(90px);opacity:0.4}
.blob.b1{width:50vw;height:50vw;background:radial-gradient(circle,#B7C9B3,transparent 70%);top:-20vh;left:-15vw}
.blob.b2{width:45vw;height:45vw;background:radial-gradient(circle,#F5D08A,transparent 70%);bottom:-15vh;right:-15vw;opacity:0.3}
img,svg{max-width:100%;display:block;height:auto}
a{color:inherit;text-decoration:none;transition:var(--t)}
button{font:inherit;cursor:pointer;border:none;background:none;color:inherit}
ul{list-style:none}
input,textarea,select{font:inherit;color:inherit}

.layout{display:grid;grid-template-columns:280px 1fr;min-height:100vh;position:relative;z-index:2}
.sidebar{position:sticky;top:0;height:100vh;background:var(--glass-strong);backdrop-filter:blur(30px) saturate(180%);-webkit-backdrop-filter:blur(30px) saturate(180%);border-right:1px solid var(--border);padding:28px 22px;display:flex;flex-direction:column;gap:8px;overflow-y:auto;z-index:10;grid-column:1}
.main{padding:30px 36px;min-width:0;grid-column:2}
.s-brand{display:flex;align-items:center;gap:12px;padding:8px 12px 24px;font-family:var(--serif);font-weight:700;font-size:1.1rem;color:var(--dark);border-bottom:1px solid var(--border);margin-bottom:16px}
.s-logo{width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:grid;place-items:center;color:#fff;box-shadow:0 6px 16px var(--primary-glow);flex-shrink:0}
.s-logo svg{width:20px;height:20px}
.s-section{font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--muted);padding:14px 12px 6px}
.s-link{display:flex;align-items:center;gap:14px;padding:12px 14px;border-radius:14px;color:var(--dark-soft);font-size:0.92rem;font-weight:500;transition:var(--t);position:relative}
.s-link svg{width:18px;height:18px;flex-shrink:0;color:var(--muted);transition:var(--t)}
.s-link:hover{background:rgba(13,107,78,0.06);color:var(--primary)}
.s-link:hover svg{color:var(--primary)}
.s-link.active{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;box-shadow:0 12px 24px var(--primary-glow)}
.s-link.active svg{color:#fff}
.s-link .badge{margin-left:auto;background:var(--accent);color:#fff;font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:var(--pill)}
.s-link.active .badge{background:rgba(255,255,255,0.25)}
.s-foot{margin-top:auto;padding-top:16px;border-top:1px solid var(--border)}

.topbar{display:flex;align-items:center;justify-content:space-between;gap:20px;margin-bottom:30px;flex-wrap:wrap}
.tb-title h1{font-family:var(--serif);font-weight:500;font-size:clamp(1.6rem,3vw,2.2rem);line-height:1.1;letter-spacing:-0.02em;margin-bottom:4px}
.tb-title p{font-size:0.92rem;color:var(--muted)}
.tb-actions{display:flex;align-items:center;gap:10px;flex-wrap:wrap}

.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:11px 20px;border-radius:var(--pill);font-weight:600;font-size:0.88rem;cursor:pointer;transition:transform var(--t),box-shadow var(--t),background var(--t);border:none;font-family:inherit;white-space:nowrap;position:relative;overflow:hidden}
.btn:hover{transform:translateY(-2px)}
.btn:active{transform:translateY(0) scale(0.98)}
.btn svg{width:16px;height:16px}
.btn-primary{background:linear-gradient(135deg,var(--accent),#D88F1A);color:#fff;box-shadow:0 12px 28px rgba(232,168,56,0.4)}
.btn-primary:hover{box-shadow:0 16px 36px rgba(232,168,56,0.55)}
.btn-dark{background:var(--dark);color:#fff;box-shadow:0 12px 28px rgba(30,27,24,0.25)}
.btn-ghost{background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--border);color:var(--dark)}
.btn-ghost:hover{background:var(--glass-strong);border-color:var(--border-strong)}
.btn-danger{background:linear-gradient(135deg,#FF6058,#C73E36);color:#fff;box-shadow:0 12px 28px rgba(199,62,54,0.3)}
.btn-success{background:linear-gradient(135deg,#0D6B4E,#094D38);color:#fff;box-shadow:0 12px 28px var(--primary-glow)}
.btn-sm{padding:8px 14px;font-size:0.8rem}

.user-pop{position:relative}
.user-btn{display:flex;align-items:center;gap:10px;padding:8px 14px 8px 8px;background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--border);border-radius:var(--pill);cursor:pointer;transition:var(--t)}
.user-btn:hover{background:var(--glass-strong)}
.user-avatar{width:34px;height:34px;border-radius:50%;display:grid;place-items:center;color:#fff;font-weight:700;font-size:0.82rem;flex-shrink:0}
.user-name{font-size:0.88rem;font-weight:600;color:var(--dark)}
.user-menu{position:absolute;top:calc(100% + 10px);right:0;min-width:240px;background:var(--glass-strong);backdrop-filter:blur(30px);-webkit-backdrop-filter:blur(30px);border:1px solid var(--border);border-radius:var(--r);padding:10px;box-shadow:var(--shadow);opacity:0;pointer-events:none;transform:translateY(-10px);transition:var(--t);z-index:50}
.user-pop.open .user-menu{opacity:1;pointer-events:auto;transform:translateY(0)}
.user-menu a{display:flex;align-items:center;gap:12px;padding:10px 14px;border-radius:10px;color:var(--dark-soft);font-size:0.9rem;font-weight:500}
.user-menu a:hover{background:rgba(13,107,78,0.06);color:var(--primary)}
.user-menu a.danger:hover{background:rgba(255,96,88,0.1);color:#C73E36}
.user-menu hr{border:none;border-top:1px solid var(--border);margin:6px 0}

.card{background:var(--glass-strong);backdrop-filter:blur(30px) saturate(160%);-webkit-backdrop-filter:blur(30px) saturate(160%);border:1px solid var(--border);border-radius:var(--r-lg);padding:28px;box-shadow:var(--shadow-sm);transition:transform var(--t),box-shadow var(--t)}
.card:hover{box-shadow:var(--shadow)}
.card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;flex-wrap:wrap;gap:14px}
.card-head h2{font-family:var(--serif);font-weight:500;font-size:1.4rem;letter-spacing:-0.01em;color:var(--dark)}
.card-head .h-card{font-family:var(--sans);font-size:1rem;font-weight:700}

.flash{padding:14px 20px;border-radius:var(--r-sm);margin-bottom:18px;display:flex;align-items:center;gap:10px;font-size:0.9rem;animation:slideIn 0.4s ease}
@keyframes slideIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
.flash.success{background:rgba(13,107,78,0.1);color:var(--primary-dark);border:1px solid rgba(13,107,78,0.2)}
.flash.error{background:rgba(255,96,88,0.1);color:#C73E36;border:1px solid rgba(255,96,88,0.3)}
.flash.info{background:rgba(232,168,56,0.1);color:#A87830;border:1px solid rgba(232,168,56,0.3)}
.flash svg{width:18px;height:18px;flex-shrink:0}

.field{margin-bottom:18px}
.field label{display:block;font-size:0.82rem;font-weight:600;color:var(--dark-soft);margin-bottom:8px;letter-spacing:0.02em}
.field input,.field textarea,.field select{width:100%;padding:13px 18px;border-radius:14px;border:1px solid var(--border-strong);background:rgba(255,253,249,0.85);color:var(--dark);font-size:0.94rem;font-family:inherit;transition:var(--t)}
.field input:focus,.field textarea:focus,.field select:focus{outline:none;border-color:var(--primary);background:var(--light);box-shadow:0 0 0 4px rgba(13,107,78,0.12)}
.field textarea{min-height:120px;resize:vertical;line-height:1.5}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}

table.tbl{width:100%;border-collapse:separate;border-spacing:0;font-size:0.9rem}
table.tbl thead th{text-align:left;padding:14px 16px;background:rgba(13,107,78,0.06);font-weight:700;color:var(--dark-soft);font-size:0.78rem;text-transform:uppercase;letter-spacing:0.06em;border-bottom:1px solid var(--border)}
table.tbl thead th:first-child{border-radius:var(--r-sm) 0 0 0}
table.tbl thead th:last-child{border-radius:0 var(--r-sm) 0 0}
table.tbl tbody td{padding:14px 16px;border-bottom:1px solid var(--border);vertical-align:middle}
table.tbl tbody tr:hover{background:rgba(13,107,78,0.03)}
table.tbl tbody tr:last-child td{border-bottom:none}
.row-actions{display:flex;gap:6px}
.row-actions a,.row-actions button{width:32px;height:32px;border-radius:10px;display:grid;place-items:center;background:rgba(13,107,78,0.06);color:var(--primary);transition:var(--t);font-size:0.8rem}
.row-actions a:hover,.row-actions button:hover{background:var(--primary);color:#fff;transform:translateY(-2px)}
.row-actions .danger{background:rgba(255,96,88,0.08);color:#C73E36}
.row-actions .danger:hover{background:#C73E36;color:#fff}
.row-actions svg{width:14px;height:14px}

.chip{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:var(--pill);font-size:0.75rem;font-weight:600}
.chip-success{background:rgba(13,107,78,0.1);color:var(--primary-dark)}
.chip-warning{background:rgba(232,168,56,0.15);color:#A87830}
.chip-danger{background:rgba(255,96,88,0.1);color:#C73E36}
.chip-muted{background:rgba(122,111,98,0.1);color:var(--muted)}

.empty{text-align:center;padding:60px 30px;color:var(--muted)}
.empty svg{width:60px;height:60px;margin:0 auto 18px;opacity:0.4}
.empty h3{font-family:var(--serif);font-weight:500;font-size:1.3rem;color:var(--dark);margin-bottom:8px}

.pagination{display:flex;gap:6px;justify-content:center;margin-top:24px;flex-wrap:wrap}
.pagination a,.pagination span{min-width:38px;height:38px;padding:0 12px;border-radius:12px;display:grid;place-items:center;font-size:0.85rem;font-weight:600;color:var(--dark-soft);background:var(--glass);border:1px solid var(--border);transition:var(--t)}
.pagination a:hover{background:var(--glass-strong);border-color:var(--border-strong)}
.pagination .active{background:var(--primary);color:#fff;border-color:var(--primary)}

.burger-mobile{display:none;width:44px;height:44px;border-radius:50%;background:var(--glass);border:1px solid var(--border);place-items:center;cursor:pointer}
.burger-mobile span{display:block;width:18px;height:2px;background:var(--dark);position:relative}
.burger-mobile span::before,.burger-mobile span::after{content:"";position:absolute;left:0;width:18px;height:2px;background:var(--dark);transition:var(--t)}
.burger-mobile span::before{top:-6px}.burger-mobile span::after{top:6px}

@media (max-width:1024px){
    .layout{grid-template-columns:1fr}
    .sidebar{position:fixed;top:0;left:-100%;width:min(320px,85vw);transition:left 0.4s cubic-bezier(0.4,0,0.2,1);box-shadow:30px 0 60px rgba(30,27,24,0.15);z-index:60}
    .sidebar.open{left:0}
    .sidebar.open + .main::before{content:"";position:fixed;inset:0;background:rgba(30,27,24,0.4);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);z-index:50;animation:fadeOverlay 0.3s ease}
    @keyframes fadeOverlay{from{opacity:0}to{opacity:1}}
    .burger-mobile{display:grid}
    .main{padding:20px;grid-column:1}
    .field-row{grid-template-columns:1fr}
}
@media (max-width:640px){
    .main{padding:14px 12px}
    .card{padding:18px 16px;border-radius:18px}
    .topbar{margin-bottom:16px;gap:10px}
    .tb-title h1{font-size:1.3rem;letter-spacing:-0.015em}
    .tb-title p{font-size:0.82rem}
    .tb-actions{gap:6px;width:100%;justify-content:flex-end}
    .btn{padding:9px 14px;font-size:0.82rem;border-radius:14px}
    .btn svg{width:14px;height:14px}
    .btn-sm{padding:7px 12px;font-size:0.76rem}
    table.tbl{font-size:0.78rem}
    table.tbl thead th,table.tbl tbody td{padding:8px 10px}
    .row-actions a,.row-actions button{width:30px;height:30px;border-radius:9px}
    .row-actions svg{width:13px;height:13px}
    .field{margin-bottom:14px}
    .field label{font-size:0.78rem;margin-bottom:6px}
    .field input,.field textarea,.field select{padding:11px 14px;border-radius:12px;font-size:0.9rem}
    .card-head h2{font-size:1.08rem}
    .card-head{margin-bottom:14px;gap:8px}
    .chip{padding:3px 9px;font-size:0.7rem}
    .pagination{gap:4px}
    .pagination a,.pagination span{min-width:32px;height:32px;font-size:0.78rem;padding:0 9px}
    .flash{padding:10px 14px;font-size:0.82rem;gap:8px;border-radius:12px}
    .user-btn{padding:5px 10px 5px 5px}
    .user-avatar{width:28px;height:28px;font-size:0.7rem}
    .user-name{font-size:0.78rem;max-width:80px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .topbar h1{font-size:1.25rem!important}
}
@media (max-width:380px){
    .main{padding:12px 10px}
    .card{padding:14px 12px;border-radius:16px}
    .tb-title h1{font-size:1.18rem}
    .btn{padding:8px 12px;font-size:0.78rem}
    .field input,.field textarea{padding:10px 12px;font-size:0.88rem}
    .user-name{display:none}
    .row-actions{gap:4px}
    .row-actions a,.row-actions button{width:28px;height:28px}
    table.tbl{font-size:0.72rem}
    table.tbl thead th,table.tbl tbody td{padding:7px 8px}
}

/* DESKTOP TAKOMILLASHTIRISH — 1280px+ */
@media (min-width:1280px){
    .layout{grid-template-columns:296px 1fr}
    .sidebar{padding:32px 22px}
    .s-link{padding:13px 16px;font-size:0.94rem}
    .s-link svg{width:19px;height:19px}
    .main{padding:36px 44px;max-width:1480px}
    .topbar{margin-bottom:34px;gap:24px}
    .tb-title h1{font-size:2rem}
    .card{padding:32px}
    .card-head{margin-bottom:24px}
    .card-head h2{font-size:1.5rem}
    .field-row{gap:18px}
    table.tbl{font-size:0.94rem}
    table.tbl thead th{padding:16px 18px;font-size:0.8rem}
    table.tbl tbody td{padding:16px 18px}
    .row-actions a,.row-actions button{width:36px;height:36px;border-radius:11px}
    .row-actions svg{width:15px;height:15px}
    .btn{padding:12px 22px}
    .btn-sm{padding:9px 16px}
}

/* WIDE DESKTOP — 1440px+ */
@media (min-width:1440px){
    .layout{grid-template-columns:312px 1fr}
    .sidebar{padding:36px 24px}
    .s-brand{font-size:1.18rem;padding:10px 14px 26px}
    .s-logo{width:42px;height:42px}
    .s-logo svg{width:22px;height:22px}
    .s-section{padding:18px 14px 8px}
    .s-link{padding:14px 18px;font-size:0.96rem;border-radius:15px}
    .s-link svg{width:20px;height:20px}
    .main{padding:44px 56px;max-width:1640px;font-size:15.5px}
    .topbar{margin-bottom:40px}
    .tb-title h1{font-size:2.2rem}
    .tb-title p{font-size:0.96rem}
    .card{padding:36px;border-radius:34px}
    .card-head h2{font-size:1.6rem}
    .field input,.field textarea,.field select{padding:14px 20px;font-size:0.97rem;border-radius:15px}
    table.tbl{font-size:0.95rem}
    table.tbl thead th{padding:18px 20px}
    table.tbl tbody td{padding:18px 20px}
}

/* ULTRA WIDE — 1920px+ */
@media (min-width:1920px){
    .layout{grid-template-columns:340px 1fr}
    .sidebar{padding:44px 26px}
    .main{padding:54px 80px;max-width:1820px;margin:0 auto}
    .tb-title h1{font-size:2.4rem}
    .card{padding:42px;border-radius:38px}
}

/* DESKTOP YORDAMCHI GRIDLAR */
@media (min-width:1280px){
    .grid-2{display:grid;grid-template-columns:repeat(2,1fr);gap:22px}
    .grid-3{display:grid;grid-template-columns:repeat(3,1fr);gap:22px}
    .grid-4{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
    .grid-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:20px}
    .grid-split{display:grid;grid-template-columns:1.5fr 1fr;gap:24px}
    .grid-sidebar-left{display:grid;grid-template-columns:340px 1fr;gap:24px}
    .table-wrap{overflow-x:visible}
    .toolbar{display:flex;gap:14px;align-items:center;flex-wrap:wrap}
    .toolbar input,.toolbar select{padding:13px 18px;border-radius:14px;border:1px solid var(--border-strong);background:rgba(255,253,249,0.85)}
    .toolbar input{flex:1;min-width:280px}
}
@media (min-width:1440px){
    .grid-stats{gap:24px}
    .grid-split{grid-template-columns:1.6fr 1fr;gap:30px}
}

/* DESKTOP STICKY BARS */
@media (min-width:1280px){
    .topbar.sticky-top{position:sticky;top:18px;z-index:10;backdrop-filter:blur(20px);background:rgba(250,247,242,0.86);border-radius:18px;padding:14px 20px;margin:-14px -20px 30px -20px;border:1px solid var(--border)}
    .card.tall{min-height:420px}
}

/* CHIP/BADGE TAKOMILI */
@media (min-width:1280px){
    .chip{padding:6px 14px;font-size:0.78rem}
}

/* SIDEBAR HOVER STATE — desktop only */
@media (min-width:1024px) and (hover:hover){
    .s-link:not(.active):hover{padding-left:18px}
    .s-link svg{transition:var(--t)}
    .s-link:hover svg{transform:translateX(2px)}
}

/* DARK SCROLLBAR */
@media (min-width:1024px){
    .sidebar::-webkit-scrollbar{width:6px}
    .sidebar::-webkit-scrollbar-thumb{background:rgba(13,107,78,0.18);border-radius:6px}
    .sidebar::-webkit-scrollbar-thumb:hover{background:rgba(13,107,78,0.32)}
    .sidebar::-webkit-scrollbar-track{background:transparent}
    .main::-webkit-scrollbar{width:8px}
    .main::-webkit-scrollbar-thumb{background:rgba(180,160,130,0.3);border-radius:8px}
    .main::-webkit-scrollbar-track{background:transparent}
}

/* PRINT */
@media print{
    .sidebar,.topbar,.row-actions,.btn,.burger-mobile,.bg-mesh,body::before{display:none!important}
    .main{padding:0;max-width:none}
    .card{box-shadow:none;border:1px solid #ccc;break-inside:avoid;page-break-inside:avoid;background:#fff}
    table.tbl{font-size:11px}
}

@keyframes spin{to{transform:rotate(360deg)}}
.spinner{width:20px;height:20px;border:2px solid rgba(13,107,78,0.2);border-top-color:var(--primary);border-radius:50%;animation:spin 0.8s linear infinite}
CSS;
}

function vpy_panel_head($title, $extra_css = '') {
    $is_cyrl = vpy_lang_code() === 'uz_cyrillic';
    $css = vpy_panel_css();
    echo '<!DOCTYPE html><html lang="' . ($is_cyrl ? 'uz-Cyrl' : 'uz') . '"><head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">';
    echo '<meta name="theme-color" content="#0D6B4E">';
    echo '<meta name="robots" content="noindex">';
    echo '<title>' . e($title) . ' — ' . e(t('site_name')) . '</title>';
    echo '<link rel="icon" type="image/svg+xml" href="' . e(vpy_favicon_url()) . '">';
    echo '<link rel="manifest" href="/manifest.json">';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">';
    echo '<style>' . $css . $extra_css . '</style>';
    echo '</head><body>';
    echo '<div class="bg-mesh"><div class="blob b1"></div><div class="blob b2"></div></div>';
}

function vpy_panel_sidebar($current, $is_admin = false) {
    $u = vpy_user();
    if (!$u) return;
    $items = $is_admin ? [
        ['/admin/', 'index', 'admin_dashboard', 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'],
        ['/admin/users.php', 'users', 'admin_users', 'M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2|circle:cx=8.5,cy=7,r=4|M20 8v6M23 11h-6'],
        ['/admin/savollar.php', 'savollar', 'admin_questions', 'M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3|M12 17h.01|circle:cx=12,cy=12,r=10'],
        ['/admin/biletlar.php', 'biletlar', 'admin_tickets', 'M21 8v13H3V8|M1 3h22v5H1z|M10 12h4'],
        ['/admin/tariflar.php', 'tariflar', 'admin_tariffs', 'M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z|circle:cx=7,cy=7,r=1.4'],
        ['/admin/tolovlar.php', 'tolovlar', 'admin_payments', 'M12 1v22M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6'],
        ['/admin/blog.php', 'blog', 'admin_blog', 'M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z|M14 2v6h6|M16 13H8M16 17H8'],
        ['/admin/sharhlar.php', 'sharhlar', 'admin_reviews', 'M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8z'],
        ['/admin/loglar.php', 'loglar', 'admin_logs', 'M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z|M14 2v6h6|M16 13H8|M10 9H8'],
        ['/admin/sozlamalar.php', 'sozlamalar', 'admin_settings', 'circle:cx=12,cy=12,r=3|M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z']
    ] : [
        ['/user/', 'index', 'user_dashboard_title', 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'],
        ['/user/testlar.php', 'testlar', 'tickets_title', 'M21 8v13H3V8|M1 3h22v5H1z|M10 12h4'],
        ['/user/test.php', 'test', 'user_quick_test', 'circle:cx=12,cy=12,r=10|M12 6v6l4 2'],
        ['/user/natijalar.php', 'natijalar', 'user_recent_tests', 'M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z|M14 2v6h6|M16 13H8M16 17H8'],
        ['/user/reyting.php', 'reyting', 'rating_title', 'M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2|circle:cx=8.5,cy=7,r=4|M20 8v6M23 11h-6'],
        ['/user/tariflar.php', 'tariflar', 'tariffs_title', 'M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z|circle:cx=7,cy=7,r=1.4'],
        ['/user/referallar.php', 'referallar', 'referral_title', 'circle:cx=9,cy=7,r=4|M3 21v-2a4 4 0 014-4h4a4 4 0 014 4v2|M16 3.13a4 4 0 010 7.75|M21 21v-2a4 4 0 00-3-3.87'],
        ['/user/profil.php', 'profil', 'profile_title', 'M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2|circle:cx=12,cy=7,r=4'],
    ];

    echo '<aside class="sidebar" id="sidebar">';
    echo '<a href="/" class="s-brand"><span class="s-logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2 4 4 8-8 4 4"/></svg></span>' . e(t('site_name')) . '</a>';
    echo '<div class="s-section">' . e($is_admin ? t('admin_title') : t('nav_dashboard')) . '</div>';
    foreach ($items as $it) {
        list($url, $key, $label, $icon) = $it;
        $active = $current === $key ? 'active' : '';
        echo '<a href="' . e($url) . '" class="s-link ' . $active . '">';
        echo '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">';
        foreach (explode('|', $icon) as $part) {
            if (strpos($part, 'circle:') === 0) {
                $attrs = substr($part, 7);
                echo '<circle ' . str_replace(',', ' ', preg_replace('/(\w+)=/', '$1="', $attrs)) . '"/>';
            } else {
                echo '<path d="' . $part . '"/>';
            }
        }
        echo '</svg>';
        echo '<span>' . e(t($label)) . '</span>';
        echo '</a>';
    }
    if (!$is_admin) {
        $unread = vpy_notify_unread_count($u['id']);
        if ($unread > 0) {
            echo '<a href="/user/" class="s-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>Bildirishnomalar<span class="badge">' . $unread . '</span></a>';
        }
    }
    echo '<div class="s-foot">';
    echo '<a href="/" class="s-link"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>' . e(t('nav_home')) . '</a>';
    echo '</div>';
    echo '</aside>';
}

function vpy_panel_topbar($title, $subtitle = '', $actions_html = '') {
    $u = vpy_user();
    $color = $u ? vpy_avatar_color($u['name']) : '#0D6B4E';
    echo '<div class="topbar">';
    echo '<div style="display:flex;align-items:center;gap:14px"><button class="burger-mobile" id="burgerMobile" aria-label="Menyu"><span></span></button><div class="tb-title">';
    echo '<h1>' . e($title) . '</h1>';
    if ($subtitle) echo '<p>' . e($subtitle) . '</p>';
    echo '</div></div>';
    echo '<div class="tb-actions">';
    echo $actions_html;
    if ($u) {
        echo '<div class="user-pop" id="userPop">';
        echo '<button class="user-btn"><span class="user-avatar" style="background:' . e($color) . '">' . e(vpy_user_initials($u['name'])) . '</span><span class="user-name">' . e($u['name']) . '</span><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg></button>';
        echo '<div class="user-menu">';
        echo '<a href="' . (vpy_is_admin() ? '/admin/sozlamalar.php' : '/user/profil.php') . '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' . e(t('profile_title')) . '</a>';
        echo '<a href="?lang=' . (vpy_lang_code() === 'uz_latin' ? 'uz_cyrillic' : 'uz_latin') . '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg>' . (vpy_lang_code() === 'uz_latin' ? 'Кирилл' : 'Lotin') . '</a>';
        echo '<hr>';
        echo '<a href="/logout.php" class="danger"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>' . e(t('nav_logout')) . '</a>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';

    foreach (vpy_flash_get() as $f) {
        $type = $f['type'] ?? 'info';
        $icon = $type === 'success' ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>' : ($type === 'error' ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>');
        echo '<div class="flash ' . e($type) . '">' . $icon . '<span>' . e($f['msg']) . '</span></div>';
    }
}

function vpy_panel_foot() {
    $storage_js = vpy_storage_js();
    echo '<script>' . $storage_js . '</script>';
    echo <<<HTML
<script>
(function(){
    var burger = document.getElementById('burgerMobile');
    var sidebar = document.getElementById('sidebar');
    if (burger && sidebar) {
        function closeSidebar(){
            sidebar.classList.remove('open');
            document.body.style.overflow = '';
            burger.setAttribute('aria-expanded', 'false');
        }
        function syncSidebarToViewport(){
            if (window.innerWidth >= 1024) closeSidebar();
        }
        burger.addEventListener('click', function(e){
            e.stopPropagation();
            var willOpen = !sidebar.classList.contains('open');
            sidebar.classList.toggle('open');
            document.body.style.overflow = willOpen ? 'hidden' : '';
            burger.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
        document.addEventListener('click', function(e){
            if (sidebar.classList.contains('open') && !sidebar.contains(e.target) && e.target !== burger && !burger.contains(e.target)) {
                closeSidebar();
            }
        });
        sidebar.querySelectorAll('.s-link').forEach(function(a){
            a.addEventListener('click', function(){
                if (window.innerWidth < 1024) closeSidebar();
            });
        });
        document.addEventListener('keydown', function(e){
            if (e.key === 'Escape' && sidebar.classList.contains('open')) closeSidebar();
        });
        var resizeTimer;
        window.addEventListener('resize', function(){
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(syncSidebarToViewport, 100);
        });
        syncSidebarToViewport();
    }
    var userPop = document.getElementById('userPop');
    if (userPop) {
        var userBtn = userPop.querySelector('.user-btn');
        userBtn.addEventListener('click', function(e){
            e.stopPropagation();
            userPop.classList.toggle('open');
        });
        document.addEventListener('click', function(){ userPop.classList.remove('open'); });
    }
    document.querySelectorAll('.btn').forEach(function(btn){
        btn.addEventListener('click', function(e){
            var rect = btn.getBoundingClientRect();
            var ripple = document.createElement('span');
            var size = Math.max(rect.width, rect.height);
            ripple.style.cssText = 'position:absolute;border-radius:50%;background:rgba(255,255,255,0.4);transform:scale(0);animation:ripple 0.6s ease-out;pointer-events:none;width:'+size+'px;height:'+size+'px;left:'+(e.clientX-rect.left-size/2)+'px;top:'+(e.clientY-rect.top-size/2)+'px;';
            btn.appendChild(ripple);
            setTimeout(function(){ ripple.remove(); }, 650);
        });
    });
})();
</script>
<style>@keyframes ripple{to{transform:scale(4);opacity:0}}</style>
</body></html>
HTML;
}
