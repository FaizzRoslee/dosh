<?php
	//****************************************************************/
	// filename: submissionView_state.php
	// description: view the submission detail for state
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
	/*********/
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
	$userLevelID = $_SESSION["user"]["Level_ID"];
	/*********/
	func_header("",
				"", // css		
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."jquery/Tabbed/jquery-ui-tabs.js,". // javascript
				$sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
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
	if($new==15){
		$windowTitle = _LBL_RENEW_SUBMISSION ." :: ". _LBL_ACKNOWLEGED;
		$formBack = "approvedRenewList_state.php";
		$menu = 15;
	}
	else{
		$windowTitle = _LBL_SUBMISSION ." :: ". _LBL_ACKNOWLEGED;
		$formBack = "approvedNewList_state.php";
		$menu = 5;
	}
	/*********/
	if(isset($_POST["submissionID"])) $submissionID = $_POST["submissionID"];
	elseif(isset($_GET["submissionID"])) $submissionID = $_GET["submissionID"];
	else $submissionID = 0;
	/*********/
?>
	<style type="text/css" title="currentStyle">
		.ui-tabs .ui-tabs-panel { padding: 10px }
	</style>
	<script language="Javascript">
	$(document).ready(function() {
		var $tabs = $("#tabs").tabs({ selected: 3 });
		var tab_selected = $tabs.tabs("option", "selected"); // => 0 ...retrieve the index of the currently selected tab
		
		$("label[title]").each(function() {
			$(this).qtip({
				position: { corner: { tooltip: "topLeft", target: "bottomLeft" } },
				style: {
					name: "blue", // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					//width: { max: 350, min: 0 },
					width: 350
				}
			});
		});
		
		var icons = {
			header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		};

		$( ".accordion" ).accordion({
			active: "-1",
			icons: icons,
			autoHeight: false,
			collapsible: true
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2( $windowTitle ." :: ". _LBL_VIEW); ?>
	<form name="myForm" action="" method="post">
	<?= form_input("submissionID",$submissionID,array("type"=>"hidden")); ?>
	<?= form_input("new",$new,array("type"=>"hidden")); ?>
	<?php 
		if($userLevelID<6){
	?>	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1" onClick=""><?= _LBL_COMPANY_INFO ?></a></li>
			<li><a href="#tabs-2" onClick=""><?= _LBL_DETAIL ?></a></li>
			<li><a href="#tabs-3" onClick=""><?= _LBL_HISTORY ?></a></li>
		</ul>
		<div id="tabs-1">
		<?php include "compProfile.php"; ?>
		</div>
		<div id="tabs-2">
		<?php include "sDetail.php"; ?>
		</div>
		<div id="tabs-3">
		<?php include "sHistory.php"; ?>
		</div>
	</div>
	<?php
		}
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				$param1 = "back";
				$param2 = _LBL_BACK;
				$param3 = array("type"=>"button","onClick"=>"redirectForm('$formBack');");
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>