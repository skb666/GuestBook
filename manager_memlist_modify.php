<?php
	session_start();	
	define('IN_TAG', true);
	header('Content-Type:text/html;charset=utf-8');
	include 'includes/common.inc.php';
	//脚本开始之心执行时间
	$startTime = runTime();
	checkManger();
	
	/*修改现有情况*/
	if(!empty($_POST['modify'])){

		include 'includes/modify.inc.php';
		$infoModify = array();
		$infoModify['newpassword'] = checkPassModify($_POST['newpassword'], 6, 15);
		$infoModify['sex'] = $_POST['sex'];
		$infoModify['face'] = $_POST['facesub'];
		$infoModify['site'] = checkSiteModify($_POST['site']);
		$infoModify['qq'] = checkQQModify($_POST['qq']);
		$infoModify['email'] = checkEmailModify($_POST['email']);
		$infoModify['switch'] = $_POST['switch'];
		//var_dump($infoModify['switch']);
		if(intval($infoModify['switch']) == 1){
			$infoModify['sign'] = $_POST['sign'];//checkSignModify($_POST['sign'],10,200);
		}elseif(intval($infoModify['switch']) == 0){
			$infoModify['sign'] = '';
		}
		$infoModify['question'] = checkQuestionModify($_POST['question'],2,10);
		$infoModify['answer'] = checkAnswerModify($_POST['answer'],$infoModify['question'],2,32);
		$infoModify['level'] = $_POST['level'];
		$modifyid = $_POST['modifyid'];
		
		//echo $modifyid;
		//echo '<br />';
		//不更新密码的语句
		$sqlStr1 = "UPDATE `tc_user` SET
											`tc_sex`='{$infoModify['sex']}',
											`tc_face`='{$infoModify['face']}',
											`tc_email`='{$infoModify['email']}',
											`tc_switch`='{$infoModify['switch']}',
											`tc_sign`='{$infoModify['sign']}',
											`tc_site`='{$infoModify['site']}',
											`tc_qq`='{$infoModify['qq']}',
											`tc_level`='{$infoModify['level']}',
											`tc_question`='{$infoModify['question']}',
											`tc_answer`='{$infoModify['answer']}'
								WHERE
											`tc_id`='$modifyid'
					";
		
		//更新密码的语句		
		$sqlStr2 = "UPDATE `tc_user` SET
											`tc_password`='{$infoModify['newpassword']}',
											`tc_sex`='{$infoModify['sex']}',
											`tc_face`='{$infoModify['face']}',
											`tc_email`='{$infoModify['email']}',
											`tc_switch`='{$infoModify['switch']}',
											`tc_sign`='{$infoModify['sign']}',
											`tc_site`='{$infoModify['site']}',
											`tc_qq`='{$infoModify['qq']}',
											`tc_level`='{$infoModify['level']}',
											`tc_question`='{$infoModify['question']}',
											`tc_answer`='{$infoModify['answer']}'
								WHERE
											`tc_id`='$modifyid'
					";
		
		//echo $sqlStr1;
		//echo '<br />';
		//echo $sqlStr2;
		//exit;
		if($GLOBALS['sys_code'] == 1){
			//有验证码
			checkCode($_POST['code'],$_SESSION['code']);	//对比验证码

			//先验证一下cookie和uniqid，防止cookie伪造攻击
			$sql = "SELECT `tc_uniqid` FROM `tc_user` WHERE `tc_username`='{$_COOKIE['username']}' LIMIT 1";
			$query = mysql_query($sql);
			$rs = mysql_fetch_array($query);	
			if(isset($rs['tc_uniqid'])){
				//mysql_close();
				checkUniqid($rs['tc_uniqid'], $_COOKIE['uniqid'],'manager_mem_list.php');	//判断唯一标识符是否异常
				if(empty($infoModify['newpassword'])){	//如果密码为空,则不修改密码
					$query = mysql_query($sqlStr1) or die(mysql_error());
				}else{		
					$query = mysql_query($sqlStr2) or die(mysql_error());
				}
			}			
		}elseif($GLOBALS['sys_code'] == 0){	//无验证码
			//先验证一下cookie和uniqid，防止cookie伪造攻击
			$sql = "select tc_uniqid from tc_user where tc_username='{$_COOKIE['username']}' limit 1";
			$query = mysql_query($sql);
			$rs = mysql_fetch_array($query);
			if(isset($rs['tc_uniqid'])){			
				checkUniqid($rs['tc_uniqid'], $_COOKIE['uniqid'],'member.php');	//判断唯一标识符是否异常
				if(empty($infoModify['newpassword'])){	//如果密码为空,则不修改密码
					$query = mysql_query($sqlStr1) or die(mysql_error());					
				}else{	//更改密码
					$query = mysql_query($sqlStr2) or die(mysql_error());
				}
			}
		}
	
		if(mysql_affected_rows() == 1){
			mysql_close();
			//session_destroy();
/* 			pushAlert('博友资料修改成功!');
			pushLocation('manager_mem_list.php'); */
			pushLocation2('博友资料修改成功!', 'manager_memlist_modify.php?modifyid='.$_POST['modifyid']);
		}else{
			
			mysql_close();
			//session_destroy();
			//pushAlert('你没有对博友资料进行任何修改');
			//pushLocation('manager_memlist_modify.php?modifyid='.$_POST['modifyid']);
			pushLocation2('你没有对博友资料进行任何修改', 'manager_memlist_modify.php?modifyid='.$_POST['modifyid']);
		}				
	}	
	
	
	/*显示现有情况*/
	if(isset($_COOKIE['username'])){	//判断是否已登录
		$sql = "SELECT 
						`tc_username`,		
						`tc_sex`,
						`tc_face`,
						`tc_switch`,
						`tc_sign`,
						`tc_email`,
						`tc_site`,
						`tc_qq`,
						`tc_reg_time`,
						`tc_level`,
						`tc_question`,
						`tc_answer` 
				FROM 
						`tc_user` 
				WHERE 
						`tc_id`='{$_GET['modifyid']}'
				";
		$query = mysql_query($sql);
		$rs = mysql_fetch_array($query);
		if($rs){
			mysql_close();	//关闭数据库连接
			$userInfo = array();
			$userInfo['username'] = $rs['tc_username'];
			$userInfo['sex'] = $rs['tc_sex'];
			$userInfo['face'] = $rs['tc_face'];
			$userInfo['email'] = $rs['tc_email'];
			$userInfo['switch'] = $rs['tc_switch'];
			$userInfo['sign'] = $rs['tc_sign'];
			$userInfo['site'] = $rs['tc_site'];
			$userInfo['qq'] = $rs['tc_qq'];
			$userInfo['reg_time'] = $rs['tc_reg_time'];
			$userInfo['level'] = $rs['tc_level'];
			$userInfo['question'] = $rs['tc_question'];
			$userInfo['answer'] = $rs['tc_answer'];
			$userInfo = userInfoHtml($userInfo);
			
			//判断性别,根据性别不同,输出不同HTML页面
			//function changeSex($sex){
			//	$userInfo['sex'] = $sex;
				if($userInfo['sex'] == '男'){
					$userInfo['sex_html'] = '<label><input type="radio" name="sex" value="男" checked="checked" />男</label><label><input type="radio" name="sex" value="女" />女</label>';
				}elseif($userInfo['sex'] == '女'){
					$userInfo['sex_html'] = '<label><input type="radio" name="sex" value="男" />男</label><label><input type="radio" name="sex" value="女" checked="checked" />女</label>';				
				}				
			//}
			
			//判断权限,根据权限不同,输出不同html页面
			if($userInfo['level'] == 1){
				$userInfo['level_html'] = '<label><input type="radio" name="level" value="0" />普通用户</label><label><input type="radio" name="level" value="1" checked="checked" />管理员</label>';
			}elseif($userInfo['level'] == 0){
				$userInfo['level_html'] = '<label><input type="radio" name="level" value="0" checked="checked" />普通用户</label><label><input type="radio" name="level" value="1" />管理员</label>';
			}
			
			//判断开关,根据开关不同,输出不同开关选择
			function changeSwitch($switch){
				$userInfo['switch'] = $switch;
				if($userInfo['switch'] == 1){
					echo '<label><input type="radio" name="switch" value="0"/>关</label><label><input type="radio" name="switch" value="1" checked="checked" />开</label>';
				}else{
					echo '<label><input type="radio" name="switch" value="0" checked="checked" />关</label><label><input type="radio" name="switch" value="1" />开</label>';
				}
			}

			//通过下啦菜单更换头像
			$userInfo['change_face'] = '<select name="change-face" id="change-face" onchange="document.images['."'".'face'."'".'].src=document.getElementById('."'".'facesub'."'".').value=options[selectedIndex].value">';
			//$userInfo['change_face'] = '<select name="change-face" id="change-face" onchange="document.images['."'".'face'."'".'].src=options[selectedIndex].value">';
			//$userInfo['change_face'] = '<select name="change-face" id="change-face">';
				foreach (range(1,9) as $num) {
					$userInfo['change_face'] .= '<option value="face/m0'.$num.'.gif">face/m0'.$num.'.gif</option>';
				}
				foreach (range(10,64) as $num) {
					$userInfo['change_face'] .= '<option value="face/m'.$num.'.gif">face/m'.$num.'.gif</option>';
				}				
			$userInfo['change_face'] .= '</select>';

		}
	}else{
		pushAlert2('非法访问!');
	}
	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title><?php echo $GLOBALS['sys_title'];?>--博友信息修改</title>
		<link rel="stylesheet" type="text/css" href="styles/style.css" />
		<link rel="stylesheet" type="text/css" href="styles/manager_memlist_modify.css" />
		<link href="favicon.ico" rel="Shortcut Icon" />
		<link href="favicon.ico" rel="Bookmark" />
		<script type="text/javascript" src="js/modify.js"></script>
		<!--<script type="text/javascript" src="js/login.js"></script> -->
		<!-- <script type="text/javascript" src="js/validator.js"></script> -->
		<!-- <script type="text/javascript" src="js/reg.ajax.js"></script> -->
		<script type="text/javascript" charset="UTF-8" src="editor/kindeditor-min.js"> </script>
		<script type="text/javascript" charset="UTF-8" src="editor/lang/zh_CN.js"> </script>
		<script>
		        //var editor;
		        //KindEditor.ready(function(K) {
		                //editor = K.create('#editor_id');
		        //});
			var editor;
			KindEditor.ready(function(K) {
				editor = K.create('textarea[name="sign"]', {
					resizeType : 1,
					allowPreviewEmoticons : false,
					allowImageUpload : false,
					items : [
						'fontname', 'fontsize', '|', 'forecolor', 'hilitecolor', 'bold', 'italic', 'underline',
						'removeformat', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
						'insertunorderedlist', '|', 'emoticons', 'image', 'link']
				});
			});
    
		</script>
	</head>
	<body>
		<?php
			include 'includes/header.inc.php';
			echo "\n";
		?>
		<div id="member">
			<?php memberSlider();?>
			<div id="member-main">
				<h2>博友信息更改</h2>
				<form action="manager_memlist_modify.php" method="post">
					<input type="hidden" name="modifyid" value="<?php echo $_GET['modifyid'];?>" />
					<dl>	
						<dd>用 户 名：<?php echo $userInfo['username']; ?></dd>
						<dd><label>新 密 码：<input type="password" name="newpassword" class="text"/></label></dd>
						<dd>性　　别：<?php echo $userInfo['sex_html']; ?></dd>
						<dd id="face">头　　像：<?php echo $userInfo['change_face']; ?><img src="<?php echo $userInfo['face']; ?>" id="face" alt="<?php echo $userInfo['face']; ?>"/><input type="hidden" value="<?php echo $userInfo['face']; ?>" id="facesub" name="facesub" /></dd>
						<dd><label>电子邮件：<input type="text" name="email" class="text" value="<?php echo $userInfo['email']; ?>" /></label></dd>
						<dd><label>主　　页：<input type="text" name="site" class="text" value="<?php echo $userInfo['site']; ?>" /></label></dd>
						<dd><label>Q 　 　Q：<input type="text" name="qq" class="text" value="<?php echo $userInfo['qq']; ?>" /></label></dd>
						<dd><label>密码提示：<input type="text" name="question" class="text" value="<?php echo $userInfo['question']; ?>" /></label></dd>
						<dd><label>密码回答：<input type="text" name="answer" class="text" value="<?php echo $userInfo['answer']; ?>" /></label></dd>
						<dd>权　　限：<?php echo $userInfo['level_html']; ?></dd>
						<dd>签名开关：<?php changeSwitch($userInfo['switch']);?></dd>
						<dd id="sign-content"><textarea id="editor_id" name="sign" class="text"><?php echo $userInfo['sign'];?></textarea></dd>
						<?php echo isDisplayCode(); ?>
						<dd><input type="submit" name="modify" value="修改资料" /></dd>
					</dl>
				</form>
			</div>
			<div class="clear"></div>
		</div>
		<?php
			include 'includes/footer.inc.php';
			echo "\n";
		?>
	</body>
</html>		