<?php
	/*************************************************************************************************
	// filename: empList.php
	// description: list of the employee


    Update 20211101 : LinuxHouse is currently reponsible for the maintenance of this CIMS
                      system ( 11 years old ). 

                      Our contract ends approx 202410.

                      With regards to this  file, we just did minor refactoring
                      as most spacing / tabbing / parentheses appears to have been deliberately
                      removed to make code maintenance difficult 

***************************************************************************************************/
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
	include_once $sys_config["includes_path"]."func_email.php";
	include_once $sys_config["includes_path"]."func_notification.php";
	include_once $sys_config["includes_path"]."func_date.php";
	/*********/
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
	$userLevelID = $_SESSION["user"]["Level_ID"];
	/*********/
	if($userLevelID>3) exit();
	/*********/
	func_header("",
				"", // css		
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."jquery/Tabbed/jquery-ui-tabs.js,". // javascript
				$sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
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
	else $new = 1;
	/*********/
	if($new==2) $formBack = "checkingNewList.php";
	else $formBack = "submissionList.php";
	/*********/
	if(isset($_POST["submissionID"])) $submissionID = $_POST["submissionID"];
	elseif(isset($_GET["submissionID"])) $submissionID = $_GET["submissionID"];
	else $submissionID = 0;
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
		$dataColumns = "*";
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
			/*********/
			$subSubmitID = $detail["Submission_Submit_ID"];
			$approvedDate = $detail["ApprovedDate"];
			/*********/
		}
	}
	
	$spLoad = '<span class="sp-load"> <img src="'. $sys_config['images_path'] .'ui-anim_basic_16x16.gif"> </span>';
?>
	<style type="text/css" title="currentStyle">
		.ui-tabs .ui-tabs-panel { padding: 10px }
	</style>
	<script language="Javascript">
	$(function() {
		var $tabs = $("#tabs").tabs({ selected: 2 });
		var tab_selected = $tabs.tabs("option", "selected"); // => 0 ...retrieve the index of the currently selected tab	
		
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
					//width: { max: 450, min: 0 },
					width: 650
				},
				show: {
					when: "click", // Show it on click...
					solo: true // ...but hide all others when its shown
				},
				hide: "unfocus"
			}).click(function(){ return false; });
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
		
		/* $("button").removeClass("btn-new-style").button(); */
		
		$(".sp-load").hide();
		$("#save").on("click",function(){
                        
                        if(confirm('<?=_ADD_SUBMISSION_CHECKING?>')){
                            
			$(this).button("disable").find(".sp-load").show();
			$.ajax({
				type: "POST",  
				dataType: "json",
				url: "<?= $sys_config["submission_path"] ."submission-ajax.php"; ?>",
				data: $("#myForm").serializeArray(),
				success: function(msg){
					$(this).button("enable").find(".sp-load").hide();
					alert( msg.result );
					location.href = '<?= $formBack ?>';
					//$('#papar').load('<?= $formBack ?>').show();

				},
				error: function(xhr, ajaxOptions, thrownError){
					alert(xhr.statusText);
					alert(thrownError);
				}
				
			});
                       
                        }
                        else {
                            //self.close();
                            return false;
                        }
		});
	});
	
	function show_save(tab_selected){
		if(tab_selected==0){
			document.getElementById("span_save").style.display = "none";
			document.getElementById("back").value = "<?= _LBL_BACK ?>";
		}else{
			document.getElementById("span_save").style.display = "";
			document.getElementById("back").value = "<?= _LBL_CANCEL ?>";
		}
	}
	
	function show_reason(val){
		if(val==2 || val==4){
			document.getElementById("tr_reason").style.display = "";
			document.getElementById("tr_contact").style.display = "";
		}else{
			document.getElementById("tr_reason").style.display = "none";
			document.getElementById("tr_contact").style.display = "none";
		}
	}

	</script>
	<?php func_window_open2( _LBL_SUBMISSION ." :: ". _LBL_CHECKING); ?>
	<form id="myForm" name="myForm" action="" method="post">
	<?= form_input("submissionID",$submissionID,array("type"=>"hidden")); ?>
	<?= form_input("new",$new,array("type"=>"hidden")); ?>
	<div id="tabs">

		<ul>
			<li><a href="#tabs-1" onClick="show_save(0);"><?= _LBL_DETAIL ?></a></li>
			<li><a href="#tabs-2" onClick="show_save(0);"><?= _LBL_HISTORY ?></a></li>
			<li><a href="#tabs-3" onClick="show_save(1);"><?= _LBL_CHECKING ?></a></li>
			<li><a href="#tabs-4" onClick="show_save(0);"><?= _LBL_COMPANY_INFO ?></a></li>
		</ul>

		<div id="tabs-1"  style="overflow-y: scroll; height:600px;">
		<?php include "sView.php"; ?>
		</div>

		<div id="tabs-2">
		<?php include "sHistory.php"; ?>
		</div>

		<div id="tabs-3">
		<?= form_input("subSubmitID",$subSubmitID,array("type"=>"hidden")); ?>
		<?= form_input("fn","checking",array("type"=>"hidden")); ?>
		<?= form_input("Usr_ID",$Usr_ID,array("type"=>"hidden")); ?>
		<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
			<tr class="contents">
				<td width="25%" class="label"><?= _LBL_ACTION ?></td>
				<td width="75%"><?php
					$param1 = "approved";
					$param2 = array(
									array("value"=>"21","label"=>_LBL_CHECKED),
									array("value"=>"2","label"=>_LBL_REJECTED_SEND_BACK)
							);
					$param3 = isset($_POST[$param1])?$_POST[$param1]:"21";
					$param4 = "&nbsp;&nbsp;";
					$param5 = array("onClick"=>"show_reason(this.value);");
					echo form_radio($param1,$param2,$param3,$param4,$param5);//$formelement,$option_array=array(),$current="",$labelSeparator="",$others=array())
				?></td>
			</tr>
			<tr class="contents">
				<td class="label"><?= _LBL_CHECKED_BY ?></td>
				<td><label><?= $_SESSION["user"]["name"]; ?></label></td>
			</tr>
			<tr class="contents">
				<td class="label"><?= _LBL_DATE ?></td>
				<td><label><?= date("d-m-Y"); ?></label></td>
			</tr>
                        <tr id="tr_reason" class="contents" style="display:none">
				<td class="label nil_submissionchecking_01"><?= _LBL_REASON ?></td>
				<td><?php 
					$param1 = "checkReason";
					$param2 = isset($_POST[$param1])?$_POST[$param1]:"";
					$param3 = array("cols"=>"70","rows"=>"4");
					echo form_textarea($param1,$param2,$param3);
				?></td>
			</tr>
			<tr class="contents">
				<td class="label"><?= _LBL_REMARK ?></td>
				<td><?php 
					$param1 = "checkRemark";
					$param2 = isset($_POST[$param1])?$_POST[$param1]:"";
					$param3 = array("cols"=>"70","rows"=>"4");
					echo form_textarea($param1,$param2,$param3);
				?></td>
			</tr>
			
			<tr id="tr_contact" class="contents" style="display:none">
				<td class="label"><?= _LBL_CONTACT_NO ?></td>
				<td><?php 
					$param1 = "checkContact";
					$param2 = isset($_POST[$param1])?$_POST[$param1]:"";
					$param3 = array("type"=>"text");
					echo form_input($param1,$param2,$param3);
				?></td>
			</tr>
		</table>
		</div>
	
		<div id="tabs-4">
		<?php include "companyView.php"; ?>
		</div>
	</div>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents nil_submissionchecking_02" >
			<td align="center"><?php
				echo '<span id="span_save" name="span_save" style="display:">';
				$dOthers = array("type"=>"button");
				echo form_button("save",_LBL_SAVE . $spLoad,$dOthers);
				echo "&nbsp;";
				echo "</span>";
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('$formBack');");
				echo form_button("back",_LBL_CANCEL,$dOthers);
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>