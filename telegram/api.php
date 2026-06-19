<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

class VpyTelegramApi {
    public string $token;
    public string $api;

    public function __construct(?string $token = null) {
        $this->token = $token ?: VPY_TELEGRAM_BOT_TOKEN ?: vpy_setting('telegram_bot_token', '');
        $this->api = 'https://api.telegram.org/bot' . $this->token . '/';
    }

    public function call(string $method, array $params = []) {
        if (!$this->token) return ['ok' => false, 'error' => 'no_token'];
        $url = $this->api . $method;
        $ctx = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($params),
                'timeout' => 8,
                'ignore_errors' => true,
            ]
        ]);
        $resp = @file_get_contents($url, false, $ctx);
        return $resp === false ? ['ok' => false] : (json_decode($resp, true) ?: ['ok' => false]);
    }

    public function send(int|string $chat_id, string $text, array $extras = []) {
        return $this->call('sendMessage', array_merge([
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => true,
        ], $extras));
    }

    public function setWebhook(string $url) {
        return $this->call('setWebhook', ['url' => $url]);
    }

    public function deleteWebhook() {
        return $this->call('deleteWebhook');
    }

    public function inlineKeyboard(array $rows) {
        return ['reply_markup' => json_encode(['inline_keyboard' => $rows])];
    }
}



/* ============================================================
 * BOT STATE MACHINE
 * ============================================================ */

function vpy_bot_state_get($chat_id) {
    foreach (vpy_read_json('bot_states', []) as $s) {
        if ((string)$s['chat_id'] === (string)$chat_id) {
            if (strtotime($s['updated_at'] ?? '') < time() - 3600) return null;
            return $s;
        }
    }
    return null;
}

function vpy_bot_state_set($chat_id, $state, $context = []) {
    $rows = vpy_read_json('bot_states', []);
    $rows = array_values(array_filter($rows, fn($s) => (string)$s['chat_id'] !== (string)$chat_id && strtotime($s['updated_at'] ?? '') > time() - 86400));
    $rows[] = [
        'chat_id' => (string)$chat_id,
        'state' => $state,
        'context' => $context,
        'updated_at' => date('Y-m-d H:i:s'),
    ];
    if (count($rows) > 1000) $rows = array_slice($rows, -1000);
    vpy_write_json('bot_states', $rows);
}

function vpy_bot_state_clear($chat_id) {
    $rows = vpy_read_json('bot_states', []);
    $rows = array_values(array_filter($rows, fn($s) => (string)$s['chat_id'] !== (string)$chat_id));
    vpy_write_json('bot_states', $rows);
}

function vpy_bot_download_photo($file_id, $token, $subfolder = 'payments') {
    if (!$token || !$file_id) return null;
    $url = 'https://api.telegram.org/bot' . $token . '/getFile?file_id=' . urlencode($file_id);
    $resp = @file_get_contents($url);
    if (!$resp) return null;
    $j = json_decode($resp, true);
    if (empty($j['ok']) || empty($j['result']['file_path'])) return null;
    $file_path = $j['result']['file_path'];
    $download_url = 'https://api.telegram.org/file/bot' . $token . '/' . $file_path;
    $data = @file_get_contents($download_url);
    if (!$data) return null;
    if (strlen($data) > 5 * 1024 * 1024) return null;
    $folder = VPY_UPLOADS . '/' . preg_replace('/[^a-z0-9_\-]/i', '', $subfolder);
    if (!is_dir($folder)) @mkdir($folder, 0775, true);
    $ext = pathinfo($file_path, PATHINFO_EXTENSION) ?: 'jpg';
    $ext = preg_replace('/[^a-z0-9]/i', '', $ext) ?: 'jpg';
    $filename = 'tg_' . bin2hex(random_bytes(8)) . '_' . date('Ymd_His') . '.' . $ext;
    $target = $folder . '/' . $filename;
    if (file_put_contents($target, $data) === false) return null;
    @chmod($target, 0644);
    return '/assets/uploads/' . $subfolder . '/' . $filename;
}
