<?php
require_once('../include/cache_start.php');
require_once('../include/db_info.inc.php');
require_once('../include/my_func.inc.php');
require_once('../include/memcache.php');
require_once('../include/setlang.php');
require_once('../include/bbcode.php');

$user = isset($_POST['user']) ? $_POST['user'] : '';
$coin = pdo_query("select coin from uinfo where user_id = '$user'")[0][0];
echo '<img src="/image/algo_coin.png" class="user_coin_img">'.$coin.'';

?>