<?php
	/*******************************************************************************
	// filename: getImage.php
	//
	*******************************************************************************/
	
	if(isset($_GET['id'])){
		$imgID = $_GET['id'];
		
		global $db;
		include 'sys_config.php';
		include 'db_config.php';
		include 'func_master.php';
	}else{
		header("HTTP/1.0 404 Not Found");
		exit();
	}
	
	$sql = "SELECT Pictogram_Url FROM tbl_Hazard_Pictogram"
		. " WHERE Pictogram_ID = ".quote_smart($imgID);
	$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
	
	if($db->sql_numrows($res)>0){
		$row = $db->sql_fetchrow($res);
		$imgSrc = $row['Pictogram_Url'];
	}else{
		$imgSrc = $sys_config['images_path'].'no_image.gif';
	}
	echo '<img src="'.$imgSrc.'" width="150" height="150" />';
?>