<?php
if (!defined('VATANPARVAR')) require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';

function vpy_public_css() {
    return <<<CSS
:root{
    --bg:#FAF7F2;--primary:#0D6B4E;--primary-dark:#094D38;--primary-glow:rgba(13,107,78,0.18);
    --accent:#E8A838;--accent-soft:#F5D08A;--secondary:#B7C9B3;
    --dark:#1E1B18;--dark-soft:#3B362F;--muted:#7A6F62;--light:#FFFDF9;
    --glass:rgba(255,252,248,0.68);--glass-strong:rgba(255,252,248,0.85);
    --border:rgba(180,160,130,0.25);--border-strong:rgba(180,160,130,0.45);
    --shadow:0 30px 50px rgba(30,27,24,0.08);--shadow-lg:0 40px 80px rgba(30,27,24,0.14);
    --r-sm:14px;--r:22px;--r-lg:32px;--r-xl:48px;--pill:100px;
    --t:0.4s cubic-bezier(0.4,0,0.2,1);--t-bounce:0.6s cubic-bezier(0.34,1.56,0.64,1);
    --serif:"Playfair Display",Georgia,serif;
    --sans:"Manrope","Inter",-apple-system,BlinkMacSystemFont,system-ui,sans-serif;
    --container:min(1280px,92vw);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent;-webkit-touch-callout:none}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%;overflow-x:hidden;text-size-adjust:100%}
body{font-family:var(--sans);font-size:clamp(15px,1vw,16px);line-height:1.6;color:var(--dark);background:var(--bg);overflow-x:hidden;min-height:100vh;position:relative;-webkit-font-smoothing:antialiased}
button,a,input,textarea,select,.btn,.nav-link,.chip{-webkit-tap-highlight-color:transparent;outline:0}
button:focus,a:focus{outline:0}
button:focus-visible,a:focus-visible{outline:2px solid var(--primary);outline-offset:2px;border-radius:8px}
input:focus-visible,textarea:focus-visible,select:focus-visible{outline:0}
button,.btn{user-select:none;-webkit-user-select:none}
::selection{background:rgba(13,107,78,0.18);color:var(--dark)}
body::before{content:"";position:fixed;inset:0;pointer-events:none;z-index:1;opacity:0.3;mix-blend-mode:multiply;background-image:url("data:image/svg+xml;utf8,<svg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3'/><feColorMatrix values='0 0 0 0 0.12 0 0 0 0 0.10 0 0 0 0 0.08 0 0 0 0.4 0'/></filter><rect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/></svg>")}
.mesh-bg{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
.mesh-blob{position:absolute;border-radius:50%;filter:blur(80px);opacity:0.5}
.mesh-blob.b1{width:55vw;height:55vw;background:radial-gradient(circle,#B7C9B3,transparent 70%);top:-15vw;left:-10vw;animation:fA 22s ease-in-out infinite}
.mesh-blob.b2{width:45vw;height:45vw;background:radial-gradient(circle,#F5D08A,transparent 70%);top:40vh;right:-15vw;opacity:0.4;animation:fB 26s ease-in-out infinite}
.mesh-blob.b3{width:40vw;height:40vw;background:radial-gradient(circle,rgba(13,107,78,0.25),transparent 70%);bottom:-10vw;left:30vw;opacity:0.4;animation:fA 30s ease-in-out infinite reverse}
@keyframes fA{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(8vw,4vh) scale(1.1)}}
@keyframes fB{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(-6vw,-5vh) scale(1.15)}}
img,svg{max-width:100%;display:block;height:auto}
a{color:inherit;text-decoration:none;transition:var(--t)}
button{font:inherit;cursor:pointer;border:none;background:none;color:inherit}
ul{list-style:none}
input,textarea,select{font:inherit;color:inherit}
main{position:relative;z-index:2}
.container{width:var(--container);margin:0 auto;position:relative}

.h-display{font-family:var(--serif);font-weight:500;font-size:clamp(2.2rem,5vw,4.4rem);line-height:1.05;letter-spacing:-0.025em;color:var(--dark)}
.h-display em{font-style:italic;font-weight:500;background:linear-gradient(120deg,var(--primary) 0%,var(--accent) 80%);-webkit-background-clip:text;background-clip:text;color:transparent}
.h-section{font-family:var(--serif);font-weight:500;font-size:clamp(1.8rem,3.6vw,2.8rem);line-height:1.05;letter-spacing:-0.02em}
.h-card{font-family:var(--sans);font-weight:700;font-size:clamp(1.1rem,1.5vw,1.3rem);letter-spacing:-0.01em;line-height:1.25}
.eyebrow{display:inline-flex;align-items:center;gap:10px;padding:8px 18px;background:var(--glass);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid var(--border);border-radius:var(--pill);font-size:0.78rem;font-weight:500;letter-spacing:0.05em;text-transform:uppercase;color:var(--primary)}
.eyebrow::before{content:"";width:6px;height:6px;border-radius:50%;background:var(--accent);box-shadow:0 0 0 4px rgba(232,168,56,0.25);animation:p 2s ease-in-out infinite}
@keyframes p{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(1.4);opacity:0.6}}
.lead{font-size:clamp(1rem,1.18vw,1.12rem);line-height:1.6;color:var(--dark-soft);max-width:62ch}
.muted{color:var(--muted)}

.btn{position:relative;display:inline-flex;align-items:center;justify-content:center;gap:10px;padding:16px 28px;border-radius:var(--pill);font-weight:600;font-size:0.92rem;cursor:pointer;transition:transform var(--t),box-shadow var(--t),background var(--t);overflow:hidden;font-family:inherit;border:none;white-space:nowrap}
.btn:hover{transform:translateY(-2px)}
.btn:active{transform:translateY(0) scale(0.98)}
.btn svg{width:16px;height:16px;transition:transform var(--t-bounce)}
.btn:hover svg{transform:translateX(3px)}
.btn-primary{background:linear-gradient(135deg,var(--accent) 0%,#D88F1A 100%);color:#fff;box-shadow:0 16px 36px rgba(232,168,56,0.4),0 0 0 1px rgba(255,255,255,0.18) inset}
.btn-primary:hover{box-shadow:0 22px 44px rgba(232,168,56,0.55)}
.btn-dark{background:var(--dark);color:#fff;box-shadow:0 16px 36px rgba(30,27,24,0.28)}
.btn-ghost{background:var(--glass);backdrop-filter:blur(20px);-webkit-backdrop-filter:blur(20px);border:1px solid var(--border);color:var(--dark)}
.btn-ghost:hover{background:var(--glass-strong);border-color:var(--border-strong)}

.glass{background:var(--glass);backdrop-filter:blur(30px) saturate(180%);-webkit-backdrop-filter:blur(30px) saturate(180%);border:1px solid var(--border);border-radius:var(--r);box-shadow:var(--shadow)}
.reveal{opacity:0;transform:translateY(30px);transition:all 0.8s cubic-bezier(0.4,0,0.2,1)}
.reveal.in{opacity:1;transform:translateY(0)}
.r1{transition-delay:0.1s}.r2{transition-delay:0.2s}.r3{transition-delay:0.3s}.r4{transition-delay:0.4s}

.navbar{position:fixed;top:18px;left:50%;transform:translateX(-50%);width:min(1200px,calc(100% - 32px));z-index:100;transition:var(--t)}
.nav-inner{display:flex;align-items:center;justify-content:space-between;gap:20px;padding:14px 14px 14px 26px;border-radius:var(--pill);background:var(--glass);backdrop-filter:blur(30px) saturate(180%);-webkit-backdrop-filter:blur(30px) saturate(180%);border:1px solid var(--border);box-shadow:0 12px 30px rgba(30,27,24,0.06)}
.navbar.scrolled .nav-inner{background:var(--glass-strong);box-shadow:0 18px 40px rgba(30,27,24,0.1)}
.nav-brand{display:flex;align-items:center;gap:12px;font-family:var(--serif);font-size:1.12rem;font-weight:700;color:var(--dark);flex-shrink:0}
.nav-logo{width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:grid;place-items:center;color:#fff;box-shadow:0 6px 16px var(--primary-glow)}
.nav-logo svg{width:20px;height:20px}
.nav-links{display:flex;align-items:center;gap:6px}
.nav-link{padding:10px 16px;font-size:0.9rem;font-weight:500;color:var(--dark-soft);border-radius:var(--pill);transition:var(--t);position:relative}
.nav-link:hover{color:var(--dark)}
.nav-link.active{color:var(--primary);background:rgba(13,107,78,0.06)}
.nav-cta{display:flex;align-items:center;gap:8px;flex-shrink:0}
.nav-lang{position:relative;padding:8px 14px;border-radius:var(--pill);font-size:0.82rem;font-weight:500;color:var(--dark-soft);background:rgba(255,252,248,0.5);border:1px solid var(--border);cursor:pointer;display:flex;align-items:center;gap:6px}
.nav-lang svg{width:14px;height:14px}
.btn-nav{padding:10px 20px;font-size:0.85rem}
.burger{display:none;width:42px;height:42px;border-radius:50%;background:var(--glass);border:1px solid var(--border);align-items:center;justify-content:center;cursor:pointer}
.burger span{display:block;width:18px;height:2px;background:var(--dark);position:relative}
.burger span::before,.burger span::after{content:"";position:absolute;left:0;width:18px;height:2px;background:var(--dark);transition:var(--t)}
.burger span::before{top:-6px}.burger span::after{top:6px}
.burger.active span{background:transparent}
.burger.active span::before{top:0;transform:rotate(45deg)}
.burger.active span::after{top:0;transform:rotate(-45deg)}
.mobile-menu{position:fixed;inset:0;z-index:99;background:var(--glass-strong);backdrop-filter:blur(40px) saturate(180%);-webkit-backdrop-filter:blur(40px) saturate(180%);padding:90px 24px 30px;transform:translateY(-100%);transition:transform 0.5s cubic-bezier(0.4,0,0.2,1);overflow-y:auto;display:flex;flex-direction:column}
.mobile-menu.open{transform:translateY(0)}
.mobile-menu a{display:block;padding:16px 20px;font-size:1.05rem;font-weight:500;color:var(--dark);border-radius:var(--r-sm);margin-bottom:6px}

.page-hero{padding:160px 0 70px;text-align:center;position:relative}
.page-hero h1{margin-top:18px}
.page-hero .lead{margin:18px auto 0;text-align:center}

section{padding:80px 0;position:relative}

.footer{background:linear-gradient(135deg,#1E1B18 0%,#2A2520 100%);color:rgba(255,253,249,0.75);padding:80px 0 36px;position:relative;overflow:hidden;margin-top:80px}
.footer::before{content:"";position:absolute;top:-50%;left:-20%;width:60%;height:200%;background:radial-gradient(ellipse,rgba(13,107,78,0.18),transparent 60%);pointer-events:none}
.footer-grid{display:grid;grid-template-columns:1.5fr 1fr 1fr 1fr;gap:50px;margin-bottom:50px;position:relative;z-index:2}
.footer-brand{display:flex;align-items:center;gap:14px;margin-bottom:20px;color:#fff;font-family:var(--serif);font-size:1.3rem;font-weight:700}
.footer-about{font-size:0.9rem;line-height:1.65;color:rgba(255,253,249,0.6);margin-bottom:24px;max-width:38ch}
.footer-social{display:flex;gap:10px}
.footer-social a{width:40px;height:40px;border-radius:50%;border:1px solid rgba(255,253,249,0.15);display:grid;place-items:center;color:rgba(255,253,249,0.7);transition:var(--t)}
.footer-social a:hover{background:var(--accent);color:var(--dark);border-color:var(--accent);transform:translateY(-3px)}
.footer-social svg{width:16px;height:16px}
.footer-col h4{color:#fff;font-size:0.82rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:20px}
.footer-col ul{display:flex;flex-direction:column;gap:10px}
.footer-col a{color:rgba(255,253,249,0.7);font-size:0.9rem;transition:var(--t)}
.footer-col a:hover{color:var(--accent);transform:translateX(4px)}
.footer-contact-line{display:flex;align-items:flex-start;gap:10px;font-size:0.9rem;color:rgba(255,253,249,0.7);line-height:1.5;margin-bottom:14px}
.footer-contact-line svg{width:16px;height:16px;color:var(--accent);flex-shrink:0;margin-top:3px}
.footer-bottom{border-top:1px solid rgba(255,253,249,0.1);padding-top:28px;display:flex;flex-wrap:wrap;gap:18px;justify-content:space-between;font-size:0.82rem;color:rgba(255,253,249,0.5);position:relative;z-index:2}

@media (max-width:1024px){.nav-links,.btn-nav{display:none}.burger{display:flex}.footer-grid{grid-template-columns:1fr 1fr;gap:40px}}
@media (max-width:768px){section{padding:60px 0}.page-hero{padding:130px 0 50px}.footer-grid{grid-template-columns:1fr}.footer{padding:60px 0 30px}.container{width:calc(100% - 32px)}}
@media (max-width:480px){
    section{padding:48px 0}
    .page-hero{padding:110px 0 36px}
    .container{width:calc(100% - 24px)}
    body{font-size:14px}
    .h-display{font-size:clamp(1.7rem,8.5vw,2.6rem)}
    .h-section{font-size:clamp(1.5rem,6.5vw,2.1rem)}
    .lead{font-size:0.92rem;line-height:1.5}
    .btn{padding:13px 22px;font-size:0.86rem;border-radius:80px}
    .btn svg{width:14px;height:14px}
    .eyebrow{padding:6px 14px;font-size:0.72rem;letter-spacing:0.04em}
    .navbar{top:10px;width:calc(100% - 20px)}
    .nav-inner{padding:8px 8px 8px 14px;border-radius:80px}
    .nav-brand{font-size:1rem;gap:8px}
    .nav-logo{width:32px;height:32px;border-radius:10px}
    .nav-logo svg{width:17px;height:17px}
    .footer{padding:48px 0 24px}
    .footer-grid{gap:28px;margin-bottom:36px}
    .footer-brand{font-size:1.1rem}
    .footer-about{font-size:0.85rem}
    .footer-col h4{font-size:0.78rem;margin-bottom:14px}
    .footer-col a,.footer-contact-line{font-size:0.85rem}
    .footer-bottom{font-size:0.74rem;gap:10px;padding-top:20px}
}
@media (max-width:380px){
    .container{width:calc(100% - 20px)}
    .h-display{font-size:clamp(1.55rem,9vw,2.3rem)}
    .h-section{font-size:clamp(1.4rem,7vw,1.9rem)}
    .lead{font-size:0.88rem}
    .btn{padding:12px 18px;font-size:0.82rem}
    .nav-brand{font-size:0.92rem}
    .navbar{top:8px;width:calc(100% - 16px)}
    .nav-inner{padding:6px 6px 6px 12px}
}
@media (min-width:1280px){body{font-size:16px}.h-display{font-size:clamp(2.6rem,4.6vw,4.8rem)}.h-section{font-size:clamp(2rem,3.4vw,3rem)}.lead{font-size:1.12rem}}
@media (min-width:1440px){.container{width:min(1380px,90vw)}body{font-size:16.5px}.page-hero{padding:170px 0 80px}}
@media (min-width:1920px){.container{width:min(1480px,80vw)}body{font-size:17px}}
@media (prefers-reduced-motion:reduce){*,*::before,*::after{animation-duration:0.01ms!important;transition-duration:0.01ms!important}}
CSS;
}

function vpy_public_head($title, $description = '', $extra_css = '') {
    $is_cyrl = vpy_lang_code() === 'uz_cyrillic';
    $css = vpy_public_css();
    echo '<!DOCTYPE html><html lang="' . ($is_cyrl ? 'uz-Cyrl' : 'uz') . '"><head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">';
    echo '<meta name="theme-color" content="#0D6B4E">';
    echo '<meta name="description" content="' . e($description ?: vpy_setting('site_description')) . '">';
    echo '<title>' . e($title) . ' — ' . e(t('site_name')) . '</title>';
    echo '<link rel="icon" type="image/svg+xml" href="' . e(vpy_favicon_url()) . '">';
    echo '<link rel="manifest" href="/manifest.json">';
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">';
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>';
    echo '<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,700;1,500&display=swap" rel="stylesheet">';
    echo '<style>' . $css . $extra_css . '</style></head><body>';
    echo '<div class="mesh-bg"><div class="mesh-blob b1"></div><div class="mesh-blob b2"></div><div class="mesh-blob b3"></div></div>';
}

function vpy_public_navbar($current = '') {
    $is_cyrl = vpy_lang_code() === 'uz_cyrillic';
    $items = [
        ['/', 'home', t('nav_home')],
        ['/tariflar.php', 'tariflar', t('nav_tariffs')],
        ['/blog.php', 'blog', t('nav_blog')],
        ['/aloqa.php', 'aloqa', t('nav_contact')],
    ];
    echo '<header class="navbar" id="navbar"><div class="nav-inner">';
    echo '<a href="/" class="nav-brand"><span class="nav-logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2 4 4 8-8 4 4"/></svg></span>' . e(t('site_name')) . '</a>';
    echo '<nav class="nav-links">';
    foreach ($items as $it) {
        $cls = $current === $it[1] ? 'active' : '';
        echo '<a href="' . e($it[0]) . '" class="nav-link ' . $cls . '">' . e($it[2]) . '</a>';
    }
    echo '</nav>';
    echo '<div class="nav-cta">';
    echo '<a class="nav-lang" href="?lang=' . ($is_cyrl ? 'uz_latin' : 'uz_cyrillic') . '"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/></svg>' . ($is_cyrl ? 'Lt' : 'Кр') . '</a>';
    if (vpy_is_logged()) {
        echo '<a href="' . (vpy_is_admin() ? '/admin/' : '/user/') . '" class="btn btn-dark btn-nav">' . e(t('nav_dashboard')) . '</a>';
    } else {
        echo '<a href="/login.php" class="nav-link">' . e(t('nav_login')) . '</a>';
        echo '<a href="/register.php" class="btn btn-primary btn-nav">' . e(t('nav_register')) . '</a>';
    }
    echo '<button class="burger" id="burger" aria-label="Menyu"><span></span></button>';
    echo '</div></div></header>';
    echo '<div class="mobile-menu" id="mobileMenu">';
    foreach ($items as $it) echo '<a href="' . e($it[0]) . '">' . e($it[2]) . '</a>';
    if (vpy_is_logged()) {
        echo '<a href="' . (vpy_is_admin() ? '/admin/' : '/user/') . '" class="btn btn-dark" style="margin-top:18px;text-align:center">' . e(t('nav_dashboard')) . '</a>';
    } else {
        echo '<a href="/login.php" class="btn btn-ghost" style="margin-top:18px;text-align:center">' . e(t('nav_login')) . '</a>';
        echo '<a href="/register.php" class="btn btn-primary" style="margin-top:8px;text-align:center">' . e(t('nav_register')) . '</a>';
    }
    echo '</div>';
}

function vpy_public_footer() {
    $year = date('Y');
    $storage_js = vpy_storage_js();
    echo '<script>' . $storage_js . '</script>';
    echo <<<HTML
<footer class="footer"><div class="container">
    <div class="footer-grid">
        <div>
            <a href="/" class="footer-brand">
                <span class="nav-logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2 4 4 8-8 4 4"/></svg></span>
HTML;
    echo e(t('site_name'));
    echo '</a><p class="footer-about">' . e(t('footer_about')) . '</p>';
    echo '<div class="footer-social">';
    echo '<a href="https://t.me/' . e(ltrim(vpy_setting('contact_telegram', 'vatanparvaryaypan'), '@')) . '" target="_blank" aria-label="Telegram"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg></a>';
    echo '<a href="https://instagram.com/' . e(vpy_setting('contact_instagram', 'vatanparvaryaypan')) . '" target="_blank" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>';
    echo '<a href="https://youtube.com/@' . e(vpy_setting('contact_youtube', 'vatanparvaryaypan')) . '" target="_blank" aria-label="YouTube"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg></a>';
    echo '</div></div>';
    echo '<div class="footer-col"><h4>' . e(t('footer_links')) . '</h4><ul>';
    echo '<li><a href="/">' . e(t('nav_home')) . '</a></li>';
    echo '<li><a href="/tariflar.php">' . e(t('nav_tariffs')) . '</a></li>';
    echo '<li><a href="/blog.php">' . e(t('nav_blog')) . '</a></li>';
    echo '<li><a href="/aloqa.php">' . e(t('nav_contact')) . '</a></li>';
    echo '</ul></div>';
    echo '<div class="footer-col"><h4>' . e(t('footer_contact')) . '</h4>';
    echo '<div class="footer-contact-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg><span>' . e(vpy_setting('contact_address', t('footer_address_value'))) . '</span></div>';
    echo '<div class="footer-contact-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg><span>' . e(vpy_setting('contact_phone', t('footer_phone_value'))) . '</span></div>';
    echo '<div class="footer-contact-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg><span>' . e(vpy_setting('contact_email', t('footer_email_value'))) . '</span></div>';
    echo '</div>';
    echo '<div class="footer-col"><h4>' . e(t('footer_hours')) . '</h4>';
    echo '<div class="footer-contact-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><span>' . e(t('footer_hours_value')) . '</span></div>';
    echo '<h4 style="margin-top:24px">' . e(t('site_city')) . '</h4>';
    echo '<div class="footer-contact-line"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg><span>' . e(t('footer_address_value')) . '</span></div>';
    echo '</div></div>';
    echo '<div class="footer-bottom"><span>© ' . $year . ' ' . e(t('site_name')) . '. ' . e(t('footer_rights')) . '.</span><span>' . e(t('footer_made')) . ' ' . e(t('site_city')) . '</span></div>';
    echo '</div></footer>';
    echo <<<HTML
<script>
(function(){
    var nav = document.getElementById('navbar');
    function ns(){ if(window.scrollY > 30) nav.classList.add('scrolled'); else nav.classList.remove('scrolled'); }
    if(nav){ ns(); window.addEventListener('scroll', ns, {passive:true}); }
    var burger = document.getElementById('burger');
    var mm = document.getElementById('mobileMenu');
    if (burger && mm) {
        burger.addEventListener('click', function(){
            burger.classList.toggle('active');
            mm.classList.toggle('open');
            document.body.style.overflow = mm.classList.contains('open') ? 'hidden' : '';
        });
        mm.querySelectorAll('a').forEach(function(a){
            a.addEventListener('click', function(){ burger.classList.remove('active'); mm.classList.remove('open'); document.body.style.overflow = ''; });
        });
    }
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function(entries){
            entries.forEach(function(en){ if (en.isIntersecting){ en.target.classList.add('in'); io.unobserve(en.target); } });
        }, {threshold:0.12, rootMargin:'0px 0px -60px 0px'});
        document.querySelectorAll('.reveal').forEach(function(el){ io.observe(el); });
    } else {
        document.querySelectorAll('.reveal').forEach(function(el){ el.classList.add('in'); });
    }
})();
</script>
</body></html>
HTML;
}
