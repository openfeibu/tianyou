<?php
// database host
if(is_file($_SERVER['DOCUMENT_ROOT'].'/360safe/360webscan.php')){
    require_once($_SERVER['DOCUMENT_ROOT'].'/360safe/360webscan.php');
}
$db_host   = "39.108.144.9";

$db_port   = '3306';
// database name
$db_name   = "tianyou";

// database username
$db_user   = "feibukeji";

// database password

$db_pass   = 'ladfjeuwf71741s';

// table prefix
$prefix    = "ty_";

$timezone    = "Asia/Chongqing";

$cookie_path    = "/";

$cookie_domain    = "";

$session = "1440";

define('EC_CHARSET','utf-8');

define('ADMIN_PATH','admin');

define('AUTH_KEY', 'this is a key');

define('OLD_AUTH_KEY', '');

define('API_TIME', '2017-05-26 21:32:04');

define('STORE_KEY','95a3796f7c5c9c49542ce5eaff7066eb');

?>
