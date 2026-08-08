
<?php
$cache_time = 30;
$OJ_CACHE_SHARE = false;
require_once('../include/cache_start.php');
require_once('../include/db_info.inc.php');
require_once('../include/my_func.inc.php');
require_once('../include/memcache.php');
require_once('../include/setlang.php');
require_once('../include/bbcode.php');

// 정보들 가져오기
$user = isset($_POST['user']) ? $_POST['user'] : '';
$sql = "select acc_exp,coin from uinfo where user_id = '$user'";
$uinfo_db=pdo_query($sql);
$exp_point = $uinfo_db[0][0];
$coin = $uinfo_db[0][1];
$l = 1;
$r = 30000;
$idx = 0;
while($l <= $r)
{
$mid = intval(($l + $r) / 2);
if($exp_total[$mid] > $exp_point) $r = $mid - 1;
else{
    $idx = $mid;
    $l = $mid + 1;
    }
}
$need_exp = $exp_point - $exp_total[$idx];
$exp_percent = $need_exp / $exp_a[$idx+1] *100;
$lv = $idx+1;


echo '<div class="header" style="margin-top:7px;font-size:1.2rem;font-weight:600;">'.$lv.' LV</div>		     
		</div>
		<div id="progressContainer"
     			onmouseover="showTooltip('.$need_exp.', '.$exp_a[$lv].')"
     			onmouseout="hideTooltip()" style="z-index:1;">
  		    <div id="progressBar" style="width:'.$exp_percent.'%;"></div>
  		    <div id="progressText">'.number_format($exp_percent,1).'%</div>
		    <div id="tooltip"></div>		    		    
		</div>		
		<div id="coin" style="font-weight:600;font-size:1.1rem;color:#FCD425;margin-top:20px;">
		<img class ="coin" src="/image/algo_coin.png" style="width:30px;height:auto;margin-right:3px;">'.$coin.'
		</div>';

?>
<script>
</script>
