<?php
	//****************************************************************/
	// filename: submissionView.php
	// description: view the submission detail
	//****************************************************************/
	include_once "../includes/sys_config.php";
	/*********/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*********/
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*********/
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_header.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."faq.php";
	/*********/
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
	$userLevelID = $_SESSION["user"]["Level_ID"];
	if($userLevelID==5) exit();
	/*********/

	func_header("",
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.css,". // css
				"", // css
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."jquery/Tabbed/jquery-ui-tabs.js,". // javascript
				// $sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery-blink/jquery-blink.js,". // javascript
				"",
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				"", // str url frame top
				"", // str url frame left
				"", // str url frame content
				false // int top for [enter]
				);
	/*********/
	if(isset($_POST["new"])) $new = $_POST["new"];
	elseif(isset($_GET["new"])) $new = $_GET["new"];
	else $new = "";
	/*********/
	$menu = 1;
	$file = "submissionList.php";
	$newStatusID = 11;
	if($new==1){
		$windowTitle = _LBL_SUBMISSION ." :: ". _LBL_LIST;
		$formBack = "submissionList.php";
	}
	elseif($new==2){
		$windowTitle = _LBL_SUBMISSION ." :: ". _LBL_CHECKING;
		$formBack = "checkingNewList.php";
	}
	elseif($new==3){
		$windowTitle = _LBL_SUBMISSION ." :: ". _LBL_ACKNOWLEGED;
		$formBack = "approvedNewList.php";
	}
	elseif($new==4){
		$windowTitle = _LBL_SUBMISSION ." :: ". _LBL_REJECTED;
		$formBack = "rejectedNewList.php";
	}
	elseif($new==6){
		$windowTitle = _LBL_SUBMISSION ." :: ". _LBL_APPROVAL;
		$formBack = "approvalNewList.php";
	}
	else{
		$windowTitle = _LBL_ALL_SUBMISSION ." :: ". _LBL_LIST;
		$formBack = "allList.php";
		$menu = 21;
		$file = "allList.php";
		$newStatusID = 0;
	}
	/*********/
	if(isset($_POST["submissionID"])) $submissionID = $_POST["submissionID"];
	elseif(isset($_GET["submissionID"])) $submissionID = $_GET["submissionID"];
	else $submissionID = 0;
	/*********/
	if(isset($_POST["send"])){
		$statusID = isset($_POST["statusID"])?$_POST["statusID"]:"";
		include_once "sSubmit.php";
	}
	/*********/
	$chemType 		= "";
	$noSubMix		= "";
	$remark			= "";
	$statusID		= "";
	$levelID		= "";
	$clientID		= "";
	$subSubmitID	= "";
	$approvedDate	= "";
	/*********/
	if($submissionID!=0){
		$dataColumns = "Type_ID,Submission_Remark,Status_ID,Usr_ID,Submission_Submit_ID,ApprovedDate";
		$dataTable = "tbl_Submission";
		$dataJoin = "";
		$dataWhere = array("AND Submission_ID" => "= ".quote_smart($submissionID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		foreach($arrayData as $detail){
			$chemType 	= $detail["Type_ID"];
			$noSubMix 	= _get_RowExist("tbl_Submission_Detail",array("AND Submission_ID"=>"= ".quote_smart($submissionID), "AND isDeleted"=>"= 0"));
			$remark		= ($detail["Submission_Remark"]!="")?nl2br($detail["Submission_Remark"]):"-";
			$statusID 	= $detail["Status_ID"];
			$levelID 	= _get_StrFromCondition("sys_User","Level_ID","Usr_ID",$detail["Usr_ID"]);
			$clientID 	= $detail["Usr_ID"];
			//=============================================
			$subSubmitID = $detail["Submission_Submit_ID"];
			$approvedDate = $detail["ApprovedDate"];
			//=============================================
		}
	}
?>
	<style type="text/css" title="currentStyle">
	.ui-tabs .ui-tabs-panel { padding: 10px }
	</style>
	<script language="Javascript">
	$(function() {
		
		$().UItoTop({ easingType: "easeOutQuart" });
		
		var $tabs = $("#tabs").tabs({ selected: 0 });
		var tab_selected = $tabs.tabs("option", "selected"); // => 0 ...retrieve the index of the currently selected tab
		
		/* $("button").removeClass("btn-new-style").button(); */
		
		/* 
		$("a[rel]").each(function() {
			$(this).qtip({
				content: {
					url: $(this).attr("rel"), // Use the rel attribute of each element for the url to load
					title: {
						text: "<?= _LBL_DESCRIPTION_FOR ?>:<br />[" + $(this).text() + "]", // Give the tooltip a title using each elements text
						button: "<?= _LBL_CLOSE ?>" // Show a close link in the title
					}
				},
				position: { 
					corner: { 
						tooltip: "topMiddle", target: "bottomMiddle" 
					}, 
					adjust: { 
						screen: true // Keep the tooltip on-screen at all times
					}
				},
				style: {
					name: "blue", // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: { max: 650, min: 0 },
					// width: 650
				},
				show: {
					when: "click", // Show it on click...
					solo: true // ...but hide all others when its shown
				},
				hide: "unfocus"
			}).click(function(){ return false; });
		});
		 */
		 
		$(document).on('click', 'a[rel]', function(event){	
			var categoryID = $(this).attr('rel').replace ( /[^\d.]/g, '' );
			$('#myModal').find('.modal-title').empty().append( $(this).text() );
			$.ajax({
				url: 'getCategoryDetails.php',
				type: 'POST',
				dataType: 'html',
				data: {
					categoryID: categoryID,
				},
				success: function(data){						
					$('#myModal').find('.modal-body').empty().append(data);
					$('#myModal').modal('show');
				}
			});
			return false;
		});
		
		var icons = {
			header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		};

		$( ".cl_accordion" ).each(function(){
			var id = $(this).attr("id");
			$( "#"+id  ).accordion({
				icons: icons,
				autoHeight: false,
				collapsible: true
			});
		});
	});
	</script>
	<?php func_window_open2( $windowTitle ." :: ". _LBL_VIEW); ?>
	<form name="myForm" action="" method="post">
	<?= form_input("submissionID",$submissionID,array("type"=>"hidden")); ?>
	<?= form_input("statusID",$statusID,array("type"=>"hidden")); ?>
	<?= form_input("new",$new,array("type"=>"hidden")); ?>
	<?php 
		if($userLevelID<6){
	?>	
	<div id="tabs">
		<?php /*********/ ?>
		<ul>
			<li><a href="#tabs-1" onClick=""><?= _LBL_DETAIL ?></a></li>
			<li><a href="#tabs-2" onClick=""><?= _LBL_HISTORY ?></a></li>
			<li><a href="#tabs-3" onClick=""><?= _LBL_COMPANY_INFO ?></a></li>
		</ul>
		<?php /*********/ ?>
		<div id="tabs-1">
	<?php
		}
	?>
		<?php include "sView.php"; ?>
	<?php 
		if($userLevelID<6){
	?>
		</div>
		<div id="tabs-2">
		<?php include "sHistory.php"; ?>
		</div>
		<div id="tabs-3">
		<?php include "companyView.php"; ?>
		</div>
		
	</div>
	<?php
		}
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				if(($userLevelID<=2 || $userLevelID>=6) && (in_array($statusID,array(1,2))) && $newStatusID!=0){
				
					if(in_array($statusID,array(1,2))) $formEdit = "submissionAddEdit.php?submissionID=".$submissionID."&new=".$new;
					
					$dOther = array("type"=>"button","onClick"=>"redirectForm('$formEdit')");
					echo form_button("edit",_LBL_EDIT,$dOther) ."&nbsp;";
				
				}
				$dOther = array("type"=>"button","onClick"=>"redirectForm('$formBack');");
				echo form_button("back",_LBL_BACK,$dOther);
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>