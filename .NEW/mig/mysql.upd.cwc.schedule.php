<?php
	require_once('../includes/sys_constants.php');
	require_once('../classes/dbs/mysql4.php');
	//=====================================================================================
	$db_config2['host_name']		= "localhost"; // server name
	$db_config2['user_fullname']	= ""; // database's user name
	$db_config2['password']			= ""; // database's user password 
	$db_config2['database']			= ""; // database name
	//=====================================================================================
	$db2 = new	sql_db($db_config2['host_name'],
						$db_config2['user_fullname'],
						$db_config2['password'],
						$db_config2['database'],
						false);	// create database object
	// print_r($db2);
	if(!$db2->db_connect_id)
	   die("Unable to establish database connection");

	$db2->sql_query("SET NAMES 'latin1';",END_TRANSACTION);	// set connection character set
	//=====================================================================================
	

	$success = 0;
	$failed = 0;
	$none = 0;

	$sql = "SELECT id,schedule FROM tbl_cwc";
	$res = $db2->sql_query($sql,END_TRANSACTION) or die(print_r($db2->sql_error()));
	
	while($row = $db2->sql_fetchrow($res)){
		$sqlU = "UPDATE tbl_Chemical SET"
			. " Chemical_Schedule = ". quote_smart(trim($row['schedule']))
			. " WHERE CWC_ID = ". quote_smart(trim($row['id']))
			. " LIMIT 1";
		// echo $sqlI;
		$resU = $db2->sql_query($sqlU,END_TRANSACTION) or die(print_r($db2->sql_error()));
		
		if($resU){
			$success++;
		}
		else{
			$failed++;
		}
	}
	echo 'success = '. $success;
	echo '<br />';
	echo 'failed = '. $failed;
	// echo '<br />';
	// echo 'successSynonym = '. $successSynonym;
	// echo '<br />';
	// echo 'failedSynonym = '. $failedSynonym;
	
	$db2->sql_close();
	
	//=================================================
	function quote_smart($value){
		// Stripslashes
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		
		// Quote if not a number or a numeric string
		if (!is_numeric($value)) {
			$value = "'" . mysql_real_escape_string($value) . "'";
			// $value = "'" . addslashes($value) . "'";
		}
		
		return $value;	
	}
	//=================================================

 ?>