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
	$successSynonym = 0;
	$failed = 0;
	$failedSynonym = 0;
	$none = 0;

	$sql = "SELECT * FROM tbl_cwc";
	$res = $db2->sql_query($sql,END_TRANSACTION) or die(print_r($db2->sql_error()));
	
	while($row = $db2->sql_fetchrow($res)){
		$sqlI = "INSERT INTO tbl_chemical ("
			. " Chemical_Cas, Chemical_Formula, Chemical_Name, Chemical_IUPAC, Chemical_Image, Chemical_Key, HS_Code, CWC_ID, isCWC"
			. " ) VALUES ("
			. " ". (!empty($row['cas']) ? quote_smart($row['cas']) : quote_smart(trim($row['key'])))
			. ", ". quote_smart(trim($row['formula'])) .", ". quote_smart(trim($row['iupac'])) .", ". quote_smart(trim($row['iupac']))
			. ", ". quote_smart(trim($row['image'])) .", ". quote_smart(trim($row['key'])) .", ". quote_smart(trim($row['hs_code']))
			. ", ". quote_smart($row['id']). ", 1"
			. ")";
		// echo $sqlI;
		$resI = $db2->sql_query($sqlI,END_TRANSACTION) or die(print_r($db2->sql_error()));
		
		if($resI){
			$success++;
			
			$id = $db2->sql_nextid();
			
			$sqlF = "SELECT synonym FROM tbl_cwc_synonym WHERE cwc_id = ". quote_smart($row['id']);
			$resF = $db2->sql_query($sqlF,END_TRANSACTION) or die(print_r($db2->sql_error()));
			
			while($rowF = $db2->sql_fetchrow($resF)){
				$sqlS = "INSERT INTO tbl_chemical_synonyms ("
					. " Synonym_Name, Chemical_ID"
					. " ) VALUES ("
					. " ". quote_smart($rowF['synonym']) .", ". quote_smart($id)
					. ")";
				$resS = $db2->sql_query($sqlS,END_TRANSACTION) or die(print_r($db2->sql_error()));
				
				if($resS) $successSynonym++;
				else $failedSynonym++;
			}
		}
		else{
			$failed++;
		}
	}
	echo 'success = '. $success;
	echo '<br />';
	echo 'failed = '. $failed;
	echo '<br />';
	echo 'successSynonym = '. $successSynonym;
	echo '<br />';
	echo 'failedSynonym = '. $failedSynonym;
	
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