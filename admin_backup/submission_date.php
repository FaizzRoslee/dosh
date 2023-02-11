<?php
	//****************************************************************/
	// filename: hazCategoryAddEdit.php
	// description: list of the 
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
	func_header("",
				$sys_config["includes_path"]."css/global.css,".// css	
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.css,". // css
				$sys_config["includes_path"]."jquery/datepicker/css/datepicker.css,". // css
				"", // css					
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				// $sys_config["includes_path"]."jquery/jquery.livequery.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.js,". // javascript
				$sys_config["includes_path"]."jquery/datepicker/js/bootstrap-datepicker.js,". // javascript
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
	$userLevelID = $_SESSION["user"]["Level_ID"];
	if($userLevelID>2) exit();
	/*********/
	if(isset($_POST["save"])){
		$year = isset($_POST["year"])?$_POST["year"]:'';
		$date_start = isset($_POST["date_start"])?$_POST["date_start"]:'';
		$date_end = isset($_POST["date_end"])?$_POST["date_end"]:'';
		$updBy = $_SESSION['user']['Usr_ID'];
		/*********/
		$date_id = _get_StrFromCondition("sys_submitdate","Date_ID","Submit_Year", $year);
		/*********/
		if(!empty($date_id)){
			$sqlUpd	= "UPDATE sys_submitdate SET"
					. " Date_Start = ".quote_smart(func_dmy2ymd($date_start)).","
					. " Date_Last = ".quote_smart(func_dmy2ymd($date_end)).","
					. " UpdateBy = ".quote_smart($updBy)."," 
					. " `Update` = NOW()" 
					. " WHERE Submit_Year = ".quote_smart($year)." LIMIT 1";
			// echo $sqlUpd;exit;
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
			$msg = ($resUpd) ? 'Update Successfully' : 'Update Unsuccessfully';
			/*********/
		} 
		else {
			$sqlIns	= "INSERT INTO sys_submitdate ("
					. " Date_Start,Date_Last,"
					. " Submit_Year,"
					. " RecordBy, RecordDate,"
					. " UpdateBy, `Update`"
					. " ) VALUES ("
					. " ".quote_smart(func_dmy2ymd($date_start)).",".quote_smart(func_dmy2ymd($date_end)).","
					. " ".quote_smart($year).","
					. " ".quote_smart($updBy).", NOW(),"
					. " ".quote_smart($updBy).", NOW()"
					. " )";
			//echo $sqlIns;
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$date_id = $db->sql_nextid();
			/*********/
			$msg = ($resIns) ? "Insert Successfully" : "Insert Unsuccessfully";
			/*********/
		}
		/*********/
		func_add_audittrail($updBy,$msg." [".$date_id."]","Admin-Submission Date","sys_submitdate,".$date_id);
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			</script>
			';
	}
	/*********/
	$year = isset($year) ? $year : date('Y');
	$date_start = "";
	$date_end = "";
	/*********/
	$dataColumns = "Date_Start, Date_Last";
	$dataTable = "sys_submitdate";
	$dataWhere = array("AND Submit_Year"=>" = ". quote_smart($year));
	$arrayData = _get_arrayData($dataColumns,$dataTable,"",$dataWhere); 
	/*********/
	foreach($arrayData as $detail){
		$date_start = func_ymd2dmy($detail["Date_Start"]);
		$date_end = func_ymd2dmy($detail["Date_Last"]);
	}
	/*********/
?>
	<style>
	.error 	{ float: none; color: red; padding-left: .5em; }
	</style>
	<script language="Javascript">
	$(document).ready(function() {
		
		$().UItoTop({ easingType: "easeOutQuart" });
		/* $("button").removeClass("btn-new-style").button(); */
		
		$('#save').on('click', function(){
			$('#year, #date_start, #date_end').closest('td').find('.error').remove();
			if($('#year').val()=='')
				$('#year').closest('td').append('<span class="error danger">This is required field</span>');
			
			if($('#date_start').val()=='')
				$('#date_start').closest('td').append('<span class="error danger">This is required field</span>');
			
			if($('#date_end').val()=='')
				$('#date_end').closest('td').append('<span class="error danger">This is required field</span>');
			
			if($('span.error').length>0)
				return false;
			else return true;
		});
		
		$('.datepicker').datepicker({
			format: 'dd-mm-yyyy',
			autoclose: true,
			todayHighlight: true,
			calendarWeeks: true,
			weekStart: 0
		});

		$('.dtPickerYear').datepicker( {
			format: 'yyyy',
			viewMode: 'years', 
			minViewMode: 'years',
			autoclose: true,
		}).on('changeDate', function(e) {
			// alert($('#year').val());
			$.ajax({
				url: 'admin-ajax.php',
				type: 'POST',
				dataType: 'json',
				data: { year: $('#year').val(), fn: 'submitdate' },
				success: function(data){
					$('#date_start').val(data.start);
					$('#date_end').val(data.end);
				}
			})
		});
	});

	</script>
	<?php func_window_open2(_LBL_SUBMISSION_SUBMIT_DATE); ?>
	<form name="myForm" action="" method="post">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_YEAR ?></td>
			<td><?php
				$param1 = "year";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$year;
				$param3 = array("type"=>"text", "class" => 'dtPickerYear text-center');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SUBMIT_START_DATE ?></td>
			<td><?php
				$param1 = "date_start";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$date_start;
				$param3 = array("type"=>"text",'class' => 'datepicker text-center');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SUBMIT_END_DATE ?></td>
			<td><?php
				$param1 = "date_end";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$date_end;
				$param3 = array("type"=>"text",'class' => 'datepicker text-center');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				$dOptions = array("type"=>"submit");
				echo form_button("save",_LBL_SAVE,$dOptions);
				echo "&nbsp;";
				$dOptions = array("type"=>"button","onClick"=>"redirectForm('submission_date.php');");
				echo form_button("cancel",_LBL_CANCEL,$dOptions);
			?></td>
		</tr>
	</table>
	<br />
	</form>