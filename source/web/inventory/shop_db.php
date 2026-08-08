<?php
require_once('../include/cache_start.php');
require_once('../include/db_info.inc.php');
require_once('../include/my_func.inc.php');
require_once('../include/memcache.php');
require_once('../include/setlang.php');
require_once('../include/bbcode.php');
$user = isset($_POST['user']) ? $_POST['user'] : '';
$git = pdo_query("select git_link from user_link where user_id = '$user'")[0][0];
$i = 0;
$hun=100;
    $sql = "select u.* from user_product_visibility u, product p where u.user_id = '$user' and u.vis_type = 2 and u.product_id = p.product_id and u.product_id != 1 order by p.rarity, u.sale_price ";
    $result = pdo_query($sql);
    $sql = "select * from user_product_visibility where user_id = '$user' and vis_type=2 and product_id < 0";
    $border = pdo_query($sql);
    if($result != NULL || $border != NULL)echo '<div class="sale_shop"><div class="shop_kind">특가 상품
	    <div class="left_time" id="sale_time">🕒 Loading...</div>
	</div>
	<div class="shop_flex">';
    foreach($result as $row){
        $product_id=$row["product_id"];
	$product=pdo_query("select * from product where product_id = '$product_id'");	
	echo '
	<div class="item_box" style="'.$background[$product[0][7]].'" onclick="item_info('.$i.')">
	    <div class="item_sale"><div style="margin-top:20px;"></div>'.$hun - number_format($row["sale_price"]/$product[0][5]*100,0).'%<br><c style="font-size:12px;line-height:12px;">OFF</c></div>
	    <div style="max-height:20px;">'.$product[0][1].'</div>
	    <div class="item_left">남은 수량 : '.$row["remain_count"].'</div>
	    <img src= "'.$product[0][3].'" class="item_img">
	    X'.$row["count"].'
	    <div class="item_price"><img src="/image/algo_coin.png" class="coin_img">'.$row["sale_price"].'</div>
	</div>
	<div class="item_info hidden" id="item_info'.$i.'">
	    <div class="item_title">'.$product[0][1].'</div>
	    <div class="item_info_off" onclick="item_off('.$i.')">X</div>
	    <div class="item_content">'.$product[0][6].'</div>
	    <div class="item_info_coin"><img src="/image/algo_coin.png" class="info_coin_img">'.$row["sale_price"].'</div>
	    <div class="item_info_count">남은 개수 : '.$row["remain_count"].'</div>
	    <div class="item_cancel" onclick="item_off('.$i.')">취소한다</div>
	    <div class="item_buy" onclick="shop_ajax('.$product_id.','.$row["vis_type"].')">구매한다</div>
        </div>
	';
    $i++;
    }
    foreach($border as $row){
	$border_id = $row["product_id"]*-1;
	echo '
	<div class="item_box" style="'.$background[5].'" onclick="item_info('.$i.')">
	    <div class="item_sale"><div style="margin-top:20px;"></div>'.$hun - number_format($row["sale_price"]/2000*100,0).'%<br><c style="font-size:12px;line-height:12px;">OFF</c></div>
	    <div style="max-height:20px;">프로필 테두리</div>
	    <div class="border_div" style="background-image: url(\'/image/border/profile_border'.$border_id.'.gif\');position:relative;margin-top:80px;margin-bottom:44px;">
		    <div class="border_hide"></div>
	            <img id="border_github" class="border_github">
	    </div>
	    X'.$row["count"].'
	    <div class="item_price" style="margin-top:16px;"><img src="/image/algo_coin.png" class="coin_img">'.$row["sale_price"].'</div>
	</div>
	<div class="item_info hidden" id="item_info'.$i.'">
	    <div class="item_title">프로필 테두리</div>
	    <div class="item_info_off" onclick="item_off('.$i.')">X</div>
	    <div class="border_div" style="background-image: url(\'/image/border/profile_border'.$border_id.'.gif\');position:relative;margin-top:30px;">
		    <div class="border_hide"></div>
	            <img id="border_github" class="border_github">
	    </div>
	    <div class="item_info_coin"><img src="/image/algo_coin.png" class="info_coin_img">'.$row["sale_price"].'</div>
	    <div class="item_info_count">남은 개수 : '.$row["remain_count"].'</div>
	    <div class="item_cancel" onclick="item_off('.$i.')">취소한다</div>
	    <div class="item_buy" onclick="shop_ajax('.$row["product_id"].','.$row["vis_type"].')">구매한다</div>
        </div>
	';
    $i++;
    }

    if($result != NULL || $border != NULL)echo '</div></div></div>';

    $sql = "select u.* from user_product_visibility u, product p where u.user_id = '$user' and u.vis_type = 1 and u.product_id = p.product_id and u.product_id != 1 order by p.rarity, u.sale_price ";
    $result = pdo_query($sql);

    if($result!=NULL)echo '<div class="sale_shop"><div class="shop_kind">데일리 상품
	    <div class="left_time" id="daily_time">🕒 Loading...</div>
	</div>
	<div class="shop_flex">';
    foreach($result as $row){
        $product_id=$row["product_id"];
	$product=pdo_query("select * from product where product_id = '$product_id'");
	echo '
	<div class="item_box" style="'.$background[$product[0][7]].'" onclick="item_info('.$i.')">
	    <div>'.$product[0][1].'</div>
	    <div class="item_left">남은 수량 :&nbsp;<img src="/image/infinity.png" style="height:12px;width:auto;margin-bottom:4px;"></div>
	    <img src= "'.$product[0][3].'" class="item_img">
	    X'.$row["count"].'
	    <div class="item_price"><img src="/image/algo_coin.png" class="coin_img">'.$row["sale_price"].'</div>
	</div>
	<div class="item_info hidden" id="item_info'.$i.'">
	    <div class="item_title">'.$product[0][1].'</div>
	    <div class="item_info_off" onclick="item_off('.$i.')">X</div>
	    <div class="item_content">'.$product[0][6].'</div>
	    <div class="item_info_coin" style="bottom:17%;"><img src="/image/algo_coin.png" class="info_coin_img">'.$row["sale_price"].'</div>
	    <div class="item_cancel" onclick="item_off('.$i.')">취소한다</div>
	    <div class="item_buy" onclick="shop_ajax('.$product_id.','.$row["vis_type"].')">구매한다</div>
        </div>
	';
    $i++;
    }
    if($result!=NULL)echo '</div></div></div>';


    
echo '<div class="always_shop">
    <div class="shop_kind">상시 상품</div>
    <div class="shop_flex">';
	
    $sql = "select * from product where product_type = 1 order by rarity,product_id";
    $result = pdo_query($sql);

    foreach($result as $row){
	echo '
	<div class="item_box" style="'.$background[$row["rarity"]].'" onclick="item_info('.$i.')">
	    <div>'.$row["product_name"].'</div>
	    <div class="item_left">남은 수량 :&nbsp;<img src="/image/infinity.png" style="height:12px;width:auto;margin-bottom:4px;"></div>
	    <img src= "'.$row["product_image_path"].'" class="item_img">
	    X1
	    <div class="item_price"><img src="/image/algo_coin.png" class="coin_img">'.$row["price"].'</div>
	</div>
	<div class="item_info hidden" id="item_info'.$i.'">
	    <div class="item_title">'.$row["product_name"].'</div>
	    <div class="item_info_off" onclick="item_off('.$i.')">X</div>
	    <div class="item_content">'.$row["content"].'</div>
	    <div class="item_info_coin" style="bottom:17%;"><img src="/image/algo_coin.png" class="info_coin_img">'.$row["price"].'</div>
	    <div class="item_cancel" onclick="item_off('.$i.')">취소한다</div>
	    <div class="item_buy" onclick="shop_ajax('.$row["product_id"].',0)">구매한다</div>
        </div>
	';
    $i++;
    }
    echo '</div>
</div>	
';
$max_border = 36;
if(pdo_query("select count(*) from user_border where user_id ='$user'")[0][0]!=$max_border){
    echo '<div class="always_shop">
    	<div class="shop_kind">프로필 테두리</div>
    	<div class="border_flex">';
	
        for($j=17;$j<=$max_border;$j++){
	    $sql = "select count(*) from user_border where user_id ='$user' and border_id =$j";
	    if(pdo_query($sql)[0][0]>0)continue;
	    echo '
	    <div class="border_box" onclick="item_info('.$i.')">
	        <div class="border_div" style="background-image: url(\'/image/border/profile_border'.$j.'.gif\');">
		    <div class="border_hide"></div>
	            <img id="border_github" class="border_github">	
	    	</div>
	        <div class="border_price"><img src="/image/algo_coin.png" class="coin_img">2000</div>
	    </div>
	    <div class="item_info hidden" id="item_info'.$i.'">
	    	<div class="item_title">프로필 테두리</div>
	    	<div class="item_info_off" onclick="item_off('.$i.')">X</div>
		<div class="border_div" style="background-image: url(\'/image/border/profile_border'.$j.'.gif\');margin-top:35px;">
		    <div class="border_hide"></div>
	            <img id="border_github" class="border_github">	
	    	</div>
	    	<div class="item_info_coin" style="bottom:17%;font-size:18px;"><img src="/image/algo_coin.png" class="info_coin_img">2000</div>
	    	<div class="item_cancel" onclick="item_off('.$i.')">취소한다</div>
	    	<div class="item_buy" onclick="shop_ajax('.$j.',3)">구매한다</div>
            </div>
	    ';	
	    $i++;
    	}
	

	echo '</div>
    </div>';
}
?>
<script>
var username = '<?php echo $git;?>';
var githubImgs = document.querySelectorAll('#border_github');
githubImgs.forEach(img => {
    fetch(`https://api.github.com/users/${username}`, {
        headers: {
            'Authorization': `token ${caslcqakl.asdjlqqlk}`
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('GitHub 사용자를 찾을 수 없습니다.');
        }
        return response.json();
    })
    .then(data => {
        img.src = data.avatar_url;
    })
    .catch(error => {
        img.src = '/image/mainicon.png';     
    });
});
</script>
