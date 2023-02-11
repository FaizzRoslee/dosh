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
	if($levelID==5) exit();
	//==========================================================
	func_header("",
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.css,". // css
				$sys_config['includes_path']."jquery/jHtmlArea/jHtmlArea.css,". // css	
				"", // css
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
				$sys_config['includes_path']."jquery/jHtmlArea/js/jHtmlArea-0.7.0.js,". // javascript
				$sys_config['includes_path']."jquery/Others/jquery.multi-open-accordion-1.5.3.js,". // javascript
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
	
	$fName 		= '';
	$fContact 	= '';
	$fEmail 	= '';
	$fType 		= '';
	$fSubject 	= '';
	$fMsg 		= '';
	$fDate 		= '';
	$rMsg = $rDate = "";

	if( !empty($id) ){
		$dCols 	= "Feedback_Name,Feedback_Contact,Feedback_Email,(SELECT Type_Name_$lang  FROM tbl_Feedback_Type WHERE Type_ID = tbl_Feedback.Type_ID) AS type, Feedback_Subject, Feedback_Message, DATE_FORMAT(RecordDate,'%d-%m-%Y') AS date, ReplyMessage, ReplyBy, DATE_FORMAT(ReplyDate,'%d-%m-%Y') AS rDate";
		$dTable = 'tbl_Feedback';
		$dJoin	= '';
		$dWhere = array("AND Feedback_ID" => "= ".quote_smart($id));
		$arrayData = _get_arrayData($dCols,$dTable,$dJoin,$dWhere);
		foreach($arrayData as $data){
			$fName 		= $data["Feedback_Name"];
			$fContact 	= $data["Feedback_Contact"];
			$fEmail 	= $data["Feedback_Email"];
			$fType 		= $data["type"];
			$fSubject 	= $data["Feedback_Subject"];
			$fMsg 		= $data["Feedback_Message"];
			$fDate 		= $data["date"];
			$rMsg 		= $data["ReplyMessage"];
			$rDate 		= $data["rDate"];
			$rBy 		= _get_StrFromCondition("sys_User_Staff","User_Fullname","User_ID",$data["ReplyBy"]);
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
	} );
	</script>
	<?php func_window_open2(_LBL_SUPPORT .' :: '. _LBL_FEEDBACK); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<input type="hidden" name="id" value="<?= $id ?>" />
	<input type="hidden" name="fn" value="faq" />
	<input type="hidden" name="ftype" value="<?= $ftype ?>" />
	<input type="hidden" name="Usr_ID" value="<?= $Usr_ID ?>" />
	<div id="users-contain" class="ui-widget">
	
		<table class="ui-widget ui-widget-content">
		<caption class="ui-widget-header ui-corner-top left"><?= _LBL_FEEDBACK ?></caption>
		<tbody>
			<tr>
				<td class="ui-widget-header" width="20%"><?= _LBL_NAME ?></td>
				<td width="80%"><?= $fName ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_CONTACT_NO ?></td>
				<td><?= $fContact ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_EMAIL ?></td>
				<td><?= $fEmail ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_TYPE ?></td>
				<td><?= $fType ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_SUBJECT ?></td>
				<td><?= $fSubject ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_MESSAGE ?></td>
				<td><?= nl2br($fMsg) ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_DATE ?></td>
				<td><?= $fDate ?></td>
			</tr>
		</tbody>
		</table>
		<table class="ui-widget ui-widget-content">
		<caption class="ui-widget-header ui-corner-top left"><?= _LBL_REPLY ?></caption>
		<tbody>
			<tr>
				<td width="20%" class="ui-widget-header"><?= _LBL_MESSAGE ?></td>
				<td width="80%"><?= nl2br($rMsg) ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_DATE ?></td>
				<td><?= $rDate ?></td>
			</tr>
			<tr>
				<td class="ui-widget-header"><?= _LBL_BY ?></td>
				<td><?= $rBy ?></td>
			</tr>
		</tbody>
		</table>
		
		<table class="ui-widget ui-widget-content">
			<tr>
				<td style="text-align:center;"><?php
					$arrOther = array('type'=>'button','onClick'=>'redirectForm(\'feedback.php\');');
					// else $arrOther = array('type'=>'reset');
					echo form_button('cancel',_LBL_BACK,$arrOther);
				?></td>
			</tr>
		</table>
		
	</div>
	</form>
	<?php func_window_close2(); ?>