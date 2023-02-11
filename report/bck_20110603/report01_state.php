<?php
	//****************************************************************/
	// filename: report01.php
	// description: overall summary report
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
	include_once $sys_config['includes_path'].'func_date.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	$uLevelID = $_SESSION['user']['Level_ID'];
	//$uLevelID = 5;
	//==========================================================
	func_header("",
				"", // css		
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
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
	if(isset($_GET['rptType'])) $rptType = $_GET['rptType'];
	else $rptType = '';
	//==========================================================
	if(isset($_GET['lvlType'])) $lvlType = $_GET['lvlType'];
	else $lvlType = '';
	//==========================================================
	if(isset($_GET['chemType'])) $chemType = $_GET['chemType'];
	else $chemType = '';
	//==========================================================
	if(isset($_GET['rptYear'])) $rptYear = $_GET['rptYear'];
	else $rptYear = '';
	//==========================================================
	if(isset($_GET['state'])) $state = $_GET['state'];
	else $state = '';
	//==========================================================
	//$lvlName = ($lvlType!='') ? _get_StrFromCondition('sys_Level','Level_Name','Level_ID',$lvlType) : '';
	$lvlName = '';
	foreach($arrLevel as $detail){
		if($detail['value']==$lvlType){
			$lvlName = $detail['label']; 
			break; 
		}
	}
	$colTypeName = 'Type_Name'. (($fileLang=='may.php')?'_may':'');
	$typeName = ($chemType!='') ? ' ['. strtoupper(_get_StrFromCondition('tbl_Chemical_Type',$colTypeName,'Type_ID',$chemType)) .']' : '';
	//$typeName = ($chemType!='') ? ' ['. strtoupper(_get_StrFromCondition('tbl_Chemical_Type','Type_Name','Type_ID',$chemType)) .']' : '';
?>
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
				echo strtoupper(_LBL_SUPPLIER_TYPE) .': '; echo ($lvlName!='') ? strtoupper($lvlName) : '-';
				echo '<br /><br />';
				echo strtoupper(_LBL_YEAR).': '; echo ($rptYear!='') ? $rptYear : '-';
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="5%" class="label" style="<?= $text_align_center ?>"><?= _LBL_BIL ?></td>
			<!--<td width="15%" class="label" style="<?= $text_align_center ?>"><nobr><?= ($lvlName!='')?$lvlName:_LBL_COMPANY_NAME ?></nobr></td>-->
			<?php 
					$minus = 1;
					if($chemType==2){
			?>
			<td width="30%" class="label" style="<?= $text_align_center ?>"><?= _LBL_PRODUCT_NAME ?></td>
			<?php 
						$minus = 0;
					} 
			?>
			<td width="20%" class="label" style="<?= $text_align_center ?>"><?= _LBL_CHEMICAL_NAME ?></td>
			<td width="10%" class="label" style="<?= $text_align_center ?>"><?= _LBL_CAS_NO ?></td>
			<td width="25%" class="label" style="<?= $text_align_center ?>"><nobr><?= _LBL_HAZARD_CLASSIFICATION ?></nobr></td>
			<td width="10%" class="label" style="<?= $text_align_center ?>"><?= _LBL_SUBMISSION_DATE ?></td>
		<?php
			$arr_state = explode(',',$state);
			$bil = 0;
			//extractArray($arr_state);
			//=========================================
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'.$text_align_center.'"';
			$td_mid_right = ' style="vertical-align:middle;'.$text_align_right.'"';
			//=========================================	
			$grand_totalSupply = 0;
			$grand_totalUse = 0;
			$countExist = 0;
			//=========================================
			foreach($arr_state as $value){
				if($value!=0){
					$countExist++;
					
		?>
		<tr class="contents"><td colspan="<?= (6-$minus) ?>" class="label"><?= _get_StrFromCondition('sys_State','State_Name','State_ID',$value); ?></td></tr>
		<?php
					//=====================================================
					$dataColumns = 'u.Usr_ID';
					$dataTable = 'sys_User_Client uc';
					$dataJoin = array('sys_User u'=>'uc.Usr_ID = u.Usr_ID');
					$dataWhere = array("AND uc.State_Reg"=>"= ".quote_smart($value),"AND Level_ID"=>"= ".quote_smart($lvlType));
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
					$countFirst = 0;
					//=====================================================
					$sum_totalSupply = 0;
					$sum_totalUse = 0;
					//=====================================================
					foreach($arrayData as $detail){
						$uID = $detail['Usr_ID'];
						//=====================================================
						$subselect = "(SELECT Sub_Record_Date FROM tbl_Submission_Record WHERE Submission_ID = s.Submission_ID AND Sub_Record_Status IN (11,12) ORDER BY Sub_Record_Date DESC LIMIT 1)";
						$dataColumns1 = '*,'.$subselect.' AS submitDate';
						$dataTable1 = 'tbl_Submission s';
						$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
						$dataWhere1 = array(
										"AND s.Usr_ID" =>"= ".quote_smart($uID),"AND s.Status_ID"=>"IN (31,32,33,41,42,43)","AND Type_ID"=>"= ".quote_smart($chemType)
										,"AND DATE_FORMAT(".$subselect.",'%Y')"=>"= ".quote_smart($rptYear)
									);
						$dataOrder1 = ' '.$subselect.' DESC';
						$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1,$dataOrder1); 
						$countData1 = count($arrayData1);
						//=====================================================
						$new_rowspan1 = 0;
						//=====================================================
						$sub_totalSupply = 0;
						$sub_totalUse = 0;
						//=====================================================
						foreach($arrayData1 as $key => $detail1){
							//=====================================================
							$entityTo 		= $detail1['Detail_EntityTo'];
							$ex_entityTo 	= explode(',',$entityTo);
							$count_entityTo	= 0;
							$entityFrom 	= $detail1['Detail_EntityFrom'];
							$ex_entityFrom 	= explode(',',$entityFrom);
							$count_entityFrom	= 0;
							//=====================================================
							$arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
							$count_arrayChem = count($arrayChem);
							//=====================================================
							$rowspan1 = max($count_entityTo,$count_entityFrom,$count_arrayChem);
							if($rowspan1==0 || $rowspan1=='') $rowspan1 = 1;
							//=====================================================
							$new_rowspan1 += $rowspan1;
							//=====================================================
						}
						foreach($arrayData1 as $key => $detail1){
							$countFirst++;
							$bil++;
							//=====================================================
							$rowspan = max($countData1,$new_rowspan1);
							if($rowspan==0 || $rowspan=='') $rowspan = 1;
							//=====================================================
							$entityTo 		= $detail1['Detail_EntityTo'];
							$ex_entityTo 	= explode(',',$entityTo);
							$count_entityTo	= 0;
							$entityFrom 	= $detail1['Detail_EntityFrom'];
							$ex_entityFrom 	= explode(',',$entityFrom);
							$count_entityFrom	= 0;
							//=====================================================
							$arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
							$count_arrayChem = count($arrayChem);
							//=====================================================
							$rowspan1 = max($count_entityTo,$count_entityFrom,$count_arrayChem);
							if($rowspan1==0 || $rowspan1=='') $rowspan1 = 1;
							//=====================================================
							for($i=0;$i<$rowspan1;$i++){
								$td_rowspan = ($rowspan1>1) ? ' rowspan="'.$rowspan1.'"' : '';
								
								if($key==0&&$i==0){
		?>
		<tr class="contents">
			<td colspan="<?= (6-$minus) ?>" class="label"><?= ':: ' . (($lvlName!='')?$lvlName:_LBL_COMPANY_NAME) .' - '. _get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$uID); ?></td>
		</tr>
		<?php
								}
		?>
		<tr class="contents">
			<?php
								if($i==0){
			?>
			<td <?= $td_rowspan.$td_mid_center; ?>><?= $bil ?></td>
			<?php 			
								}
/*
								if($key==0&&$i==0){
			?>
			<td <?= ($rowspan>1) ? 'rowspan="'.$rowspan.'"' : '' ?> <?= $td_mid ?>><?= _get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$uID); ?></td>
			<?php
								}
*/
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
									$totalSupply = $detail1['Detail_TotalSupply'];
									$sub_totalSupply += $totalSupply;
									//=========================================
			?>
			<td <?= $td_rowspan.$td_mid; ?>><?php
				$ex_comb_hazCat = explode(',',$comb_hazCat);
				$tyIDs = 0;
				foreach($ex_comb_hazCat as $catID){
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
						echo _get_StrFromCondition(
									"tbl_Hazard_Category",
									"CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									"Category_ID",$catID
							);
						echo ';<br />';
					}
				}
			?></td>
			<td <?= $td_rowspan.$td_mid_center; ?>><?= func_ymd2dmy($detail1['submitDate']); ?></td>
			<?php				
								}
			?>
		</tr>
		<?php
							}
						}
					}
					//================================================state looping================================================\\
					if($countFirst==0){
		?>
		<tr class="contents" height="30">
			<td colspan="<?= (6-$minus) ?>" <?= $td_mid_center; ?>><?= _LBL_NO_RECORD ?></td>
		</tr>
		<?php
					}
		?>
		<?php
					//================================================state looping================================================\\
				}
			}
		?>
		<?php
			if($countExist==0){
		?>
		<tr class="contents" height="30">
			<td colspan="<?= (6-$minus) ?>" <?= $td_mid_center; ?>><?= _LBL_NO_RECORD ?></td>
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
					$url_pdf = 'report01_to_pdf_state.php?rptType='.$rptType.'&lvlType='.$lvlType.'&chemType='.$chemType.'&rptYear='.$rptYear.'&state='.$state;
					echo form_button('convert',_LBL_CONVERT_TO_PDF,array('type'=>'button','onClick'=>"funcPopup('".$url_pdf."');"));
					echo '&nbsp;';
				}
				echo form_button('close',_LBL_CLOSE,array('type'=>'button','onClick'=>'window.close()'));
			?></td>
		</tr>
	</table>
