<?php
	define('IN_TAG', true);
	
	include 'includes/common.inc.php';    
	
	if(!isset($_COOKIE['username'])){
		pushLocation2('请登录后再进行操作!', 'login.php');
	}
	
	//先验证一下cookie和uniqid，防止cookie伪造攻击
	$sql = "select tc_uniqid from tc_user where tc_username='{$_COOKIE['username']}' limit 1";
	$query = mysql_query($sql);
	$rs = mysql_fetch_array($query);
	
	if(isset($rs['tc_uniqid'])){
		//mysql_close();
		checkUniqid($rs['tc_uniqid'], $_COOKIE['uniqid'],'member.php');	//判断唯一标识符是否异常	
		
		//print_r($_GET);
		//exit();
		if(($_GET['action'] == 'check') && isset($_GET['id']) && !empty($_GET['id'])){
			$sql = "UPDATE `tc_friend` SET `tc_friend_state`=1 WHERE `tc_id`={$_GET['id']} LIMIT 1";
			mysql_query($sql);
			//echo mysql_affected_rows();
			//exit();
			if(mysql_affected_rows() == 1){
				mysql_close();
				pushLocation2('验证好友成功', 'friend_list.php');
			}else{
				mysql_close();
				pushLocation2('验证好友失败', 'friend_list.php');
			}
		}else{
			pushCloseWindow('非法操作');
		}
	}
?>