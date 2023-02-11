<?php
	//****************************************************************/
	// filename: report02_supplier.php
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
	if(isset($_GET["rptYear"])) $rptYear = $_GET["rptYear"];
	else $rptYear = "";
	/*****/
	$lvlName = "";
	foreach($arrLevel as $detail){
		if($detail["value"]==$uLevelID){
			$lvlName = " [". strtoupper($detail["label"]) ."]"; 
			break; 
		}
	}
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
						$rptName = strtoupper($detail['label']) ; 
						break; 
					}
				}
				echo strtoupper(_LBL_SUMMARY).': '; echo ($rptName!='') ? $rptName : '-';
				echo '<br /><br />';
				echo strtoupper(_LBL_SUPPLIER_NAME) .': '.$suppName; echo ($lvlName!='') ? $lvlName : '';
				echo '<br /><br />';
				echo strtoupper(_LBL_YEAR).': '; echo ($rptYear!='') ? ($rptYear-1) : '-';
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<thead>
		<tr class="contents">
			<td class="label" style="<?= $text_align_center ?>" colspan="3"><?= _LBL_CHEMICAL ?></td>
		</tr>
		<tr class="contents">
			<td width="5%" class="label" style="<?= $text_align_center ?>"><?= _LBL_NUMBER ?></td>
			<td width="70%" class="label" style="<?= $text_align_center ?>"><?= _LBL_NAME ?></td>
			<td width="15%" class="label" style="<?= $text_align_center ?>"><?= _LBL_CAS_NO ?></td>
			<!--<td width="10%" class="label" style="<?= $text_align_center ?>"><?= _LBL_NOLONGER ?></td>-->
		</tr>
		</thead>
		<?php 
			//=========================================
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'.$text_align_center.'"';
			$td_mid_right = ' style="vertical-align:middle;'.$text_align_right.'"';
			//=========================================	
			$bil = 0;
			$uID = $_SESSION['user']['Usr_ID'];
			$lID = $uLevelID;
			$aID = _get_StrFromCondition('sys_User','Active','Usr_ID',$uID);
			//=====================================================
			$dataColumns1 = '*';
			$dataTable1 = 'tbl_Submission s';
			$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
			$dataWhere1 = array(
							"AND s.Usr_ID" =>"= ".quote_smart($uID),"AND s.Status_ID"=>"IN (31,32,33,41,42,43)"
							,"AND DATE_FORMAT(s.ApprovedDate,'%Y')"=>"= ".quote_smart($rptYear)
						);
			$dataOrder1 = '';
			$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1); 
			//print_r($arrayData1);
			$countData1 = count($arrayData1);
			//=====================================================
			$new_chemIDs = array();
			//=====================================================
			foreach($arrayData1 as $detail1){
				//=====================================================
				$arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
				//extractArray($arrayChem);
				//=====================================================
				foreach($arrayChem as $indChem => $detChem){
					if(! in_array($detChem,$new_chemIDs) ) array_push($new_chemIDs,$detChem);
				}
				//=====================================================
			}
			//extractArray($new_chemIDs);
			//=====================================================
			$rowspan = count($new_chemIDs);
			if($rowspan==0 || $rowspan=='') $rowspan = 1;
			$countFirst = 0;
			//=====================================================
			foreach($new_chemIDs as $key1 => $detail1){
				$bil++;
				$countFirst++;
				//=====================================================
				$td_rowspan = ($rowspan>1) ? ' rowspan="'.$rowspan.'"' : '';
		?>
		<tr class="contents">
			<td <?= $td_mid_center; ?>><?= $countFirst; ?></td>
			<td <?= $td_mid; ?>><?= _get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$detail1['Chemical_ID']); ?></td>
			<td <?= $td_mid_center; ?>><?= _get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$detail1['Chemical_ID']); ?></td>
			<!--<td <?= $td_mid_center; ?>><?= (!empty($detail1['DateStop'])?_LBL_YES:'-'); ?></td>-->
		</tr>
		<?php
							
			}
			//================================================company looping================================================\\
			if($bil==0){
		?>
		<tr class="contents" height="30">
			<td colspan="3" <?= $td_mid_center; ?>><?= _LBL_NO_RECORD ?></td>
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
					$url_pdf = 'report02_to_pdf_supp.php?rptType='.$rptType.'&rptYear='.$rptYear;
					echo form_button('convert',_LBL_CONVERT_TO_PDF,array('type'=>'button','onClick'=>"funcPopup('".$url_pdf."');"));
					echo '&nbsp;';
				}
				
				
				
				
				
	            // linuxhouse__20220710
                // start penambahbaikan - 12_penjanaan laporan - v001
				if($bil>0){
					$url_excel = 'report02_to_excel_supp.php?rptType='.$rptType.'&rptYear='.$rptYear;
					echo form_button('convert',_LBL_CONVERT_TO_EXCEL,array('type'=>'button','onClick'=>"funcPopup('".$url_excel."');"));
					echo '&nbsp;';
				}
				
				
// 				if($bil>0){
// 					$url_word = 'report02_to_word_supp.php?rptType='.$rptType.'&chemType='.$chemType.'&rptYear='.$rptYear;
// 					echo form_button('convert',_LBL_CONVERT_TO_WORD,array('type'=>'button','onClick'=>"funcPopup('".$url_word."');"));
// 					echo '&nbsp;';
// 				}
                // end penambahbaikan - 12_penjanaan laporan - v001
                
                
                
                
				
				echo form_button('close',_LBL_CLOSE,array('type'=>'button','onClick'=>'window.close()'));
			?></td>
		</tr>
	</table>
