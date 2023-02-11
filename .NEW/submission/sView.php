<?php
	//****************************************************************/
	// filename: sView.php
	// description: view the submission detail
	//****************************************************************/
	//include_once '../includes/sys_config.php';
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	//==========================================================
	if($userLevelID==5) exit();
	
?>	
	<script language="Javascript">
	jQuery(document).ready(function(){
		$(".blink").blink();
	});
	</script>
	<?php if($statusID==2 || $statusID==4){ ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td class="label title" colspan="2"><?= _LBL_REMARK_REASON_REJECT ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REMARK ?></td>
			<td width="80%"><label><?php
				$note = _get_StrFromManyConditions("tbl_Submission_Record","Sub_Record_Remark",
								array("AND Sub_Record_Status"=>"IN (2,4)","AND Submission_ID"=>"= ".quote_smart($submissionID),"ORDER BY"=>"Sub_Record_ID DESC")
							); 
				echo ($note!="")?nl2br($note):"-";
			?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_REASON ?></td>
			<td><label><?php
				$reason = _get_StrFromManyConditions("tbl_Submission_Record","Sub_Record_Reason",
								array("AND Sub_Record_Status"=>"IN (2,4)","AND Submission_ID"=>"= ".quote_smart($submissionID),"ORDER BY"=>"Sub_Record_ID DESC")
							); 
				echo ($reason!="")?nl2br($reason):"-";
			?></label></td>
		</td>
		<tr class="contents">
			<td class="label"><?= _LBL_CONTACT_NO ?></td>
			<td><label><?php
				$contact = _get_StrFromManyConditions("tbl_Submission_Record","Sub_Record_Contact",
								array("AND Sub_Record_Status"=>"IN (2,4)","AND Submission_ID"=>"= ".quote_smart($submissionID),"ORDER BY"=>"Sub_Record_ID DESC")
							); 
				echo ($contact!="")?$contact:"-";
			?></label></td>
		</td>
	</table>
	<br />
	<?php } ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<?php if(!empty($subSubmitID) || $subSubmitID!=""){ ?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SUBMISSION_ID ?></td>
			<td width="80%"><label><?= $subSubmitID; ?></label></td>
		</tr>
		<?php } ?>
		<?php if($statusID > 30 && $statusID < 60){ ?>
		<tr class="contents">
			<td class="label"><?= _LBL_APPROVED_DATE ?></td>
			<td><label><?= func_ymd2dmy($approvedDate); ?></label></td>
		</tr>
		<?php } ?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_TYPE ?></td>
			<td width="80%"><label><?php 
				$colTypeName = "Type_Name". (($fileLang=="may.php")?"_may":"");
				echo _get_StrFromCondition("tbl_Chemical_Type",$colTypeName,"Type_ID",$detail["Type_ID"]);
				$noRow = $noSubMix;
			?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_REMARK ?></td>
			<td><label><?= $remark; ?><?= form_input("remark",$remark,array("type"=>"hidden")); ?></label></td>
		</tr>
	</table>
	<br />
	<?php		
		if($noRow>0){
			$i=1;
			//======================
			$dataColumns = "Detail_ID,Detail_ProductName,Detail_ID_Number,Form_ID,Category_ID_ph,Category_ID_hh,Category_ID_eh,"
						. "Detail_TotalSupply,Detail_TotalUse,ApprovedDate,RecordDate";
			$dataTable = "tbl_Submission_Detail";
			$dataJoin = "";
			$dataWhere = array("AND Submission_ID" => "= ".quote_smart($submissionID),"AND isDeleted"=>"= 0");
			$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
			foreach($arrayData as $detail){
				$detailID	 	= ($detail["Detail_ID"]!="")?$detail["Detail_ID"]:"-";
				//======================
				$productName 	= ($detail["Detail_ProductName"]!="")?$detail["Detail_ProductName"]:"-";
				$idNo		 	= ($detail["Detail_ID_Number"]!="")?$detail["Detail_ID_Number"]:"-";
				$physicalForm 	= _get_StrFromCondition("tbl_Chemical_Form","Form_Name","Form_ID",$detail["Form_ID"]);
				//======================
				$phCategory 	= ($detail["Category_ID_ph"]!="")?explode(",",$detail["Category_ID_ph"]):"-";
				$hhCategory 	= ($detail["Category_ID_hh"]!="")?explode(",",$detail["Category_ID_hh"]):"-";
				$ehCategory 	= ($detail["Category_ID_eh"]!="")?explode(",",$detail["Category_ID_eh"]):"-";
				//======================
				$totalSupply 	= (!empty($detail["Detail_TotalSupply"]))?$detail["Detail_TotalSupply"]:"";
				$totalUse	 	= (!empty($detail["Detail_TotalUse"]))?$detail["Detail_TotalUse"]:"";
				//======================
				$appDate	= $detail["ApprovedDate"];
				$recordDate	= $detail["RecordDate"];
				
				$isCWC = 0;
				$chemicalName = '';
				$casNo = '';
				
				$dataColumns = "Sub_Chemical_ID,Chemical_ID,Sub_Chemical_Name,Sub_Chemical_Composition,Sub_Chemical_Source,Sub_Chemical_CAS,StopDate";
				$dataTable = "tbl_Submission_Chemical";
				$dataJoin = "";
				$dataWhere = array("AND Detail_ID" => "= ".quote_smart($detailID),"AND isDeleted"=>"= 0");
				$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
				foreach($arrayData as $detail){
					$subChemID	 	= $detail["Sub_Chemical_ID"];
					$chemicalID 	= $detail["Chemical_ID"];
					$chemicalName 	= !empty($detail["Sub_Chemical_Name"])?$detail["Sub_Chemical_Name"]
																		:_get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$chemicalID);
					$composition 	= !empty($detail["Sub_Chemical_Composition"])?$detail["Sub_Chemical_Composition"]:"-";
					$source		 	= $detail["Sub_Chemical_Source"];
					$casNo			= !empty($detail["Sub_Chemical_CAS"])?$detail["Sub_Chemical_CAS"]
																		:_get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$chemicalID);
					$nolonger		= !empty($detail["StopDate"])?func_ymd2dmy($detail["StopDate"]):"-";
					
					$isCWC			= _get_StrFromCondition("tbl_Chemical","isCWC","Chemical_ID",$chemicalID);
				}
				
				if($chemType==1){ $titleAcc = _LBL_SUBSTANCE; }
				elseif($chemType==2){ $titleAcc = _LBL_MIXTURE; }
				else $titleAcc = "";
	?>
	
	<div id="accordian_<?= $i ?>" class="cl_accordion">
		<h3><a href="#"><?= $titleAcc ." ".$i ?></a></h3>
		<div>
					
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<?php 	if((empty($appDate) || $appDate=="") && $statusID>30){ ?>
		<tr class="contents">
			<td class="label"><?= _LBL_RECORD_UPDATE ?></td>
			<td colspan="3"><label><?= func_ymd2dmy($recordDate); ?></label></td>
		</tr>
		<?php 	} ?>
		<?php 
				if($chemType==1){ 
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PHYSICAL_FORM ?></td>
			<td colspan="3"><label><?= $physicalForm; ?></label></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_TRADE_PRODUCT ?></td>
			<td width="30%"><label><?= $chemicalName . (($isCWC==1 && $userLevelID<=5)?' <span class="blink"><b>[CWC]</b></span>' : ''); ?></label></td>
			<td width="20%" class="label"><?= _LBL_CAS_NO ?></td>
			<td width="30%"><label><?= $casNo; ?></label></td>
		</tr>
		<?php 
				} 
				elseif($chemType==2){ 
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_PRODUCT_NAME ?></td>
			<td colspan="3"><label><?= $productName; ?></label></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PHYSICAL_FORM ?></td>
			<td width="30%"><label><?= $physicalForm; ?></label></td>
			<td width="20%" class="label"><?= _LBL_ID_NUMBER ?></td>
			<td width="30%"><label><?= $idNo; ?></label></td>
		</tr>
		<?php 
					$rowIng = _get_RowExist("tbl_Submission_Chemical",array("AND Detail_ID"=>"= ".quote_smart($detailID), "AND isDeleted"=>"= 0"));
					if($rowIng>0){
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_INGREDIENT ?></td>
			<td colspan="3"><table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
				<tr class="contents">
					<td class="label" width="5%"><?= _LBL_NUMBER ?></td>
					<td class="label" width="40%"><?= _LBL_CHEMICAL_NAME ?></td>
					<td class="label" width="10%"><?= _LBL_CAS_NO ?></td>
					<td class="label" width="15%"><?= _LBL_COMPOSITION ?></td>
					<td class="label" width="20%"><?= _LBL_CHEMICAL_SOURCE ?></td>
					<!--
					<td class="label" width="10%"><?= _LBL_DATE_NOLONGER ?></td>
					-->
				</tr>
				<?php
						$j=1;
						//======================
						$dataColumns = "Sub_Chemical_ID,Chemical_ID,Sub_Chemical_Name,Sub_Chemical_Composition,Sub_Chemical_Source,Sub_Chemical_CAS,StopDate";
						$dataTable = "tbl_Submission_Chemical";
						$dataJoin = "";
						$dataWhere = array("AND Detail_ID" => "= ".quote_smart($detailID),"AND isDeleted"=>"= 0");
						$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
						foreach($arrayData as $detail){
							$subChemID	 	= $detail["Sub_Chemical_ID"];
							$chemicalID 	= $detail["Chemical_ID"];
							$chemicalName 	= !empty($detail["Sub_Chemical_Name"])?$detail["Sub_Chemical_Name"]
																				:_get_StrFromCondition("tbl_Chemical","Chemical_Name","Chemical_ID",$chemicalID);
							$composition 	= !empty($detail["Sub_Chemical_Composition"])?$detail["Sub_Chemical_Composition"]:"-";
							$source		 	= $detail["Sub_Chemical_Source"];
							$casNo			= !empty($detail["Sub_Chemical_CAS"])?$detail["Sub_Chemical_CAS"]
																				:_get_StrFromCondition("tbl_Chemical","Chemical_CAS","Chemical_ID",$chemicalID);
							$nolonger		= !empty($detail["StopDate"])?func_ymd2dmy($detail["StopDate"]):"-";
							
							$isCWC			= _get_StrFromCondition("tbl_Chemical","isCWC","Chemical_ID",$chemicalID);
							//======================
				?>
				<tr class="contents">
					<td align="center"><label><?= $j ?></label></td>
					<td><label><?= $chemicalName . (($isCWC==1 && $userLevelID<=5)?' <span class="blink"><b>[CWC]</b></span>' : ''); ?></label></td>
					<td><label><?= $casNo; ?></label></td>
					<td><label><?php
							if($composition!=0 && strlen($composition)==1){
								foreach($arrComposition as $detail){
									if($detail['value']==$composition) echo $detail['label']; 
								}
							}
							else{ 
								$composition = preg_replace("/[^0-9\.]/", "", $composition); 
								echo !empty($composition) ? $composition . "%" : "-" ; 
							}
					?></label></td>
					<td><label><?php
							if($source!=0){
								foreach($arrSource as $detail){
									if($detail["value"]==$source) echo $detail["label"]; 
								}
							}else{ echo "-"; }
					?></label></td>
					<!--<td align="center"><label><?= $nolonger ?></label></td>-->
				</tr>
				<?php
							$j++;
						}
				?>
			</table></td>
		</tr>
		<?php
					}
				}
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_HAZARD_CLASSIFICATION ?></td>
			<td colspan="3" align="center"><table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
				<tr class="contents">
					<td class="label" width="30%"><?= _LBL_PHY_HAZARD_CLASS ?></td>
					<td width="70%"><label><?php
						if(is_array($phCategory)){
							foreach($phCategory as $value){
								if($value!=''){
									echo '<a href="#" rel="categoryID-'.$value.'">';
									echo _get_StrFromCondition('tbl_Hazard_Category',
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										'Category_ID',$value);
									echo '</a>;<br />';
								}
							}
						}else{ echo '-';}
					?></label></td>
				</tr>
				<tr class="contents">
					<td class="label"><?= _LBL_HEALTH_HAZARD_CLASS ?></td>
					<td><label><?php
						if(is_array($hhCategory)){
							foreach($hhCategory as $value){
								if($value!=''){
									echo '<a href="#" rel="categoryID-'.$value.'">';
									echo _get_StrFromCondition('tbl_Hazard_Category',
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										'Category_ID',$value);
									echo '</a>;<br />';
								}
							}
						}else{ echo '-';}
					?></label></td>
				</tr>
				<tr class="contents">
					<td class="label"><?= _LBL_ENV_HAZARD_CLASS ?></td>
					<td><label><?php
						if(is_array($ehCategory)){
							foreach($ehCategory as $value){
								if($value!=''){
									echo '<a href="#" rel="categoryID-'.$value.'">';
									echo _get_StrFromCondition('tbl_Hazard_Category',
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										'Category_ID',$value);
									echo '</a>;<br />';
								}
							}
						}else{ echo '-';}
					?></label></td>
				</tr>
			</table></td>
		</tr>
		<?php 
				if($levelID!=7){ // == $levelID = 6/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_SUPPLY ?></td>
			<td colspan="3"><label><?= (!empty($totalSupply) ? $totalSupply : "0.0"); ?> <?= _LBL_TONNE_YEAR ?></label></td>
		</tr>
		<?php 
				}
				if($levelID!=6){ // == $levelID = 7/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_USE ?></td>
			<td colspan="3"><label><?= (!empty($totalUse) ? $totalUse : "0.0"); ?> <?= _LBL_TONNE_YEAR ?></label></td>
		</tr>
		<?php
				}
		?>
	</table>
	
		</div>
	</div>
	<?php
				$i++;
			}
		}
	?>
	
	
	
	<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="myModalLabel"></h4>
				</div>
				<div class="modal-body"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _LBL_CLOSE ?></button>
				</div>
			</div>
		</div>
	</div>