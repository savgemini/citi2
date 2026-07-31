<?php
function send_telegram_message($settings, $message) {
    if (empty($settings['telegram']) || $settings['telegram'] != '1') {
        return false;
    }

    $chatId = trim((string) ($settings['chat_id'] ?? ''));
    $botUrl = trim((string) ($settings['bot_url'] ?? ''));
    if ($chatId === '' || $botUrl === '') {
        return false;
    }

    $botToken = preg_replace('#^https?://api\.telegram\.org/+#i', '', $botUrl);
    $botToken = preg_replace('#^bot#i', '', $botToken);
    $botToken = trim($botToken, '/');

    if ($botToken === '' || strpos($botToken, ':') === false) {
        return false;
    }

    $apiUrl = 'https://api.telegram.org/bot' . $botToken . '/sendMessage';
    $payload = http_build_query(array(
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true,
    ));

    if (function_exists('curl_init')) {
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($ch);
        curl_close($ch);
    } else {
        @file_get_contents($apiUrl . '?' . $payload);
    }

    return true;
}
