<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (vpy_is_logged()) vpy_redirect(vpy_is_admin() ? '/admin/' : '/user/');

$error = '';
$phone_val = '';
if (vpy_is_post()) {
    if (!vpy_csrf_check(vpy_post('csrf'))) { $error = t('xato_csrf'); }
    else {
        $phone_val = vpy_post('phone');
        $r = vpy_login($phone_val, vpy_post('password'));
        if ($r['ok']) {
            $redirect = $_SESSION['vpy_login_redirect'] ?? null;
            unset($_SESSION['vpy_login_redirect']);
            vpy_redirect(vpy_safe_redirect_target($redirect, $r['user']['role'] === 'admin' ? '/admin/' : '/user/'));
        }
        $error = $r['error'];
    }
}
$lang = vpy_lang_code();
$is_cyrl = $lang === 'uz_cyrillic';

// Get login images from settings
$login_images = array_filter([
    vpy_setting('login_image_1', ''),
    vpy_setting('login_image_2', ''),
    vpy_setting('login_image_3', ''),
]);
?>
<!DOCTYPE html>
<html lang="<?= $is_cyrl ? 'uz-Cyrl' : 'uz' ?>" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#1A5FB4">
<meta name="robots" content="noindex">
<title><?= e(t('auth_login_title')) ?> — VatanParvar</title>
<link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
<script>!function(){var t=localStorage.getItem("vpy_theme");if(t)document.documentElement.setAttribute("data-theme",t)}()</script>
<script>document.addEventListener("keydown",function(e){if((e.ctrlKey||e.metaKey)&&e.key==="u"){e.preventDefault();window.location="/ogoh.php"}});document.addEventListener("contextmenu",function(e){e.preventDefault();window.location="/ogoh.php"});</script>
<style>
:root{--bg:#F4F6F9;--primary:#1A5FB4;--primary-dark:#144A8C;--primary-glow:rgba(26,95,180,0.18);--dark:#1A1D23;--dark-soft:#353A45;--muted:#6B7280;--light:#FFFFFF;--glass:rgba(255,255,255,0.80);--glass-strong:rgba(255,255,255,0.92);--border:rgba(26,95,180,0.12);--border-strong:rgba(26,95,180,0.22);--shadow:0 8px 32px rgba(26,95,180,0.10);--r:18px;--r-lg:28px;--pill:100px;--t:0.35s cubic-bezier(0.4,0,0.2,1);--sans:"Manrope",sans-serif;--serif:"Playfair Display",serif}
[data-theme="dark"]{--bg:#0F1117;--primary:#3D7DD4;--primary-dark:#2B6CBF;--dark:#F0F4FF;--dark-soft:#C5CEDF;--muted:#7A8BA8;--light:#0F1117;--glass:rgba(26,32,50,0.85);--glass-strong:rgba(26,32,50,0.95);--border:rgba(61,125,212,0.18);--border-strong:rgba(61,125,212,0.30);--shadow:0 8px 32px rgba(0,0,0,0.28)}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--sans);font-size:16px;line-height:1.6;color:var(--dark);background:var(--bg);min-height:100vh;display:flex;overflow:hidden;transition:background var(--t),color var(--t)}
.layout{display:grid;grid-template-columns:1fr 1fr;width:100%;min-height:100vh}
/* LEFT - FORM */
.left{display:flex;flex-direction:column;justify-content:center;align-items:center;padding:48px 40px;position:relative;z-index:2}
.panel{width:100%;max-width:420px;animation:slideIn 0.5s cubic-bezier(0.34,1.56,0.64,1)}
@keyframes slideIn{from{opacity:0;transform:translateX(-20px)}to{opacity:1;transform:translateX(0)}}
.top-bar{position:absolute;top:20px;left:20px;right:20px;display:flex;justify-content:space-between;align-items:center}
.back-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:var(--glass);backdrop-filter:blur(16px);border:1px solid var(--border);border-radius:var(--pill);font-size:0.82rem;font-weight:500;color:var(--dark);transition:var(--t)}
.back-btn:hover{background:var(--glass-strong);transform:translateX(-2px)}
.back-btn svg{width:13px;height:13px}
.register-link{padding:8px 14px;background:var(--glass);backdrop-filter:blur(16px);border:1px solid var(--border);border-radius:var(--pill);font-size:0.82rem;font-weight:600;color:var(--primary);transition:var(--t)}
.register-link:hover{background:var(--primary);color:#fff}
.brand{display:inline-flex;align-items:center;gap:10px;margin-bottom:32px;font-family:var(--serif);font-weight:700;font-size:1.05rem;color:var(--dark)}
.brand-logo{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:grid;place-items:center;color:#fff;box-shadow:0 4px 12px var(--primary-glow)}
.brand-logo svg{width:18px;height:18px}
h1{font-family:var(--serif);font-weight:700;font-size:clamp(1.6rem,3vw,2.2rem);line-height:1.1;margin-bottom:8px}
h1 em{font-style:italic;color:var(--primary)}
.sub{color:var(--muted);margin-bottom:28px;font-size:0.92rem}
.error{background:rgba(220,53,69,0.08);border:1px solid rgba(220,53,69,0.2);color:#A81D2B;padding:12px 16px;border-radius:var(--r-sm,12px);margin-bottom:18px;font-size:0.88rem;display:flex;align-items:center;gap:8px;animation:shake 0.4s ease}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}
.error svg{width:16px;height:16px;flex-shrink:0}
.field{position:relative;margin-bottom:16px}
.field label{display:block;font-size:0.78rem;font-weight:600;color:var(--dark-soft);margin-bottom:6px}
.field input{width:100%;padding:14px 16px 14px 44px;border-radius:12px;border:1.5px solid var(--border-strong);background:var(--glass);color:var(--dark);font-size:0.94rem;transition:border-color var(--t),box-shadow var(--t)}
.field input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-glow)}
.field-icon{position:absolute;left:15px;top:38px;color:var(--muted);pointer-events:none}
.field-icon svg{width:16px;height:16px}
.toggle-pwd{position:absolute;right:12px;top:34px;width:34px;height:34px;border-radius:8px;color:var(--muted);cursor:pointer;display:grid;place-items:center;transition:var(--t);border:none;background:none}
.toggle-pwd:hover{background:var(--primary-glow);color:var(--primary)}
.toggle-pwd svg{width:16px;height:16px}
.row{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;gap:8px}
.remember{display:inline-flex;align-items:center;gap:7px;font-size:0.85rem;color:var(--dark-soft);cursor:pointer}
.remember input{width:16px;height:16px;border-radius:4px;accent-color:var(--primary)}
.forgot{font-size:0.84rem;color:var(--primary);font-weight:600}
.btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:15px;border-radius:var(--pill);border:none;font-family:inherit;font-weight:700;font-size:0.94rem;cursor:pointer;background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;box-shadow:0 8px 24px var(--primary-glow);transition:transform var(--t),box-shadow var(--t)}
.btn:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(26,95,180,0.3)}
.btn:active{transform:scale(0.98)}
.btn svg{width:16px;height:16px}
.divider{display:flex;align-items:center;gap:12px;margin:24px 0 18px;color:var(--muted);font-size:0.82rem}
.divider::before,.divider::after{content:"";flex:1;height:1px;background:var(--border)}
.alt{text-align:center;font-size:0.9rem;color:var(--dark-soft)}
.alt a{color:var(--primary);font-weight:700}
/* RIGHT - IMAGE SLIDESHOW */
.right{position:relative;overflow:hidden;background:linear-gradient(135deg,#1A5FB4 0%,#0D3B7A 100%)}
.right::before{content:"";position:absolute;inset:0;background:radial-gradient(ellipse at 30% 70%,rgba(93,173,226,0.2),transparent 50%);pointer-events:none;z-index:1}
.slide-container{position:absolute;inset:0}
.slide-img{position:absolute;inset:0;object-fit:cover;width:100%;height:100%;opacity:0;transition:opacity 1.2s ease}
.slide-img.active{opacity:1}
.slide-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(26,95,180,0.3) 0%,rgba(13,59,122,0.7) 100%);z-index:2}
.right-content{position:absolute;bottom:60px;left:40px;right:40px;z-index:3;color:#fff}
.right-content h2{font-family:var(--serif);font-size:clamp(1.4rem,2.5vw,2rem);font-weight:700;margin-bottom:10px;text-shadow:0 2px 12px rgba(0,0,0,0.3)}
.right-content p{font-size:0.92rem;color:rgba(255,255,255,0.8);line-height:1.6;max-width:38ch}
.dots{display:flex;gap:6px;margin-top:16px}
.dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,0.4);transition:var(--t)}
.dot.active{background:#fff;width:24px;border-radius:4px}
@media (max-width:900px){
    .layout{grid-template-columns:1fr}
    .right{position:fixed;inset:0;z-index:0}
    .slide-overlay{background:linear-gradient(180deg,rgba(15,17,23,0.6) 0%,rgba(15,17,23,0.85) 100%)}
    .left{position:relative;z-index:2;background:transparent;padding:100px 24px 40px}
    .panel{background:var(--glass);backdrop-filter:blur(28px);border:1px solid var(--border);border-radius:var(--r-lg);padding:36px 28px;box-shadow:var(--shadow)}
    .right-content{display:none}
}
</style>
</head>
<body>
<div class="layout">
    <section class="left">
        <div class="top-bar">
            <a href="/" class="back-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M19 12H5M12 19l-7-7 7-7"/></svg><?= e(t('btn_back')) ?></a>
            <a href="/register.php" class="register-link"><?= e(t('nav_register')) ?></a>
        </div>
        <div class="panel">
            <a href="/" class="brand"><span class="brand-logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M3 12l2-2 4 4 8-8 4 4"/></svg></span>VatanParvar</a>
            <h1><em><?= e(t('auth_login_title')) ?></em></h1>
            <p class="sub"><?= e(t('auth_login_subtitle')) ?></p>
            <?php if ($error): ?>
            <div class="error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span><?= e($error) ?></span></div>
            <?php endif; ?>
            <form method="post" autocomplete="on" novalidate>
                <input type="hidden" name="csrf" value="<?= e(vpy_csrf()) ?>">
                <div class="field">
                    <label for="phone"><?= e(t('auth_phone')) ?></label>
                    <input id="phone" name="phone" type="tel" inputmode="tel" required autocomplete="tel" placeholder="<?= e(t('auth_phone_ph')) ?>" value="<?= e($phone_val) ?>">
                    <span class="field-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg></span>
                </div>
                <div class="field">
                    <label for="password"><?= e(t('auth_password')) ?></label>
                    <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="<?= e(t('auth_password_ph')) ?>">
                    <span class="field-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg></span>
                    <button type="button" class="toggle-pwd" id="togglePwd"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
                </div>
                <div class="row">
                    <label class="remember"><input type="checkbox" name="remember" value="1" checked><?= e(t('auth_remember')) ?></label>
                    <a href="#" class="forgot"><?= e(t('auth_forgot')) ?></a>
                </div>
                <button type="submit" class="btn"><?= e(t('auth_login_btn')) ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
            </form>
            <div class="divider"><?= e(t('auth_or')) ?></div>
            <p class="alt"><?= e(t('auth_no_account')) ?> <a href="/register.php"><?= e(t('auth_register_btn')) ?></a></p>
        </div>
    </section>
    <aside class="right">
        <div class="slide-container">
            <?php if (!empty($login_images)): foreach ($login_images as $i => $img): ?>
            <img class="slide-img <?= $i === 0 ? 'active' : '' ?>" src="<?= e($img) ?>" alt="VatanParvar">
            <?php endforeach; else: ?>
            <div style="width:100%;height:100%;background:linear-gradient(135deg,#1A5FB4,#0D3B7A)"></div>
            <?php endif; ?>
        </div>
        <div class="slide-overlay"></div>
        <div class="right-content">
            <h2>VatanParvar Yaypan</h2>
            <p><?= e(t('hero_subtitle')) ?></p>
            <?php if (count($login_images) > 1): ?>
            <div class="dots"><?php for ($i=0;$i<count($login_images);$i++): ?><span class="dot <?= $i===0?'active':'' ?>"></span><?php endfor; ?></div>
            <?php endif; ?>
        </div>
    </aside>
</div>
<script>
(function(){
    var pwd=document.getElementById('password'),btn=document.getElementById('togglePwd');
    btn.onclick=function(){var is=pwd.type==='password';pwd.type=is?'text':'password';};
    var phone=document.getElementById('phone');
    phone.addEventListener('input',function(e){var v=e.target.value.replace(/\D/g,'');if(v.startsWith('998'))v=v.substring(3);if(v.length>9)v=v.substring(0,9);var f='+998';if(v.length>0)f+=' '+v.substring(0,2);if(v.length>2)f+=' '+v.substring(2,5);if(v.length>5)f+=' '+v.substring(5,7);if(v.length>7)f+=' '+v.substring(7,9);e.target.value=f;});
    // Slideshow
    var imgs=document.querySelectorAll('.slide-img');
    var dots=document.querySelectorAll('.dot');
    if(imgs.length>1){var cur=0;setInterval(function(){imgs[cur].classList.remove('active');if(dots[cur])dots[cur].classList.remove('active');cur=(cur+1)%imgs.length;imgs[cur].classList.add('active');if(dots[cur])dots[cur].classList.add('active');},4000);}
})();
</script>
</body>
</html>
