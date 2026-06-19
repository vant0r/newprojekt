<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/notifications.php';
require_once __DIR__ . '/api.php';

$raw = file_get_contents('php://input');
if (!$raw) { http_response_code(200); echo 'ok'; exit; }
$update = json_decode($raw, true) ?: [];
vpy_log('telegram_update', 'Webhook update', ['update_id' => $update['update_id'] ?? null, 'has_message' => isset($update['message']), 'has_callback' => isset($update['callback_query'])]);

$bot = new VpyTelegramApi();
$token = $bot->token;

/* ============================================================
 * MAIN MENU + KEYBOARD
 * ============================================================ */
function vpy_bot_main_menu($bot, $chat_id, $name = '') {
    $msg = "🚦 <b>VatanParvar Yaypan</b>\n\n";
    $msg .= "Yaypan avtomaktabi YHQ imtihoniga onlayn tayyorgarlik platformasi.\n\n";
    $msg .= "Quyidagi tugmalardan birini tanlang:";
    $bot->send($chat_id, $msg, $bot->inlineKeyboard([
        [['text' => '🌐 Saytni ochish', 'url' => 'https://' . VPY_DOMAIN]],
        [['text' => '💰 To\'lov qilish', 'callback_data' => 'menu_pay']],
        [
            ['text' => '📋 Tariflar', 'url' => 'https://' . VPY_DOMAIN . '/tariflar.php'],
            ['text' => '📚 Testlar', 'url' => 'https://' . VPY_DOMAIN . '/register.php'],
        ],
        [['text' => 'ℹ️ Yordam', 'callback_data' => 'menu_help']],
    ]));
}

function vpy_bot_payment_menu($bot, $chat_id) {
    $msg = "💰 <b>To'lov bo'limi</b>\n\nNima qilmoqchisiz?";
    $bot->send($chat_id, $msg, $bot->inlineKeyboard([
        [['text' => '🆕 Yangi to\'lov', 'callback_data' => 'pay_new']],
        [['text' => '🔢 Mavjud to\'lov ID', 'callback_data' => 'pay_existing']],
        [['text' => '◀️ Orqaga', 'callback_data' => 'menu_main']],
    ]));
}

function vpy_bot_tariff_list($bot, $chat_id) {
    $tariffs = vpy_filter('tariflar', fn($t) => !empty($t['active']));
    usort($tariffs, fn($a, $b) => ($a['sort'] ?? 0) <=> ($b['sort'] ?? 0));
    $msg = "🆕 <b>Yangi to'lov</b>\n\nTarifni tanlang:\n\n";
    $kb = [];
    foreach ($tariffs as $t) {
        $msg .= "🎯 <b>" . htmlspecialchars($t['name']) . "</b> — " . number_format((float)$t['price'], 0, '.', ' ') . " so'm / " . htmlspecialchars($t['period_label']) . "\n";
        $kb[] = [['text' => $t['name'] . ' · ' . number_format((float)$t['price'], 0, '.', ' '), 'callback_data' => 'pay_tariff_' . (int)$t['id']]];
    }
    $kb[] = [['text' => '◀️ Orqaga', 'callback_data' => 'menu_pay']];
    $bot->send($chat_id, $msg, $bot->inlineKeyboard($kb));
}

function vpy_bot_payment_details($bot, $chat_id, $payment) {
    $card_number = vpy_setting('payment_card_number', '8600 0000 0000 0000');
    $card_holder = vpy_setting('payment_card_holder', 'VATANPARVAR YAYPAN');
    $card_bank = vpy_setting('payment_card_bank', 'Hamkorbank');

    $msg = "✅ <b>To'lov yaratildi</b>\n\n";
    $msg .= "🎯 Tarif: <b>" . htmlspecialchars($payment['tariff_name']) . "</b>\n";
    $msg .= "💵 Summa: <b>" . number_format((float)$payment['amount'], 0, '.', ' ') . " so'm</b>\n";
    $msg .= "🔢 To'lov ID: <code>" . htmlspecialchars($payment['invoice_number']) . "</code>\n\n";
    $msg .= "💳 <b>Karta ma'lumotlari:</b>\n";
    $msg .= "Raqam: <code>" . htmlspecialchars($card_number) . "</code>\n";
    $msg .= "Egasi: <b>" . htmlspecialchars($card_holder) . "</b>\n";
    $msg .= "Bank: " . htmlspecialchars($card_bank) . "\n\n";
    $msg .= "📸 Endi to'lov chekining <b>screenshot</b>ini menga rasm sifatida yuboring (JPG/PNG, 5 MB gacha).";

    $bot->send($chat_id, $msg, $bot->inlineKeyboard([
        [['text' => '❌ Bekor qilish', 'callback_data' => 'pay_cancel']],
    ]));
}

/* ============================================================
 * MESSAGE HANDLER
 * ============================================================ */
if (isset($update['message'])) {
    $msg = $update['message'];
    $chat_id = $msg['chat']['id'];
    $text = trim($msg['text'] ?? '');
    $name = $msg['from']['first_name'] ?? 'Foydalanuvchi';
    $tg_user_id = $msg['from']['id'] ?? null;
    $state = vpy_bot_state_get($chat_id);

    /* PHOTO yuborildi — screenshot upload */
    if (isset($msg['photo']) && is_array($msg['photo'])) {
        if ($state && ($state['state'] ?? '') === 'awaiting_screenshot' && !empty($state['context']['payment_id'])) {
            $payment_id = (int)$state['context']['payment_id'];
            $payment = vpy_find('tolovlar', 'id', $payment_id);
            if (!$payment) {
                $bot->send($chat_id, "❌ To'lov topilmadi. Iltimos, /start dan qayta boshlang.");
                vpy_bot_state_clear($chat_id);
                http_response_code(200); echo 'ok'; exit;
            }
            $photos = $msg['photo'];
            usort($photos, fn($a, $b) => ($b['file_size'] ?? 0) <=> ($a['file_size'] ?? 0));
            $largest = $photos[0];
            $path = vpy_bot_download_photo($largest['file_id'], $token, 'payments');
            if (!$path) {
                $bot->send($chat_id, "❌ Faylni yuklab bo'lmadi. Qayta urinib ko'ring.");
                http_response_code(200); echo 'ok'; exit;
            }
            $payment['screenshot'] = $path;
            $payment['screenshot_uploaded_at'] = date('Y-m-d H:i:s');
            $payment['telegram_chat_id'] = $chat_id;
            $payment['telegram_user_id'] = $tg_user_id;
            if (($payment['status'] ?? '') !== 'success') $payment['status'] = 'pending';
            vpy_upsert('tolovlar', $payment);
            vpy_bot_state_clear($chat_id);
            vpy_log('telegram_screenshot', 'Bot orqali screenshot', ['payment_id' => $payment_id, 'chat_id' => $chat_id]);

            $bot->send($chat_id, "✅ <b>Screenshot qabul qilindi</b>\n\nTo'lov ID: <code>" . htmlspecialchars($payment['invoice_number']) . "</code>\n\nAdmin tasdiqlashidan keyin (odatda 5-30 daqiqa) sizga yana xabar yuboriladi.", $bot->inlineKeyboard([[['text' => '🏠 Bosh menyu', 'callback_data' => 'menu_main']]]));

            $u = vpy_find('users', 'id', $payment['user_id']);
            $admin_msg = "📸 <b>Yangi to'lov screenshoti</b>\n\n";
            $admin_msg .= "💵 " . number_format((float)$payment['amount'], 0, '.', ' ') . " so'm · " . htmlspecialchars($payment['tariff_name']) . "\n";
            $admin_msg .= "🔢 " . htmlspecialchars($payment['invoice_number']) . "\n";
            $admin_msg .= "👤 " . htmlspecialchars($u['name'] ?? 'Mehmon') . " · " . htmlspecialchars($u['phone'] ?? '—') . "\n";
            $admin_msg .= "🆔 Chat: " . $chat_id;
            $admin_chat = vpy_setting('telegram_chat_id', VPY_TELEGRAM_CHAT_ID);
            if ($admin_chat) {
                $bot->send($admin_chat, $admin_msg, $bot->inlineKeyboard([
                    [['text' => '✅ Tasdiqlash', 'callback_data' => 'admin_approve_' . $payment_id], ['text' => '❌ Rad etish', 'callback_data' => 'admin_reject_' . $payment_id]],
                    [['text' => '🔍 Admin paneli', 'url' => 'https://' . VPY_DOMAIN . '/admin/tolovlar.php']],
                ]));
            }
            vpy_notify_admin('Yangi to\'lov screenshoti', "To'lov #" . $payment['invoice_number'] . " · " . $payment['tariff_name'] . " · " . number_format((float)$payment['amount'], 0, '.', ' ') . " so'm");

            http_response_code(200); echo 'ok'; exit;
        } else {
            $bot->send($chat_id, "📸 Rasm qabul qilindi, lekin hozir to'lov sessiyasi yo'q.\n\nTo'lov boshlash uchun /tolov yuboring.");
            http_response_code(200); echo 'ok'; exit;
        }
    }

    /* MATN — STATE asosida */
    if ($state && ($state['state'] ?? '') === 'awaiting_payment_id') {
        $needle = preg_replace('/[^A-Z0-9\-]/i', '', strtoupper($text));
        $payment = null;
        foreach (vpy_read_json('tolovlar', []) as $p) {
            if (preg_replace('/[^A-Z0-9\-]/i', '', strtoupper($p['invoice_number'] ?? '')) === $needle || (string)$p['id'] === $text) {
                $payment = $p;
                break;
            }
        }
        if ($payment) {
            vpy_bot_state_set($chat_id, 'awaiting_screenshot', ['payment_id' => $payment['id']]);
            $msg_out = "✅ To'lov topildi: <code>" . htmlspecialchars($payment['invoice_number']) . "</code>\n";
            $msg_out .= "💵 " . number_format((float)$payment['amount'], 0, '.', ' ') . " so'm · " . htmlspecialchars($payment['tariff_name']) . "\n";
            $msg_out .= "📊 Status: " . htmlspecialchars($payment['status']) . "\n\n";
            if (!empty($payment['screenshot'])) {
                $msg_out .= "⚠️ Bu to'lovga screenshot allaqachon yuklangan. Yangi screenshot yuborsangiz, eskisi almashtiriladi.\n\n";
            }
            $msg_out .= "📸 To'lov chekining <b>screenshot</b>ini rasm sifatida yuboring.";
            $bot->send($chat_id, $msg_out, $bot->inlineKeyboard([[['text' => '❌ Bekor qilish', 'callback_data' => 'pay_cancel']]]));
        } else {
            $bot->send($chat_id, "❌ To'lov ID topilmadi: <code>" . htmlspecialchars($text) . "</code>\n\nID format: <code>INV-2026-0001</code>\nQayta kiriting yoki /start ni bosing.");
        }
        http_response_code(200); echo 'ok'; exit;
    }

    /* Buyruqlar */
    if ($text === '/start' || $text === '🏠') {
        vpy_bot_state_clear($chat_id);
        vpy_bot_main_menu($bot, $chat_id, $name);
    } elseif ($text === '/tolov' || $text === '/pay' || stripos($text, "to'lov") === 0) {
        vpy_bot_state_clear($chat_id);
        vpy_bot_payment_menu($bot, $chat_id);
    } elseif ($text === '/help' || $text === '/yordam') {
        $bot->send($chat_id, "<b>Buyruqlar:</b>\n\n/start — bosh menyu\n/tolov — to'lov bo'limi\n/sayt — sayt havolasi\n/yordam — bu yordam\n\n<b>To'lov qanday?</b>\n1. /tolov yuboring\n2. \"Yangi to'lov\" → tarif tanlang\n3. Karta ma'lumotlari va ID olasiz\n4. To'lov qiling va screenshot yuboring");
    } elseif ($text === '/sayt') {
        $bot->send($chat_id, "🌐 https://" . VPY_DOMAIN);
    } else {
        $bot->send($chat_id, "Buyruq tushunilmadi. /start yuboring yoki menyudan tanlang.", $bot->inlineKeyboard([
            [['text' => '🏠 Bosh menyu', 'callback_data' => 'menu_main']],
            [['text' => '💰 To\'lov', 'callback_data' => 'menu_pay']],
        ]));
    }
}

/* ============================================================
 * CALLBACK QUERY HANDLER
 * ============================================================ */
if (isset($update['callback_query'])) {
    $cb = $update['callback_query'];
    $chat_id = $cb['message']['chat']['id'];
    $cb_id = $cb['id'];
    $data = $cb['data'] ?? '';
    $tg_user_id = $cb['from']['id'] ?? null;

    /* Acknowledge callback */
    $bot->call('answerCallbackQuery', ['callback_query_id' => $cb_id]);

    if ($data === 'menu_main') {
        vpy_bot_state_clear($chat_id);
        vpy_bot_main_menu($bot, $chat_id);
    } elseif ($data === 'menu_pay') {
        vpy_bot_state_clear($chat_id);
        vpy_bot_payment_menu($bot, $chat_id);
    } elseif ($data === 'menu_help') {
        $bot->send($chat_id, "ℹ️ <b>Yordam</b>\n\n· Saytda ro'yxatdan o'ting\n· Tarifni tanlang\n· Karta orqali to'lang yoki bot orqali screenshot yuboring\n· Admin tasdiqlashidan keyin tarif faollashadi\n\nAloqa: " . vpy_setting('contact_phone'));
    } elseif ($data === 'pay_new') {
        vpy_bot_tariff_list($bot, $chat_id);
    } elseif ($data === 'pay_existing') {
        vpy_bot_state_set($chat_id, 'awaiting_payment_id', []);
        $bot->send($chat_id, "🔢 <b>To'lov ID kiriting</b>\n\nFormat: <code>INV-2026-0001</code>\n\nID ni saytdan yoki bu botdan olishingiz mumkin edi.", $bot->inlineKeyboard([[['text' => '❌ Bekor qilish', 'callback_data' => 'pay_cancel']]]));
    } elseif (strpos($data, 'pay_tariff_') === 0) {
        $tariff_id = (int)substr($data, 11);
        $tariff = vpy_find('tariflar', 'id', $tariff_id);
        if (!$tariff) {
            $bot->send($chat_id, "❌ Tarif topilmadi");
            http_response_code(200); echo 'ok'; exit;
        }
        $tg_user = vpy_find('users', 'telegram_user_id', $tg_user_id);
        $user_id = $tg_user ? (int)$tg_user['id'] : 0;
        $payment = [
            'id' => vpy_id_next('tolovlar'),
            'user_id' => $user_id,
            'tariff_id' => (int)$tariff['id'],
            'tariff_name' => $tariff['name'],
            'amount' => (float)$tariff['price'],
            'method' => 'card',
            'status' => 'pending',
            'transaction_id' => '',
            'invoice_number' => 'INV-' . date('Y') . '-' . sprintf('%04d', vpy_id_next('tolovlar')),
            'expires_at' => null,
            'paid_at' => null,
            'screenshot' => '',
            'telegram_chat_id' => $chat_id,
            'telegram_user_id' => $tg_user_id,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        vpy_upsert('tolovlar', $payment);
        vpy_bot_state_set($chat_id, 'awaiting_screenshot', ['payment_id' => $payment['id']]);
        vpy_bot_payment_details($bot, $chat_id, $payment);
        vpy_log('telegram_payment_init', 'Bot orqali to\'lov boshlandi', ['payment_id' => $payment['id'], 'chat_id' => $chat_id]);
    } elseif ($data === 'pay_cancel') {
        vpy_bot_state_clear($chat_id);
        $bot->send($chat_id, "❌ Bekor qilindi. Qaytadan boshlash uchun /start yoki /tolov yuboring.");
    } elseif (strpos($data, 'admin_approve_') === 0 || strpos($data, 'admin_reject_') === 0) {
        $admin_chat = vpy_setting('telegram_chat_id', VPY_TELEGRAM_CHAT_ID);
        if ((string)$chat_id !== (string)$admin_chat) {
            $bot->send($chat_id, "❌ Bu amalga ruxsat yo'q");
            http_response_code(200); echo 'ok'; exit;
        }
        $is_approve = strpos($data, 'admin_approve_') === 0;
        $payment_id = (int)substr($data, $is_approve ? 14 : 13);
        $payment = vpy_find('tolovlar', 'id', $payment_id);
        if (!$payment) {
            $bot->send($chat_id, "❌ To'lov topilmadi");
            http_response_code(200); echo 'ok'; exit;
        }
        if ($is_approve) {
            $tariff = vpy_find('tariflar', 'id', $payment['tariff_id']);
            $payment['status'] = 'success';
            $payment['paid_at'] = date('Y-m-d H:i:s');
            $payment['expires_at'] = date('Y-m-d H:i:s', strtotime('+' . (int)($tariff['duration_days'] ?? 30) . ' days'));
            $payment['transaction_id'] = $payment['transaction_id'] ?: ('TG-APPROVED-' . strtoupper(vpy_random_string(6)));
            vpy_upsert('tolovlar', $payment);
            $bot->send($chat_id, "✅ To'lov tasdiqlandi: " . $payment['invoice_number']);
            if (!empty($payment['telegram_chat_id'])) {
                $bot->send($payment['telegram_chat_id'], "🎉 <b>To'lovingiz tasdiqlandi!</b>\n\n" . htmlspecialchars($payment['tariff_name']) . " tarifi faollashtirildi.\n" . htmlspecialchars($payment['expires_at']) . " gacha amal qiladi.", $bot->inlineKeyboard([[['text' => '🌐 Saytga kirish', 'url' => 'https://' . VPY_DOMAIN . '/login.php']]]));
            }
            if (!empty($payment['user_id'])) {
                vpy_notify_payment_success($payment['user_id'], $payment['tariff_name'], $payment['amount']);
            }
            vpy_log('telegram_admin_approve', 'Bot orqali tasdiqlandi', ['payment_id' => $payment_id]);
        } else {
            $payment['status'] = 'failed';
            vpy_upsert('tolovlar', $payment);
            $bot->send($chat_id, "❌ To'lov rad etildi: " . $payment['invoice_number']);
            if (!empty($payment['telegram_chat_id'])) {
                $bot->send($payment['telegram_chat_id'], "⚠️ <b>To'lovingiz rad etildi</b>\n\n" . htmlspecialchars($payment['invoice_number']) . "\n\nIltimos, admin bilan bog'laning: " . vpy_setting('contact_phone'));
            }
            vpy_log('telegram_admin_reject', 'Bot orqali rad etildi', ['payment_id' => $payment_id]);
        }
    }
}

http_response_code(200);
echo 'ok';
