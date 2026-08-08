<?php
require_once('../include/cache_start.php');
require_once('../include/db_info.inc.php');
require_once('../include/my_func.inc.php');
require_once('../include/memcache.php');
require_once('../include/setlang.php');
require_once('../include/bbcode.php');
$kind = isset($_POST['kind']) ? $_POST['kind'] : '';
$current_time = strtotime(pdo_query("SELECT NOW()")[0][0]);
if($kind == 1){
    $reset_interval = 24 * 3600;
    $next_reset = strtotime('midnight', $current_time) + ceil(($current_time - strtotime('midnight', $current_time)) / $reset_interval) * $reset_interval;
    $time_left = $next_reset - $current_time;
    $hours_left = floor($time_left / 3600);
    $minutes_left = floor(($time_left % 3600) / 60);
    $seconds_left = $time_left % 60;
    if($hours_left==23 && $minutes_left == 59 && $seconds_left>54)echo '<script>shop_db();</script>';
    echo '🕒 ' . $hours_left . 'h ' . $minutes_left . 'min';
}
if($kind == 2){ 
    $reset_interval = 72 * 3600; 
    $reset_time = strtotime('2024-01-02 midnight');
    $next_reset = $reset_time + ceil(($current_time - $reset_time) / $reset_interval) * $reset_interval;
    $time_left = $next_reset - $current_time;
    $hours_left = floor($time_left / 3600);
    $minutes_left = floor(($time_left % 3600) / 60);
    echo '🕒 ' . $hours_left . 'h ' . $minutes_left . 'min';
}
?>