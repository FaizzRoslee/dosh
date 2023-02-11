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
				$sys_config['includes_path']."jquery/Others/jquery.multi-open-accordion-1.5.3.js,". // javascript
				// $sys_config['includes_path']."jquery/Others/jquery.validate.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.validation.1.8.1/jquery.validate.js,". // javascript
				$sys_config['includes_path']."Javascript/js_common.js,". // javascript
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
	$id 	= isset($_GET['id']) ? $_GET['id'] : 0;
	$ftype 	= isset($_GET['ftype']) ? $_GET['ftype'] : 'add';
	
	$faq_q_eng = '';
	$faq_a_eng = '';
	$faq_q_may = '';
	$faq_a_may = '';
	
	if( !empty($id) ){
		$dCols 	= 'Faq_Q_eng,Faq_A_eng,Faq_Q_may,Faq_A_may';
		$dTable = 'tbl_Faq';
		$dJoin	= '';
		$dWhere = array("AND Faq_ID" => "= ".quote_smart($id));
		$arrayData = _get_arrayData($dCols,$dTable,$dJoin,$dWhere);
		foreach($arrayData as $data){
			$faq_q_eng = $data['Faq_Q_eng'];
			$faq_a_eng = $data['Faq_A_eng'];
			$faq_q_may = $data['Faq_Q_may'];
			$faq_a_may = $data['Faq_A_may'];
		}
	}
?>
	<style>
	.left { text-align:left; padding: 5px; }
	label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
	</style>
	<script type="text/javascript" charset="utf-8">
	$(document).ready(function() {
		$().UItoTop({ easingType: 'easeOutQuart' });
		
		/* $("button").removeClass("btn-new-style").button(); */
		$("#fCategory").change(function(){
			$("input[name=fCategory-label]").val( $(this).find("option:selected").text() );
		});
		
		$("#faq_a_eng, #faq_a_may").htmlarea({
			toolbar: [
				["html"],["bold", "italic", "underline"],
				["h1", "h2", "h3", "h4", "h5", "h6"],
				["cut", "copy", "paste"],
				["orderedList","unorderedList"],["superscript","subscript"],
				["indent","outdent"],["justifyLeft","justifyCenter","justifyRight"],
				["link", "unlink", "image", "horizontalrule"]
			]
		});
		
		$("#myForm").validate({
			rules: {
				faq_q_eng: "required",
				faq_a_eng: "required",
				faq_q_may: "required",
				faq_a_may: "required"
			},
			messages: {
				faq_q_eng: "<?= _LBL_REQUIRED_FIELD ?>",
				faq_a_eng: "<?= _LBL_REQUIRED_FIELD ?>",
				faq_q_may: "<?= _LBL_REQUIRED_FIELD ?>",
				faq_a_may: "<?= _LBL_REQUIRED_FIELD ?>"
			},
			submitHandler: function(form) {
				// return false
				$.ajax({
					url: 'support-ajax.php',
					type : 'POST',
					dataType : 'json',
					data: $('#myForm').serializeArray(), 
					success: function( msg ) {
						alert( msg.result );
						$("#cancel").trigger("click");
					},
					error: function(xhr, ajaxOptions, thrownError){
						alert(xhr.statusText);
						alert(thrownError);
					}
				});
				return false;
			}
		
		});
				
		$("#myTab").tabs({
			select: function( e, ui ) {
				// set the tab_index equals to current index
				<?php if( $ftype!='view' ){ ?>
				if( ui.index==1 ){
					$("#send").hide();
					$( "button[name=cancel]" ).button( "option", "label", "<?= _LBL_BACK ?>" );
				}
				else{
					$("#send").show();
					$( "button[name=cancel]" ).button( "option", "label", "<?= _LBL_CANCEL ?>" );
				}
				<?php } ?>
			}
		});
		
		$('#multiOpenAccordion').multiOpenAccordion({
			active: [0,1]
		});
	} );
	</script>
	<?php func_window_open2(_LBL_SUPPORT .' :: '. _LBL_FREQUENTLY_ASK_Q); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<input type="hidden" name="id" value="<?= $id ?>" />
	<input type="hidden" name="fn" value="faq" />
	<input type="hidden" name="ftype" value="<?= $ftype ?>" />
	<input type="hidden" name="Usr_ID" value="<?= $Usr_ID ?>" />
	<div id="users-contain" class="ui-widget">
	
		<?php if($ftype!='add'){ ?>
		<div id="myTab">
			<ul>
				<li><a href="#tabs-1"><?= ($ftype=='view') ? _LBL_VIEW : _LBL_EDIT ?></a></li>
				<li><a href="#tabs-2"><?= _LBL_PREVIEW ?></a></li>
			</ul>
			
			
			<div id="tabs-1">
		<?php } ?>
		
		<?php
			if($ftype!='view'){
		?>
		<table class="ui-widget ui-widget-content">
		<caption class="ui-widget-header ui-corner-top left"><?= _LBL_ENGLISH ?></caption>
		<tbody>
			<tr>
				<td class="ui-widget-header" width="20%"><?= _LBL_QUESTION ?></td>
				<td width="80%"><?= form_textarea('faq_q_eng',$faq_q_eng,array('style'=>'width:100%;','rows'=>'2')); ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_ANSWER ?></td>
				<td><?php
					$param1 = 'faq_a_eng';
					$param2 = $faq_a_eng;
					$param3 = array('style'=>'width:80%','rows'=>'15');
					echo form_textarea($param1,$param2,$param3);
				?></td>
			</tr>
		</tbody>
		</table>
		<table class="ui-widget ui-widget-content">
		<caption class="ui-widget-header ui-corner-top left"><?= _LBL_MALAY ?></caption>
		<tbody>
			<tr>
				<td class="ui-widget-header" width="20%"><?= _LBL_QUESTION ?></td>
				<td width="80%"><?= form_textarea('faq_q_may',$faq_q_may,array('style'=>'width:100%;','rows'=>'2')); ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_ANSWER ?></td>
				<td><?php
					$param1 = 'faq_a_may';
					$param2 = $faq_a_may;
					$param3 = array('style'=>'width:80%','rows'=>'15');
					echo form_textarea($param1,$param2,$param3);
				?></td>
			</tr>
		</tbody>
		</table>
		<?php
			}
			else {
		?>

				<table class="ui-widget ui-widget-content">
				<caption class="ui-widget-header ui-corner-top left"><?= _LBL_ENGLISH ?></caption>
				<tbody>
					<tr>
						<td class="ui-widget-header" width="20%"><?= _LBL_QUESTION ?></td>
						<td width="80%"><?= nl2br($faq_q_eng); ?></td>
					</tr>
					<tr>
						<td class="ui-widget-header"><?= _LBL_ANSWER ?></td>
						<td><?= nl2br($faq_a_eng); ?></td>
					</tr>
				</tbody>
				</table>
				<table class="ui-widget ui-widget-content">
				<caption class="ui-widget-header ui-corner-top left"><?= _LBL_MALAY ?></caption>
				<tbody>
					<tr>
						<td class="ui-widget-header" width="20%"><?= _LBL_QUESTION ?></td>
						<td width="80%"><?= nl2br($faq_q_may); ?></td>
					</tr>
					<tr>
						<td class="ui-widget-header"><?= _LBL_ANSWER ?></td>
						<td><?= nl2br($faq_a_may); ?></td>
					</tr>
				</tbody>
				</table>
		<?php 
			}
		?>
		
		<?php if($ftype!='add'){ ?>	
			</div>
			
			<div id="tabs-2">
			
				<div id="multiOpenAccordion">
					<h3><a href="#">Q: <?= $faq_q_eng ?></a></h3>
					<div><p>A: <?= $faq_a_eng ?></p></div>
					<h3><a href="#">Q: <?= $faq_q_may ?></a></h3>
					<div><p>A: <?= $faq_a_may ?></p></div>
				</div>
				
			</div>
						
		</div>
		<?php } ?>
		
		<table class="ui-widget ui-widget-content">
			<tr>
				<td style="text-align:center;"><?php
					if( $ftype!='view' ){
						echo form_button('send',_LBL_SAVE,array());
						echo '&nbsp;';
						$btn = _LBL_CANCEL;
					}
					else $btn = _LBL_BACK;
					$arrOther = array('type'=>'button','onClick'=>'redirectForm(\'faq.php\');');
					// else $arrOther = array('type'=>'reset');
					echo form_button('cancel',$btn,$arrOther);
				?></td>
			</tr>
		</table>
		
	</div>
	</form>
	<?php func_window_close2(); ?>