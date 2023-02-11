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
	<!--
	<script type="text/javascript" src="includes/jquery/jquery-ui-1.8.9/jquery-1.4.4.min.js"></script>
	<script language="Javascript">
	$(document).ready(function() {
		$('a').click(function(){
			var name = $(this).attr('name');
			$.ajax({ 
				type: "GET",  
				url: "mysql.create.query.php",
				data: "name=" + name,  
				success: function(msg){
					$("span[name="+ name +"]").text(msg);
				}
			});
			return false;
		});
	});
	</script>
	-->
<?php
	$success = 0;
	$no_image = 0;

	/*
	$sql = "SELECT `key`, id FROM tbl_cwc WHERE done = 0";
	$res = $db2->sql_query($sql,END_TRANSACTION) or die(print_r($db2->sql_error()));
	
	$url = "../upload/CWC-images/";
	while($row = $db2->sql_fetchrow($res)){
		$img_url = $url . $row['key'] .'.wmf';
		echo '<a href="'. $img_url .'">'. $row['key'] .'</a><br />';
		if( file_exists( $img_url ) ){
			$sqlU = "UPDATE tbl_cwc SET image = ". quote_smart( $img_url ) .", done = 1 WHERE id = ". quote_smart( $row['id'] ); 
			$resU = $db2->sql_query($sqlU,END_TRANSACTION) or die(print_r($db2->sql_error()));

			if($resU) $success++;
		}
		else{
			$sqlU = "UPDATE tbl_cwc SET done = 2 WHERE id = ". quote_smart( $row['id'] ); 
			$resU = $db2->sql_query($sqlU,END_TRANSACTION) or die(print_r($db2->sql_error()));
			
			if($resU) $no_image++;
		}
	}
	echo 'success = '. $success;
	echo '<br />';
	echo 'no_image = '. $no_image;
	*/
	

	$sql = "SELECT `key`, image FROM tbl_cwc LIMIT 10";
	$res = $db2->sql_query($sql,END_TRANSACTION) or die(print_r($db2->sql_error()));
	
	while($row = $db2->sql_fetchrow($res)){
		echo '<a href="'. $row['image'] .'">'.$row['key'] .'</a> = <img src="'. $row['image'] .'" /><br />';
	}
	
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