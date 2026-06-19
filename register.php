<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (vpy_is_logged()) vpy_redirect(vpy_is_admin() ? '/admin/' : '/user/');

$error = '';
$vals = ['name' => '', 'phone' => '', 'referral' => vpy_get('ref', '')];
if (vpy_is_post()) {
    if (!vpy_csrf_check(vpy_post('csrf'))) {
        $error = t('xato_csrf');
    } else {
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
?>
<!DOCTYPE html>
<html lang="<?= $is_cyrl ? 'uz-Cyrl' : 'uz' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0D6B4E">
<meta name="robots" content="noindex">
<title><?= e(t('auth_register_title')) ?> — <?= e(t('site_name')) ?></title>
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
    --shadow-lg:0 50px 100px rgba(30,27,24,0.18);
    --r-lg:32px;--pill:100px;
    --t:0.4s cubic-bezier(0.4,0,0.2,1);
    --serif:"Playfair Display",Georgia,serif;
    --sans:"Manrope","Inter",-apple-system,BlinkMacSystemFont,system-ui,sans-serif;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent;-webkit-touch-callout:none}
html{-webkit-text-size-adjust:100%;text-size-adjust:100%}
button,a,input,textarea,select,.btn{-webkit-tap-highlight-color:transparent;outline:0}
button:focus,a:focus{outline:0}
button:focus-visible,a:focus-visible{outline:2px solid var(--primary);outline-offset:2px}
body{font-family:var(--sans);font-size:16px;line-height:1.6;color:var(--dark);background:var(--bg);min-height:100vh;display:flex;overflow-x:hidden;position:relative;-webkit-font-smoothing:antialiased}
body::before{content:"";position:fixed;inset:0;pointer-events:none;z-index:1;opacity:0.3;mix-blend-mode:multiply;background-image:url("data:image/svg+xml;utf8,<svg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3'/><feColorMatrix values='0 0 0 0 0.12 0 0 0 0 0.10 0 0 0 0 0.08 0 0 0 0.4 0'/></filter><rect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/></svg>")}
.bg-mesh{position:fixed;inset:0;z-index:0;overflow:hidden;pointer-events:none}
.blob{position:absolute;border-radius:50%;filter:blur(80px);opacity:0.55}
.blob.b1{width:55vw;height:55vw;background:radial-gradient(circle,#B7C9B3,transparent 70%);top:-20vh;left:-15vw;animation:fA 22s ease-in-out infinite}
.blob.b2{width:50vw;height:50vw;background:radial-gradient(circle,#F5D08A,transparent 70%);bottom:-15vh;right:-15vw;opacity:0.45;animation:fB 28s ease-in-out infinite}
.blob.b3{width:40vw;height:40vw;background:radial-gradient(circle,rgba(13,107,78,0.25),transparent 70%);top:50%;left:50%;animation:fA 32s ease-in-out infinite reverse}
@keyframes fA{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(8vw,5vh) scale(1.1)}}
@keyframes fB{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(-6vw,-4vh) scale(1.15)}}

.layout{display:grid;grid-template-columns:1fr 1.1fr;width:100%;min-height:100vh;position:relative;z-index:2}
.left{padding:60px;display:flex;flex-direction:column;justify-content:space-between;position:relative;overflow:hidden;background:linear-gradient(135deg,#1E1B18 0%,#2A2520 100%);color:#fff}
.left::before{content:"";position:absolute;top:-30%;right:-30%;width:80%;height:160%;background:radial-gradient(ellipse,rgba(232,168,56,0.2),transparent 60%);pointer-events:none}
.left::after{content:"";position:absolute;bottom:-30%;left:-20%;width:80%;height:160%;background:radial-gradient(ellipse,rgba(13,107,78,0.3),transparent 60%);pointer-events:none}
.left>*{position:relative;z-index:2}
.brand{display:inline-flex;align-items:center;gap:12px;font-family:var(--serif);font-weight:700;font-size:1.18rem;color:#fff;text-decoration:none}
.brand-logo{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));display:grid;place-items:center;color:#fff;box-shadow:0 6px 16px rgba(13,107,78,0.5)}
.brand-logo svg{width:22px;height:22px}

.left-content h2{font-family:var(--serif);font-weight:500;font-size:clamp(2rem,3.5vw,2.8rem);line-height:1.1;letter-spacing:-0.02em;margin-bottom:18px}
.left-content em{font-style:italic;color:var(--accent)}
.left-content .lead{color:rgba(255,253,249,0.7);font-size:1rem;line-height:1.65;max-width:42ch}

.feats{display:flex;flex-direction:column;gap:18px;margin-top:50px}
.feat{display:flex;gap:18px;align-items:flex-start}
.feat-ico{width:46px;height:46px;border-radius:14px;background:rgba(232,168,56,0.18);color:var(--accent);display:grid;place-items:center;flex-shrink:0}
.feat-ico svg{width:22px;height:22px}
.feat-text strong{display:block;color:#fff;margin-bottom:2px;font-size:0.98rem}
.feat-text span{font-size:0.88rem;color:rgba(255,253,249,0.6);line-height:1.4}

.left-foot{display:flex;align-items:center;justify-content:space-between;padding-top:30px;border-top:1px solid rgba(255,253,249,0.12);font-size:0.85rem;color:rgba(255,253,249,0.5)}

.right{padding:60px;display:flex;flex-direction:column;justify-content:center;background:transparent;position:relative}
.lang-switch{position:absolute;top:24px;right:24px;display:flex;gap:4px;padding:4px;background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--border);border-radius:var(--pill)}
.lang-switch a{padding:8px 14px;font-size:0.82rem;font-weight:500;color:var(--dark-soft);text-decoration:none;border-radius:var(--pill);transition:var(--t)}
.lang-switch a.active{background:var(--primary);color:#fff}

.frame{width:100%;max-width:480px;margin:0 auto;animation:appear 0.7s cubic-bezier(0.34,1.56,0.64,1)}
@keyframes appear{from{opacity:0;transform:translateY(30px) scale(0.96)}to{opacity:1;transform:translateY(0) scale(1)}}

h1{font-family:var(--serif);font-weight:500;font-size:clamp(1.8rem,3.5vw,2.4rem);line-height:1.05;letter-spacing:-0.02em;margin-bottom:10px}
h1 em{font-style:italic;color:var(--primary)}
.sub{color:var(--muted);margin-bottom:30px;font-size:0.97rem}

.error{background:rgba(255,96,88,0.1);border:1px solid rgba(255,96,88,0.3);color:#C73E36;padding:14px 18px;border-radius:14px;margin-bottom:20px;font-size:0.92rem;display:flex;align-items:center;gap:10px;animation:shake 0.4s ease}
@keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-6px)}75%{transform:translateX(6px)}}
.error svg{width:18px;height:18px;flex-shrink:0}

.field{position:relative;margin-bottom:16px}
.field label{display:block;font-size:0.82rem;font-weight:600;color:var(--dark-soft);margin-bottom:8px;letter-spacing:0.02em}
.field input{width:100%;padding:15px 20px 15px 48px;border-radius:14px;border:1px solid var(--border-strong);background:rgba(255,253,249,0.85);color:var(--dark);font-size:0.95rem;font-family:inherit;transition:border-color var(--t),background var(--t),box-shadow var(--t)}
.field input:focus{outline:none;border-color:var(--primary);background:var(--light);box-shadow:0 0 0 4px rgba(13,107,78,0.12)}
.field-icon{position:absolute;left:18px;top:41px;color:var(--muted);pointer-events:none}
.field-icon svg{width:18px;height:18px}
.toggle-pwd{position:absolute;right:14px;top:37px;width:36px;height:36px;border-radius:10px;border:none;background:none;color:var(--muted);cursor:pointer;display:grid;place-items:center;transition:var(--t)}
.toggle-pwd:hover{background:rgba(13,107,78,0.08);color:var(--primary)}
.toggle-pwd svg{width:18px;height:18px}

.strength{display:flex;gap:4px;margin-top:8px}
.strength span{flex:1;height:4px;border-radius:2px;background:rgba(180,160,130,0.2);transition:var(--t)}
.strength.s1 span:nth-child(1){background:#FF6058}
.strength.s2 span:nth-child(-n+2){background:var(--accent)}
.strength.s3 span:nth-child(-n+3){background:#83C77A}
.strength.s4 span{background:var(--primary)}

.terms{display:flex;align-items:flex-start;gap:10px;font-size:0.85rem;color:var(--dark-soft);margin:20px 0;cursor:pointer;user-select:none;line-height:1.4}
.terms input{display:none}
.terms .check{width:20px;height:20px;border-radius:6px;border:1.5px solid var(--border-strong);background:rgba(255,253,249,0.6);transition:var(--t);display:grid;place-items:center;flex-shrink:0;margin-top:1px}
.terms input:checked + .check{background:var(--primary);border-color:var(--primary)}
.terms .check::after{content:"";width:10px;height:6px;border-left:2px solid #fff;border-bottom:2px solid #fff;transform:rotate(-45deg) scale(0);transition:var(--t);margin-bottom:2px}
.terms input:checked + .check::after{transform:rotate(-45deg) scale(1)}
.terms a{color:var(--primary);text-decoration:none;font-weight:600}

.btn{display:flex;align-items:center;justify-content:center;gap:10px;width:100%;padding:18px 28px;border-radius:var(--pill);border:none;font-family:inherit;font-weight:600;font-size:0.97rem;cursor:pointer;background:linear-gradient(135deg,var(--accent) 0%,#D88F1A 100%);color:#fff;box-shadow:0 18px 40px rgba(232,168,56,0.42),0 0 0 1px rgba(255,255,255,0.18) inset;transition:transform var(--t),box-shadow var(--t)}
.btn:hover{transform:translateY(-2px);box-shadow:0 24px 50px rgba(232,168,56,0.55)}
.btn:active{transform:translateY(0) scale(0.98)}
.btn svg{width:18px;height:18px;transition:transform 0.4s cubic-bezier(0.34,1.56,0.64,1)}
.btn:hover svg{transform:translateX(4px)}

.alt{text-align:center;font-size:0.92rem;color:var(--dark-soft);margin-top:24px}
.alt a{color:var(--primary);font-weight:600;text-decoration:none}

@media (max-width:1024px){
    .layout{grid-template-columns:1fr}
    .left{display:none}
    .right{padding:80px 24px 40px}
}
@media (max-width:480px){
    .right{padding:90px 20px 30px}
    .lang-switch{top:14px;right:14px}
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

<div class="layout">
    <aside class="left">
        <a href="/" class="brand">
            <span class="brand-logo">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2 4 4 8-8 4 4"/></svg>
            </span>
            <?= e(t('site_name')) ?>
        </a>

        <div class="left-content">
            <h2><em><?= e(t('hero_title_2')) ?></em><br><?= e(t('hero_title_1')) ?></h2>
            <p class="lead"><?= e(t('hero_subtitle')) ?></p>

            <div class="feats">
                <div class="feat">
                    <span class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg></span>
                    <div class="feat-text"><strong><?= e(t('feat_2_title')) ?></strong><span>4000+ rasmiy YHQ savollari</span></div>
                </div>
                <div class="feat">
                    <span class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 019-9 9.75 9.75 0 016.74 2.74L21 8"/><path d="M21 3v5h-5"/></svg></span>
                    <div class="feat-text"><strong><?= e(t('feat_3_title')) ?></strong><span>Aqlli takrorlash 4× samaraliroq</span></div>
                </div>
                <div class="feat">
                    <span class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/></svg></span>
                    <div class="feat-text"><strong><?= e(t('feat_4_title')) ?></strong><span>Yaypan eng kuchli o'quvchilari ro'yxatida</span></div>
                </div>
            </div>
        </div>

        <div class="left-foot">
            <span>© <?= date('Y') ?> <?= e(t('site_name')) ?></span>
            <span><?= e(t('site_city')) ?></span>
        </div>
    </aside>

    <section class="right">
        <div class="lang-switch">
            <a href="?lang=uz_latin" class="<?= !$is_cyrl ? 'active' : '' ?>">Lt</a>
            <a href="?lang=uz_cyrillic" class="<?= $is_cyrl ? 'active' : '' ?>">Кр</a>
        </div>

        <div class="frame">
            <h1><em><?= e(t('auth_register_title')) ?></em></h1>
            <p class="sub"><?= e(t('auth_register_subtitle')) ?></p>

            <?php if ($error): ?>
                <div class="error">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" autocomplete="on" novalidate>
                <input type="hidden" name="csrf" value="<?= e(vpy_csrf()) ?>">

                <div class="field">
                    <label for="name"><?= e(t('auth_name')) ?></label>
                    <input id="name" name="name" type="text" required autocomplete="name" placeholder="<?= e(t('auth_name_ph')) ?>" value="<?= e($vals['name']) ?>">
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                </div>

                <div class="field">
                    <label for="phone"><?= e(t('auth_phone')) ?></label>
                    <input id="phone" name="phone" type="tel" inputmode="tel" required autocomplete="tel" placeholder="<?= e(t('auth_phone_ph')) ?>" value="<?= e($vals['phone']) ?>">
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                    </span>
                </div>

                <div class="field">
                    <label for="password"><?= e(t('auth_password')) ?></label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" placeholder="<?= e(t('auth_password_ph')) ?>" minlength="6">
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </span>
                    <button type="button" class="toggle-pwd" id="togglePwd" aria-label="Parolni ko'rsatish">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                    <div class="strength" id="strength"><span></span><span></span><span></span><span></span></div>
                </div>

                <div class="field">
                    <label for="referral"><?= e(t('auth_referral')) ?></label>
                    <input id="referral" name="referral" type="text" autocomplete="off" placeholder="<?= e(t('auth_referral_ph')) ?>" value="<?= e($vals['referral']) ?>" maxlength="12" style="text-transform:uppercase">
                    <span class="field-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7zM12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
                    </span>
                </div>

                <label class="terms">
                    <input type="checkbox" name="terms" value="1" required checked>
                    <span class="check"></span>
                    <span><?= e(t('auth_terms')) ?></span>
                </label>

                <button type="submit" class="btn">
                    <?= e(t('auth_register_btn')) ?>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </button>
            </form>

            <p class="alt">
                <?= e(t('auth_have_account')) ?>
                <a href="/login.php"><?= e(t('auth_login_btn')) ?></a>
            </p>
        </div>
    </section>
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

    var strength = document.getElementById('strength');
    pwd.addEventListener('input', function(){
        var v = pwd.value;
        var s = 0;
        if (v.length >= 6) s = 1;
        if (v.length >= 8 && /\d/.test(v)) s = 2;
        if (v.length >= 8 && /[A-Z]/.test(v) && /\d/.test(v)) s = 3;
        if (v.length >= 10 && /[A-Z]/.test(v) && /\d/.test(v) && /[^A-Za-z0-9]/.test(v)) s = 4;
        strength.className = 'strength s' + s;
    });
})();
</script>
</body>
</html>
