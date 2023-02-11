<?php
	//****************************************************************/
	// filename: approvedList.php
	// description: list approved submission for renewing
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
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*********/
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
	$userLevelID = $_SESSION["user"]["Level_ID"];
	if($userLevelID!=1&&$userLevelID!=5) exit();
	/*********/
	if(isset($_POST["chemicalID"])) $chemicalID = $_POST["chemicalID"];
	elseif(isset($_GET["chemicalID"])) $chemicalID = $_GET["chemicalID"];
	else $chemicalID = 0;
	/*********/
	func_header("",
				"", // css		
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."jquery/Tabbed/jquery-ui-tabs.js,". // javascript
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
?>

	<style type="text/css" title="currentStyle">
		.ui-tabs .ui-tabs-panel { padding: 10px }
	</style>
	<script language="Javascript">
	$(document).ready(function() {
		var $tabs = $("#tabs").tabs();
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2( _LBL_SUBMISSION_ACKNOWLEDGE ." :: ". _LBL_LIST_CHEMICAL ." :: ". _LBL_VIEW); ?>
	<form name="myForm" action="" method="post">
	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1" onClick=""><?= _LBL_CHEMICAL_DETAILS ?></a></li>
			<li><a href="#tabs-2" onClick=""><?= _LBL_SUPLLIER ?></a></li>
		</ul>
		<div id="tabs-1">
			<?php
				$chemName = _get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$chemicalID);
				$chemCas = _get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$chemicalID);
				$chemIupac = _get_StrFromCondition("tbl_Chemical","Chemical_IUPAC","Chemical_ID",$chemicalID);
				
				$dataColumns1 = "*";
				$dataTable1 = "tbl_Chemical_Classified";
				$dataJoin1 = "";
				$dataWhere1 = array("AND Chemical_ID" => "= ".quote_smart($chemicalID),"AND Active" => "= 1");
				$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1); 
				$classified = count($arrayData1);
			?>
			<table class="contents" cellspacing="1" cellpadding="3" width="100%">
				<tr class="contents">
					<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
					<td width="80%"><label><?= (!empty($chemName)?nl2br($chemName):"-"); ?></label></td>
				</tr>
				<tr class="contents">
					<td class="label"><?= _LBL_CAS_NO ?></td>
					<td><label><?= (!empty($chemCas)?$chemCas:"-"); ?></label></td>
				</tr>
				<tr class="contents">
					<td class="label"><?= _LBL_CHEM_IUPAC ?></td>
					<td><label><?= (!empty($chemIupac)?nl2br($chemIupac):nl2br($chemName)); ?></label></td>
				</tr>
				<tr class="contents">
					<td class="label"><?= _LBL_CLASSIFIED ?></td>
					<td><label><?= (($classified>0)?"<img src=\"".$sys_config["images_path"]."icons/tick.gif\" />":"&nbsp;"); ?></label></td>
				</tr>
			</table>
			<?php
				foreach($arrayData1 as $key1 => $detail1){
					$statementID 	= $detail1["Lbl_Statement_ID"];
					$signalID	 	= $detail1["Lbl_Signal_ID"];
					$pictogramID 	= $detail1["Lbl_Pictogram_ID"];
					$categoryID 	= $detail1["Category_ID"];
					
					$countRec = 0;
					if($categoryID!=""){
						$arrCategoryID = explode(",",$categoryID);
						$countRec = count($arrCategoryID);
					}
			?>
			<br />
			<table class="contents" cellspacing="1" cellpadding="3" width="100%">
				<tr class="contents">
					<td width="20%" class="label"><?= _LBL_CLASSIFICATION_CODE ?></td>
					<td width="80%"><label><?php
						if(isset($arrCategoryID)){
							$tyIDs = 0;
							foreach($arrCategoryID as $value){
								/*********/
								$tyID = _get_StrFromCondition("tbl_Hazard_Category","Type_ID","Category_ID",$value);
								if($tyID!=$tyIDs){
									if($tyID==1){ $clName = _LBL_PHY_HAZARD; }
									elseif($tyID==2){ $clName = _LBL_HEALTH_HAZARD; }
									elseif($tyID==3){ $clName = _LBL_ENV_HAZARD; }
									else{ $clName = "-"; }
									echo "<strong><u>".$clName."</u>:-</strong><br />";
								}
								$tyIDs = $tyID;
								/*********/
								echo _get_StrFromCondition("tbl_Hazard_Category","CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 0,1),Category_Name)","Category_ID",$value);
								/*********/
								echo ";<br />";
							}
						}
					?></label></td>
				</tr>
				<tr class="contents">
					<td class="label"><?= _LBL_SIGNAL_WORD ?></td>
					<td><label><?= ($signalID!=0) ? _get_StrFromCondition("tbl_Hazard_Signal","Signal_Name","Signal_ID",$signalID) : "-"; ?></td>
				</tr>
				<tr class="contents">
					<td class="label"><?= _LBL_HAZARD_PICTOGRAM ?></td>
					<td><?php 
						/*********/
						if($pictogramID!=""){
							$lbl_pictogramID = array();
							if($pictogramID!="") $lbl_pictogramID = explode(",",$pictogramID);
							$param2 = _get_arraySelect(
											"tbl_Hazard_Pictogram","Pictogram_ID","Pictogram_Desc",
											array("AND Pictogram_ID"=>"IN (".$pictogramID.")","AND Active"=>"= 1")
										);
							/*********/
							$form_rc = "";
							$i=0;
							if(is_array($param2)){
								$maxArr = count($param2);
								$form_rc .= "<table width=\"100%\">";
								/*********/
								foreach($param2 as $opt_val){
									$i++;
									if($i%4==1){ $form_rc .= "<tr>"; }
									$form_rc .= "<td width=\"25%\" align=\"center\"><label><img";
									$form_rc .= " src=\"" ._get_StrFromCondition("tbl_Hazard_Pictogram","Pictogram_Url","Pictogram_ID",$opt_val["value"]) ."\"";
									$form_rc .= " width=\"80%\" /><br /><strong>".$opt_val["label"]."</strong></label>";
									$form_rc .= "</td>";
									if($maxArr==$i && $i%4!=0){
										$emptyCell = 4-($i%4); 
										for($j=0;$j<$emptyCell;$j++){ $form_rc .= "<td width=\"25%\">&nbsp;</td>"; }
									}
									if($i%4==0){ $form_rc .= "</tr>"; }
								}
								/*********/
								$form_rc .= "</table>";
								/*********/
							}
							echo $form_rc;
						}else{ echo "<label>-</label>"; }
						/*********/
					?></td>
				</tr>
				<tr class="contents">
					<td class="label"><?= _LBL_STATEMENT_CODE ." : ". _LBL_HAZARD_STATEMENT ?></td>
					<td><?php
						/*********/
						if($statementID!=""){
							$param2 = _get_arraySelect(
											"tbl_Hazard_Statement","Statement_ID","Statement_Code",
											array("AND Statement_ID"=>"IN (".$statementID.")","AND Active"=>"= 1")
										);
							/*********/
							$param5 = "";
							$form_rc = "";
							$i=0;
							if(is_array($param2)){
								/*********/
								$form_rc .= "<table width=\"100%\">";
								foreach($param2 as $opt_val){
									$form_rc .= "
										<tr>
											<td width=\"10%\"><label><strong>". $opt_val["label"] ." :-</strong></label></td>
											<td width=\"90%\"><label>". trim(_get_StrFromCondition("tbl_Hazard_Statement","Statement_Desc","Statement_ID",$opt_val["value"])) .";</label></td>
										</tr>
										";
								}
								$form_rc .= "</table>";
								/*********/
							}
							echo $form_rc;
						}else{ echo "-"; }
						/*********/
					?></td>
				</tr>
			</table>
			<?php
				}
			?>
		
		</div>
		<div id="tabs-2">
		
			<?php
				/*********/
				//$dSubSelect = "SELECT Detail_ID FROM tbl_Submission_Chemical WHERE Chemical_ID = ".quote_smart($chemicalID);
				
				
				// linuxhouse_20220608
				// start penambahbaikan - 10_carian hilang - need more info from user - v003
				$detailID = _get_StrFromCondition("tbl_Submission_Chemical","GROUP_CONCAT(Detail_ID separator ',')","Chemical_ID",$chemicalID);
				$detailID = explode(',',$detailID);
				$detailID = array_filter($detailID);
				$detailID = implode(',',$detailID);
				/*********/
				$dataColumns2	= "s.Usr_ID, s.Submission_Submit_ID, sd.Detail_ID, s.Submission_ID";
				$dataTable2 = "tbl_Submission s";
				$dataJoin2 	= array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
				/*********/
				
				
// 				$dataWhereSub1 = array(
// 								"AND s.isDeleted"=>"= 0","AND s.Status_ID"=>"IN (31,32,33,51,41,42,43,52)",
// 								"AND Detail_ID"=>"IN (".$detailID.")"
// 							);
							
                
                // debug
                $empty = '';
                $dataWhereSub1 = array(
								"AND s.ApprovedDate"=>"<> ''","AND s.Status_ID"=>"IN (31,32,33,51,41,42,43,52)",
								"AND Detail_ID"=>"IN (".$detailID.")"
							);
							
							
				$dataWhereSub2	= array();
				if($userLevelID==5){ 
					$sID = _get_StrFromCondition("sys_User_Staff","State_ID","Usr_ID",$Usr_ID);
					if($sID==14 || $sID==16)
						$sID = "14,16";
					// $dataWhereSub2 = array("AND (SELECT State_Reg FROM sys_User_Client WHERE Usr_ID = s.Usr_ID LIMIT 1)"=>"= ". quote_smart($sID)); 
					$dataWhereSub2 = array("AND (SELECT State_Reg FROM sys_User_Client WHERE Usr_ID = s.Usr_ID LIMIT 1)"=>"IN ($sID)"); 
				}
				$dataWhere2 = array_merge($dataWhereSub1,$dataWhereSub2,array("GROUP BY"=>"Usr_ID","ORDER BY"=>"Submission_Submit_ID DESC"));
				/*********/
                $arrayData2 = _get_arrayData($dataColumns2,$dataTable2,$dataJoin2,$dataWhere2); 
                
                
                // debug
                var_dump($arrayData2);
                exit();
				
				
			?>
			<table class="contents" cellspacing="1" cellpadding="3" width="100%">
				<tr class="contents">
					<td class="label" width="5%" style="text-align:center;"><?= _LBL_NUMBER ?></td>
					<td class="label" width="30%"><?= _LBL_SUPPLIER_NAME ?></td>
					<td class="label" width="15%" style="text-align:center;"><?= _LBL_SUPPLIER_TYPE ?></td>
					<td class="label" width="20%" style="text-align:center;"><?= _LBL_SUBMISSION_ID ?></td>
					<?php /*
					<td class="label" width="15%" style="text-align:center;"><?= _LBL_DATE_NOLONGER ?></td>
					*/ ?>
				</tr>
				<?php
					$i = 0;
					foreach($arrayData2 as $detail2){ 
						$i++;
						
						$compName = _get_StrFromCondition("sys_User_Client","Company_Name","Usr_ID",$detail2["Usr_ID"]);
						$levelID = _get_StrFromCondition("sys_User","Level_ID","Usr_ID",$detail2["Usr_ID"]);

						$lvlName = "-";
						foreach($arrLevel as $level){
							if( $level["value"] == $levelID ){ $lvlName = $level["label"]; break; }
						}
						
						$stopDate = _get_StrFromManyConditions("tbl_Submission_Chemical","StopDate",
													array("AND Detail_ID"=>"= ".quote_smart($detail2["Detail_ID"]),"AND Chemical_ID"=>"= ".quote_smart($chemicalID))
									);
                                                $suid = $detail2["Submission_ID"];
				?>
				<tr class="contents">
					<td align="center"><label><?= $i ?></label></td>
					<td><label><?= $compName; ?></label></td>
					<td align="center"><label><?= $lvlName; ?></label></td>
					<td align="center"><label>
                                        <?php
							$imgParam1 = $detail2["Submission_Submit_ID"] ;
							//$imgParam2 = "Approve";
							$imgParam2 = "submissionView.php?submissionID=$suid&new=1";
						 
							echo _get_imagedoc2($imgParam1,$imgParam2);
						 ?>
                                       
                                        </label></td>
					<?php /*
					<td align="center"><label><?= (!empty($stopDate)?func_ymd2dmy($stopDate):"-"); ?></label></td>
					*/ ?>
				</tr>
				<?php 
					}
					if($i==0){
				?>
				<tr class="contents"><td align="center" colspan="4"><label><?= _LBL_NO_RECORD ?></label></td></tr>
				<?php
					}
				?>
			</table>
			
		</div>
	</div>
	<br />		
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				$param1 = "back";
				$param2 = _LBL_BACK;
				$param3 = array("type"=>"button","onClick"=>"redirectForm('ackChemicalList.php');");
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>
