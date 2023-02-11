<?php
	//****************************************************************/
	// filename: reportList.php
	// description: list of the reports
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
	include_once $sys_config['includes_path'].'func_date.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	func_header("",
				$sys_config['includes_path']."css/global.css,". // css	
				"", // css	
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				// $sys_config['includes_path']."jquery/js/jquery-1.3.2.min.js,". // javascript
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
	/*******/
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
	$userLevelID = $_SESSION["user"]["Level_ID"];
	/*******/
?>
	<style>
	.center { text-align: center; }
	</style>
	<script language="Javascript">
	$(document).ready(function() {
		/* $("button").removeClass("btn-new-style").button(); */
	});
	
	(function($){
	})(jQuery);
	</script>
	<?php func_window_open2( _LBL_REPORT .' :: '._LBL_ENFORCEMENT_CONTACT_INFO); ?>
	<form name="myForm" action="" method="post">
	
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="5%" class="label center"><?= _LBL_BIL ?></td>
			<td width="20%" class="label center"><?= _LBL_STATE ?></td>
			<td width="75%" class="label center"><?= _LBL_CONTACT_INFO ?></td>
		</tr>
		<?php
			$i = 0;
			$dStates = _get_arraySelect("sys_State","State_ID","State_Name",array("AND isDeleted"=>"= 0"));
			foreach($dStates as $state){
				$i++;
				
				$dCols 	= "PIC_Name,PIC_Email,PIC_ContactNo";
				$dTable = "sys_User_Staff";
				$dJoin 	= array("sys_User"=>"$dTable.Usr_ID = sys_User.Usr_ID");
				$dWhere = array("AND State_ID" => "= ".quote_smart($state["value"]),
								"AND Level_ID" => "= 5",
								"AND Active" => "= 1",
								"AND isDeleted" => "= 0"
							);
				$arData = _get_arrayData($dCols,$dTable,$dJoin,$dWhere); 
				//=============================================================================
				$contact = "";
				foreach($arData as $detail){
					$name	 = $detail["PIC_Name"];
					$email	 = $detail["PIC_Email"];
					$phoneno = $detail["PIC_ContactNo"];
					
					if(empty($name)) $name = "-";
					if(empty($email)) $email = "-";
					if(empty($phoneno)) $phoneno = "-";
					
					$contact .= "<div>". _LBL_NAME .": $name</div>"
							. "<div>". _LBL_EMAIL .": $email</div>"
							. "<div>". _LBL_CONTACT_NO .": $phoneno</div><br />"
							;
				}
		?>
		<tr class="contents">
			<td class="center"><?= $i ?></td>
			<td><?= $state["label"] ?></td>
			<td><?= (!empty($contact)?$contact:"-") ?></td>
		</tr>
		<?php
			}
		?>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				
				$url_pdf = "report05_to_pdf.php";
				echo form_button('convert',_LBL_CONVERT_TO_PDF,array('type'=>'button','onClick'=>"funcPopup('".$url_pdf."');"));

			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>