<?php
	/****************************************************/
	// filename: main.php
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

	if (isset($_GET['menu'])) $menu = $_GET['menu'];
	else $menu = 1;
	
	if($menu==1) $str_url_content = 'submissionList.php';
	elseif($menu==2) $str_url_content = 'checkingNewList.php';
	elseif($menu==3) $str_url_content = 'approvedNewList.php';
	elseif($menu==4) $str_url_content = 'rejectedNewList.php';
	elseif($menu==6) $str_url_content = 'approvalNewList.php';
	elseif($menu==12) $str_url_content = 'checkingRenewList.php';
	elseif($menu==13) $str_url_content = 'approvedRenewList.php';
	elseif($menu==14) $str_url_content = 'rejectedRenewList.php';
	elseif($menu==16) $str_url_content = 'approvalRenewList.php';
	
	
	
	
    // linuxhouse__20220701
    // start penambahbaikan - 3_auto-blast notification- need more info from user - v005
    // redirect to message menu at supplier/manufacturer site
    elseif($menu==17) $str_url_content = 'announcementNewList.php';
    // end penambahbaikan - 3_auto-blast notification- need more info from user - v005
	
	
	
	
	else $str_url_content = 'allList.php';
	
	if($_SESSION['user']['Level_ID']==5){
		$str_url_content = 'approvedNewList_state.php';
		$menu = 5;
	}
	
	$mod_config['module_name'] = 'submission';
	
	func_header(_LBL_SUBMISSION,
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
