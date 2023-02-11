<?php
	//****************************************************************/
	// filename: report03.php
	// description: overall summary report
	//****************************************************************/
	include_once "../includes/sys_config.php";
	/*****/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*****/
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*****/
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_header.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*****/
	$uLevelID = $_SESSION["user"]["Level_ID"];
	if($uLevelID==5) exit();
	/*****/
	func_header("",
				"", // css		
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
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
	/*****/
	if(isset($_GET["rptType"])) $rptType = $_GET["rptType"];
	else $rptType = "";
	/*****/
	if(isset($_GET["lvlType"])) $lvlType = $_GET["lvlType"];
	else $lvlType = "";
	/*****/
	if(isset($_GET["chemType"])) $chemType = $_GET["chemType"];
	else $chemType = "";
	/*****/
	if(isset($_GET["status"])) $status = $_GET["status"];
	else $status = "";
	/*****/
	if(isset($_GET["rptYear"])) $rptYear = $_GET["rptYear"];
	else $rptYear = "";
	/*****/
	if(isset($_GET["state"])) $state = $_GET["state"];
	else $state = "";
	/*****/
	$colTypeName = "Type_Name". (($fileLang=="may.php")?"_may":"");
	$typeName = ($chemType!="") ? " [". strtoupper(_get_StrFromCondition("tbl_Chemical_Type",$colTypeName,"Type_ID",$chemType)) ."]" : "";

	$lvlName = "";
	foreach($arrLevel as $detail){
		if($detail["value"]==$lvlType){
			$lvlName = strtoupper($detail["label"]); 
			break; 
		}
	}
?>
	<script language="Javascript">
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php $text_align_center = "text-align:center;"; ?>
	<?php $text_align_right = "text-align:right;"; ?>
	<?php $text_valign_mid = "vertical-align:middle;"; ?>
	<table width="100%">
		<tr>
			<td align="center"><?php
				/*****/
				echo '<img src="'.$sys_config["images_path"].'jata.gif" />';
				echo "<br /><br />";
				/*****/
				echo strtoupper(_LBL_DOSH);
				echo "<br /><br />";
				/*****/
				echo strtoupper(_LBL_CIMS);
				echo "<br /><br />";
				/*****/
				$rptName = "";
				foreach($arrReportSub as $detail){
					if($detail["value"]==$rptType){
						$rptName = strtoupper($detail["label"]) ." ". $typeName ; 
						break; 
					}
				}
				echo strtoupper(_LBL_REPORT) .": "; echo ($rptName!="") ? $rptName : "-";
				echo "<br /><br />";
				/*****/
				if($rptType=="rpt04") $arrDetail = $arrRptStatusR;
				else $arrDetail = $arrRptStatusN;
				$sName = "";
				foreach($arrDetail as $detail){
					if($detail["value"]==$status){
						$sName = strtoupper($detail["label"]) ; 
						break; 
					}
				}
				echo strtoupper(_LBL_STATUS).": "; echo ($sName!="") ? $sName : "-";
				echo "<br /><br />";
				/*****/
				echo strtoupper(_LBL_SUPPLIER_TYPE) .": "; echo ($lvlName!="") ? $lvlName : "-";
				echo "<br /><br />";
				/*****/
				echo strtoupper(_LBL_YEAR).": "; echo ($rptYear!="") ? ($rptYear-1) : "-";
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<thead>
		<tr class="contents">
			<td width="3%" class="label" style="<?= $text_align_center.$text_valign_mid ?>" rowspan="2"><?= _LBL_BIL ?></td>
			<td width="7%" class="label" style="<?= $text_align_center.$text_valign_mid ?>" rowspan="2"><?= _LBL_PHYSICAL_FORM ?></td>
			<?php 
					$minus_1 = 1;
					$width_11_14 = '11%';
					$width_10_11 = '10%';
					$trade_chemical = _LBL_TRADE_PRODUCT;
					if($chemType==2){
			?>
			<td width="10%" class="label" style="<?= $text_align_center.$text_valign_mid ?>" rowspan="2"><?= _LBL_PRODUCT_NAME ?></td>
			<?php 
						$minus_1 = 0;
						$width_11_14 = '14%';
						$width_10_11 = '11%';
						$trade_chemical = _LBL_CHEMICAL_NAME;
					}
			?>
			<td width="<?= $width_10_11 ?>" class="label" style="<?= $text_align_center.$text_valign_mid ?>" rowspan="2"><?= $trade_chemical ?></td>
			<td width="7%" class="label" style="<?= $text_align_center.$text_valign_mid ?>" rowspan="2"><?= _LBL_CAS_NO ?></td>
			<td class="label" style="<?= $text_align_center.$text_valign_mid ?>" colspan="3"><?= _LBL_HAZARD_CLASSIFICATION ?></td>
			<?php
					if($lvlType!=7){
			?>	
			<td width="10%" class="label" style="<?= $text_align_center.$text_valign_mid ?>" rowspan="2"><?= _LBL_TOTAL_QUANTITY_SUPPLY .'<br />('. _LBL_TONNE_YEAR .')' ?></td>
			<?php
					}
					if($lvlType!=6){
			?>	
			<td width="10%" class="label" style="<?= $text_align_center.$text_valign_mid ?>" rowspan="2"><?= _LBL_TOTAL_QUANTITY_USE .'<br />('. _LBL_TONNE_YEAR .')' ?></td>
			<?php
					}
			?>	
		</tr>
		<tr class="contents">
			<td width="<?= $width_11_14 ?>" class="label" style="<?= $text_align_center ?>"><?= _LBL_PHY ?></td>
			<td width="<?= $width_11_14 ?>" class="label" style="<?= $text_align_center ?>"><?= _LBL_HEALTH ?></td>
			<td width="<?= $width_11_14 ?>" class="label" style="<?= $text_align_center ?>"><?= _LBL_ENV ?></td>
		</tr>
		</thead>
		<?php 
			$arr_state = explode(',',$state);
			/*****/
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'.$text_align_center.'"';
			$td_mid_right = ' style="vertical-align:middle;'.$text_align_right.'"';
			/*****/
			$countExist = 0;
			$grand_totalSupply = 0;
			$grand_totalUse = 0;
			$showExport = 0;
			/*****/
			
			$dateStart 	= _get_StrFromCondition("sys_SubmitDate","Date_Start","YEAR(Date_Last)",$rptYear);
			$dateLast 	= _get_StrFromCondition("sys_SubmitDate","Date_Last","YEAR(Date_Last)",$rptYear);
			
			foreach($arr_state as $value){
				if($value!=0){
					$countExist++;
		?>
		<tr class="contents">
			<td colspan="<?= (10-$minus_1) ?>" class="label"><?= _get_StrFromCondition("sys_State","State_Name","State_ID",$value); ?></td>
		</tr>
		<?php
					/*****/
					$dataColumns = "u.Usr_ID,u.Level_ID,u.Active,uc.Company_Name";
					$dataTable = "sys_User_Client uc";
					$dataJoin = array("sys_User u"=>"uc.Usr_ID = u.Usr_ID");
					$dataWhere["AND uc.State_Reg"] = "= ".quote_smart($value);
					if($lvlType!="")
						$dataWhere["AND Level_ID"] = "= ".quote_smart($lvlType);
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
					$bil = 0;
					$sub_totalSupply = 0;
					$sub_totalUse = 0;
					/*****/
					foreach($arrayData as $detail){		
						$uID = $detail["Usr_ID"];
						$lID = $detail["Level_ID"];
						$cName = $detail["Company_Name"];
						/*****/
						$statusIn = $status;
						if($status=="31") $statusIn .= ",32";
						elseif($status=="41") $statusIn .= ",42";
						if(empty($statusIn)) $statusIn = 0;
						// $dataColumns1 = "*";
						$dataColumns1 = "Detail_EntityTo,Detail_EntityFrom,Detail_ID,Form_ID,Detail_ProductName,"
									.	" Category_ID_ph,Category_ID_hh,Category_ID_eh,Detail_TotalSupply,Detail_TotalUse";
						$dataTable1 = "tbl_Submission s";
						$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID","tbl_Submission_Submit ss"=>"s.Submission_ID = ss.Submission_ID");
						$dataWhere1 = array(
										"AND s.Usr_ID" =>"= ".quote_smart($uID),
										"AND s.Status_ID"=>"IN (".$statusIn.")",
										"AND s.Type_ID" =>"= ".quote_smart($chemType),
										// "AND DATE_FORMAT(s.RecordDate,'%Y')"=>"= ".quote_smart($rptYear)
										"AND (ss.SubmitTime >= ". quote_smart($dateStart) => "AND ss.SubmitTime <= ". quote_smart($dateLast)  .")"
									);
						$dataOrder1 = "SubmitTime ASC";
						$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1); 
						//extractArray($arrayData1);
						$countData1 = count($arrayData1);
						/*****/
						$new_chemIDs = array();
						$new_rowspan1 = 0;
						/*****/
						foreach($arrayData1 as $detail1){
							/*****/
							$entityTo 		= $detail1["Detail_EntityTo"];
							$ex_entityTo 	= explode(",",$entityTo);
							$count_entityTo	= count($ex_entityTo);
							$entityFrom 	= $detail1["Detail_EntityFrom"];
							$ex_entityFrom 	= explode(",",$entityFrom);
							$count_entityFrom	= count($ex_entityFrom);
							/*****/
							$arrayChem = _get_arrayData("Chemical_ID","tbl_Submission_Chemical","",array("AND Detail_ID"=>"= ".quote_smart($detail1["Detail_ID"]) )); 
							$count_arrayChem = count($arrayChem);
							/*****/
							$rowspan1 = max($count_entityTo,$count_entityFrom,$count_arrayChem);
							if($rowspan1==0 || $rowspan1=="") $rowspan1 = 1;
							/*****/
							$new_rowspan1 += $rowspan1;
							/*****/
						}
						/*****/
						$rowspan = count($new_chemIDs);
						if($rowspan==0 || $rowspan=='') $rowspan = 1;
						$countFirst = 0;
						/*****/
						//extractArray($arrayData1);
						foreach($arrayData1 as $key1 => $detail1){
							$bil++;
							$countFirst++;
							/*****/
							$rowspan = max($countData1,$new_rowspan1);
							if($rowspan==0 || $rowspan=="") $rowspan = 1;
							/*****/
							$entityTo 		= $detail1["Detail_EntityTo"];
							$ex_entityTo 	= explode(",",$entityTo);
							//$count_entityTo	= count($ex_entityTo);
							$count_entityTo	= 0;
							$entityFrom 	= $detail1["Detail_EntityFrom"];
							$ex_entityFrom 	= explode(",",$entityFrom);
							//$count_entityFrom	= count($ex_entityFrom);
							$count_entityFrom	= 0;
							/*****/
							$arrayChem = _get_arrayData("Chemical_ID","tbl_Submission_Chemical","",array("AND Detail_ID"=>"= ".quote_smart($detail1["Detail_ID"]) )); 
							$count_arrayChem = count($arrayChem);
							/*****/
							$rowspan1 = max($count_entityTo,$count_entityFrom,$count_arrayChem);
							if($rowspan1==0 || $rowspan1=="") $rowspan1 = 1;
							/*****/
							for($i=0;$i<$rowspan1;$i++){
								$td_rowspan = ($rowspan1>1) ? ' rowspan="'.$rowspan1.'"' : '';
								if($i==0 && $key1==0){
		?>
		<tr class="contents">
			<td colspan="<?= (10-$minus_1) ?>" class="label">- <i><?= _LBL_SUPPLIER_NAME ?>:</i> <?= $cName; ?></td>
		</tr>
		<?php
								}
		?>
		<tr class="contents">
			<?php
								if($i==0){
			?>
			<td width="3%" <?= $td_rowspan ?> style="<?= $text_align_center.$text_valign_mid ?>"><?= $bil ?></td>
			<td width="7%" <?= $td_rowspan ?> style="<?= $text_align_center.$text_valign_mid ?>"><?= _get_StrFromCondition("tbl_Chemical_Form","Form_Name","Form_ID",$detail1["Form_ID"]); ?></td>
			<?php 
									if($chemType==2){
			?>
			<td width="10%" <?= $td_rowspan ?> style="<?= $text_valign_mid ?>"><?= $detail1["Detail_ProductName"] ?></td>
			<?php
									}
								}
								
								$isCWC = _get_StrFromCondition("tbl_Chemical","isCWC","Chemical_ID",$arrayChem[$i]["Chemical_ID"]);
								
								$cwc = "";
								if($uLevelID<5 && $isCWC==1)
									$cwc = " <b>[". _LBL_CWC ."]</b>";
			?>
			<td width="<?= $width_10_11 ?>" style="<?= $text_valign_mid ?>"><?= isset($arrayChem[$i]["Chemical_ID"])?nl2br(_get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$arrayChem[$i]["Chemical_ID"])) . $cwc:""; ?></td>
			<td width="7%" style="<?= $text_align_center.$text_valign_mid ?>"><?= isset($arrayChem[$i]["Chemical_ID"])?_get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$arrayChem[$i]["Chemical_ID"]):""; ?></td>
			<?php
								if($i==0){
			?>
			<td width="<?= $width_11_14 ?>" <?= $td_rowspan ?> style="<?= $text_valign_mid ?>"><?php
				$ex_hazCat = explode(',',$detail1['Category_ID_ph']);
				foreach($ex_hazCat as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
													array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
													array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							echo _get_StrFromCondition(
									"tbl_Hazard_Category",
									"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catID
								);
							echo ';<br />';
						}
					}
				}
			?></td>
			<td width="<?= $width_11_14 ?>" <?= $td_rowspan ?> style="<?= $text_valign_mid ?>"><?php
				$ex_hazCat = explode(',',$detail1['Category_ID_hh']);
				foreach($ex_hazCat as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
													array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
													array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							echo _get_StrFromCondition(
									"tbl_Hazard_Category",
									"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catID
								);
							echo ';<br />';
						}
					}
				}
			?></td>
			<td width="<?= $width_11_14 ?>" <?= $td_rowspan ?> style="<?= $text_valign_mid ?>"><?php
				$ex_hazCat = explode(',',$detail1['Category_ID_eh']);
				foreach($ex_hazCat as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
													array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
													array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							echo _get_StrFromCondition(
									"tbl_Hazard_Category",
									"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catID
								);
							echo ';<br />';
						}
					}
				}
			?></td>
			<?php
									if($lvlType!=7){
										$totalSupply = $detail1['Detail_TotalSupply'];
										$sub_totalSupply += $totalSupply;			
			?>
			<td <?= $td_rowspan ?> style="<?= $text_align_right.$text_valign_mid ?>"><?= number_format($totalSupply,1); ?></td>
			<?php
									}
									if($lvlType!=6){
										$totalUse = $detail1['Detail_TotalUse'];
										$sub_totalUse += $totalUse;
			?>
			<td <?= $td_rowspan ?> style="<?= $text_align_right.$text_valign_mid ?>"><?= number_format($totalUse,1); ?></td>
			<?php
									}
								}
			?>
		</tr>
		<?php
							}
						}
					}
					if($bil>0){
		?>
		<tr class="contents">
			<td class="label" colspan="<?= (7-$minus_1) ?>" <?= $td_mid_right; ?>>&nbsp;</td>
			<td class="label" <?= $td_mid_right; ?>><?= _LBL_TOTAL ?></td>
			<?php
									if($lvlType!=7){
										$grand_totalSupply += $sub_totalSupply;
			?>	
			<td class="label" <?= $td_mid_right; ?>><?= number_format($sub_totalSupply,1); ?></td>
			<?php
									}
									if($lvlType!=6){
										$grand_totalUse += $sub_totalUse;
			?>	
			<td class="label" <?= $td_mid_right; ?>><?= number_format($sub_totalUse,1); ?></td>
			<?php
									}
			?>	
		</tr>
		<?php
					}
					if($bil==0){
		?>
		<tr class="contents" height="30">
			<td colspan="<?= (10-$minus_1) ?>" <?= $td_mid_center; ?>><?= _LBL_NO_RECORD ?></td>
		</tr>
		<?php
					}
					$showExport += $bil;
				}
			}
			if($countExist==0){
		?>
		<tr class="contents" height="30">
			<td colspan="<?= (10-$minus_1) ?>" <?= $td_mid_center; ?>><?= _LBL_NO_RECORD ?></td>
		</tr>
		<?php
			}
			if($showExport>0){
		?>
		<tr class="contents">
			<td class="label" colspan="<?= (7-$minus_1) ?>" <?= $td_mid_right; ?>>&nbsp;</td>
			<td class="label" <?= $td_mid_right; ?>><?= _LBL_GRANDTOTAL ?></td>
			<?php
									if($lvlType!=7){
			?>	
			<td class="label" <?= $td_mid_right; ?>><?= number_format($grand_totalSupply,1); ?></td>
			<?php
									}
									if($lvlType!=6){
			?>	
			<td class="label" <?= $td_mid_right; ?>><?= number_format($grand_totalUse,1); ?></td>
			<?php
									}
			?>
		</tr>
		<?php
			}
		?>
	</table>
	<br />
	<table width="100%">
		<tr>
			<td align="center"><?php
				if($showExport>0){
					$url_pdf = 'report03_to_pdf.php?rptType='.$rptType.'&lvlType='.$lvlType.'&chemType='.$chemType.'&status='.$status.'&rptYear='.$rptYear.'&state='.$state;
					echo form_button('convert',_LBL_CONVERT_TO_PDF,array('type'=>'button','onClick'=>"funcPopup('".$url_pdf."');"));
					echo '&nbsp;';
				}
				echo form_button('close',_LBL_CLOSE,array('type'=>'button','onClick'=>'window.close()'));
			?></td>
		</tr>
	</table>
