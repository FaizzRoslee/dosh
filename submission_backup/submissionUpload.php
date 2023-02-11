<?php
	//****************************************************************/
	// filename: submissionList_get.php
	// description: list of the submission
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
	$l = $_SESSION['lang'];
	//==========================================================	
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'formElement.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	include_once $sys_config['includes_path'].'faq.php';
	//==========================================================
	$Usr_ID = $_SESSION['user']['Usr_ID'];
	$levelID = $_SESSION['user']['Level_ID'];
	if($levelID==5) exit();
	//==========================================================
	func_header("",
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.css,". // css
				$sys_config['includes_path']."jquery/Uploader/fileuploader.css,". // css
				"", // css
				$sys_config['includes_path']."jquery/Uploader/fileuploader.js,". // javascript
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
				$sys_config["includes_path"]."jquery/Tabbed/jquery-ui-tabs.js,". // javascript
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

?>
	
	<script language="javascript">
	jQuery(document).ready(function() {
		$("#myTab").tabs();
		
		$("#preview-sub-button, #preview-mix-button, .sp-load").hide();
		
		$("button").button();
		
		$("#bt-dl-sub, #bt-dl-mix, #bt-format-sub, #bt-format-mix").click(function(){
			// $(".sp-load").show();
			$(this).button("disable");
			$(this).closest("td").find(".sp-load").show();
			// alert( $(this).closest("td").find(".sp-load").show() );
			var type;
			if( $(this).attr("id") == 'bt-dl-sub' ||  $(this).attr("id") == 'bt-dl-mix' ){
			
				if( $(this).attr("id") == 'bt-dl-sub' ){
					type = 'sub';
				}
				else if( $(this).attr("id") == 'bt-dl-mix' ){
					type = 'mix';
				}
				
				$.ajax({
					url: 'dl-reference.php',
					type : 'POST',
					dataType : 'json',
					data: "type=" + type, 
					success: function( msg ) {
						// alert(msg.url);
						
						$("#bt-dl-sub, #bt-dl-mix, #bt-format-sub, #bt-format-mix").button("enable");
						window.location.href = msg.url;
						$(".sp-load").hide();
						// $("button").removeAttr("disabled");
					},
					error: function(xhr, ajaxOptions, thrownError){
						alert(xhr.statusText);
						alert(thrownError);
					}
				});
				return false;
			}
			else if( $(this).attr("id") == 'bt-format-sub' ||  $(this).attr("id") == 'bt-format-mix' ){
			
				if( $(this).attr("id") == 'bt-format-sub' ){
					type = 'sub';
				}
				else if( $(this).attr("id") == 'bt-format-mix' ){
					type = 'mix';
				}
				
				$.ajax({
					url: 'dl-format.php',
					type : 'POST',
					dataType : 'json',
					data: "type=" + type, 
					success: function( msg ) {
						// alert(msg.url);
						$("#bt-dl-sub, #bt-dl-mix, #bt-format-sub, #bt-format-mix").button("enable");
						window.location.href = msg.url;
						$(".sp-load").hide();
						// $("button").removeAttr("disabled");
					},
					error: function(xhr, ajaxOptions, thrownError){
						alert(xhr.statusText);
						alert(thrownError);
					}
				});
				return false;
			}
			else return false;
		});
		
		$("#sub-send,#sub-submit").click(function(){
			$("input[name=ftype]").val("sub");
			if( $(this).attr("id")=="sub-submit" ){
				// alert( $(this).attr("id") );
				$(this).button("disable").find(".sp-load").show();
			}
			
			$.ajax({
				url: 'submission-ajax.php',
				type : 'POST',
				dataType : 'json',
				data: $('#myForm').serializeArray(), 
				success: function( msg ) {
					alert( msg.message );
					// alert( msg.unlink );
					// alert( msg.sSQL );
					parent.left.location.href="<?= $sys_config['frame_path'].'left_submission.php?menu=1' ?>";
					location.href="submissionList.php";
				},
				error: function(xhr, ajaxOptions, thrownError){
					alert(xhr.statusText);
					alert(thrownError);
				}			
			});			
			return false;
		});
		
		$("#mix-send,#mix-submit").click(function(){
			$("input[name=ftype]").val("mix");
			if( $(this).attr("id")=="mix-submit" ){
				// alert( $(this).attr("id") );
				$(this).button("disable").find(".sp-load").show();
			}
			$.ajax({
				url: 'submission-ajax.php',
				type : 'POST',
				dataType : 'json',
				data: $('#myForm').serializeArray(), 
				success: function( msg ) {
					alert( msg.message );
					// alert( msg.unlink );
					parent.left.location.href="<?= $sys_config['frame_path'].'left_submission.php?menu=1' ?>";
					location.href="submissionList.php";
				},
				error: function(xhr, ajaxOptions, thrownError){
					alert(xhr.statusText);
					alert(thrownError);
				}			
			});			
			return false;
		});
		
		$("#sub-submit,#mix-submit").hide();
		$("input[name=disclaimer1]").click(function(){
			if( $(this).is(":checked") ) $("#sub-submit").show();
			else $("#sub-submit").hide();
		});
		$("input[name=disclaimer2]").click(function(){
			if( $(this).is(":checked") ) $("#mix-submit").show();
			else $("#mix-submit").hide();
		});
		
		$("#sub-cancel").click(function(){
			$("#preview-info-sub").html("");
			$("#preview-sub-button,#sub-submit").hide();
			$("input[name=disclaimer1]").removeAttr("checked");
			$.ajax({
				url: 'submission-ajax.php',
				type : 'POST',
				dataType : 'json',
				data: "fn=unlink&fpath=" + $("#sub-file").val(), 
				success: function( msg ) {
					// alert( msg.message );
					$("#sub-file,#filepath").val("");
				},
				error: function(xhr, ajaxOptions, thrownError){
					alert(xhr.statusText);
					alert(thrownError);
				}			
			});			
			return false;
		});		
		$("#mix-cancel").click(function(){
			$("#preview-info-mix").html("");
			$("#preview-mix-button,#mix-submit").hide();
			$("input[name=disclaimer2]").removeAttr("checked");
			$.ajax({
				url: 'submission-ajax.php',
				type : 'POST',
				dataType : 'json',
				data: "fn=unlink&fpath=" + $("#mix-file").val(), 
				success: function( msg ) {
					// alert( msg.message );
					$("#mix-file,#filepath").val("");
				},
				error: function(xhr, ajaxOptions, thrownError){
					alert(xhr.statusText);
					alert(thrownError);
				}			
			});			
			return false;
		});
		
		$(".cl-confirm1_all").live("click",function(){
			$(".cl-confirm1").prop("checked", $(this).is(":checked") );
		});
		
		$(".cl-confirm2_all").live("click",function(){
			$(".cl-confirm2").prop("checked", $(this).is(":checked") );
		});
	});
	</script>
	<?php func_window_open2(_LBL_UPLOAD_SUBMISSION); ?>
	<?php
		$spLoad = '<span class="sp-load"> <img src="'. $sys_config['images_path'] .'ui-anim_basic_16x16.gif"> </span>';
	?>
	<form name="myForm" id="myForm" action="" method="post">
	<input type="hidden" name="fn" value="bulk-submission" />
	<input type="hidden" name="ftype" value="" />
	<input type="hidden" name="filepath" id="filepath" value="" />
	<input type="hidden" name="sub-file" id="sub-file" value="" />
	<input type="hidden" name="mix-file" id="mix-file" value="" />
	<input type="hidden" name="Usr_ID" value="<?= $Usr_ID ?>" />
	<div id="users-contain" class="ui-widget">
	<div class="ui-state-highlight ui-corner-all" style="margin-top: 20px; padding: 0 .7em;">
		<p>
			<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
			<strong><?= _LBL_GUIDELINE ?></strong>
		</p>
		<p style="text-align:justify; margin-right: 1.8em; margin-left: 1.8em;"><?= _LBL_GUIDELINE_STEP ?></p>
	</div>
	<br />
	<div id="myTab">
		<?php //=========================================================================== ?>
		<ul>
			<li><a href="#tabs-1" onClick=""><?= _LBL_SUBSTANCE ?></a></li>
			<li><a href="#tabs-2" onClick=""><?= _LBL_MIXTURE ?></a></li>
		</ul>
		<?php //=========================================================================== ?>
		<div id="tabs-1">
		
			<table class="ui-widget" width="100%">
				<tr>
					<td width="20%" class="ui-widget-header"><?= _LBL_REFERENCE ?></td>
					<td width="80%"><button id="bt-dl-sub"><?= _LBL_DOWNLOAD ?><span class="sp-load"> <img src="<?= $sys_config['images_path'] .'ui-anim_basic_16x16.gif' ?>"> </span></button></td>
				</tr>
				<tr>
					<td class="ui-widget-header"><?= _LBL_FORMAT ?></td>
					<td><button id="bt-format-sub"><?= _LBL_DOWNLOAD ?><span class="sp-load"> <img src="<?= $sys_config['images_path'] .'ui-anim_basic_16x16.gif' ?>"> </span></button></td>
				</tr>
				<tr>
					<td class="ui-widget-header"><?= _LBL_SUBSTANCE ?></td>
					<td>
					<div id="file-uploader1">		
					<noscript><p>Please enable JavaScript to use file uploader.</p><!-- class="cl_hide" or put a simple form for upload here --></noscript>         
					</div>
					</td>
				</tr>
			</table>
			<div id="preview-info-sub"></div>
			
			<div id="preview-sub-button">
				<?php if(getValidTime()==true){ ?>
				<table cellpadding="3" class="ui-widget">
					<tr>
						<td class="ui-widget-header" colspan="2"><?= _LBL_TERM_COND ?></td>
					</tr>
					<tr>
						<td width="5%" style="text-align:center; vertical-align:middle;"><?php
							$param1 = "disclaimer1";
							$param2 = "11";
							$param3 = array("type"=>"checkbox");
							echo form_input($param1,$param2,$param3); ?></td>
						<td style="text-align:justify;"><label for="disclaimer1"><?= _LBL_DISCLAIMER_DETAIL ?></label></td>
					</tr>
				</table>
				<?php } ?>
				<table cellpadding="3" class="ui-widget"><tr>
					<td style="text-align: center;"><button id="sub-send" class="nil_submission_upload_01"><?= _LBL_SAVE ?></button>&nbsp;<button id="sub-submit"><?= _LBL_SAVE ." & ". _LBL_SUBMIT . $spLoad ?></button>&nbsp;<button id="sub-cancel"><?= _LBL_CANCEL ?></button></td>
				</tr></table>
			</div>

		</div>
		<div id="tabs-2">
		
			<table border="0" class="ui-widget" width="100%">
				<tr>
					<td width="20%" class="ui-widget-header"><?= _LBL_REFERENCE ?></td>
					<td width="80%"><button id="bt-dl-mix"><?= _LBL_DOWNLOAD ?><span class="sp-load"> <img src="<?= $sys_config['images_path'] .'ui-anim_basic_16x16.gif' ?>"> </span></button></td>
				</tr>
				<tr>
					<td class="ui-widget-header"><?= _LBL_FORMAT ?></td>
					<td><button id="bt-format-mix"><?= _LBL_DOWNLOAD ?><span class="sp-load"> <img src="<?= $sys_config['images_path'] .'ui-anim_basic_16x16.gif' ?>"> </span></button></td>
				</tr>
				<tr>
					<td class="ui-widget-header"><?= _LBL_MIXTURE ?></td>
					<td>
					<div id="file-uploader2">		
					<noscript><p>Please enable JavaScript to use file uploader.</p><!-- class="cl_hide" or put a simple form for upload here --></noscript>         
					</div>
					</td>
				</tr>
			</table>
			<div id="preview-info-mix"></div>
			
			<div id="preview-mix-button">
				<?php if(getValidTime()==true){ ?>
				<table cellpadding="3" class="ui-widget">
					<tr>
						<td class="ui-widget-header" colspan="2"><?= _LBL_TERM_COND ?></td>
					</tr>
					<tr>
						<td width="5%" style="text-align:center; vertical-align:middle;"><?php
							$param1 = "disclaimer2";
							$param2 = "11";
							$param3 = array("type"=>"checkbox");
							echo form_input($param1,$param2,$param3); ?></td>
						<td style="text-align:justify;"><label for="disclaimer2"><?= _LBL_DISCLAIMER_DETAIL ?></label></td>
					</tr>
				</table>
				<?php } ?>
				<table cellpadding="3" class="ui-widget"><tr>
					<td style="text-align: center;"><button id="mix-send" ><?= _LBL_SAVE ?></button>&nbsp;<button id="mix-submit"><?= _LBL_SAVE ." & ". _LBL_SUBMIT . $spLoad ?></button>&nbsp;<button id="mix-cancel"><?= _LBL_CANCEL ?></button></td>
				</tr></table>
			</div>

		</div>
	</div>
	
	</div>
	<br />
	</form>
	<script language="javascript">
	function createUploader1(){
		var uploader1 = new qq.FileUploader({
			element: document.getElementById("file-uploader1"),
			multiple: false,
			action: "<?= $sys_config["includes_path"] . "class-uploader.php" ?>",
			params: { up_path: "<?= $sys_config["upload_path"] ."submission/" ?>" },
			sizeLimit: "2097152", // "1048576", // 1MB
			allowedExtensions:["xls"],
			debug: true,
			onComplete: function(id, fileName, result){
				$("#filepath,#sub-file").val( result.path );
				// alert(result.path);
				if(result.path){
					jQuery.ajax({
						type: "POST",
						data: "fpath=" + result.path,
						url: "phpexcel-substance.php",
						dataType: "json",
						success: function(msg){
							$(".qq-upload-list").html("");
							$("#preview-info-sub").html( msg.excel );
							$("#preview-sub-button").show();
						}
					});
				}
				
			}
		});
	}
	
	function createUploader2(){
		var uploader2 = new qq.FileUploader({
			element: document.getElementById('file-uploader2'),
			multiple: false,
			action: '<?= $sys_config['includes_path'] . 'class-uploader.php' ?>',
			params: { up_path: '<?= $sys_config['upload_path'] ."submission/" ?>' },
			sizeLimit: '2097152', // '1048576', // 1MB
			allowedExtensions:["xls"],
			debug: true,
			onComplete: function(id, fileName, result){
				$('#filepath,#mix-file').val( result.path );
				// alert(result.path);
				if(result.path){
					jQuery.ajax({
						type: "POST",
						data: "fpath=" + result.path,
						url: "phpexcel-mixture.php",
						dataType: "json",
						success: function(msg){
							$('.qq-upload-list').html('');
							$("#preview-info-mix").html( msg.excel );
							$("#preview-mix-button").show();
						}
					});
				}
			}
		});
	}
	
	window.onload = function() {
		createUploader1();
		createUploader2();
	};
	</script>
	<?php func_window_close2(); ?>