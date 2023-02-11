<?php
	//****************************************************************/
	// filename: empList.php
	// description: list of the employee
	//****************************************************************/
	include_once '../includes/sys_config.php';
	//==========================================================
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	//==========================================================
	if(isset($_SESSION['lang'])){ $fileLang = $_SESSION['lang'].'.php'; }
	else{ $fileLang = 'eng.php'; }
	include_once $sys_config['languages_path'].$fileLang;
	//==========================================================
	include_once $sys_config['includes_path'].'formElement.php'; //need file language
	include_once $sys_config['includes_path'].'arrayCommon.php'; //need file language
	//==========================================================
	
	func_header("",
				"", // css	
				"", // javascript
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false // int top for [enter]
				);
	
	unset($_SESSION);
	session_destroy();
	
	echo '
		<script language="Javascript">
		//header("Location: '.$sys_config['system_url'].'" ) ;
		location.href = "'.$sys_config['system_url'].'";
		</script>
		';
?>
