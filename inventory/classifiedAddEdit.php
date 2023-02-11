<?php
	//****************************************************************/
	// filename: classifiedAddEdit.php
	// description: add & edit the classified chemical
	//****************************************************************/
	include_once "../includes/sys_config.php";
	/*********/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*********/
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*********/
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_header.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*********/
	func_header("",
				$sys_config["includes_path"]."css/global.css,". // css
				"",
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."javascript/js_validate.js,". // javascript
				$sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
				"",
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				"", // str url frame top
				"", // str url frame left
				"", // str url frame content
				false // int top for [enter]
				);
	/*********/
	$userLevelID = $_SESSION["user"]["Level_ID"];
	if($userLevelID>2) exit();
	/*********/
	if(isset($_POST["chemicalID"])) $chemicalID = $_POST["chemicalID"];
	elseif(isset($_GET["chemicalID"])) $chemicalID = $_GET["chemicalID"];
	else $chemicalID = 0;
	/*********/
	if(isset($_POST["hidrow1"])) $hidrow1 = $_POST["hidrow1"];
	else $hidrow1 = 1;
	/*********/
	if(isset($_POST["newhidrow1"])) $newhidrow1 = $_POST["newhidrow1"];
	else $newhidrow1 = 1;
	/*********/
	if(isset($_POST["hidrow2"])) $hidrow2 = $_POST["hidrow2"];
	else $hidrow2 = 1;
	/*********/
	if(isset($_POST["newhidrow2"])) $newhidrow2 = $_POST["newhidrow2"];
	else $newhidrow2 = 1;
	/*********/
	if(isset($_POST["hidrow3"])) $hidrow3 = $_POST["hidrow3"];
	else $hidrow3 = 1;
	/*********/
	if(isset($_POST["newhidrow3"])) $newhidrow3 = $_POST["newhidrow3"];
	else $newhidrow3 = 1;
	/*********/
	if($chemicalID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	/*********/
	if(isset($_POST["save"])){
		$classifiedID 		= isset($_POST["classifiedID"])?$_POST["classifiedID"]:"";
		$lbl_statementID 	= isset($_POST["lbl_statementID"])?$_POST["lbl_statementID"]:array();
		$lbl_signalID 		= isset($_POST["lbl_signalID"])?$_POST["lbl_signalID"]:"";
		if($lbl_signalID=="") $lbl_signalID = 0;
		$lbl_pictogramID	= isset($_POST["lbl_pictogramID"])?$_POST["lbl_pictogramID"]:array();
		$updBy = $_SESSION["user"]["Usr_ID"];
		$msg = "";
		/*********/
		$categoryIDs	= isset($_POST["catIDs"])?$_POST["catIDs"]:array();
		$arrCategoryIDs	= "";
		// print_r($categoryIDs);
		foreach($categoryIDs as $id){
			if($id!=""){
				if($arrCategoryIDs!=""){ $arrCategoryIDs .= ","; }
				$arrCategoryIDs .= $id;
			}
		}
		/*********/
		$arrStatementIDs = '';
		foreach($lbl_statementID as $detail){
			if($arrStatementIDs!='') $arrStatementIDs .= ',';
			$arrStatementIDs .= $detail;
		}
		/*********/
		$arrPictogramIDs = '';
		foreach($lbl_pictogramID as $detail){
			if($arrPictogramIDs!='') $arrPictogramIDs .= ',';
			$arrPictogramIDs .= $detail;
		}
		/*********/
		if($classifiedID!=''){
			$sqlUpd	= "UPDATE tbl_Chemical_Classified SET"
					. " Category_ID = ".quote_smart($arrCategoryIDs).","
					. " Lbl_Statement_ID = ".quote_smart($arrStatementIDs).","
					. " Lbl_Signal_ID = ".quote_smart($lbl_signalID).","
					. " Lbl_Pictogram_ID = ".quote_smart($arrPictogramIDs).","
					. " UpdateBy = ".quote_smart($updBy).","
					. " UpdateDate = NOW()"
					. " WHERE Classified_ID = ".quote_smart($classifiedID)." AND Chemical_ID = ".quote_smart($chemicalID)." LIMIT 1"
					;
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
			if($resUpd) $msg = "Update Successfully";
			else $msg = "Update Unsuccessfully";
		}else{
			$sqlIns	= "INSERT INTO tbl_Chemical_Classified ("
					. " Category_ID,Lbl_Statement_ID,Lbl_Signal_ID,Lbl_Pictogram_ID,"
					. " Chemical_ID,"
					. " RecordBy,RecordDate,UpdateBy,UpdateDate,"
					. " Active"
					. " ) VALUES ("
					. " ".quote_smart($arrCategoryIDs).",".quote_smart($arrStatementIDs).",".quote_smart($lbl_signalID).",".quote_smart($arrPictogramIDs).","
					. " ".quote_smart($chemicalID).","
					. " ".quote_smart($updBy).",NOW(),".quote_smart($updBy).",NOW(),"
					. " 1"
					. " )";
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$classifiedID = $db->sql_nextid();
			/*********/
			if($resIns) $msg = "Insert Successfully";
			else $msg = "Insert Unsuccessfully";
			/*********/
			$sqlUpd	= "UPDATE tbl_Chemical_Classified SET"
					. " Active = 0, UpdateBy = ".quote_smart($updBy).", UpdateDate = NOW()"
					. " WHERE Classified_ID != ".quote_smart($classifiedID)." AND Chemical_ID = ".quote_smart($chemicalID)."";
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
		}
		func_add_audittrail($updBy,$msg." [".$classifiedID."]","Inventory-Chemical Classified","tbl_Chemical_Classified,".$classifiedID);
		/*********/
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href="classifiedList.php";
			</script>
			';	
		exit();
	}
	/*********/
	$chemName 		= "";
	$chemIUPAC 		= "";
	$chemCAS 		= "";
	$classifiedID 	= "";
	$categoryID1 	= "";
	$categoryID2 	= "";
	$categoryID3 	= "";
	$statementID 	= "";
	$signalID	 	= "";
	$pictogramID	= "";
	$arrClass[]		= "";
	/*********/
	if($chemicalID!=0){
		$dataColumns = "*";
		$dataTable = "tbl_Chemical c";
		$dataJoin = array("tbl_Chemical_Classified cc"=>"c.Chemical_ID = cc.Chemical_ID");
		$dataWhere = array("AND c.Chemical_ID"=>"= ".quote_smart($chemicalID),"AND cc.Active"=>"= 1");
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		//==========================================================
		foreach($arrayData as $detail){
			$chemName 		= $detail["Chemical_Name"];
			$chemIUPAC 		= $detail["Chemical_IUPAC"];
			$chemCAS 		= $detail["Chemical_CAS"];
			$classifiedID 	= $detail["Classified_ID"];
			
			$catIDs	= $detail["Category_ID"];
			$ex_catIDs	= explode(",",$catIDs);
			
			foreach($ex_catIDs as $id){
				$tID = _get_StrFromCondition("tbl_Hazard_Category","Type_ID","Category_ID",$id);
				if($tID==1){
					$categoryID1 .= ($categoryID1!="") ? ",".$id : $id;
				}
				elseif($tID==2){
					$categoryID2 .= ($categoryID2!="") ? ",".$id : $id;
				}
				elseif($tID==3){
					$categoryID3 .= ($categoryID3!="") ? ",".$id : $id;
				}
			}
			$statementID 	= $detail["Lbl_Statement_ID"];
			$signalID	 	= $detail["Lbl_Signal_ID"];
			$pictogramID 	= $detail["Lbl_Pictogram_ID"];
		}
	}
	/*********/
	$str_onClick = "getChemID();";
?>
	<script language="Javascript">
	function addRow(n){
		var d = document;
		var row = d.getElementById(n).value;
		d.getElementById(n).value = parseInt(row) + 1;
		d.myForm.submit();
	}
	function emptyRow(el){
		var d = document;
		d.getElementById(el).value = '';
	}
	function formSubmit(){
		document.myForm.submit();
	}
	$(document).ready(function() {
		$('a[title]').each(function() {
			$(this).qtip({
				content: {
					//url: $(this).attr('rel'), // Use the rel attribute of each element for the url to load
					title: {
					   text: '<?= _LBL_DESCRIPTION_FOR ?>: ' + $(this).text(), // Give the tooltip a title using each elements text
					  // button: 'Close' // Show a close link in the title
					},
				},
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: { max: 350, min: 0 },
				},
			});
		});
		$('#imgDisplay a[rel]').each(function() {
			$(this).qtip({
				content: {
					url: $(this).attr('rel'), // Use the rel attribute of each element for the url to load
					title: {
						text: '<?= _LBL_PICTOGRAM_FOR ?>: ' + $(this).text(), // Give the tooltip a title using each elements text
						//button: 'Close' // Show a close link in the title
					},
				},
				position: { corner: { tooltip: 'bottomMiddle', target: 'topMiddle' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					textAlign: 'center',
				},
			});
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	function getChemID(){
		url = '<?= $sys_config['inventory_path'] ?>chemicalSearch.php';
		funcPopup(url,'900','600');
	}
	</script>
	<?php func_window_open2(_LBL_CLASSIFIED_CHEM .' :: '. $addEdit); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<?= form_input('chemicalID',$chemicalID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td colspan="3" class="nama"><?php
				$param1 = 'chemName';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemName;
				$param3 = array('type'=>'text','readonly'=>'readonly','style'=>'width:50%;','onClick'=>$str_onClick);
				echo form_input($param1,$param2,$param3); ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td colspan="3" class="nama"><?php
				$param1 = 'chemCAS';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemCAS;
				$param3 = array('type'=>'text','readonly'=>'readonly','onClick'=>$str_onClick);
				echo form_input($param1,$param2,$param3); 
			?></td>
		</tr>
	</table>
	<br />
	<?= form_input('hidrow1',$hidrow1,array('type'=>'hidden')); ?>
	<?= form_input('hidrow2',$hidrow2,array('type'=>'hidden')); ?>
	<?= form_input('hidrow3',$hidrow3,array('type'=>'hidden')); ?>
	<?php
		//=============================================
		$countRec1 = 0;
		if($categoryID1!=''){
			$arrCategoryID1 = explode(',',$categoryID1);
			$countRec1 = count($arrCategoryID1);
		}
		$rowspan1 = $hidrow1+$countRec1;
		//=============================================
		$countRec2 = 0;
		if($categoryID2!=''){
			$arrCategoryID2 = explode(',',$categoryID2);
			$countRec2 = count($arrCategoryID2);
		}
		$rowspan2 = $hidrow2+$countRec2;
		//=============================================
		$countRec3 = 0;
		if($categoryID3!=''){
			$arrCategoryID3 = explode(',',$categoryID3);
			$countRec3 = count($arrCategoryID3);
		}
		$rowspan3 = $hidrow3+$countRec3;
		//=============================================
		$subSelect = '(SELECT Class_Name FROM tbl_Hazard_Classification hc WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 0,1)';
		//=============================================
	?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="30">
			<td class="label title" colspan="2"><?= _LBL_CLASSIFICATION_CODE ?></td>
		</tr>
		<tr class="contents">
			<td class="label" <?= ($rowspan1>1)?'rowspan="'. $rowspan1 .'"':'' ?> width="20%"><?= _LBL_PHY_HAZARD ?> <a href="#" onClick="addRow('hidrow1');">Add</a></td>
			<?php
				$i = 1;
				
				if(isset($arrCategoryID1)){
					foreach($arrCategoryID1 as $value){
						//==========================================================================================
						if($i>1){ echo '<tr class="contents" width="80%">'; }
						echo '<td>';
						//==========================================================================================
						$param1 = 'categoryID1_'.$i;
						// $param2 = _get_arrayParent('tbl_Hazard_Type','Type_ID','Type_Name',array('AND Active'=>'= 1'),'Type_ID');
						$param2 = '';
						$param3 = _get_arraySelectParent('tbl_Hazard_Category','Category_ID',
									"CONCAT_WS(': ',".$subSelect.",Category_Name)",
									'Type_ID',array('AND Active'=>'= 1','AND Type_ID'=>'= 1'),
									$subSelect.', Category_Name'
								);
						$param4 = isset($_POST[$param1])?$_POST[$param1]:$value;
						$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
						//==========================================================================================
						echo form_select($param1,$param2,$param3,$param4,$param5);
						//==========================================================================================
						if($param4!=''){
							$arrClass[] = $param4;
							echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
						}
						echo '</td></tr>';
						$i++;
					}
				}
				if($i==0) $i++;
				else $newhidrow1 = $hidrow1+$i;
				for(;$i<$newhidrow1;$i++){
					if($i>1){ echo '<tr class="contents" width="80%">'; }
					echo '<td>';
					//==========================================================================================
					$param1 = 'categoryID1_'.$i;
					$param2 = '';
					$param3 = _get_arraySelectParent('tbl_Hazard_Category','Category_ID',
								"CONCAT_WS(': ',".$subSelect.",Category_Name)",
								'Type_ID',array('AND Active'=>'= 1','AND Type_ID'=>'= 1'),
								$subSelect.', Category_Name'
							);
					$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
					//==========================================================================================
					echo form_select($param1,$param2,$param3,$param4,$param5);
					//==========================================================================================
					if($param4!=''){
						$arrClass[] = $param4;
						echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
					}
					echo '</td></tr>';
				}
				
			?>
		<tr class="contents">
			<td class="label" <?= ($rowspan2>1)?'rowspan="'. $rowspan2 .'"':'' ?> width="20%"><?= _LBL_HEALTH_HAZARD ?> <a href="#" onClick="addRow('hidrow2');">Add</a></td>
			<?php
				$i = 1;
				
				if(isset($arrCategoryID2)){
					foreach($arrCategoryID2 as $value){
						//==========================================================================================
						if($i>1){ echo '<tr class="contents" width="80%">'; }
						echo '<td>';
						//==========================================================================================
						$param1 = 'categoryID2_'.$i;
						$param2 = '';
						$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
									"CONCAT_WS(': ',".$subSelect.",Category_Name)",
									array('AND Active'=>'= 1','AND Type_ID'=>'= 2'),
									$subSelect.', Category_Name'
								);
						$param4 = isset($_POST[$param1])?$_POST[$param1]:$value;
						$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
						//==========================================================================================
						echo form_select($param1,$param2,$param3,$param4,$param5);
						//==========================================================================================
						if($param4!=''){
							$arrClass[] = $param4;
							echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
						}
						echo '</td></tr>';
						$i++;
					}
				}
				if($i==0) $i++;
				else $newhidrow2 = $hidrow2+$i;
				for(;$i<$newhidrow2;$i++){
					if($i>1){ echo '<tr class="contents" width="80%">'; }
					echo '<td>';
					//==========================================================================================
					$param1 = 'categoryID2_'.$i;
					$param2 = '';
					$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
								"CONCAT_WS(': ',".$subSelect.",Category_Name)",
								array('AND Active'=>'= 1','AND Type_ID'=>'= 2'),
								$subSelect.', Category_Name'
							);
					$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
					//==========================================================================================
					echo form_select($param1,$param2,$param3,$param4,$param5);
					//==========================================================================================
					if($param4!=''){
						$arrClass[] = $param4;
						echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
					}
					echo '</td></tr>';
				}
				
			?>
		<tr class="contents">
			<td class="label" <?= ($rowspan3>1)?'rowspan="'. $rowspan3 .'"':'' ?> width="20%"><?= _LBL_ENV_HAZARD ?> <a href="#" onClick="addRow('hidrow3');">Add</a></td>
			<?php
				$i = 1;
				
				if(isset($arrCategoryID3)){
					foreach($arrCategoryID3 as $value){
						//==========================================================================================
						if($i>1){ echo '<tr class="contents" width="80%">'; }
						echo '<td>';
						//==========================================================================================
						$param1 = 'categoryID3_'.$i;
						$param2 = '';
						$param3 = _get_arraySelectParent('tbl_Hazard_Category','Category_ID',
									"CONCAT_WS(': ',".$subSelect.",Category_Name)",
									'Type_ID',array('AND Active'=>'= 1','AND Type_ID'=>'= 3'),
									$subSelect.', Category_Name'
								);
						$param4 = isset($_POST[$param1])?$_POST[$param1]:$value;
						$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
						//==========================================================================================
						echo form_select($param1,$param2,$param3,$param4,$param5);
						//==========================================================================================
						if($param4!=''){
							$arrClass[] = $param4;
							echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
						}
						echo '</td></tr>';
						$i++;
					}
				}
				if($i==0) $i++;
				else $newhidrow3 = $hidrow3+$i;
				for(;$i<$newhidrow3;$i++){
					if($i>1){ echo '<tr class="contents" width="80%">'; }
					echo '<td>';
					//==========================================================================================
					$param1 = 'categoryID3_'.$i;
					$param2 = '';
					$param3 = _get_arraySelectParent('tbl_Hazard_Category','Category_ID',
								"CONCAT_WS(': ',".$subSelect.",Category_Name)",
								'Type_ID',array('AND Active'=>'= 1','AND Type_ID'=>'= 3'),
								$subSelect.', Category_Name'
							);
					$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
					//==========================================================================================
					echo form_select($param1,$param2,$param3,$param4,$param5);
					//==========================================================================================
					if($param4!=''){
						$arrClass[] = $param4;
						echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
					}
					echo '</td></tr>';
				}
				
			?>
			
		<?php 
			$countArr = count($arrClass);
			// print_r($arrClass);
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_STATEMENT_CODE ?></td>
			<td><?php 
				$statementIDs = '';
				//==========================================================================================
				foreach($arrClass as $key => $value){
					$sql = "SELECT Statement_ID FROM tbl_Hazard_Category WHERE 1 AND Category_ID = ".quote_smart($value);
					$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
					while($row = $db->sql_fetchrow($res)){
						if(!empty($row['Statement_ID'])){
							if($statementIDs!='') $statementIDs .= ',';
							$statementIDs .= $row['Statement_ID'];
						}
					}
				}
				//==========================================================================================
				if($statementIDs!=''){
					$arrStatementIDs = explode(',',$statementIDs);
					$param1 = 'statementID';
					$param2 = _get_arraySelect(
									'tbl_Hazard_Statement','Statement_ID','Statement_Code',
									array('AND Statement_ID'=>'IN ('.$statementIDs.')','AND Active'=>'= 1')
								);
					$param3 = $arrStatementIDs;
					$param4 = '<br />';
					$param5 = '';
					//==========================================================================================
					//echo form_checkbox($param1,$param2,$param3,$param4,$param5);
					$form_rc = '';
					$i=0;
					//====================================
					if(is_array($param2)){
						$maxArr = count($param2);
						$form_rc .= '<table width="100%">';
						if($i%8==1){ $form_rc .= '<tr>'; }
						//====================================
						foreach($param2 as $opt_val){
							$i++;
							$form_rc .= '<td width="12.5%"><label><input type="checkbox" name="'.$param1.'[]" id="'.$param1.'" ';
							$form_rc .= in_array($opt_val['value'],$param3)?'checked':'';
							$form_rc .= ' value="'.$opt_val['value'].'" onClick="'.$param5.'" disabled /><a href="#"';
							$form_rc .= ' title="'._get_StrFromCondition('tbl_Hazard_Statement','Statement_Desc','Statement_ID',$opt_val['value']).'"';
							$form_rc .= ' >'.$opt_val['label'].'</label>';
							$form_rc .= '</td>';
							//====================================
							if($maxArr==$i && $i%8!=0){
								$emptyCell = 8-($i%8); 
								for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
							}
							//====================================
							if($i%8==0){ $form_rc .= '</tr>'; }
						}
						//====================================
						$form_rc .= '</table>';
						//====================================
					}
					echo $form_rc;
				}
				//==========================================================================================
			?></td>
		</tr>
		<?= form_input('classifiedID',$classifiedID,array('type'=>'hidden')); ?>
		<tr class="contents" height="30">
			<td class="label title" colspan="2"><?= _LBL_LABELLING ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATEMENT_CODE ?></td>
			<td><?php
				$statementIDs = '';
				foreach($arrClass as $key => $value){
					$sql = "SELECT Statement_ID FROM tbl_Hazard_Category WHERE 1 AND Category_ID = ".quote_smart($value)."";
					$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
					while($row = $db->sql_fetchrow($res)){
						if(!empty($row['Statement_ID'])){
							if($statementIDs!='') $statementIDs .= ',';
							$statementIDs .= $row['Statement_ID'];
						}
					}
				}
				//==========================================================================================
				if($statementIDs!=''){
					$arrStatementIDs = explode(',',$statementIDs);
					$lbl_statementID = array();
					if($statementID!='') $lbl_statementID = explode(',',$statementID);
					$param1 = 'lbl_statementID';
					$param2 = _get_arraySelect(
									'tbl_Hazard_Statement','Statement_ID','Statement_Code',
									array('AND Statement_ID'=>'IN ('.$statementIDs.')','AND Active'=>'= 1')
								);
					$param3 = isset($_POST[$param1])?$_POST[$param1]:$lbl_statementID;
					//==========================================================================================
					//print_r($arrStatementIDs);echo '<br />';print_r($param2);echo '<br />';print_r($param3);echo '<br />';
					$param4 = '<br />';
					$param5 = '';
					$form_rc = '';
					$i=0;
					if(is_array($param2)){
						$maxArr = count($param2);
						$form_rc .= '<table width="100%">';
						if($i%8==1){ $form_rc .= '<tr>'; }
						//====================================
						foreach($param2 as $opt_val){
							$i++;
							$form_rc .= '<td width="12.5%"><label><input type="checkbox" name="'.$param1.'[]" id="'.$param1.'" ';
							$form_rc .= in_array($opt_val['value'],$param3)?'checked':'';
							$form_rc .= ' value="'.$opt_val['value'].'" onClick="'.$param5.'" /><a href="#"';
							$form_rc .= ' title="'._get_StrFromCondition('tbl_Hazard_Statement','Statement_Desc','Statement_ID',$opt_val['value']).'"';
							$form_rc .= ' >'.$opt_val['label'].'</label>';
							$form_rc .= '</td>';
							//====================================
							if($maxArr==$i && $i%8!=0){
								$emptyCell = 8-($i%8); 
								for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
							}
							//====================================
							if($i%8==0){ $form_rc .= '</tr>'; }
						}
						//====================================
						$form_rc .= '</table>';
						//====================================
					}
					echo $form_rc;
				}
				//==========================================================================================
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SIGNAL_WORD ?></td>
			<td><?php
				$param1 = 'lbl_signalID';
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Signal','Signal_ID','Signal_Name',array('AND Active'=>'= 1'));
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$signalID;
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HAZARD_PICTOGRAM ?></td>
			<td><div id="imgDisplay"><?php 
				$pictogramIDs = '';
				//==========================================================================================
				foreach($arrClass as $key => $value){
					$sql = "SELECT Pictogram_ID FROM tbl_Hazard_Category WHERE 1 AND Category_ID = ".quote_smart($value)."";
					$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
					while($row = $db->sql_fetchrow($res)){
						if(!empty($row['Pictogram_ID'])){
							if($pictogramIDs!='') $pictogramIDs .= ',';
							$pictogramIDs .= $row['Pictogram_ID'];
						}
					}
				}
				//==========================================================================================
				if($pictogramIDs!=''){
					$arrPictogramIDs = explode(',',$pictogramIDs);
					$lbl_pictogramID = array();
					if($pictogramID!='') $lbl_pictogramID = explode(',',$pictogramID);
					$param1 = 'lbl_pictogramID';
					$param2 = _get_arraySelect(
									'tbl_Hazard_Pictogram','Pictogram_ID','Pictogram_Desc',
									array('AND Pictogram_ID'=>'IN ('.$pictogramIDs.')','AND Active'=>'= 1')
								);
					$param3 = isset($_POST[$param1])?$_POST[$param1]:$lbl_pictogramID;
					$param4 = '';
					$param5 = '';
					//==========================================================================================
					$form_rc = '';
					$i=0;
					if(is_array($param2)){
						$maxArr = count($param2);
						$form_rc .= '<table width="100%">';
						//====================================
						foreach($param2 as $opt_val){
							$i++;
							if($i%4==1){ $form_rc .= '<tr>'; }
							$form_rc .= '<td width="25%"><label><input type="checkbox" name="'.$param1.'[]" id="'.$param1.'" ';
							$form_rc .= in_array($opt_val['value'],$param3)?'checked':'';
							$form_rc .= ' value="'.$opt_val['value'].'" onClick="'.$param5.'" /><a href="#"';
							$form_rc .= ' rel="'.$sys_config['includes_path'].'getImage.php?id='.$opt_val['value'].'">'.$opt_val['label'].'</a></label>';
							$form_rc .= '</td>';
							if($maxArr==$i && $i%4!=0){
								$emptyCell = 4-($i%4); 
								for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
							}
							if($i%4==0){ $form_rc .= '</tr>'; }
						}
						//====================================
						$form_rc .= '</table>';
						//====================================
					}
					echo $form_rc;
				}
				//==========================================================================================
			?></div></td>
		</tr>
	</table>
	<?= form_input('newhidrow1',$newhidrow1,array('type'=>'hidden')); ?>
	<?= form_input('newhidrow2',$newhidrow2,array('type'=>'hidden')); ?>
	<?= form_input('newhidrow3',$newhidrow3,array('type'=>'hidden')); ?>
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
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'classifiedList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>