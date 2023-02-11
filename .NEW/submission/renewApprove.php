<?php
	//****************************************************************/
	// filename: renewApprove.php
	// description: approval for renew submission
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
	$userLevelID = $_SESSION['user']['Level_ID'];
	//==========================================================
	if($userLevelID>2 && $userLevelID!=4)
		exit();
	//==========================================================
	func_header("",
				"", // css		
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."jquery/Tabbed/jquery-ui-tabs.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
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
	else $new = 11;
	//===================
	if($new==16) $formBack = 'approvalRenewList.php';
	else $formBack = 'renewList.php';
	//============================================================		
	if(isset($_POST['submissionID'])) $submissionID = $_POST['submissionID'];
	elseif(isset($_GET['submissionID'])) $submissionID = $_GET['submissionID'];
	else $submissionID = 0;
	//============================================================		
	if(isset($_POST['save'])){
		$subSubmitID = isset($_POST['subSubmitID'])?$_POST['subSubmitID']:'';
		$statusID 	= isset($_POST['approved'])?$_POST['approved']:0;
		$appRemark	= isset($_POST['appRemark'])?$_POST['appRemark']:'';
		$appReason	= isset($_POST['appReason'])?$_POST['appReason']:'';
		$appContact	= isset($_POST['appContact'])?$_POST['appContact']:'';
		//==========================================================
		if($statusID=='41'){
			$ex_subSubmitID = explode('/',$subSubmitID); // ex: DOSH/2010/000001, if approved: DOSH/2010/000001/NS, ir renew: DOSH/2010/000001/RS/1
			$newsubSubmitID = '';
			if(is_array($ex_subSubmitID)){
				$arrCount = count($ex_subSubmitID);
				//echo $arrCount.'<br />';
				if($ex_subSubmitID[$arrCount-2]=='RS'){ //if have been renewed before
					$renewCount = $ex_subSubmitID[$arrCount-1];
					$renewTime = (int)$renewCount + 1;
					foreach($ex_subSubmitID as $index => $value){
						if($index < ($arrCount-2)){
							$newsubSubmitID .= ($newsubSubmitID!='')?'/':'';
							$newsubSubmitID .= $value;
						}else break;
					}
					$newsubSubmitID .= '/RS/'.$renewTime;
				}else{
					if($ex_subSubmitID[$arrCount-1]=='NS'){ //if submission already approved, change NS to RS
						foreach($ex_subSubmitID as $index => $value){
							if($index < ($arrCount-1)){
								$newsubSubmitID .= ($newsubSubmitID!='')?'/':'';
								$newsubSubmitID .= $value;
								//echo $newsubSubmitID.'<br />';
							}else break;
						}
						$newsubSubmitID .= '/RS/1';
					}else{ //if new submission have been approved, add NS add the end of submission id
						$newsubSubmitID .= $subSubmitID.'/NS';
					}
				}
				//==========================================================
			}
		}
		//==========================================================
		$sqlUpd = "UPDATE tbl_Submission SET";
		if($statusID=='41')	$sqlUpd .= " Submission_Submit_ID = ". quote_smart($newsubSubmitID) .",";
		$sqlUpd .= " Status_ID = ". quote_smart($statusID) .","
				. " ApprovedBy = ". quote_smart($Usr_ID) .","
				. " ApprovedDate = NOW(),"
				. " ApprovedRemark = ". quote_smart($appRemark) .","
				. " ApprovedReason = ". quote_smart($appReason) .","
				. " ApprovedContact = ". quote_smart($appContact) .""
				. " WHERE Submission_ID = ".quote_smart($submissionID)." LIMIT 1";
				//echo $sqlUpd;die;
		$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
		//==========================================================
		if($resUpd){
			if($statusID=='41'){
				$sqlUpd	= "UPDATE tbl_submission s,tbl_submission_detail sd,tbl_submission_chemical sc SET"
						. " sc.ApprovedDate = NOW(),sc.UpdateDate = NOW(),"
						. " sd.ApprovedDate = NOW(),sd.UpdateDate = NOW(),"
						. " s.UpdateDate = NOW(),s.UpdateBy = ". quote_smart($Usr_ID) .""
						. " WHERE s.isDeleted = 0 AND sd.isDeleted = 0 AND sc.isDeleted = 0"
						. " AND sc.Detail_ID = sd.Detail_ID AND sd.Submission_ID = s.Submission_ID"
						. " AND sc.ApprovedDate IS NULL AND s.Submission_ID = ".quote_smart($submissionID)."";
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				$msg = 'Submission was acknowledged.';
				//==========================================================
				//notification==============================================
				$usrid = _get_StrFromCondition('tbl_Submission','Usr_ID','Submission_ID',$submissionID);
				$content = _get_Notification(3,$usrid,$submissionID); //3 is for renew submission
				//print_r($content);die();
				if(is_array($content)){
					if($content['email']!=''){
						func_email2($sys_config['email_admin'],$content['email'],$content['title'],$content['body']);
					}
				}
				//notification==============================================
			}elseif($statusID=='4'){
				$msg = 'Submission was sent back to the supplier.';
				//==========================================================
				$usrid = _get_StrFromCondition('tbl_Submission','Usr_ID','Submission_ID',$submissionID);
				$content = _get_Notification(8,$usrid,$submissionID); //
				if(is_array($content)){
					if($content['email']!=''){
						func_email2($sys_config['email_admin'],$content['email'],$content['title'],$content['body']);
					}
				}
				//==========================================================
			}
			else $msg = 'NA';
			//==========================================================
			$sqlIns = "INSERT INTO tbl_Submission_Record ("
					. " Sub_Record_Desc,Sub_Record_Remark,Sub_Record_Reason,Sub_Record_Contact,"
					. " Sub_Record_By,Sub_Record_Date,Sub_Record_Status,Submission_ID"
					. " ) VALUE ("
					. " ".quote_smart($msg).",".quote_smart($appRemark).",".quote_smart($appReason).",".quote_smart($appContact).","
					. " ".quote_smart($Usr_ID).",NOW(),".quote_smart($statusID).",".quote_smart($submissionID)
					. " )";
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			//==========================================================
		}else{ $msg = 'Submission cannot be acknowledged'; }
		func_add_audittrail($Usr_ID,$msg.' ['.$submissionID.']','Submission-New','tbl_Submission,'.$submissionID);
		//==========================================================
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href = "'.$formBack.'";
			</script>
			';
		exit();
	}
	//=====================================================================
	$chemType 		= '';
	$noSubMix		= '';
	$remark			= '';
	$statusID		= '';
	$levelID		= '';
	$subSubmitID	= '';
	$approvedDate	= '';
	//=====================================================================
	if($submissionID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Submission';
		$dataJoin = '';
		$dataWhere = array("AND Submission_ID" => "= ".quote_smart($submissionID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		foreach($arrayData as $detail){
			$chemType = $detail['Type_ID'];
			$noSubMix = _get_RowExist('tbl_Submission_Detail',array("AND Submission_ID"=>"= ".quote_smart($submissionID), "AND isDeleted"=>"= 0"));
			$remark		= ($detail['Submission_Remark']!='')?nl2br($detail['Submission_Remark']):'-';
			$statusID = $detail['Status_ID'];
			$levelID 	= _get_StrFromCondition('sys_User','Level_ID','Usr_ID',$detail['Usr_ID']);
			//=============================================
			$subSubmitID = $detail['Submission_Submit_ID'];
			$approvedDate = $detail['ApprovedDate'];
			//=============================================
		}
	}
?>
	<style type="text/css" title="currentStyle">
		.ui-tabs .ui-tabs-panel { padding: 10px }
	</style>
	<script language="Javascript">
	$(function() {
		var $tabs = $("#tabs").tabs({ selected: 2 });
		var tab_selected = $tabs.tabs('option', 'selected'); // => 0 ...retrieve the index of the currently selected tab
		
		$('a[rel]').each(function() {
			$(this).qtip({
				content: {
					url: $(this).attr('rel'), // Use the rel attribute of each element for the url to load
					title: {
						text: '<?= _LBL_DESCRIPTION_FOR ?>:<br />[' + $(this).text() + ']', // Give the tooltip a title using each elements text
						button: '<?= _LBL_CLOSE ?>' // Show a close link in the title
					}
				},
				position: { 
					corner: { 
						tooltip: 'topMiddle', target: 'bottomMiddle' 
					}, 
					adjust: { 
						screen: true // Keep the tooltip on-screen at all times
					}
				},
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					//width: { max: 450, min: 0 },
					width: 650,
				},
				show: {
					when: 'click', // Show it on click...
					solo: true // ...but hide all others when its shown
				},
				hide: 'unfocus',
			}).click(function(){ return false; });
		});	
		
		var icons = {
			header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		};

		$( '.cl_accordion' ).each(function(){
			var id = $(this).attr('id');
			$( '#'+id  ).accordion({
				icons: icons,
				autoHeight: false,
				collapsible: true
			});
		});
	});
	
	function show_save(tab_selected){
		if(tab_selected==0){
			document.getElementById('span_save').style.display = 'none';
			document.getElementById('back').value = '<?= _LBL_BACK ?>';
		}else{
			document.getElementById('span_save').style.display = '';
			document.getElementById('back').value = '<?= _LBL_CANCEL ?>';
		}
	}
	
	function show_reason(val){
		if(val==2 || val==4){
			document.getElementById('tr_reason').style.display = '';
			document.getElementById('tr_contact').style.display = '';
		}else{
			document.getElementById('tr_reason').style.display = 'none';
			document.getElementById('tr_contact').style.display = 'none';
		}
	}
	</script>
	<?php func_window_open2( _LBL_RENEW_SUBMISSION .' :: '. _LBL_APPROVAL); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('submissionID',$submissionID,array('type'=>'hidden')); ?>
	<?= form_input('new',$new,array('type'=>'hidden')); ?>
	<div id="tabs">
		<?php //=========================================================================== ?>
		<ul>
			<li><a href="#tabs-1" onClick="show_save(0);"><?= _LBL_DETAIL ?></a></li>
			<li><a href="#tabs-2" onClick="show_save(0);"><?= _LBL_HISTORY ?></a></li>
			<li><a href="#tabs-3" onClick="show_save(1);"><?= _LBL_APPROVAL ?></a></li>
		</ul>
		<?php //=========================================================================== ?>
		<div id="tabs-1">
		<?php include 'sView.php'; ?>
		</div>
		<?php //=========================================================================== ?>
		<div id="tabs-2">
		<?php include 'sHistory.php'; ?>
		</div>
		<?php //=========================================================================== ?>
		<div id="tabs-3">
		<?= form_input('subSubmitID',_get_StrFromCondition('tbl_Submission','Submission_Submit_ID','Submission_ID',$submissionID),array('type'=>'hidden')); ?>
		<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
			<tr class="contents">
				<td width="25%" class="label"><?= _LBL_ACTION ?></td>
				<td width="75%"><?php
					$param1 = 'approved';
					$param2 = array(
									array('value'=>'41','label'=>_LBL_ACKNOWLEGE),
									array('value'=>'4','label'=>_LBL_REJECTED_SEND_BACK)
							);
					$param3 = isset($_POST[$param1])?$_POST[$param1]:'41';
					$param4 = '&nbsp;&nbsp;';
					$param5 = array('onClick'=>'show_reason(this.value);');
					echo form_radio($param1,$param2,$param3,$param4,$param5);//$formelement,$option_array=array(),$current='',$labelSeparator='',$others=array())
				?></td>
			</tr>
			<tr class="contents">
				<td class="label"><?= _LBL_CHECKED_BY ?></td>
				<td><label><?= $_SESSION['user']['name']; ?></label></td>
			</tr>
			<tr class="contents">
				<td class="label"><?= _LBL_DATE ?></td>
				<td><label><?= date('d-m-Y'); ?></label></td>
			</tr>
			<tr class="contents">
				<td class="label"><?= _LBL_REMARK ?></td>
				<td><?php 
					$param1 = 'appRemark';
					$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param3 = array('cols'=>'70','rows'=>'4');
					echo form_textarea($param1,$param2,$param3);
				?></td>
			</tr>
			<tr id="tr_reason" class="contents" style="display:none">
				<td class="label"><?= _LBL_REASON ?></td>
				<td><?php 
					$param1 = 'appReason';
					$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param3 = array('cols'=>'70','rows'=>'4');
					echo form_textarea($param1,$param2,$param3);
				?></td>
			</tr>
			<tr id="tr_contact" class="contents" style="display:none">
				<td class="label"><?= _LBL_CONTACT_NO ?></td>
				<td><?php 
					$param1 = 'appContact';
					$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param3 = array('type'=>'text');
					echo form_input($param1,$param2,$param3);
				?></td>
			</tr>
		</table>
		</div>
		<?php //=========================================================================== ?>
	</div>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				echo '<span id="span_save" name="span_save" style="display:">';
				$param1 = 'save';
				$param2 = _LBL_SAVE;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
				echo '&nbsp;';
				echo '</span>';
				$param1 = 'back';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\''.$formBack.'\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>