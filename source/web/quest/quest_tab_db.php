
<?php
$cache_time = 30;
$OJ_CACHE_SHARE = false;
require_once('../include/cache_start.php');
require_once('../include/db_info.inc.php');
require_once('../include/my_func.inc.php');
require_once('../include/memcache.php');
require_once('../include/setlang.php');
require_once('../include/bbcode.php');
$quest_class = isset($_POST['quest_class']) ? $_POST['quest_class'] : '';
$user = isset($_POST['user']) ? $_POST['user'] : '';

if($quest_class == 1)echo '<div class="quest_tab selected" name="일일" onclick="selectTab(this)">일일 퀘스트';
else echo '<div class="quest_tab" name="일일" onclick="selectTab(this)">일일 퀘스트';
$sql = "select count(*) from progress where user_prog = quest_end_prog and quest_rec_rewards = 0 and quest_class = 1 and user_id = '$user'";	
if(pdo_query($sql)[0][0]>0)echo '<div class="quest_can_clear"></div>';
echo '</div>';
if($quest_class == 2)echo '<div class="quest_tab selected" name="주간" onclick="selectTab(this)">주간 퀘스트';
else echo '<div class="quest_tab" name="주간" onclick="selectTab(this)">주간 퀘스트';
$sql = "select count(*) from progress where user_prog = quest_end_prog and quest_rec_rewards = 0 and quest_class = 2 and user_id = '$user'";	
if(pdo_query($sql)[0][0]>0)echo '<div class="quest_can_clear"></div>';
echo '</div>';
if($quest_class == 3)echo '<div class="quest_tab selected" name="메인" onclick="selectTab(this)">메인 퀘스트';
else echo '<div class="quest_tab" name="메인" onclick="selectTab(this)">메인 퀘스트';   
$sql = "select count(*) from progress where user_prog = quest_end_prog and quest_rec_rewards = 0 and quest_class > 2 and user_id = '$user'";	
if(pdo_query($sql)[0][0]>0)echo '<div class="quest_can_clear"></div>';
echo '</div>';
?>
<script>
</script>
