<?php
require_once('../include/cache_start.php');
require_once('../include/db_info.inc.php');
require_once('../include/my_func.inc.php');
require_once('../include/memcache.php');
require_once('../include/setlang.php');
require_once('../include/bbcode.php');

$user = isset($_POST['user']) ? $_POST['user'] : '';
$product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '';
$vis_type = isset($_POST['vis_type']) ? $_POST['vis_type'] : '';
echo'<div class="item_info_off" onclick="result_off()">X</div>';
if($vis_type == 0){
    $sql = "select * from product where product_id = $product_id";
    $result = pdo_query($sql);
    if($result[0][8]==1){
	if($result[0][5]>pdo_query("select coin from uinfo where user_id = '$user'")[0][0]){
	    echo '<div class ="result_content">보유한 코인이 부족합니다.</div>';
  	}
	else {
	    $new_coin = pdo_query("select coin from uinfo where user_id = '$user'")[0][0] - $result[0][5];
            pdo_query("update uinfo set coin ='$new_coin' where user_id = '$user'");
    	    $item_count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'");
	    $new_item_count = 0;
    	    if($item_count == NULL)pdo_query("insert into user_inventory(user_id,product_id,count) values('$user',$product_id,1)");
   	    else {
	    	$new_item_count = $item_count[0][0]+1;
	    	pdo_query("update user_inventory set count ='$new_item_count' where user_id = '$user' and product_id=$product_id");
  	    }
	    echo "<script>coin_db();</script>";
	    $item_count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0];
	    echo '<div class= "remain_count">보유 수량 : '.$item_count.'</div>';
   	    if(pdo_query("select coin from uinfo where user_id = '$user'")[0][0]>=$result[0][5])echo '<div class="item_use_onemore" onclick="shop_ajax('.$product_id.',0)">한번 더 구매하기</div>';    
    	    echo '<div class ="result_content">아이템이 구매되었습니다.</div>';
	    $item_price = $result[0][5];
	    $log = "insert into content_log values('$user',2,'$user 가 코인 $item_price 소모[상점]','$user 의 코인 $new_coin ',now())";
	    pdo_query($log);
	    $log = "insert into content_log values('$user',4,'$user 가 아이템 $product_id 1개 구매[상점]','$user 가 $product_id 을 $item_count 개 보유',now())";
    	    pdo_query($log);
	}
    }
    else{
	echo '<div class ="result_content">아이템 구매에 실패하였습니다.</div>';
    }
}
else if($vis_type == 1 || $vis_type == 2){
    $sql = "select * from user_product_visibility where user_id =  '$user' and product_id = '$product_id' and vis_type = '$vis_type'";
    $result = pdo_query($sql);
    if($result==null || $result[0][3]<1){
    	echo '<div class ="result_content">아이템 구매에 실패하였습니다.</div>';
    }else if($result[0][5] > pdo_query("select coin from uinfo where user_id = '$user'")[0][0]){
    	echo '<div class ="result_content">보유한 코인이 부족합니다.</div>';
    }else {
    	if($vis_type==2){
    	    $remain_count = pdo_query("select remain_count from user_product_visibility where user_id = '$user' and product_id = '$product_id' and vis_type = '$vis_type'")[0][0]-1;
    	    if($remain_count == 0) pdo_query("delete from user_product_visibility where user_id = '$user' and product_id = '$product_id' and vis_type = '$vis_type'");
    	    else pdo_query("update user_product_visibility set remain_count ='$remain_count' where user_id ='$user' and product_id = '$product_id' and vis_type = '$vis_type'");
    	}
    	$new_coin = pdo_query("select coin from uinfo where user_id = '$user'")[0][0] - $result[0][5];
   	pdo_query("update uinfo set coin ='$new_coin' where user_id = '$user'");
	if($product_id > 0){
    	    $item_count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'");
    	    $shop_count = $result[0][4];
	    $new_item_count = 0;
    	    if($item_count == NULL)pdo_query("insert into user_inventory(user_id,product_id,count) values('$user',$product_id,$shop_count)");
    	    else {
	    	$new_item_count = $item_count[0][0]+$result[0][4];
	    	pdo_query("update user_inventory set count ='$new_item_count' where user_id = '$user' and product_id=$product_id");
            }
    	    $remain_count = pdo_query("select remain_count from user_product_visibility where user_id = '$user' and product_id = '$product_id' and vis_type = '$vis_type'")[0][0];
    	    if($remain_count == NULL)$remain_count = 0;
	    $item_count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0];
	    echo '<div class= "remain_count" style="bottom:22%">보유 수량 : '.$item_count.'</div>';
    	    echo '<div class= "remain_count">남은 수량 : '.$remain_count.'</div>';
    	    if($remain_count>0)echo '<div class="item_use_onemore" onclick="shop_ajax('.$product_id.','.$vis_type.')">한번 더 구매하기</div>';    
    	    $item_price = $result[0][5];
	    $log = "insert into content_log values('$user',2,'$user 가 코인 $item_price 소모[상점]','$user 의 코인 $new_coin ',now())";
	    pdo_query($log);
	    $log = "insert into content_log values('$user',4,'$user 가 아이템 $product_id 1개 구매[상점]','$user 가 $product_id 을 $item_count 개 보유',now())";
    	    pdo_query($log);
	}
	else if($product_id <0){
	    $border_id = $product_id * -1;
	    pdo_query("insert into user_border values('$user',$border_id)");
	    $item_price = $result[0][5];
	    $log = "insert into content_log values('$user',2,'$user 가 코인 $item_price 소모[상점]','$user 의 코인 $new_coin ',now())";
	    pdo_query($log);
	    $log = "insert into content_log values('$user',4,'$user 가 테두리 $border_id 1개 구매[상점]','.',now())";
    	    pdo_query($log);
   	}
	echo "<script>shop_db();</script>";
	echo "<script>setTimeout(time_reload, 15)</script>";
	echo "<script>coin_db();</script>";
	echo '<div class ="result_content">아이템이 구매되었습니다.</div>';
    }
}
else if($vis_type == 3){
    if(pdo_query("select coin from uinfo where user_id = '$user'")[0][0] < 2000){
	echo '<div class ="result_content">보유한 코인이 부족합니다.</div>';
    }
    else if(pdo_query("select count(*) from user_border where user_id = '$user' and border_id = $product_id")[0][0]>0){
	echo '<div class ="result_content">이미 보유한 테두리 입니다.</div>';
    }else{
	$new_coin = pdo_query("select coin from uinfo where user_id = '$user'")[0][0];
        $new_coin-=2000;
	pdo_query("update uinfo set coin = '$new_coin' where user_id = '$user'");
	pdo_query("insert into user_border values('$user','$product_id')");
	echo '<div class ="result_content">아이템이 구매되었습니다.</div>';
	echo "<script>shop_db();</script>";
	echo "<script>setTimeout(time_reload, 5)</script>";
	echo "<script>setTimeout(avatar_update, 5)</script>";
	echo "<script>coin_db();</script>";
	$log = "insert into content_log values('$user',2,'$user 가 코인 2000 소모[상점]','$user 의 코인 $new_coin ',now())";
	pdo_query($log);
	$log = "insert into content_log values('$user',4,'$user 가 테두리 $product_id 1개 구매[상점]','.',now())";
    	pdo_query($log);

    }

}
?>
<script>
item_off(on_info);
var result = document.getElementById("result");
result.classList.remove("hidden");
</script>
