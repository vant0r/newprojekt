<?php
require_once __DIR__ . '/../includes/panel_layout.php';
vpy_require_admin('/login.php');

if (vpy_is_post() && vpy_csrf_check(vpy_post('csrf'))) {
    $settings = vpy_read_json('sozlamalar', []);
    $by_key = [];
    foreach ($settings as $i => $s) $by_key[$s['key']] = $i;
    foreach ($_POST as $key => $val) {
        if ($key === 'csrf' || !is_string($val)) continue;
        if (isset($by_key[$key])) {
            $settings[$by_key[$key]]['value'] = is_array($val) ? json_encode($val) : (string)$val;
        } else {
            $settings[] = ['key' => $key, 'value' => (string)$val, 'group' => 'custom'];
        }
    }
    vpy_write_json('sozlamalar', $settings);
    vpy_flash_set('success', t('msg_saved'));
    vpy_redirect('/admin/sozlamalar.php');
}

$settings = vpy_read_json('sozlamalar', []);
$grouped = [];
foreach ($settings as $s) $grouped[$s['group'] ?? 'general'][$s['key']] = $s['value'];

$tabs = [
    'general' => 'Umumiy',
    'contact' => 'Aloqa',
    'social' => 'Ijtimoiy',
    'company' => 'Kompaniya',
    'tests' => 'Testlar',
    'referral' => 'Referral',
    'landing' => 'Bosh sahifa',
    'telegram' => 'Telegram',
    'payments' => 'To\'lovlar',
    'system' => 'Tizim',
];
$current_tab = vpy_get('tab', 'general');
if (!isset($tabs[$current_tab])) $current_tab = 'general';

vpy_panel_head(t('admin_settings'), <<<CSS
.s-tabs{display:flex;gap:6px;flex-wrap:wrap;padding:6px;background:var(--glass);backdrop-filter:blur(20px);border:1px solid var(--border);border-radius:18px;margin-bottom:22px}
.s-tabs a{padding:10px 16px;border-radius:14px;font-size:0.85rem;font-weight:600;color:var(--dark-soft);text-decoration:none}
.s-tabs a.active{background:var(--primary);color:#fff;box-shadow:0 8px 18px var(--primary-glow)}
CSS);
vpy_panel_sidebar('sozlamalar', true);
?>
<main class="main">
<?php vpy_panel_topbar(t('admin_settings'), $tabs[$current_tab]); ?>

<div class="s-tabs">
    <?php foreach ($tabs as $k => $name): ?>
        <a href="?tab=<?= e($k) ?>" class="<?= $current_tab === $k ? 'active' : '' ?>"><?= e($name) ?></a>
    <?php endforeach; ?>
</div>

<form method="post">
    <input type="hidden" name="csrf" value="<?= e(vpy_csrf()) ?>">
    <div class="card">
        <?php
        $current_settings = $grouped[$current_tab] ?? [];
        $textareas = ['site_description', 'site_keywords', 'contact_address'];
        ?>
        <?php foreach ($current_settings as $key => $value): ?>
        <div class="field">
            <label><?= e(ucfirst(str_replace('_', ' ', $key))) ?></label>
            <?php if (in_array($key, $textareas)): ?>
                <textarea name="<?= e($key) ?>" rows="3"><?= e($value) ?></textarea>
            <?php elseif (strpos($key, '_secret') !== false || strpos($key, '_key') !== false || strpos($key, '_token') !== false || strpos($key, '_password') !== false): ?>
                <input type="password" name="<?= e($key) ?>" value="<?= e($value) ?>" autocomplete="off">
            <?php else: ?>
                <input type="text" name="<?= e($key) ?>" value="<?= e($value) ?>">
            <?php endif; ?>
        </div>
        <?php endforeach; ?>

        <div style="display:flex;gap:10px;margin-top:14px">
            <button type="submit" class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"/></svg><?= e(t('btn_save')) ?></button>
        </div>
    </div>
</form>

</main>
<?php vpy_panel_foot(); ?>
