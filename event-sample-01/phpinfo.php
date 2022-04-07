<?php
// PHP Version 8.1

ini_set( 'display_errors', 1 );

define('__ROOT__', dirname(__FILE__));
require_once(__ROOT__.'/logging.php');

logInfo('ログ出力確認1');
logError('ログ出力確認2');
echo "サーバのIPアドレス:[{$_SERVER["SERVER_ADDR"]}]<br>";
session_start();
if (isset($_SESSION['session_id_16']) && !empty($_SESSION['session_id_16'])) {
    echo "Loaded session! : ";
}
else {
    echo "New session! : ";
}
$_SESSION['session_id_16'] = substr(session_id(), 0, 16);
echo $_SESSION['session_id_16'];
phpinfo();