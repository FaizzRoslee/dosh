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
	include_once $sys_config['includes_path'].'func_rand_str.php';
	include_once $sys_config['includes_path'].'func_valid_login.php';
	include_once $sys_config['includes_path'].'func_get_user.php';
	//==========================================================
	if(isset($_POST['language'])) $language = $_POST['language'];
	elseif(isset($_GET['language'])) $language = $_GET['language'];
	else{
		if(isset($_SESSION['lang'])) $language = $_SESSION['lang'];
		else $language = 'eng';
	}
	if(empty($language)) $language = 'eng';
	$fileLang = $language.'.php';
	include_once $sys_config['languages_path'].$fileLang;
	//==========================================================
	include_once $sys_config['includes_path'].'formElement.php'; //need file language
	include_once $sys_config['includes_path'].'arrayCommon.php'; //need file language
	//==========================================================
	if(isset($_POST['language'])){
		$_SESSION['lang'] = $language;
	}


$sql = "INSERT INTO tbl_visitor_counter (visit_time) VALUES (NOW())";
							//var_dump($sql);
	$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
	$counter_all = _get_RowExist('tbl_visitor_counter');
	$counter_today = _get_RowExist('tbl_visitor_counter', array("AND DATE_FORMAT(visit_time, '%d-%m-%Y')" => " = ". quote_smart(date('d-m-Y'))));
						
	
        //echo $counter_all;

	echo '{"count":{ "all":'.$counter_all.', "today":'.$counter_today.' }}'

	
?>

	
    
	