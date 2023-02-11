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
	require_once $sys_config['includes_path'].'func_header.php';
	require_once $sys_config['includes_path'].'func_footer.php';
	//==========================================================
	
	$str_url_content = 'home.php';
		
	$mod_config['module_name'] = 'authentication';
	
	func_header(_LBL_ADMINISTRATION,
				"", // css	
				"", // javascript
				true, // menu
				false, // portlet
				false, // header
				true, // frame
				$sys_config['frame_path'].'head.php', // str url frame top
				"", // str url frame left
				$str_url_content, // str url frame content
				false // int top for [enter]
				);
				
	func_footer(false,false);
?>