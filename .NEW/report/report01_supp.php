<?php
	//****************************************************************/
	// filename: report01_supp.php
	// description: overall summary report supplier
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
	if($uLevelID<6) exit();
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
	if(isset($_GET["chemType"])) $chemType = $_GET["chemType"];
	else $chemType = "";
	/*****/
	if(isset($_GET["rptYear"])) $rptYear = $_GET["rptYear"];
	else $rptYear = "";
	/*****/
	$lvlName = "";
	foreach($arrLevel as $detail){
		if($detail["value"]==$uLevelID){
			$lvlName = $detail["label"]; 
			break; 
		}
	}
	$colTypeName = "Type_Name". (($fileLang=="may.php")?"_may":"");
	$typeName = ($chemType!="") ? " [". strtoupper(_get_StrFromCondition("tbl_Chemical_Type",$colTypeName,"Type_ID",$chemType)) ."]" : "";
	// $typeName = ($chemType!="") ? " [". strtoupper(_get_StrFromCondition("tbl_Chemical_Type","Type_Name","Type_ID",$chemType)) ."]" : "";
	$suppName = strtoupper($_SESSION["user"]["name"]);
?>
	<script language="Javascript">
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php $text_align_center = 'text-align:center;'; ?>
	<?php $text_align_right = 'text-align:right;'; ?>
	<table width="100%">
		<tr>
			<td align="center"><?php
				echo '<img src="'.$sys_config['images_path'].'jata.gif" />';
				echo '<br /><br />';
				echo strtoupper(_LBL_DOSH);
				echo '<br /><br />';
				echo strtoupper(_LBL_CIMS);
				echo '<br /><br />';
				$rptName = '';
				foreach($arrReport as $detail){
					if($detail['value']==$rptType){
						$rptName = strtoupper($detail['label']); 
						$rptName .= ($typeName!='') ? $typeName : ''; 
						break; 
					}
				}
				echo strtoupper(_LBL_SUMMARY).': '; echo ($rptName!='') ? $rptName : '-';
				echo '<br /><br />';
				echo strtoupper(_LBL_SUPPLIER_NAME) .': '.$suppName; echo ($lvlName!='') ? ' ['.strtoupper($lvlName).']' : '';
				echo '<br /><br />';
				echo strtoupper(_LBL_YEAR).': '; echo ($rptYear!='') ? ($rptYear-1) : '-';
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="3%" class="label" style="<?= $text_align_center ?>"><?= _LBL_BIL ?></td>
			<?php 
					$minus = 1;
					if($chemType==2){
			?>
			<td width="12%" class="label" style="<?= $text_align_center ?>"><?= _LBL_PRODUCT_NAME ?></td>
			<?php 
						$minus = 0;
						$trade_chemical = _LBL_CHEMICAL_NAME;
					}
					else{
						$trade_chemical = _LBL_TRADE_PRODUCT;
					}
			?>
			<td width="10%" class="label" style="<?= $text_align_center ?>"><?= $trade_chemical ?></td>
			<td width="7%" class="label" style="<?= $text_align_center ?>"><?= _LBL_CAS_NO ?></td>
			<td width="34%" class="label" style="<?= $text_align_center ?>"><nobr><?= _LBL_HAZARD_CLASSIFICATION ?></nobr></td>
			<td width="8%" class="label" style="<?= $text_align_center ?>"><?= _LBL_APPROVED_DATE ?></td>
			<?php 
					$minus_sup = 1;
					if($uLevelID!=7 && $uLevelID!=5){
						$minus_sup = 0;
			?>
			<td width="8%" class="label" style="<?= $text_align_center ?>"><?= _LBL_TOTAL_QUANTITY_SUPPLY .'<br />('. _LBL_TONNE_YEAR .')' ?></td>
			<?php 
					} 
					$minus_use = 1;
					if($uLevelID!=6 && $uLevelID!=5){
						$minus_use = 0;
			?>
			<td width="8%" class="label" style="<?= $text_align_center ?>"><?= _LBL_TOTAL_QUANTITY_USE .'<br />('. _LBL_TONNE_YEAR .')' ?></td>
			<?php 
					} 
			?>
		</tr>
		<?php
			$bil = 0;
			//extractArray($arr_state);
			//=========================================
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'.$text_align_center.'"';
			$td_mid_right = ' style="vertical-align:middle;'.$text_align_right.'"';
			//=========================================
			$sum_totalSupply = 0;
			$sum_totalUse = 0;
			//=====================================================
			$uID = $_SESSION['user']['Usr_ID'];
			//=====================================================
			$dataColumns1 = '*';
			$dataTable1 = 'tbl_Submission s';
			$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
			$dataWhere1 = array(
							"AND s.Usr_ID" =>"= ".quote_smart($uID),"AND s.Status_ID"=>"IN (31,32,33,41,42,43)","AND Type_ID"=>"= ".quote_smart($chemType)
							,"AND DATE_FORMAT(s.ApprovedDate,'%Y')"=>"= ".quote_smart($rptYear)
						);
			$dataOrder1 = 'ApprovedDate ASC';
			$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1); 
			$countData1 = count($arrayData1);
			//=====================================================
			$new_rowspan1 = 0;
			//=====================================================
			foreach($arrayData1 as $key => $detail1){
				$bil++;
				//=====================================================
				$rowspan = max($countData1,$new_rowspan1);
				if($rowspan==0 || $rowspan=='') $rowspan = 1;
				//=====================================================
				$entityTo 		= $detail1['Detail_EntityTo'];
				$ex_entityTo 	= explode(',',$entityTo);
				$entityFrom 	= $detail1['Detail_EntityFrom'];
				$ex_entityFrom 	= explode(',',$entityFrom);
				//=====================================================
				$arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
				$count_arrayChem = count($arrayChem);
				//=====================================================
				$rowspan1 = $count_arrayChem;
				if($rowspan1==0 || $rowspan1=='') $rowspan1 = 1;
				//=====================================================
				for($i=0;$i<$rowspan1;$i++){
					$td_rowspan = ($rowspan1>1) ? ' rowspan="'.$rowspan1.'"' : '';
		?>
		<tr class="contents">
			<?php
					if($i==0){
			?>
			<td <?= $td_rowspan.$td_mid_center; ?>><?= $bil ?></td>
			<?php 			
					}
					if($i==0 && $chemType==2){
			?>
			<td <?= $td_rowspan.$td_mid; ?>><?= $detail1['Detail_ProductName']; ?></td>
			<?php
					}
			?>
			<td <?= $td_mid; ?>><?= isset($arrayChem[$i]['Chemical_ID'])?nl2br(func_fn_escape(_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$arrayChem[$i]['Chemical_ID']))):''; ?></td>
			<td <?= $td_mid_center; ?>><?= isset($arrayChem[$i]['Chemical_ID'])?_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$arrayChem[$i]['Chemical_ID']):''; ?></td>
			<?php
					if($i==0){
						//=========================================
						$phCategory = $detail1['Category_ID_ph'];
						$hhCategory = $detail1['Category_ID_hh'];
						$ehCategory = $detail1['Category_ID_eh'];
						//=========================================
						$comb_hazCat = $phCategory;
						$comb_hazCat .= ($hhCategory!='') ? ( ($comb_hazCat!='') ? ','.$hhCategory : '' ) :'';
						$comb_hazCat .= ($ehCategory!='') ? ( ($comb_hazCat!='') ? ','.$ehCategory : '' ) :'';
						//=========================================
			?>
			<td <?= $td_rowspan.$td_mid; ?>><?php
				$ex_comb_hazCat = explode(',',$comb_hazCat);
				$tyIDs = 0;
				foreach($ex_comb_hazCat as $catID){
					if($catID!=''){
						$nohazard = _get_RowExist('tbl_Hazard_Category hcat',
									array('AND hcat.Category_ID'=>'= '.quote_smart($catID),'AND hcl.Class_Name'=>"LIKE '%no hazard%'"),
									array('LEFT JOIN tbl_Hazard_Classification hcl ON hcat.Class_ID = hcl.Class_ID'));
						if($nohazard==0){
							$tyID = _get_StrFromCondition('tbl_Hazard_Category','Type_ID','Category_ID',$catID);
							if($tyID!=$tyIDs){
								if($tyID==1){ $clName = _LBL_PHY_HAZARD; }
								elseif($tyID==2){ $clName = _LBL_HEALTH_HAZARD; }
								elseif($tyID==3){ $clName = _LBL_ENV_HAZARD; }
								else{ $clName = '-'; }
								echo '<strong><u>'.$clName.'</u>:-</strong><br />';
							}
							$tyIDs = $tyID;
							echo "-". _get_StrFromCondition(
										"tbl_Hazard_Category",
										"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										"Category_ID",$catID
								);
							echo ';<br />';
						}
					}
				}
			?></td>
			<td <?= $td_rowspan.$td_mid_center; ?>><?= _getSubmitDate($detail1['Submission_ID']); ?></td>
			<?php
						//=========================================					
						$totalSupply = $detail1['Detail_TotalSupply'];
						$sum_totalSupply += $totalSupply;
						//=========================================					
						if($uLevelID!=7 && $uLevelID!=5){ 
			?>
			<td <?= $td_rowspan.$td_mid_right; ?>><?= number_format($totalSupply,1); ?></td>
			<?php
						}
						//=========================================
						$totalUse = $detail1['Detail_TotalUse'];
						$sum_totalUse += $totalUse;
						//=========================================
						if($uLevelID!=6 && $uLevelID!=5){ 
			?>
			<td <?= $td_rowspan.$td_mid_right; ?>><?= number_format($totalUse,1); ?></td>
			<?php				
						}
					}
			?>
		</tr>
		<?php
				}
			}
			if($bil>0){
		?>
		<tr class="contents">
			<td class="label" colspan="<?= (5-$minus) ?>" <?= $td_mid_right; ?>>&nbsp;</td>
			<td class="label" <?= $td_mid_right; ?>><?= _LBL_TOTAL ?></td>
			<?php
						if($uLevelID!=7){
			?>
			<td class="label" <?= $td_mid_right; ?>><?= number_format($sum_totalSupply,1); ?></td>
			<?php
						}
						if($uLevelID!=6){
			?>
			<td class="label" <?= $td_mid_right; ?>><?= number_format($sum_totalUse,1) ?></td>
			<?php		} ?>
		</tr>
		<?php
			}else{
		?>
		<tr class="contents" height="30">
			<td colspan="<?= (8-$minus-$minus_sup-$minus_use) ?>" <?= $td_mid_center; ?>><?= _LBL_NO_RECORD ?></td>
		</tr>
		<?php
			}
		?>
	</table>
	<br />
	<table width="100%">
		<tr>
			<td align="center"><?php
				if($bil>0){
					$url_pdf = 'report01_to_pdf_supp.php?rptType='.$rptType.'&chemType='.$chemType.'&rptYear='.$rptYear."&aaa";
					echo form_button('convert',_LBL_CONVERT_TO_PDF,array('type'=>'button','onClick'=>"funcPopup('".$url_pdf."');"));
					echo '&nbsp;';
				}
				echo form_button('close',_LBL_CLOSE,array('type'=>'button','onClick'=>'window.close()'));
			?></td>
		</tr>
	</table>
