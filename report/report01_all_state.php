<?php
	//****************************************************************/
	// filename: report01.php
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
	//$uLevelID = 5;
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
	if(isset($_GET["rptYear"])) $rptYear = $_GET["rptYear"];
	else $rptYear = "";
	/*****/
	if(isset($_GET["state"])) $state = $_GET["state"];
	else $state = "";
	/*****/
	if(isset($_GET["comp"])) $comp = $_GET["comp"];
	else $comp = "";
	/*****/
	//$lvlName = ($lvlType!="") ? _get_StrFromCondition("sys_Level","Level_Name","Level_ID",$lvlType) : "";
	$lvlName = "";
	if($lvlType=="all"){
		foreach($arrLevel as $detail){
			$lvlName .= $detail["label"] .", ";
		}
		$lvlName = substr_replace($lvlName,"",-2);
		$levelType = "6,7,8";
	}
	else{
		$lvlName = _getLabel($lvlType,$arrLevel);
		$levelType = $lvlType;
		/*
		foreach($arrLevel as $detail){
			if($detail["value"]==$lvlType){
				$lvlName = $detail["label"]; 
				break; 
			}
		}
		*/
	}
	$colTypeName = "Type_Name". (($fileLang=="may.php")?"_may":"");
	$typeName = ($chemType!="") ? " [". strtoupper(_get_StrFromCondition("tbl_Chemical_Type",$colTypeName,"Type_ID",$chemType)) ."]" : "";
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
				echo strtoupper(_LBL_SUPPLIER_TYPE) .': '; echo ($lvlName!='') ? strtoupper($lvlName) : '-';
				echo '<br /><br />';
				echo strtoupper(_LBL_YEAR).': '; echo ($rptYear!='') ? ($rptYear-1) : '-';
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
			/*****/
			$td_mid = ' style="vertical-align:middle;"';
			$td_mid_center = ' style="vertical-align:middle;'.$text_align_center.'"';
			$td_mid_right = ' style="vertical-align:middle;'.$text_align_right.'"';
			/*****/	
			$grand_totalSupply = 0;
			$grand_totalUse = 0;
			$countExist = 0;
			
			$dateStart 	= _get_StrFromCondition("sys_SubmitDate","Date_Start","YEAR(Date_Last)",$rptYear);
			$dateLast 	= _get_StrFromCondition("sys_SubmitDate","Date_Last","YEAR(Date_Last)",$rptYear);
			
			/*****/
			foreach($arr_state as $value){
				if($value!=0){
					$countExist++;
					
		?>
		<tr class="contents"><td colspan="<?= (6-$minus) ?>" class="label"><?= _get_StrFromCondition('sys_State','State_Name','State_ID',$value); ?></td></tr>
		<?php
					/*****/
					$dTable 	= "sys_User_Client";
					$dColumns 	= "$dTable.Usr_ID,Company_Name,Address_Reg,Postcode_Reg,City_Reg,Level_ID";
					$dJoin 		= array("sys_User"=>"sys_User.Usr_ID = $dTable.Usr_ID");
					$dWhere 	= array(
									"AND Level_ID"=>"IN (". quote_smart($levelType,false) .")",
									// "AND Usr_ID" => "IN (". $sub .")",
									"AND Active" => "= 1",
									"AND isDeleted" => "= 0",
									"AND State_Reg"=>"= ". quote_smart($value)
								);
					if( $comp!="" ) $dWhere = array_merge($dWhere,array("AND $dTable.Usr_ID"=>"=" . quote_smart($comp)));
					$dOrder		= "Company_Name ASC";
					/*****/
					$arrayData = _get_arrayData($dColumns,$dTable,$dJoin,$dWhere,$dOrder);
					$countFirst = 0;
					/*****/
					$sum_totalSupply = 0;
					$sum_totalUse = 0;
					$total_bil = 0;
					/*****/
					foreach($arrayData as $detail){
						$bil = 0;
						$uID = $detail['Usr_ID'];
						$lvlID = $detail["Level_ID"];
						
						$compName 	= $detail['Company_Name'];
						$add1 		= $detail['Address_Reg'];
						$add2 		= $detail['Postcode_Reg'];
						$add3 		= $detail['City_Reg'];
						$compAdd	= "<br />". nl2br($add1) . "<br />$add2 $add3";
						/*****/
									
						$lvlName = _getLabel($lvlID,$arrLevel);
	?>
		<tr class="contents">
			<td colspan="<?= (6-$minus) ?>" class="label"><?= ':: ' . (($lvlName!='')?$lvlName:_LBL_COMPANY_NAME) .' ::' ?><div><?= $compName . $compAdd ?></div></td>
		</tr>
	<?php
						/*****/
						
						$dTable1 	= 'tbl_Submission';
						$dColumns1 	= "Detail_EntityTo,Detail_EntityFrom,Detail_ID,Form_ID,Detail_ProductName,"
									.	" Category_ID_ph,Category_ID_hh,Category_ID_eh,Detail_TotalSupply,Detail_TotalUse,ss.SubmitTime AS submitDate";
						$dJoin1 	= array("tbl_Submission_Detail sd"=>"$dTable1.Submission_ID = sd.Submission_ID",
											"tbl_Submission_Submit ss"=>"$dTable1.Submission_ID = ss.Submission_ID");
						$dWhere1 	= array(
										"AND $dTable1.Usr_ID" =>"= ".quote_smart($uID),
										"AND $dTable1.Status_ID"=>"IN (31)",
										"AND Type_ID"=>"= ".quote_smart($chemType),
										// "AND DATE_FORMAT(SubmitTime,'%Y')"=>"= ".quote_smart($rptYear)
										// "AND (ss.SubmitTime >= ". quote_smart($dateStart) => "AND ss.SubmitTime <= ". quote_smart($dateLast)  .")"
										"AND YEAR(ss.SubmitTime)" => "= ". quote_smart($rptYear)
									);
						$dOrder1 	= "SubmitTime DESC";
						$arrayData1 = _get_arrayData($dColumns1,$dTable1,$dJoin1,$dWhere1,$dOrder1); 
						$countData1 = count($arrayData1);
						/*****/
						$new_rowspan1 = 0;
						/*****/
						$sub_totalSupply = 0;
						$sub_totalUse = 0;
						/*****/
						foreach($arrayData1 as $key => $detail1){
							/*****/
							$entityTo 		= $detail1['Detail_EntityTo'];
							$ex_entityTo 	= explode(',',$entityTo);
							$count_entityTo	= 0;
							$entityFrom 	= $detail1['Detail_EntityFrom'];
							$ex_entityFrom 	= explode(',',$entityFrom);
							$count_entityFrom	= 0;
							/*****/
							$arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
							$count_arrayChem = count($arrayChem);
							/*****/
							$rowspan1 = max($count_entityTo,$count_entityFrom,$count_arrayChem);
							if($rowspan1==0 || $rowspan1=='') $rowspan1 = 1;
							/*****/
							$new_rowspan1 += $rowspan1;
							/*****/
						}
						foreach($arrayData1 as $key => $detail1){
							$countFirst++;
							$bil++;
							$total_bil++;
							/*****/
							$rowspan = max($countData1,$new_rowspan1);
							if($rowspan==0 || $rowspan=='') $rowspan = 1;
							/*****/
							$entityTo 		= $detail1['Detail_EntityTo'];
							$ex_entityTo 	= explode(',',$entityTo);
							$count_entityTo	= 0;
							$entityFrom 	= $detail1['Detail_EntityFrom'];
							$ex_entityFrom 	= explode(',',$entityFrom);
							$count_entityFrom	= 0;
							/*****/
							$arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
							$count_arrayChem = count($arrayChem);
							/*****/
							$rowspan1 = max($count_entityTo,$count_entityFrom,$count_arrayChem);
							if($rowspan1==0 || $rowspan1=='') $rowspan1 = 1;
							/*****/
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
									/*****/
									$phCategory = $detail1['Category_ID_ph'];
									$hhCategory = $detail1['Category_ID_hh'];
									$ehCategory = $detail1['Category_ID_eh'];
									/*****/
									$comb_hazCat = $phCategory;
									$comb_hazCat .= ($hhCategory!='') ? ( ($comb_hazCat!='') ? ','.$hhCategory : '' ) :'';
									$comb_hazCat .= ($ehCategory!='') ? ( ($comb_hazCat!='') ? ','.$ehCategory : '' ) :'';
									/*****/
									$totalSupply = $detail1['Detail_TotalSupply'];
									$sub_totalSupply += $totalSupply;
									/*****/
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
						echo "-". _get_StrFromCondition(
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
						if(count($arrayData1)==0){
		?>
		<tr class="contents" height="30">
			<td colspan="<?= (6-$minus) ?>" <?= $td_mid_center; ?>><?= _LBL_NO_RECORD ?></td>
		</tr>
		
		<?php
						}
					}
					//================================================state looping================================================\\
					if(count($arrayData)==0){
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
				if($total_bil>0){
					$url_pdf = 'report01_to_pdf_state.php?rptType='.$rptType.'&lvlType='.$lvlType.'&chemType='.$chemType.'&rptYear='.$rptYear.'&state='.$state.'&comp='.$comp;
					echo form_button('convert',_LBL_CONVERT_TO_PDF,array('type'=>'button','onClick'=>"funcPopup('".$url_pdf."');"));
					echo '&nbsp;';
				}
				echo form_button('close',_LBL_CLOSE,array('type'=>'button','onClick'=>'window.close()'));
			?></td>
		</tr>
	</table>
