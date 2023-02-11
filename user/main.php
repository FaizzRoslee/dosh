<?php
	/****************************************************/
	// filename: formElement.php
	// 
	/****************************************************/
	require_once '../includes/sys_config.php';
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	//==========================================================
	if(isset($_SESSION['lang'])){ $fileLang = $_SESSION['lang'].'.php'; }
	else{ $fileLang = 'eng.php'; }
	include_once $sys_config['languages_path'].$fileLang;
	//==========================================================
	require_once $sys_config['includes_path'].'func_master.php';
	require_once $sys_config['includes_path'].'func_header.php';
	require_once $sys_config['includes_path'].'func_footer.php';
	//==========================================================
	if($_SESSION['user']['Level_ID']>5){
		$str_url_content = 'userProfile.php';
		$menu = 1;
	}else{
		$str_url_content = 'changePassword.php';
		$menu = 3;
	}
	$mod_config['module_name'] = 'user';
	
	func_header("Profile",
				"", // css	
				"", // javascript
				true, // menu
				false, // portlet
				false, // header
				true, // frame
				$sys_config['frame_path'].'head.php', // str url frame top
				$sys_config['frame_path'].'left_'.$mod_config['module_name'].'.php?menu='.$menu, // str url frame left
				$str_url_content, // str url frame content
				false // int top for [enter]
				);
				
	func_footer(false,false);
?>