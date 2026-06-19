<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

$lang = vpy_lang_code();
$is_cyrl = $lang === 'uz_cyrillic';
$tariffs = vpy_filter('tariflar', fn($t) => !empty($t['active']));
usort($tariffs, fn($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));
$reviews = vpy_filter('sharhlar', fn($r) => ($r['status'] ?? '') === 'approved' && !empty($r['featured']));
$blog_posts = array_slice(vpy_filter('blog', fn($p) => ($p['status'] ?? '') === 'published'), 0, 3);

$stat_users = (int)vpy_setting('stat_users', '8420');
$stat_tests = (int)vpy_setting('stat_tests', '287000');
$stat_score = (float)vpy_setting('stat_score', '18.4');
$stat_success = (int)vpy_setting('stat_success', '96');

$page_title = t('site_name') . ' — ' . t('site_tagline');
$page_desc = vpy_setting('site_description', t('footer_about'));
?>
<!DOCTYPE html>
<html lang="<?= $is_cyrl ? 'uz-Cyrl' : 'uz' ?>" data-lang="<?= e($lang) ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
<meta name="theme-color" content="#0D6B4E">
<meta name="color-scheme" content="light">
<meta name="description" content="<?= e($page_desc) ?>">
<meta name="keywords" content="<?= e(vpy_setting('site_keywords')) ?>">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($page_title) ?>">
<meta property="og:description" content="<?= e($page_desc) ?>">
<meta property="og:url" content="https://<?= VPY_DOMAIN ?>/">
<meta property="og:locale" content="<?= $is_cyrl ? 'uz_Cyrl_UZ' : 'uz_Latn_UZ' ?>">
<link rel="canonical" href="https://<?= VPY_DOMAIN ?>/">
<link rel="manifest" href="/manifest.json">
<link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">
<title><?= e($page_title) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Manrope:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,500;0,700;0,900;1,500&display=swap" rel="stylesheet">
<style>
:root{
    --bg:#FAF7F2;
    --primary:#0D6B4E;
    --primary-dark:#094D38;
    --primary-glow:rgba(13,107,78,0.18);
    --accent:#E8A838;
    --accent-soft:#F5D08A;
    --secondary:#B7C9B3;
    --dark:#1E1B18;
    --dark-soft:#3B362F;
    --muted:#7A6F62;
    --light:#FFFDF9;
    --glass:rgba(255,252,248,0.68);
    --glass-strong:rgba(255,252,248,0.85);
    --border:rgba(180,160,130,0.25);
    --border-strong:rgba(180,160,130,0.45);
    --shadow-sm:0 2px 8px rgba(30,27,24,0.04);
    --shadow:0 30px 50px rgba(30,27,24,0.08);
    --shadow-lg:0 40px 80px rgba(30,27,24,0.14);
    --shadow-glow:0 30px 80px rgba(13,107,78,0.22);
    --r-sm:14px;
    --r:22px;
    --r-lg:32px;
    --r-xl:48px;
    --pill:100px;
    --t:0.4s cubic-bezier(0.4,0.0,0.2,1);
    --t-bounce:0.6s cubic-bezier(0.34,1.56,0.64,1);
    --serif:"Playfair Display",Georgia,serif;
    --sans:"Manrope","Inter",-apple-system,BlinkMacSystemFont,system-ui,sans-serif;
    --container:min(1320px,92vw);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;-webkit-tap-highlight-color:transparent;-webkit-touch-callout:none}
html{scroll-behavior:smooth;-webkit-text-size-adjust:100%;overflow-x:hidden;text-size-adjust:100%}
body{
    font-family:var(--sans);
    font-weight:400;
    font-size:clamp(15px,1.05vw,17px);
    line-height:1.6;
    color:var(--dark);
    background:var(--bg);
    overflow-x:hidden;
    min-height:100vh;
    position:relative;
    -webkit-font-smoothing:antialiased;
    text-rendering:optimizeLegibility;
}
button,a,input,textarea,select,.btn,.nav-link,.faq-q,.tariff-card,.bento-cell{-webkit-tap-highlight-color:transparent;outline:0}
button:focus,a:focus{outline:0}
button:focus-visible,a:focus-visible{outline:2px solid var(--primary);outline-offset:3px;border-radius:8px}
button,.btn{user-select:none;-webkit-user-select:none}
::selection{background:rgba(13,107,78,0.18);color:var(--dark)}
img,svg{max-width:100%;display:block;height:auto}
a{color:inherit;text-decoration:none;transition:var(--t)}
button{font:inherit;cursor:pointer;border:none;background:none;color:inherit}
ul{list-style:none}
input,textarea,select{font:inherit;color:inherit}

/* GRAIN TEXTURE — element 14 */
body::before{
    content:"";
    position:fixed;
    inset:0;
    pointer-events:none;
    z-index:1;
    opacity:0.35;
    mix-blend-mode:multiply;
    background-image:url("data:image/svg+xml;utf8,<svg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='3' stitchTiles='stitch'/><feColorMatrix values='0 0 0 0 0.12 0 0 0 0 0.10 0 0 0 0 0.08 0 0 0 0.4 0'/></filter><rect width='100%25' height='100%25' filter='url(%23n)' opacity='0.5'/></svg>");
}

/* MESH GRADIENT BACKGROUND — element 3 */
.mesh-bg{
    position:fixed;
    inset:0;
    z-index:0;
    overflow:hidden;
    pointer-events:none;
}
.mesh-blob{
    position:absolute;
    border-radius:50%;
    filter:blur(80px);
    opacity:0.55;
    will-change:transform;
}
.mesh-blob.b1{
    width:55vw;height:55vw;
    background:radial-gradient(circle,#B7C9B3,transparent 70%);
    top:-15vw;left:-10vw;
    animation:floatA 22s ease-in-out infinite;
}
.mesh-blob.b2{
    width:50vw;height:50vw;
    background:radial-gradient(circle,#F5D08A,transparent 70%);
    top:30vh;right:-15vw;
    animation:floatB 26s ease-in-out infinite;
    opacity:0.45;
}
.mesh-blob.b3{
    width:45vw;height:45vw;
    background:radial-gradient(circle,rgba(13,107,78,0.35),transparent 70%);
    bottom:-10vw;left:25vw;
    animation:floatC 30s ease-in-out infinite;
    opacity:0.42;
}
.mesh-blob.b4{
    width:30vw;height:30vw;
    background:radial-gradient(circle,#E8A838,transparent 70%);
    top:60vh;left:-5vw;
    animation:floatA 28s ease-in-out infinite reverse;
    opacity:0.3;
}
@keyframes floatA{
    0%,100%{transform:translate(0,0) scale(1)}
    33%{transform:translate(8vw,4vh) scale(1.1)}
    66%{transform:translate(-4vw,8vh) scale(0.95)}
}
@keyframes floatB{
    0%,100%{transform:translate(0,0) scale(1)}
    50%{transform:translate(-6vw,-5vh) scale(1.15)}
}
@keyframes floatC{
    0%,100%{transform:translate(0,0) scale(1)}
    40%{transform:translate(5vw,-6vh) scale(1.08)}
    80%{transform:translate(-7vw,3vh) scale(0.92)}
}

/* ORGANIC SHAPES — element 15 */
.organic{
    position:absolute;
    pointer-events:none;
    opacity:0.6;
}

main{position:relative;z-index:2}

/* CONTAINER */
.container{width:var(--container);margin:0 auto;position:relative}

/* TYPOGRAPHY — element 7 */
.h-display{
    font-family:var(--serif);
    font-weight:500;
    font-size:clamp(2.5rem,6.5vw,5.8rem);
    line-height:1.02;
    letter-spacing:-0.03em;
    color:var(--dark);
}
.h-display em{
    font-style:italic;
    font-weight:500;
    background:linear-gradient(120deg,var(--primary) 0%,var(--accent) 80%);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
}
.h-section{
    font-family:var(--serif);
    font-weight:500;
    font-size:clamp(2rem,4.2vw,3.8rem);
    line-height:1.05;
    letter-spacing:-0.025em;
    color:var(--dark);
}
.h-section em{font-style:italic;font-weight:500;color:var(--primary)}
.h-card{
    font-family:var(--sans);
    font-weight:700;
    font-size:clamp(1.2rem,1.8vw,1.5rem);
    line-height:1.2;
    letter-spacing:-0.01em;
}
.eyebrow{
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:8px 18px;
    background:var(--glass);
    backdrop-filter:blur(20px);
    -webkit-backdrop-filter:blur(20px);
    border:1px solid var(--border);
    border-radius:var(--pill);
    font-size:0.82rem;
    font-weight:500;
    letter-spacing:0.04em;
    text-transform:uppercase;
    color:var(--primary);
}
.eyebrow::before{
    content:"";
    width:6px;height:6px;border-radius:50%;
    background:var(--accent);
    box-shadow:0 0 0 4px rgba(232,168,56,0.25);
    animation:pulse 2s ease-in-out infinite;
}
@keyframes pulse{
    0%,100%{transform:scale(1);opacity:1}
    50%{transform:scale(1.4);opacity:0.6}
}
.muted{color:var(--muted)}
.lead{
    font-size:clamp(1rem,1.25vw,1.18rem);
    line-height:1.6;
    color:var(--dark-soft);
    max-width:60ch;
}

/* BUTTONS — element 13 */
.btn{
    position:relative;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    padding:18px 32px;
    border-radius:var(--pill);
    font-weight:600;
    font-size:0.97rem;
    letter-spacing:0.01em;
    cursor:pointer;
    transition:transform var(--t),box-shadow var(--t),background var(--t);
    overflow:hidden;
    isolation:isolate;
    white-space:nowrap;
}
.btn::before{
    content:"";
    position:absolute;
    inset:0;
    background:radial-gradient(circle at var(--x,50%) var(--y,50%),rgba(255,255,255,0.5),transparent 50%);
    opacity:0;
    transition:opacity var(--t);
    z-index:-1;
}
.btn:hover::before{opacity:1}
.btn:hover{transform:translateY(-3px)}
.btn:active{transform:translateY(0) scale(0.98)}
.btn-primary{
    background:linear-gradient(135deg,var(--accent) 0%,#D88F1A 100%);
    color:#fff;
    box-shadow:0 18px 40px rgba(232,168,56,0.42),0 0 0 1px rgba(255,255,255,0.18) inset;
}
.btn-primary:hover{
    box-shadow:0 24px 50px rgba(232,168,56,0.55),0 0 0 1px rgba(255,255,255,0.25) inset;
}
.btn-dark{
    background:var(--dark);
    color:var(--light);
    box-shadow:0 18px 40px rgba(30,27,24,0.32);
}
.btn-ghost{
    background:var(--glass);
    backdrop-filter:blur(20px);
    -webkit-backdrop-filter:blur(20px);
    border:1px solid var(--border);
    color:var(--dark);
}
.btn-ghost:hover{background:var(--glass-strong);border-color:var(--border-strong)}
.btn-icon svg{width:18px;height:18px;transition:transform var(--t-bounce)}
.btn:hover .btn-icon svg{transform:translateX(4px) rotate(-2deg)}
.ripple{
    position:absolute;
    border-radius:50%;
    background:rgba(255,255,255,0.5);
    transform:scale(0);
    animation:ripple 0.6s ease-out;
    pointer-events:none;
}
@keyframes ripple{to{transform:scale(4);opacity:0}}

/* GLASS PANEL — element 1 */
.glass{
    background:var(--glass);
    backdrop-filter:blur(30px) saturate(180%);
    -webkit-backdrop-filter:blur(30px) saturate(180%);
    border:1px solid var(--border);
    border-radius:var(--r);
    box-shadow:var(--shadow);
}

/* SCROLL REVEAL — element 9 */
.reveal{
    opacity:0;
    transform:translateY(40px);
    transition:opacity 0.8s cubic-bezier(0.4,0,0.2,1),transform 0.8s cubic-bezier(0.4,0,0.2,1);
}
.reveal.in{opacity:1;transform:translateY(0)}
.reveal-delay-1{transition-delay:0.1s}
.reveal-delay-2{transition-delay:0.2s}
.reveal-delay-3{transition-delay:0.3s}
.reveal-delay-4{transition-delay:0.4s}
.reveal-delay-5{transition-delay:0.5s}


/* NAVBAR — section 1 */
.navbar{
    position:fixed;
    top:18px;
    left:50%;
    transform:translateX(-50%);
    width:min(1200px,calc(100% - 32px));
    z-index:100;
    transition:transform var(--t),box-shadow var(--t),background var(--t);
}
.nav-inner{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:20px;
    padding:14px 14px 14px 26px;
    border-radius:var(--pill);
    background:var(--glass);
    backdrop-filter:blur(30px) saturate(180%);
    -webkit-backdrop-filter:blur(30px) saturate(180%);
    border:1px solid var(--border);
    box-shadow:0 12px 30px rgba(30,27,24,0.06);
}
.navbar.scrolled .nav-inner{
    background:var(--glass-strong);
    box-shadow:0 18px 40px rgba(30,27,24,0.1);
}
.nav-brand{
    display:flex;
    align-items:center;
    gap:12px;
    font-family:var(--serif);
    font-size:1.18rem;
    font-weight:700;
    letter-spacing:-0.01em;
    color:var(--dark);
    text-decoration:none;
    flex-shrink:0;
}
.nav-logo{
    width:38px;height:38px;
    border-radius:12px;
    background:linear-gradient(135deg,var(--primary),var(--primary-dark));
    display:grid;place-items:center;
    color:#fff;
    box-shadow:0 6px 16px var(--primary-glow);
    position:relative;
    overflow:hidden;
}
.nav-logo::after{
    content:"";
    position:absolute;
    top:-50%;left:-50%;
    width:200%;height:200%;
    background:linear-gradient(45deg,transparent 40%,rgba(255,255,255,0.3) 50%,transparent 60%);
    animation:shine 4s ease-in-out infinite;
}
@keyframes shine{
    0%,100%{transform:translate(-30%,-30%) rotate(0deg)}
    50%{transform:translate(30%,30%) rotate(180deg)}
}
.nav-links{
    display:flex;
    align-items:center;
    gap:6px;
}
.nav-link{
    position:relative;
    padding:10px 16px;
    font-size:0.92rem;
    font-weight:500;
    color:var(--dark-soft);
    border-radius:var(--pill);
    transition:color var(--t),background var(--t);
}
.nav-link::after{
    content:"";
    position:absolute;
    left:50%;
    bottom:6px;
    width:0;
    height:2px;
    background:linear-gradient(90deg,var(--accent),var(--primary));
    border-radius:2px;
    transform:translateX(-50%);
    transition:width var(--t-bounce);
}
.nav-link:hover{color:var(--dark)}
.nav-link:hover::after{width:24px}
.nav-cta{
    display:flex;
    align-items:center;
    gap:8px;
    flex-shrink:0;
}
.nav-lang{
    position:relative;
    padding:10px 14px;
    border-radius:var(--pill);
    font-size:0.85rem;
    font-weight:500;
    color:var(--dark-soft);
    background:rgba(255,252,248,0.5);
    border:1px solid var(--border);
    cursor:pointer;
    display:flex;align-items:center;gap:6px;
}
.nav-lang:hover{background:var(--light)}
.nav-lang-menu{
    position:absolute;
    top:calc(100% + 8px);
    right:0;
    background:var(--glass-strong);
    backdrop-filter:blur(30px);
    -webkit-backdrop-filter:blur(30px);
    border:1px solid var(--border);
    border-radius:var(--r-sm);
    padding:8px;
    min-width:140px;
    box-shadow:var(--shadow);
    opacity:0;
    pointer-events:none;
    transform:translateY(-10px);
    transition:all var(--t);
}
.nav-lang.open .nav-lang-menu{opacity:1;pointer-events:auto;transform:translateY(0)}
.nav-lang-menu a{
    display:block;
    padding:10px 14px;
    font-size:0.88rem;
    border-radius:10px;
    color:var(--dark);
}
.nav-lang-menu a:hover{background:rgba(13,107,78,0.08);color:var(--primary)}
.nav-lang-menu a.active{background:var(--primary);color:#fff}
.btn-nav{padding:12px 22px;font-size:0.88rem}
.burger{
    display:none;
    width:42px;height:42px;
    border-radius:50%;
    background:var(--glass);
    border:1px solid var(--border);
    align-items:center;justify-content:center;
    cursor:pointer;
}
.burger span{
    display:block;
    width:18px;height:2px;
    background:var(--dark);
    border-radius:2px;
    position:relative;
    transition:var(--t);
}
.burger span::before,.burger span::after{
    content:"";
    position:absolute;
    left:0;
    width:18px;height:2px;
    background:var(--dark);
    border-radius:2px;
    transition:var(--t);
}
.burger span::before{top:-6px}
.burger span::after{top:6px}
.burger.active span{background:transparent}
.burger.active span::before{top:0;transform:rotate(45deg)}
.burger.active span::after{top:0;transform:rotate(-45deg)}

/* HERO — section 2 */
.hero{
    position:relative;
    padding:160px 0 100px;
    overflow:hidden;
}
.hero-grid{
    display:grid;
    grid-template-columns:1.05fr 1fr;
    gap:60px;
    align-items:center;
}
.hero-content{position:relative;z-index:3}
.hero-title{margin-top:24px}
.hero-title-line{
    display:block;
    overflow:hidden;
}
.hero-title-line span{
    display:inline-block;
    transform:translateY(110%);
    animation:lineUp 0.9s cubic-bezier(0.34,1.56,0.64,1) forwards;
}
.hero-title-line:nth-child(1) span{animation-delay:0.2s}
.hero-title-line:nth-child(2) span{animation-delay:0.4s}
@keyframes lineUp{to{transform:translateY(0)}}
.hero-sub{
    margin-top:28px;
    max-width:52ch;
    opacity:0;
    animation:fadeIn 0.8s ease 0.7s forwards;
}
@keyframes fadeIn{to{opacity:1}}
.hero-cta{
    display:flex;
    flex-wrap:wrap;
    gap:14px;
    margin-top:38px;
    opacity:0;
    animation:fadeIn 0.8s ease 0.9s forwards;
}
.hero-trust{
    display:flex;
    gap:36px;
    margin-top:54px;
    padding-top:32px;
    border-top:1px solid var(--border);
    opacity:0;
    animation:fadeIn 0.8s ease 1.1s forwards;
}
.trust-item{
    display:flex;
    flex-direction:column;
    gap:2px;
}
.trust-num{
    font-family:var(--serif);
    font-size:clamp(1.6rem,2.5vw,2.2rem);
    font-weight:700;
    color:var(--primary);
    line-height:1;
}
.trust-label{
    font-size:0.78rem;
    color:var(--muted);
    text-transform:uppercase;
    letter-spacing:0.06em;
}

/* HERO MOCKUP — element 12 + 5 */
.hero-mockup{
    position:relative;
    perspective:2000px;
}
.mockup-stack{
    position:relative;
    transform:rotateY(-8deg) rotateX(4deg) rotate(-1deg);
    transform-style:preserve-3d;
    transition:transform 0.8s cubic-bezier(0.34,1.56,0.64,1);
    will-change:transform;
}
.mockup-card{
    position:relative;
    background:var(--glass-strong);
    backdrop-filter:blur(30px) saturate(180%);
    -webkit-backdrop-filter:blur(30px) saturate(180%);
    border:1px solid var(--border-strong);
    border-radius:var(--r-lg);
    padding:28px;
    box-shadow:0 50px 100px rgba(30,27,24,0.18),0 0 0 1px rgba(255,255,255,0.4) inset;
    animation:floatUp 7s ease-in-out infinite;
}
@keyframes floatUp{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-12px)}
}
.mockup-head{
    display:flex;align-items:center;justify-content:space-between;
    margin-bottom:20px;
}
.mockup-dots{display:flex;gap:6px}
.mockup-dots span{
    width:10px;height:10px;border-radius:50%;
    background:rgba(180,160,130,0.3);
}
.mockup-dots span:first-child{background:#FF6058}
.mockup-dots span:nth-child(2){background:var(--accent)}
.mockup-dots span:nth-child(3){background:#54C156}
.mockup-tab{
    font-size:0.78rem;
    color:var(--muted);
    padding:6px 12px;
    background:rgba(13,107,78,0.06);
    border-radius:var(--pill);
    font-weight:500;
}
.mockup-meta{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:16px;
}
.mockup-q-num{
    font-family:var(--serif);
    font-size:1.1rem;
    font-weight:600;
    color:var(--primary);
}
.mockup-timer{
    display:flex;align-items:center;gap:8px;
    padding:6px 14px;
    background:linear-gradient(135deg,var(--accent),#D88F1A);
    color:#fff;
    border-radius:var(--pill);
    font-size:0.85rem;
    font-weight:600;
    box-shadow:0 6px 16px rgba(232,168,56,0.3);
}
.timer-dot{
    width:7px;height:7px;border-radius:50%;
    background:#fff;
    animation:pulse 1s ease-in-out infinite;
}
.mockup-progress{
    height:6px;
    border-radius:var(--pill);
    background:rgba(180,160,130,0.18);
    margin-bottom:22px;
    overflow:hidden;
    position:relative;
}
.mockup-progress::after{
    content:"";
    display:block;
    height:100%;
    width:65%;
    border-radius:var(--pill);
    background:linear-gradient(90deg,var(--primary),var(--accent));
    box-shadow:0 0 12px rgba(232,168,56,0.5);
    animation:progressShimmer 2.4s ease-in-out infinite;
}
@keyframes progressShimmer{
    0%,100%{width:65%}
    50%{width:72%}
}
.mockup-question{
    font-family:var(--serif);
    font-size:1.18rem;
    font-weight:500;
    line-height:1.35;
    color:var(--dark);
    margin-bottom:20px;
}
.mockup-answers{display:flex;flex-direction:column;gap:10px}
.mockup-answer{
    display:flex;
    align-items:center;
    gap:14px;
    padding:14px 18px;
    background:rgba(255,252,248,0.7);
    border:1px solid var(--border);
    border-radius:var(--r-sm);
    font-size:0.95rem;
    transition:var(--t);
    cursor:default;
}
.mockup-answer .letter{
    width:32px;height:32px;
    border-radius:10px;
    background:rgba(13,107,78,0.08);
    color:var(--primary);
    display:grid;place-items:center;
    font-weight:600;
    font-size:0.85rem;
    flex-shrink:0;
}
.mockup-answer.correct{
    background:linear-gradient(135deg,rgba(13,107,78,0.12),rgba(232,168,56,0.08));
    border-color:var(--primary);
    color:var(--primary-dark);
    font-weight:600;
    box-shadow:0 8px 24px rgba(13,107,78,0.18);
}
.mockup-answer.correct .letter{
    background:var(--primary);color:#fff;
}
.mockup-answer.correct::after{
    content:"";
    width:18px;height:18px;
    margin-left:auto;
    border-radius:50%;
    background:var(--primary);
    background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='white'><path d='M5.7 11.3 2.4 8 3.8 6.6l1.9 1.9 5.3-5.3L12.4 4.6z'/></svg>");
    background-size:60%;background-position:center;background-repeat:no-repeat;
}
.mockup-foot{
    margin-top:22px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}
.mockup-stat{
    display:flex;gap:18px;
    font-size:0.78rem;
    color:var(--muted);
}
.mockup-stat strong{color:var(--primary);font-weight:700}
.mockup-next{
    padding:10px 18px;
    background:var(--dark);color:#fff;
    border-radius:var(--pill);
    font-size:0.82rem;
    font-weight:600;
    display:flex;align-items:center;gap:6px;
}

/* FLOATING MINI CARDS — element 4 */
.mockup-floater{
    position:absolute;
    background:var(--glass-strong);
    backdrop-filter:blur(20px);
    -webkit-backdrop-filter:blur(20px);
    border:1px solid var(--border-strong);
    border-radius:var(--r-sm);
    padding:14px 18px;
    box-shadow:0 20px 40px rgba(30,27,24,0.12);
    display:flex;align-items:center;gap:12px;
    z-index:5;
    animation:floatBadge 6s ease-in-out infinite;
}
.mockup-floater.f1{
    top:-20px;left:-30px;
    animation-delay:0.5s;
}
.mockup-floater.f2{
    bottom:60px;right:-40px;
    animation-delay:1.2s;
}
.mockup-floater.f3{
    top:50%;right:-30px;
    animation-delay:0.8s;
}
@keyframes floatBadge{
    0%,100%{transform:translateY(0)}
    50%{transform:translateY(-10px)}
}
.floater-icon{
    width:38px;height:38px;
    border-radius:12px;
    display:grid;place-items:center;
    color:#fff;
    flex-shrink:0;
}
.floater-icon.green{background:linear-gradient(135deg,var(--primary),var(--primary-dark));box-shadow:0 8px 18px var(--primary-glow)}
.floater-icon.amber{background:linear-gradient(135deg,var(--accent),#D88F1A);box-shadow:0 8px 18px rgba(232,168,56,0.3)}
.floater-icon.dark{background:var(--dark);box-shadow:0 8px 18px rgba(30,27,24,0.25)}
.floater-icon svg{width:18px;height:18px}
.floater-text{display:flex;flex-direction:column;gap:1px}
.floater-num{font-family:var(--serif);font-size:1.1rem;font-weight:700;color:var(--dark);line-height:1}
.floater-label{font-size:0.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.05em}


/* SECTIONS */
section{padding:110px 0;position:relative}
.section-head{
    text-align:center;
    max-width:780px;
    margin:0 auto 64px;
}
.section-head .lead{margin:18px auto 0;text-align:center}

/* BENTO GRID — element 2 */
.bento{
    display:grid;
    grid-template-columns:repeat(6,1fr);
    grid-auto-rows:minmax(180px,auto);
    gap:22px;
}
.bento-cell{
    position:relative;
    padding:32px;
    border-radius:var(--r-lg);
    overflow:hidden;
    transition:transform var(--t),box-shadow var(--t);
    cursor:default;
    isolation:isolate;
}
.bento-cell.glass{
    background:var(--glass);
    backdrop-filter:blur(30px) saturate(160%);
    -webkit-backdrop-filter:blur(30px) saturate(160%);
    border:1px solid var(--border);
}
.bento-cell:hover{
    transform:translateY(-6px) rotate(-0.3deg);
    box-shadow:var(--shadow-lg);
}
.b-c1{grid-column:span 3;grid-row:span 2}
.b-c2{grid-column:span 3;grid-row:span 1}
.b-c3{grid-column:span 2;grid-row:span 1}
.b-c4{grid-column:span 4;grid-row:span 2}
.b-c5{grid-column:span 2;grid-row:span 1}
.b-c6{grid-column:span 6;grid-row:span 1;min-height:auto}

.bento-cell.dark{
    background:linear-gradient(135deg,#1E1B18 0%,#2A2520 100%);
    color:var(--light);
    border:1px solid rgba(232,168,56,0.18);
}
.bento-cell.dark::before{
    content:"";
    position:absolute;
    top:-50%;right:-30%;
    width:80%;height:160%;
    background:radial-gradient(ellipse,rgba(232,168,56,0.18),transparent 60%);
    z-index:-1;
}
.bento-cell.dark .h-card,.bento-cell.dark p{color:var(--light)}
.bento-cell.amber{
    background:linear-gradient(135deg,#F5D08A 0%,#E8A838 100%);
    color:var(--dark);
}
.bento-cell.green{
    background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);
    color:var(--light);
}
.bento-cell.green .h-card,.bento-cell.green p{color:var(--light)}

.feat-icon{
    width:56px;height:56px;
    border-radius:18px;
    display:grid;place-items:center;
    margin-bottom:22px;
    background:rgba(13,107,78,0.1);
    color:var(--primary);
    transition:transform var(--t-bounce);
}
.bento-cell:hover .feat-icon{transform:rotate(-8deg) scale(1.08)}
.bento-cell.dark .feat-icon{background:rgba(232,168,56,0.18);color:var(--accent)}
.bento-cell.amber .feat-icon{background:rgba(30,27,24,0.12);color:var(--dark)}
.bento-cell.green .feat-icon{background:rgba(255,255,255,0.15);color:var(--light)}
.feat-icon svg{width:28px;height:28px}
.bento-cell h3{margin-bottom:10px}
.bento-cell p{font-size:0.97rem;line-height:1.55;color:var(--dark-soft)}

.bento-deco{
    position:absolute;
    pointer-events:none;
    opacity:0.5;
}
.bento-deco.tr{top:-30px;right:-30px;width:200px;height:200px;border-radius:50%;background:radial-gradient(circle,var(--accent-soft),transparent 70%)}
.bento-deco.bl{bottom:-40px;left:-40px;width:180px;height:180px;border-radius:50%;background:radial-gradient(circle,rgba(13,107,78,0.2),transparent 70%)}

/* DEMO SHOWCASE — section 4 */
.demo-section{
    padding:120px 0;
    position:relative;
}
.demo-frame{
    position:relative;
    margin-top:60px;
    border-radius:var(--r-xl);
    background:linear-gradient(135deg,#FFFDF9 0%,#F0EBE2 100%);
    padding:40px;
    box-shadow:var(--shadow-lg);
    overflow:hidden;
    transform:translateY(0);
    transition:transform 0.8s cubic-bezier(0.4,0,0.2,1);
}
.demo-frame::before{
    content:"";
    position:absolute;
    inset:0;
    background:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='60' height='60'><path d='M0 0h60v1H0zM0 0v60h1V0z' fill='%23B7C9B3' opacity='0.15'/></svg>");
    pointer-events:none;
    opacity:0.6;
}
.demo-grid{
    display:grid;
    grid-template-columns:1.2fr 1fr;
    gap:40px;
    position:relative;
    z-index:2;
}
.demo-screen{
    background:var(--light);
    border-radius:var(--r-lg);
    border:1px solid var(--border);
    padding:32px;
    box-shadow:0 30px 60px rgba(30,27,24,0.1);
    position:relative;
    overflow:hidden;
}
.demo-side{
    display:flex;
    flex-direction:column;
    gap:18px;
    padding:20px 0;
}
.demo-chip{
    display:inline-flex;
    align-items:center;
    gap:10px;
    padding:12px 20px;
    background:var(--glass);
    backdrop-filter:blur(20px);
    border:1px solid var(--border);
    border-radius:var(--pill);
    font-size:0.88rem;
    font-weight:500;
    color:var(--dark);
    align-self:flex-start;
    transition:var(--t);
}
.demo-chip:hover{transform:translateX(6px);background:var(--glass-strong)}
.demo-chip-dot{
    width:8px;height:8px;border-radius:50%;
    background:var(--primary);
    box-shadow:0 0 0 4px rgba(13,107,78,0.18);
}
.demo-chip-dot.amber{background:var(--accent);box-shadow:0 0 0 4px rgba(232,168,56,0.2)}
.demo-chip-dot.dark{background:var(--dark)}
.demo-stat{
    padding:18px 22px;
    border-radius:var(--r-sm);
    background:var(--light);
    border:1px solid var(--border);
    display:flex;
    align-items:center;
    justify-content:space-between;
    transition:var(--t);
}
.demo-stat:hover{transform:translateX(6px);border-color:var(--primary)}
.demo-stat-label{font-size:0.82rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.06em}
.demo-stat-value{font-family:var(--serif);font-size:1.4rem;font-weight:700;color:var(--primary)}

/* STATS — section 5 */
.stats-section{
    padding:100px 0;
    position:relative;
}
.stats-wrap{
    background:linear-gradient(135deg,var(--dark) 0%,#2A2520 100%);
    border-radius:var(--r-xl);
    padding:80px 60px;
    position:relative;
    overflow:hidden;
    color:var(--light);
}
.stats-wrap::before{
    content:"";
    position:absolute;
    top:-50%;right:-20%;
    width:80%;height:200%;
    background:radial-gradient(ellipse,rgba(232,168,56,0.18),transparent 60%);
    pointer-events:none;
}
.stats-wrap::after{
    content:"";
    position:absolute;
    bottom:-50%;left:-20%;
    width:80%;height:200%;
    background:radial-gradient(ellipse,rgba(13,107,78,0.25),transparent 60%);
    pointer-events:none;
}
.stats-head{
    text-align:center;
    margin-bottom:60px;
    position:relative;
    z-index:2;
}
.stats-head .h-section{color:var(--light)}
.stats-head em{color:var(--accent)}
.stats-head .lead{color:rgba(255,253,249,0.75);margin:18px auto 0}
.stats-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:30px;
    position:relative;
    z-index:2;
}
.stat-cell{
    text-align:center;
    padding:30px 20px;
    border-right:1px solid rgba(255,253,249,0.12);
}
.stat-cell:last-child{border-right:none}
.stat-num{
    font-family:var(--serif);
    font-size:clamp(2.5rem,4.5vw,4.2rem);
    font-weight:700;
    line-height:1;
    background:linear-gradient(135deg,var(--accent) 0%,var(--accent-soft) 100%);
    -webkit-background-clip:text;
    background-clip:text;
    color:transparent;
    letter-spacing:-0.03em;
    display:inline-block;
}
.stat-suffix{font-size:0.7em;color:var(--accent-soft);margin-left:2px}
.stat-label{
    margin-top:14px;
    font-size:0.92rem;
    color:rgba(255,253,249,0.7);
    text-transform:uppercase;
    letter-spacing:0.08em;
    font-weight:500;
}
.stat-bar{
    margin:18px auto 0;
    width:50px;height:3px;
    border-radius:3px;
    background:linear-gradient(90deg,var(--accent),var(--primary));
}

/* TARIFFS — section 6 */
.tariffs-section{padding:130px 0;position:relative}
.tariffs-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:24px;
    align-items:stretch;
    margin-top:54px;
}
.tariff-card{
    position:relative;
    padding:42px 36px;
    border-radius:var(--r-lg);
    background:var(--glass);
    backdrop-filter:blur(30px) saturate(160%);
    -webkit-backdrop-filter:blur(30px) saturate(160%);
    border:1px solid var(--border);
    transition:transform var(--t),box-shadow var(--t),border-color var(--t);
    display:flex;flex-direction:column;
    overflow:hidden;
}
.tariff-card:hover{transform:translateY(-10px) scale(1.01);box-shadow:var(--shadow-lg);border-color:var(--border-strong)}
.tariff-card.featured{
    background:linear-gradient(135deg,#FFFDF9 0%,#FAF7F2 100%);
    border:2px solid var(--accent);
    box-shadow:0 30px 60px rgba(232,168,56,0.18);
    transform:scale(1.04);
}
.tariff-card.featured:hover{transform:translateY(-10px) scale(1.06)}
.tariff-badge{
    position:absolute;
    top:-1px;right:32px;
    padding:8px 18px;
    background:linear-gradient(135deg,var(--accent),#D88F1A);
    color:#fff;
    font-size:0.75rem;
    font-weight:700;
    letter-spacing:0.06em;
    text-transform:uppercase;
    border-radius:0 0 12px 12px;
    box-shadow:0 8px 18px rgba(232,168,56,0.35);
}
.tariff-name{
    font-family:var(--serif);
    font-size:1.5rem;
    font-weight:700;
    color:var(--dark);
    margin-bottom:8px;
}
.tariff-desc{
    font-size:0.92rem;
    color:var(--muted);
    margin-bottom:28px;
}
.tariff-price-row{
    display:flex;
    align-items:baseline;
    gap:8px;
    margin-bottom:6px;
}
.tariff-old{
    font-size:1rem;
    color:var(--muted);
    text-decoration:line-through;
    text-decoration-color:rgba(122,111,98,0.5);
    font-weight:500;
}
.tariff-price{
    font-family:var(--serif);
    font-size:2.6rem;
    font-weight:700;
    color:var(--primary);
    line-height:1;
}
.tariff-period{
    font-size:0.92rem;
    color:var(--muted);
    margin-bottom:30px;
}
.tariff-features{
    flex:1;
    display:flex;
    flex-direction:column;
    gap:14px;
    margin-bottom:30px;
    padding-top:24px;
    border-top:1px dashed var(--border-strong);
}
.tariff-features li{
    display:flex;
    align-items:flex-start;
    gap:12px;
    font-size:0.92rem;
    color:var(--dark-soft);
    line-height:1.45;
}
.tariff-features svg{
    width:18px;height:18px;
    color:var(--primary);
    flex-shrink:0;
    margin-top:2px;
}
.tariff-card .btn{width:100%;padding:16px}


/* REVIEWS — section 7 */
.reviews-section{padding:120px 0;position:relative;overflow:hidden}
.reviews-track-wrap{
    margin-top:60px;
    position:relative;
    -webkit-mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent);
    mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent);
}
.reviews-track{
    display:flex;
    gap:24px;
    animation:reviewScroll 50s linear infinite;
    width:max-content;
}
.reviews-track:hover{animation-play-state:paused}
@keyframes reviewScroll{
    0%{transform:translateX(0)}
    100%{transform:translateX(-50%)}
}
.review-card{
    flex:0 0 380px;
    padding:32px;
    border-radius:var(--r-lg);
    background:var(--glass);
    backdrop-filter:blur(30px) saturate(160%);
    -webkit-backdrop-filter:blur(30px) saturate(160%);
    border:1px solid var(--border);
    box-shadow:var(--shadow-sm);
    display:flex;
    flex-direction:column;
    transition:var(--t);
}
.review-card:hover{transform:translateY(-6px);box-shadow:var(--shadow)}
.review-stars{
    display:flex;gap:3px;
    margin-bottom:18px;
    color:var(--accent);
}
.review-stars svg{width:16px;height:16px}
.review-text{
    font-size:0.97rem;
    line-height:1.6;
    color:var(--dark-soft);
    margin-bottom:24px;
    flex:1;
}
.review-author{
    display:flex;
    align-items:center;
    gap:14px;
    padding-top:18px;
    border-top:1px solid var(--border);
}
.review-avatar{
    width:46px;height:46px;
    border-radius:50%;
    display:grid;place-items:center;
    color:#fff;
    font-weight:700;
    font-size:0.95rem;
    flex-shrink:0;
    box-shadow:0 6px 14px rgba(13,107,78,0.2);
}
.review-meta{display:flex;flex-direction:column}
.review-name{font-weight:600;color:var(--dark);font-size:0.95rem}
.review-city{font-size:0.8rem;color:var(--muted)}

/* FAQ — section 8 */
.faq-section{padding:130px 0}
.faq-grid{
    display:grid;
    grid-template-columns:1fr 1.4fr;
    gap:80px;
    align-items:start;
}
.faq-side{position:sticky;top:120px}
.faq-side .h-section{margin-bottom:18px}
.faq-side-deco{
    margin-top:40px;
    padding:28px;
    border-radius:var(--r);
    background:var(--glass);
    backdrop-filter:blur(20px);
    border:1px solid var(--border);
    display:flex;align-items:center;gap:18px;
}
.faq-side-icon{
    width:54px;height:54px;
    border-radius:18px;
    background:linear-gradient(135deg,var(--primary),var(--primary-dark));
    color:#fff;
    display:grid;place-items:center;
    box-shadow:0 10px 24px var(--primary-glow);
    flex-shrink:0;
}
.faq-side-icon svg{width:24px;height:24px}
.faq-side-deco-text strong{display:block;font-size:0.95rem;color:var(--dark)}
.faq-side-deco-text span{font-size:0.85rem;color:var(--muted)}
.faq-list{display:flex;flex-direction:column;gap:14px}
.faq-item{
    background:var(--glass);
    backdrop-filter:blur(20px);
    border:1px solid var(--border);
    border-radius:var(--r);
    overflow:hidden;
    transition:border-color var(--t),background var(--t),box-shadow var(--t);
}
.faq-item:hover{border-color:var(--border-strong)}
.faq-item.open{
    background:var(--glass-strong);
    border-color:var(--primary);
    box-shadow:0 18px 40px rgba(13,107,78,0.1);
}
.faq-q{
    width:100%;
    padding:24px 28px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:24px;
    text-align:left;
    font-family:var(--sans);
    font-size:1.05rem;
    font-weight:600;
    color:var(--dark);
    cursor:pointer;
    transition:var(--t);
}
.faq-toggle{
    width:36px;height:36px;
    border-radius:50%;
    background:rgba(13,107,78,0.08);
    color:var(--primary);
    display:grid;place-items:center;
    flex-shrink:0;
    transition:var(--t-bounce);
}
.faq-item.open .faq-toggle{
    background:var(--primary);
    color:#fff;
    transform:rotate(45deg);
}
.faq-toggle svg{width:16px;height:16px}
.faq-a{
    max-height:0;
    overflow:hidden;
    transition:max-height 0.5s cubic-bezier(0.4,0,0.2,1);
}
.faq-item.open .faq-a{max-height:300px}
.faq-a-inner{
    padding:0 28px 24px;
    font-size:0.97rem;
    line-height:1.65;
    color:var(--dark-soft);
}

/* CTA — section 9 */
.cta-section{padding:120px 0}
.cta-wrap{
    background:linear-gradient(135deg,var(--primary) 0%,var(--primary-dark) 100%);
    border-radius:var(--r-xl);
    padding:80px 60px;
    text-align:center;
    position:relative;
    overflow:hidden;
    color:var(--light);
}
.cta-wrap::before{
    content:"";
    position:absolute;
    top:-30%;left:-20%;
    width:140%;height:160%;
    background:radial-gradient(ellipse at 30% 50%,rgba(232,168,56,0.3),transparent 60%);
    pointer-events:none;
}
.cta-wrap::after{
    content:"";
    position:absolute;
    bottom:-30%;right:-20%;
    width:140%;height:160%;
    background:radial-gradient(ellipse at 70% 50%,rgba(183,201,179,0.25),transparent 60%);
    pointer-events:none;
}
.cta-content{position:relative;z-index:2;max-width:720px;margin:0 auto}
.cta-content .h-section{color:var(--light);margin-bottom:18px}
.cta-content em{color:var(--accent-soft)}
.cta-content .lead{color:rgba(255,253,249,0.8);margin:0 auto 36px}
.cta-content .btn{padding:22px 44px;font-size:1.05rem}
.cta-note{
    margin-top:20px;
    font-size:0.85rem;
    color:rgba(255,253,249,0.6);
    display:flex;align-items:center;justify-content:center;gap:8px;
}
.cta-note svg{width:16px;height:16px}

/* FOOTER — section 10 */
.footer{
    background:linear-gradient(135deg,#1E1B18 0%,#2A2520 100%);
    color:rgba(255,253,249,0.75);
    padding:90px 0 40px;
    position:relative;
    overflow:hidden;
}
.footer::before{
    content:"";
    position:absolute;
    top:-50%;left:-20%;
    width:60%;height:200%;
    background:radial-gradient(ellipse,rgba(13,107,78,0.18),transparent 60%);
    pointer-events:none;
}
.footer-grid{
    display:grid;
    grid-template-columns:1.5fr 1fr 1fr 1fr;
    gap:50px;
    margin-bottom:60px;
    position:relative;
    z-index:2;
}
.footer-brand{
    display:flex;
    align-items:center;
    gap:14px;
    margin-bottom:20px;
    color:var(--light);
    font-family:var(--serif);
    font-size:1.4rem;
    font-weight:700;
}
.footer-about{
    font-size:0.92rem;
    line-height:1.65;
    color:rgba(255,253,249,0.6);
    margin-bottom:24px;
    max-width:38ch;
}
.footer-social{display:flex;gap:10px}
.footer-social a{
    width:42px;height:42px;
    border-radius:50%;
    border:1px solid rgba(255,253,249,0.15);
    display:grid;place-items:center;
    color:rgba(255,253,249,0.7);
    transition:var(--t);
}
.footer-social a:hover{
    background:var(--accent);
    color:var(--dark);
    border-color:var(--accent);
    transform:translateY(-3px) rotate(-6deg);
}
.footer-social svg{width:18px;height:18px}
.footer-col h4{
    color:var(--light);
    font-family:var(--sans);
    font-size:0.85rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:0.1em;
    margin-bottom:22px;
}
.footer-col ul{display:flex;flex-direction:column;gap:12px}
.footer-col a{
    color:rgba(255,253,249,0.7);
    font-size:0.92rem;
    transition:var(--t);
    display:inline-flex;
    align-items:center;
    gap:6px;
}
.footer-col a:hover{color:var(--accent);transform:translateX(4px)}
.footer-contact-line{
    display:flex;
    align-items:flex-start;
    gap:10px;
    font-size:0.92rem;
    color:rgba(255,253,249,0.7);
    line-height:1.5;
    margin-bottom:14px;
}
.footer-contact-line svg{width:16px;height:16px;color:var(--accent);flex-shrink:0;margin-top:3px}
.footer-bottom{
    border-top:1px solid rgba(255,253,249,0.1);
    padding-top:30px;
    display:flex;
    flex-wrap:wrap;
    gap:18px;
    justify-content:space-between;
    align-items:center;
    font-size:0.85rem;
    color:rgba(255,253,249,0.5);
    position:relative;
    z-index:2;
}
.footer-made{
    display:flex;
    align-items:center;
    gap:8px;
}
.footer-made svg{
    width:16px;height:16px;
    color:#FF6058;
    animation:heart 1.5s ease-in-out infinite;
}
@keyframes heart{
    0%,100%{transform:scale(1)}
    50%{transform:scale(1.2)}
}

/* RESPONSIVE — 320 / 375 / 390 / 414 / 768 / 1024 / 1280 / 1440 / 1920 */
@media (max-width:1280px){
    .b-c1{grid-column:span 3}
    .b-c4{grid-column:span 4}
}
@media (max-width:1024px){
    .hero-grid{grid-template-columns:1fr;gap:80px}
    .hero{padding:140px 0 80px}
    .hero-mockup{max-width:560px;margin:0 auto}
    .stats-grid{grid-template-columns:repeat(2,1fr);gap:40px 30px}
    .stat-cell:nth-child(2){border-right:none}
    .stat-cell{border-bottom:1px solid rgba(255,253,249,0.12);padding-bottom:30px}
    .stat-cell:nth-child(3),.stat-cell:nth-child(4){border-bottom:none}
    .tariffs-grid{grid-template-columns:1fr;max-width:520px;margin:54px auto 0}
    .tariff-card.featured{transform:none}
    .tariff-card.featured:hover{transform:translateY(-10px)}
    .faq-grid{grid-template-columns:1fr;gap:40px}
    .faq-side{position:relative;top:auto}
    .footer-grid{grid-template-columns:1fr 1fr;gap:40px}
    .demo-grid{grid-template-columns:1fr}
    .bento{grid-template-columns:repeat(4,1fr)}
    .b-c1{grid-column:span 4;grid-row:span 1}
    .b-c2{grid-column:span 4}
    .b-c3,.b-c5{grid-column:span 2}
    .b-c4{grid-column:span 4;grid-row:span 1}
    .b-c6{grid-column:span 4}
}
@media (max-width:768px){
    section{padding:80px 0}
    .nav-links,.btn-nav{display:none}
    .burger{display:flex}
    .navbar{top:12px;width:calc(100% - 24px)}
    .nav-inner{padding:10px 10px 10px 18px}
    .hero{padding:120px 0 60px}
    .hero-trust{gap:24px;flex-wrap:wrap}
    .stats-wrap{padding:60px 30px}
    .stats-grid{gap:30px 20px}
    .stat-cell{padding:18px 10px;border:none}
    .cta-wrap{padding:60px 30px}
    .cta-content .btn{padding:18px 30px;font-size:0.97rem}
    .footer-grid{grid-template-columns:1fr;gap:36px}
    .demo-frame{padding:24px}
    .demo-screen{padding:24px}
    .review-card{flex-basis:300px}
    .bento{grid-template-columns:1fr;gap:18px}
    .bento-cell{grid-column:span 1!important;padding:28px}
    .mockup-floater.f1{left:-10px;top:-10px}
    .mockup-floater.f2{right:-10px;bottom:30px}
    .mockup-floater.f3{display:none}
    .mockup-stack{transform:none}
    .stats-wrap::before,.stats-wrap::after{filter:blur(40px)}
}
@media (max-width:480px){
    section{padding:64px 0}
    .hero{padding:110px 0 50px}
    .h-display{font-size:clamp(2rem,9vw,3rem)}
    .h-section{font-size:clamp(1.7rem,7vw,2.4rem)}
    .lead{font-size:0.97rem}
    .hero-cta{flex-direction:column;align-items:stretch}
    .hero-cta .btn{justify-content:center}
    .hero-trust{gap:18px}
    .trust-num{font-size:1.4rem}
    .mockup-card{padding:22px}
    .mockup-question{font-size:1.05rem}
    .mockup-answer{padding:12px 14px;font-size:0.88rem}
    .mockup-floater{padding:10px 14px;font-size:0.78rem}
    .floater-icon{width:32px;height:32px}
    .floater-num{font-size:0.95rem}
    .stats-wrap{padding:50px 22px}
    .stats-grid{grid-template-columns:1fr;gap:24px}
    .stat-num{font-size:2.5rem}
    .tariff-card{padding:32px 26px}
    .tariff-price{font-size:2.2rem}
    .cta-wrap{padding:50px 22px;border-radius:var(--r-lg)}
    .demo-frame{padding:18px;border-radius:var(--r-lg)}
    .footer{padding:64px 0 30px}
    .container{width:calc(100% - 32px)}
    .review-card{flex-basis:280px;padding:26px}
}
@media (min-width:1440px){
    .container{width:min(1380px,92vw)}
}
@media (min-width:1920px){
    .container{width:min(1480px,80vw)}
    body{font-size:17px}
}

/* MOBILE MENU */
.mobile-menu{
    position:fixed;
    inset:0;
    z-index:99;
    background:var(--glass-strong);
    backdrop-filter:blur(40px) saturate(180%);
    -webkit-backdrop-filter:blur(40px) saturate(180%);
    padding:90px 24px 30px;
    transform:translateY(-100%);
    transition:transform 0.5s cubic-bezier(0.4,0,0.2,1);
    overflow-y:auto;
    display:flex;
    flex-direction:column;
}
.mobile-menu.open{transform:translateY(0)}
.mobile-menu a{
    display:block;
    padding:16px 20px;
    font-size:1.1rem;
    font-weight:500;
    color:var(--dark);
    border-radius:var(--r-sm);
    margin-bottom:6px;
}
.mobile-menu a:hover{background:rgba(13,107,78,0.06)}
.mobile-menu .btn{margin-top:20px;justify-content:center}

/* COUNTER ANIMATION */
[data-counter]{display:inline-block}
[data-counter].counting{color:var(--accent)}

@media (prefers-reduced-motion:reduce){
    *,*::before,*::after{animation-duration:0.01ms!important;transition-duration:0.01ms!important}
    .reviews-track{animation:none}
}
</style>
</head>
<body>

<div class="mesh-bg" aria-hidden="true">
    <div class="mesh-blob b1"></div>
    <div class="mesh-blob b2"></div>
    <div class="mesh-blob b3"></div>
    <div class="mesh-blob b4"></div>
</div>



<!-- ========== NAVBAR ========== -->
<header class="navbar" id="navbar">
    <div class="nav-inner">
        <a href="/" class="nav-brand">
            <span class="nav-logo">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2 4 4 8-8 4 4"/></svg>
            </span>
            <?= e(t('site_name')) ?>
        </a>
        <nav class="nav-links" aria-label="Asosiy navigatsiya">
            <a href="#platform" class="nav-link"><?= e(t('nav_about')) ?></a>
            <a href="#features" class="nav-link"><?= e(t('nav_features')) ?></a>
            <a href="#tariffs" class="nav-link"><?= e(t('nav_tariffs')) ?></a>
            <a href="/blog.php" class="nav-link"><?= e(t('nav_blog')) ?></a>
            <a href="/aloqa.php" class="nav-link"><?= e(t('nav_contact')) ?></a>
        </nav>
        <div class="nav-cta">
            <div class="nav-lang" id="navLang" role="button" tabindex="0" aria-haspopup="true">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15 15 0 010 20M12 2a15 15 0 000 20"/></svg>
                <?= $is_cyrl ? 'Кр' : 'Lt' ?>
                <div class="nav-lang-menu">
                    <a href="?lang=uz_latin" class="<?= !$is_cyrl ? 'active' : '' ?>"><?= e(t('nav_lang_latin')) ?></a>
                    <a href="?lang=uz_cyrillic" class="<?= $is_cyrl ? 'active' : '' ?>"><?= e(t('nav_lang_cyrillic')) ?></a>
                </div>
            </div>
            <?php if (vpy_is_logged()): ?>
                <a href="<?= vpy_is_admin() ? '/admin/' : '/user/' ?>" class="btn btn-dark btn-nav"><?= e(t('nav_dashboard')) ?></a>
            <?php else: ?>
                <a href="/login.php" class="nav-link"><?= e(t('nav_login')) ?></a>
                <a href="/register.php" class="btn btn-primary btn-nav"><?= e(t('nav_register')) ?></a>
            <?php endif; ?>
            <button class="burger" id="burger" aria-label="Menyu"><span></span></button>
        </div>
    </div>
</header>

<div class="mobile-menu" id="mobileMenu" aria-hidden="true">
    <a href="#platform"><?= e(t('nav_about')) ?></a>
    <a href="#features"><?= e(t('nav_features')) ?></a>
    <a href="#tariffs"><?= e(t('nav_tariffs')) ?></a>
    <a href="/blog.php"><?= e(t('nav_blog')) ?></a>
    <a href="/aloqa.php"><?= e(t('nav_contact')) ?></a>
    <?php if (vpy_is_logged()): ?>
        <a href="<?= vpy_is_admin() ? '/admin/' : '/user/' ?>" class="btn btn-dark"><?= e(t('nav_dashboard')) ?></a>
    <?php else: ?>
        <a href="/login.php" class="btn btn-ghost"><?= e(t('nav_login')) ?></a>
        <a href="/register.php" class="btn btn-primary"><?= e(t('nav_register')) ?></a>
    <?php endif; ?>
</div>

<main>

<!-- ========== HERO ========== -->
<section class="hero" id="hero">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content">
                <span class="eyebrow"><?= e(t('hero_badge')) ?></span>
                <h1 class="h-display hero-title">
                    <span class="hero-title-line"><span><?= e(t('hero_title_1')) ?></span></span>
                    <span class="hero-title-line"><span><em><?= e(t('hero_title_2')) ?></em></span></span>
                </h1>
                <p class="lead hero-sub"><?= e(t('hero_subtitle')) ?></p>
                <div class="hero-cta">
                    <a href="/register.php" class="btn btn-primary">
                        <?= e(t('hero_cta_primary')) ?>
                        <span class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                    </a>
                    <a href="#demo" class="btn btn-ghost">
                        <span class="btn-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></span>
                        <?= e(t('hero_cta_secondary')) ?>
                    </a>
                </div>
                <div class="hero-trust">
                    <div class="trust-item">
                        <span class="trust-num" data-counter="<?= $stat_users ?>">0</span>
                        <span class="trust-label"><?= e(t('hero_trust_users')) ?></span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-num" data-counter="<?= $stat_success ?>" data-suffix="%">0</span>
                        <span class="trust-label"><?= e(t('hero_trust_pass')) ?></span>
                    </div>
                    <div class="trust-item">
                        <span class="trust-num" data-counter="4000" data-suffix="+">0</span>
                        <span class="trust-label"><?= e(t('hero_trust_questions')) ?></span>
                    </div>
                </div>
            </div>

            <div class="hero-mockup">
                <div class="mockup-stack" id="mockupStack">
                    <div class="mockup-card">
                        <div class="mockup-head">
                            <div class="mockup-dots"><span></span><span></span><span></span></div>
                            <div class="mockup-tab"><?= e(t('mockup_title')) ?></div>
                        </div>
                        <div class="mockup-meta">
                            <div class="mockup-q-num"><?= e(t('mockup_question_label')) ?> 13 / 20</div>
                            <div class="mockup-timer">
                                <span class="timer-dot"></span>
                                14:32
                            </div>
                        </div>
                        <div class="mockup-progress"></div>
                        <div class="mockup-question"><?= e(t('mockup_question_text')) ?></div>
                        <div class="mockup-answers">
                            <div class="mockup-answer"><span class="letter">A</span><?= e(t('mockup_answer_a')) ?></div>
                            <div class="mockup-answer"><span class="letter">B</span><?= e(t('mockup_answer_b')) ?></div>
                            <div class="mockup-answer"><span class="letter">C</span><?= e(t('mockup_answer_c')) ?></div>
                            <div class="mockup-answer correct"><span class="letter">D</span><?= e(t('mockup_answer_d')) ?></div>
                        </div>
                        <div class="mockup-foot">
                            <div class="mockup-stat">
                                <span><strong>12</strong> <?= e(t('count_correct')) ?></span>
                                <span><strong>1</strong> <?= e(t('count_wrong')) ?></span>
                            </div>
                            <button class="mockup-next">
                                <?= e(t('mockup_next')) ?>
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="mockup-floater f1">
                        <span class="floater-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                        </span>
                        <div class="floater-text">
                            <span class="floater-num">96%</span>
                            <span class="floater-label"><?= e(t('hero_trust_pass')) ?></span>
                        </div>
                    </div>

                    <div class="mockup-floater f2">
                        <span class="floater-icon amber">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.12"/></svg>
                        </span>
                        <div class="floater-text">
                            <span class="floater-num">19/20</span>
                            <span class="floater-label"><?= e(t('user_score')) ?></span>
                        </div>
                    </div>

                    <div class="mockup-floater f3">
                        <span class="floater-icon dark">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                        </span>
                        <div class="floater-text">
                            <span class="floater-num">25:00</span>
                            <span class="floater-label"><?= e(t('test_timer')) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== PLATFORM FEATURES (BENTO) ========== -->
<section class="platform-section" id="platform">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><?= e(t('nav_features')) ?></span>
            <h2 class="h-section" style="margin-top:18px"><?= e(t('platform_title')) ?></h2>
            <p class="lead"><?= e(t('platform_subtitle')) ?></p>
        </div>

        <div class="bento" id="features">
            <div class="bento-cell glass b-c1 reveal">
                <div class="bento-deco tr"></div>
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                </div>
                <h3 class="h-card"><?= e(t('feat_1_title')) ?></h3>
                <p style="margin-top:12px"><?= e(t('feat_1_desc')) ?></p>
                <div style="display:flex;gap:8px;margin-top:24px;flex-wrap:wrap">
                    <span style="padding:6px 14px;background:rgba(13,107,78,0.08);border-radius:var(--pill);font-size:0.78rem;color:var(--primary);font-weight:600"><?= e(t('topic_signs')) ?></span>
                    <span style="padding:6px 14px;background:rgba(232,168,56,0.12);border-radius:var(--pill);font-size:0.78rem;color:#A87830;font-weight:600"><?= e(t('topic_signals')) ?></span>
                    <span style="padding:6px 14px;background:rgba(30,27,24,0.08);border-radius:var(--pill);font-size:0.78rem;color:var(--dark);font-weight:600"><?= e(t('topic_speed')) ?></span>
                </div>
            </div>

            <div class="bento-cell green b-c2 reveal reveal-delay-1">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                </div>
                <h3 class="h-card"><?= e(t('feat_2_title')) ?></h3>
                <p style="margin-top:12px"><?= e(t('feat_2_desc')) ?></p>
            </div>

            <div class="bento-cell glass b-c3 reveal reveal-delay-2">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 019-9 9.75 9.75 0 016.74 2.74L21 8"/><path d="M21 3v5h-5M21 12a9 9 0 01-9 9 9.75 9.75 0 01-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                </div>
                <h3 class="h-card"><?= e(t('feat_3_title')) ?></h3>
                <p style="margin-top:8px;font-size:0.9rem"><?= e(t('feat_3_desc')) ?></p>
            </div>

            <div class="bento-cell dark b-c4 reveal reveal-delay-2">
                <div class="bento-deco bl"></div>
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>
                </div>
                <h3 class="h-card"><?= e(t('feat_4_title')) ?></h3>
                <p style="margin-top:12px"><?= e(t('feat_4_desc')) ?></p>
                <div style="display:flex;gap:18px;margin-top:30px;align-items:center;flex-wrap:wrap">
                    <div style="display:flex;align-items:center;gap:-8px">
                        <?php for ($i = 0; $i < 4; $i++): $name = ['SK','MK','BR','DT'][$i]; $col = vpy_avatar_color('rev'.$i); ?>
                        <div style="width:38px;height:38px;border-radius:50%;background:<?= $col ?>;color:#fff;display:grid;place-items:center;font-weight:700;font-size:0.78rem;border:2px solid #1E1B18;margin-left:<?= $i ? '-12px' : '0' ?>"><?= $name ?></div>
                        <?php endfor; ?>
                    </div>
                    <span style="color:rgba(255,253,249,0.65);font-size:0.88rem">+8420 <?= e(t('count_users')) ?></span>
                </div>
            </div>

            <div class="bento-cell amber b-c5 reveal reveal-delay-3">
                <div class="feat-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12" y2="18"/></svg>
                </div>
                <h3 class="h-card"><?= e(t('feat_5_title')) ?></h3>
                <p style="margin-top:8px;font-size:0.9rem"><?= e(t('feat_5_desc')) ?></p>
            </div>

            <div class="bento-cell glass b-c6 reveal reveal-delay-4" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:24px">
                <div style="display:flex;align-items:center;gap:24px">
                    <div class="feat-icon" style="margin-bottom:0">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="7"/><path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.12"/></svg>
                    </div>
                    <div>
                        <h3 class="h-card" style="margin:0"><?= e(t('feat_6_title')) ?></h3>
                        <p style="margin-top:4px;font-size:0.92rem"><?= e(t('feat_6_desc')) ?></p>
                    </div>
                </div>
                <a href="/register.php" class="btn btn-dark"><?= e(t('btn_start')) ?></a>
            </div>
        </div>
    </div>
</section>



<!-- ========== DEMO SHOWCASE ========== -->
<section class="demo-section" id="demo">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><?= e(t('demo_chip_realtime')) ?></span>
            <h2 class="h-section" style="margin-top:18px"><?= e(t('demo_title')) ?></h2>
            <p class="lead"><?= e(t('demo_subtitle')) ?></p>
        </div>

        <div class="demo-frame reveal">
            <div class="demo-grid">
                <div class="demo-screen">
                    <div class="mockup-head">
                        <div class="mockup-dots"><span></span><span></span><span></span></div>
                        <div class="mockup-tab"><?= e(t('ticket_label')) ?> #08 — 14/20</div>
                    </div>
                    <div class="mockup-meta" style="margin-top:24px">
                        <div class="mockup-q-num"><?= e(t('mockup_question_label')) ?> 14</div>
                        <div class="mockup-timer">
                            <span class="timer-dot"></span>
                            18:42
                        </div>
                    </div>
                    <div class="mockup-progress" style="--p:70%"></div>
                    <div class="mockup-question">Yo'lda regulyator qo'lini yon tomonlarga uzatgan paytda harakatga ruxsat etiladimi?</div>
                    <div class="mockup-answers">
                        <div class="mockup-answer"><span class="letter">A</span>Faqat to'g'ri yo'nalishda</div>
                        <div class="mockup-answer correct"><span class="letter">B</span>Yon tomonlardan to'g'ri va o'ngga</div>
                        <div class="mockup-answer"><span class="letter">C</span>Hech qanday harakat ruxsat etilmaydi</div>
                        <div class="mockup-answer"><span class="letter">D</span>Faqat orqadan</div>
                    </div>
                    <div style="margin-top:18px;padding:14px 18px;background:rgba(13,107,78,0.06);border-left:3px solid var(--primary);border-radius:10px;font-size:0.88rem;color:var(--dark-soft);line-height:1.5">
                        <strong style="color:var(--primary)"><?= e(t('test_explain')) ?>:</strong> Regulyator qo'llari yon tomonlarga uzatilganda yon tomondagi haydovchilar to'g'ri va o'ng tomonga harakat qilishlari mumkin.
                    </div>
                </div>

                <div class="demo-side">
                    <div class="demo-chip"><span class="demo-chip-dot"></span><?= e(t('demo_chip_realtime')) ?></div>
                    <div class="demo-chip"><span class="demo-chip-dot amber"></span><?= e(t('demo_chip_official')) ?></div>
                    <div class="demo-chip"><span class="demo-chip-dot dark"></span><?= e(t('demo_chip_smart')) ?></div>

                    <div class="demo-stat">
                        <div>
                            <div class="demo-stat-label"><?= e(t('demo_label_timer')) ?></div>
                            <div class="demo-stat-value">25:00</div>
                        </div>
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#0D6B4E" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </div>
                    <div class="demo-stat">
                        <div>
                            <div class="demo-stat-label"><?= e(t('demo_label_progress')) ?></div>
                            <div class="demo-stat-value">14 / 20</div>
                        </div>
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#E8A838" stroke-width="1.5"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                    </div>
                    <div class="demo-stat">
                        <div>
                            <div class="demo-stat-label"><?= e(t('demo_label_score')) ?></div>
                            <div class="demo-stat-value">13 / 14</div>
                        </div>
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#1E1B18" stroke-width="1.5"><circle cx="12" cy="8" r="7"/><path d="M8.21 13.89L7 23l5-3 5 3-1.21-9.12"/></svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== STATISTIKA ========== -->
<section class="stats-section">
    <div class="container">
        <div class="stats-wrap reveal">
            <div class="stats-head">
                <span class="eyebrow" style="background:rgba(255,253,249,0.1);border-color:rgba(255,253,249,0.2);color:var(--accent)"><?= e(t('stats_title')) ?></span>
                <h2 class="h-section" style="margin-top:18px"><?= e(t('stats_title')) ?></h2>
                <p class="lead"><?= e(t('stats_subtitle')) ?></p>
            </div>
            <div class="stats-grid">
                <div class="stat-cell reveal reveal-delay-1">
                    <div><span class="stat-num" data-counter="<?= $stat_users ?>">0</span><span class="stat-suffix">+</span></div>
                    <div class="stat-bar"></div>
                    <div class="stat-label"><?= e(t('stats_users')) ?></div>
                </div>
                <div class="stat-cell reveal reveal-delay-2">
                    <div><span class="stat-num" data-counter="<?= $stat_tests ?>">0</span></div>
                    <div class="stat-bar"></div>
                    <div class="stat-label"><?= e(t('stats_tests')) ?></div>
                </div>
                <div class="stat-cell reveal reveal-delay-3">
                    <div><span class="stat-num" data-counter="<?= (int)round($stat_score) ?>" data-decimal="<?= $stat_score ?>">0</span></div>
                    <div class="stat-bar"></div>
                    <div class="stat-label"><?= e(t('stats_score')) ?> / 20</div>
                </div>
                <div class="stat-cell reveal reveal-delay-4">
                    <div><span class="stat-num" data-counter="<?= $stat_success ?>">0</span><span class="stat-suffix">%</span></div>
                    <div class="stat-bar"></div>
                    <div class="stat-label"><?= e(t('stats_success')) ?></div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== TARIFLAR ========== -->
<section class="tariffs-section" id="tariffs">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><?= e(t('nav_tariffs')) ?></span>
            <h2 class="h-section" style="margin-top:18px"><?= e(t('tariffs_title')) ?></h2>
            <p class="lead"><?= e(t('tariffs_subtitle')) ?></p>
        </div>

        <div class="tariffs-grid">
            <?php foreach ($tariffs as $i => $tf):
                $features = $is_cyrl ? ($tf['features_cyrl'] ?? $tf['features']) : $tf['features'];
                $name = $is_cyrl ? ($tf['name_cyrl'] ?? $tf['name']) : $tf['name'];
                $desc = $is_cyrl ? ($tf['description_cyrl'] ?? $tf['description']) : $tf['description'];
                $period = $is_cyrl ? ($tf['period_label_cyrl'] ?? $tf['period_label']) : $tf['period_label'];
                $featured = !empty($tf['highlight']) || !empty($tf['popular']);
            ?>
            <div class="tariff-card reveal reveal-delay-<?= $i + 1 ?> <?= $featured ? 'featured' : '' ?>">
                <?php if (!empty($tf['popular'])): ?>
                    <div class="tariff-badge"><?= e(t('tariffs_badge_popular')) ?></div>
                <?php endif; ?>
                <h3 class="tariff-name"><?= e($name) ?></h3>
                <p class="tariff-desc"><?= e($desc) ?></p>
                <?php if (!empty($tf['old_price'])): ?>
                    <div class="tariff-price-row"><span class="tariff-old"><?= number_format((float)$tf['old_price'], 0, '.', ' ') ?></span></div>
                <?php endif; ?>
                <div class="tariff-price-row">
                    <span class="tariff-price"><?= number_format((float)$tf['price'], 0, '.', ' ') ?></span>
                    <span class="muted" style="font-size:1rem;font-weight:500"><?= e(t('valyuta_sum')) ?></span>
                </div>
                <div class="tariff-period"><?= e($period) ?></div>
                <ul class="tariff-features">
                    <?php foreach ((array)$features as $f): ?>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><?= e($f) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <a href="/register.php?tarif=<?= (int)$tf['id'] ?>" class="btn <?= $featured ? 'btn-primary' : 'btn-dark' ?>">
                    <?= e(t('tariffs_buy')) ?>
                    <span class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========== SHARHLAR ========== -->
<section class="reviews-section">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><?= e(t('reviews_title')) ?></span>
            <h2 class="h-section" style="margin-top:18px"><?= e(t('reviews_title')) ?></h2>
            <p class="lead"><?= e(t('reviews_subtitle')) ?></p>
        </div>
    </div>

    <div class="reviews-track-wrap reveal">
        <div class="reviews-track">
            <?php
            $review_pool = $reviews;
            if (count($review_pool) < 4) $review_pool = array_merge(vpy_filter('sharhlar', fn($r) => ($r['status'] ?? '') === 'approved'), $review_pool);
            $loop = array_merge($review_pool, $review_pool);
            foreach ($loop as $r):
                $text = $is_cyrl && !empty($r['text_cyrl']) ? $r['text_cyrl'] : $r['text'];
                $color = vpy_avatar_color($r['name']);
            ?>
            <article class="review-card">
                <div class="review-stars">
                    <?php for ($s = 0; $s < (int)$r['rating']; $s++): ?>
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <?php endfor; ?>
                </div>
                <p class="review-text">"<?= e($text) ?>"</p>
                <div class="review-author">
                    <div class="review-avatar" style="background:<?= e($color) ?>"><?= e(vpy_user_initials($r['name'])) ?></div>
                    <div class="review-meta">
                        <span class="review-name"><?= e($r['name']) ?></span>
                        <span class="review-city"><?= e($r['city'] ?? t('site_city')) ?></span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========== FAQ ========== -->
<section class="faq-section" id="faq">
    <div class="container">
        <div class="faq-grid">
            <div class="faq-side reveal">
                <span class="eyebrow"><?= e(t('faq_title')) ?></span>
                <h2 class="h-section" style="margin-top:18px"><?= e(t('faq_title')) ?></h2>
                <p class="lead" style="margin-top:18px"><?= e(t('faq_subtitle')) ?></p>
                <div class="faq-side-deco">
                    <span class="faq-side-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    </span>
                    <div class="faq-side-deco-text">
                        <strong><?= e(t('contact_title')) ?></strong>
                        <span><?= e(vpy_setting('contact_phone', t('footer_phone_value'))) ?></span>
                    </div>
                </div>
            </div>

            <div class="faq-list">
                <?php for ($i = 1; $i <= 6; $i++): ?>
                <div class="faq-item reveal reveal-delay-<?= min($i, 5) ?>">
                    <button class="faq-q">
                        <span><?= e(t('faq_' . $i . '_q')) ?></span>
                        <span class="faq-toggle">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        </span>
                    </button>
                    <div class="faq-a"><div class="faq-a-inner"><?= e(t('faq_' . $i . '_a')) ?></div></div>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</section>

<!-- ========== CTA ========== -->
<section class="cta-section">
    <div class="container">
        <div class="cta-wrap reveal">
            <div class="cta-content">
                <span class="eyebrow" style="background:rgba(255,253,249,0.12);border-color:rgba(255,253,249,0.2);color:var(--accent-soft)">
                    <?= e(t('hero_badge')) ?>
                </span>
                <h2 class="h-section" style="margin-top:24px"><em><?= e(t('cta_title')) ?></em></h2>
                <p class="lead"><?= e(t('cta_subtitle')) ?></p>
                <a href="/register.php" class="btn btn-primary" style="margin-top:6px">
                    <?= e(t('cta_button')) ?>
                    <span class="btn-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                </a>
                <div class="cta-note">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                    <?= e(t('cta_note')) ?>
                </div>
            </div>
        </div>
    </div>
</section>

</main>

<!-- ========== FOOTER ========== -->
<footer class="footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <a href="/" class="footer-brand">
                    <span class="nav-logo">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12l2-2 4 4 8-8 4 4"/></svg>
                    </span>
                    <?= e(t('site_name')) ?>
                </a>
                <p class="footer-about"><?= e(t('footer_about')) ?></p>
                <div class="footer-social">
                    <a href="https://t.me/<?= e(ltrim(vpy_setting('contact_telegram', 'vatanparvaryaypan'), '@')) ?>" aria-label="Telegram" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 000 12a12 12 0 0012 12 12 12 0 0012-12A12 12 0 0012 0a12 12 0 00-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 01.171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    </a>
                    <a href="https://instagram.com/<?= e(vpy_setting('contact_instagram', 'vatanparvaryaypan')) ?>" aria-label="Instagram" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="https://youtube.com/@<?= e(vpy_setting('contact_youtube', 'vatanparvaryaypan')) ?>" aria-label="YouTube" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 00-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 00-1.94 2A29 29 0 001 11.75a29 29 0 00.46 5.33A2.78 2.78 0 003.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 001.94-2 29 29 0 00.46-5.25 29 29 0 00-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
                    </a>
                    <a href="https://facebook.com/<?= e(vpy_setting('contact_facebook', 'vatanparvaryaypan')) ?>" aria-label="Facebook" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                </div>
            </div>

            <div class="footer-col">
                <h4><?= e(t('footer_links')) ?></h4>
                <ul>
                    <li><a href="/">‒ <?= e(t('nav_home')) ?></a></li>
                    <li><a href="#platform">‒ <?= e(t('nav_about')) ?></a></li>
                    <li><a href="#tariffs">‒ <?= e(t('nav_tariffs')) ?></a></li>
                    <li><a href="/blog.php">‒ <?= e(t('nav_blog')) ?></a></li>
                    <li><a href="/aloqa.php">‒ <?= e(t('nav_contact')) ?></a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4><?= e(t('footer_contact')) ?></h4>
                <div class="footer-contact-line">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span><?= e(vpy_setting('contact_address', t('footer_address_value'))) ?></span>
                </div>
                <div class="footer-contact-line">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                    <a href="tel:<?= e(preg_replace('/\D/', '', vpy_setting('contact_phone', '+998901234567'))) ?>"><?= e(vpy_setting('contact_phone', t('footer_phone_value'))) ?></a>
                </div>
                <div class="footer-contact-line">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <a href="mailto:<?= e(vpy_setting('contact_email', t('footer_email_value'))) ?>"><?= e(vpy_setting('contact_email', t('footer_email_value'))) ?></a>
                </div>
            </div>

            <div class="footer-col">
                <h4><?= e(t('footer_hours')) ?></h4>
                <div class="footer-contact-line">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span><?= e(t('footer_hours_value')) ?></span>
                </div>
                <h4 style="margin-top:30px"><?= e(t('site_city')) ?></h4>
                <div class="footer-contact-line">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    <span><?= e(t('footer_address_value')) ?></span>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <span>© <?= date('Y') ?> <?= e(t('site_name')) ?>. <?= e(t('footer_rights')) ?>.</span>
            <span class="footer-made">
                <?= e(t('footer_made')) ?>
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                <?= e(t('site_city')) ?>
            </span>
        </div>
    </div>
</footer>

<script>
(function(){
    'use strict';

    /* NAVBAR scroll effect */
    var nav = document.getElementById('navbar');
    function navScroll(){
        if(window.scrollY > 30) nav.classList.add('scrolled');
        else nav.classList.remove('scrolled');
    }
    navScroll();
    window.addEventListener('scroll', navScroll, {passive:true});

    /* MOBILE BURGER */
    var burger = document.getElementById('burger');
    var mobileMenu = document.getElementById('mobileMenu');
    if (burger) {
        burger.addEventListener('click', function(){
            burger.classList.toggle('active');
            mobileMenu.classList.toggle('open');
            document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
        });
        mobileMenu.querySelectorAll('a').forEach(function(a){
            a.addEventListener('click', function(){
                burger.classList.remove('active');
                mobileMenu.classList.remove('open');
                document.body.style.overflow = '';
            });
        });
    }

    /* LANG dropdown */
    var navLang = document.getElementById('navLang');
    if (navLang) {
        navLang.addEventListener('click', function(e){
            e.stopPropagation();
            navLang.classList.toggle('open');
        });
        document.addEventListener('click', function(){ navLang.classList.remove('open'); });
    }

    /* RIPPLE — element 13 */
    document.querySelectorAll('.btn').forEach(function(btn){
        btn.addEventListener('click', function(e){
            var rect = btn.getBoundingClientRect();
            var ripple = document.createElement('span');
            var size = Math.max(rect.width, rect.height);
            ripple.className = 'ripple';
            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = (e.clientX - rect.left - size/2) + 'px';
            ripple.style.top = (e.clientY - rect.top - size/2) + 'px';
            btn.appendChild(ripple);
            setTimeout(function(){ ripple.remove(); }, 650);
        });
        btn.addEventListener('mousemove', function(e){
            var rect = btn.getBoundingClientRect();
            btn.style.setProperty('--x', ((e.clientX - rect.left) / rect.width * 100) + '%');
            btn.style.setProperty('--y', ((e.clientY - rect.top) / rect.height * 100) + '%');
        });
    });

    /* SCROLL REVEAL — element 9 */
    if ('IntersectionObserver' in window) {
        var io = new IntersectionObserver(function(entries){
            entries.forEach(function(en){
                if (en.isIntersecting){
                    en.target.classList.add('in');
                    io.unobserve(en.target);
                }
            });
        }, {threshold:0.12, rootMargin:'0px 0px -60px 0px'});
        document.querySelectorAll('.reveal').forEach(function(el){ io.observe(el); });
    } else {
        document.querySelectorAll('.reveal').forEach(function(el){ el.classList.add('in'); });
    }

    /* COUNTERS — element 11 */
    function easeOutCubic(t){ return 1 - Math.pow(1 - t, 3); }
    function animateCounter(el){
        var target = parseFloat(el.getAttribute('data-counter'));
        var decimal = parseFloat(el.getAttribute('data-decimal'));
        var suffix = el.getAttribute('data-suffix') || '';
        var hasDecimal = !isNaN(decimal);
        var dur = 1800, start = performance.now();
        function tick(now){
            var p = Math.min(1, (now - start) / dur);
            var eased = easeOutCubic(p);
            var val = hasDecimal ? (decimal * eased) : Math.round(target * eased);
            if (hasDecimal) {
                el.textContent = val.toFixed(1).replace('.', ',');
            } else if (target >= 1000) {
                el.textContent = val.toLocaleString('uz-UZ').replace(/,/g, ' ');
            } else {
                el.textContent = val;
            }
            if (p < 1) requestAnimationFrame(tick);
            else if (suffix) {
                /* suffix is rendered separately */
            }
        }
        requestAnimationFrame(tick);
    }
    if ('IntersectionObserver' in window) {
        var co = new IntersectionObserver(function(entries){
            entries.forEach(function(en){
                if (en.isIntersecting){
                    animateCounter(en.target);
                    co.unobserve(en.target);
                }
            });
        }, {threshold:0.4});
        document.querySelectorAll('[data-counter]').forEach(function(el){ co.observe(el); });
    } else {
        document.querySelectorAll('[data-counter]').forEach(function(el){ animateCounter(el); });
    }

    /* PARALLAX HERO MOCKUP — element 6 */
    var stack = document.getElementById('mockupStack');
    if (stack && window.matchMedia('(min-width:1024px)').matches){
        var rafId = null;
        function onMove(e){
            if (rafId) return;
            rafId = requestAnimationFrame(function(){
                var rx = (e.clientY / window.innerHeight - 0.5) * 8;
                var ry = (e.clientX / window.innerWidth - 0.5) * -10;
                stack.style.transform = 'rotateY(' + (ry - 8) + 'deg) rotateX(' + (rx + 4) + 'deg) rotate(-1deg)';
                rafId = null;
            });
        }
        window.addEventListener('mousemove', onMove, {passive:true});
        window.addEventListener('scroll', function(){
            var y = window.scrollY;
            if (y < 800) stack.style.translate = '0 ' + (y * 0.08) + 'px';
        }, {passive:true});
    }

    /* FAQ ACCORDION — section 8 */
    document.querySelectorAll('.faq-q').forEach(function(q){
        q.addEventListener('click', function(){
            var item = q.parentElement;
            var isOpen = item.classList.contains('open');
            document.querySelectorAll('.faq-item.open').forEach(function(o){ o.classList.remove('open'); });
            if (!isOpen) item.classList.add('open');
        });
    });

    /* SMOOTH SCROLL */
    document.querySelectorAll('a[href^="#"]').forEach(function(a){
        a.addEventListener('click', function(e){
            var id = a.getAttribute('href');
            if (id === '#' || id.length < 2) return;
            var t = document.querySelector(id);
            if (t){
                e.preventDefault();
                var y = t.getBoundingClientRect().top + window.scrollY - 90;
                window.scrollTo({top:y, behavior:'smooth'});
            }
        });
    });

    /* SERVICE WORKER */
    if ('serviceWorker' in navigator && location.protocol === 'https:'){
        window.addEventListener('load', function(){
            navigator.serviceWorker.register('/sw.js').catch(function(){});
        });
    }
})();
</script>
</body>
</html>
