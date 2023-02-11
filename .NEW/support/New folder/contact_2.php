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
	if($levelID==5) exit();
	//==========================================================
	func_header("",
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.css,". // css
				$sys_config['includes_path']."jquery/jHtmlArea/jHtmlArea.css,". // css	
				"", // css
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
				$sys_config['includes_path']."jquery/jHtmlArea/js/jHtmlArea-0.7.0.js,". // javascript
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
?>
	<style>
	.left { text-align:left; padding: 5px; }
	td.fontstyle { font-size: 14px; font-weight: bold; line-height:200% }
	.hand-cursor { cursor:hand;cursor:pointer; }
	</style>
	<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$().UItoTop({ easingType: 'easeOutQuart' });
		
		$("#myTab").tabs();
		
		$(".ui-icon").click(function(){
			alert('aaa');
			/*
			$.ajax({
				url: '<?= module_href2('administration','administration-ajax',array()); ?>',
				type : 'POST',
				dataType : 'json',
				data: 'fn=design&ftype=delete&id=' + gaiSelected, 
				success: function( msg ) {
					jAlert( msg.result,'<?= LBL_MESSAGE ?>',function (r) {
						if (r) {
							oTable.fnClearTable(0);
							oTable.fnDraw();
							gaiSelected = [];
						}
					});	
				},
				error: function(xhr, ajaxOptions, thrownError){
					alert(xhr.statusText);
					alert(thrownError);
				}			
			});
			*/
			return false;
		}).addClass("hand-cursor");
		
		$("#contact-info").htmlarea();
	} );
	</script>	
	<?php func_window_open2(_LBL_SUPPORT .' :: '. _LBL_CONTACT_US); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<input type="hidden" name="fn" value="support" />
	<input type="hidden" name="ftype" value="contact" />
	<div id="users-contain" class="ui-widget">
	
		<table width="100%" class="ui-widget ui-widget-content">
		<caption class="ui-widget-header ui-corner-top left"><?= _LBL_CONTACT_US ?> <span class="ui-icon ui-icon-pencil" style="float: right;"></span></caption>
		<tbody>
			<tr id="contact-view">
				<td class="fontstyle">
					<div>Chemical Management Division</div>
					<div>Department of Occupational Safety and Health</div>
					<div>(Ministry of Human Resource)</div>
					<div>Level 2, 3 & 4, Block D3, Complex D,</div>
					<div>Federal Government Administrative Centre</div>
					<div>62530 W. P. Putrajaya</div>
					<br />
					<div>Tel: 03 - 8886 5000</div>
					<div>Fax:03 - 8889 2443</div>
				</td>
			</tr>
			<tr id="contact-edit">
				<td><?php
					$param1 = 'contact-info';
					$param2 = isset($_POST[$param1])?$_POST[$param1]:$infoDesc;
					$param3 = array('cols'=>'90','rows'=>'20');
					echo form_textarea($param1,$param2,$param3);
				?></td>
			</tr>
		</tbody>
		</table>
		
	</div>
	</form>
	<?php func_window_close2(); ?>