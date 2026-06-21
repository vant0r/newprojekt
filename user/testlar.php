<?php
require_once __DIR__ . '/../includes/panel_layout.php';
vpy_require_login('/login.php');

$u = vpy_user();
$pdo = vpy_pdo();
$bilet_data = [];

if ($pdo) {
    try {
        $st = $pdo->query("SELECT bilet_id, COUNT(*) as cnt FROM test_savollar WHERE holat='faol' GROUP BY bilet_id ORDER BY bilet_id");
        foreach ($st->fetchAll() as $row) $bilet_data[(int)$row['bilet_id']] = (int)$row['cnt'];
    } catch (Exception $e) {}
}

$total_bilets = max(40, max(array_keys($bilet_data) ?: [0]));

// User natijalar
$user_results = vpy_filter('natijalar', fn($r) => (int)$r['user_id'] === (int)$u['id'] && ($r['type'] ?? '') === 'bilet');
$bilet_done = [];
foreach ($user_results as $r) {
    $bid = (int)($r['bilet_id'] ?? 0);
    if (!$bid) continue;
    if (!isset($bilet_done[$bid]) || ((int)$r['score'] > (int)$bilet_done[$bid]['score'])) $bilet_done[$bid] = $r;
}

// Tarif faol mi?
$active_tariff = vpy_active_tariff_for_user($u['id']);
$has_access = !empty($active_tariff);

// Bepul biletlar (admin belgilagan) — sozlamalardan
$free_bilets_str = vpy_setting('free_bilets', '');
$free_bilets = array_filter(array_map('intval', explode(',', $free_bilets_str)));

vpy_panel_head(t('tickets_title'), <<<CSS
.bilet-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:16px}
.bilet-card{position:relative;aspect-ratio:1;background:var(--glass);backdrop-filter:blur(20px) saturate(160%);-webkit-backdrop-filter:blur(20px) saturate(160%);border:1px solid var(--border-strong);border-radius:var(--r);padding:18px;display:flex;flex-direction:column;justify-content:space-between;transition:transform var(--t),box-shadow var(--t),border-color var(--t);text-decoration:none;color:inherit;overflow:hidden}
.bilet-card:hover:not(.locked){transform:translateY(-4px);box-shadow:var(--shadow);border-color:var(--primary)}
.bilet-card.done{background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;border-color:transparent}
.bilet-card.free{border-color:rgba(34,197,94,0.4);background:rgba(34,197,94,0.04)}
.bilet-card.free .bilet-num{color:#16A34A}
.bilet-card.locked{opacity:0.55;cursor:not-allowed}
.bilet-card.locked:hover{transform:none;box-shadow:none}
.bilet-card.bilet-empty{opacity:0.35;pointer-events:none}
.bilet-num{font-family:var(--sans);font-size:2.2rem;font-weight:800;line-height:1;color:var(--primary)}
.bilet-card.done .bilet-num{color:#fff}
.bilet-card.locked .bilet-num{color:var(--muted)}
.bilet-meta{display:flex;flex-direction:column;gap:2px;position:relative;z-index:2}
.bilet-label{font-size:0.7rem;color:var(--muted);text-transform:uppercase;letter-spacing:0.06em;font-weight:700}
.bilet-card.done .bilet-label{color:rgba(255,255,255,0.65)}
.bilet-count{font-size:0.88rem;font-weight:600}
.bilet-card.done .bilet-count{color:rgba(255,255,255,0.9)}
.bilet-score{position:absolute;top:12px;right:12px;padding:4px 10px;border-radius:var(--pill);font-size:0.72rem;font-weight:700;background:rgba(20,86,168,0.1);color:var(--primary)}
.bilet-card.done .bilet-score{background:rgba(255,255,255,0.2);color:#fff}
.bilet-lock{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:32px;height:32px;color:var(--muted);opacity:0.4}
.bilet-free-badge{position:absolute;top:10px;left:10px;padding:2px 8px;border-radius:6px;font-size:0.6rem;font-weight:700;background:rgba(34,197,94,0.15);color:#16A34A;text-transform:uppercase}
.access-banner{padding:18px 22px;background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:14px;margin-bottom:18px;display:flex;align-items:center;gap:14px;flex-wrap:wrap}
.access-banner svg{width:22px;height:22px;color:#B45309;flex-shrink:0}
.access-banner p{flex:1;font-size:0.88rem;color:var(--dark-soft);font-weight:500}
.access-banner .btn{flex-shrink:0;padding:9px 18px;font-size:0.82rem}
@media (max-width:640px){.bilet-grid{grid-template-columns:repeat(3,1fr);gap:12px}.bilet-card{padding:14px}.bilet-num{font-size:1.6rem}}
@media (max-width:380px){.bilet-grid{grid-template-columns:repeat(2,1fr)}}
CSS);
vpy_panel_sidebar('testlar', false);
?>

<main class="main">
<?php vpy_panel_topbar(t('tickets_title'), t('tickets_subtitle'),
    $has_access
        ? '<a href="/user/test.php?type=quick" class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>' . e(t('user_quick_test')) . '</a>'
        : ''
); ?>

<?php if (!$has_access): ?>
<div class="access-banner">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
    <p>Biletlarni ochish uchun tarif sotib oling. Bepul biletlar <strong style="color:#16A34A">yashil</strong> rangda belgilangan.</p>
    <a href="/user/tariflar.php" class="btn btn-primary">Tarif sotib olish</a>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-head">
        <h2><?= e(t('tickets_title')) ?> · <?= count($bilet_data) ?>/<?= $total_bilets ?></h2>
        <span class="chip chip-success"><?= count($bilet_done) ?> <?= e(t('ticket_done')) ?></span>
    </div>

    <div class="bilet-grid">
        <?php for ($i = 1; $i <= $total_bilets; $i++):
            $count = $bilet_data[$i] ?? 0;
            $done = $bilet_done[$i] ?? null;
            $is_free = in_array($i, $free_bilets);
            $can_access = $has_access || $is_free;

            if ($count === 0) $cls = 'bilet-empty';
            elseif ($done) $cls = 'done';
            elseif (!$can_access) $cls = 'locked';
            elseif ($is_free) $cls = 'free';
            else $cls = '';

            $href = ($count && $can_access) ? '/user/test.php?bilet=' . $i : '#';
        ?>
            <a href="<?= $href ?>" class="bilet-card <?= $cls ?>" <?= !$can_access && $count ? 'onclick="alert(\'Bu biletni ochish uchun tarif sotib oling!\');return false;"' : '' ?>>
                <?php if ($done): ?>
                    <span class="bilet-score"><?= (int)$done['score'] ?>/<?= (int)$done['total'] ?></span>
                <?php endif; ?>
                <?php if ($is_free && !$done): ?>
                    <span class="bilet-free-badge">Bepul</span>
                <?php endif; ?>
                <?php if (!$can_access && $count && !$done): ?>
                    <svg class="bilet-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                <?php endif; ?>
                <div>
                    <div class="bilet-label"><?= e(t('ticket_label')) ?></div>
                    <div class="bilet-num"><?= sprintf('%02d', $i) ?></div>
                </div>
                <div class="bilet-meta">
                    <span class="bilet-count"><?= $count ?: '—' ?> <?= e(t('ticket_count')) ?></span>
                </div>
            </a>
        <?php endfor; ?>
    </div>
</div>

</main>
<?php vpy_panel_foot(); ?>
