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
				$sys_config['includes_path']."jquery/jHtmlArea/jHtmlArea.css,". // css	
				"", // css
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
				$sys_config['includes_path']."jquery/jHtmlArea/js/jHtmlArea-0.7.0.js,". // javascript
				// $sys_config["includes_path"]."jquery/Tabbed/jquery-ui-tabs.js,". // javascript
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
	$id = 10;
	$info = _get_StrFromCondition('sys_Info','Info_Desc','Info_ID',$id);
?>
	<style>
	.left { text-align:left; padding: 5px; }
	td.fontstyle { font-size: 14px; font-weight: bold; line-height:200% }
	.hand-cursor { cursor:hand;cursor:pointer; }
	div.jHtmlArea .ToolBar ul li a.custom_disk_button 
	{
		background: url(<?= $sys_config['includes_path'] ?>jquery/jHtmlArea/images/disk.png) no-repeat;
		background-position: 0 0;
	}
	div.jHtmlArea .ToolBar ul li a.custom_cancel_button
	{
		background: url(<?= $sys_config['includes_path'] ?>jquery/jHtmlArea/images/cancel.png) no-repeat;
		background-position: 0 0;
	}
	</style>
	<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$().UItoTop({ easingType: 'easeOutQuart' });
		
		// $("#myTab").tabs();
		$("#contact-edit").hide();
		
		function create_jhmtl(){
			$("#contact-info").htmlarea({
				toolbar: [
					["html"],["bold", "italic", "underline"],
					["h1", "h2", "h3", "h4", "h5", "h6"],
					[{
						// This is how to add a completely custom Toolbar Button
						css: "custom_disk_button",
						text: "Save",
						action: function(btn) {
							// 'this' = jHtmlArea object
							// 'btn' = jQuery object that represents the <A> "anchor" tag for the Toolbar Button
							
							// parent.$('frame[name=right]').location.reload();
							
							$("input[name=ftype]").val("edit");
							$.ajax({
								url: 'support-ajax.php',
								type : 'POST',
								dataType : 'json',
								data: $("#myForm").serializeArray(), 
								success: function( msg ) {
									alert( msg.result );
									parent.frames["right"].location.reload();
									// $("#contact-info").val( msg.result );
								},
								error: function(xhr, ajaxOptions, thrownError){
									alert(xhr.statusText);
									alert(thrownError);
								}			
							});

							
							
							// alert('SAVE!\n\n' + this.toHtmlString());
							// $("#contact-edit").hide();
							// $("#contact-view, .ui-icon").show();
							
							// $("#contact-details").html( $("#contact-info").val() );
						}
					}, {
						css: "custom_cancel_button",
						text: 'Cancel',
						action: function(btn) { parent.frames["right"].location.reload(); }
					}]
				]
			});
		}
		
		$(".ui-icon").click(function(){
			$(".ui-icon, #contact-view").hide();
			$("#contact-edit").show();
			$("input[name=ftype]").val("search");
			create_jhmtl();
			
			/*
			$.ajax({
				url: 'support-ajax.php',
				type : 'POST',
				dataType : 'json',
				data: $("#myForm").serializeArray(), 
				success: function( msg ) {
					alert( msg.result );
					$("#contact-info").val( msg.result );
				},
				error: function(xhr, ajaxOptions, thrownError){
					alert(xhr.statusText);
					alert(thrownError);
				}			
			});
			*/
			
			return false;
		}).addClass("hand-cursor");
	} );
	</script>
	<?php
		if($levelID<5)
			$edit = '<span class="ui-icon ui-icon-pencil" style="float: right;"></span>';
		else $edit = '';
	?>
	<?php func_window_open2(_LBL_SUPPORT .' :: '. _LBL_CONTACT_US); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<input type="hidden" name="fn" value="contact" />
	<input type="hidden" name="ftype" value="" />
	<input type="hidden" name="id" value="<?= $id ?>" />
	<input type="hidden" name="Usr_ID" value="<?= $Usr_ID ?>" />
	<div id="users-contain" class="ui-widget">
	
		<table width="100%" class="ui-widget ui-widget-content">
		<caption class="ui-widget-header ui-corner-top left"><?= _LBL_CONTACT_US ?><?= $edit ?></caption>
		<tbody>
			<tr id="contact-view">
				<td class="fontstyle"><span id="contact-details"><?= !empty($info) ? $info : '-' ?>
					<!--
					<div>Chemical Management Division</div>
					<div>Department of Occupational Safety and Health</div>
					<div>(Ministry of Human Resource)</div>
					<div>Level 2, 3 & 4, Block D3, Complex D,</div>
					<div>Federal Government Administrative Centre</div>
					<div>62530 W. P. Putrajaya</div>
					<br />
					<div>Tel: 03 - 8886 5000</div>
					<div>Fax:03 - 8889 2443</div>
					-->
				</span></td>
			</tr>
			<tr id="contact-edit">
				<td><?php
					$param1 = 'contact-info';
					$param2 = $info;
					$param3 = array('cols'=>'90','rows'=>'20');
					echo form_textarea($param1,$param2,$param3);
				?></td>
			</tr>
		</tbody>
		</table>
		
	</div>
	</form>
	<?php func_window_close2(); ?>