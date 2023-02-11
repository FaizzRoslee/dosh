<?php
	//****************************************************************/
	// filename: hazPictogramAddEdit.php
	// description: add/edit details of the employee
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
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'formElement.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	func_header("",
				$sys_config['includes_path']."css/global.css,". // css	
				"",
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
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
	//==========================================================
	$userLevelID = $_SESSION['user']['Level_ID'];
	if($userLevelID>2) exit();
	//==========================================================
	if(isset($_POST['pictogramID'])) $pictogramID = $_POST['pictogramID'];
	elseif(isset($_GET['pictogramID'])) $pictogramID = $_GET['pictogramID'];
	else $pictogramID = 0;
	//==========================================================
	if($pictogramID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	//==========================================================
	if(isset($_POST['save'])){
		$picDesc = isset($_POST['picDesc'])?$_POST['picDesc']:'';
		$typeID = isset($_POST['typeID'])?$_POST['typeID']:0;
		$active = isset($_POST['active'])?$_POST['active']:0;
		//==========================================================
		$updBy = $_SESSION['user']['Usr_ID'];
		$url_path = '';
		$msg = '';
		//==========================================================
		if($_FILES['picUrl']['name']!=''){
			if($pictogramID!=0){
				$sqlCheck	= "SELECT Pictogram_Url"
							. " FROM tbl_Hazard_Pictogram"
							. " WHERE Pictogram_ID = ".quote_smart($pictogramID)."";
				$resCheck	= $db->sql_query($sqlCheck,END_TRANSACTION) or die (print_r($db->sql_error()));
				$rowCheck	= $db->sql_fetchrow($resCheck);
				if(!empty($rowCheck['Pictogram_Url']))
					unlink($rowCheck['Pictogram_Url']); //untuk remove dr folder
			}
			//==========================================
			$imgtype = $_FILES['picUrl']['type'];
			$image = $_FILES['picUrl']['tmp_name'];
			//==========================================
			function image_valid($type){
				$file_types = array("image/jpeg","image/jpg","image/pjpeg","image/gif","image/x-png","image/png","image/bmp");
				
				if(in_array($type, $file_types)==TRUE) return TRUE;
				else return FALSE;
			}
			//==========================================
			function size_valid($image){
				if((filesize($image))>1048576) return FALSE;
				else return TRUE;
			}
			//==========================================
			if(image_valid($imgtype)==TRUE && size_valid($image)==TRUE){
				$key_date = date("Ymd");
				$file_name = $_FILES["picUrl"]["name"];
				$moduleName = 'hazPictogram/';
				$target_path = $sys_config['upload_path'].$moduleName;
				//die($target_path);
				if (!(is_dir($target_path))) mkdir($target_path);
				//==========================================
				if(file_exists($target_path.$file_name)){
					$i=1;
					$ext = explode(".",$file_name);
					//==========================================	
					$file_name_new = '';
					for($y=0;$y<count($ext);$y++){
						if($y==(count($ext) -1 ))
							break;
						$file_name_new .= $ext[$y];
					}
					$file_name_new .= "(".$i.").".$ext[(count($ext) - 1)];
					//==========================================	
					while(file_exists($target_path.$file_name_new)){
						$i++;
						$file_name_new = '';
						for($y=0;$y<count($ext);$y++){
							if ($y == (count($ext) -1 ))
								break;
							$file_name_new .= $ext[$y];
						}
						$file_name_new .= "(".$i.").".$ext[(count($ext) - 1)];
					}
				}else
					$file_name_new = $file_name;
				//==========================================	
				$file_name_new = str_replace(" ","_",$file_name_new);
				$file_path = $target_path.$file_name_new;
				//==========================================	
				if(move_uploaded_file($_FILES['picUrl']['tmp_name'], $file_path)) $msg = 'Picture was uploaded Successfully\n';
				else $msg = 'Picture cannot upload\n';
				//==========================================	
				$url_path = $file_path;
			}else{
				$status2 = "NOTOK";
				if(size_valid($image)==FALSE){ $msg .= 'Picture File Size Should Not Be More Than 1MB.\n'; }
				elseif(image_valid($imgtype)==FALSE){ $msg .= 'Incompatible Picture File Type Detected.\n'; }
			}
		}
		//=====================================================================
		if($pictogramID!=0){
			$sqlUpd	= "UPDATE tbl_Hazard_Pictogram SET"
					. " Pictogram_Desc = ".quote_smart($picDesc).",";
			if($url_path!='')
				$sqlUpd	.= " Pictogram_Url = ".quote_smart($url_path).",";
			$sqlUpd	.= " Type_ID = ".quote_smart($typeID)."," 
					. " UpdateBy = ".quote_smart($updBy)."," 
					. " UpdateDate = NOW()," 
					. " Active = ".quote_smart($active).""
					. " WHERE Pictogram_ID = ".quote_smart($pictogramID)." LIMIT 1";
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			//=====================================================================
			$msg = ($resUpd) ? 'Update Successfully' : 'Update Unsuccessfully';
			//=====================================================================
		}else{
			//=====================================================================
			$sqlIns	= "INSERT INTO tbl_Hazard_Pictogram ("
					. " Pictogram_Desc,";
			if($url_path!='')
				$sqlIns	.= " Pictogram_Url,";
			$sqlIns	.= " Type_ID,"
					. " RecordBy, RecordDate,"
					. " UpdateBy, UpdateDate,"
					. " Active" 
					. " ) VALUES ("
					. " ".quote_smart($picDesc).",";
			if($url_path!='')
				$sqlIns	.= " ".quote_smart($url_path).",";
			$sqlIns	.= " ".quote_smart($typeID).","
					. " ".quote_smart($updBy).", NOW(),"
					. " ".quote_smart($updBy).", NOW(),"
					. " ".quote_smart($updBy).""
					. " )";
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$pictogramID = $db->sql_nextid();
			//=====================================================================
			$msg = ($resIns) ? 'Insert Successfully' : 'Insert Unsuccessfully';
			//=====================================================================
		}
		func_add_audittrail($updBy,$msg.' ['.$pictogramID.']','Admin-Hazard Pictogram','tbl_Hazard_Pictogram,'.$pictogramID);
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href="hazPictogramList.php";
			</script>
			';
		exit();
	}
	//=====================================================================
	$picDesc = '';
	$picUrl = '';
	$typeID = 0;
	$active = 1;
	//=====================================================================
	if($pictogramID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Hazard_Pictogram';
		$dataWhere = array("AND Pictogram_ID" => " = ".quote_smart($pictogramID)."");
		$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere); 
		//=====================================================================
		foreach($arrayData as $detail){
			$picDesc = $detail['Pictogram_Desc'];
			$picUrl = $detail['Pictogram_Url'];
			$typeID = $detail['Type_ID'];
			$active = $detail['Active'];
		}
		//=====================================================================
	}
	//=====================================================================
?>
	<script language="Javascript">
	$(document).ready(function() {
		$('#div_rel a[rel]').each(function() {
			$(this).qtip({
				content: { url: $(this).attr('rel'), title: { text: $(this).attr('name') } },
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: { name:'blue', border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_HAZARD_PICTOGRAM .' :: '. $addEdit); ?>
	<form name="myForm" action="" method="post" enctype="multipart/form-data">
	<div id="div_rel">
	<?= form_input('pictogramID',$pictogramID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PICTOGRAM_DESC ?></td>
			<td width="80%"><?php
				$param1 = 'picDesc';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$picDesc;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PICTOGRAM_UPLOAD ?></td>
			<td><?php
				$param1 = 'picUrl';
				$param2 = '';
				$param3 = array('type'=>'file');
				echo form_input($param1,$param2,$param3);
			?><span class="formInfo"><a href="#" rel="<?= $sys_config['admin_path'] ?>help.php?help=pictogram" name="<?= _LBL_HELP_PICTOGRAM_TITLE ?>" >?</a></span></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_TYPE_NAME ?></td>
			<td><?php
				$param1 = 'typeID';
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Type','Type_ID','Type_Name');//($tblName='',$colID='',$colDesc='',$colWhere=array())
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$typeID;
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td><?php
				$param1 = 'active';
				$param2 = $arrStatusAktif;
				$param3 = isset($_POST[$param1])?$_POST[$param1]:$active;
				$param4 = '&nbsp;';
				echo form_radio($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				$param1 = 'save';
				$param2 = _LBL_SAVE;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
				echo '&nbsp;';
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'hazPictogramList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</div>
	</form>