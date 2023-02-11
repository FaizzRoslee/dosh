<?php
	//****************************************************************/
	// filename: reportSubmission.php
	// description: list of the submission reports
	//****************************************************************/
	include_once "../includes/sys_config.php";
	/*****/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*****/
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*****/
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_header.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*****/
	func_header("",
				$sys_config["includes_path"]."css/global.css,". // css	
				"", // css	
				// $sys_config["includes_path"]."jquery/js/jquery-1.3.2.min.js,". // javascript
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				"", // javascript
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				"", // str url frame top
				"", // str url frame left
				"", // str url frame content
				false // int top for [enter]
				);
	/*****/
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
	$userLevelID = $_SESSION["user"]["Level_ID"];
	/*****/
	// if(isset($_POST["rptType"])) $rptType = $_POST["rptType"];
	// elseif(isset($_GET["rptType"])) $rptType = $_GET["rptType"];
	// else $rptType = "";
	$rptType = "rpt03";
	/*****/
	if(isset($_POST["lvlType"])) $lvlType = $_POST["lvlType"];
	elseif(isset($_GET["lvlType"])) $lvlType = $_GET["lvlType"];
	else $lvlType = "";
	/*****/
	if(isset($_POST["chemType"])) $chemType = $_POST["chemType"];
	elseif(isset($_GET["chemType"])) $chemType = $_GET["chemType"];
	else $chemType = "";
	/*****/
	if(isset($_POST["status"])) $status = $_POST["status"];
	elseif(isset($_GET["status"])) $status = $_GET["status"];
	else $status = "";
	/*****/
	if(isset($_POST["rptYear"])) $rptYear = $_POST["rptYear"];
	elseif(isset($_GET["rptYear"])) $rptYear = $_GET["rptYear"];
	else $rptYear = date("Y");
	/*****/
	/*
	if(isset($_POST['send'])){
		if($userLevelID==5) $enforce = '_state';
		else $enforce = '';
		// if($rptType=='rpt04') $fileRpt = 'report04.php';
		// else 
		$fileRpt = 'report03'.$enforce.'.php';

		$selectTo = isset($_POST['selectTo'])?$_POST['selectTo']:array();
		$state = '';
		foreach($selectTo as $value){
			if($value!=''){
				if($state!='') $state .= ',';
				$state .= $value;
			}
		}

		$url = $fileRpt.'?rptType='.$rptType.'&lvlType='.$lvlType.'&chemType='.$chemType.'&status='.$status.'&rptYear='.$rptYear.'&state='.$state;
		echo '
			<script language="Javascript">
			funcPopup("'.$url.'","1100","800");
			</script>
			';
	}
	*/
	/*****/
?>
	<script language="Javascript">
	$(document).ready(function() {
		$('#choose').click(function() {
			return !$('#selectFrom option:selected').remove().appendTo('#selectTo');
		});
		$('#remove').click(function() {
			return !$('#selectTo option:selected').remove().appendTo('#selectFrom');
		});
		/* $("button").removeClass("btn-new-style").button(); */
		
		$("#send").click(function(){
			if( $("#lvlType").val()=="" || $("#chemType").val()=="" || $("#selectTo option:selected").length==0 || $("#rptYear").val()=="" ){
			
				var msg = "<?= _LBL_DO_NOT_LEAVE_EMPTY ?>";
				var i = 0;
				if( $("#lvlType").val()=="" ){
					i++;
					msg += "\n"+ i +". <?= _LBL_LEVEL_TYPE ?>";
				}
				if( $("#chemType").val()=="" ){
					i++;
					msg += "\n"+ i +". <?= _LBL_CHEMICAL_TYPE ?>";
				}
				if( ($("#selectTo").length>0 && $("#selectTo option:selected").length==0) || ($("#selectTo2").length>0 && $("#selectTo2").val()=="") ){
					i++;
					msg += "\n"+ i +". <?= _LBL_STATE ?>";
				}
				if( $("#rptYear").val()=="" ){
					i++;
					msg += "\n"+ i +". <?= _LBL_YEAR ?>";
				}
				
				if(i>0){
					alert(msg);
					return false;
				}
			}
			
			if( $("#selectTo").length>0 ){
				$(".selectAll option").each(function(i) {  
					$(this).attr("selected", "selected");  
				});
				var selectTo = $("#selectTo").val();
			}
			else if( $("#selectTo2").length>0 ){
				var selectTo = $("#selectTo2").val();
			}
			
			var rptFile = "report03"+ $("#enforce").val() +".php";
			var url 	= rptFile +"?rptType="+ $("#rptType").val() +"&lvlType="+ $("#lvlType").val() 
						+"&chemType="+ $("#chemType").val() +"&status="+ $("#status").val() 
						+"&rptYear="+ $("#rptYear").val() +"&state="+ selectTo;
						
			// alert( url );
			funcPopup( url,"1100","800");
		});
	});
	</script>
	<?php func_window_open2( _LBL_REPORT .' :: '._LBL_SUBMISSION); ?>
	<form name="myForm" action="" method="post">
	<?php
		if($userLevelID==5) $enforce = "_state";
		else $enforce = "";
		echo form_input("enforce",$enforce,array("type"=>"hidden"));
		echo form_input("rptType",$rptType,array("type"=>"hidden"));
	?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<?php
		/*
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REPORT_TYPE ?></td>
			<td width="80%"><?php
				$param1 = 'rptType';
				$param2 = '';
				$param3 = $arrReportSub;
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$rptType;
				$param5 = array('onChange'=>'document.myForm.submit();');
				echo form_select($param1,$param2,$param3,$param4,$param5);
				$rType = $param4;
			?></td>
		</tr>
		*/ 
		$rType = $rptType;
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_LEVEL_TYPE ?></td>
			<td width="80%"><?php
				$param1 = 'lvlType';
				$param2 = '';
				$param3 = $arrLevel; //tgk dlm arrayCommon.php
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$lvlType;
				$param5 = '';//array('onChange'=>'showHide();');
				echo form_select($param1,$param2,$param3,$param4,$param5);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEMICAL_TYPE ?></td>
			<td><?php
				$colTypeName = "Type_Name". (($fileLang=="may.php")?"_may":"");
				$param1 = "chemType";
				$param2 = "";
				$param3 = _get_arraySelect("tbl_Chemical_Type","Type_ID",$colTypeName,array("AND Active"=>"= 1"));
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$chemType;
				$param5 = "";//array("onChange"=>"document.myForm.submit();");
				echo form_select($param1,$param2,$param3,$param4,$param5);
			?></td>
		</tr>
		<tr class="contents" style="display:none;">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td><?php
				/*
				$param1 = "status";
				$param2 = "";
				if($rType=="rpt03") $param3 = $arrRptStatusN;
				elseif($rType=="rpt04") $param3 = $arrRptStatusR;
				else $param3 = array();
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$status;
				$param5 = ""; // array("onChange"=>"showHide();");
				echo form_select($param1,$param2,$param3,$param4,$param5);
				*/
				$dOptions = array("type"=>"hidden");
				echo _LBL_APPROVED . form_input("status",31,$dOptions);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE ?></td>
			<td><?php
				if($userLevelID!=5){
					/*****/
					$param6 = 'selectTo';
					$param7 = '';
					$param9 = isset($_POST[$param6])?$_POST[$param6]:array();
					/*****/
					$ids = '0';
					foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
					/*****/
					$param8 = _get_arraySelect('sys_State','State_ID','State_Name',array('AND isDeleted'=>'= 0','AND State_ID'=>'IN ('.$ids.')'));
					$param0 = array('style'=>'width:100%','size'=>'4','class'=>'selectAll');
					/*****/
					echo '<table width="100%"><tr><td width="45%">';
					/*****/
					$param1 = 'selectFrom';
					$param2 = '';
					$param3 = _get_arraySelect('sys_State','State_ID','State_Name',array('AND isDeleted'=>'= 0','AND State_ID'=>'NOT IN ('.$ids.')'));
					$param4 = isset($_POST[$param1])?$_POST[$param1]:array();
					$param5 = array('style'=>'width:100%','size'=>'4');
					echo form_multiselect($param1,$param2,$param3,$param4,$param5);
					/*****/
					echo '</td><td width="10%" align="center"><a href="#" id="choose">'. _LBL_ADD .'&gt;</a><br /><a href="#" id="remove">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
					/*****/
					echo form_multiselect($param6,$param7,$param8,$param9,$param0);
					/*****/
					echo '</td></tr></table>';
					/*****/
					$state = $ids;
				}else{
					$state = _get_StrFromCondition("sys_User_Staff","State_ID","Usr_ID",$Usr_ID);
					echo "<label>"._get_StrFromCondition("sys_State","State_Name","State_ID",$state)."</label>";
					echo '<input type="hidden" id="selectTo2" name="selectTo[]" value="'.$state.'">';
				}
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_YEAR ?></td>
			<td><?php
				/*****/
				$sql	= "SELECT Submit_Year AS year, Date_ID AS id FROM sys_SubmitDate"
						. " WHERE 1"
						. " GROUP BY year ORDER BY year DESC";
				$res	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				/*****/
				$arr_options = array();
				while($row = $db->sql_fetchrow($res)){
					$value 	= $row["year"];
					$id 	= $row["id"];
					// $arr_options[$value]["label"] = $value;
					// $arr_options[$value]["value"] = $id;
					$arr_options[] = array(
										"label" => $value-1,
										"value" => $value
									);
				}
				/*****/
				$param1 = "rptYear";
				$param2 = "";
				$param3 = $arr_options;
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$rptYear;
				$param5 = ""; //array("onChange"=>"document.myForm.submit();");
				echo form_select($param1,$param2,$param3,$param4,$param5);
				/*****/
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				//if($rptType=='rpt03' && $lvlType!='')
					$display = '';
				//else
					//$display = 'none';
				//=======================================
				$param1 = 'send';
				$param2 = _LBL_SUBMIT;
				$param3 = array('type'=>'button','style'=>'display:'.$display);
				echo form_button($param1,$param2,$param3);
				//=======================================
				echo '&nbsp;';
				//=======================================
				$param1 = 'back';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'reportSubmission.php\');');
				echo form_button($param1,$param2,$param3);
				//=======================================
			?></td>
		</tr>
	</table>
	<!--
	<br />
	<fieldset width="100%"><legend align="center"><?= _LBL_RESULT ?></legend>
	<iframe src="report01.php?rptType=<?= $rptType ?>&lvlType=<?= $lvlType ?>&rptYear=<?= $rptYear ?>&state=<?= $state ?>" width="100%" height="400px" />
	</fieldset>
	-->
	<br />
	</form>
	<?php func_window_close2(); ?>