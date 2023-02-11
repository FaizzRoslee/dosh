<?php
	//****************************************************************/
	// filename: renewList.php
	// description: list of the renew submission
	//***************************************************************/
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
	$lang = $_SESSION['lang'];
	//==========================================================	
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'formElement.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	$Usr_ID = $_SESSION['user']['Usr_ID'];
	$levelID = $_SESSION['user']['Level_ID'];
	// if($levelID==5) exit();
	//==========================================================
	func_header("",
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.css,". // css
				$sys_config['includes_path']."jquery/css/demos.css,". // css
				"", // css
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
				$sys_config['includes_path']."Javascript/js_common.js,". // javascript
				// $sys_config['includes_path']."jquery/Others/jquery.validate.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.validation.1.8.1/jquery.validate.js,". // javascript
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
	if(isset($_GET["id"])) $id = $_GET["id"];
	else $id = 0;
	
	$arLang = explode(".",$fileLang);
	
	$fName = $fContact = $fEmail = $fType = $fSubject = $fMessage = "";
	if(!empty($id)){
	
		$dataTable = "tbl_Feedback";
		$dataColumns = "Feedback_Name,Feedback_Contact,Feedback_Email,(SELECT Type_Name_". $arLang[0] ." FROM tbl_Feedback_Type WHERE Type_ID = $dataTable.Type_ID) AS TypeName,Feedback_Subject,Feedback_Message,RecordDate";
		$dataJoin = "";
		$dataWhere = array("AND Feedback_ID" => "= ".quote_smart($id));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
		foreach($arrayData as $detail){
			$fName 		= $detail["Feedback_Name"];
			$fContact 	= $detail["Feedback_Contact"];
			$fEmail		= $detail["Feedback_Email"];
			$fType 		= $detail["TypeName"];
			$fSubject	= $detail["Feedback_Subject"];
			$fMessage 	= $detail["Feedback_Message"];
			$fTime 		= $detail["RecordDate"];
			
		}
	}
	
	$spLoad = '<span class="sp-load" style="display:none;"> <img src="'. $sys_config['images_path'] .'ui-anim_basic_16x16.gif"> </span>';
?>
	<style>
	.left { text-align:left; padding: 5px; }
	label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
	</style>
	<script type="text/javascript" charset="utf-8">
	var asInitVals = new Array();
	$(document).ready(function() {
		$().UItoTop({ easingType: 'easeOutQuart' });
	
		var new_width = $(document).width() * 0.9;
		$("#demo, #container").css({ 'width': new_width + 'px', 'margin': '5px auto', 'padding':'0' });
	
		
		/* $("button").removeClass("btn-new-style").button(); */
		$("#fCategory").change(function(){
			$("input[name=fCategory-label]").val( $(this).find("option:selected").text() );
		});
		
		$("#send").on("click",function(){
			$("label.error[for=reply]").hide();
			if( $("#reply").val()=="" ){
				$("label.error[for=reply]").show();
				return false;
			}
			
			$(this).button("disable").find(".sp-load").show();
			$.ajax({
				url: 'support-ajax.php',
				type : 'POST',
				dataType : 'json',
				data: $('#myForm').serializeArray(), 
				success: function( msg ) {
					
					$("#send").button("enable").find(".sp-load").hide();
					alert( msg.result );
					//$("#cancel").trigger("click");
					location.href = "<?= $sys_config['support_path']."feedback.php" ?>"
				},
				error: function(xhr, ajaxOptions, thrownError){
					alert(xhr.statusText);
					alert(thrownError);
				}
			});
			
		});
		$("#myForm").validate({
			rules: {
				reply: "required"
			},
			messages: {
				reply: "<?= _LBL_REQUIRED_FIELD ?>"
			},
			submitHandler: function(form) {
				// return false
				$.ajax({
					url: 'support-ajax.php',
					type : 'POST',
					dataType : 'json',
					data: $('#myForm').serializeArray(), 
					success: function( msg ) {
						
						$("#send").button("enable").find(".sp-load").hide();
						alert( msg.result );
						//$("#cancel").trigger("click");
						location.href = "<?= $sys_config['support_path']."feedback.php" ?>"
					},
					error: function(xhr, ajaxOptions, thrownError){
						alert(xhr.statusText);
						alert(thrownError);
					}
				});
				return false;
			}
		
		});
		
		
		$("#cancel").on("click",function(){
			location.href = "<?= $sys_config['support_path']."feedback.php" ?>"
		});
		
	} );
	</script>
	<?php func_window_open2(_LBL_SUPPORT .' :: '. _LBL_FEEDBACK .' :: '. _LBL_REPLY); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<input type="hidden" name="fn" value="reply" />
	<input type="hidden" name="ftype" value="add" />
	<input type="hidden" name="Usr_ID" value="<?= $Usr_ID ?>" />
	<input type="hidden" name="id" value="<?= $id ?>" />
	
		
	<div id="users-contain" class="ui-widget">
	<table class="ui-widget ui-widget-content">
	<caption class="ui-widget-header ui-corner-top left"><?= _LBL_FEEDBACK ?></caption>
	<tbody>
		<tr>
			<td class="ui-widget-header" width="20%"><?= _LBL_NAME ?></td>
			<td width="80%"><label><?= $fName ?></label><?= form_input("fName",$fName,array("type"=>"hidden")) ?></td>
		</tr>
		<tr>
			<td class="ui-widget-header"><?= _LBL_CONTACT_NO ?></td>
			<td><label><?= $fContact ?></label></td>
		</tr>
		<tr>
			<td class="ui-widget-header"><?= _LBL_EMAIL ?></td>
			<td><label><?= $fEmail ?></label><?= form_input("fEmail",$fEmail,array("type"=>"hidden")) ?></td>
		</tr>
		<tr>
			<td class="ui-widget-header"><?= _LBL_TYPE ?></td>
			<td><label><?= $fType ?></label><?= form_input("fType",$fType,array("type"=>"hidden")) ?></td>
		</tr>
		<tr>
			<td class="ui-widget-header"><?= _LBL_SUBJECT ?></td>
			<td><label><?= $fSubject ?></label><?= form_input("fSubject",$fSubject,array("type"=>"hidden")) ?><?= form_input("fTime",$fTime,array("type"=>"hidden")) ?></td>
		</tr>
		<tr>
			<td class="ui-widget-header"><?= _LBL_MESSAGE ?></td>
			<td><label><?= $fMessage ?></label><?= form_textarea("fMessage",$fMessage,array("style"=>"display: none")) ?></td>
		</tr>
	</tbody>
	</table>
	
	<table class="ui-widget ui-widget-content">
	<caption class="ui-widget-header ui-corner-top left"><?= _LBL_REPLY ." (". _LBL_INFO_WILL_BE_SEND_TO_SENDER .")" ?></caption>
	<tbody>
		<tr>
			<td class="ui-widget-header" width="20%"><?= _LBL_MESSAGE ?></td>
			<td width="80%"><?= form_textarea("reply","",array("rows"=>"4","style"=>"width:50%")) ?><label for="reply" class="error" style="display:none;"><?= _LBL_REQUIRED_FIELD ?></label></td>
		</tr>
	</tbody>
	</table>	

	<table class="ui-widget ui-widget-content">
		<tr>
			<td style="text-align:center;"><?php
				echo form_button('send',_LBL_SAVE . $spLoad,array("type"=>"button"));
				echo '&nbsp;';
				echo form_button('cancel',_LBL_CANCEL,array('type'=>'button'));
			?></td>
		</tr>
	</table>
	</div>
	
	<br />
	<br />
	</form>
	<?php func_window_close2(); ?>