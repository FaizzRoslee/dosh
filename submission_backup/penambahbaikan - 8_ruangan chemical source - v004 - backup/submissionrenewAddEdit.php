<?php
	//****************************************************************/
	// filename: submissionrenewAddEdit.php
	// description: add/edit renew submission
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
	include_once $sys_config['includes_path'].'arrayCommon.php';
	include_once $sys_config['includes_path'].'func_email.php';
	include_once $sys_config['includes_path'].'func_notification.php';
	include_once $sys_config['includes_path'].'func_date.php';
	//==========================================================
	$Usr_ID = $_SESSION['user']['Usr_ID'];
	$levelID = $_SESSION['user']['Level_ID'];
	$userLevelID = $levelID;
	if($userLevelID==5) exit();
	//==========================================================
	func_header("",
				"", // css
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."javascript/js_validate.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				// $sys_config['includes_path']."jquery/Others/jquery.validate.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.validation.1.8.1/jquery.validate.js,". // javascript
				$sys_config['includes_path']."jquery/Others/jquery.formatCurrency-1.4.0.js,". // javascript
				"",
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false // int top for [enter]
				);
	//============================================================	
	if(isset($_POST['new'])) $new = $_POST['new'];
	elseif(isset($_GET['new'])) $new = $_GET['new'];
	else $new = 0;
	//============================================================		
	if(isset($_POST['submissionID'])) $submissionID = $_POST['submissionID'];
	elseif(isset($_GET['submissionID'])) $submissionID = $_GET['submissionID'];
	else $submissionID = 0;
	//============================================================
	if(isset($_POST['save']) || isset($_POST['send'])){
 		$chemType = isset($_POST['chemType'])?$_POST['chemType']:'';
		if($chemType=='') $chemType = 0;
		$noSubMix = isset($_POST['noSubMix'])?$_POST['noSubMix']:0;
		$remark 	= isset($_POST['remark'])?$_POST['remark']:'';
		// $disclaimer	= isset($_POST['disclaimer'])?1:0;
		$menu = 11;
		$file = "renewList.php";
		//============================================================	
		if($submissionID!=0){ //===================================================================================================
			$statusID = isset($_POST['statusID'])?$_POST['statusID']:'';
			if($statusID==31||$statusID==32||$statusID==51||$statusID==41||$statusID==42||$statusID==52){ //status: approved, expired
				$sqlIns	= "INSERT INTO tbl_Submission ("
						. " Submission_Submit_ID,Usr_ID,Type_ID,Status_ID,Submission_Remark,"
						. " RecordBy,RecordDate,UpdateBy,UpdateDate" 
						. " ) SELECT "
						. " Submission_Submit_ID,".quote_smart($Usr_ID).",Type_ID,3,".quote_smart($remark).","
						. " ".quote_smart($Usr_ID).",NOW(),".quote_smart($Usr_ID).",NOW()"
						. " FROM tbl_Submission WHERE Submission_ID = ".quote_smart($submissionID)."";
				//echo $sqlIns.'<br />';
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				$newSubmissionID = $db->sql_nextid();
				//==========================================================
				if($resIns){ $msg1 = 'Renew Submission Saved Successfully'; }
				else{ $msg1 = 'Renew Submission Saved Unsuccessfully'; }
				func_add_audittrail($Usr_ID,$msg1.' ['.$newSubmissionID.']','Submission-Renew','tbl_Submission,'.$newSubmissionID);
				//==========================================================			
				$sqlUpd	= "UPDATE tbl_Submission SET"
						. " Status_ID = ". (($statusID==31||$statusID==32||$statusID==51)?'33':'43') ."," //submission renew
						. " UpdateBy = ".quote_smart($Usr_ID).","
						. " UpdateDate = NOW()"
						. " WHERE Submission_ID = ".quote_smart($submissionID)." LIMIT 1";
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				//==========================================================
				if($resUpd){ $msg = 'Update Status Successfully - Submission Renew'; }
				else{ $msg = 'Update Status Unsuccessfully'; }
				func_add_audittrail($Usr_ID,$msg.' ['.$submissionID.']','Submission-Update','tbl_Submission,'.$submissionID);
				//===========================================================================================================
				$prevHist = _get_arrayData('Sub_Record_ID','tbl_Submission_Record','',array('AND Submission_ID'=>'= '.quote_smart($submissionID)));
				//==========================================================
				foreach($prevHist as $histDetail){
					if($histDetail['Sub_Record_ID']!=''){
						$sqlInsHist	= "INSERT INTO tbl_Submission_Record ("
									. " Sub_Record_Desc,Sub_Record_Remark,Sub_Record_Reason,"
									. " Sub_Record_By,Sub_Record_Date,Sub_Record_Status,Submission_ID"
									. ") SELECT"
									. " Sub_Record_Desc,Sub_Record_Remark,Sub_Record_Reason,"
									. " Sub_Record_By,Sub_Record_Date,Sub_Record_Status,".quote_smart($newSubmissionID)
									. " FROM tbl_Submission_Record"
									. " WHERE Sub_Record_ID = ".quote_smart($histDetail['Sub_Record_ID']);
						$resInsHist	= $db->sql_query($sqlInsHist) or die(print_r($db->sql_error()));
					}
				}
				//===========================================================================================================
				$submissionID = $newSubmissionID;
				//==========================================================
				for($i=1;$i<=$noSubMix;$i++){
					//==========================================================
					$chkbx =  isset($_POST['chkSelect_'.$i])?'1':'0';
					//==============================
					$detailID 		= isset($_POST['detailID_'.$i])?$_POST['detailID_'.$i]:'';
					$productName 	= isset($_POST['productName'.$i])?$_POST['productName'.$i]:'';
					$idNo		 	= isset($_POST['idNo'.$i])?$_POST['idNo'.$i]:'';
					$physicalForm 	= isset($_POST['physicalForm'.$i])?$_POST['physicalForm'.$i]:'';
					if($physicalForm=='') $physicalForm = 0;
					//==============================
					$phCategory 	= isset($_POST['selectTo_PH_'.$i])?$_POST['selectTo_PH_'.$i]:'';
					$phCategory_new = '';
					if(is_array($phCategory)){ foreach($phCategory as $index){
						$phCategory_new .= (($phCategory_new!='')?',':'');
						$phCategory_new .= (($index!='')?$index:'');
					}}
					//==============================
					$hhCategory 	= isset($_POST['selectTo_HH_'.$i])?$_POST['selectTo_HH_'.$i]:'';
					$hhCategory_new = '';
					if(is_array($hhCategory)){ foreach($hhCategory as $index){
						$hhCategory_new .= (($hhCategory_new!='')?',':'');
						$hhCategory_new .= (($index!='')?$index:'');
					}}
					//==============================
					$ehCategory 	= isset($_POST['selectTo_EH_'.$i])?$_POST['selectTo_EH_'.$i]:'';
					$ehCategory_new = '';
					if(is_array($ehCategory)){ foreach($ehCategory as $index){
						$ehCategory_new .= (($ehCategory_new!='')?',':'');
						$ehCategory_new .= (($index!='')?$index:'');
					}}
					//==============================
					$totalSupply 	= isset($_POST['totalSupply'.$i])?$_POST['totalSupply'.$i]:''; //for levelID 6&8
					//==============================
					$entityTo	 	= isset($_POST['entityTo_To_'.$i])?$_POST['entityTo_To_'.$i]:''; //for levelID 6&8
					// $entityTo_new = '';
					// if(is_array($entityTo)){ foreach($entityTo as $index){
						// $entityTo_new .= (($entityTo_new!='')?',':'');
						// $entityTo_new .= (($index!='')?$index:'');
					// }}
					//==============================
					$totalUse	 	= isset($_POST['totalUse'.$i])?$_POST['totalUse'.$i]:''; //for levelID 7&8
					//==============================
					$entityFrom	 	= isset($_POST['entityFrom_To_'.$i])?$_POST['entityFrom_To_'.$i]:''; //for levelID 7&8
					// $entityFrom_new = '';
					// if(is_array($entityFrom)){ foreach($entityFrom as $index){
						// $entityFrom_new .= (($entityFrom_new!='')?',':'');
						// $entityFrom_new .= (($index!='')?$index:'');
					// }}
					//==========================================================
					if($chkbx!=0){
						$sqlIns = "INSERT INTO tbl_Submission_Detail (";
						if($chemType==2) //if mixture
							$sqlIns .= " Detail_ProductName,";
						$sqlIns .= " Detail_ID_Number,Form_ID,"
								. " Category_ID_ph,Category_ID_hh,Category_ID_eh,";
						if($levelID!=7){
							if($totalSupply!='') $sqlIns .= " Detail_TotalSupply,";
							// $sqlIns .= " Detail_EntityTo,";
						}
						if($levelID!=6){
							if($totalUse!='') $sqlIns .= " Detail_TotalUse,";
							// $sqlIns .= " Detail_EntityFrom,";
						}
						$sqlIns .= " Submission_ID,RecordDate,UpdateDate";
						$sqlIns .= " ) VALUES (";
						if($chemType==2) //if mixture
							$sqlIns .= " ".quote_smart($productName).",";
						$sqlIns .= " ".quote_smart($idNo).",".quote_smart($physicalForm).","
								. " ".quote_smart($phCategory_new).",".quote_smart($hhCategory_new).",".quote_smart($ehCategory_new).",";
						if($levelID!=7){
							if($totalSupply!='') $sqlIns .= " ".quote_smart($totalSupply).",";
							// $sqlIns .= " ".quote_smart($entityTo_new).",";
						}
						if($levelID!=6){
							if($totalUse!='') $sqlIns .= " ".quote_smart($totalUse).",";
							// $sqlIns .= " ".quote_smart($entityFrom_new).",";
						}
						$sqlIns .= " ".quote_smart($submissionID).",NOW(),NOW()";
						$sqlIns .= " )";
						//echo $sqlIns.'<br />';
						$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
						$detailID = $db->sql_nextid();
						//==========================================================
						if($resIns){ $msg = 'Submission Detail Insert Successfully'; }
						else{ $msg = 'Submission Detail Insert Unsuccessfully'; }
						func_add_audittrail($Usr_ID,$msg.' ['.$detailID.']','Submission-Renew','tbl_Submission_Detail,'.$detailID);
						//==========================================================
						$noIngredient 	= isset($_POST['noIngredient'.$i])?$_POST['noIngredient'.$i]:'';
						if($noIngredient=='') $noIngredient = 0;
						for($j=1;$j<=$noIngredient;$j++){
							$subChemID	 	= isset($_POST['subChemID_'.$i.'_'.$j])?$_POST['subChemID_'.$i.'_'.$j]:'';
							$chemicalID 	= isset($_POST['chemicalID_'.$i.'_'.$j])?$_POST['chemicalID_'.$i.'_'.$j]:'';
							$chemicalName 	= isset($_POST['chemicalName_'.$i.'_'.$j])?$_POST['chemicalName_'.$i.'_'.$j]:'';
							$composition 	= isset($_POST['composition_'.$i.'_'.$j])?$_POST['composition_'.$i.'_'.$j]:'';
							$composition 	= isset($_POST['composition2_'.$i.'_'.$j])?$_POST['composition2_'.$i.'_'.$j]:$composition;
							$source		 	= isset($_POST['source_'.$i.'_'.$j])?$_POST['source_'.$i.'_'.$j]:0;
							$nolonger		= isset($_POST['nolonger_'.$i.'_'.$j])?func_dmy2ymd($_POST['nolonger_'.$i.'_'.$j]):'';
							$casNo		 	= isset($_POST['casNo_'.$i.'_'.$j])?$_POST['casNo_'.$i.'_'.$j]:'';
							//==========================================================
							if($chemicalName!=''){
								//==========================================================
								if($chemicalID==''){
									$sqlNewChem	= "INSERT INTO tbl_Chemical ("
												. " Chemical_Name,Chemical_CAS,"
												. " RecordBy,RecordDate,UpdateBy,UpdateDate,isNew"
												. " ) VALUES ("
												. " ".quote_smart($chemicalName).",".quote_smart($casNo).","
												. " ".quote_smart($Usr_ID).",NOW(),".quote_smart($Usr_ID).",NOW(),1"
												. " )";
									$resNewChem	= $db->sql_query($sqlNewChem,END_TRANSACTION) or die(print_r($db->sql_error()));
									$chemicalID = $db->sql_nextid();
									//==========================================================
									if($resNewChem){ $msg = 'Chemical Insert Successfully'; }
									else{ $msg = 'Chemical Insert Unsuccessfully'; }
									func_add_audittrail($Usr_ID,$msg.' ['.$chemicalID.']','Inventory-New Chemical','tbl_Chemical,'.$chemicalID);
									//==========================================================
								}
								//==========================================================
								$sqlIns = "INSERT INTO tbl_Submission_Chemical (";
								if($chemicalID=='') $sqlIns .= " Sub_Chemical_Name,Sub_Chemical_CAS,";
								if($composition!='') $sqlIns .= " Sub_Chemical_Composition,";
								$sqlIns .= " Sub_Chemical_Source,";
								$sqlIns .= " StopDate,";
								$sqlIns .= " Chemical_ID,Detail_ID,";
								$sqlIns .= " RecordDate,UpdateDate"
										. " ) VALUES (";
								if($chemicalID=='') $sqlIns .= " ".quote_smart($chemicalName).",".quote_smart($casNo).",";
								if($composition!='') $sqlIns .= " ".quote_smart($composition).",";
								$sqlIns .= " ".quote_smart($source).",";
								$sqlIns .= " ". (($nolonger!='')?quote_smart($nolonger):"NULL") .",";
								$sqlIns .= " ".quote_smart($chemicalID).",".quote_smart($detailID).",";
								$sqlIns .= " NOW(),NOW()"
										. " )";
								//echo $sqlIns.'<br />';
								$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
								$subChemID = $db->sql_nextid();
								//==========================================================
								if($resIns){ $msg = 'Submission Chemical List Insert Successfully'; }
								else{ $msg = 'Submission Chemical List Insert Unsuccessfully'; }
								func_add_audittrail($Usr_ID,$msg.' ['.$subChemID.']','Submission-Renew','tbl_Submission_Chemical,'.$subChemID);
								//==========================================================
							}
						}
					}
				}
			}
		}
		//==========================================================
		if(isset($_POST['send'])){
			$statusID = isset($_POST['statusID'])?$_POST['statusID']:'';
			$newStatusID = '12';
			include_once 'sSubmit.php';
		}
		//==========================================================
		echo '
			<script language="Javascript">
			alert("'.$msg1.'");
			parent.left.location.href="'.$sys_config['frame_path'].'left_submission.php?menu='.$menu.'";
			location.href="'.$file.'";
			</script>
			';
		exit();
	}
	//=====================================================================
	$subSubmitID	= '';
	$chemType 	= '';
	$noSubMix	= '';
	$remark		= '';
	$statusID	= '';
	// $disclaimer	= '0';
	//=====================================================================
	if($submissionID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Submission';
		$dataJoin = '';
		$dataWhere = array("AND Submission_ID" => "= ".quote_smart($submissionID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		foreach($arrayData as $detail){
			$subSubmitID = $detail['Submission_Submit_ID'];
			$chemType 	= $detail['Type_ID'];
			$noSubMix 	= _get_RowExist('tbl_Submission_Detail',array("AND Submission_ID"=>"= ".quote_smart($submissionID), "AND isDeleted"=>"= 0"));
			$remark		= $detail['Submission_Remark'];
			$statusID 	= $detail['Status_ID'];
			// $disclaimer = $detail['Disclaimer'];
		}
	}
	//============================================================
	$arrStatus = array(31,32,41,42,51,52);
	if(in_array($statusID,$arrStatus)) $addEdit = _LBL_ADD;
	else $addEdit = _LBL_EDIT;
	//============================================================
	$noSubMix = isset($_POST['noSubMix'])?$_POST['noSubMix']:$noSubMix;
	$noRow = ($noSubMix!='')?$noSubMix:0;
	//============================================================
	$requiredField = '<span style="color:red"> * </span>';
?>
	<style>
		label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }

		div.error { display: none; }

		input.checkbox { border: none }
		input.error { border: 1px dotted red; }
		textarea.error { border: 1px dotted red; }
		select.error { border: 1px dotted red; }	
		/*
		input:focus { border: 1px dotted black; }
		textarea:focus { border: 1px dotted black; }
		select:focus { border: 1px dotted black; }
		*/
	</style>

	<script language="Javascript">
	$(document).ready(function() {

		$('#slt_right option').each(function() {
			// if($(this).data("qtip")) $(this).qtip("destroy");
			$(this).qtip({
				content: { 
					url: $(this).attr('title'),
					title: {
						text: '<?= _LBL_DESCRIPTION_FOR ?>:<br />[' + $(this).text() + ']', // Give the tooltip a title using each elements text
						button: '<?= _LBL_CLOSE ?>' // Show a close link in the title
					}
				},
				position: { 
					corner: { tooltip: 'bottomLeft', target: 'topLeft' },
					adjust: { 
						screen: true // Keep the tooltip on-screen at all times
					} 
				},
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: 650
				},
				show: {
					when: 'click', // Show it on click...
					solo: true // ...but hide all others when its shown
				},
				hide: 'unfocus'
			});
		});
		
		$('#slt_left option').each(function() {
			// if($(this).data("qtip")) $(this).qtip("destroy");
			$(this).qtip({
				content: { 
					url: $(this).attr('title'),
					title: {
						text: '<?= _LBL_DESCRIPTION_FOR ?>:<br />[' + $(this).text() + ']', // Give the tooltip a title using each elements text
						button: '<?= _LBL_CLOSE ?>' // Show a close link in the title
					}
				},
				position: { 
					corner: { tooltip: 'bottomRight', target: 'topRight' },
					adjust: { 
						screen: true // Keep the tooltip on-screen at all times
					} 
				},
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: 650
				},
				show: {
					when: 'click', // Show it on click...
					solo: true // ...but hide all others when its shown
				},
				hide: 'unfocus'
			});
		});
		
		$('img[title]').each(function() {
			// if($(this).data("qtip")) $(this).qtip("destroy");
			$(this).qtip({
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: { max: 350, min: 0 }
				}
			});
		});
		
		$('input[title]').each(function() {
			// if($(this).data("qtip")) $(this).qtip("destroy");
			$(this).qtip({
				position: { corner: { tooltip: 'leftMiddle', target: 'rightMiddle' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: { max: 350, min: 0 }
				}
			});
		});
		
		$( '.choose, .remove' ).click(function() {
			var id = $(this).attr('id');
			var rel = $(this).attr('rel');
			var ex_rel = rel.split('-');
			
			$('#select'+ ex_rel[0] +'_'+ id +' option:selected').remove().appendTo('#select'+ ex_rel[1] +'_'+ id);

			if($(this).hasClass('choose'))
				optionDisabled(ex_rel[1],id,ex_rel[0]);
			else
				optionDisabled(ex_rel[0],id,ex_rel[1]);

			sortingOption('select'+ ex_rel[0] +'_'+ id);
			sortingOption('select'+ ex_rel[1] +'_'+ id);

			return false;
		});
		
		$('.dummy').each(function(){
			var id = $(this).attr('id');
			var ex_id = id.split('_'); /* index 0 (PH/HH/EH); index 1 row number */
			
			var to = 'To_'+ ex_id[0];
			var row = ex_id[1];
			var from = 'From_'+ ex_id[0];
			
			optionDisabled(to,row,from);
		});
		
		function optionDisabled(to,id,from){
			if($('#select'+ to +'_'+ id +' option').val()){
				var disAll = 0;
				
				$('#select'+ to +'_'+ id +' option').each(function(){
					var cID = $(this).val();
					var ar_optText = ['Classification Not Possible:','Not Applicable:','Not Classified:'];
					var optText = $.trim( $(this).text() );
				
					if( $.inArray( optText , ar_optText ) > -1 ){
						$('#select'+ to +'_'+ id +' option[value!=' + cID + ']').remove().appendTo('#select'+ from +'_'+ id).removeAttr("selected");
						disAll = 1;
					}	
				});
				
				var sameParent = '';
				$('#select'+ to +'_'+ id +' option').each(function(){
					var cID = $(this).val();
					
					$.ajax({ 
						type: "POST",  
						url: "<?= $sys_config['submission_path']."check.php"; ?>",
						data: "cID="+cID,  
						success: function(cIDs){
							if(sameParent!='') sameParent += ',';
							sameParent += cIDs;
							// alert(sameParent);
							if(sameParent!=''){
								var ex_parent = sameParent.split(',');
								$('#select'+ from +'_'+ id +' option').each(function(){
									var a = $(this).val();
									if($.inArray(a,ex_parent) >= 0){ // if false it will return value -1
										$(this).attr('disabled','disabled');
									}else{
										$(this).removeAttr("disabled");
									}
								});
							}
						}
					});
				});
				
				if(disAll==1){
					$('#select'+ from +'_'+ id).attr('disabled','disabled');
				}
				
			}else{
				$('#select'+ from +'_'+ id +' option').each(function(){
					$(this).removeAttr("disabled");
				});
				
				if( $('#select'+ from +'_'+ id).attr("disabled") )
					$('#select'+ from +'_'+ id).removeAttr("disabled");
			}
		}

		function sortingOption(id){
			var mylist = $('#'+id);
			var listitems = mylist.children('option').get();
			listitems.sort(function(a, b) {
			   var nameA = $(a).text().toUpperCase();
			   var nameB = $(b).text().toUpperCase();
			   return (nameA < nameB) ? -1 : (nameA > nameB) ? 1 : 0;
			})
			$.each(listitems, function(idx, itm) { mylist.append(itm); });
		}
		
		$("#myForm").validate({
			ignore: ".ignore",
			ignoreTitle: true,
			invalidHandler: function(form, validator) {
				var errors = validator.numberOfInvalids();
				if (errors) {
					$("div.error").show();
				} else {
					$("div.error").hide();
				}
				/*
			},
			errorPlacement: function(error, element) {
				if ( element.is(":radio") ) {

				} else {
					error.appendTo( element.parent() );
				}
				*/
			}
		});
		
		$( '.dateNo input[type=text]' ).datepicker({
			showOn: 'button',
			buttonImageOnly: true,
			buttonImage: '<?= $sys_config['images_path'] ?>calendar/calendar.gif',
			buttonText: 'Calendar',		
			dateFormat: 'dd-mm-yy',
			gotoCurrent: true,
			showButtonPanel: true,
			showAnim: "drop",
			showOtherMonths: true, // show date on other month
			selectOtherMonths: true, // enable user to select date on other month while show the current month
			changeMonth: true,
			changeYear: true,
			yearRange: '2005:2020'
		});
		
		$('#go1, #send, #save').click(function() {
			$('.slt_left select option').each(function(i) { 
				$(this).attr("selected", "selected");  
			});
			$('.selectAll select option').each(function(i) { 
				$(this).attr("selected", "selected");  
			});
			$('.chemID').each(function(){
				var id = $(this).attr('id');
				$('#new'+id).val($(this).val());
			});
			if($(this).attr('id')=='go1'){
				if($('#myForm input, #myForm select').hasClass("required")){ $('#myForm input, #myForm select').removeClass("required").removeClass('error'); }
				$(this).closest('form').submit();
			}else{
				var countChk = 0;
				$('.cl_chkbx').each(function(){
					if($(this).is(':checked'))
						countChk++;
				});
				
				if(countChk==0){
					return false;
				}
			}
		});
		
		$('.cl_chkbx').hide().each(function(){
			$(this).attr('checked');
			if($(this).is(':checked')){				
				var id = $(this).attr('id');
				var ex_id = id.split('_');
				var row = ex_id[1];
				
				$('#tbl'+ row +' input[type=text], #tbl'+ row +' input[type=radio], #tbl'+ row +' select[id=physicalForm'+ row +']').addClass('required');
				if( $('#classified_'+ row).val() == 0 )
					$('#tbl'+ row +' .slt_left select').addClass('required').attr('minlength','1');
				$('#tbl'+ row +' .selectAll select').addClass('required').attr('minlength','1');
				
				$('.dateNo input[type=text], .ignoreValidate input[type=text]').removeClass('required');
			}
		});
		
		$(".numbersonly").numeric();
		$(".numberspointer").blur(function(){
			$(this).formatCurrency({ symbol:'', roundToDecimalPlace:1 });
		}).numeric({ allow:'.', decimals:1 }).trigger("blur");
		$(".numbers3decimals").blur(function(){
			$(this).formatCurrency({ symbol:'', roundToDecimalPlace:3 });
		}).numeric({ allow:'.', decimals:3 }).trigger("blur");
		
		var fn_eitherOne = function(){
			var nameSplit = $(this).attr("name").split("_");
			if( $(this).attr("type") == 'text' ){
				$comp = $("select[name=composition_" + nameSplit[1] + "_" + nameSplit[2] + "]");
				if( $(this).val() != '' ){
					$comp.val('');
					$comp.attr('disabled', 'disabled');
					
					$(this).closest("td").find("label.error").removeAttr("for");
					$(this).closest("td").find("label.error").hide();
					$(this).closest("td").find("select,input").removeClass("error");
				}
				else{
					$comp.removeAttr('disabled');
					$(this).closest("td").find("label.error").attr("for","composition2_" + nameSplit[1] + "_" + nameSplit[2]);
				}
			}
			else{
				$comp2 = $("input[name=composition2_" + nameSplit[1] + "_" + nameSplit[2] + "]");
				if( $(this).val() != '' ){
					$comp2.val('');
					$comp2.attr('disabled', 'disabled');
					
					$(this).closest("td").find("label.error").removeAttr("for");
					$(this).closest("td").find("label.error").hide();
					$(this).closest("td").find("select,input").removeClass("error");
					$(this).closest("td").find("label.error").attr("for","composition2_" + nameSplit[1] + "_" + nameSplit[2]);
				}
				else{
					$comp2.removeAttr('disabled');
				}
			}
		}
		
		$(".eitherOne").change(fn_eitherOne).trigger("change");
	});
	
	function init_validation(row){
		if($('#chkSelect_'+ row).is(':checked')){
			$( '#tbl'+ row +' input[type=text], #tbl'+ row +' input[type=radio], #tbl'+ row +' select[id=physicalForm'+ row +']' ).addClass('required');
			if( $('#classified_'+ row).val() == 0 )
				$('#tbl'+ row +' .slt_left select').addClass('required').attr('minlength','1');
			$('#tbl'+ row +' .selectAll select').addClass('required').attr('minlength','1');		
		}else{
			$( '#tbl'+ row +' input[type=text], #tbl'+ row +' input[type=radio], #tbl'+ row +' select[id=physicalForm'+ row +']' ).removeClass('required').removeClass('error');
			if( $('#classified_'+ row).val() == 0 )
				$('#tbl'+ row +' .slt_left select').removeClass('required').removeAttr('minlength').removeClass('error');
			$('#tbl'+ row +' .selectAll select').removeClass('required').removeAttr('minlength').removeClass('error');
			
			$('#tbl'+ row +' div.error').hide();
		}
		$('.dateNo input[type=text], .ignoreValidate input[type=text]').removeClass('required').removeClass('error');
	}

	
	function getChemID(i,j,composition){
		var c;
		if(composition) c = '&c=true';
		else c = '';
		url = '<?= $sys_config['submission_path'] ?>chemicalList.php?i=' + i + '&j=' + j + c;
		funcPopup(url,'900','600');
	}

	function emptyRow(i,j,c){
		$('#chemicalID_'+i+'_'+j).val('');
		$('#chemicalName_'+i+'_'+j).val('');
		if(c){ $('#composition_'+i+'_'+j).val(''); }
		$('#casNo_'+i+'_'+j).val('');
	}
	
	function func_check(chk_obj){
		//alert(chk_obj);
		e = document.myForm;
		len = e.elements.length;
		var i;
		for (i=0; i < len; i++) {
			if (e.elements[i].name=='disclaimer'){
				if(e.elements[i].checked=chk_obj.checked) {
					$('#spanSubmit').show();
					// document.getElementById('spanSubmit').style.display = '';
				}else{
					$('#spanSubmit').hide();
					// document.getElementById('spanSubmit').style.display = 'none';
				}
			}
		}
	}
	</script>
	<?php func_window_open2( _LBL_RENEW_SUBMISSION .' :: '.$addEdit); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<?= form_input('submissionID',$submissionID,array('type'=>'hidden')); ?>
	<?= form_input('statusID',$statusID,array('type'=>'hidden')); ?>
	<?= form_input('new',$new,array('type'=>'hidden')); ?>
	<div id="div_title">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_TYPE ?></td>
			<td width="80%"><?php
				$colTypeName = 'Type_Name'. (($fileLang=='may.php')?'_may':'');
				if($submissionID==0){
					$param1 = 'chemType';
					$param2 = '';
					$param3 = _get_arraySelect('tbl_Chemical_Type','Type_ID',$colTypeName,array('AND Active'=>'= 1'),'Type_ID');
					$param4 = isset($_POST[$param1])?$_POST[$param1]:$chemType;
					$param5 = '';
					echo form_select($param1,$param2,$param3,$param4,$param5);
					$chem_type = $param4;
				}else{
					$param1 = 'chemType1';
					$param2 = '';
					$param3 = _get_arraySelect('tbl_Chemical_Type','Type_ID',$colTypeName,array('AND Active'=>'= 1'),'Type_ID');
					$param4 = isset($_POST[$param1])?$_POST[$param1]:$chemType;
					$param5 = array('disabled'=>'disabled');
					echo form_select($param1,$param2,$param3,$param4,$param5);
					$chem_type = $param4;
					
					echo form_input('chemType',$chemType,array('type'=>'hidden'));
				}				
				echo '&nbsp;';		
				echo ' '. _LBL_QUANTITY .':';
				$param1 = 'noSubMix';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$noSubMix;
				$param3 = array('type'=>'text','maxlength'=>'2','class'=>'numbersonly',
								'style'=>'width:30px','title'=>_LBL_MAXLENGTH_2 .'<br />'. _LBL_NUMBER_ONLY);
				echo form_input($param1,$param2,$param3);
				echo '&nbsp;';
				echo form_button('go1',_LBL_GO,array('type'=>'button'));
				echo addNote(_LBL_FILL_UP);
				
				//$noRow = ($param2!='')?$param2:0;
			?></td>
		</tr>
		<?php if($noRow>0){ ?>
		<tr class="contents">
			<td class="label"><?= _LBL_REMARK ?></td>
			<td><?php 
				$param1 = 'remark';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$remark;
				$param3 = array('cols'=>'50','rows'=>'2');
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
		<?php } ?>
	</table>
	<?php		
		if($chem_type==1 && $noRow>0){
			$i=1;
			//======================
			$dataColumns = 'sd.*,sc.Sub_Chemical_ID,sc.Sub_Chemical_Name,sc.Sub_Chemical_CAS,sc.Sub_Chemical_Composition,sc.Chemical_ID,sc.StopDate';
			$dataTable = 'tbl_Submission_Detail sd';
			$dataJoin = array('tbl_Submission_Chemical sc'=>'sd.Detail_ID = sc.Detail_ID');
			$dataWhere = array("AND sd.Submission_ID" => "= ".quote_smart($submissionID),"AND sd.isDeleted"=>"= 0");
			$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
			foreach($arrayData as $detail){
				if($i>$noRow) break;
				$detailID	 	= $detail['Detail_ID'];
				$chemicalID 	= $detail['Chemical_ID'];
				$subChemID 		= $detail['Sub_Chemical_ID'];
				//======================
				//$productName 	= $detail['Detail_ProductName'];
				$physicalForm 	= $detail['Form_ID'];
				//======================
				$chemicalName 	= !empty($detail['Sub_Chemical_Name'])?$detail['Sub_Chemical_Name']:_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$chemicalID);
				$composition 	= $detail['Sub_Chemical_Composition'];
				$casNo			= !empty($detail['Sub_Chemical_CAS'])?$detail['Sub_Chemical_CAS']:_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$chemicalID);
				$nolonger		= func_ymd2dmy($detail['StopDate']);
				//======================
				$selectTo_PH 	= explode(',',$detail['Category_ID_ph']);
				$selectTo_HH 	= explode(',',$detail['Category_ID_hh']);
				$selectTo_EH 	= explode(',',$detail['Category_ID_eh']);
				//======================
				$totalSupply 	= $detail['Detail_TotalSupply'];
				$entityTo 		= explode(',',$detail['Detail_EntityTo']);
				$totalUse	 	= $detail['Detail_TotalUse'];
				$entityFrom 	= explode(',',$detail['Detail_EntityFrom']);
				//======================
				echo form_input('noIngredient'.$i,'1',array('type'=>'hidden'));
				//======================
				$param1 = 'detailID_'.$i; 
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$detailID; 
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
				//======================
				$param1 = 'chemicalID_'.$i.'_1'; 
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemicalID; 
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
				$chemID = $param2;
				//======================
				$param1 = 'newchemicalID_'.$i.'_1'; 
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemID; 
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
				$newchemID = $param2;
				//======================
				$param1 = 'subChemID_'.$i.'_1'; 
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$subChemID; 
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
				//======================
				if(in_array($statusID,$arrStatus)){ $str_onClick = "getChemID('".$i."','1');"; }
				else{ $str_onClick = ""; }
				//======================
				$classified = 0;
				$arr_catIDs = array();
				if($chemID!=''){
					$dTable = 'tbl_Chemical_Classified';
					$dWhere = array('AND Chemical_ID'=>'= '.quote_smart($chemID),'AND Active'=>'= 1');
					$rowExist = _get_RowExist($dTable,$dWhere);
					if($rowExist>0){
						$classified = 1;
						$catIDs = _get_StrFromManyConditions($dTable,'Category_ID',$dWhere);
						if($catIDs!='') $arr_catIDs = explode(',',$catIDs);
					}
				}
				echo form_input('classified_'.$i,$classified,array('type'=>'hidden'));
				//======================
				$chk_name = 'chkSelect_'.$i;
				$chk_value = isset($_POST[$chk_name])?'1':'0';
				$chk_checked = 'checked';
				//======================
				$tblID = 'tbl'.$i;
				//======================
				$labelTitle = '<label><input type="checkbox" name="'.$chk_name.'" id="'.$chk_name.'" value="1" '.$chk_checked
							. ' onClick="init_validation(\''.$i.'\');" class="cl_chkbx" />'. _LBL_NO_OF_SUBSTANCE .' '.$i .'</label>';
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" id="<?= $tblID ?>">
		<tr class="contents">
			<td class="label title" colspan="4"><?= $labelTitle ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PHYSICAL_FORM . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'physicalForm'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Chemical_Form','Form_ID','Form_Name',array('AND Active'=>'= 1'),'Form_ID');
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$physicalForm;
				$param5 = '';
				echo form_select($param1,$param2,$param3,$param4,$param5);
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_TRADE_PRODUCT . $requiredField ?></td>
			<td width="30%"><?php
				$param1 = 'chemicalName_'.$i.'_1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemicalName;
				$param3 = array('type'=>'text','readonly'=>'readonly',
								'style'=>'width:90%;','onClick'=>$str_onClick);
				echo form_input($param1,$param2,$param3);
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
			<td width="20%" class="label"><?= _LBL_CAS_NO ?></td>
			<td width="30%"><?php
				$param1 = 'casNo_'.$i.'_1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$casNo;
				$param3 = array('type'=>'text','readonly'=>'readonly',
								'onClick'=>$str_onClick);
				echo '<span class="ignoreValidate">'.form_input($param1,$param2,$param3).'</span>';
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PHY_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_PH_'.$i;
				$param7 = '';
				if($classified==1){ $param9 = $arr_catIDs; }
				else{
					if($chemID==$newchemID){ 
						if($chemID==$chemicalID){ $param9 = isset($_POST[$param6])?$_POST[$param6]:$selectTo_PH; }
						else{ $param9 = isset($_POST[$param6])?$_POST[$param6]:array(); }
					}
					else{ $param9 = array(); }
				}
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 1','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_PH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 1','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				if($classified==1){ $param5 = array_merge($param5,array('disabled'=>'disabled')); }
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				if($classified==1){ 
					echo '</td><td width="10%" align="center">'. _LBL_ADD .'&gt;<br />&lt;'. _LBL_REMOVE .'</td><td width="45%">';
				}else{
					echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_PH-To_PH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_PH-From_PH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				}
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('PH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HEALTH_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_HH_'.$i;
				$param7 = '';
				if($classified==1){ $param9 = $arr_catIDs; }
				else{
					if($chemID==$newchemID){ 
						if($chemID==$chemicalID){ $param9 = isset($_POST[$param6])?$_POST[$param6]:$selectTo_HH; }
						else{ $param9 = isset($_POST[$param6])?$_POST[$param6]:array(); }
					}
					else{ $param9 = array(); }
				}
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 2','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_HH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 2','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				if($classified==1){ $param5 = array_merge($param5,array('disabled'=>'disabled')); }
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				if($classified==1){ 
					echo '</td><td width="10%" align="center">'. _LBL_ADD .'&gt;<br />&lt;'. _LBL_REMOVE .'</td><td width="45%">';
				}else{
					echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_HH-To_HH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_HH-From_HH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				}
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('HH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_ENV_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_EH_'.$i;
				$param7 = '';
				if($classified==1){ $param9 = $arr_catIDs; }
				else{
					if($chemID==$newchemID){ 
						if($chemID==$chemicalID){ $param9 = isset($_POST[$param6])?$_POST[$param6]:$selectTo_EH; }
						else{ $param9 = isset($_POST[$param6])?$_POST[$param6]:array(); }
					}
					else{ $param9 = array(); }
				}
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 3','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_EH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 3','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				if($classified==1){ $param5 = array_merge($param5,array('disabled'=>'disabled')); }
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				if($classified==1){ 
					echo '</td><td width="10%" align="center">'. _LBL_ADD .'&gt;<br />&lt;'. _LBL_REMOVE .'</td><td width="45%">';
				}else{
					echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_EH-To_EH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_EH-From_EH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				}
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('EH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<?php 
				if($levelID!=7){ // == $levelID = 6/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_SUPPLY . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'totalSupply'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$totalSupply;
				$param3 = array('type'=>'text','class'=>'numberspointer','title'=>_LBL_NUMBER_ONLY);
				echo form_input($param1,$param2,$param3) .' '. _LBL_TONNE_YEAR; 
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_LIST_SUPPLIED_ENTITY . $requiredField ?></td>
			<td colspan="3"><?php
				//======================
				$param6 = 'entityTo_To_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:$entityTo;
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'IN ('.$ids.')'),'Company_Name'
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'entityTo_From_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'NOT IN ('.$ids.')'),'Company_Name'
						);
				//echo '<pre>';print_r($param3);echo '</pre>';
				$param4 = array();
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo form_multiselect($param1,$param2,$param3,$param4,$param5);
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose2" rel="To_From-To_To" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove2"  rel="To_To-To_From" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="selectAll">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
			?></td>
		</tr>
		-->
		<?php 
				}
				if($levelID!=6){ // == $levelID = 7/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_USE . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'totalUse'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$totalUse;
				$param3 = array('type'=>'text','class'=>'numberspointer','title'=>_LBL_NUMBER_ONLY);
				echo form_input($param1,$param2,$param3) .' '. _LBL_TONNE_YEAR;
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_LIST_SUPPLIER_ENTITY . $requiredField ?></td>
			<td colspan="3"><?php
				//======================
				$param6 = 'entityFrom_To_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:$entityFrom;
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'IN ('.$ids.')'),'Company_Name'
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'entityFrom_From_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'NOT IN ('.$ids.')'),'Company_Name'
						);
				$param4 = array();
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo form_multiselect($param1,$param2,$param3,$param4,$param5);
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose2" rel="From_From-From_To" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove2"   rel="From_To-From_From" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="selectAll">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
			?></td>
		</tr>
		-->
		<?php 
				}
		?>
		<?php
			if($statusID==31||$statusID==32||$statusID==41||$statusID==42){
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_DATE_NOLONGER ?></td>
			<td colspan="3"><?php
				$param1 = 'nolonger_'.$i.'_1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$nolonger;
				$param3 = array('type'=>'text','style'=>'width:80px');
				echo '<span class="dateNo">'.form_input($param1,$param2,$param3).'</span>';
			?></td>
		</tr>
		<?php
			}
		?>
	</table>
	<?php
				$i++;
			}
			//======================
			for(;$i<=$noRow;$i++){
				//======================
				echo form_input('noIngredient'.$i,'1',array('type'=>'hidden'));
				//======================
				$param1 = 'detailID_'.$i.'_1'; 
				$param2 = isset($_POST[$param1])?$_POST[$param1]:''; 
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
				//======================
				$param1 = 'chemicalID_'.$i.'_1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
				$chemID = $param2;
				//======================
				$param1 = 'newchemicalID_'.$i.'_1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
				$newchemID = $param2;
				//======================
				$param1 = 'subChemID_'.$i.'_1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
				//======================
				$classified = 0;
				$arr_catIDs = array();
				if($chemID!=''){
					$dTable = 'tbl_Chemical_Classified';
					$dWhere = array('AND Chemical_ID'=>'= '.quote_smart($chemID),'AND Active'=>'= 1');
					$rowExist = _get_RowExist($dTable,$dWhere);
					if($rowExist>0){
						$classified = 1;
						$catIDs = _get_StrFromManyConditions($dTable,'Category_ID',$dWhere);
						if($catIDs!='') $arr_catIDs = explode(',',$catIDs);
					}
				}
				echo form_input('classified_'.$i,$classified,array('type'=>'hidden'));
				//======================
				$chk_name = 'chkSelect_'.$i;
				$chk_value = isset($_POST[$chk_name])?'1':'0';
				$chk_checked = 'checked';
				//======================
				$tblID = 'tbl'.$i;
				//======================
				$labelTitle = '<label><input type="checkbox" name="'.$chk_name.'" id="'.$chk_name.'" value="1" '.$chk_checked
							. ' onClick="init_validation(\''.$i.'\');" class="cl_chkbx" />'. _LBL_NO_OF_SUBSTANCE .' '.$i .'</label>';
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" id="<?= $tblID ?>">
		<tr class="contents">
			<td class="label title" colspan="4"><?= $labelTitle ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PHYSICAL_FORM . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'physicalForm'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Chemical_Form','Form_ID','Form_Name',array('AND Active'=>'= 1'),'Form_ID');
				$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param5 = '';
				echo form_select($param1,$param2,$param3,$param4,$param5);
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_TRADE_PRODUCT . $requiredField ?></td>
			<td width="30%"><?php
				$param1 = 'chemicalName_'.$i.'_1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','readonly'=>'readonly',
								'style'=>'width:90%;','onClick'=>"getChemID('".$i."','1');");
				echo form_input($param1,$param2,$param3);
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
			<td width="20%" class="label"><?= _LBL_CAS_NO ?></td>
			<td width="30%"><?php
				$param1 = 'casNo_'.$i.'_1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','readonly'=>'readonly',
								'onClick'=>"getChemID('".$i."','1');");
				echo '<span class="ignoreValidate">'.form_input($param1,$param2,$param3).'</span>'; 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PHY_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_PH_'.$i;
				$param7 = '';
				if($classified==1){ $param9 = $arr_catIDs; }
				else{
					if($chemID==$newchemID) $param9 = isset($_POST[$param6])?$_POST[$param6]:array(); 
					else $param9 = array();
				}
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 1','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_PH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 1','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				if($classified==1){ $param5 = array_merge($param5,array('disabled'=>'disabled')); }
				//======================
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				if($classified==1){ 
					echo '</td><td width="10%" align="center">'. _LBL_ADD .'&gt;<br />&lt;'. _LBL_REMOVE .'</td><td width="45%">';
				}else{
					echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_PH-To_PH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_PH-From_PH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				}
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('PH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HEALTH_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_HH_'.$i;
				$param7 = '';
				if($classified==1){ $param9 = $arr_catIDs; }
				else{
					if($chemID==$newchemID) $param9 = isset($_POST[$param6])?$_POST[$param6]:array(); 
					else $param9 = array();
				}
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 2','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4','class'=>'selectAll');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_HH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 2','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				if($classified==1){ $param5 = array_merge($param5,array('disabled'=>'disabled')); }
				//======================
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				if($classified==1){ 
					echo '</td><td width="10%" align="center">'. _LBL_ADD .'&gt;<br />&lt;'. _LBL_REMOVE .'</td><td width="45%">';
				}else{
					echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_HH-To_HH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_HH-From_HH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				}
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('HH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_ENV_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_EH_'.$i;
				$param7 = '';
				if($classified==1){ $param9 = $arr_catIDs; }
				else{
					if($chemID==$newchemID) $param9 = isset($_POST[$param6])?$_POST[$param6]:array(); 
					else $param9 = array();
				}
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 3','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_EH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 3','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				if($classified==1){ $param5 = array_merge($param5,array('disabled'=>'disabled')); }
				//======================
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				if($classified==1){ 
					echo '</td><td width="10%" align="center">'. _LBL_ADD .'&gt;<br />&lt;'. _LBL_REMOVE .'</td><td width="45%">';
				}else{
					echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_EH-To_EH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove" rel="To_EH-From_EH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				}
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('EH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<?php 
				if($levelID!=7){ // == $levelID = 6/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_SUPPLY . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'totalSupply'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','class'=>'numberspointer','title'=>_LBL_NUMBER_ONLY);
				echo form_input($param1,$param2,$param3) .' '. _LBL_TONNE_YEAR;
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_LIST_SUPPLIED_ENTITY . $requiredField ?></td>
			<td colspan="3"><?php
				//======================
				$param6 = 'entityTo_To_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:array();
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'IN ('.$ids.')'),'Company_Name'
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'entityTo_From_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'NOT IN ('.$ids.')'),'Company_Name'
						);
				$param4 = array();
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo form_multiselect($param1,$param2,$param3,$param4,$param5);
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose2" rel="To_From-To_To" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove2"   rel="To_To-To_From" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="selectAll">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
			?></td>
		</tr>
		-->
		<?php 
				}
				if($levelID!=6){ // == $levelID = 7/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_USE . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'totalUse'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','class'=>'numberspointer','title'=>_LBL_NUMBER_ONLY);
				echo form_input($param1,$param2,$param3) .' '. _LBL_TONNE_YEAR; 
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_LIST_SUPPLIER_ENTITY . $requiredField ?></td>
			<td colspan="3"><?php
				//======================
				$param6 = 'entityFrom_To_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:array();
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'IN ('.$ids.')'),'Company_Name'
						);
				$param0 = array('style'=>'width:100%','size'=>'4','class'=>'selectAll');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'entityFrom_From_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'NOT IN ('.$ids.')'),'Company_Name'
						);
				$param4 = array();
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo form_multiselect($param1,$param2,$param3,$param4,$param5);
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose2" rel="From_From-From_To" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove2"   rel="From_To-From_From" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="selectAll">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
			?></td>
		</tr>
		-->
		<?php 
				}
		?>
		<?php
			if($statusID==31||$statusID==32||$statusID==41||$statusID==42){
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_DATE_NOLONGER ?></td>
			<td colspan="3"><?php
				$param1 = 'nolonger_'.$i.'_1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','style'=>'width:80px');
				echo '<span class="dateNo">'.form_input($param1,$param2,$param3).'</span>';
			?></td>
		</tr>
		<?php
			}
		?>
	</table>
	<?php
			}
		}
		elseif($chem_type==2 && $noRow>0){
			//======================
			$i=1;
			//======================
			$dataColumns = '*';
			$dataTable = 'tbl_Submission_Detail';
			$dataJoin = '';
			$dataWhere = array("AND Submission_ID" => "= ".quote_smart($submissionID),"AND isDeleted"=>"= 0");
			$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
			foreach($arrayData as $detail){
				if($i>$noRow) break;
				$detailID	 	= $detail['Detail_ID'];
				//======================
				$productName 	= $detail['Detail_ProductName'];
				$idNo		 	= $detail['Detail_ID_Number'];
				$physicalForm 	= $detail['Form_ID'];
				//======================
				$selectTo_PH 	= explode(',',$detail['Category_ID_ph']);
				$selectTo_HH 	= explode(',',$detail['Category_ID_hh']);
				$selectTo_EH 	= explode(',',$detail['Category_ID_eh']);
				//======================
				$totalSupply 	= $detail['Detail_TotalSupply'];
				$entityTo 		= explode(',',$detail['Detail_EntityTo']);
				$totalUse	 	= $detail['Detail_TotalUse'];
				$entityFrom 	= explode(',',$detail['Detail_EntityFrom']);
				//======================
				$param1 = 'detailID_'.$i; 
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$detailID; 
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
				//======================
				$chk_name = 'chkSelect_'.$i;
				$chk_value = isset($_POST[$chk_name])?'1':'0';
				$chk_checked = 'checked';
				//======================
				echo form_input('classified_'.$i,'0',array('type'=>'hidden'));
				//======================
				$tblID = 'tbl'.$i;
				//======================
				$labelTitle = '<label><input type="checkbox" name="'.$chk_name.'" id="'.$chk_name.'" value="1" '.$chk_checked
							. ' onClick="init_validation(\''.$i.'\');" class="cl_chkbx" />'. _LBL_NO_OF_MIXTURE .' '.$i .'</label>';
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" id="<?= $tblID ?>">
		<tr class="contents">
			<td class="label title" colspan="4"><?= $labelTitle ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PRODUCT_NAME . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'productName'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$productName;
				$param3 = array('type'=>'text','style'=>'width:400px');
				echo form_input($param1,$param2,$param3); 
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PHYSICAL_FORM . $requiredField ?></td>
			<td width="30%"><?php
				$param1 = 'physicalForm'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Chemical_Form','Form_ID','Form_Name',array('AND Active'=>'= 1'),'Form_ID');
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$physicalForm;
				$param5 = '';
				echo form_select($param1,$param2,$param3,$param4,$param5);
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
			<td width="20%" class="label"><?= _LBL_ID_NUMBER ?></td>
			<td width="30%"><?php
				$param1 = 'idNo'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$idNo;
				$param3 = array('type'=>'text');
				echo '<span class="ignoreValidate">'.form_input($param1,$param2,$param3).'</span>'; 
			?></td>
		</tr>
		<?php
			$noIngredient = _get_RowExist('tbl_Submission_Chemical',array("AND Detail_ID"=>"= ".quote_smart($detailID), "AND isDeleted"=>"= 0"));
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_NO_OF_INGREDIENT ?></td>
			<td colspan="3"><?php
				if(in_array($statusID,$arrStatus)){
					$param1 = 'noIngredient'.$i;
					$param2 = isset($_POST[$param1])?$_POST[$param1]:$noIngredient;
					$param3 = array('type'=>'text','maxlength'=>'2','class'=>'numbersonly',
									//'style'=>'width:30px','title'=>'Maxlength 2 characters<br />Number Only','onBlur'=>'document.myForm.submit();');
									'style'=>'width:30px','title'=>_LBL_MAXLENGTH_2 .'<br />'. _LBL_NUMBER_ONLY);
					echo form_input($param1,$param2,$param3);
					echo '&nbsp;';
					echo form_button('go1',_LBL_GO,array('type'=>'button'));
					echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
				}else{
					$param1 = 'noIngredient'.$i;
					$param2 = isset($_POST[$param1])?$_POST[$param1]:$noIngredient;
					$param3 = array('type'=>'text','readonly'=>'readonly','style'=>'width:30px');
					echo form_input($param1,$param2,$param3);
				}
				$rowIng = ($param2!='')?$param2:0;
			?></td>
		</tr>
		<?php 
				if($rowIng>0){
		?>
		<tr class="contents">
			<!--<td class="label">&nbsp;</td>-->
			<td colspan="4"><table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
				<tr class="contents">
					<td class="label" width="3%"><?= _LBL_NUMBER ?></td>
					<td class="label" width="37%"><?= _LBL_CHEMICAL_NAME ?></td>
					<td class="label" width="10%"><?= _LBL_CAS_NO ?></td>
					<td class="label" width="10%"><?= _LBL_COMPOSITION ?></td>
					<td class="label" width="20%"><?= _LBL_CHEMICAL_SOURCE ?></td>
					<?php
					/*
						if($statusID==31||$statusID==32||$statusID==41||$statusID==42){
					?>
					<td class="label" width="15%"><?= _LBL_DATE_NOLONGER ?></td>
					<?php
						}
					*/
					?>
					<td class="label" width="5%"><?= _LBL_DELETE ?></td>
				</tr>
				<?php
					$j=1;
					//======================
					$dataColumns = '*';
					$dataTable = 'tbl_Submission_Chemical';
					$dataJoin = '';
					$dataWhere = array("AND Detail_ID" => "= ".quote_smart($detailID),"AND isDeleted"=>"= 0");
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
					foreach($arrayData as $detail){
						if($j>$rowIng) break;
						$subChemID	 	= $detail['Sub_Chemical_ID'];
						$chemicalID 	= $detail['Chemical_ID'];
						$chemicalName 	= !empty($detail['Sub_Chemical_Name'])?$detail['Sub_Chemical_Name']
																			:_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$chemicalID);
						$composition 	= $detail['Sub_Chemical_Composition'];
						$source		 	= $detail['Sub_Chemical_Source'];
						$casNo			= !empty($detail['Sub_Chemical_CAS'])?$detail['Sub_Chemical_CAS']
																			:_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$chemicalID);
						$nolonger		= func_ymd2dmy($detail['StopDate']);
						//======================
						$param1 = 'subChemID_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:$subChemID;
						$param3 = array('type'=>'hidden');
						echo form_input($param1,$param2,$param3);
						//======================
						$param1 = 'chemicalID_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemicalID;
						$param3 = array('type'=>'hidden');
						echo form_input($param1,$param2,$param3);
						//======================
						if(in_array($statusID,$arrStatus)){ $str_onClick = "getChemID('".$i."','".$j."',true);"; $str_title="title='". _LBL_JUST_CLICK_ADDEDIT ."'"; }
						else{ $str_onClick = ""; $str_title=""; }
						//======================
				?>
				<tr class="contents" style="vertical-align:middle">
					<td align="center"><?= $j ?></td>
					<td align="center"><?php
						$param1 = 'chemicalName_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemicalName;
						$param3 = array('type'=>'text','readonly'=>'readonly',
										'style'=>'width:98%','onClick'=>$str_onClick);
						echo form_input($param1,$param2,$param3); 
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
					?></td>
					<td align="center"><?php
						$param1 = 'casNo_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:$casNo;
						$param3 = array('type'=>'text', 'readonly'=>'readonly',
										'style'=>'width:97%','onClick'=>$str_onClick);
						echo '<span class="ignoreValidate">'.form_input($param1,$param2,$param3).'</span>'; 
					?></td>
					<td align="center"><?php
						// echo '<span class="ignoreValidate">';
						
						// $param1 = 'composition_'.$i.'_'.$j;
						// $param2 = isset($_POST[$param1])?$_POST[$param1]:$composition;
						// $param3 = array('type'=>'text','style'=>'width:98%');
						// echo form_input($param1,$param2,$param3); 
						// echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
						$param1 = 'composition_'.$i.'_'.$j;
						$param2 = '';
						$param3 = $arrComposition;
						$param4 = isset($_POST[$param1])?$_POST[$param1]:$composition;
						$param5 = array('class'=>'eitherOne');
						echo form_select($param1,$param2,$param3,$param4,$param5);
						
						echo '<br />'. _LBL_OR .'<br />';
						
						$composition = preg_replace("/[^0-9\.]/", '', $composition);
						
						$param1 = 'composition2_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:$composition;
						$param3 = array('type'=>'text',//'readonly'=>'readonly',
										'style'=>'width:60%;text-align:center;','class'=>'numbers3decimals eitherOne');
						echo form_input($param1,$param2,$param3) . '%'; 
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
						// echo '</span>';
					?></td>
					<td><?php
						$param1 = 'source_'.$i.'_'.$j;
						$param2 = $arrSource;
						$param3 = isset($_POST[$param1])?$_POST[$param1]:$source;
						$param4 = '<br />';
						echo form_radio($param1,$param2,$param3,$param4,'');
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
					?></td>
					<?php
					/*
						if($statusID==31||$statusID==32||$statusID==41||$statusID==42){
					?>
					<td><?php
						$param1 = 'nolonger_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:$nolonger;
						$param3 = array('type'=>'text','style'=>'width:80px');
						echo '<span class="dateNo">'.form_input($param1,$param2,$param3).'</span>';
					?></td>
					<?php
						}
					*/
					?>
					<td><?php
						if(in_array($statusID,$arrStatus)){ $func_js = "emptyRow('".$i."','".$j."',true)"; $imgName = "delete.gif"; }
						else{ $func_js = ""; $imgName = "delete_0.gif"; }
						//========================================================
						$imgParam1 = $sys_config['images_path'].'icons/'.$imgName;
						$imgParam2 = _LBL_DELETE;
						$imgParam3 = $func_js;
						echo _get_imagebutton($imgParam1,$imgParam2,$imgParam3); 
					?></td>
				</tr>
				<?php
						$j++;
					}
					for(;$j<=$rowIng;$j++){
						//======================
						$param1 = 'subChemID_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'hidden');
						echo form_input($param1,$param2,$param3); 
						//======================
						$param1 = 'chemicalID_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'hidden');
						echo form_input($param1,$param2,$param3);
						//======================
						if(in_array($statusID,$arrStatus)){ $str_onClick = "getChemID('".$i."','".$j."',true);"; $str_title="title='". _LBL_JUST_CLICK_ADDEDIT ."'"; }
						else{ $str_onClick = ""; $str_title=""; }
						//======================
				?>
				<tr class="contents" style="vertical-align:middle">
					<td align="center"><?= $j ?></td>
					<td align="center"><?php
						$param1 = 'chemicalName_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'text', 'readonly'=>'readonly',
										'style'=>'width:98%','onClick'=>$str_onClick);
						echo form_input($param1,$param2,$param3); 
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
					?></td>
					<td align="center"><?php
						$param1 = 'casNo_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'text', 'readonly'=>'readonly',
										'style'=>'width:97%','onClick'=>$str_onClick);
						echo '<span class="ignoreValidate">'.form_input($param1,$param2,$param3).'</span>'; 
					?></td>
					<td align="center"><?php
						// echo '<span class="ignoreValidate">';
						
						// $param1 = 'composition_'.$i.'_'.$j;
						// $param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						// $param3 = array('type'=>'text',//'readonly'=>'readonly',
										// 'style'=>'width:98%');
						// echo form_input($param1,$param2,$param3); 
						// echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
						$param1 = 'composition_'.$i.'_'.$j;
						$param2 = '';
						$param3 = $arrComposition;
						$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param5 = array('class'=>'eitherOne');
						echo form_select($param1,$param2,$param3,$param4,$param5);
						echo '<br />'. _LBL_OR .'<br />';
						
						$param1 = 'composition2_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'text',//'readonly'=>'readonly',
										'style'=>'width:60%;text-align:center;','class'=>'numbers3decimals eitherOne');
						echo form_input($param1,$param2,$param3) . '%'; 
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
						// echo '</span>';
					?></td>
					<td><?php
						$param1 = 'source_'.$i.'_'.$j;
						$param2 = $arrSource;
						$param3 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param4 = '<br />';
						echo form_radio($param1,$param2,$param3,$param4,'');
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
					?></td>
					<?php
					/*
						if($statusID==31||$statusID==32||$statusID==41||$statusID==42){
					?>
					<td><?php
						$param1 = 'nolonger_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'text','style'=>'width:80px');
						echo '<span class="dateNo">'.form_input($param1,$param2,$param3).'</span>';
					?></td>
					<?php
						}
					*/
					?>
					<td><?php
						if(in_array($statusID,$arrStatus)){ $func_js = "emptyRow('".$i."','".$j."',true)"; $imgName = "delete.gif"; }
						else{ $func_js = ""; $imgName = "delete_0.gif"; }
						//========================================================
						$imgParam1 = $sys_config['images_path'].'icons/'.$imgName;
						$imgParam2 = _LBL_DELETE;
						$imgParam3 = $func_js;
						echo _get_imagebutton($imgParam1,$imgParam2,$imgParam3); 
					?></td>
				</tr>
				<?php } ?>
			</table></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PHY_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_PH_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:$selectTo_PH;
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 1','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_PH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 1','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_PH-To_PH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_PH-From_PH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('PH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HEALTH_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_HH_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:$selectTo_HH;
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 2','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_HH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 2','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_HH-To_HH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_HH-From_HH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('HH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_ENV_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_EH_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:$selectTo_EH;
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 3','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_EH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 3','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_EH-To_EH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_EH-From_EH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('EH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<?php 
					if($levelID!=7){ // == $levelID = 6/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_SUPPLY . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'totalSupply'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$totalSupply;
				$param3 = array('type'=>'text','class'=>'numberspointer','title'=>_LBL_NUMBER_ONLY);
				echo form_input($param1,$param2,$param3) .' '. _LBL_TONNE_YEAR; 
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_LIST_SUPPLIED_ENTITY . $requiredField ?></td>
			<td colspan="3"><?php
				//======================
				$param6 = 'entityTo_To_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:$entityTo;
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'IN ('.$ids.')'),'Company_Name'
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'entityTo_From_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'NOT IN ('.$ids.')'),'Company_Name'
						);
				$param4 = array();
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo form_multiselect($param1,$param2,$param3,$param4,$param5);
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose2" rel="To_From-To_To" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove2"  rel="To_To-To_From" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="selectAll">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
			?></td>
		</tr>
		-->
		<?php 
					}
					if($levelID!=6){ // == $levelID = 7/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_USE . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'totalUse'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$totalUse;
				$param3 = array('type'=>'text','class'=>'numberspointer','title'=>_LBL_NUMBER_ONLY);
				echo form_input($param1,$param2,$param3) .' '. _LBL_TONNE_YEAR; 
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_LIST_SUPPLIER_ENTITY . $requiredField ?></td>
			<td colspan="3"><?php
				//======================
				$param6 = 'entityFrom_To_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:$entityFrom;
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'IN ('.$ids.')'),'Company_Name'
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'entityFrom_From_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'NOT IN ('.$ids.')'),'Company_Name'
						);
				$param4 = array();
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo form_multiselect($param1,$param2,$param3,$param4,$param5);
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose2" rel="From_From-From_To" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove2"  rel="From_To-From_From" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="selectAll">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
			?></td>
		</tr>
		-->
		<?php 
					}
		?>
	</table>
	<?php
				}
				$i++;
			}
			for(;$i<=$noRow;$i++){
				//=============================
				$chk_name = 'chkSelect_'.$i;
				$chk_value = isset($_POST[$chk_name])?'1':'0';
				$chk_checked = 'checked';
				//=============================
				echo form_input('classified_'.$i,'0',array('type'=>'hidden'));
				//=============================
				$tblID = 'tbl'.$i;
				//=============================
				$labelTitle = '<label><input type="checkbox" name="'.$chk_name.'" id="'.$chk_name.'" value="1" '.$chk_checked
							. ' onClick="init_validation(\''.$i.'\');" class="cl_chkbx" />'. _LBL_NO_OF_MIXTURE .' '. $i .'</label>';
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" id="<?= $tblID ?>">
		<tr class="contents">
			<td class="label title" colspan="4"><?= $labelTitle ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PRODUCT_NAME . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'productName'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','style'=>'width:250px');
				echo form_input($param1,$param2,$param3); 
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PHYSICAL_FORM . $requiredField ?></td>
			<td width="30%"><?php
				$param1 = 'physicalForm'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Chemical_Form','Form_ID','Form_Name',array('AND Active'=>'= 1'),'Form_ID');
				$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param5 = '';
				echo form_select($param1,$param2,$param3,$param4,$param5);
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
			<td width="20%" class="label"><?= _LBL_ID_NUMBER ?></td>
			<td width="30%"><?php
				$param1 = 'idNo'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text');
				echo '<span class="ignoreValidate">'.form_input($param1,$param2,$param3).'</span>'; 
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_NO_OF_INGREDIENT ?></td>
			<td colspan="3"><?php
				if(in_array($statusID,$arrStatus)){
					$param1 = 'noIngredient'.$i;
					$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param3 = array('type'=>'text','maxlength'=>'2','class'=>'numbersonly',
									'style'=>'width:30px','title'=>_LBL_MAXLENGTH_2 .'<br />'. _LBL_NUMBER_ONLY);
					echo form_input($param1,$param2,$param3);
					echo '&nbsp;';
					echo form_button('go1',_LBL_GO,array('type'=>'button'));
					echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
				}else{
					$param1 = 'noIngredient'.$i;
					$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param3 = array('type'=>'text','readonly'=>'readonly','style'=>'width:30px');
					echo form_input($param1,$param2,$param3);
				}
				$rowIng = ($param2!='')?$param2:0;
			?></td>
		</tr>
		<?php 	if($rowIng>0){ ?>
		<tr class="contents">
			<!--<td class="label">&nbsp;</td>-->
			<td colspan="4"><table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
				<tr class="contents">
					<td class="label" width="3%"><?= _LBL_NUMBER ?></td>
					<td class="label" width="37%"><?= _LBL_CHEMICAL_NAME ?></td>
					<td class="label" width="10%"><?= _LBL_CAS_NO ?></td>
					<td class="label" width="10%"><?= _LBL_COMPOSITION ?></td>
					<td class="label" width="20%"><?= _LBL_CHEMICAL_SOURCE ?></td>
					<?php
					/*
						if($statusID==31||$statusID==32||$statusID==41||$statusID==42){
					?>
					<td class="label" width="15%"><?= _LBL_DATE_NOLONGER ?></td>
					<?php
						}
					*/
					?>
					<td class="label" width="5%"><?= _LBL_DELETE ?></td>
				</tr>
				<?php 
					for($j=1;$j<=$rowIng;$j++){
						$param1 = 'chemicalID_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'hidden');
						echo form_input($param1,$param2,$param3); 
						//======================
						if(in_array($statusID,$arrStatus)){ $str_onClick = "getChemID('".$i."','".$j."',true);"; $str_title="title='". _LBL_JUST_CLICK_ADDEDIT ."'"; }
						else{ $str_onClick = ""; $str_title=""; }
						//======================
				?>
				<tr class="contents" style="vertical-align:middle">
					<td align="center"><?= $j ?></td>
					<td align="center"><?php
						$param1 = 'chemicalName_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'text', 'readonly'=>'readonly',
										'style'=>'width:98%','onClick'=>$str_onClick);
						echo form_input($param1,$param2,$param3); 
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
					?></td>
					<td align="center"><?php
						$param1 = 'casNo_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'text', 'readonly'=>'readonly',
										'style'=>'width:97%','onClick'=>$str_onClick);
						echo '<span class="ignoreValidate">'.form_input($param1,$param2,$param3).'</span>'; 
					?></td>
					<td align="center"><?php
						// echo '<span class="ignoreValidate">';
						
						/*
						$param1 = 'composition_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'text','style'=>'width:98%');
						echo form_input($param1,$param2,$param3); 
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						*/
						
						$param1 = 'composition_'.$i.'_'.$j;
						$param2 = '';
						$param3 = $arrComposition;
						$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param5 = array('style'=>'width:90px;','class'=>'eitherOne');
						echo form_select($param1,$param2,$param3,$param4,$param5);
						echo '<br />'. _LBL_OR .'<br />';
						
						$param1 = 'composition2_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'text',//'readonly'=>'readonly',
										'style'=>'width:60%;text-align:center;','class'=>'numbers3decimals eitherOne');
						echo form_input($param1,$param2,$param3) . '%';
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
						
						// echo '</span>';
					?></td>
					<td><?php
						$param1 = 'source_'.$i.'_'.$j;
						$param2 = $arrSource;
						$param3 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param4 = '<br />';
						echo form_radio($param1,$param2,$param3,$param4,'');
						echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
					?></td>
					<?php
					/*
						if($statusID==31||$statusID==32||$statusID==41||$statusID==42){
					?>
					<td><?php
						$param1 = 'nolonger_'.$i.'_'.$j;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
						$param3 = array('type'=>'text','style'=>'width:80px');
						echo '<span class="dateNo">'.form_input($param1,$param2,$param3).'</span>';
					?></td>
					<?php
						}
					*/
					?>
					<td><?php
						if(in_array($statusID,$arrStatus)){ $func_js = "emptyRow('".$i."','".$j."',true)"; $imgName = "delete.gif"; }
						else{ $func_js = ""; $imgName = "delete_0.gif"; }
						//========================================================
						$imgParam1 = $sys_config['images_path'].'icons/'.$imgName;
						$imgParam2 = _LBL_DELETE;
						$imgParam3 = $func_js;
						echo _get_imagebutton($imgParam1,$imgParam2,$imgParam3); 
					?></td>
				</tr>
				<?php } ?>
			</table></td>
		</tr>
		<?php	} ?>
		<tr class="contents">
			<td class="label"><?= _LBL_PHY_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_PH_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:array();
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 1','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_PH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 1','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_PH-To_PH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_PH-From_PH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('PH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HEALTH_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_HH_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:array();
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 2','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_HH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 2','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_HH-To_HH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_HH-From_HH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('HH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_ENV_HAZARD_CLASS . $requiredField ?></td>
			<td colspan="3" valign="middle"><?php
				//======================
				$param6 = 'selectTo_EH_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:array();
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 3','AND Active'=>'= 1','AND Category_ID'=>'IN ('.$ids.')'),'',TRUE
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'selectFrom_EH_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
							"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
							array('AND Type_ID'=>'= 3','AND Active'=>'= 1','AND Category_ID'=>'NOT IN ('.$ids.')'),'',TRUE
						);
				$param4 = '';
				$param5 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<span class="slt_right">'.form_select2($param1,$param2,$param3,$param4,$param5).'</span>';
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose" rel="From_EH-To_EH" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove"   rel="To_EH-From_EH" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="slt_left">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
				echo form_input('EH_'.$i,'',array('type'=>'hidden','class'=>'dummy'));
				//======================
			?></td>
		</tr>
		<?php 
				if($levelID!=7){ // == $levelID = 6/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_SUPPLY . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'totalSupply'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','class'=>'numberspointer','title'=>_LBL_NUMBER_ONLY);
				echo form_input($param1,$param2,$param3) .' '. _LBL_TONNE_YEAR; 
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_LIST_SUPPLIED_ENTITY . $requiredField ?></td>
			<td colspan="3"><?php
				//======================
				$param6 = 'entityTo_To_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:array();
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'IN ('.$ids.')'),'Company_Name'
						);
				$param0 = array('style'=>'width:100%','size'=>'4');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'entityTo_From_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'NOT IN ('.$ids.')'),'Company_Name'
						);
				$param4 = array();
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo form_multiselect($param1,$param2,$param3,$param4,$param5);
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose2" rel="To_From-To_To" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove2"  rel="To_To-To_From" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="selectAll">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
			?></td>
		</tr>
		-->
		<?php 
				}
				if($levelID!=6){ // == $levelID = 7/8
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_TOTAL_QUANTITY_USE . $requiredField ?></td>
			<td colspan="3"><?php
				$param1 = 'totalUse'.$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'text','class'=>'numberspointer','title'=>_LBL_NUMBER_ONLY);
				echo form_input($param1,$param2,$param3) .' '. _LBL_TONNE_YEAR; 
				echo '<div class="error"><label for="'.$param1.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>';
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_LIST_SUPPLIER_ENTITY . $requiredField ?></td>
			<td colspan="3"><?php
				//======================
				$param6 = 'entityFrom_To_'.$i;
				$param7 = '';
				$param9 = isset($_POST[$param6])?$_POST[$param6]:array();
				//======================
				$ids = '0';
				foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
				//======================
				$param8 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'IN ('.$ids.')'),'Company_Name'
						);
				$param0 = array('style'=>'width:100%','size'=>'4','class'=>'selectAll');
				//======================
				echo '<table width="100%"><tr><td width="45%">';
				//======================
				$param1 = 'entityFrom_From_'.$i;
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Client_Company','Company_ID','Company_Name',
							array('AND Usr_ID'=>'= '.quote_smart($Usr_ID),'AND Active'=>'= 1','AND isDeleted'=>'= 0','AND Company_ID'=>'NOT IN ('.$ids.')'),'Company_Name'
						);
				$param4 = array();
				$param5 = array('style'=>'width:100%','size'=>'4');
				echo form_multiselect($param1,$param2,$param3,$param4,$param5);
				//======================
				echo '</td><td width="10%" align="center"><a href="#" class="choose2" rel="From_From-From_To" id="'.$i.'">'. _LBL_ADD .'&gt;</a><br /><a href="#" class="remove2"  rel="From_To-From_From" id="'.$i.'">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
				//======================
				echo '
					<span class="selectAll">'.form_multiselect($param6,$param7,$param8,$param9,$param0).'</span>
					<div class="error"><label for="'.$param6.'" class="error">'. _LBL_REQUIRED_FIELD .'</label></div>
				';
				//======================
				echo '</td></tr></table>';
				//======================
			?></td>
		</tr>
		-->
		<?php 
				}
		?>
	</table>
	<?php 
			}
		}
		if($noRow>0){
 			// if($submissionID!=0){
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td class="label title" colspan="2"><?= _LBL_TERM_COND ?></td>
		</tr>
		<tr class="contents">
			<td width="5%" align="center" valign="middle"><?php
				$param2 = isset($_POST['disclaimer'])?1:0;//$disclaimer;
				$dischecked = $param2;
				if($dischecked==0)
					$param3 = array('type'=>'checkbox','onClick'=>'func_check(this);');
				else
					$param3 = array('type'=>'checkbox','onClick'=>'func_check(this);','checked'=>'checked');
					
				echo form_input('disclaimer',$param2,$param3); ?></td>
			<td><label for="disclaimer"><?= _LBL_DISCLAIMER_DETAIL ?></label></td>
		</tr>
	</table>
	<?php
			// }
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				if($statusID==31||$statusID==32||$statusID==51){ $formBack = 'approvedNewList.php'; }
				else{ $formBack = 'approvedRenewList.php'; }
				
				if($new==11) $formBack = 'renewList.php';
				if($userLevelID<=2 || $userLevelID>=6){
					$param1 = 'save';
					$param2 = _LBL_SAVE;
					$param3 = array('type'=>'submit','onClick'=>'return check_form();');
					echo form_button($param1,$param2,$param3);
					echo '&nbsp;';
					// if($statusID==3||$statusID==4){
						echo '<span id="spanSubmit" style="display:'. (($dischecked==1)?'':'none') .'">';
						$param1 = 'send';
						$param2 = _LBL_SAVE .' & '. _LBL_SUBMIT;
						$param3 = array('type'=>'submit','onClick'=>'return check_form();');
						echo form_button($param1,$param2,$param3);
						echo '&nbsp;';
						echo '</span>';
					// }
				}
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\''.$formBack.'\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<?php
		}
	?>
	</div>
	<br />
	</form>
	<?php func_window_close2(); ?>