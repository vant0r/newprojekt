<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/public_layout.php';

$lang = vpy_lang_code();
$is_cyrl = $lang === 'uz_cyrillic';
$tariffs = vpy_filter('tariflar', fn($t) => !empty($t['active']));
usort($tariffs, fn($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));
$reviews = vpy_filter('sharhlar', fn($r) => ($r['status'] ?? '') === 'approved' && !empty($r['featured']));

$stat_users = (int)vpy_setting('stat_users', '8420');
$stat_tests = (int)vpy_setting('stat_tests', '287000');
$stat_score = (float)vpy_setting('stat_score', '18.4');
$stat_success = (int)vpy_setting('stat_success', '96');

$hero_bg = vpy_setting('hero_bg_image', '');
$banners = array_filter([vpy_setting('banner_image_1',''),vpy_setting('banner_image_2',''),vpy_setting('banner_image_3','')]);
$ticker_texts = array_filter([vpy_setting('ticker_text_1',''),vpy_setting('ticker_text_2',''),vpy_setting('ticker_text_3',''),vpy_setting('ticker_text_4','')]);
$founder_active = vpy_setting('founder_active', '0') === '1';

$page_css = <<<CSS
.hero{position:relative;padding:140px 0 80px;overflow:hidden}
.hero-bg{position:absolute;inset:0;z-index:0}
.hero-bg img{width:100%;height:100%;object-fit:cover}
.hero-bg::after{content:"";position:absolute;inset:0;background:linear-gradient(135deg,rgba(244,246,249,0.92),rgba(244,246,249,0.8))}
[data-theme="dark"] .hero-bg::after{background:linear-gradient(135deg,rgba(15,17,23,0.92),rgba(15,17,23,0.8))}
.hero-grid{display:grid;grid-template-columns:1fr 1fr;gap:50px;align-items:center;position:relative;z-index:2}
.hero-content h1{margin-top:18px}
.hero-sub{margin-top:20px;max-width:50ch}
.hero-cta{display:flex;flex-wrap:wrap;gap:12px;margin-top:30px}
.hero-trust{display:flex;gap:32px;margin-top:42px;padding-top:24px;border-top:1px solid var(--border)}
.trust-item{display:flex;flex-direction:column;gap:2px}
.trust-num{font-family:var(--serif);font-size:clamp(1.4rem,2.2vw,1.9rem);font-weight:700;color:var(--primary);line-height:1}
.trust-label{font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.06em}
.hero-visual{position:relative;display:flex;align-items:center;justify-content:center}
.hero-card{background:var(--glass-strong);backdrop-filter:blur(28px);border:1px solid var(--border);border-radius:var(--r-lg);padding:28px;box-shadow:var(--shadow-lg);max-width:400px;width:100%}
.hero-card-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.hero-card-badge{font-size:0.72rem;font-weight:700;background:var(--primary);color:#fff;padding:4px 10px;border-radius:var(--pill)}
.hero-q{font-family:var(--serif);font-size:1.05rem;font-weight:600;margin-bottom:14px;line-height:1.4}
.hero-answers{display:flex;flex-direction:column;gap:8px}
.hero-answer{display:flex;align-items:center;gap:10px;padding:11px 14px;background:var(--surface);border:1px solid var(--border);border-radius:var(--r-sm);font-size:0.88rem;transition:var(--t)}
.hero-answer.correct{background:rgba(26,95,180,0.08);border-color:var(--primary);color:var(--primary);font-weight:600}
.hero-answer .letter{width:28px;height:28px;border-radius:8px;background:var(--blue-soft);color:var(--primary);display:grid;place-items:center;font-weight:700;font-size:0.78rem;flex-shrink:0}
.hero-answer.correct .letter{background:var(--primary);color:#fff}
/* BANNER */
.banner-section{display:none;padding:0;margin-top:-30px;position:relative;z-index:2}
@media (min-width:769px){.banner-section{display:block}}
.banner-slider{position:relative;border-radius:var(--r-lg);overflow:hidden;height:200px;box-shadow:var(--shadow)}
.banner-slide{position:absolute;inset:0;opacity:0;transition:opacity 1s ease}
.banner-slide.active{opacity:1}
.banner-slide img{width:100%;height:100%;object-fit:cover}
/* FEATURES */
.feat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-top:48px}
.feat-card{padding:28px 24px;background:var(--glass-strong);backdrop-filter:blur(20px);border:1px solid var(--border);border-radius:var(--r-lg);transition:transform var(--t),box-shadow var(--t)}
.feat-card:hover{transform:translateY(-4px);box-shadow:var(--shadow)}
.feat-ico{width:48px;height:48px;border-radius:14px;background:var(--blue-soft);color:var(--primary);display:grid;place-items:center;margin-bottom:16px}
.feat-ico svg{width:22px;height:22px}
.feat-card h3{font-size:1rem;font-weight:700;margin-bottom:6px}
.feat-card p{font-size:0.85rem;color:var(--muted);line-height:1.6}
/* STATS */
.stats-wrap{background:linear-gradient(135deg,var(--dark) 0%,#1E2535 100%);border-radius:var(--r-xl);padding:60px 50px;color:#fff;position:relative;overflow:hidden}
[data-theme="dark"] .stats-wrap{background:linear-gradient(135deg,#080B12 0%,#0E1320 100%)}
.stats-wrap::before{content:"";position:absolute;top:-40%;right:-15%;width:50%;height:180%;background:radial-gradient(ellipse,rgba(26,95,180,0.2),transparent 60%);pointer-events:none}
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;position:relative;z-index:2}
.stat-cell{text-align:center}
.stat-num{font-family:var(--serif);font-size:clamp(2rem,3.5vw,3.2rem);font-weight:700;line-height:1;color:var(--primary-light)}
.stat-label{margin-top:8px;font-size:0.82rem;color:rgba(240,244,255,0.6);text-transform:uppercase;letter-spacing:0.06em}
/* TARIFFS */
.tariffs-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:22px;margin-top:48px}
.tariff-card{padding:34px 28px;border-radius:var(--r-lg);background:var(--glass-strong);backdrop-filter:blur(20px);border:1px solid var(--border);display:flex;flex-direction:column;transition:transform var(--t),box-shadow var(--t)}
.tariff-card:hover{transform:translateY(-5px);box-shadow:var(--shadow-lg)}
.tariff-card.featured{border:2px solid var(--primary);box-shadow:0 12px 40px var(--primary-glow)}
.tariff-badge{display:inline-block;padding:4px 12px;background:var(--primary);color:#fff;font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;border-radius:var(--pill);margin-bottom:12px;align-self:flex-start}
.tariff-name{font-family:var(--serif);font-size:1.4rem;font-weight:700;margin-bottom:4px}
.tariff-desc{font-size:0.85rem;color:var(--muted);margin-bottom:20px}
.tariff-price{font-family:var(--serif);font-size:2.2rem;font-weight:700;color:var(--primary);line-height:1}
.tariff-period{font-size:0.82rem;color:var(--muted);margin-bottom:22px;margin-top:4px}
.tariff-features{flex:1;display:flex;flex-direction:column;gap:10px;margin-bottom:22px;padding-top:18px;border-top:1px dashed var(--border-strong)}
.tariff-features li{display:flex;align-items:flex-start;gap:8px;font-size:0.85rem;color:var(--dark-soft);line-height:1.4}
.tariff-features svg{width:15px;height:15px;color:var(--primary);flex-shrink:0;margin-top:2px}
.tariff-card .btn{width:100%}
/* FOUNDER */
.founder-section{padding:80px 0}
.founder-card{display:grid;grid-template-columns:auto 1fr;gap:40px;align-items:center;padding:48px;background:var(--glass-strong);backdrop-filter:blur(20px);border:1px solid var(--border);border-radius:var(--r-xl);box-shadow:var(--shadow)}
.founder-img{width:180px;height:180px;border-radius:50%;object-fit:cover;border:4px solid var(--primary);box-shadow:0 8px 32px var(--primary-glow)}
.founder-info h3{font-family:var(--serif);font-size:1.6rem;font-weight:700;margin-bottom:4px}
.founder-info .title{font-size:0.88rem;color:var(--primary);font-weight:600;margin-bottom:14px}
.founder-info p{font-size:0.92rem;color:var(--muted);line-height:1.7}
@media (max-width:1024px){.hero-grid{grid-template-columns:1fr}.hero-visual{display:none}.feat-grid{grid-template-columns:1fr}.stats-grid{grid-template-columns:repeat(2,1fr)}.tariffs-grid{grid-template-columns:1fr;max-width:450px;margin-left:auto;margin-right:auto}.founder-card{grid-template-columns:1fr;text-align:center;gap:24px}.founder-img{margin:0 auto}}
@media (max-width:768px){.hero{padding:120px 0 60px}.hero-trust{flex-wrap:wrap;gap:20px}}
CSS;

vpy_public_head(t('site_name') . ' — ' . t('site_tagline'), vpy_setting('site_description'), $page_css);
vpy_public_navbar('home');
?>

<main>
<!-- TICKER -->
<?php if (!empty($ticker_texts)): ?>
<div class="ticker-wrap" style="margin-top:70px">
    <div class="ticker-inner">
        <?php for ($rep = 0; $rep < 3; $rep++): foreach ($ticker_texts as $txt): ?>
        <span class="ticker-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"/></svg><?= e($txt) ?></span>
        <?php endforeach; endfor; ?>
    </div>
</div>
<?php endif; ?>

<!-- HERO -->
<section class="hero">
    <?php if ($hero_bg): ?><div class="hero-bg"><img src="<?= e($hero_bg) ?>" alt=""></div><?php endif; ?>
    <div class="container">
        <div class="hero-grid">
            <div class="hero-content reveal">
                <span class="eyebrow"><?= e(t('hero_badge')) ?></span>
                <h1 class="h-display"><em><?= e(t('hero_title_2')) ?></em><br><?= e(t('hero_title_1')) ?></h1>
                <p class="lead hero-sub"><?= e(t('hero_subtitle')) ?></p>
                <div class="hero-cta">
                    <a href="/register.php" class="btn btn-primary"><?= e(t('hero_cta_primary')) ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
                    <a href="/tariflar.php" class="btn btn-ghost"><?= e(t('nav_tariffs')) ?></a>
                </div>
                <div class="hero-trust">
                    <div class="trust-item"><span class="trust-num"><?= number_format($stat_users, 0, '.', ' ') ?>+</span><span class="trust-label"><?= e(t('hero_trust_users')) ?></span></div>
                    <div class="trust-item"><span class="trust-num"><?= $stat_success ?>%</span><span class="trust-label"><?= e(t('hero_trust_pass')) ?></span></div>
                    <div class="trust-item"><span class="trust-num">4000+</span><span class="trust-label"><?= e(t('hero_trust_questions')) ?></span></div>
                </div>
            </div>
            <div class="hero-visual reveal r2">
                <div class="hero-card">
                    <div class="hero-card-head"><span style="font-family:var(--serif);font-weight:600"><?= e(t('mockup_title')) ?></span><span class="hero-card-badge">13/20</span></div>
                    <p class="hero-q"><?= e(t('mockup_question_text')) ?></p>
                    <div class="hero-answers">
                        <div class="hero-answer"><span class="letter">A</span><?= e(t('mockup_answer_a')) ?></div>
                        <div class="hero-answer correct"><span class="letter">B</span><?= e(t('mockup_answer_b')) ?></div>
                        <div class="hero-answer"><span class="letter">C</span><?= e(t('mockup_answer_c')) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BANNER (desktop only) -->
<?php if (!empty($banners)): ?>
<section class="banner-section">
    <div class="container">
        <div class="banner-slider">
            <?php foreach ($banners as $i => $b): ?>
            <div class="banner-slide <?= $i===0?'active':'' ?>"><img src="<?= e($b) ?>" alt="Banner"></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FEATURES -->
<section>
    <div class="container">
        <div class="section-head" style="text-align:center;max-width:700px;margin:0 auto 0">
            <span class="eyebrow reveal"><?= e(t('nav_about')) ?></span>
            <h2 class="h-section reveal r1" style="margin-top:16px"><?= e(t('platform_title')) ?></h2>
            <p class="lead reveal r2" style="margin:12px auto 0;text-align:center"><?= e(t('platform_subtitle')) ?></p>
        </div>
        <div class="feat-grid">
            <?php $feats = [
                ['feat_1_title','feat_1_desc','M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3|M12 17h.01|circle:cx=12,cy=12,r=10'],
                ['feat_2_title','feat_2_desc','M22 11.08V12a10 10 0 11-5.93-9.14|M22 4L12 14.01l-3-3'],
                ['feat_3_title','feat_3_desc','M3 12a9 9 0 019-9 9.75 9.75 0 016.74 2.74L21 8|M21 3v5h-5'],
                ['feat_4_title','feat_4_desc','M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2|circle:cx=8.5,cy=7,r=4'],
                ['feat_5_title','feat_5_desc','M12 18h.01|M8 21h8|M10 2h4|M5 8h14l1 10H4L5 8z'],
                ['feat_6_title','feat_6_desc','M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z|M14 2v6h6'],
            ];
            foreach ($feats as $i => $f): ?>
            <div class="feat-card reveal r<?= ($i % 4) + 1 ?>">
                <div class="feat-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?php foreach(explode('|',$f[2]) as $p){if(strpos($p,'circle:')===0){$a=substr($p,7);echo '<circle '.str_replace(',', ' ', preg_replace('/(\w+)=/','$1="',$a)).'"/>';}else echo '<path d="'.$p.'"/>';} ?></svg></div>
                <h3><?= e(t($f[0])) ?></h3>
                <p><?= e(t($f[1])) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- STATS -->
<section>
    <div class="container">
        <div class="stats-wrap reveal">
            <div style="text-align:center;margin-bottom:40px"><h2 class="h-section" style="color:#fff"><em style="color:var(--primary-light)"><?= e(t('stats_title')) ?></em></h2></div>
            <div class="stats-grid">
                <div class="stat-cell"><div class="stat-num"><?= number_format($stat_users, 0, '.', ' ') ?>+</div><div class="stat-label"><?= e(t('stats_users')) ?></div></div>
                <div class="stat-cell"><div class="stat-num"><?= number_format($stat_tests, 0, '.', ' ') ?>+</div><div class="stat-label"><?= e(t('stats_tests')) ?></div></div>
                <div class="stat-cell"><div class="stat-num"><?= $stat_score ?></div><div class="stat-label"><?= e(t('stats_score')) ?></div></div>
                <div class="stat-cell"><div class="stat-num"><?= $stat_success ?>%</div><div class="stat-label"><?= e(t('stats_success')) ?></div></div>
            </div>
        </div>
    </div>
</section>

<!-- TARIFFS -->
<section>
    <div class="container">
        <div style="text-align:center;max-width:700px;margin:0 auto">
            <span class="eyebrow reveal"><?= e(t('nav_tariffs')) ?></span>
            <h2 class="h-section reveal r1" style="margin-top:16px"><?= e(t('tariffs_title')) ?></h2>
            <p class="lead reveal r2" style="margin:12px auto 0;text-align:center"><?= e(t('tariffs_subtitle')) ?></p>
        </div>
        <div class="tariffs-grid">
            <?php foreach ($tariffs as $i => $tf):
                $features = $is_cyrl ? ($tf['features_cyrl'] ?? $tf['features']) : $tf['features'];
                $name = $is_cyrl ? ($tf['name_cyrl'] ?? $tf['name']) : $tf['name'];
                $desc = $is_cyrl ? ($tf['description_cyrl'] ?? $tf['description']) : $tf['description'];
                $period = $is_cyrl ? ($tf['period_label_cyrl'] ?? $tf['period_label']) : $tf['period_label'];
                $featured = !empty($tf['popular']);
            ?>
            <div class="tariff-card reveal r<?= $i + 1 ?> <?= $featured ? 'featured' : '' ?>">
                <?php if ($featured): ?><span class="tariff-badge"><?= e(t('tariffs_badge_popular')) ?></span><?php endif; ?>
                <h3 class="tariff-name"><?= e($name) ?></h3>
                <p class="tariff-desc"><?= e($desc) ?></p>
                <div class="tariff-price"><?= number_format((float)$tf['price'], 0, '.', ' ') ?> <span style="font-size:0.4em;font-weight:500"><?= e(t('valyuta_sum')) ?></span></div>
                <div class="tariff-period"><?= e($period) ?><?php if (!empty($tf['price_per_day'])): ?> · <?= number_format((float)$tf['price_per_day'],0,'.',' ') ?> so'm/kun<?php endif; ?></div>
                <ul class="tariff-features">
                    <?php foreach ((array)$features as $f): ?>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"/></svg><span><?= e($f) ?></span></li>
                    <?php endforeach; ?>
                </ul>
                <a href="<?= vpy_is_logged() ? '/user/tariflar.php?tarif=' : '/register.php?tarif=' ?><?= (int)$tf['id'] ?>" class="btn <?= $featured ? 'btn-primary' : 'btn-dark' ?>"><?= e(t('tariffs_buy')) ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- FOUNDER -->
<?php if ($founder_active && vpy_setting('founder_name')): ?>
<section class="founder-section">
    <div class="container">
        <div class="founder-card reveal">
            <?php if (vpy_setting('founder_image')): ?>
            <img src="<?= e(vpy_setting('founder_image')) ?>" alt="<?= e(vpy_setting('founder_name')) ?>" class="founder-img">
            <?php endif; ?>
            <div class="founder-info">
                <span class="eyebrow" style="margin-bottom:14px"><?= $is_cyrl ? 'Асосчи' : 'Asoschi' ?></span>
                <h3><?= e(vpy_setting('founder_name')) ?></h3>
                <div class="title"><?= e(vpy_setting('founder_title')) ?></div>
                <p><?= e(vpy_setting('founder_description')) ?></p>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section style="padding-bottom:0">
    <div class="container">
        <div class="reveal" style="text-align:center;padding:60px 40px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));border-radius:var(--r-xl);color:#fff;box-shadow:0 20px 60px var(--primary-glow)">
            <h2 class="h-section" style="color:#fff;margin-bottom:12px"><?= e(t('cta_title')) ?></h2>
            <p style="color:rgba(255,255,255,0.8);margin-bottom:24px;font-size:0.95rem"><?= e(t('cta_subtitle')) ?></p>
            <a href="/register.php" class="btn" style="background:#fff;color:var(--primary);box-shadow:0 8px 24px rgba(0,0,0,0.15)"><?= e(t('cta_button')) ?><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
        </div>
    </div>
</section>
</main>

<script>
// Banner slider
(function(){var s=document.querySelectorAll('.banner-slide');if(s.length>1){var c=0;setInterval(function(){s[c].classList.remove('active');c=(c+1)%s.length;s[c].classList.add('active');},5000);}})();
</script>

<?php vpy_public_footer(); ?>
