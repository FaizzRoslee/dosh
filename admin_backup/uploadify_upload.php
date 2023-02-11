<?php
	include_once '../includes/sys_config.php';
	//==========================================================
	if(isset($_SESSION['lang'])){ $fileLang = $_SESSION['lang'].'.php'; }
	else{ $fileLang = 'eng.php'; }
	include_once $sys_config['languages_path'].$fileLang;
	//==========================================================
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	//==========================================================
	
	// JQuery File Upload Plugin v1.4.1 by RonnieSan - (C)2009 Ronnie Garcia
	if (!empty($_FILES)) {
		
		$id = $_GET['id'];
		$usrID = $_GET['usrID'];
		//==============================
		if($id==1) $type = 'home';
		elseif($id==2) $type = 'login';
		elseif($id==30) $type = 'banner';
		//==============================
		$tempFile = $_FILES['Filedata']['tmp_name'];
		$targetPath = $sys_config['upload_path'] . $type .'/';
		$new_filename = str_replace(' ','_',$_FILES['Filedata']['name']);
		$targetFile =  str_replace('//','/',$targetPath) . $new_filename;
		
		// Uncomment the following line if you want to make the directory if it doesn't exist
		// mkdir(str_replace('//','/',$targetPath), 0755, true);
		
		//move_uploaded_file($tempFile,$targetFile);
		$updBy = $usrID;
		if(move_uploaded_file($tempFile,$targetFile)){
 			//==============================
			$sql 	= "INSERT INTO sys_info_image ("
					. " Image_URL,Info_ID,RecordBy,RecordDate"
					. " ) VALUES ("
					. " ".quote_smart($targetFile).",".quote_smart($id).",".quote_smart($updBy).",NOW()"
					. " )"
					;
			$res	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
			//==============================
			$msg 	= ($res) ? 'Insert Successfully' : 'Insert Unsuccessfully';
			func_add_audittrail($updBy,$msg.' ['.$id.']','Admin-Info Image','sys_Info_Image,'.$id);
			//==============================
		}
	}
	//echo $targetFile;
	echo '$_GET=';print_r($_GET);
	echo '$_POST=';print_r($_POST);
	//echo '1';

?>