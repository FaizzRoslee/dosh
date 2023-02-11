<?php
	require_once('../includes/sys_constants.php');
	require_once('../classes/dbs/mysql4.php');
	//=====================================================================================
	$db_config2['host_name']		= "localhost"; // server name
	$db_config2['user_fullname']	= "root"; // database's user name
	$db_config2['password']			= ""; // database's user password 
	$db_config2['database']			= "jkkp_einventory"; // database name
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
	
?>
<?php
	/*
KEY = KEY
Chemical_Cas = !empty(cas) ? cas : KEY
Chemical_Formula = formula
Chemical_Name = iupac
Chemical_IUPAC = iupac
HS_Code = hc_code
Chemical_Image = image
	*/

	$success = 0;
	$failed = 0;
	$none = 0;

	$sql = "SELECT `key`, COUNT(id) AS had, cwc_id FROM tbl_cwc_synonym WHERE cwc_id IS NULL GROUP BY `key`";
	$res = $db2->sql_query($sql,END_TRANSACTION) or die(print_r($db2->sql_error()));
	
	while($row = $db2->sql_fetchrow($res)){
		$sqlF = "SELECT id FROM tbl_cwc WHERE `key` = ". quote_smart($row['key']);
		$resF = $db2->sql_query($sqlF,END_TRANSACTION) or die(print_r($db2->sql_error()));
		
		if($db2->sql_numrows($resF) > 0){
			$rowF = $db2->sql_fetchrow($resF);
			$sqlU = "UPDATE tbl_cwc_synonym SET cwc_id = ". quote_smart($rowF['id']) ." WHERE `key` = ". quote_smart($row['key']) ." LIMIT ". $row['had'];
			$resU = $db2->sql_query($sqlU,END_TRANSACTION) or die(print_r($db2->sql_error()));
			
			if($resU) $success++;
			else $failed++;
		}
		else{
			$none++;
		}
	}
	echo 'success = '. $success;
	echo '<br />';
	echo 'failed = '. $failed;
	echo '<br />';
	echo 'none = '. $none;
	
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