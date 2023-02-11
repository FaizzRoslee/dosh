<?php
	//****************************************************************/
	// filename: sHistory.php
	// description: view the history of submission
	//****************************************************************/
	//include_once '../includes/sys_config.php';
	/*********/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*********/
	include_once $sys_config["includes_path"]."func_date.php";
	/*********/
	$arrayData = array();
	/*********/
	if($submissionID!=0){
		$dataColumns = "sd.Detail_ProductName,sd.Category_ID_ph,sd.Category_ID_hh,sd.Category_ID_eh,sc.*";
		$dataTable = "tbl_Submission_Detail sd";
		$dataJoin = array("tbl_Submission_Chemical sc"=>"sd.Detail_ID = sc.Detail_ID");
		$dataWhere = array("AND sd.Submission_ID" => "= ".quote_smart($submissionID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		
		$chemType = _get_StrFromCondition("tbl_Submission","Type_ID","Submission_ID",$submissionID);
	}
	// extractArray($arrayData);
	/*********/
	$textAlignCenter = "text-align:center";
?>	
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="5%" class="label" style="<?= $textAlignCenter ?>"><?= _LBL_NUMBER ?></td>
			<td width="30%" class="label" style="<?= $textAlignCenter ?>"><?= (($chemType==1) ? _LBL_PRODUCT_NAME : _LBL_CHEMICAL_NAME ); ?></td>
			<td width="15%" class="label" style="<?= $textAlignCenter ?>"><?= _LBL_CAS_NO ?></td>
			<?php if($chemType==2){ ?>
			<td width="25%" class="label" style="<?= $textAlignCenter ?>"><?= _LBL_CHEMICAL_SOURCE ?></td>
			<?php } ?>
			<?php
			/*
			<td width="15%" class="label" style="<?= $textAlignCenter ?>"><?= _LBL_DATE_NOLONGER ?></td>
			*/
			?>
			<td width="10%" class="label" style="<?= $textAlignCenter ?>"><?= _LBL_CLASSIFIED ?></td>
		</tr>
		<?php 
			$newArr = array();
			foreach ($arrayData as $key => $value) {
				if( isset($newArr[ $value["Detail_ID"] ]) ) $newArr[ $value["Detail_ID"] ]++;
				else $newArr[ $value["Detail_ID"] ] = 1;
			}
			// echo "<pre>";
			// print_r($newArr);
			// echo "</pre>";			
		
			$dID = $dataCurrent = 0;
			foreach($arrayData as $key => $detail){
				if($chemType==2 && $dID != $detail["Detail_ID"]){
					echo '<tr class="contents"><td colspan="6" class="label">'
						. _LBL_PRODUCT_NAME .': ['. (($detail['Detail_ProductName']!='')?$detail['Detail_ProductName']:'-') .']</td></tr>';
						
					$dataCurrent = 1;
				}
				else $dataCurrent++;
				
				$cID = $detail["Chemical_ID"];
				$dID = $detail["Detail_ID"];
				
				$dataColumns2 = "Lbl_Statement_ID,Lbl_Signal_ID,Lbl_Pictogram_ID,Category_ID";
				$dataTable2 = "tbl_Chemical_Classified";
				$dataJoin2 = "";
				$dataWhere2 = array("AND Chemical_ID" => "= ".quote_smart($cID),"AND Active" => "= 1");
				$arrayData2 = _get_arrayData($dataColumns2,$dataTable2,$dataJoin2,$dataWhere2); 
				// print_r($arrayData2);
				$classified = count($arrayData2);
				
				// $numbering = ($key+1);
				$numbering = $dataCurrent;
		?>
		<tr class="contents">
			<td style="<?= $textAlignCenter ?>" <?= (($chemType==1)?'rowspan="2"':"") ?>><label><?= $numbering ?></label></td>
			<td><label><?= nl2br(_get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$cID)); ?></label></td>
			<td style="<?= $textAlignCenter ?>"><label><?= _get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$cID) ?></label></td>
			<?php if($chemType==2){ ?>
			<td style="<?= $textAlignCenter ?>"><label><?php
				if($detail["Sub_Chemical_Source"]!=0){
					foreach($arrSource as $source){
						if($source["value"]==$detail["Sub_Chemical_Source"]) echo $source["label"]; 
					}
				}
				else{ echo "-"; }
			?></label></td>
			<?php } ?>
			<?php 
			/*
			<td style="<?= $textAlignCenter ?>"><label><?= (!empty($detail["StopDate"])?func_ymd2dmy($detail["StopDate"]):"-") ?></label></td>
			*/
			?>
			<td style="<?= $textAlignCenter ?>"><label><?= (($classified>0)?'<img src="'.$sys_config['images_path'].'icons/tick.gif" />':'-'); ?></label></td>
		</tr>
		<?php
				$categoryIDph = $detail["Category_ID_ph"];
				$categoryIDhh = $detail["Category_ID_hh"];
				$categoryIDeh = $detail["Category_ID_eh"];
				
				$arrCategoryID1 = explode(",",$categoryIDph);
				$arrCategoryID2 = explode(",",$categoryIDhh);
				$arrCategoryID3 = explode(",",$categoryIDeh);
				
				$arrCategoryID = array();
				if(count($arrCategoryID1) > 0) $arrCategoryID = array_merge($arrCategoryID,$arrCategoryID1); 
				if(count($arrCategoryID2) > 0) $arrCategoryID = array_merge($arrCategoryID,$arrCategoryID2); 
				if(count($arrCategoryID3) > 0) $arrCategoryID = array_merge($arrCategoryID,$arrCategoryID3);
				
				$arrCategoryID = array_filter($arrCategoryID); // remove empty value
				
				if( $chemType==1 || ($chemType==2 && $newArr[$dID] == $dataCurrent ) ){
		?>
		<tr class="contents">
			<td colspan="<?= (($chemType==2)?"5":"4") ?>">

			<div class="accordion">
				<h3><a href="#"><?= _LBL_CLASSIFIED ?></a></h3>
				<div><table class="contents" cellspacing="1" cellpadding="3" width="100%">
					<tr class="contents">
						<td width="20%" class="label" <?//= (($countRec>0)?'rowspan="'.$countRec.'"':'') ?>><?= _LBL_CLASSIFICATION_CODE ?></td>
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
										else{ $clName = '-'; }
										echo '<strong><u>'.$clName.'</u>:-</strong><br />';
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
				</table></div>
			</div>
			
			</td>
		</tr>
		<?php 
				}
			}
		?>
	</table>
