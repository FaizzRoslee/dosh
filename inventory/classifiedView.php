<?php
	//****************************************************************/
	// filename: classifiedView.php
	// description: view the details of classified chemical
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
				"", // css	
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
	if($userLevelID>=6) exit();
	/*********/
	if(isset($_POST["chemicalID"])) $chemicalID = $_POST["chemicalID"];
	elseif(isset($_GET["chemicalID"])) $chemicalID = $_GET["chemicalID"];
	else $chemicalID = 0;
	/*********/
	$chemName 		= "";
	$chemIUPAC 		= "";
	$chemCAS 		= "";
	$classifiedID 	= "";
	$categoryID 	= "";
	$signalID	 	= "";
	/*********/
	if($chemicalID!=0){
		$dataColumns = "*";
		$dataTable = "tbl_Chemical c";
		$dataJoin = array("tbl_Chemical_Classified cc"=>"c.Chemical_ID = cc.Chemical_ID");
		$dataWhere = array("AND c.Chemical_ID" => " = ".quote_smart($chemicalID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		/*********/
		foreach($arrayData as $detail){
			$chemName 		= $detail["Chemical_Name"];
			$chemIUPAC 		= $detail["Chemical_IUPAC"];
			$chemCAS 		= $detail["Chemical_CAS"];
			$classifiedID 	= $detail["Classified_ID"];
			$categoryID 	= $detail["Category_ID"];
			$statementID 	= $detail["Lbl_Statement_ID"];
			$signalID	 	= $detail["Lbl_Signal_ID"];
			$pictogramID 	= $detail["Lbl_Pictogram_ID"];
		}
	}
	/*********/
	$str_onClick = "getChemID();";
?>
	<script language="Javascript">
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
		$('label[title]').each(function() {
			$(this).qtip({
				position: { corner: { tooltip: 'topLeft', target: 'bottomLeft' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					//width: { max: 350, min: 0 },
					width: 350,
				},
			});
		});
		$('#imgDisplay a[rel]').each(function() {
			$(this).qtip({
				content: {
					url: $(this).attr('rel'), // Use the rel attribute of each element for the url to load
					title: {
					   text: '<?= _LBL_PICTOGRAM_FOR ?>: ' + $(this).text(), // Give the tooltip a title using each elements text
						//button: 'Close'  // Show a close link in the title
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
	</script>
	<?php func_window_open2(_LBL_CLASSIFIED_CHEM .' :: '. _LBL_VIEW ); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<?= form_input('chemicalID',$chemicalID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td colspan="3" class="nama"><?= $chemName; ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td colspan="3" class="nama"><?= $chemCAS; ?></td>
		</tr>
	</table>
	<br />
	<?php
		$countRec = 0;
		if($categoryID!=''){
			$arrCategoryID = explode(',',$categoryID);
			$countRec = count($arrCategoryID);
		}
		$rowspan = $countRec;
	?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="30">
			<td class="label" colspan="2"><?= _LBL_CLASSIFICATION ?></td>
		</tr>
		<tr class="contents">
			<td class="label" width="20%"><?= _LBL_HAZARD_CLASSIFICATION ?></td>
			<td width="80%"><label><?php
				if(isset($arrCategoryID)){
					$tyIDs = 0;
					foreach($arrCategoryID as $value){
						/*********/
						$tyID = _get_StrFromCondition('tbl_Hazard_Category','Type_ID','Category_ID',$value);
						if($tyID!=$tyIDs){
							if($tyID==1){ $clName = _LBL_PHY_HAZARD; }
							elseif($tyID==2){ $clName = _LBL_HEALTH_HAZARD; }
							elseif($tyID==3){ $clName = _LBL_ENV_HAZARD; }
							else{ $clName = '-'; }
							echo '<strong><u>'.$clName.'</u>:-</strong><br />';
						}
						$tyIDs = $tyID;
						/*********/
						echo _get_StrFromCondition('tbl_Hazard_Category',"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 0,1),Category_Name)",'Category_ID',$value);
						/*********/
						echo ';<br />';
						$arrClass[] = $value;
					}
				}
			?></label></td>
		</tr>
		<?php 
			$countArr = count($arrClass);
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_STATEMENT_CODE ?></td>
			<td><?php 
				$statementIDs = '';
				/*********/
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
				/*********/
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
					/*********/
					//echo form_checkbox($param1,$param2,$param3,$param4,$param5);
					$form_rc = '';
					$i=0;
					/*********/
					if(is_array($param2)){
						$maxArr = count($param2);
						$form_rc .= '<table width="100%">';
						if($i%8==1){ $form_rc .= '<tr>'; }
						/*********/
						foreach($param2 as $opt_val){
							$i++;
							$form_rc .= '<td width="12.5%"><label><a href="#"';
							$form_rc .= ' title="'._get_StrFromCondition('tbl_Hazard_Statement','Statement_Desc','Statement_ID',$opt_val['value']).'"';
							$form_rc .= ' >'.$opt_val['label'].'</label>';
							$form_rc .= '</td>';
							/*********/
							if($maxArr==$i && $i%8!=0){
								$emptyCell = 8-($i%8); 
								for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
							}
							/*********/
							if($i%8==0){ $form_rc .= '</tr>'; }
						}
						/*********/
						$form_rc .= '</table>';
						/*********/
					}
					echo $form_rc;
				}
				/*********/
			?></td>
		</tr>
		<tr class="contents" height="30">
			<td class="label title" colspan="2"><?= _LBL_LABELLING ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATEMENT_CODE ?></td>
			<td><?php
				/*********/
				if($statementID!=''){
					$lbl_statementID = array();
					if($statementID!='') $lbl_statementID = explode(',',$statementID);
					$param2 = _get_arraySelect(
									'tbl_Hazard_Statement','Statement_ID','Statement_Code',
									array('AND Statement_ID'=>'IN ('.$statementID.')','AND Active'=>'= 1')
								);
					/*********/
					$param5 = '';
					$form_rc = '';
					$i=0;
					if(is_array($param2)){
						$maxArr = count($param2);
						$form_rc .= '<table width="100%">';
						if($i%8==1){ $form_rc .= '<tr>'; }
						/*********/
						foreach($param2 as $opt_val){
							$i++;
							$form_rc .= '<td width="12.5%"><label><a href="#"';
							$form_rc .= ' title="'._get_StrFromCondition('tbl_Hazard_Statement','Statement_Desc','Statement_ID',$opt_val['value']).'"';
							$form_rc .= ' >'.$opt_val['label'].'</label>';
							$form_rc .= '</td>';
							/*********/
							if($maxArr==$i && $i%8!=0){
								$emptyCell = 8-($i%8); 
								for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
							}
							/*********/
							if($i%8==0){ $form_rc .= '</tr>'; }
						}
						/*********/
						$form_rc .= '</table>';
						/*********/
					}
					echo $form_rc;
				}else{ echo '<label>-</label>'; }
				/*********/
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SIGNAL_WORD ?></td>
			<td><label><?= ($signalID!=0) ? _get_StrFromCondition('tbl_Hazard_Signal','Signal_Name','Signal_ID',$signalID) : '-'; ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HAZARD_PICTOGRAM ?></td>
			<td><div id="imgDisplay"><?php 
				$pictogramIDs = '';
				/*********/
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
				/*********/
				if($pictogramID!=''){
					$lbl_pictogramID = array();
					if($pictogramID!="") $lbl_pictogramID = explode(",",$pictogramID);
					$param2 = _get_arraySelect(
									"tbl_Hazard_Pictogram","Pictogram_ID","Pictogram_Desc",
									array("AND Pictogram_ID"=>"IN (".$pictogramID.")","AND Active"=>"= 1")
								);
					/*********/
					$form_rc = '';
					$i=0;
					if(is_array($param2)){
						$maxArr = count($param2);
						$form_rc .= '<table width="100%">';
						/*********/
						foreach($param2 as $opt_val){
							$i++;
							if($i%4==1){ $form_rc .= '<tr>'; }
							$form_rc .= '<td width="20%"><label><a href="#"';
							$form_rc .= ' rel="'.$sys_config['includes_path'].'getImage.php?id='.$opt_val['value'].'">'.$opt_val['label'].'</a></label>';
							$form_rc .= '</td>';
							if($maxArr==$i && $i%4!=0){
								$emptyCell = 4-($i%4); 
								for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
							}
							if($i%4==0){ $form_rc .= '</tr>'; }
						}
						/*********/
						$form_rc .= '</table>';
						/*********/
					}
					echo $form_rc;
				}else{ echo '<label>-</label>'; }
				/*********/
			?></div></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				/*********/
				if($userLevelID<=2){
					$dOthers = array("type"=>"button","onClick"=>"redirectForm('classifiedAddEdit.php?chemicalID=".$chemicalID."');");
					echo form_button("edit",_LBL_EDIT,$dOthers);
					/*********/
					echo '&nbsp;';
				}
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('classifiedList.php');");
				echo form_button("cancel",_LBL_BACK,$dOthers);
			?></td>
		</tr>
	</table>
	<br />
	</form>