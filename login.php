<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (vpy_is_logged()) vpy_redirect(vpy_is_admin() ? '/admin/' : '/user/');

$error = '';
$phone_val = '';
if (vpy_is_post()) {
    if (!vpy_csrf_check(vpy_post('csrf'))) {
        $error = t('xato_csrf');
    } else {
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
?>
<!DOCTYPE html>
<html lang="<?= $is_cyrl ? 'uz-Cyrl' : 'uz' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0D6B4E">
<meta name="robots" content="noindex">
<title><?= e(t('auth_login_title')) ?> — <?= e(t('site_name')) ?></title>
<link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@500;700&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#FAF7F2;--primary:#0D6B4E;--primary-dark:#094D38;
    --accent:#E8A838;--accent-soft:#F5D08A;--secondary:#B7C9B3;
    --dark:#1E1B18;--dark-soft:#3B362F;--muted:#7A6F62;--light:#FFFDF9;
    --glass:rgba(255,252,248,0.7);--glass-strong:rgba(255,252,248,0.88);
    --border:rgba(180,160,130,0.25);--border-strong:rgba(180,160,130,0.45);
    --shadow:0 30px 60px rgba(30,27,24,0.1);--shadow-lg:0 50px 100px rgba(30,27,24,0.18);
    --r:22px;--r-lg:32px;--pill:100px;
    --t:0.4s cubic-bezier(0.4,0,0.2,1);
    --serif:"Playfair Display",Georgia,serif;
    --sans:"Manrope","Inter",-apple-system,BlinkMacSystemFont,system-ui,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
html{-webkit-text-size-adjust:100%}
body{
    font-family:var(--sans);
    font-size:16px;
    line-height:1.6;
    color:var(--dark);
    background:var(--bg);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:24px;
    overflow-x:hidden;
    position:relative;
    -webkit-font-smoothing:antialiased;
}
body::before{
    content:"";position:fixed;inset:0;pointer-events:none;z-index:1;opacity:0.3;mix-blend-mode:multiply;
    background-image:url("data:image/svg+xml;utf8,<svg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3'/><feColorMatrix values='0 0 0 0 0.12 0 0 0 0 0.10 0 0 0 0 0.08 0 0 0 0.4 0'/></filter><rect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/></svg>");
}
.bg-mesh{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
.blob{position:absolute;border-radius:50%;filter:blur(80px);opacity:0.55}
.blob.b1{width:55vw;height:55vw;background:radial-gradient(circle,#B7C9B3,transparent 70%);top:-20vh;left:-15vw;animation:fA 22s ease-in-out infinite}
.blob.b2{width:50vw;height:50vw;background:radial-gradient(circle,#F5D08A,transparent 70%);bottom:-15vh;right:-15vw;opacity:0.45;animation:fB 28s ease-in-out infinite}
.blob.b3{width:40vw;height:40vw;background:radial-gradient(circle,rgba(13,107,78,0.25),transparent 70%);top:50%;left:50%;animation:fA 32s ease-in-out infinite reverse}
@keyframes fA{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(8vw,5vh) scale(1.1)}}
@keyframes fB{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(-6vw,-4vh) scale(1.15)}}

.frame{
    position:relative;z-index:2;
    width:100%;
    max-width:480px;
    background:var(--glass);
    backdrop-filter:blur(30px) saturate(180%);
    -webkit-backdrop-filter:blur(30px) saturate(180%);
    border:1px solid var(--border);
    border-radius:var(--r-lg);
    padding:48px 44px;
    box-shadow:var(--shadow-lg);
    animation:appear 0.7s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes appear{from{opacity:0;transform:translateY(30px) scale(0.96)}to{opacity:1;transform:translateY(0) scale(1)}}

.brand{
    display:inline-flex;
    align-items:center;
    gap:12px;
    margin-bottom:36px;
    font-family:var(--serif);
    font-weight:700;
    font-size:1.18rem;
    color:var(--dark);
    text-decoration:none;
}
.brand-logo{
    width:40px;height:40px;
    border-radius:12px;
    background:linear-gradient(135deg,var(--primary),var(--primary-dark));
    display:grid;place-items:center;
    color:#fff;
    box-shadow:0 6px 16px rgba(13,107,78,0.3);
}
.brand-logo svg{width:22px;height:22px}

h1{
    font-family:var(--serif);
    font-weight:500;
    font-size:clamp(1.8rem,3.5vw,2.4rem);
    line-height:1.05;
    letter-spacing:-0.02em;
    margin-bottom:10px;
}
h1 em{font-style:italic;color:var(--primary)}
.sub{
    color:var(--muted);
    margin-bottom:36px;
    font-size:0.97rem;
}
.error{
    background:rgba(255,96,88,0.1);
    border:1px solid rgba(255,96,88,0.3);
    color:#C73E36;
    padding:14px 18px;
    border-radius:14px;
    margin-bottom:22px;
    font-size:0.92rem;
    display:flex;
    align-items:center;
    gap:10px;
    animation:shake 0.4s ease;
}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
.error svg{width:18px;height:18px;flex-shrink:0}

.field{position:relative;margin-bottom:18px}
.field label{
    display:block;
    font-size:0.82rem;
    font-weight:600;
    color:var(--dark-soft);
    margin-bottom:8px;
    letter-spacing:0.02em;
}
.field input{
    width:100%;
    padding:16px 20px 16px 48px;
    border-radius:14px;
    border:1px solid var(--border-strong);
    background:rgba(255,253,249,0.85);
    color:var(--dark);
    font-size:0.97rem;
    font-family:inherit;
    transition:border-color var(--t),box-shadow var(--t),background var(--t);
}
.field input:focus{
    outline:none;
    border-color:var(--primary);
    background:var(--light);
    box-shadow:0 0 0 4px rgba(13,107,78,0.12);
}
.field-icon{
    position:absolute;
    left:18px;
    top:42px;
    color:var(--muted);
    pointer-events:none;
}
.field-icon svg{width:18px;height:18px}
.field input:focus + .field-icon,.field-active .field-icon{color:var(--primary)}
.toggle-pwd{
    position:absolute;
    right:14px;
    top:38px;
    width:36px;height:36px;
    border-radius:10px;
    color:var(--muted);
    cursor:pointer;
    display:grid;place-items:center;
    transition:var(--t);
}
.toggle-pwd:hover{background:rgba(13,107,78,0.08);color:var(--primary)}
.toggle-pwd svg{width:18px;height:18px}

.row{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-bottom:24px;
    flex-wrap:wrap;
    gap:10px;
}
.remember{
    display:inline-flex;
    align-items:center;
    gap:8px;
    font-size:0.9rem;
    color:var(--dark-soft);
    cursor:pointer;
    user-select:none;
}
.remember input{display:none}
.remember .check{
    width:18px;height:18px;
    border-radius:6px;
    border:1.5px solid var(--border-strong);
    background:rgba(255,253,249,0.6);
    transition:var(--t);
    display:grid;place-items:center;
    position:relative;
}
.remember input:checked + .check{background:var(--primary);border-color:var(--primary)}
.remember .check::after{
    content:"";
    width:10px;height:6px;
    border-left:2px solid #fff;
    border-bottom:2px solid #fff;
    transform:rotate(-45deg) scale(0);
    transition:var(--t);
    margin-bottom:2px;
}
.remember input:checked + .check::after{transform:rotate(-45deg) scale(1)}
.forgot{font-size:0.88rem;color:var(--primary);font-weight:500;text-decoration:none}
.forgot:hover{color:var(--primary-dark)}

.btn{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    width:100%;
    padding:18px 28px;
    border-radius:var(--pill);
    border:none;
    font-family:inherit;
    font-weight:600;
    font-size:0.97rem;
    cursor:pointer;
    background:linear-gradient(135deg,var(--accent) 0%,#D88F1A 100%);
    color:#fff;
    box-shadow:0 18px 40px rgba(232,168,56,0.42),0 0 0 1px rgba(255,255,255,0.18) inset;
    transition:transform var(--t),box-shadow var(--t);
    position:relative;
    overflow:hidden;
}
.btn:hover{transform:translateY(-2px);box-shadow:0 24px 50px rgba(232,168,56,0.55)}
.btn:active{transform:translateY(0) scale(0.98)}
.btn svg{width:18px;height:18px;transition:transform 0.4s cubic-bezier(0.34,1.56,0.64,1)}
.btn:hover svg{transform:translateX(4px)}

.divider{
    display:flex;
    align-items:center;
    gap:14px;
    margin:28px 0 22px;
    color:var(--muted);
    font-size:0.85rem;
}
.divider::before,.divider::after{
    content:"";
    flex:1;
    height:1px;
    background:var(--border);
}

.alt{text-align:center;font-size:0.92rem;color:var(--dark-soft)}
.alt a{color:var(--primary);font-weight:600;text-decoration:none}
.alt a:hover{color:var(--primary-dark)}

.back{
    position:absolute;
    top:24px;left:24px;
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:10px 16px;
    background:var(--glass);
    backdrop-filter:blur(20px);
    border:1px solid var(--border);
    border-radius:var(--pill);
    font-size:0.85rem;
    font-weight:500;
    color:var(--dark);
    text-decoration:none;
    z-index:5;
    transition:var(--t);
}
.back:hover{background:var(--glass-strong);transform:translateX(-3px)}
.back svg{width:14px;height:14px}

.lang-switch{
    position:absolute;
    top:24px;right:24px;
    z-index:5;
    display:flex;
    gap:4px;
    padding:4px;
    background:var(--glass);
    backdrop-filter:blur(20px);
    border:1px solid var(--border);
    border-radius:var(--pill);
}
.lang-switch a{
    padding:8px 14px;
    font-size:0.82rem;
    font-weight:500;
    color:var(--dark-soft);
    text-decoration:none;
    border-radius:var(--pill);
    transition:var(--t);
}
.lang-switch a.active{background:var(--primary);color:#fff}

@media (max-width:480px){
    .frame{padding:36px 26px}
    .back,.lang-switch{top:14px}
    .back{left:14px;font-size:0.78rem;padding:8px 12px}
    .lang-switch{right:14px}
    h1{font-size:1.6rem}
}
</style>
</head>
<body>

<div class="bg-mesh">
    <div class="blob b1"></div>
    <div class="blob b2"></div>
    <div class="blob b3"></div>
</div>

<a href="/" class="back">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    <?= e(t('btn_back')) ?>
</a>

<div class="lang-switch">
    <a href="?lang=uz_latin" class="<?= !$is_cyrl ? 'active' : '' ?>">Lt</a>
    <a href="?lang=uz_cyrillic" class="<?= $is_cyrl ? 'active' : '' ?>">Кр</a>
</div>

<div class="frame">
    <a href="/" class="brand">
        <span class="brand-logo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2 4 4 8-8 4 4"/></svg>
        </span>
        <?= e(t('site_name')) ?>
    </a>

    <h1><em><?= e(t('auth_login_title')) ?></em></h1>
    <p class="sub"><?= e(t('auth_login_subtitle')) ?></p>

    <?php if ($error): ?>
        <div class="error">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= e($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="post" autocomplete="on" novalidate>
        <input type="hidden" name="csrf" value="<?= e(vpy_csrf()) ?>">

        <div class="field">
            <label for="phone"><?= e(t('auth_phone')) ?></label>
            <input id="phone" name="phone" type="tel" inputmode="tel" required autocomplete="tel" placeholder="<?= e(t('auth_phone_ph')) ?>" value="<?= e($phone_val) ?>">
            <span class="field-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
            </span>
        </div>

        <div class="field">
            <label for="password"><?= e(t('auth_password')) ?></label>
            <input id="password" name="password" type="password" required autocomplete="current-password" placeholder="<?= e(t('auth_password_ph')) ?>">
            <span class="field-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
            </span>
            <button type="button" class="toggle-pwd" id="togglePwd" aria-label="Parolni ko'rsatish">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
            </button>
        </div>

        <div class="row">
            <label class="remember">
                <input type="checkbox" name="remember" value="1" checked>
                <span class="check"></span>
                <?= e(t('auth_remember')) ?>
            </label>
            <a href="#" class="forgot"><?= e(t('auth_forgot')) ?></a>
        </div>

        <button type="submit" class="btn">
            <?= e(t('auth_login_btn')) ?>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
    </form>

    <div class="divider"><?= e(t('auth_or')) ?></div>

    <p class="alt">
        <?= e(t('auth_no_account')) ?>
        <a href="/register.php"><?= e(t('auth_register_btn')) ?></a>
    </p>
</div>

<script>
(function(){
    var btn = document.getElementById('togglePwd');
    var pwd = document.getElementById('password');
    btn.addEventListener('click', function(){
        var isPwd = pwd.type === 'password';
        pwd.type = isPwd ? 'text' : 'password';
        btn.innerHTML = isPwd
            ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>'
            : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
    });

    /* Phone formatter */
    var phone = document.getElementById('phone');
    phone.addEventListener('input', function(e){
        var v = e.target.value.replace(/\D/g, '');
        if (v.startsWith('998')) v = v.substring(3);
        if (v.length > 9) v = v.substring(0, 9);
        var f = '+998';
        if (v.length > 0) f += ' ' + v.substring(0, 2);
        if (v.length > 2) f += ' ' + v.substring(2, 5);
        if (v.length > 5) f += ' ' + v.substring(5, 7);
        if (v.length > 7) f += ' ' + v.substring(7, 9);
        e.target.value = f;
    });
    if (phone.value && !phone.value.startsWith('+998')) phone.dispatchEvent(new Event('input'));

    /* Field active states */
    document.querySelectorAll('.field input').forEach(function(inp){
        inp.addEventListener('focus', function(){ inp.closest('.field').classList.add('field-active'); });
        inp.addEventListener('blur', function(){ if (!inp.value) inp.closest('.field').classList.remove('field-active'); });
        if (inp.value) inp.closest('.field').classList.add('field-active');
    });
})();
</script>
</body>
</html>
