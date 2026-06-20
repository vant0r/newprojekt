<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (vpy_is_logged()) vpy_redirect(vpy_is_admin() ? '/admin/' : '/user/');

$error = '';
$vals = ['name' => '', 'phone' => '', 'referral' => vpy_get('ref', '')];
if (vpy_is_post()) {
    if (!vpy_csrf_check(vpy_post('csrf'))) { $error = t('xato_csrf'); }
    else {
        $vals['name'] = vpy_post('name');
        $vals['phone'] = vpy_post('phone');
        $vals['referral'] = vpy_post('referral');
        $r = vpy_register($vals['name'], $vals['phone'], vpy_post('password'), $vals['referral']);
        if ($r['ok']) {
            $tariff = (int)vpy_get('tarif');
            vpy_redirect($tariff ? '/user/tariflar.php?tarif=' . $tariff : '/user/');
        }
        $error = $r['error'];
    }
}
$lang = vpy_lang_code();
$is_cyrl = $lang === 'uz_cyrillic';
$login_images = array_filter([vpy_setting('login_image_1',''),vpy_setting('login_image_2',''),vpy_setting('login_image_3','')]);
?>
<!DOCTYPE html>
<html lang="<?= $is_cyrl ? 'uz-Cyrl' : 'uz' ?>" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#1A5FB4">
<meta name="robots" content="noindex">
<title><?= e(t('auth_register_title')) ?> — VatanParvar</title>
<link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
<script>!function(){var t=localStorage.getItem("vpy_theme");if(t)document.documentElement.setAttribute("data-theme",t)}()</script>
<script>document.addEventListener("keydown",function(e){if((e.ctrlKey||e.metaKey)&&e.key==="u"){e.preventDefault();window.location="/ogoh.php"}});document.addEventListener("contextmenu",function(e){e.preventDefault();window.location="/ogoh.php"});</script>
<style>
:root{--bg:#F4F6F9;--primary:#1A5FB4;--primary-dark:#144A8C;--primary-glow:rgba(26,95,180,0.18);--dark:#1A1D23;--dark-soft:#353A45;--muted:#6B7280;--light:#FFFFFF;--glass:rgba(255,255,255,0.80);--glass-strong:rgba(255,255,255,0.92);--border:rgba(26,95,180,0.12);--border-strong:rgba(26,95,180,0.22);--shadow:0 8px 32px rgba(26,95,180,0.10);--r:18px;--r-lg:28px;--pill:100px;--t:0.35s cubic-bezier(0.4,0,0.2,1);--sans:"Manrope",sans-serif;--serif:"Playfair Display",serif}
[data-theme="dark"]{--bg:#0F1117;--primary:#3D7DD4;--primary-dark:#2B6CBF;--dark:#F0F4FF;--dark-soft:#C5CEDF;--muted:#7A8BA8;--light:#0F1117;--glass:rgba(26,32,50,0.85);--glass-strong:rgba(26,32,50,0.95);--border:rgba(61,125,212,0.18);--border-strong:rgba(61,125,212,0.30);--shadow:0 8px 32px rgba(0,0,0,0.28)}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:var(--sans);font-size:16px;line-height:1.6;color:var(--dark);background:var(--bg);min-height:100vh;display:flex;overflow:hidden;transition:background var(--t)}
.layout{display:grid;grid-template-columns:1fr 1fr;width:100%;min-height:100vh}
.left{position:relative;overflow:hidden;background:linear-gradient(135deg,#1A5FB4,#0D3B7A)}
.left::after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(26,95,180,0.4),rgba(13,59,122,0.75));z-index:1}
.slide-container{position:absolute;inset:0}.slide-img{position:absolute;inset:0;object-fit:cover;width:100%;height:100%;opacity:0;transition:opacity 1.2s ease}.slide-img.active{opacity:1}
.left-content{position:absolute;bottom:60px;left:40px;right:40px;z-index:2;color:#fff}
.left-content h2{font-family:var(--serif);font-size:clamp(1.4rem,2.5vw,2rem);font-weight:700;margin-bottom:10px}
.left-content p{font-size:0.9rem;color:rgba(255,255,255,0.8);line-height:1.6}
.right{display:flex;flex-direction:column;justify-content:center;align-items:center;padding:40px 36px;position:relative;z-index:2}
.top-bar{position:absolute;top:20px;left:20px;right:20px;display:flex;justify-content:space-between;align-items:center}
.back-btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;background:var(--glass);backdrop-filter:blur(16px);border:1px solid var(--border);border-radius:var(--pill);font-size:0.82rem;font-weight:500;color:var(--dark);transition:var(--t)}
.back-btn:hover{background:var(--glass-strong)}
.back-btn svg{width:13px;height:13px}
.login-link{padding:8px 14px;background:var(--glass);backdrop-filter:blur(16px);border:1px solid var(--border);border-radius:var(--pill);font-size:0.82rem;font-weight:600;color:var(--primary);transition:var(--t)}
.login-link:hover{background:var(--primary);color:#fff}
.panel{width:100%;max-width:420px;animation:slideIn 0.5s cubic-bezier(0.34,1.56,0.64,1)}
@keyframes slideIn{from{opacity:0;transform:translateX(20px)}to{opacity:1;transform:translateX(0)}}
.brand{display:inline-flex;align-items:center;gap:10px;margin-bottom:28px;font-family:var(--serif);font-weight:700;font-size:1.05rem;color:var(--dark)}
.brand-logo{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:grid;place-items:center;color:#fff;box-shadow:0 4px 12px var(--primary-glow)}
.brand-logo svg{width:18px;height:18px}
h1{font-family:var(--serif);font-weight:700;font-size:clamp(1.5rem,2.5vw,2rem);line-height:1.1;margin-bottom:6px}
h1 em{font-style:italic;color:var(--primary)}
.sub{color:var(--muted);margin-bottom:24px;font-size:0.9rem}
.error{background:rgba(220,53,69,0.08);border:1px solid rgba(220,53,69,0.2);color:#A81D2B;padding:12px 16px;border-radius:12px;margin-bottom:16px;font-size:0.88rem;display:flex;align-items:center;gap:8px;animation:shake 0.4s ease}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}
.error svg{width:16px;height:16px;flex-shrink:0}
.field{position:relative;margin-bottom:14px}
.field label{display:block;font-size:0.78rem;font-weight:600;color:var(--dark-soft);margin-bottom:5px}
.field input{width:100%;padding:13px 16px 13px 42px;border-radius:12px;border:1.5px solid var(--border-strong);background:var(--glass);color:var(--dark);font-size:0.92rem;transition:border-color var(--t),box-shadow var(--t)}
.field input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--primary-glow)}
.field-icon{position:absolute;left:14px;top:36px;color:var(--muted);pointer-events:none}
.field-icon svg{width:15px;height:15px}
.toggle-pwd{position:absolute;right:12px;top:32px;width:32px;height:32px;border-radius:8px;border:none;background:none;color:var(--muted);cursor:pointer;display:grid;place-items:center;transition:var(--t)}
.toggle-pwd:hover{background:var(--primary-glow);color:var(--primary)}
.toggle-pwd svg{width:15px;height:15px}
.strength{display:flex;gap:3px;margin-top:6px}
.strength span{flex:1;height:3px;border-radius:2px;background:var(--border);transition:var(--t)}
.strength.s1 span:nth-child(1){background:#DC3545}
.strength.s2 span:nth-child(-n+2){background:#F39C12}
.strength.s3 span:nth-child(-n+3){background:#2185D0}
.strength.s4 span{background:#1A5FB4}
.terms{display:flex;align-items:flex-start;gap:8px;font-size:0.82rem;color:var(--dark-soft);margin:16px 0;cursor:pointer;line-height:1.4}
.terms input{width:16px;height:16px;border-radius:4px;accent-color:var(--primary);margin-top:1px;flex-shrink:0}
.btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:15px;border-radius:var(--pill);border:none;font-family:inherit;font-weight:700;font-size:0.94rem;cursor:pointer;background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;box-shadow:0 8px 24px var(--primary-glow);transition:transform var(--t),box-shadow var(--t)}
.btn:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(26,95,180,0.3)}
.btn svg{width:16px;height:16px}
.alt{text-align:center;font-size:0.88rem;color:var(--dark-soft);margin-top:20px}
.alt a{color:var(--primary);font-weight:700}
@media (max-width:900px){
    .layout{grid-template-columns:1fr}
    .left{position:fixed;inset:0;z-index:0}
    .left::after{background:linear-gradient(180deg,rgba(15,17,23,0.6),rgba(15,17,23,0.85))}
    .right{position:relative;z-index:2;background:transparent;padding:90px 24px 40px}
    .panel{background:var(--glass);backdrop-filter:blur(28px);border:1px solid var(--border);border-radius:var(--r-lg);padding:32px 24px;box-shadow:var(--shadow)}
    .left-content{display:none}
}
</style>
</head>
<body>
<div class="layout">
    <aside class="left">
        <div class="slide-container">
            <?php if (!empty($login_images)): foreach ($login_images as $i => $img): ?>
            <img class="slide-img <?= $i===0?'active':'' ?>" src="<?= e($img) ?>" alt="">
            <?php endforeach; else: ?>
            <div style="width:100%;height:100%;background:linear-gradient(135deg,#1A5FB4,#0D3B7A)"></div>
            <?php endif; ?>
        </div>
        <div class="left-content">
            <h2>VatanParvar Yaypan</h2>
            <p><?= e(t('hero_subtitle')) ?></p>
        </div>
    </aside>
    <section class="right">
        <div class="top-bar">
            <a href="/login.php" class="back-btn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M19 12H5M12 19l-7-7 7-7"/></svg><?= e(t('btn_back')) ?></a>
            <a href="/login.php" class="login-link"><?= e(t('nav_login')) ?></a>
        </div>
        <div class="panel">
            <a href="/" class="brand"><span class="brand-logo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M3 12l2-2 4 4 8-8 4 4"/></svg></span>VatanParvar</a>
            <h1><em><?= e(t('auth_register_title')) ?></em></h1>
            <p class="sub"><?= e(t('auth_register_subtitle')) ?></p>
            <?php if ($error): ?>
            <div class="error"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg><span><?= e($error) ?></span></div>
            <?php endif; ?>
            <form method="post" autocomplete="on" novalidate>
                <input type="hidden" name="csrf" value="<?= e(vpy_csrf()) ?>">
                <div class="field"><label for="name"><?= e(t('auth_name')) ?></label><input id="name" name="name" type="text" required placeholder="<?= e(t('auth_name_ph')) ?>" value="<?= e($vals['name']) ?>"><span class="field-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span></div>
                <div class="field"><label for="phone"><?= e(t('auth_phone')) ?></label><input id="phone" name="phone" type="tel" inputmode="tel" required placeholder="<?= e(t('auth_phone_ph')) ?>" value="<?= e($vals['phone']) ?>"><span class="field-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg></span></div>
                <div class="field"><label for="password"><?= e(t('auth_password')) ?></label><input id="password" name="password" type="password" required placeholder="<?= e(t('auth_password_ph')) ?>" minlength="6"><span class="field-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg></span><button type="button" class="toggle-pwd" id="togglePwd"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button><div class="strength" id="strength"><span></span><span></span><span></span><span></span></div></div>
                <div class="field"><label for="referral"><?= e(t('auth_referral')) ?></label><input id="referral" name="referral" type="text" placeholder="<?= e(t('auth_referral_ph')) ?>" value="<?= e($vals['referral']) ?>" maxlength="12" style="text-transform:uppercase"><span class="field-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/></svg></span></div>
                <label class="terms"><input type="checkbox" name="terms" value="1" required checked><span><?= e(t('auth_terms')) ?></span></label>
                <button type="submit" class="btn"><?= e(t('auth_register_btn')) ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button>
            </form>
            <p class="alt"><?= e(t('auth_have_account')) ?> <a href="/login.php"><?= e(t('auth_login_btn')) ?></a></p>
        </div>
    </section>
</div>
<script>
(function(){
    var pwd=document.getElementById('password'),btn=document.getElementById('togglePwd');
    btn.onclick=function(){pwd.type=pwd.type==='password'?'text':'password';};
    var phone=document.getElementById('phone');
    phone.addEventListener('input',function(e){var v=e.target.value.replace(/\D/g,'');if(v.startsWith('998'))v=v.substring(3);if(v.length>9)v=v.substring(0,9);var f='+998';if(v.length>0)f+=' '+v.substring(0,2);if(v.length>2)f+=' '+v.substring(2,5);if(v.length>5)f+=' '+v.substring(5,7);if(v.length>7)f+=' '+v.substring(7,9);e.target.value=f;});
    var s=document.getElementById('strength');
    pwd.addEventListener('input',function(){var v=pwd.value,l=0;if(v.length>=6)l=1;if(v.length>=8&&/\d/.test(v))l=2;if(v.length>=8&&/[A-Z]/.test(v)&&/\d/.test(v))l=3;if(v.length>=10&&/[A-Z]/.test(v)&&/\d/.test(v)&&/[^A-Za-z0-9]/.test(v))l=4;s.className='strength s'+l;});
    var imgs=document.querySelectorAll('.slide-img');
    if(imgs.length>1){var c=0;setInterval(function(){imgs[c].classList.remove('active');c=(c+1)%imgs.length;imgs[c].classList.add('active');},4000);}
})();
</script>
</body>
</html>
