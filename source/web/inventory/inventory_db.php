
<?php
require_once('../include/cache_start.php');
require_once('../include/db_info.inc.php');
require_once('../include/my_func.inc.php');
require_once('../include/memcache.php');
require_once('../include/setlang.php');
require_once('../include/bbcode.php');

$user = isset($_POST['user']) ? $_POST['user'] : '';
$i = 0;
$sql ="select u.* from user_inventory u, product p where u.product_id = p.product_id and u.user_id = '$user' order by rarity,p.product_id";
$result = pdo_query($sql);
foreach($result as $row){
    $product_id = $row["product_id"];
    $count = $row["count"];
    if($count == 0)continue;
    $sql = "select * from product where product_id = '$product_id'";
    $product = pdo_query($sql);
    echo '<script>document.addEventListener("click", function(event) {
        var item_info = document.getElementById("item"+'.$i.');
        if (!event.target.closest(".item_info") && !event.target.closest(".items")) {
            item_info.classList.add("hidden");
        }
    });
</script>';
    echo '<div class="items" onclick="item_info('.$i.');result_off()" style="'.$background_inventory[$product[0][7]].'">';
	echo '<img src="'.$product[0][3].'">';
	echo '<div class="item_count">'.$count.'</div>';
    echo '</div>';
    echo '<div class="item_info hidden" id="item'.$i.'">';
	echo '<div class="item_title">'.$product[0][1].'</div>';
	echo '<div class="item_content">'.$product[0][2].'</div>';
	if(!!$product[0][4]){
	    if($product_id==20 || $product_id==29 || $product_id==30 || $product_id==31 || $product_id==32 || $product_id==33 || $product_id==34 || $product_id==35){
		echo'<div style="display:flex;justify-content: center;align-items: center;font-size:18px;">문제 번호 : <input class="form-control" style="width:30%;margin-left:10px;"  id="item_input_'.$i.'" type="text"></div>';
	    }
	    else echo $product[0][4];
	}
	echo '<div class="item_info_off" onclick="item_off('.$i.')">X</div>';
	echo '<div class="item_cancel" onclick="item_off('.$i.')">취소한다</div>';
	echo '<div class="item_use" onclick="input_change('.$i.');item_use('.$product_id.');">사용한다</div>';
    echo '</div>';
$i++;
}
?>