<?php
require_once __DIR__ . '/includes/public_layout.php';
require_once __DIR__ . '/includes/notifications.php';

$success = false;
$error = '';
$vals = ['name' => '', 'phone' => '', 'subject' => '', 'message' => ''];

if (vpy_is_post()) {
    if (!vpy_csrf_check(vpy_post('csrf'))) {
        $error = t('xato_csrf');
    } else {
        $vals['name'] = vpy_post('name');
        $vals['phone'] = vpy_post('phone');
        $vals['subject'] = vpy_post('subject');
        $vals['message'] = vpy_post('message');
        if (mb_strlen($vals['name'], 'UTF-8') < 2 || mb_strlen($vals['message'], 'UTF-8') < 5) {
            $error = t('xato_format');
        } else {
            vpy_log('contact', 'Aloqa xabari', $vals);
            vpy_notify_admin('Yangi aloqa xabari', $vals['name'] . ' (' . $vals['phone'] . '): ' . $vals['subject'] . ' — ' . mb_substr($vals['message'], 0, 200, 'UTF-8'));
            $success = true;
            $vals = ['name' => '', 'phone' => '', 'subject' => '', 'message' => ''];
        }
    }
}

vpy_public_head(t('contact_title'), t('contact_subtitle'), <<<CSS
.contact-grid{display:grid;grid-template-columns:1fr 1.3fr;gap:50px;align-items:start;margin-top:30px}
.contact-info{display:flex;flex-direction:column;gap:16px}
.contact-info-card{padding:24px;background:var(--glass);backdrop-filter:blur(30px);border:1px solid var(--border);border-radius:var(--r);display:flex;align-items:flex-start;gap:18px;transition:var(--t)}
.contact-info-card:hover{transform:translateX(4px);border-color:var(--primary)}
.contact-info-ico{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,var(--primary),var(--primary-dark));color:#fff;display:grid;place-items:center;flex-shrink:0;box-shadow:0 8px 18px var(--primary-glow)}
.contact-info-ico svg{width:22px;height:22px}
.contact-info-card h3{font-family:var(--serif);font-size:1.05rem;font-weight:600;margin-bottom:4px}
.contact-info-card p,.contact-info-card a{font-size:0.9rem;color:var(--dark-soft);line-height:1.5}
.contact-info-card a:hover{color:var(--primary)}
.contact-form{padding:42px 38px;background:var(--glass-strong);backdrop-filter:blur(30px) saturate(180%);border:1px solid var(--border);border-radius:var(--r-lg);box-shadow:var(--shadow)}
.contact-form h2{font-family:var(--serif);font-size:1.6rem;font-weight:500;margin-bottom:8px}
.contact-form p.sub{color:var(--muted);font-size:0.9rem;margin-bottom:26px}
.field{margin-bottom:16px;position:relative}
.field label{display:block;font-size:0.78rem;font-weight:600;color:var(--dark-soft);margin-bottom:6px;letter-spacing:0.04em;text-transform:uppercase}
.field input,.field textarea{width:100%;padding:14px 18px;border-radius:14px;border:1px solid var(--border-strong);background:rgba(255,253,249,0.85);color:var(--dark);font-size:0.95rem;font-family:inherit;transition:var(--t)}
.field textarea{min-height:140px;resize:vertical;line-height:1.5}
.field input:focus,.field textarea:focus{outline:none;border-color:var(--primary);background:var(--light);box-shadow:0 0 0 4px rgba(13,107,78,0.12)}
.fld-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.flash{padding:14px 20px;border-radius:14px;margin-bottom:20px;display:flex;align-items:center;gap:10px;font-size:0.92rem}
.flash.success{background:rgba(13,107,78,0.1);color:var(--primary-dark);border:1px solid rgba(13,107,78,0.2)}
.flash.error{background:rgba(255,96,88,0.1);color:#C73E36;border:1px solid rgba(255,96,88,0.3)}
.flash svg{width:18px;height:18px}
.contact-form .btn{width:100%;padding:18px}
.map-frame{margin-top:50px;border-radius:var(--r-lg);overflow:hidden;height:380px;border:1px solid var(--border);box-shadow:var(--shadow);position:relative;background:linear-gradient(135deg,#FAF7F2 0%,#F0EBE2 100%);display:grid;place-items:center;color:var(--muted);font-family:var(--serif);font-size:1.5rem}
.map-frame::before{content:"";position:absolute;inset:0;background:radial-gradient(ellipse at center,rgba(13,107,78,0.08),transparent 60%)}
.map-pin{position:relative;z-index:2;text-align:center}
.map-pin svg{width:50px;height:50px;color:var(--primary);margin:0 auto 14px;animation:bounce 2s ease-in-out infinite}
@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}
.map-pin span{font-family:var(--sans);font-size:0.95rem;color:var(--dark);font-weight:500;display:block;margin-top:6px}
@media (max-width:1024px){.contact-grid{grid-template-columns:1fr}}
@media (max-width:640px){.contact-form{padding:30px 24px}.fld-row{grid-template-columns:1fr}}
CSS);
vpy_public_navbar('aloqa');
?>

<main>
<section class="page-hero">
    <div class="container">
        <span class="eyebrow"><?= e(t('nav_contact')) ?></span>
        <h1 class="h-display"><?= e(t('contact_title')) ?></h1>
        <p class="lead"><?= e(t('contact_subtitle')) ?></p>
    </div>
</section>

<section style="padding-top:20px">
    <div class="container">
        <div class="contact-grid">
            <div class="contact-info">
                <div class="contact-info-card reveal r1">
                    <span class="contact-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg></span>
                    <div>
                        <h3><?= e(t('footer_phone')) ?></h3>
                        <a href="tel:<?= e(preg_replace('/\D/', '', vpy_setting('contact_phone', '+998901234567'))) ?>"><?= e(vpy_setting('contact_phone', t('footer_phone_value'))) ?></a>
                    </div>
                </div>
                <div class="contact-info-card reveal r2">
                    <span class="contact-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                    <div>
                        <h3><?= e(t('footer_email')) ?></h3>
                        <a href="mailto:<?= e(vpy_setting('contact_email', t('footer_email_value'))) ?>"><?= e(vpy_setting('contact_email', t('footer_email_value'))) ?></a>
                    </div>
                </div>
                <div class="contact-info-card reveal r3">
                    <span class="contact-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg></span>
                    <div>
                        <h3><?= e(t('footer_address')) ?></h3>
                        <p><?= e(vpy_setting('contact_address', t('footer_address_value'))) ?></p>
                    </div>
                </div>
                <div class="contact-info-card reveal r4">
                    <span class="contact-info-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></span>
                    <div>
                        <h3><?= e(t('footer_hours')) ?></h3>
                        <p><?= e(t('footer_hours_value')) ?></p>
                    </div>
                </div>
            </div>

            <div class="contact-form reveal r2">
                <h2><?= e(t('contact_title')) ?></h2>
                <p class="sub"><?= e(t('contact_subtitle')) ?></p>

                <?php if ($success): ?>
                    <div class="flash success">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        <span><?= e(t('contact_sent')) ?></span>
                    </div>
                <?php elseif ($error): ?>
                    <div class="flash error">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/></svg>
                        <span><?= e($error) ?></span>
                    </div>
                <?php endif; ?>

                <form method="post" novalidate>
                    <input type="hidden" name="csrf" value="<?= e(vpy_csrf()) ?>">
                    <div class="fld-row">
                        <div class="field">
                            <label for="c-name"><?= e(t('contact_name')) ?></label>
                            <input id="c-name" name="name" type="text" required value="<?= e($vals['name']) ?>" placeholder="<?= e(t('auth_name_ph')) ?>">
                        </div>
                        <div class="field">
                            <label for="c-phone"><?= e(t('contact_phone')) ?></label>
                            <input id="c-phone" name="phone" type="tel" required value="<?= e($vals['phone']) ?>" placeholder="<?= e(t('auth_phone_ph')) ?>">
                        </div>
                    </div>
                    <div class="field">
                        <label for="c-subject"><?= e(t('contact_subject')) ?></label>
                        <input id="c-subject" name="subject" type="text" required value="<?= e($vals['subject']) ?>">
                    </div>
                    <div class="field">
                        <label for="c-message"><?= e(t('contact_message')) ?></label>
                        <textarea id="c-message" name="message" required><?= e($vals['message']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <?= e(t('contact_send')) ?>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="map-frame reveal">
            <div class="map-pin">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                <?= e(t('site_city')) ?>
                <span><?= e(t('footer_address_value')) ?></span>
            </div>
        </div>
    </div>
</section>
</main>

<?php vpy_public_footer(); ?>
