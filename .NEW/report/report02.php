<?php
	//****************************************************************/
	// filename: report02.php
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
	if(isset($_GET["rptYear"])) $rptYear = $_GET["rptYear"];
	else $rptYear = "";
	/*****/
	if(isset($_GET["state"])) $state = $_GET["state"];
	else $state = "";
	/*****/
	$lvlName = "";
	if($lvlType=="all"){
		foreach($arrLevel as $detail){
			$lvlName .= $detail["label"] .", ";
		}
		$lvlName = substr_replace($lvlName,"",-2);
		$lvlType = "6,7,8";
	}
	else{
		$lvlName = _getLabel($lvlType,$arrLevel);
		/*
		$lvlName = "";
		foreach($arrLevel as $detail){
			if($detail["value"]==$lvlType){
				$lvlName = "[". strtoupper($detail["label"]) ."]"; 
				break; 
			}
		}
		*/
	}
	$lvlName = "[". strtoupper($lvlName) ."]"; 
	
	$langdesc = (($fileLang=="may.php")?"_may":"");
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
						$rptName = strtoupper($detail['label']) .' '. $lvlName ; 
						break; 
					}
				}
				echo strtoupper(_LBL_SUMMARY).': '; echo ($rptName!='') ? $rptName : '-';
				echo '<br /><br />';
				echo strtoupper(_LBL_YEAR).': '; echo ($rptYear!='') ? ($rptYear-1) : '-';
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<thead>
		<tr class="contents">
			<td width="5%" class="label" style="<?= $text_align_center ?>" rowspan="2"><?= _LBL_BIL ?></td>
			<td width="20%" class="label" style="<?= $text_align_center ?>" rowspan="2"><nobr><?= _LBL_COMPANY_NAME ?></nobr></td>
			<td width="15%" class="label" style="<?= $text_align_center ?>" rowspan="2"><nobr><?= _LBL_COMPANY_TYPE ?></nobr></td>
			<td width="10%" class="label" style="<?= $text_align_center ?>" rowspan="2"><?= _LBL_STATUS ?></td>
			<td class="label" style="<?= $text_align_center ?>" colspan="3"><?= _LBL_CHEMICAL ?></td>
		</tr>
		<tr class="contents">
			<td width="5%" class="label" style="<?= $text_align_center ?>"><?= _LBL_NUMBER ?></td>
			<td width="25%" class="label" style="<?= $text_align_center ?>"><?= _LBL_NAME ?></td>
			<td width="10%" class="label" style="<?= $text_align_center ?>"><?= _LBL_CAS_NO ?></td>
			<!--<td width="10%" class="label" style="<?= $text_align_center ?>"><?= _LBL_NOLONGER ?></td>-->
		</tr>
		</thead>
		<?php 
			$arr_state = explode(',',$state);
			//=========================================
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'.$text_align_center.'"';
			$td_mid_right = ' style="vertical-align:middle;'.$text_align_right.'"';
			//=========================================	
			$countExist = 0;
			$showExport = 0;
			foreach($arr_state as $value){
				if($value!=0){
					$countExist++;
					
		?>
		<tr class="contents"><td colspan="9" class="label"><?= _get_StrFromCondition('sys_State','State_Name','State_ID',$value); ?></td></tr>
		<?php
					//=====================================================
					$dataColumns 	= 'u.Usr_ID,u.Level_ID,u.Active';
					$dataTable 		= 'sys_User_Client uc';
					$dataJoin 		= array('sys_User u'=>'uc.Usr_ID = u.Usr_ID');
					$dataWhere["AND uc.State_Reg"] = "= ".quote_smart($value);
					if($lvlType!='')
						$dataWhere["AND Level_ID"] = "IN (". quote_smart($lvlType,false) .")";
					$dataOrder		= "Level_ID ASC, Company_Name ASC";
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere,$dataOrder);
					$bil = 0;
					//=====================================================
					foreach($arrayData as $detail){
						$uID = $detail['Usr_ID'];
						$lID = $detail['Level_ID'];
						$aID = $detail['Active'];
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
							$countFirst++;
							//=====================================================
							$td_rowspan = ($rowspan>1) ? ' rowspan="'.$rowspan.'"' : '';
		?>
		<tr class="contents">
			<?php
							if($key1==0){
								$bil++;
			?>
			<td <?= $td_rowspan.$td_mid_center; ?>><?= $bil ?></td>
			<td <?= $td_rowspan.$td_mid; ?>><?= _get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$uID); ?></td>
			<td <?= $td_rowspan.$td_mid_center; ?>><?= _get_StrFromCondition('sys_Level','Level_Name'. $langdesc,'Level_ID',$lID); ?></td>
			<td <?= $td_rowspan.$td_mid_center; ?>><?php
				foreach($arrStatusAktif as $stat){
					if($stat['value']==$aID) echo $stat['label']; 
				}
			?></td>
			<?php
							}
			?>
			<td <?= $td_mid_center; ?>><?= $countFirst; ?></td>
			<td <?= $td_mid; ?>><?= _get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$detail1['Chemical_ID']); ?></td>
			<td <?= $td_mid_center; ?>><?= _get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$detail1['Chemical_ID']); ?></td>
			<!--<td <?= $td_mid_center; ?>><?= !empty($detail1['DateStop'])?_LBL_YES:'-'; ?></td>-->
		</tr>
		<?php
							
						}
						//================================================company looping================================================\\
					}
					if($bil==0){
		?>
		<tr class="contents" height="30">
			<td colspan="9" <?= $td_mid_center; ?>><?= _LBL_NO_RECORD ?></td>
		</tr>
		<?php
					}
					$showExport += $bil;
				}
			}
			if($countExist==0){
		?>
		<tr class="contents" height="30">
			<td colspan="9" <?= $td_mid_center; ?>><?= _LBL_NO_RECORD ?></td>
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
					$url_pdf = 'report02_to_pdf.php?rptType='.$rptType.'&lvlType='.$lvlType.'&rptYear='.$rptYear.'&state='.$state;
					echo form_button('convert',_LBL_CONVERT_TO_PDF,array('type'=>'button','onClick'=>"funcPopup('".$url_pdf."');"));
					echo '&nbsp;';
				}
				echo form_button('close',_LBL_CLOSE,array('type'=>'button','onClick'=>'window.close()'));
			?></td>
		</tr>
	</table>
