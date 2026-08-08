
<?php
require_once('../include/cache_start.php');
require_once('../include/db_info.inc.php');
require_once('../include/my_func.inc.php');
require_once('../include/memcache.php');
require_once('../include/setlang.php');
require_once('../include/bbcode.php');

// 정보들 가져오기
$user = isset($_POST['user']) ? $_POST['user'] : '';
$quest_id = isset($_POST['quest_id']) ? $_POST['quest_id'] : '';

$sql = "select user_prog,quest_end_prog,quest_rec_rewards from progress where user_id = '$user' and quest_id = '$quest_id'";
$result = pdo_query($sql);
$user_prog = $result[0][0];
$quest_end_prog = $result[0][1];
$quest_rec_rewards = $result[0][2];

if($user_prog >= $quest_end_prog && $quest_rec_rewards == 0){
    $sql = "update progress set quest_rec_rewards = 1 where user_id = '$user' and quest_id = '$quest_id'";
    pdo_query($sql);
    $sql = "update progress set quest_sort_weight = -1 where user_id = '$user' and quest_id = '$quest_id'";
    pdo_query($sql);
    $log = "insert into content_log values('$user',9,'$user 가 퀘스트 $quest_id 보상 수령','.',now())";
    pdo_query($log);
    $sql = "update uinfo set quest_clear_count = quest_clear_count+1 where user_id = '$user'";
    pdo_query($sql);
    $sql = "select quest_comp_coin, quest_comp_exp,rewards_type,sub_reward from quests where quest_id ='$quest_id'";
    $result = pdo_query($sql);
    if($result[0][2]==0){//보상이 코인,exp 면
	$comp_coin = $result[0][0];
        $sql = "update uinfo set total_coin = total_coin + $comp_coin where user_id = '$user'";
	pdo_query($sql);
	$comp_exp = $result[0][1];	
	$sql = "select acc_exp,coin from uinfo where user_id = '$user'";
	$result = pdo_query($sql);
	$exp = $result[0][0] + $comp_exp;
	$coin = $result[0][1] + $comp_coin;
	if($comp_coin > 0){
	    $log = "insert into content_log values('$user',1,'$user 가 코인 $comp_coin 획득[퀘스트]','$user 의 코인 $coin',now())";
	    pdo_query($log);
	}
	if($comp_exp > 0){
	    $log = "insert into content_log values('$user',3,'$user 가 경험치 $comp_exp 획득[퀘스트]','$user 의 경험치 $exp',now())";
	    pdo_query($log);
	}
	$sql = "update uinfo set acc_exp = '$exp',coin = '$coin' where user_id = '$user'";
	pdo_query($sql);
    }
    else if($result[0][2]==1){//보상이 칭호면
	$title_id = $result[0][3];
	$log = "insert into content_log values('$user',7,'$user 가 칭호 $title_id 획득[퀘스트]','.',now())";
	pdo_query($log);
	$sql = "insert into user_title values('$user',$title_id)";
	pdo_query($sql);
    }
    else if($result[0][2]==2){//보상이 아이템 이면
	$item_id = $result[0][3];
	$item_count = $result[0][0];
	$sql = "select count(*) from user_inventory where user_id = '$user' and product_id = '$item_id'";
	$count = pdo_query($sql)[0][0];
	$log_count = $item_count + $count;
	$log = "insert into content_log values('$user',5,'$user 에게 아이템 $item_id 지급[퀘스트]','$user 가 $item_id 를 $log_count 개 보유',now())";
	pdo_query($log);
	if($count > 0) $sql = "update user_inventory set count = count + $item_count where user_id = '$user' and product_id = '$item_id'";
	else $sql = "insert into user_inventory(user_id, product_id, count) values('$user', $item_id, $item_count)";
	pdo_query($sql);
    }
    $sql = "select quest_class from quests where quest_id = $quest_id";
    $quest_class = pdo_query($sql)[0][0];
    if($quest_class == 1){
	progress_update($user, 92);
	$progress = pdo_query("select user_prog from progress where user_id = '$user' and quest_id = 92")[0][0];
	$log = "insert into content_log values('$user',8,'$user 의 퀘스트 92 진척도 변화','$user 의 92 번 진척도 $progress ',now())";
	pdo_query($log);	
    }
    if($quest_class==4)$quest_class=3;
    echo '<script>quest_tab_ajax('.$quest_class.')</script>';
}
?>
<script>
</script>
