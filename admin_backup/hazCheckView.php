<?php
	//****************************************************************/
	// filename: hazCheckView.php
	// description: show detail of the hazard checking selected
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
	include_once $sys_config["includes_path"]."func_hazard.php";
	/*********/
	func_header("",
				"", // css"",
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
				"", // javascript
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
	if(isset($_POST["codeID"])) $codeID = $_POST["codeID"];
	elseif(isset($_GET["codeID"])) $codeID = $_GET["codeID"];
	else $codeID = 0;
	/*********/
	if(isset($_POST["hidrow1"])) $hidrow1 = $_POST["hidrow1"];
	else $hidrow1 = 1;
	if(isset($_POST["hidrow2"])) $hidrow2 = $_POST["hidrow2"];
	else $hidrow2 = 1;
	if(isset($_POST["hidrow3"])) $hidrow3 = $_POST["hidrow3"];
	else $hidrow3 = 1;
	if(isset($_POST["hidrow4"])) $hidrow4 = $_POST["hidrow4"];
	else $hidrow4 = 1;
	/*********/
	if($codeID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	/*********/
	$casNo = "";
	$pictogramID = array();
	$signalID = "";
	$hStatementID = array();
	$pStatementID1 = array();
	$psRow1 = 0;
	$pStatementID2 = array();
	$psRow2 = 0;
	$pStatementID3 = array();
	$psRow3 = 0;
	$pStatementID4 = array();
	$psRow4 = 0;
	$typeID = 0;
	$active = 1;
	/*********/
	if($codeID!=0){
		$dataColumns = "CAS_No,Pictogram_ID,Signal_ID,Statement_ID,"
					.	"PreStatement_ID1,PreStatement_ID2,PreStatement_ID3,PreStatement_ID4,Type_ID,Active";
		$dataTable = "tbl_Hazard_Codes";
		$dataWhere = array("AND Code_ID"=>" = ".quote_smart($codeID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere); 
		/*********/
		foreach($arrayData as $detail){
			$casNo = $detail["CAS_No"];
			$pictogramID = explode(",",$detail["Pictogram_ID"]);
			$signalID = $detail["Signal_ID"];
			$hStatementID = explode(",",$detail["Statement_ID"]);
			$pStatementID1 = explode(";",$detail["PreStatement_ID1"]);
			$psRow1 = count($pStatementID1);
			$pStatementID2 = explode(";",$detail["PreStatement_ID2"]);
			$psRow2 = count($pStatementID2);
			$pStatementID3 = explode(";",$detail["PreStatement_ID3"]);
			$psRow3 = count($pStatementID3);
			$pStatementID4 = explode(";",$detail["PreStatement_ID4"]);
			$psRow4 = count($pStatementID4);
			$typeID = $detail["Type_ID"];
			$active = $detail["Active"];
		}
	}
	/*********/
	$fRow1 = $hidrow1+$psRow1;
	$fRow2 = $hidrow2+$psRow2;
	$fRow3 = $hidrow3+$psRow3;
	$fRow4 = $hidrow4+$psRow4;
	/*********/
?>
	<script language="Javascript">
	$().ready(function() {
		$("#div_img a[rel]").each(function() {
			$(this).qtip({
				content: {
					url: $(this).attr("rel"), // Use the rel attribute of each element for the url to load
					title: {
					   text: "Pictogram for: " + $(this).text() // Give the tooltip a title using each elements text
					}
				},
				position: { corner: { tooltip: "topMiddle", target: "bottomMiddle" } },

				style: {
					name: "blue",  // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					textAlign: "center"
				}
			});
		});
		$("#div_hs a[rel]").each(function() {
			$(this).qtip({
				content: { url: $(this).attr("rel"), title: { text: "Description for: " + $(this).text() } },
				position: { corner: { tooltip: "topMiddle", target: "bottomMiddle" } },
				style: { name: "blue", border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		$("label[title]").each(function() {
			$(this).qtip({
				//content: { title: { text: $(this).attr("title") } },
				position: { corner: { tooltip: "bottomLeft", target: "topLeft" } },
				style: { name: "blue",  border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_HAZARD_CHECKING .' :: '. _LBL_VIEW); ?>
	<form name="myForm" action="" method="post">
	<div
	<?= form_input('codeID',$codeID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td><label><?= $casNo; ?></label></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_TYPE_NAME ?></td>
			<td width="80%"><label><?= _get_StrFromCondition('tbl_Hazard_Type','Type_Name','Type_ID',$typeID); ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HAZARD_PICTOGRAM ?></td>
			<td><?php 
				echo '<div id="div_img">';
				//print_r($pictogramID);
				foreach($pictogramID as $detail){
					$labelPic = _get_StrFromCondition('tbl_Hazard_Pictogram','Pictogram_Desc','Pictogram_ID',$detail);
					echo '<a href="#" rel="'.$sys_config['includes_path'].'getImage.php?id='.$detail.'">'.$labelPic.'</a>; ';
				}
				echo '</div>'; 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SIGNAL_WORD ?></td>
			<td><label><?= _get_StrFromCondition('tbl_Hazard_Signal','Signal_Name','Signal_ID',$signalID); ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HAZARD_STATEMENT ?></td>
			<td><?php 
				echo '<div id="div_hs">';
				//print_r($hStatementID);
				foreach($hStatementID as $detail){
					$labelStatement = _get_StrFromCondition('tbl_Hazard_Statement','Statement_Code','Statement_ID',$detail);
					echo '<a href="#" rel="help.php?help=showSttmnt&sID='.$detail.'" >'.$labelStatement.'</a>; ';
				}
				echo '</div>'; 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label title" colspan="2"><?= _LBL_PRECAUTIONARY_STATEMENT ?></td>
		</tr>
		<?php
			$arrPreStatement = _get_arraySelect('tbl_Precaution_Type','Precaution_ID','Precaution_Name',array('AND Active'=>'= 1'),'Precaution_ID');
			foreach($arrPreStatement as $detail){
				$pID = $detail['value'];
				echo '<input type="hidden" name="pID[]" id="pID" value="'.$pID.'">';
		?>
		<tr class="contents">
			<td class="label"><?= $detail['label'] ?></td>
			<td><?php
				if($pID==1){ $existingData = $pStatementID1; }
				elseif($pID==2){ $existingData = $pStatementID2; }
				elseif($pID==3){ $existingData = $pStatementID3; }
				else{ $existingData = $pStatementID4; }
				//echo $maxRow.' ';
				echo '<table width="100%" cellspacing="1" cellpadding="3" class="contents">';
				echo 	'<thead>';
				echo 	'<tr class="contents">';
				echo 		'<td width="5%" class="label" align="center">No</td>';
				echo 		'<td width="30%" class="label">Statement Code</td><td width="55%" class="label">Statement Description</td>';
				echo 	'</tr>';
				echo 	'</thead>';
				
				$r = 0;
				foreach($existingData as $value){
					if($value!=''){
						$r++;
						/*********/
						$ex_value = explode("+",$value);
						$new_value = "";
						$new_desc = "";
						$new_title = "";
						foreach($ex_value as $value2){
							if($value2!=""){
								if($new_value!="") $new_value .= " + ";
								if($new_desc!="") $new_desc .= " [ + ] ";
								$new_value .= _get_StrFromCondition("tbl_Precaution_Statement","Statement_Code","Statement_ID",trim($value2));
								$new_title .= _get_StrFromCondition("tbl_Precaution_Statement","Statement_Code","Statement_ID",trim($value2)).", ";
								$new_desc .= _get_StrFromCondition("tbl_Precaution_Statement","Statement_Desc","Statement_ID",trim($value2));
							}
						}
						/*********/
						$inputNameID = "test_".$pID."_".$r;
						echo 	'<tr class="contents">';
						echo 		'<td align="center">'.$r.'</td>';
						echo 		'<td><label title="'. _get_Hazard_Statement($new_title) .'">'. $new_value .'</td>';
						echo 		'<td><label>'. $new_desc .'</label></td>';
						echo 	'</tr>';
					}
				}
				if($r==0){
					$r++;
					echo 	'<tr class="contents">';
					echo		'<td align="center">'. $r .'</td>';
					echo 		'<td>-</td>';
					echo 		'<td>-</td>';
					echo 	'</tr>';
				}
				echo '</table>';
				
			?>
			</td>
		</tr>
		<?php
			}
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td><label><?php 
					foreach($arrStatusAktif as $detail){
						if($detail['value']==$active) echo $detail['label']; 
					}
			?></label></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('hazCheckAddEdit.php?codeID=$codeID');");
				echo form_button("edit",_LBL_EDIT,$dOthers);
				/*********/
				echo "&nbsp;";
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('hazCheckList.php');");
				echo form_button("cancel",_LBL_CANCEL,$dOthers);
				/*********/
			?></td>
		</tr>
	</table>
	<br />
	</form>
