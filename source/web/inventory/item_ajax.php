<?php
require_once('../include/cache_start.php');
require_once('../include/db_info.inc.php');
require_once('../include/my_func.inc.php');
require_once('../include/memcache.php');
require_once('../include/setlang.php');
require_once('../include/bbcode.php');

$user = isset($_POST['user']) ? $_POST['user'] : '';
$product_id = isset($_POST['index']) ? $_POST['index'] : '';
$input = isset($_POST['input']) ? $_POST['input'] : '';
if(pdo_query("select count from user_inventory where user_id = '$user' and product_id='$product_id'")[0][0]<=0)exit(0);
echo'<div class="item_info_off" onclick="result_off()">X</div>';

if($product_id==0){//저주인형
    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]+1;
    pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
    echo '<script>inventory_ajax();</script>';
    echo '<div class ="result_content">저 ㅋ 주</div>';
    echo '<div class="item_use_onemore" onclick="item_use('.$product_id.')">한번 더 사용하기</div>';
}

else if($product_id==6){//Random - Beginner 리롤권
    exec('/home/judge/src/web/inventory/script/dquest_beg_reload '.$user,$output,$returns);
    if($returns==0){
	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
	echo '<div class ="result_content">Random - Beginner 퀘스트가 초기화 되었습니다!</div>';
	echo '<div class="item_use_onemore" onclick="toquest()">퀘스트 바로가기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count',now())";
	pdo_query($log);	   
    }else{
	echo '<div class ="result_content">아이템을 사용할 수 없습니다.</div>';
    }
}

else if($product_id==7){//Random - Normal 리롤권
    exec('/home/judge/src/web/inventory/script/dquest_nor_reload '.$user,$output,$returns);
    if($returns==0){
	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
	echo '<div class ="result_content">Random - Normal 퀘스트가 초기화 되었습니다!</div>';
	echo '<div class="item_use_onemore" onclick="toquest()">퀘스트 바로가기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count',now())";
	pdo_query($log);
    }else{
	echo '<div class ="result_content">아이템을 사용할 수 없습니다.</div>';
    }
}
else if($product_id==8){//Random - Advanced 리롤권
    exec('/home/judge/src/web/inventory/script/dquest_adv_reload '.$user,$output,$returns);
    if($returns==0){
	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
	echo '<div class ="result_content">Random - Advanced 퀘스트가 초기화 되었습니다!</div>';
	echo '<div class="item_use_onemore" onclick="toquest()">퀘스트 바로가기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count',now())";
	pdo_query($log);
    }else{
	echo '<div class ="result_content">아이템을 사용할 수 없습니다.</div>';
    }
}

else if($product_id==9){//Random - Tag 퀘스트 리롤
    exec('/home/judge/src/web/inventory/script/dquest_tag_reload '.$user,$output,$returns);
    if($returns==0){
	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
	echo '<div class ="result_content">Random - Tag 퀘스트가 초기화 되었습니다!</div>';
	echo '<div class="item_use_onemore" onclick="toquest()">퀘스트 바로가기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count',now())";
	pdo_query($log);
    }else{
	echo '<div class ="result_content">아이템을 사용할 수 없습니다.</div>';
    }
}
else if($product_id==10){//일일퀘스트 리롤권
    exec('/home/judge/src/web/inventory/script/dquest_all_reload '.$user,$output,$returns);
    if($returns==0){
	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
	echo '<div class ="result_content">일일퀘스트가 초기화 되었습니다!</div>';
	echo '<div class="item_use_onemore" onclick="toquest()">퀘스트 바로가기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count',now())";
	pdo_query($log);
    }else{
	echo '<div class ="result_content">아이템을 사용할 수 없습니다.</div>';
    }
}
else if($product_id==11){//알고 복권
    if(pdo_query("select count(*) from lottery where user_id = '$user'")[0][0] ==0)$result= pdo_query("insert into lottery values('$user', 1)");
    else $result = pdo_query("update lottery set purchase_count = purchase_count + 1 where user_id = '$user'");
    if($result == NULL){
        echo '<div class ="result_content">아이템 사용에 실패하였습니다.</div>';
    }
    else {
    	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	$sum = pdo_query("select sum(purchase_count) from lottery")[0][0]*50;
	
	echo "<script>inventory_ajax();</script>";
        echo '<div class ="result_content">알고 복권이 사용되었습니다.<br>모인 금액 : <c style="color:#FCD425;">'.number_format($sum).'</c> <img class="coin" src="/image/algo_coin.png" style="width:auto;height:25px;margin-bottom:5px;"></div>';
	echo '<div class= "remain_count">남은 개수 : '.$count.'</div>';
	if(pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]>0)echo '<div class="item_use_onemore" onclick="item_use('.$product_id.')">한번 더 사용하기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count',now())";
	pdo_query($log);

    }
}

else if($product_id==12){//닉네임 컬러 변경권 1

    $sql = "select nick_color from users where user_id = '$user'";
    $prev_color = pdo_query($sql);
    $rand_idx = mt_rand(0, 6);

    while($color_arr[$rand_idx] == $prev_color[0][0])
        $rand_idx = mt_rand(0, 6);

    $sql = "update users set nick_color = '$color_arr[$rand_idx]' where user_id = '$user'";
    $result = pdo_query($sql);

    if($result == NULL)
    {
        echo '<div class ="result_content">아이템 사용에 실패하였습니다.</div>';
    }
    else
    {
    	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
        echo "<div class ='result_content'>닉네임 색깔이&nbsp;<c style='color:{$color_arr[$rand_idx]}'>$ko_color_arr[$rand_idx]</c> 으로 변경되었습니다!</div>";
	echo '<div class= "remain_count">남은 개수 : '.$count.'</div>';
   	if(pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]>0)echo '<div class="item_use_onemore" onclick="item_use('.$product_id.')">한번 더 사용하기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $ko_color_arr[$rand_idx] 당첨',now())";
	pdo_query($log);
    }
}
else if($product_id==13){//닉네임 컬러 변경권 2
    $sql = "select nick_color from users where user_id = '$user'";
    $prev_color = pdo_query($sql);
    $rand_idx = mt_rand(0, 99);
    if($rand_idx <80){
    $start_idx = 0;
    $end_idx = 6;
    }
    else{
    $start_idx=7;
    $end_idx=12;
    echo '<script>firework();</script>';
    }
    $rand_idx = mt_rand($start_idx, $end_idx);

    while($color_arr[$rand_idx] == $prev_color[0][0])
        $rand_idx = mt_rand($start_idx, $end_idx);
    $sql = "update users set nick_color = '$color_arr[$rand_idx]' where user_id = '$user'";
    $result = pdo_query($sql);

    if($result == NULL)
    {
        echo '<div class ="result_content">아이템 사용에 실패하였습니다.</div>';
    }
    else
    {
    	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
        echo "<div class ='result_content'>닉네임 색깔이&nbsp;<c style='color:{$color_arr[$rand_idx]}'>$ko_color_arr[$rand_idx]</c> 으로 변경되었습니다!</div>";
	echo '<div class= "remain_count">남은 개수 : '.$count.'</div>';
	if(pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]>0)echo '<div class="item_use_onemore" onclick="item_use('.$product_id.')">한번 더 사용하기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $ko_color_arr[$rand_idx] 당첨',now())";
	pdo_query($log);
    }
} 

else if($product_id==14){//닉네임 컬러 변경권 3
    $sql = "select nick_color from users where user_id = '$user'";
    $prev_color = pdo_query($sql);
    $rand_idx = mt_rand(0, 99);
    if($rand_idx <80){
    $start_idx = 0;
    $end_idx = 12;
    }
    else{
    $start_idx=13;
    $end_idx=17;
    echo '<script>firework();</script>';
    }
    $rand_idx = mt_rand($start_idx, $end_idx);

    while($color_arr[$rand_idx] == $prev_color[0][0])
        $rand_idx = mt_rand($start_idx, $end_idx);
    $sql = "update users set nick_color = '$color_arr[$rand_idx]' where user_id = '$user'";
    $result = pdo_query($sql);

    if($result == NULL)
    {
        echo '<div class ="result_content">아이템 사용에 실패하였습니다.</div>';
    }
    else
    {
    	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
        echo "<div class ='result_content'>닉네임 색깔이&nbsp;<c style='color:{$color_arr[$rand_idx]}'>$ko_color_arr[$rand_idx]</c> 으로 변경되었습니다!</div>";
	echo '<div class= "remain_count">남은 개수 : '.$count.'</div>';
	if(pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]>0)echo '<div class="item_use_onemore" onclick="item_use('.$product_id.')">한번 더 사용하기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $ko_color_arr[$rand_idx] 당첨',now())";
	pdo_query($log);
    }
}

else if($product_id==15){//닉네임 컬러 변경권 4
    $sql = "select nick_color from users where user_id = '$user'";
    $prev_color = pdo_query($sql);
    $rand_idx = mt_rand(0, 99);
    if($rand_idx <80){
    $start_idx = 0;
    $end_idx = 17;
    }
    else{
    $start_idx=18;
    $end_idx=23;
    echo '<script>firework();</script>';
    }
    $rand_idx = mt_rand($start_idx, $end_idx);

    while($color_arr[$rand_idx] == $prev_color[0][0])
        $rand_idx = mt_rand($start_idx, $end_idx);
    $sql = "update users set nick_color = '$color_arr[$rand_idx]' where user_id = '$user'";
    $result = pdo_query($sql);

    if($result == NULL)
    {
        echo '<div class ="result_content">아이템 사용에 실패하였습니다.</div>';
    }
    else
    {
    	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
        echo "<div class ='result_content'>닉네임 색깔이&nbsp;<c style='color:{$color_arr[$rand_idx]}'>$ko_color_arr[$rand_idx]</c> 으로 변경되었습니다!</div>";
	echo '<div class= "remain_count">남은 개수 : '.$count.'</div>';
	if(pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]>0)echo '<div class="item_use_onemore" onclick="item_use('.$product_id.')">한번 더 사용하기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $ko_color_arr[$rand_idx] 당첨',now())";
	pdo_query($log);
    }
}
else if($product_id==18){//스트릭 컬러 변경권
    $sql = "select streak_color from users where user_id = '$user'";
    $prev_color = pdo_query($sql);
    $rand_idx = mt_rand(0, 99);
    if($rand_idx <91){
    $start_idx = 0;
    $end_idx = 13;
    }
    else{
    $start_idx=14;
    $end_idx=19;
    echo '<script>firework();</script>';
    }
    $rand_idx = mt_rand($start_idx, $end_idx);

    while($str_color_arr[$rand_idx] == $prev_color[0][0])
        $rand_idx = mt_rand($start_idx, $end_idx);

    $sql = "update users set streak_color = \"$str_color_arr[$rand_idx]\" where user_id = '$user'";
    $result = pdo_query($sql);
    if($result == NULL)
    {
        echo '<div class ="result_content">아이템 사용에 실패하였습니다.</div>';
    }
    else
    {
    	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
        echo '<div class ="result_content" style="display:flex;justify-content:center;align-items:center;">스트릭 색깔이&nbsp;<div style="background-image: linear-gradient(135deg, '.$str_color_div[$rand_idx][0].' 50%, '.$str_color_div[$rand_idx][1].' 50%);width:20px;height:20px;border-radius:5px;margin-right:5px;"></div>색으로 변경되었습니다!</div>';
	echo '<div class= "remain_count">남은 개수 : '.$count.'</div>';
	if(pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]>0)echo '<div class="item_use_onemore" onclick="item_use('.$product_id.')">한번 더 사용하기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $rand_idx 당첨',now())";
	pdo_query($log);
    }
}
else if($product_id==20){//시간복잡도 확인권
    $result = pdo_query("select defunct,time_comp from problem where problem_id = '$input'");
    if($result == null){
	echo '<div class ="result_content">올바르지 않은 입력입니다.</div>';
    }else if($result[0][0] != 'N'){
  	echo '<div class ="result_content">공개된 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][1] == '?'){
	echo '<div class ="result_content">의도된 시간복잡도가 존재하지 않는 문제입니다.</div>';
    }else{
	if(pdo_query("select count(*) from time_item_use where user_id = '$user' and problem_id = '$input'")[0][0]>0){
	    echo '<div class ="result_content">이미 사용된 문제입니다.</div>';
	}else{
	    pdo_query("insert into time_item_use(user_id, problem_id) values('$user', $input)");
	    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	    if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	    else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	    echo "<script>inventory_ajax();</script>";
            echo "<div class ='result_content'>시간 복잡도 확인권이 사용되었습니다.<br>해당 문제 페이지에서 확인 가능합니다.</div>";
	    echo '<div class="item_use_onemore" onclick="toproblem('.$input.')">문제 바로가기</div>';
	    $log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $input 번 문제에 사용',now())";
	    pdo_query($log);

	}
    }
}
else if($product_id==27){//만수르
    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
    if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
    else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
    pdo_query("insert into user_title values('$user',25)");
    echo "<script>inventory_ajax();</script>";
    echo "<div class ='result_content'>만수르 칭호를 획득하였습니다.</div>";
    $log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count',now())";
    pdo_query($log);
    $log = "insert into content_log values('$user',7,'$user 가 칭호 25 획득[인벤토리]','.',now())";
    pdo_query($log);

}

else if($product_id==29){//Normal I 난이도 이하 힌트권
    $result = pdo_query("select difficulty,defunct,hint_item from problem where problem_id = '$input'");
    if($result == null){
	echo '<div class ="result_content">올바르지 않은 입력입니다.</div>';
    }else if($result[0][1] != 'N'){
  	echo '<div class ="result_content">공개된 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][0] > 5){
	echo '<div class ="result_content">Normal I 난이도 이하 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][0] == 0){
	echo '<div class ="result_content">? 난이도 문제에서는 사용 불가능합니다.</div>';
    }else if($result[0][2] == NULL){
	echo '<div class ="result_content">아이템 힌트가 존재하지 않는 문제입니다.</div>';
    }else{
	if(pdo_query("select count(*) from hint_item_use where user_id = '$user' and problem_id = '$input'")[0][0]>0){
	    echo '<div class ="result_content">이미 사용된 문제입니다.</div>';
	}else{
	    pdo_query("insert into hint_item_use(user_id, problem_id) values('$user', $input)");
	    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	    if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	    else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	    echo "<script>inventory_ajax();</script>";
            echo "<div class ='result_content'>힌트가 사용되었습니다.<br>해당 문제 페이지에서 확인 가능합니다.</div>";
	    echo '<div class="item_use_onemore" onclick="toproblem('.$input.')">문제 바로가기</div>';
	    $log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $input 번 문제에 사용',now())";
	    pdo_query($log);

	}
    }
}
else if($product_id==30){//Easy 난이도 힌트권
    $result = pdo_query("select difficulty,defunct,hint_item from problem where problem_id = '$input'");
    if($result == null){
	echo '<div class ="result_content">올바르지 않은 입력입니다.</div>';
    }else if($result[0][1] != 'N'){
  	echo '<div class ="result_content">공개된 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][0] != 1 && $result[0][0] != 2){
	echo '<div class ="result_content">Easy 난이도 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][2] == NULL){
	echo '<div class ="result_content">아이템 힌트가 존재하지 않는 문제입니다.</div>';
    }else{
	if(pdo_query("select count(*) from hint_item_use where user_id = '$user' and problem_id = '$input'")[0][0]>0){
	    echo '<div class ="result_content">이미 사용된 문제입니다.</div>';
	}else{
	    pdo_query("insert into hint_item_use(user_id, problem_id) values('$user', $input)");
	    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	    if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	    else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	    echo "<script>inventory_ajax();</script>";
            echo "<div class ='result_content'>힌트가 사용되었습니다.<br>해당 문제 페이지에서 확인 가능합니다.</div>";
	    echo '<div class="item_use_onemore" onclick="toproblem('.$input.')">문제 바로가기</div>';
	    $log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $input 번 문제에 사용',now())";
	    pdo_query($log);
	}
    }
}
else if($product_id==31){//Beginner 난이도 힌트권
    $result = pdo_query("select difficulty,defunct,hint_item from problem where problem_id = '$input'");
    if($result == null){
	echo '<div class ="result_content">올바르지 않은 입력입니다.</div>';
    }else if($result[0][1] != 'N'){
  	echo '<div class ="result_content">공개된 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][0] != 3 && $result[0][0] != 4){
	echo '<div class ="result_content">Beginner 난이도 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][2] == NULL){
	echo '<div class ="result_content">아이템 힌트가 존재하지 않는 문제입니다.</div>';
    }else{
	if(pdo_query("select count(*) from hint_item_use where user_id = '$user' and problem_id = '$input'")[0][0]>0){
	    echo '<div class ="result_content">이미 사용된 문제입니다.</div>';
	}else{
	    pdo_query("insert into hint_item_use(user_id, problem_id) values('$user', $input)");
	    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	    if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	    else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	    echo "<script>inventory_ajax();</script>";
            echo "<div class ='result_content'>힌트가 사용되었습니다.<br>해당 문제 페이지에서 확인 가능합니다.</div>";
	    echo '<div class="item_use_onemore" onclick="toproblem('.$input.')">문제 바로가기</div>';
	    $log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $input 번 문제에 사용',now())";
	    pdo_query($log);

	}
    }
}
else if($product_id==32){//Normal난이도 힌트권
    $result = pdo_query("select difficulty,defunct,hint_item from problem where problem_id = '$input'");
    if($result == null){
	echo '<div class ="result_content">올바르지 않은 입력입니다.</div>';
    }else if($result[0][1] != 'N'){
  	echo '<div class ="result_content">공개된 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][0] != 5 && $result[0][0] != 6){
	echo '<div class ="result_content">Normal난이도 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][2] == NULL){
	echo '<div class ="result_content">아이템 힌트가 존재하지 않는 문제입니다.</div>';
    }else{
	if(pdo_query("select count(*) from hint_item_use where user_id = '$user' and problem_id = '$input'")[0][0]>0){
	    echo '<div class ="result_content">이미 사용된 문제입니다.</div>';
	}else{
	    pdo_query("insert into hint_item_use(user_id, problem_id) values('$user', $input)");
	    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	    if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	    else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	    echo "<script>inventory_ajax();</script>";
            echo "<div class ='result_content'>힌트가 사용되었습니다.<br>해당 문제 페이지에서 확인 가능합니다.</div>";
	    echo '<div class="item_use_onemore" onclick="toproblem('.$input.')">문제 바로가기</div>';
	    $log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $input 번 문제에 사용',now())";
	    pdo_query($log);

	}
    }
}
else if($product_id==33){//Advanced 난이도 힌트권
    $result = pdo_query("select difficulty,defunct,hint_item from problem where problem_id = '$input'");
    if($result == null){
	echo '<div class ="result_content">올바르지 않은 입력입니다.</div>';
    }else if($result[0][1] != 'N'){
  	echo '<div class ="result_content">공개된 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][0] != 7 && $result[0][0] != 8){
	echo '<div class ="result_content">Advanced 난이도 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][2] == NULL){
	echo '<div class ="result_content">아이템 힌트가 존재하지 않는 문제입니다.</div>';
    }else{
	if(pdo_query("select count(*) from hint_item_use where user_id = '$user' and problem_id = '$input'")[0][0]>0){
	    echo '<div class ="result_content">이미 사용된 문제입니다.</div>';
	}else{
	    pdo_query("insert into hint_item_use(user_id, problem_id) values('$user', $input)");
	    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	    if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	    else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	    echo "<script>inventory_ajax();</script>";
            echo "<div class ='result_content'>힌트가 사용되었습니다.<br>해당 문제 페이지에서 확인 가능합니다.</div>";
	    echo '<div class="item_use_onemore" onclick="toproblem('.$input.')">문제 바로가기</div>';
	    $log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $input 번 문제에 사용',now())";
	    pdo_query($log);
	}
    }
}
else if($product_id==34){//Hard 난이도 힌트권
    $result = pdo_query("select difficulty,defunct,hint_item from problem where problem_id = '$input'");
    if($result == null){
	echo '<div class ="result_content">올바르지 않은 입력입니다.</div>';
    }else if($result[0][1] != 'N'){
  	echo '<div class ="result_content">공개된 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][0] != 9 && $result[0][0] != 10){
	echo '<div class ="result_content">Hard 난이도 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][2] == NULL){
	echo '<div class ="result_content">아이템 힌트가 존재하지 않는 문제입니다.</div>';
    }else{
	if(pdo_query("select count(*) from hint_item_use where user_id = '$user' and problem_id = '$input'")[0][0]>0){
	    echo '<div class ="result_content">이미 사용된 문제입니다.</div>';
	}else{
	    pdo_query("insert into hint_item_use(user_id, problem_id) values('$user', $input)");
	    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	    if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	    else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	    echo "<script>inventory_ajax();</script>";
            echo "<div class ='result_content'>힌트가 사용되었습니다.<br>해당 문제 페이지에서 확인 가능합니다.</div>";
	    echo '<div class="item_use_onemore" onclick="toproblem('.$input.')">문제 바로가기</div>';
	    $log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $input 번 문제에 사용',now())";
	    pdo_query($log);
	}
    }
}
else if($product_id==35){//Challenge 난이도 힌트권
    $result = pdo_query("select difficulty,defunct,hint_item from problem where problem_id = '$input'");
    if($result == null){
	echo '<div class ="result_content">올바르지 않은 입력입니다.</div>';
    }else if($result[0][1] != 'N'){
  	echo '<div class ="result_content">공개된 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][0] != 11 && $result[0][0] != 12){
	echo '<div class ="result_content">Challenge 난이도 문제에서만 사용 가능합니다.</div>';
    }else if($result[0][2] == NULL){
	echo '<div class ="result_content">아이템 힌트가 존재하지 않는 문제입니다.</div>';
    }else{
	if(pdo_query("select count(*) from hint_item_use where user_id = '$user' and problem_id = '$input'")[0][0]>0){
	    echo '<div class ="result_content">이미 사용된 문제입니다.</div>';
	}else{
	    pdo_query("insert into hint_item_use(user_id, problem_id) values('$user', $input)");
	    $count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	    if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	    else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	    echo "<script>inventory_ajax();</script>";
            echo "<div class ='result_content'>힌트가 사용되었습니다.<br>해당 문제 페이지에서 확인 가능합니다.</div>";
	    echo '<div class="item_use_onemore" onclick="toproblem('.$input.')">문제 바로가기</div>';
	    $log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count, $input 번 문제에 사용',now())";
	    pdo_query($log);
	}
    }
}
else if($product_id==36){//럭키 박스
    $rand_idx = mt_rand(0, 99);
    if($rand_idx<50){
	$coin = 0;
    }
    else if($rand_idx<82){
	$coin = 100;
    }
    else if($rand_idx<92){
	$coin = 200;
    }
    else if($rand_idx<97){
	$coin = 300;
    }
    else if($rand_idx<100){
	$coin = 500;
    }

    $sql = "update uinfo set coin = coin + $coin where user_id = '$user'";
    $result = pdo_query($sql);

    if($result == NULL && $coin!=0)
    {
        echo '<div class ="result_content">아이템 사용에 실패하였습니다.</div>';
    }
    else
    {	
	pdo_query("update uinfo set total_coin = total_coin + $coin where user_id = '$user'");
    	$count = pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]-1;
	if($count == 0) pdo_query("delete from user_inventory where user_id = '$user' and product_id = '$product_id'");
	else pdo_query("update user_inventory set count ='$count' where user_id ='$user' and product_id = '$product_id'");
	echo "<script>inventory_ajax();</script>";
	if($coin == 0)echo "<div class ='result_content'>꽝 !</div>";
        else if($coin == 100)echo "<div class ='result_content'>본전 !</div>";
	else echo "<div class ='result_content'><c style='color:#FCD425;'>".$coin."</c><img src = '/image/algo_coin.png' class='coin_img'> 획득 !</div><script>firework();</script>";
	echo '<div class= "remain_count">남은 개수 : '.$count.'</div>';
   	if(pdo_query("select count from user_inventory where user_id = '$user' and product_id = '$product_id'")[0][0]>0)echo '<div class="item_use_onemore" onclick="item_use('.$product_id.')">한번 더 사용하기</div>';
	$log = "insert into content_log values('$user',6,'$user 가 아이템 $product_id 사용[인벤토리]','$user 의 남은 아이템 갯수 $count',now())";
	pdo_query($log);
	if($coin!=0){
	    $user_coin = pdo_query("select coin from uinfo where user_id = '$user'")[0][0];
	    $log = "insert into content_log values('$user',1,'$user 가 코인 $coin 획득[퀘스트]','$user 의 코인 $user_coin',now())";
	    pdo_query($log);
	}
    }
}

?>
<script>
item_off(on_info);
var result = document.getElementById("result");
result.classList.remove("hidden");
</script>
