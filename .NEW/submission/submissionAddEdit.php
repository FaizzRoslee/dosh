<?php
	//****************************************************************/
	// filename: empList.php
	// description: list of the employee
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
	include_once $sys_config["includes_path"]."arrayCommon.php";
	include_once $sys_config["includes_path"]."func_email.php";
	include_once $sys_config["includes_path"]."func_notification.php";
	include_once $sys_config["includes_path"]."func_date.php";
	/*****/
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
	$levelID = $_SESSION["user"]["Level_ID"];
	$userLevelID = $levelID;
	if($userLevelID==5) exit();
	/*****/
	func_header("",
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.css,". // css
				"", // css
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."javascript/js_validate.js,". // javascript
				// $sys_config["includes_path"]."jquery/Qtip/jquery.qtip-1.0.js,". // javascript
				// $sys_config["includes_path"]."jquery/Qtip/jquery.qtip-1.0.0-rc3-dm.min.js,". // javascript
				// $sys_config["includes_path"]."jquery/Qtip/jquery.qtip-1.0.0-rc3-dm.js,". // javascript
				// $sys_config["includes_path"]."jquery/Qtip/jquery.qtip.preload.js,". // javascript
				// $sys_config["includes_path"]."jquery/Others/jquery.validate.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.validation.1.8.1/jquery.validate.js,". // javascript
				$sys_config["includes_path"]."jquery/Others/jquery.formatCurrency-1.4.0.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.js,". // javascript
				// $sys_config["includes_path"]."jquery/jquery.livequery.js,". // javascript
				"",
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				"", // str url frame top
				"", // str url frame left
				"", // str url frame content
				false, // int top for [enter]
				""
				);
	/*****/
	if(isset($_POST["new"])) $new = $_POST["new"];
	elseif(isset($_GET["new"])) $new = $_GET["new"];
	else $new = 1;
	/*****/
	if(isset($_POST["submissionID"])) $submissionID = $_POST["submissionID"];
	elseif(isset($_GET["submissionID"])) $submissionID = $_GET["submissionID"];
	else $submissionID = 0;
	/*****/
	if($submissionID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	/*****/
	if($new==4) $formBack = "rejectedNewList.php";
	elseif($new==3) $formBack = "approvedNewList.php";
	elseif($new==2) $formBack = "allList.php";
	else $formBack = "submissionList.php";
	/*****/
	// if(isset($_POST)){ echo "<pre>"; print_r($_POST); echo "</pre>"; }
	if(isset($_POST["save"]) || isset($_POST["send"])){ 
		// die("submit sudah");
 		$chemType 	= isset($_POST["chemType"])		? $_POST["chemType"]	:"";
		$noSubMix 	= isset($_POST["noSubMix"])		? $_POST["noSubMix"]	:0;
		$remark 	= isset($_POST["remark"])		? $_POST["remark"]		:"";
		// $quantity 	= isset($_POST["quantity"])		? $_POST["quantity"]	:"";
		$disclaimer	= isset($_POST["disclaimer"])	? 1:0;

		if($chemType=="") $chemType = 0;
		$menu = $new;
		$file = $formBack;
		$dateNow = date("Y-m-d H:i:s");
		/*****/
		$arrData = array(
					"Type_ID"=>$chemType,
					"Submission_Remark"=>$remark,
					"Disclaimer"=>$disclaimer,
					"UpdateBy"=>$Usr_ID,
					"UpdateDate"=>$dateNow
				);
				
		if($new==3) $submissionID = 0; /* resubmit same info for next year submission */
				
		if($submissionID!=0){
			/*****/
			$sqlUpd	= "UPDATE tbl_Submission SET";
			foreach($arrData as $column => $value){
				$sqlUpd .= " $column = ". (!empty($value) ? quote_smart($value) : "NULL") .",";
			}
			$sqlUpd = substr_replace( $sqlUpd, "", -1 );
			$sqlUpd .= " WHERE Submission_ID = ". quote_smart($submissionID) ." LIMIT 1";
			$sqlUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*****/
			if($sqlUpd){ $msg1 = "Update Successfully"; }
			else{ $msg1 = "Update Unsuccessfully"; }
			func_add_audittrail($Usr_ID,$msg1." [".$submissionID."]","Submission","tbl_Submission,".$submissionID);
			/*****/
		}
		else{
			/*****/
			$arrData2 = array(
							"Status_ID"=>"1",
							"Usr_ID"=>$Usr_ID,
							"RecordBy"=>$Usr_ID,
							"RecordDate"=>$dateNow,
							// "Quantity"=>$quantity
					);
			$arrData = array_merge($arrData,$arrData2);
			/*****/
			$sqlIns = "INSERT INTO tbl_Submission (";
			foreach($arrData as $column => $value){
				$sqlIns .= "$column,";
			}
			$sqlIns = substr_replace( $sqlIns, "", -1 );
			$sqlIns .= ") VALUES (";
			foreach($arrData as $value){
				$sqlIns .= (!empty($value) ? quote_smart($value) : "NULL") .",";
			}
			$sqlIns = substr_replace( $sqlIns, "", -1 );
			$sqlIns .= ")";
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$submissionID = $db->sql_nextid();
			/*****/
			if($resIns){ $msg1 = "Submission Save Successfully"; }
			else{ $msg1 = "Submission Save Unsuccessfully"; }
			func_add_audittrail($Usr_ID,$msg1." [".$submissionID."]","Submission","tbl_Submission,".$submissionID);
			/*****/
			
			if(isset($_POST["send"])) $skipsave = 1; // submit the submission
		}
		
		$detailIDs = "";
		$trigger 	= isset($_POST["trigger"])	? explode(",",$_POST["trigger"]) : "";
		
		for($i=1;$i<=$noSubMix;$i++){
			/*****/
			$chkbx =  isset($_POST["chkSelect_$i"])?1:0;
			/*****/
			$detailID	 	= isset($_POST["detailID_$i"])		? $_POST["detailID_$i"]		: "";
			$productName 	= isset($_POST["productName$i"])	? $_POST["productName$i"]	: "";
			$idNo		 	= isset($_POST["idNo$i"])			? $_POST["idNo$i"]			: "";
			$physicalForm 	= isset($_POST["physicalForm$i"])	? $_POST["physicalForm$i"]	: "";
			if($physicalForm=="") $physicalForm = 0;
			/*****/
			$phCategory 	= isset($_POST["selectTo_PH_$i"])	? $_POST["selectTo_PH_$i"]	: "";
			$hhCategory 	= isset($_POST["selectTo_HH_$i"])	? $_POST["selectTo_HH_$i"]	: "";
			$ehCategory 	= isset($_POST["selectTo_EH_$i"])	? $_POST["selectTo_EH_$i"]	: "";
			$phCategory_new = $hhCategory_new = $ehCategory_new = "";
			if(is_array($phCategory) && count($phCategory)>0) $phCategory_new = implode(",",$phCategory);
			if(is_array($hhCategory) && count($hhCategory)>0) $hhCategory_new = implode(",",$hhCategory);
			if(is_array($ehCategory) && count($ehCategory)>0) $ehCategory_new = implode(",",$ehCategory);
			/*****/
			$totalSupply 	= isset($_POST["totalSupply$i"])	? $_POST["totalSupply$i"]	:""; //for levelID 6&8
			$totalUse	 	= isset($_POST["totalUse$i"])		? $_POST["totalUse$i"]		:""; //for levelID 7&8
			/*****/			
			$arrDetails = array(
							"Detail_ID_Number"=>$idNo,
							"Form_ID"=>$physicalForm,
							"Category_ID_ph"=>$phCategory_new,
							"Category_ID_hh"=>$hhCategory_new,
							"Category_ID_eh"=>$ehCategory_new,
							"Detail_TotalSupply"=>str_replace(",","",$totalSupply),
							"Detail_TotalUse"=>str_replace(",","",$totalUse),
							"Submission_ID"=>$submissionID,
							"UpdateDate"=>$dateNow
						);						
			
			if($chemType==2){
				$arrDetails2 = array("Detail_ProductName"=>$productName);
				$arrDetails = array_merge($arrDetails,$arrDetails2);
			}
			
			
			
			if($new==3){
				$detailID = ""; /* resubmit same info for next year submission */
				if(empty($physicalForm)) continue;
			}
			
			if(empty($detailID)){
				if(!in_array($i,$trigger) && $new!=3){
					continue; /* skip for empty new details of submission */
				}
				/*****/
				$arrDetails2 = array("RecordDate"=>$dateNow);
				$arrDetails = array_merge($arrDetails,$arrDetails2);
				/*****/
				$sqlIns = "INSERT INTO tbl_Submission_Detail (";
				foreach($arrDetails as $column => $value){
					$sqlIns .= "$column,";
				}
				$sqlIns = substr_replace( $sqlIns, "", -1 );
				$sqlIns .= ") VALUES (";
				foreach($arrDetails as $value){
					$sqlIns .= (!empty($value) ? quote_smart($value) : "NULL") .",";
				}
				$sqlIns = substr_replace( $sqlIns, "", -1 );
				$sqlIns .= ")";
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				$detailID = $db->sql_nextid();
				/*****/
				if($resIns){ $msg = "Submission Detail Insert Successfully"; }
				else{ $msg = "Submission Detail Insert Unsuccessfully"; }
				func_add_audittrail($Usr_ID,$msg." [".$detailID."]","Submission","tbl_Submission_Detail,".$detailID);
				/*****/
			}
			else{
				/*****/
				$sqlUpd	= "UPDATE tbl_Submission_Detail SET";
				foreach($arrDetails as $column => $value){
					$sqlUpd .= " $column = ". (!empty($value) ? quote_smart($value) : "NULL") .",";
				}
				$sqlUpd = substr_replace( $sqlUpd, "", -1 );
				$sqlUpd .= " WHERE Detail_ID = ". quote_smart($detailID) ." LIMIT 1";				
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				/*****/
				if($resUpd){ $msg = "Submission Detail Update Successfully"; }
				else{ $msg = "Submission Detail Update Unsuccessfully"; }
				func_add_audittrail($Usr_ID,$msg." [".$detailID."]","Submission","tbl_Submission_Detail,".$detailID);
				/*****/
			}
			$detailIDs .= $detailID .",";
			
			$noIngredient 	= isset($_POST["noIngredient$i"])	? $_POST["noIngredient$i"] : "";
			if(empty($noIngredient)) $noIngredient = 0;
			$subChemIDs = "";
			/*****/
			for($j=1;$j<=$noIngredient;$j++){
				
				$subChemID	 	= isset($_POST["subChemID_".$i."_".$j])		? $_POST["subChemID_".$i."_".$j]		:"";
				$chemicalID 	= isset($_POST["chemicalID_".$i."_".$j])	? $_POST["chemicalID_".$i."_".$j]		:"";
				$chemicalName 	= isset($_POST["chemicalName_".$i."_".$j])	? $_POST["chemicalName_".$i."_".$j]		:"";
				$composition 	= isset($_POST["composition_".$i."_".$j])	? $_POST["composition_".$i."_".$j]		:"";
				$composition 	= isset($_POST["composition2_".$i."_".$j])	? $_POST["composition2_".$i."_".$j]		:$composition;
				$source		 	= isset($_POST["source_".$i."_".$j])		? $_POST["source_".$i."_".$j]			:0;
				$nolonger		= isset($_POST["nolonger_".$i."_".$j])		? $_POST["nolonger_".$i."_".$j]			:"";
				$casNo		 	= isset($_POST["casNo_".$i."_".$j])			? $_POST["casNo_".$i."_".$j]			:"";
							
				if(empty($chemicalID)){
					/*****/
					$sqlNewChem	= "INSERT INTO tbl_Chemical ("
								. " Chemical_Name,Chemical_CAS,"
								. " RecordBy,RecordDate,UpdateBy,UpdateDate,isNew"
								. " ) VALUES ("
								. " ".quote_smart($chemicalName).",".quote_smart($casNo).","
								. " ".quote_smart($Usr_ID).",NOW(),".quote_smart($Usr_ID).",NOW(),1"
								. " )";
					$resNewChem	= $db->sql_query($sqlNewChem,END_TRANSACTION) or die(print_r($db->sql_error()));
					$chemicalID = $db->sql_nextid();
					/*****/
					if($resNewChem){ $msg = "Chemical Insert Successfully"; }
					else{ $msg = "Chemical Insert Unsuccessfully"; }
					func_add_audittrail($Usr_ID,$msg." [".$chemicalID."]","Inventory-New Chemical","tbl_Chemical,".$chemicalID);
					/*****/
				}
				
				if(!empty($chemicalID)){
					$chemicalName = $casNo = "";
				}
				if($chemType!=2){
					$composition = $source = "";
				}
				$arrSubChem	= array(
								"Sub_Chemical_Name"=>$chemicalName,
								"Sub_Chemical_CAS"=>$casNo,
								"Sub_Chemical_Composition"=>$composition,
								"Sub_Chemical_Source"=>$source,
								"StopDate"=>func_dmy2ymd($nolonger),
								"Chemical_ID"=>$chemicalID,
								"Detail_ID"=>$detailID,
								"UpdateDate"=>$dateNow
							);
											
				$arrChemical = array(
								"StopDate"=>$nolonger
							);

				if($new==3) $subChemID = ""; /* resubmit same info for next year submission */
				if(empty($subChemID)){
					$arrSubChem2 = array("RecordDate"=>$dateNow);
					$arrSubChem = array_merge($arrSubChem,$arrSubChem2);
					/*****/
					$sqlIns = "INSERT INTO tbl_Submission_Chemical (";
					foreach($arrSubChem as $column => $value){
						$sqlIns .= "$column,";
					}
					$sqlIns = substr_replace( $sqlIns, "", -1 );
					$sqlIns .= ") VALUES (";
					foreach($arrSubChem as $value){
						$sqlIns .= (!empty($value) ? quote_smart($value) : "NULL") .",";
					}
					$sqlIns = substr_replace( $sqlIns, "", -1 );
					$sqlIns .= ")";
					$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
					$subChemID = $db->sql_nextid();
					/*****/
					if($resIns){ $msg = "Submission Chemical List Insert Successfully"; }
					else{ $msg = "Submission Chemical List Insert Unsuccessfully"; }
					func_add_audittrail($Usr_ID,$msg." [".$subChemID."]","Submission","tbl_Submission_Chemical,".$subChemID);
					/*****/
				}
				else{
					$arrSubChem = array_merge($arrSubChem,$arrChemical);
					/*****/
					$sqlUpd	= "UPDATE tbl_Submission_Chemical SET";
					foreach($arrSubChem as $column => $value){
						$sqlUpd .= " $column = ". (!empty($value) ? quote_smart($value) : "NULL") .",";
					}
					$sqlUpd = substr_replace( $sqlUpd, "", -1 );
					$sqlUpd .= " WHERE Sub_Chemical_ID = ". quote_smart($subChemID) ." LIMIT 1";
					$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
					/*****/
					if($resUpd){ $msg = "Submission Chemical List Update Successfully"; }
					else{ $msg = "Submission Chemical List Update Unsuccessfully"; }
					func_add_audittrail($Usr_ID,$msg." [".$subChemID."]","Submission","tbl_Submission_Chemical,".$subChemID);
					/*****/
				}
				$subChemIDs .= $subChemID .",";
			}
			$subChemIDs = substr_replace( $subChemIDs, "", -1 );
			
			if(!empty($subChemIDs)){
				$sqlUpd	= "UPDATE tbl_Submission_Chemical SET isDeleted = 1, UpdateDate = NOW()"
						. " WHERE Sub_Chemical_ID NOT IN (". $subChemIDs .") AND Detail_ID = ". quote_smart($detailID);
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			}
		}
		$detailIDs = substr_replace( $detailIDs, "", -1 );
		
		if(!empty($detailIDs)){
			$sqlUpd	= "UPDATE tbl_Submission_Detail SET isDeleted = 1, UpdateDate = NOW()"
					. " WHERE Detail_ID NOT IN (". $detailIDs .") AND Submission_ID = ". quote_smart($submissionID);
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
		}
		
		/**********************************
		 * if user/client hit Save & Submit button without saving it first
		 **********************************/
		if(isset($_POST["send"])){ 
			$statusID = isset($_POST["statusID"])?$_POST["statusID"]:"";
			if(isset($skipsave) && $skipsave==1) $statusID = 1;
			$newStatusID = 11;
			include_once "sSubmit.php";
		}
					
		if(isset($_POST["save"])){
			$strJs = '<script language="Javascript">'
					. 'var r=confirm("'.$msg1.'\nContinue edit/update Submission");'
					. 'if (r==true){'
					. '/* x="You pressed OK!"; */'
					. '}'
					. 'else{'
					. 'parent.left.location.href="'.$sys_config['frame_path'].'left_submission.php?menu='.$menu.'";'
					. 'location.href="'.$file.'";'
					. '}'
					. '</script>'
					;
			echo $strJs;
		}
		else{
			$strJs = '<script language="Javascript">'
					. 'alert("'.$msg1.'");'
					. 'parent.left.location.href="'.$sys_config['frame_path'].'left_submission.php?menu='.$menu.'";'
					. 'location.href="'.$file.'";'
					. '</script>'
					;
			echo $strJs;
			exit();
		}
				
	}
	/*****/
	$chemType 	= "";
	$noSubMix	= "";
	$remark		= "";
	$statusID	= "";
	$disclaimer	= 0;
	$subSubmitID = "";
	// $quantity	= "";
	/*****/
	if($submissionID!=0){
		// $dataColumns = "Type_ID,Submission_Remark,Status_ID,Disclaimer,Quantity";
		$dataColumns = "Type_ID,Submission_Remark,Status_ID,Disclaimer,Submission_Submit_ID";
		$dataTable = "tbl_Submission";
		$dataJoin = "";
		$dataWhere = array("AND Submission_ID" => "= ".quote_smart($submissionID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		foreach($arrayData as $detail){
			$chemType 	= $detail["Type_ID"];
			$noSubMix 	= _get_RowExist("tbl_Submission_Detail",array("AND Submission_ID"=>"= ".quote_smart($submissionID), "AND isDeleted"=>"= 0"));
			$remark		= $detail["Submission_Remark"];
			$statusID 	= $detail["Status_ID"];
			$disclaimer = $detail["Disclaimer"];
			$subSubmitID = $detail["Submission_Submit_ID"];
			// $quantity 	= $detail["Quantity"];
		}
	}
	$noSubMix = isset($_POST["noSubMix"])?$_POST["noSubMix"]:$noSubMix;
	$noRow = ($noSubMix!="")?$noSubMix:0;
	$requiredField = '<span style="color:red"> * </span>';
	
	$spLoad = '<span class="sp-load"> <img src="'. $sys_config['images_path'] .'ui-anim_basic_16x16.gif"> </span>';
	
?>
	<style>
		label.error 	{ float: none; color: red; padding-left: .5em; vertical-align: top; }
		div.error 		{ display: none; }
		/*
		input.checkbox 	{ border: none }
		input.error 	{ border: 1px dotted red; }
		textarea.error 	{ border: 1px dotted red; }
		select.error 	{ border: 1px dotted red; }
		*/
	</style>

	<script language="Javascript">
	var reload = [],
		reload2 = [];
	jQuery(document).ready(function() {
		$().UItoTop({ easingType: "easeOutQuart" });
			
		/*
		 * for both imported/manufactured type of company
		 */
		<?php 
		/*
		<?php if(!in_array($levelID,array(6,7))){ ?>
		$(".cls-both").show();
		if( $("#submissionID").val()!="" ){
			if( $(".cls-import").is(":visible") && $(".cls-manufacture").is(":visible") ){
				$("input[name=quantity][value='']").filter("checked",true);
			}
			else{
				if( $(".cls-import").is(":visible") && !$(".cls-manufacture").is(":visible") )
					$("input[name=quantity][value=import]").filter("checked",true);
				else if( !$(".cls-import").is(":visible") && $(".cls-manufacture").is(":visible") )
					$("input[name=quantity][value=manufacture]").filter("checked",true);
			}
		}
		<?php }else{ ?>
		$(".cls-both").hide();
		<?php } ?>
		*/ 
		?>
		
		$("#submission-details,#submission-remark,#submission-footer").hide();
		$(".numbersonly").numeric();
		$("[title]").tooltip();
		// $("input[title]").livequery(reloadQtipTitle);

		$("#myForm").validate({
			ignore: ".ignore",
			ignoreTitle: true,
			invalidHandler: function(form, validator) {
				var errors = validator.numberOfInvalids();
				// alert(errors);return false;
				if (errors) {
					$("div.error").show();
					$("#send, #save").button('reset');;
					$(".newCreated").remove();
				} else {
					$("div.error").hide();
				}
			}
		});
		
		$("#send,#save").on("click",function(){
			$("label.error").hide();
			$(this).button("loading");
			// $(this).button("disable").find(".sp-load").show();
			
			if( $("#trigger").val()=="" ) {
				// alert("empty");
				triggerReload("tbl1");
					
				$ingredient = $("input[name=noIngredient1]");
				if( $ingredient.length > 0 ){
					if( $ingredient.val()!="" ){
						$("label.error[for=noIngredient1]").empty().hide().removeClass("error");
					}
				}
			}
			else{
				// alert("trigger not empty");
				var valueTrigger = $("#trigger").val(),
					countReload = 0,
					ar_valueTrigger = [];
					
				ar_valueTrigger = valueTrigger.split(",");
				$.each(ar_valueTrigger, function(index, value) {
						
					$ingredient = $("input[name=noIngredient"+ value +"]")
					if( $ingredient.length > 0 ){
						if( $ingredient.val()!="" ){
							$("label.error[for=noIngredient"+ value +"]").empty().hide().removeClass("error");
						}
					}
					
					if( jQuery.inArray(value, reload2) == -1 ){
						countReload++;
						triggerReload( "tbl" + value );
					}
					
					if(countReload==2) return false;
				});
			}
			
			$(".slt_left select option").attr("selected", "selected");
			$("#div_title").append('<input type="hidden" name="'+ $(this).attr("id") +'" class="newCreated" />');
			// alert( $(this).attr("id") +" "+ $("#trigger").val() );
			
			if( $("label.error:visible").length > 0 ){
				$(this).button('reset');
				return false;
			}
			else{
				$(this).button('reset');
				return true;
			}
		});
		
		$(".sp-load").hide();
		/* $("button").removeClass("btn-new-style").button(); */
		
		$("#noSubMix").on("blur keypress keyup",function(){
			// $(this).closest("td").find(".error").hide();
			$("label.error[for=noSubMix]").html("");
			$("label.error[for=noSubMix]").hide();
			$(this).removeClass("error");
		}).trigger("blur");
		
		$("#go").on("click",function(){
			$("#noSubMix").trigger("blur");
			
			if( ($("#chemType").val()==1 || $("#chemType").val()==2) && ($("#noSubMix").val()!="" || $("#noSubMix").val()!=0) ){
				
				if( $("#noSubMix").val()!=0 ){
					
					if( $("#chemType").val() != $("#prevType").val() ){ 
						$("#trigger,#trigger2").val("");
						reload = [];
						reload2 = []; 
					}
					
					$("#submission-details,#submission-remark,#submission-footer").show();
					// $("#go").button("disable").find(".sp-load").show();
					$("#go").button('loading');
					$.ajax({
						type: "POST",  
						// dataType: "json",
						url: "<?= $sys_config["submission_path"] ."submission-ajax.php"; ?>",
						data: {
							fn: "submission",
							chemType: function(){ return $("#chemType").val() },
							quantity: function(){ return $("#noSubMix").val() },
							levelID: "<?= $levelID ?>",
							submissionID: "<?= $submissionID ?>"
						},
						success: function(msg){
							$("#go").button('reset');
							$("#submission-details").html( msg );

							$(".cl_chkbx").hide();
							
							if($("button[name=gomixture]").hasClass("btn-new-style"))
								$("button[name=gomixture]").removeClass("btn-new-style").addClass("btn btn-primary btn-xs");
							
							$('[title]').tooltip({ html: true });
						
							$("#trigger2").val("");
							reload2 = [];
							return false;
						}					
					});
					
				}
				
				$("#noSubMix").trigger("blur");
			}
			else{
				$("#submission-details,#submission-remark,#submission-footer").hide();
				$("#submission-details").empty();
				return false;
			}
		}).trigger("click");
		
		$(document).on("click", "button[name=gomixture]", function(){
			var ingredient = $(this).closest("td").find("input").val();
			var tableid = $(this).closest("table").attr("id");
			var id = tableid.replace("tbl","");
			
			if(ingredient>0){
				$(this).closest("td").find("label.error").hide();
				$.ajax({
					type: "POST",  
					dataType: "json",
					url: "<?= $sys_config["submission_path"] ."submission-ajax.php"; ?>",
					data: {
						fn: "mixturechemical",
						row: id,
						mixture: ingredient,
						detailID: function(){ return $("#detailID_"+ id).val() }
					},
					success: function(msg){
						// alert( msg.dData );
						$("#ingredient-"+ tableid +" tbody").html( msg.dData );
						$("#"+ tableid).find(".cls-ingredient").show();
						$('[title]').tooltip({ html: true });
						
						if( $("#chemType").val()==2 ){ reloadMixFunction(tableid); reloadRequired(tableid); }
					}
				});
			}
			else{
				$(this).closest("table").find(".cls-ingredient").hide();
			}
		});
	
		$(".cls-disclaimer").on("click",function(){
			if( $(this).is(":checked") ){
				$("#spanSubmit").show();
			}
			else{ $("#spanSubmit").hide(); }
		});
		
		$(document).on("click",".getSubChemical, .getMixChemical", function(){
			var c = "",
				i = "",
				j = "",
				tblid = "",
				toRemove;
			if( $(this).hasClass("getSubChemical") ){ // substance
				toRemove = "tbl";
				tblid = $(this).closest("table").attr("id");
				i = tblid.replace(toRemove,"");
				
				j = 1;
			}
			else{ // mixture
				c = "&c=true";
				
				toRemove = "ingredient-tbl";
				tblid = $(this).closest("table").attr("id");
				i = tblid.replace(toRemove,"");
				
				var elemid = $(this).attr("id");
				var ar_elemid = elemid.split("_");
				j = ar_elemid[2];
			}
			
			url = "<?= $sys_config["submission_path"] ?>chemicalList.php?i=" + i + "&j=" + j + c;
			funcPopup(url,950,650);
		});
		
		$(document).on("change", ".eitherOne", eitherOne);
		// $(document).on("click",".cls-delete img", emptyRow);
		
		$("#cancel11").on("click",function(){
			var r=confirm("<?= _CANCEL_CONFIRMATION ?>");
			if (r==true){
				redirectForm("<?= $formBack ?>");
			}
		});
		
		$(document).on("click", ".cls-click-detect", function(){
			triggerReload( $(this).attr("id") );
			return false;
		});
		
		$(document).on("click", ".slt_right,.slt_left", function(){
			var tableid = $(this).parents("table").eq(1).attr("id");
			var row = tableid.replace("tbl","");
			if( jQuery.inArray(row, reload) == -1 ){
				if( $(this).hasClass("slt_right") ){
					$(this).find("option").removeAttr("selected");
				}
				triggerReload( tableid );
			}
		});
		
		$(document).on("click", ".choose,.remove", function(){
			var id = $(this).attr("id");
			var rel = $(this).attr("rel");
			var ex_rel = rel.split("-");
			
			$("#select"+ ex_rel[0] +"_"+ id +" option:selected").remove().prependTo("#select"+ ex_rel[1] +"_"+ id);

			if( $(this).hasClass("choose") )
				optionDisabled(ex_rel[1],id,ex_rel[0]);
			else optionDisabled(ex_rel[0],id,ex_rel[1]);

			sortingOption("select"+ ex_rel[1] +"_"+ id);
			// reloadQtipSelect("tbl" + id);
			return false;
		});

		$(document).on("click", ".rbSource", function(){
			$("#"+ $(this).attr("id") ).prop("checked",false);
			return false;
		});
		
		$(document).on("blur", ".numbersonly", function(){
			if($(this).val() != ""){
				$(this).removeClass("required");
				$(this).removeClass("error");
				$(this).closest("td").find("label.error").hide();
			}
		});
		
		$(document).on('click', '.cls-add-row', function(){
			var no_ingredient = $(this).closest('table[id^=tbl]').find('input[name^=noIngredient]').val();
			var row = $(this).closest('table').find('.cls-delete-row').length;
			
			var tableid = $(this).closest('table').attr('id');
			$('#' + tableid + ' tbody tr:last').clone().insertAfter('#' + tableid + ' tbody tr:last');
			$('#' + tableid + ' tbody tr:last').find('[title]').tooltip();
			$('#' + tableid + ' tbody tr:last').find('input, select').val('').prop('disabled', false);
			renumberIngredient(tableid);
			return false;
		});
		
		$(document).on('click', '.cls-delete-row', function(){
			var no_ingredient = $(this).closest('table[id^=tbl]').find('input[name^=noIngredient]').val();
			var row = $(this).closest('table').find('.cls-delete-row').length;
			
			var tableid = $(this).closest('table').attr('id');
			if(row>1){
				$(this).closest('tr').remove();
				renumberIngredient(tableid);
			} else {
				$(this).closest('tr').find('input, select').val('').prop('disabled', false);
			}
			return false;
		});
		
		$("#noSubMix").on("blur",function(){
			if($(this).val()>20){
				alert("<?= _LBL_NOT_EXCEED_20 ?>");
				$(this).focus();
				$("#go").prop("disabled", true);
			}
			else $("#go").prop("disabled", false);
		});
		
		$("html").on("keypress", function(e) {
			if(e.keyCode == 13) {
				alert("Enter Key was disabled");
				return false;
			}
		});
		
		///*
		$(document).on('dblclick', '.cls-show-detail', function(event){	
			$('.cls-show-detail').popover('destroy');
			if($('option:selected', this)){
				$('#myModal').find('.modal-title').empty().append( $('option:selected', this).text() );
				$.ajax({
					url: 'getCategoryDetails.php',
					type: 'POST',
					dataType: 'html',
					data: {
						categoryID: $('option:selected', this).val(),
					},
					success: function(data){						
						$('#myModal').find('.modal-body').empty().append(data);
						$('#myModal').modal('show');
					}
				});
			}
			return false;
		});		

		$(document).on('mouseleave', '.cls-show-detail', function(e) {
			$(this).popover('destroy');
		});
		   
		$(document).on('click', '.cls-show-detail', function(e) {
			$('.cls-show-detail').popover('destroy');
			$('#' + this.id).popover({
				trigger: 'manual',
				placement: 'top',
				title: '<?php echo _LBL_CLASSIFIED ?>',
				content: $('option:selected', this).text() + '<br /><strong><i>* Double click to see Details</i></strong>',
				html: true,
			}).popover('show'); 
		});
	});

	// /*
	(function($) {
		triggerReload = function(tableid){
			var row = tableid.replace("tbl","");
			if ( jQuery.inArray(row, reload) == -1 ) {
				reload[reload.length++] = row;
				reload2[reload2.length++] = row;
				
				reloadFunction( tableid );
				reloadQtipSelect( tableid );
				reloadRequired( tableid );
				if( $("#chemType").val()==2 ) reloadMixFunction("tbl1"); 
			}
			else{
				if ( jQuery.inArray(row, reload2) == -1 ) {
					reload2[reload2.length++] = row;
					
					reloadFunction( tableid );
					reloadQtipSelect( tableid );
					reloadRequired( tableid );
					if( $("#chemType").val()==2 ) reloadMixFunction("tbl1"); 
				}
			}
			
			reload = reload.sort(function(a,b){
						return a-b;
					});
			
			reload2 = reload2.sort(function(a,b){
						return a-b;
					});
			
			$("#trigger").val( reload );
			$("#trigger2").val( reload2 );
		}
	
		reloadFunction = function(tableid){	
			// $("#"+ tableid +" .disableSelect").hide();
			$("#"+ tableid +" .numbersonly").numeric();
			
			$("#"+ tableid +" .numberspointer").on("blur", function(){
				$(this).formatCurrency({ symbol:"", roundToDecimalPlace:1 });
			}).numeric({ allow:".", decimals:1 }).trigger("blur");
			
			$("#"+ tableid +" .dummy").each(function(){
				var id = $(this).attr("id");
				var ex_id = id.split("_"); /* index 0 (PH/HH/EH); index 1 row number */
				
				var to = "To_"+ ex_id[0];
				var row = ex_id[1];
				var from = "From_"+ ex_id[0];
				
				optionDisabled(to,row,from);
			});
			
			// alert("reloadFunction");
		}
		
		reloadMixFunction = function(tableid){
			$("#"+ tableid +" .numbers3decimals").on("blur", function(){
				$(this).formatCurrency({ symbol:"", roundToDecimalPlace:3 });
			}).numeric({ allow:".", decimals:3 }).trigger("change");
		}
		
		reloadQtipSelect = function(tableid){
			/*
			$("#"+ tableid +" .slt_right option").each(function() {
				// var categoryID = $(this).val();
				if($(this).data("qtip")) $(this).qtip("destroy");
				$(this).qtip({
					content: { 
						url: $(this).attr("title"),
						// method: "get",
						// data: "categoryID="+ categoryID,
						title: {
							text: "<?= _LBL_DESCRIPTION_FOR ?>:<br />[" + $(this).text() + "]", // Give the tooltip a title using each elements text
							button: "<?= _LBL_CLOSE ?>" // Show a close link in the title
						}
					},					
					style: {
						name: "blue", // Give it the preset dark style //dark/light/cream/green/red/blue
						border: { width: 0, radius: 4  }, 
						tip: true, // Apply a tip at the default tooltip corner
						width: { max: $(document).width()*0.9, min: 0 }
					},
					position: {
						target: $(document.body), // Display it at the...
						corner: 'center' // ...center of the screen
					},
					show: {
						when: "click", // Show it on click...
						solo: true,
						modal: {
							on: true, // No modal, but...
							blur: true // Don't hide if the overlay is clicked
						}
					},
					hide: "unfocus" // Don't hide on any event except close button
				});
			});
			
			$("#"+ tableid +" .slt_left option").each(function() {
				// var categoryID = $(this).val();
				if($(this).data("qtip")) $(this).qtip("destroy");
				$(this).qtip({
					content: { 
						url: $(this).attr("title"),
						// method: "get",
						// data: { categoryID: categoryID },
						title: {
							text: "<?= _LBL_DESCRIPTION_FOR ?>:<br />[" + $(this).text() + "]", // Give the tooltip a title using each elements text
							button: "<?= _LBL_CLOSE ?>" // Show a close link in the title
						}
					},					
					style: {
						name: "blue", // Give it the preset dark style //dark/light/cream/green/red/blue
						border: { width: 0, radius: 4  }, 
						tip: true, // Apply a tip at the default tooltip corner
						width: { max: $(document).width()*0.9, min: 0 }
					},
					position: {
						target: $(document.body), // Display it at the...
						corner: 'center' // ...center of the screen
					},
					show: {
						when: "click", // Show it on click...
						solo: true,
						modal: {
							on: true, // No modal, but...
							blur: true // Don't hide if the overlay is clicked
						}
					},
					hide: "unfocus", // Don't hide on any event except close button
				});		
			});
			*/
			return false;
		}
		
		reloadRequired = function(tableid){
			var row = tableid.replace("tbl","");
			$("#"+ tableid +" input[type=text], #"+ tableid +" select[class=slSource], #"+ tableid +" select[id=physicalForm"+ row +"]").addClass("required");
			
			if( $("#classified_"+ row).val() == 0 )
				$("#"+ tableid +" .slt_left select").addClass("required").attr("minlength","1");
			
			$("#"+ tableid +" .dateNo input[type=text], #"+ tableid +" .ignoreValidate input[type=text]").removeClass("required");
			
			if( $("#"+ tableid +" input[name^=noIngredient]").length > 0 ){
				if( $("#"+ tableid +" input[name^=noIngredient]").val()!="" ){
					// alert("removeClass");
					$("#"+ tableid +" input[name^=noIngredient]").removeClass("required");
					$("#"+ tableid +" input[name^=noIngredient]").addClass("ignore");
				}
			}
		}
		
		getHazardClass = function(i,id){
			$.ajax({
				type: "POST",  
				dataType: "json",
				url: "<?= $sys_config["submission_path"] ."submission-ajax.php"; ?>",
				data: {
					fn: "hazardclass",
					type: function(){ return $("#chemType").val() },
					id: id,
					row: i
				},
				success: function(msg){
					if( $("#chemType").val()==1 ){
						if(msg.classified==1){
							$("#selectFrom_PH_"+ msg.row).html( msg.fromPH ).attr("disabled",true);
							$("#selectTo_PH_"+ msg.row).html( msg.toPH ).removeClass("required error");
							$("#selectFrom_HH_"+ msg.row).html( msg.fromHH ).attr("disabled",true);
							$("#selectTo_HH_"+ msg.row).html( msg.toHH ).removeClass("required error");
							$("#selectFrom_EH_"+ msg.row).html( msg.fromEH ).attr("disabled",true);
							$("#selectTo_EH_"+ msg.row).html( msg.toEH ).removeClass("required error");
							
							$("#selectTo_PH_"+ msg.row, "#selectTo_HH_"+ msg.row, "#selectTo_EH_"+ msg.row).closest("td").find("div.error,label.error").hide();
							
							$("#tbl"+ msg.row).find(".disableSelect").show();
							$("#tbl"+ msg.row).find(".enableSelect").hide();
						}
						else{
							$("#selectFrom_PH_"+ msg.row).html( msg.fromPH ).removeAttr("disabled");
							$("#selectTo_PH_"+ msg.row).empty();
							$("#selectFrom_HH_"+ msg.row).html( msg.fromHH ).removeAttr("disabled");
							$("#selectTo_HH_"+ msg.row).empty();
							$("#selectFrom_EH_"+ msg.row).html( msg.fromEH ).removeAttr("disabled");
							$("#selectTo_EH_"+ msg.row).empty();
						
							$("#tbl"+ msg.row).find(".disableSelect").hide();
							$("#tbl"+ msg.row).find(".enableSelect").show();
						}
						$("#classified_"+ msg.row).val( msg.classified );
						reloadQtipSelect("tbl"+ msg.row);
					}
					return false;
				}
			});
		}
		
		eitherOne = function(){
			var nameSplit = $(this).attr("name").split("_");
			if( $(this).attr("type") == "text" ){
				$comp = $("select[name=composition_" + nameSplit[1] + "_" + nameSplit[2] + "]");
				if( $(this).val() != "" ){
					$comp.val("").prop("disabled", true);
					
					$(this).closest("td").find("label.error").attr("for","");
					$(this).closest("td").find("label.error").hide();
					$(this).closest("td").find("select,input").removeClass("error");
				}
				else{
					$comp.prop("disabled", false);
					$(this).closest("td").find("label.error").attr("for", $(this).attr("name") );
				}
			}
			else{
				$comp2 = $("input[name=composition2_" + nameSplit[1] + "_" + nameSplit[2] + "]");
				if( $(this).val() != "" ){
					$comp2.val("").prop("disabled", true);
					
					$(this).closest("td").find("label.error").attr("for","");
					$(this).closest("td").find("label.error").hide();
					$(this).closest("td").find("select,input").removeClass("error");
				}
				else{
					$comp2.prop("disabled", false);
					$(this).closest("td").find("label.error").attr("for", $(this).attr("name") );
				}
			}
			return false;
		}
		
		sortingOption = function(id){
			var mylist = $("#"+id);
			var listitems = mylist.children("option").get();
			listitems.sort(function(a, b) {
			   var nameA = $(a).text().toUpperCase();
			   var nameB = $(b).text().toUpperCase();
			   return (nameA < nameB) ? -1 : (nameA > nameB) ? 1 : 0;
			});
			$.each(listitems, function(idx, itm) { mylist.append(itm); });
			return false;
		}			
		
		optionDisabled = function(to,id,from){
			if( $("#select"+ to +"_"+ id +" option").val() ){
				var disAll = 0;
				
				$("#select"+ to +"_"+ id +" option").each(function(){
					var cID = $(this).val();
					var ar_optText = ["NONE:"];
					var optText = $.trim( $(this).text() );
				
					if( $.inArray( optText , ar_optText ) > -1 ){
						$("#select"+ to +"_"+ id +" option[value!=" + cID + "]").remove().appendTo("#select"+ from +"_"+ id).removeAttr("selected");
						disAll = 1;
					}	
				});
				
				var sameParent = "";
				$("#select"+ to +"_"+ id +" option").each(function(){
					var cID = $(this).val();
					
					$.ajax({ 
						type: "POST",  
						url: "<?= $sys_config["submission_path"]."check.php"; ?>",
						data: "cID="+cID,
						dataType: "json",
						success: function(msg){
							if(sameParent!="") sameParent += ",";
							sameParent += msg.cIDs;
							// alert(sameParent);
							if(sameParent!=""){
								var ex_parent = sameParent.split(",");
								$("#select"+ from +"_"+ id +" option").each(function(){
									var a = $(this).val();
									if($.inArray(a,ex_parent) >= 0){ // if false it will return value -1
										$(this).prop("disabled", true);
									}
									else{
										$(this).prop("disabled", false);
									}
								});
							}
						}
					});
				});
				
				if(disAll==1){
					$("#select"+ from +"_"+ id).prop("disabled", true);
				}
				
			}
			else{
				
				if( $("#classified_"+ id).val()==1 ){
					$("#select"+ from +"_"+ id).prop("disabled", true);
				}
				else{
					$("#select"+ from +"_"+ id +" option").each(function(){
						$(this).removeAttr("disabled");
					});
					
					if( $("#select"+ from +"_"+ id).attr("disabled") )
						$("#select"+ from +"_"+ id).prop("disabled", false);
				}
			}
			return false;
		}
		
		emptyRow = function(){
			$(this).closest("tr").find("input[type=text],input[type=hidden],select").val("");
			$(this).closest("tr").find(".eitherOne").prop("disabled", false);
			// $(this).closest("tr").find("input[type=radio]").attr("checked",false);
			$(this).closest("tr").find("td:eq(3) label.error").attr("for",$(this).closest("tr").find("td:eq(3) input[type=text]").attr("id"));
		}
	
		renumberIngredient = function(tableid){
			var itemNo = $('#' + tableid).closest('table[id^=tbl]').attr('id').replace ( /[^\d.]/g, '' );
			itemNo = parseInt(itemNo);
			var i = 0;
			$('#' + tableid + ' tbody tr').each(function(){
				$(this).find('td:eq(0)').html( ++i );
				$(this).find('input[name^=chemicalID_]').attr({ 'name': 'chemicalID_' + itemNo + '_' + i, 'id': 'chemicalID_' + itemNo + '_' + i });
				$(this).find('input[name^=chemicalName_]').attr({ 'name': 'chemicalName_' + itemNo + '_' + i, 'id': 'chemicalName_' + itemNo + '_' + i });
				$(this).find('label[for^=chemicalName_]').attr('for', 'chemicalName_' + itemNo + '_' + i);
				$(this).find('input[name^=casNo_]').attr({ 'name': 'casNo_' + itemNo + '_' + i, 'id': 'casNo_' + itemNo + '_' + i });
				$(this).find('select[name^=composition_]').attr({ 'name': 'composition_' + itemNo + '_' + i, 'id': 'composition_' + itemNo + '_' + i });
				$(this).find('input[name^=composition2_]').attr({ 'name': 'composition2_' + itemNo + '_' + i, 'id': 'composition2_' + itemNo + '_' + i });
				$(this).find('label[for^=composition_]').attr('for', 'composition_' + itemNo + '_' + i);
				$(this).find('label[for^=composition2_]').attr('for', 'composition2_' + itemNo + '_' + i);
				$(this).find('select[name^=source_]').attr({ 'name': 'source_' + itemNo + '_' + i, 'id': 'source_' + itemNo + '_' + i });
				$(this).find('label[for^=source_]').attr('for', 'source_' + itemNo + '_' + i);
			});
			$('#' + tableid).closest('table[id^=tbl]').find('input[name^=noIngredient]').val( i );
		}
	})(jQuery);
	// */
	</script>
	
	<?php func_window_open2( _LBL_SUBMISSION ." :: ".$addEdit); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<?= form_input("submissionID",$submissionID,array("type"=>"hidden")); ?>
	<?= form_input("statusID",$statusID,array("type"=>"hidden")); ?>
	<?= form_input("new",$new,array("type"=>"hidden")); ?>
	<?= form_input("trigger","",array("type"=>"hidden")); ?>
	<?= form_input("trigger2","",array("type"=>"hidden")); ?>
	<div id="div_title">
		<?php if($statusID==2){ ?>
		<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
			<tr class="contents">
				<td class="label title" colspan="2"><?= _LBL_REMARK_REASON_REJECT ?></td>
			</tr>
			<tr class="contents">
				<td width="20%" class="label"><?= _LBL_REMARK ?></td>
				<td width="80%"><label><?php
					$note = _get_StrFromManyConditions("tbl_Submission_Record","Sub_Record_Remark",
									array("AND Sub_Record_Status"=>"= 2","AND Submission_ID"=>"= ". quote_smart($submissionID),"ORDER BY"=>"Sub_Record_ID DESC")
								); 
					echo ($note!="")?nl2br($note):"-";
				?></label></td>
			</td>
			<tr class="contents">
				<td class="label"><?= _LBL_REASON ?></td>
				<td><label><?php
					$reason = _get_StrFromManyConditions("tbl_Submission_Record","Sub_Record_Reason",
									array("AND Sub_Record_Status"=>"= 2","AND Submission_ID"=>"= ".quote_smart($submissionID),"ORDER BY"=>"Sub_Record_ID DESC")
								); 
					echo ($reason!="")?nl2br($reason):"-";
				?></label></td>
			</td>
			<tr class="contents">
				<td class="label"><?= _LBL_REASON ?></td>
				<td><label><?= $subSubmitID ?><?= form_input("subSubmitID",$subSubmitID,array("type"=>"hidden")); ?></label></td>
			</td>
		</table>
		<br />
		<?php } ?>
		<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
			<tr class="contents">
				<td width="20%" class="label"><?= _LBL_CHEMICAL_TYPE ?></td>
				<td width="80%"><?php
					$colTypeName = "Type_Name". (($fileLang=="may.php")?"_may":"");
					if($submissionID==0){
						$dOptions = _get_arraySelect("tbl_Chemical_Type","Type_ID",$colTypeName,array("AND Active"=>"= 1"),"Type_ID");
						$dValue = isset($_POST["chemType"])?$_POST["chemType"]:$chemType;
						echo form_select("chemType",$chemType,$dOptions,$dValue,"");
						$chem_type = $dValue;
					}
					else{
						$dOptions = _get_arraySelect("tbl_Chemical_Type","Type_ID",$colTypeName,array("AND Active"=>"= 1"),"Type_ID");
						$dValue = isset($_POST["chemType"])?$_POST["chemType"]:$chemType;
						echo form_select("chemType1",$chemType,$dOptions,$dValue,array("disabled"=>"disabled"));
						$chem_type = $dValue;
						
						echo form_input("chemType",$chemType,array("type"=>"hidden"));
					}
					echo "&nbsp;";
					echo " ". _LBL_QUANTITY .":";
					
					$dOthers = array("type"=>"text","maxlength"=>"2","class"=>"numbersonly ignore text-center",
									"style"=>"width:30px","title"=>_LBL_MAXLENGTH_2 ."<br />". _LBL_NUMBER_ONLY ."<br />". _LBL_NOT_EXCEED_20, "data-placement" => "bottom");
					echo form_input("noSubMix",$noSubMix,$dOthers);
					echo "&nbsp;";
					echo form_button("go",_LBL_GO,array("type"=>"button", "data-loading-text" => _LBL_PROCESSING));
					echo addNote(_LBL_FILL_UP);
					echo '<div class="error"><label for="noSubMix" class="error">&nbsp;</label></div>';
				?></td>
			</tr>
			<tr class="contents" id="submission-remark">
				<td class="label"><?= _LBL_REMARK ?></td>
				<td><?= form_textarea("remark",$remark,array("cols"=>"60","rows"=>"3")); ?></td>
			</tr>
		</table>
	
		<div id="submission-details"></div>
	
		<div id="submission-footer">
		<br />
		<?php
			/**********************************
			 * update on 20141031
			 * within time period for submission or submission rejected need to resubmit
			 **********************************/
			// if(getValidTime($submissionID)==true || $statusID==2){
			if(getValidTime($submissionID,$statusID)==true){
		?>
		<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
			<tr class="contents">
				<td class="label title" colspan="2"><?= _LBL_TERM_COND ?></td>
			</tr>
			<tr class="contents">
				<td width="5%" align="center" valign="middle"><?php
					$dOthers = array("type"=>"checkbox","class"=>"cls-disclaimer");
					if(in_array($statusID,array(31,32,41,42))){
						if($new!=3) $dOthers = array_merge($dOthers,array("checked"=>"checked","readonly"=>"readonly"));
						else $disclaimer = 0;
					}
					else{
						if($disclaimer!=0) $dOthers = array_merge($dOthers,array("checked"=>"checked"));
					}		
					echo form_input("disclaimer",$disclaimer,$dOthers); ?></td>
				<td><label for="disclaimer"><?= _LBL_DISCLAIMER_DETAIL ?></label></td>
			</tr>
		</table>
		<br />
		<?php 
			} 
			/**********************************/
		?>
		<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
			<tr class="contents">
				<td align="center"><?php
					if($userLevelID<=2 || $userLevelID>=6){
						// echo form_button("save",_LBL_SAVE . $spLoad,array("type"=>"submit"));
						echo form_button("save",_LBL_SAVE,array("type"=>"submit", "data-loading-text" => _LBL_PROCESSING));
						echo "&nbsp;";
						
						/**********************************
						 * update on 20141031
						 * within time period for submission or submission rejected need to resubmit
						 **********************************/
						// if(getValidTime($submissionID)==true || $statusID==2){
						if(getValidTime($submissionID,$statusID)==true){
							echo '<span id="spanSubmit" style="display:'. (($disclaimer==1)?"":"none") .'">';
							// echo form_button("send",_LBL_SAVE ." & ". _LBL_SUBMIT  . $spLoad,array("type"=>"submit"));
							echo form_button("send",_LBL_SAVE ." & ". _LBL_SUBMIT, array("type"=>"submit", "data-loading-text" => _LBL_PROCESSING));
							echo "&nbsp;";
							echo "</span>";
						}
						/**********************************/
					}
					$dOthers = array("type"=>"button");
					echo form_button("cancel11",_LBL_CANCEL,$dOthers);
				?></td>
			</tr>
		</table>
		</div>
	</div>
	<br />
	</form>
	<?php func_window_close2(); ?>
	
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