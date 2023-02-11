<?php
	//****************************************************************/
	// filename: search.php
	// description: list of the precautioncode,description
	//****************************************************************/
	include_once '../includes/sys_config.php';
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
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	//==========================================================
	
	$q = strtolower($_GET["q"]);
	if (!$q) return;
	
	$sql 	= "SELECT Statement_Code, Statement_Desc FROM tbl_Precaution_Statement"
			. " WHERE 1 AND Active = 1"
			. " ORDER BY Statement_Code ASC";
	$res	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
	
	$createArr = array();
	while($row = $db->sql_fetchrow($res)){
		$createArr[$row['Statement_Code']] = $row['Statement_Desc'];
	}

	$items 	= $createArr;
	foreach ($items as $key=>$value) {
		if (strpos(strtolower($key), $q) !== false) {
			echo "$key|$value\n";
		}
	}

?>