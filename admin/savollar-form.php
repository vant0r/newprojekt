<?php
require_once __DIR__ . '/../includes/panel_layout.php';
vpy_require_admin('/login.php');

$pdo = vpy_pdo();
if (!$pdo) {
    vpy_flash_set('error', 'Bazaga ulanib bo\'lmadi');
    vpy_redirect('/admin/savollar.php');
}

$id = (int)vpy_get('id');
$q = null;
if ($id) {
    $st = $pdo->prepare("SELECT * FROM test_savollar WHERE id = :id");
    $st->execute([':id' => $id]);
    $q = $st->fetch();
}
$is_edit = !empty($q);

/* Yangi savol uchun avtomatik bilet+tartib */
if (!$is_edit) {
    $auto_bilet = (int)vpy_get('bilet', 0);
    if ($auto_bilet > 0) {
        $st = $pdo->prepare("SELECT MAX(tartib) FROM test_savollar WHERE bilet_id = :b");
        $st->execute([':b' => $auto_bilet]);
        $next_tartib = (int)$st->fetchColumn() + 1;
        $q = ['bilet_id' => $auto_bilet, 'tartib' => min(20, $next_tartib), 'qiyinlik' => 'orta', 'mavzu' => 'umumiy', 'togri' => 'A', 'holat' => 'faol'];
    }
}

if (vpy_is_post() && vpy_csrf_check(vpy_post('csrf'))) {
    $data = [
        'bilet_id' => (int)vpy_post('bilet_id', 1),
        'tartib' => (int)vpy_post('tartib', 1),
        'mavzu' => vpy_post('mavzu', 'umumiy'),
        'qiyinlik' => vpy_post('qiyinlik', 'orta'),
        'savol' => vpy_post('savol'),
        'savol_cyrl' => vpy_post('savol_cyrl'),
        'variant_a' => vpy_post('variant_a'),
        'variant_b' => vpy_post('variant_b'),
        'variant_c' => vpy_post('variant_c'),
        'variant_d' => vpy_post('variant_d'),
        'variant_a_cyrl' => vpy_post('variant_a_cyrl'),
        'variant_b_cyrl' => vpy_post('variant_b_cyrl'),
        'variant_c_cyrl' => vpy_post('variant_c_cyrl'),
        'variant_d_cyrl' => vpy_post('variant_d_cyrl'),
        'togri' => vpy_post('togri', 'A'),
        'izoh' => vpy_post('izoh'),
        'izoh_cyrl' => vpy_post('izoh_cyrl'),
        'holat' => vpy_post('holat', 'faol'),
    ];

    /* RASM yuklash yoki olib tashlash */
    $current_image = $is_edit ? ($q['rasm'] ?? '') : '';
    $data['rasm'] = $current_image;

    if (vpy_post('remove_image') === '1' && $current_image) {
        vpy_delete_upload($current_image);
        $data['rasm'] = null;
    }

    if (!empty($_FILES['rasm']['name'])) {
        $up = vpy_upload_image('rasm', 'savollar', 2048);
        if ($up['ok']) {
            if ($current_image) vpy_delete_upload($current_image);
            $data['rasm'] = $up['path'];
        } else {
            $reason_map = [
                'too_big' => 'Rasm hajmi 2 MB dan oshmasligi kerak',
                'bad_mime' => 'Faqat JPG, PNG, WEBP, GIF yoki SVG ruxsat etilgan',
                'unsafe_svg' => 'SVG ichida xavfli kod aniqlandi',
                'no_folder' => 'Yuklash papkasini yaratib bo\'lmadi',
                'move_failed' => 'Faylni saqlab bo\'lmadi'
            ];
            vpy_flash_set('error', $reason_map[$up['reason']] ?? ('Yuklash xatosi: ' . $up['reason']));
            vpy_redirect('/admin/savollar-form.php' . ($is_edit ? '?id=' . $id : ''));
        }
    }

    if ($is_edit) {
        $set = implode(', ', array_map(fn($k) => "$k = :$k", array_keys($data)));
        $st = $pdo->prepare("UPDATE test_savollar SET $set WHERE id = :id");
        $data[':id'] = $id;
        $params = [];
        foreach ($data as $k => $v) $params[(strpos($k, ':') === 0 ? $k : ":$k")] = $v;
        $st->execute($params);
        vpy_flash_set('success', t('msg_updated'));
    } else {
        $cols = implode(',', array_keys($data));
        $vals = ':' . implode(',:', array_keys($data));
        $st = $pdo->prepare("INSERT INTO test_savollar ($cols) VALUES ($vals)");
        foreach ($data as $k => $v) $st->bindValue(":$k", $v);
        $st->execute();
        vpy_flash_set('success', t('msg_added'));
    }
    if (vpy_post('and_new') === '1') {
        vpy_redirect('/admin/savollar-form.php?bilet=' . $data['bilet_id']);
    }
    vpy_redirect('/admin/savollar.php');
}

vpy_panel_head($is_edit ? t('admin_edit') . ' #' . $id : t('admin_add'));
vpy_panel_sidebar('savollar', true);
?>
<main class="main">
<?php vpy_panel_topbar(
    $is_edit ? t('admin_edit') . ' · #' . $id : t('admin_add'),
    sprintf('Bilet %02d · Savol %02d', (int)($q['bilet_id'] ?? 1), (int)($q['tartib'] ?? 1)),
    '<a href="/admin/savollar.php" class="btn btn-ghost">' . e(t('btn_back')) . '</a>'
); ?>

<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(vpy_csrf()) ?>">

    <div class="card">
        <div class="card-head"><h2>Asosiy ma'lumotlar</h2></div>
        <div class="field-row">
            <div class="field">
                <label>Bilet raqami</label>
                <input type="number" name="bilet_id" min="1" max="40" value="<?= (int)($q['bilet_id'] ?? 1) ?>" required>
            </div>
            <div class="field">
                <label>Tartib</label>
                <input type="number" name="tartib" min="1" max="20" value="<?= (int)($q['tartib'] ?? 1) ?>" required>
            </div>
        </div>
        <div class="field-row">
            <div class="field">
                <label>Mavzu</label>
                <select name="mavzu" style="width:100%;padding:13px 18px;border-radius:14px;border:1px solid var(--border-strong);background:rgba(255,253,249,0.85)">
                    <?php foreach (['belgilar','chiziqlar','signallar','tezlik','parking','kesishma','piyoda','hujjatlar','favqulodda','umumiy'] as $m): ?>
                        <option value="<?= e($m) ?>" <?= ($q['mavzu'] ?? '') === $m ? 'selected' : '' ?>><?= e($m) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label>Qiyinlik</label>
                <select name="qiyinlik" style="width:100%;padding:13px 18px;border-radius:14px;border:1px solid var(--border-strong);background:rgba(255,253,249,0.85)">
                    <option value="oson" <?= ($q['qiyinlik'] ?? '') === 'oson' ? 'selected' : '' ?>>Oson</option>
                    <option value="orta" <?= ($q['qiyinlik'] ?? 'orta') === 'orta' ? 'selected' : '' ?>>O'rta</option>
                    <option value="qiyin" <?= ($q['qiyinlik'] ?? '') === 'qiyin' ? 'selected' : '' ?>>Qiyin</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card" style="margin-top:18px">
        <div class="card-head"><h2>Savol matni</h2></div>
        <div class="field"><label>Savol (lotin)</label><textarea name="savol" required><?= e($q['savol'] ?? '') ?></textarea></div>
        <div class="field"><label>Savol (kirill)</label><textarea name="savol_cyrl"><?= e($q['savol_cyrl'] ?? '') ?></textarea></div>
    </div>

    <div class="card" style="margin-top:18px">
        <div class="card-head"><h2>Savol rasmi <span class="muted" style="font-weight:400;font-size:0.82rem">— ixtiyoriy, agar bo'lmasa logo ko'rsatiladi</span></h2></div>
        <div class="upload-grid" style="display:grid;grid-template-columns:200px 1fr;gap:24px;align-items:flex-start">
            <div class="upload-preview" id="imgPreview">
                <?php $current_rasm = $q['rasm'] ?? ''; ?>
                <?php if ($current_rasm && is_file(VPY_ROOT . $current_rasm)): ?>
                    <img src="<?= e($current_rasm) ?>" alt="Hozirgi rasm" data-state="custom">
                    <div class="upload-state-label">Yuklangan rasm</div>
                <?php else: ?>
                    <img src="<?= e(vpy_logo_url()) ?>" alt="Logo (default)" data-state="logo" class="is-logo">
                    <div class="upload-state-label">Logo (default)</div>
                <?php endif; ?>
            </div>
            <div>
                <input type="file" name="rasm" id="rasmInput" accept="image/jpeg,image/png,image/webp,image/gif,image/svg+xml" style="display:none">
                <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:12px">
                    <button type="button" class="btn btn-dark" onclick="document.getElementById('rasmInput').click()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Rasm tanlash
                    </button>
                    <?php if ($current_rasm): ?>
                    <label class="btn btn-ghost" style="cursor:pointer">
                        <input type="checkbox" name="remove_image" value="1" style="display:none" onchange="document.getElementById('removeNotice').style.display=this.checked?'block':'none';this.closest('label').style.background=this.checked?'rgba(255,96,88,0.1)':'';this.closest('label').style.color=this.checked?'#C73E36':'';">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-2 14a2 2 0 01-2 2H9a2 2 0 01-2-2L5 6"/></svg>
                        Hozirgi rasmni o'chirish
                    </label>
                    <?php endif; ?>
                </div>
                <div id="fileInfo" style="font-size:0.85rem;color:var(--muted);min-height:24px"></div>
                <div id="removeNotice" style="display:none;margin-top:10px;padding:10px 14px;background:rgba(255,96,88,0.08);border:1px solid rgba(255,96,88,0.25);border-radius:12px;color:#C73E36;font-size:0.85rem">
                    Saqlash bilan birga, hozirgi rasm o'chiriladi va logo ko'rsatiladi.
                </div>
                <div style="margin-top:14px;font-size:0.85rem;color:var(--muted);line-height:1.6">
                    <strong>Talablar:</strong><br>
                    · Format: JPG, PNG, WEBP, GIF yoki SVG<br>
                    · Maksimal hajm: 2 MB<br>
                    · Tavsiya etilgan o'lcham: 800×600 px<br>
                    · Rasm yuklamasangiz, foydalanuvchi savol oldida logo ko'radi
                </div>
            </div>
        </div>
        <style>
            .upload-preview{position:relative;width:200px;border-radius:18px;overflow:hidden;background:var(--glass);border:1.5px dashed var(--border-strong);aspect-ratio:4/3;display:flex;align-items:center;justify-content:center;padding:18px}
            .upload-preview img{max-width:100%;max-height:100%;object-fit:contain;border-radius:12px}
            .upload-preview img.is-logo{opacity:0.5;filter:grayscale(0.2)}
            .upload-state-label{position:absolute;bottom:8px;left:8px;right:8px;text-align:center;font-size:0.7rem;text-transform:uppercase;letter-spacing:0.06em;color:var(--muted);font-weight:600;background:rgba(255,253,249,0.85);padding:4px 8px;border-radius:8px;backdrop-filter:blur(6px)}
            @media (max-width:768px){.upload-grid{grid-template-columns:1fr!important}.upload-preview{width:100%;max-width:280px;margin:0 auto}}
        </style>
    </div>

    <div class="card" style="margin-top:18px">
        <div class="card-head"><h2>Variantlar</h2></div>
        <?php foreach (['a','b','c','d'] as $L): ?>
            <div style="display:grid;grid-template-columns:auto 1fr 1fr;gap:14px;align-items:start;margin-bottom:14px">
                <div style="padding-top:36px"><label style="display:flex;align-items:center;gap:8px;font-size:0.9rem;font-weight:700"><input type="radio" name="togri" value="<?= strtoupper($L) ?>" <?= strtoupper($q['togri'] ?? 'A') === strtoupper($L) ? 'checked' : '' ?>> <span style="width:32px;height:32px;border-radius:10px;background:rgba(13,107,78,0.08);color:var(--primary);display:grid;place-items:center;font-weight:700"><?= strtoupper($L) ?></span></label></div>
                <div class="field" style="margin:0"><label>Variant <?= strtoupper($L) ?> (lotin)</label><input type="text" name="variant_<?= $L ?>" value="<?= e($q["variant_$L"] ?? '') ?>" <?= in_array($L, ['a','b']) ? 'required' : '' ?>></div>
                <div class="field" style="margin:0"><label>Variant <?= strtoupper($L) ?> (kirill)</label><input type="text" name="variant_<?= $L ?>_cyrl" value="<?= e($q["variant_{$L}_cyrl"] ?? '') ?>"></div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="card" style="margin-top:18px">
        <div class="card-head"><h2>Izoh</h2></div>
        <div class="field-row">
            <div class="field"><label>Izoh (lotin)</label><textarea name="izoh"><?= e($q['izoh'] ?? '') ?></textarea></div>
            <div class="field"><label>Izoh (kirill)</label><textarea name="izoh_cyrl"><?= e($q['izoh_cyrl'] ?? '') ?></textarea></div>
        </div>
        <div class="field-row">
            <div class="field">
                <label>Holat</label>
                <select name="holat" style="width:100%;padding:13px 18px;border-radius:14px;border:1px solid var(--border-strong);background:rgba(255,253,249,0.85)">
                    <option value="faol" <?= ($q['holat'] ?? 'faol') === 'faol' ? 'selected' : '' ?>>Faol</option>
                    <option value="noaktiv" <?= ($q['holat'] ?? '') === 'noaktiv' ? 'selected' : '' ?>>Noaktiv</option>
                </select>
            </div>
            <div></div>
        </div>
    </div>

    <div style="display:flex;gap:10px;margin-top:18px">
        <button type="submit" class="btn btn-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><polyline points="20 6 9 17 4 12"/></svg><?= e(t('btn_save')) ?></button>
        <button type="submit" name="and_new" value="1" class="btn btn-success" title="Ctrl+Enter"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg> Saqlash va yangi qo'shish</button>
        <a href="/admin/savollar.php" class="btn btn-ghost"><?= e(t('btn_cancel')) ?></a>
    </div>
</form>
</main>
<script>
(function(){
    var input = document.getElementById('rasmInput');
    var preview = document.getElementById('imgPreview');
    var info = document.getElementById('fileInfo');
    if (!input || !preview) return;
    input.addEventListener('change', function(e){        var file = e.target.files[0];
        if (!file) return;
        var maxBytes = 2 * 1024 * 1024;
        if (file.size > maxBytes){
            info.innerHTML = '<span style="color:#C73E36">Fayl 2 MB dan oshib ketdi (' + (file.size/1024/1024).toFixed(2) + ' MB)</span>';
            input.value = '';
            return;
        }
        var allowed = ['image/jpeg','image/png','image/webp','image/gif','image/svg+xml'];
        if (allowed.indexOf(file.type) === -1){
            info.innerHTML = '<span style="color:#C73E36">Faqat JPG/PNG/WEBP/GIF/SVG ruxsat etilgan</span>';
            input.value = '';
            return;
        }
        var reader = new FileReader();
        reader.onload = function(ev){
            preview.innerHTML = '<img src="' + ev.target.result + '" alt="Yangi rasm" data-state="new"><div class="upload-state-label" style="background:rgba(232,168,56,0.18);color:#A87830">Yangi tanlangan</div>';
        };
        reader.readAsDataURL(file);
        info.innerHTML = '<strong>' + (file.name) + '</strong> · ' + (file.size/1024).toFixed(0) + ' KB · saqlash uchun "Saqlash" tugmasini bosing';
    });

    /* Auto-focus first empty required field for fast input */
    var firstField = document.querySelector('input[name="bilet_id"]');
    if (firstField && !<?= $is_edit ? 'true' : 'false' ?>) firstField.focus();

    /* Keyboard shortcuts: Ctrl+S save, Ctrl+Enter save and add new */
    document.addEventListener('keydown', function(e){
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            var btn = document.querySelector('button.btn-primary[type="submit"]');
            if (btn) btn.click();
        }
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
            e.preventDefault();
            var btnNew = document.querySelector('button[name="and_new"]');
            if (btnNew) btnNew.click();
        }
    });
})();
</script>
<?php vpy_panel_foot(); ?>
