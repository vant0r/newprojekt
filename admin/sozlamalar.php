<?php
require_once __DIR__ . '/../includes/panel_layout.php';
vpy_require_admin('/login.php');

if (vpy_is_post() && vpy_csrf_check(vpy_post('csrf'))) {
    $settings = vpy_read_json('sozlamalar', []);
    $by_key = [];
    foreach ($settings as $i => $s) $by_key[$s['key']] = $i;

    /* BRENDING — logo, favicon, og-image yuklash */
    $upload_fields = [
        'site_logo' => ['folder' => 'branding', 'group' => 'branding'],
        'site_favicon' => ['folder' => 'branding', 'group' => 'branding'],
        'site_og_image' => ['folder' => 'branding', 'group' => 'branding'],
    ];
    foreach ($upload_fields as $field => $cfg) {
        if (vpy_post('remove_' . $field) === '1') {
            $existing = $settings[$by_key[$field] ?? -1]['value'] ?? '';
            if ($existing) vpy_delete_upload($existing);
            if (isset($by_key[$field])) {
                $settings[$by_key[$field]]['value'] = '';
            }
        }
        if (!empty($_FILES[$field]['name'])) {
            $up = vpy_upload_image($field, $cfg['folder'], 4096);
            if ($up['ok']) {
                $existing = $settings[$by_key[$field] ?? -1]['value'] ?? '';
                if ($existing && $existing !== $up['path']) vpy_delete_upload($existing);
                if (isset($by_key[$field])) {
                    $settings[$by_key[$field]]['value'] = $up['path'];
                } else {
                    $settings[] = ['key' => $field, 'value' => $up['path'], 'group' => $cfg['group']];
                    $by_key[$field] = count($settings) - 1;
                }
            } else {
                $reason_map = [
                    'too_big' => 'Fayl 4 MB dan oshmasligi kerak',
                    'bad_mime' => 'Faqat JPG, PNG, WEBP, GIF yoki SVG',
                    'unsafe_svg' => 'SVG ichida xavfli kod aniqlandi',
                ];
                vpy_flash_set('error', $field . ': ' . ($reason_map[$up['reason']] ?? $up['reason']));
                vpy_redirect('/admin/sozlamalar.php?tab=' . vpy_post('tab', 'branding'));
            }
        }
    }

    /* Boshqa text/textarea sozlamalari */
    foreach ($_POST as $key => $val) {
        if ($key === 'csrf' || !is_string($val)) continue;
        if (strpos($key, 'remove_') === 0 || $key === 'tab') continue;
        if (isset($upload_fields[$key])) continue;
        if (isset($by_key[$key])) {
            $settings[$by_key[$key]]['value'] = (string)$val;
        } else {
            $settings[] = ['key' => $key, 'value' => (string)$val, 'group' => 'custom'];
        }
    }
    vpy_write_json('sozlamalar', $settings);
    vpy_flash_set('success', t('msg_saved'));
    vpy_redirect('/admin/sozlamalar.php?tab=' . vpy_post('tab', 'general'));
}

$settings = vpy_read_json('sozlamalar', []);
$grouped = [];
foreach ($settings as $s) $grouped[$s['group'] ?? 'general'][$s['key']] = $s['value'];

$tabs = [
    'general' => 'Umumiy',
    'branding' => 'Brending',
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
.brand-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:22px}
.brand-card{padding:24px;background:rgba(255,253,249,0.5);border:1px solid var(--border);border-radius:var(--r);transition:var(--t)}
.brand-card:hover{border-color:var(--border-strong)}
.brand-card h3{font-family:var(--serif);font-size:1.1rem;font-weight:600;margin-bottom:6px}
.brand-card .hint{font-size:0.82rem;color:var(--muted);margin-bottom:16px;line-height:1.5}
.brand-preview{aspect-ratio:16/10;background:linear-gradient(135deg,#FFFDF9,#F0EBE2);border:1.5px dashed var(--border-strong);border-radius:14px;display:flex;align-items:center;justify-content:center;padding:18px;margin-bottom:14px;position:relative;overflow:hidden}
.brand-preview img{max-width:100%;max-height:100%;object-fit:contain}
.brand-preview.empty img{opacity:0.5;filter:grayscale(0.3)}
.brand-state{position:absolute;top:8px;right:8px;font-size:0.68rem;text-transform:uppercase;letter-spacing:0.06em;font-weight:700;padding:4px 10px;border-radius:var(--pill)}
.brand-state.custom{background:var(--primary);color:#fff}
.brand-state.empty{background:rgba(180,160,130,0.2);color:var(--muted)}
.brand-actions{display:flex;gap:8px;flex-wrap:wrap}
.brand-actions .btn{flex:1;padding:10px 16px;font-size:0.82rem;justify-content:center}
.brand-favicon-preview{aspect-ratio:1;max-width:140px;margin:0 auto 14px}
@media (max-width:640px){.brand-grid{grid-template-columns:1fr}}
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

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(vpy_csrf()) ?>">
    <input type="hidden" name="tab" value="<?= e($current_tab) ?>">

    <?php if ($current_tab === 'branding'): ?>
        <?php
        $logo_path = $grouped['branding']['site_logo'] ?? '';
        $favicon_path = $grouped['branding']['site_favicon'] ?? '';
        $og_path = $grouped['branding']['site_og_image'] ?? '';
        $logo_has = $logo_path && is_file(VPY_ROOT . $logo_path);
        $favicon_has = $favicon_path && is_file(VPY_ROOT . $favicon_path);
        $og_has = $og_path && is_file(VPY_ROOT . $og_path);
        ?>
        <div class="card">
            <div class="card-head">
                <h2>Brending</h2>
                <span class="muted" style="font-size:0.85rem">Logo, favicon va ulashish rasmlari</span>
            </div>
            <div class="brand-grid">

                <div class="brand-card">
                    <h3>Logo</h3>
                    <p class="hint">Saytning yuqori chap burchagida va savol uchun rasm yo'q paytda ko'rsatiladi. Tavsiya: SVG yoki shaffof PNG, 220×64.</p>
                    <div class="brand-preview <?= $logo_has ? '' : 'empty' ?>">
                        <span class="brand-state <?= $logo_has ? 'custom' : 'empty' ?>"><?= $logo_has ? 'Yuklangan' : 'Default' ?></span>
                        <img src="<?= e($logo_has ? $logo_path : '/assets/images/logo.svg') ?>" alt="Logo">
                    </div>
                    <input type="file" name="site_logo" id="logoInput" accept="image/jpeg,image/png,image/webp,image/svg+xml" style="display:none">
                    <div class="brand-actions">
                        <button type="button" class="btn btn-dark" onclick="document.getElementById('logoInput').click()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Yuklash
                        </button>
                        <?php if ($logo_has): ?>
                        <label class="btn btn-ghost" style="cursor:pointer">
                            <input type="checkbox" name="remove_site_logo" value="1" style="display:none">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/></svg>
                            O'chirish
                        </label>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="brand-card">
                    <h3>Favicon</h3>
                    <p class="hint">Brauzer tabidagi belgi. Tavsiya: SVG yoki kvadrat PNG, 64×64.</p>
                    <div class="brand-preview brand-favicon-preview <?= $favicon_has ? '' : 'empty' ?>">
                        <span class="brand-state <?= $favicon_has ? 'custom' : 'empty' ?>"><?= $favicon_has ? 'Yuklangan' : 'Default' ?></span>
                        <img src="<?= e($favicon_has ? $favicon_path : '/assets/images/favicon.svg') ?>" alt="Favicon">
                    </div>
                    <input type="file" name="site_favicon" id="faviconInput" accept="image/png,image/svg+xml,image/x-icon" style="display:none">
                    <div class="brand-actions">
                        <button type="button" class="btn btn-dark" onclick="document.getElementById('faviconInput').click()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Yuklash
                        </button>
                        <?php if ($favicon_has): ?>
                        <label class="btn btn-ghost" style="cursor:pointer">
                            <input type="checkbox" name="remove_site_favicon" value="1" style="display:none">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/></svg>
                            O'chirish
                        </label>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="brand-card">
                    <h3>OG Image</h3>
                    <p class="hint">Telegram, Facebook, Twitter ulashish paytida ko'rinadigan rasm. Tavsiya: 1200×630 px JPG/PNG.</p>
                    <div class="brand-preview <?= $og_has ? '' : 'empty' ?>">
                        <span class="brand-state <?= $og_has ? 'custom' : 'empty' ?>"><?= $og_has ? 'Yuklangan' : 'Yuklanmagan' ?></span>
                        <?php if ($og_has): ?>
                            <img src="<?= e($og_path) ?>" alt="OG Image">
                        <?php else: ?>
                            <img src="/assets/images/logo.svg" alt="Logo (placeholder)">
                        <?php endif; ?>
                    </div>
                    <input type="file" name="site_og_image" id="ogInput" accept="image/jpeg,image/png,image/webp" style="display:none">
                    <div class="brand-actions">
                        <button type="button" class="btn btn-dark" onclick="document.getElementById('ogInput').click()">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                            Yuklash
                        </button>
                        <?php if ($og_has): ?>
                        <label class="btn btn-ghost" style="cursor:pointer">
                            <input type="checkbox" name="remove_site_og_image" value="1" style="display:none">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/></svg>
                            O'chirish
                        </label>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <div style="margin-top:22px;padding:18px 22px;background:rgba(232,168,56,0.08);border:1px solid rgba(232,168,56,0.25);border-radius:14px;color:#A87830;font-size:0.88rem;line-height:1.6">
                <strong>Eslatma:</strong> Logo yuklamasangiz, default <code>/assets/images/logo.svg</code> ishlatiladi. Logo savollar uchun rasm bo'lmaganda ham ko'rsatiladi. Maksimal hajm: 4 MB. Format: JPG, PNG, WEBP, SVG.
            </div>

            <div style="display:flex;gap:10px;margin-top:18px">
                <button type="submit" class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"/></svg><?= e(t('btn_save')) ?></button>
            </div>
        </div>

    <?php else: ?>
        <div class="card">
            <?php
            $current_settings = $grouped[$current_tab] ?? [];
            $textareas = ['site_description', 'site_keywords', 'contact_address'];
            ?>
            <?php if (empty($current_settings)): ?>
                <div class="empty"><h3>Bu bo'limda sozlama yo'q</h3></div>
            <?php else: ?>
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
            <?php endif; ?>

            <div style="display:flex;gap:10px;margin-top:14px">
                <button type="submit" class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"/></svg><?= e(t('btn_save')) ?></button>
            </div>
        </div>
    <?php endif; ?>
</form>

</main>
<script>
(function(){
    ['logoInput','faviconInput','ogInput'].forEach(function(id){
        var inp = document.getElementById(id);
        if (!inp) return;
        inp.addEventListener('change', function(){
            if (inp.files[0]) {
                var card = inp.closest('.brand-card');
                var preview = card.querySelector('.brand-preview img');
                var state = card.querySelector('.brand-state');
                var reader = new FileReader();
                reader.onload = function(ev){
                    preview.src = ev.target.result;
                    preview.style.opacity = '1';
                    preview.style.filter = 'none';
                    card.querySelector('.brand-preview').classList.remove('empty');
                    state.textContent = 'Yangi tanlangan';
                    state.style.background = '#E8A838';
                    state.style.color = '#fff';
                };
                reader.readAsDataURL(inp.files[0]);
            }
        });
    });
    document.querySelectorAll('input[name^="remove_"]').forEach(function(cb){
        cb.addEventListener('change', function(){
            var label = cb.closest('label');
            if (cb.checked) {
                label.style.background = 'rgba(255,96,88,0.1)';
                label.style.color = '#C73E36';
                label.style.borderColor = 'rgba(255,96,88,0.3)';
            } else {
                label.style.background = '';
                label.style.color = '';
                label.style.borderColor = '';
            }
        });
    });
})();
</script>
<?php vpy_panel_foot(); ?>
