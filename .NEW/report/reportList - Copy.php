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
	if(isset($_POST["rptType"])) $rptType = $_POST["rptType"];
	elseif(isset($_GET["rptType"])) $rptType = $_GET["rptType"];
	else $rptType = "";
	/*******/
	if(isset($_POST["lvlType"])) $lvlType = $_POST["lvlType"];
	elseif(isset($_GET["lvlType"])) $lvlType = $_GET["lvlType"];
	else $lvlType = "";
	/*******/
	if(isset($_POST["chemType"])) $chemType = $_POST["chemType"];
	elseif(isset($_GET["chemType"])) $chemType = $_GET["chemType"];
	else $chemType = "";
	/*******/
	if(isset($_POST["comp"])) $comp = $_POST["comp"];
	elseif(isset($_GET["comp"])) $chemType = $_GET["comp"];
	else $comp = "";
	/*******/
	if(isset($_POST["rptYear"])) $rptYear = $_POST["rptYear"];
	elseif(isset($_GET["rptYear"])) $rptYear = $_GET["rptYear"];
	else $rptYear = date("Y");
	/*******/
	if(isset($_POST["send"])){
		$ex_param = "";
		if($userLevelID==5){ $enforce = "_state"; $ex_param = "&comp=".$comp; }
		else $enforce = "";
		if($rptType=="rpt02") $fileRpt = "report02.php";
		else $fileRpt = "report01".$enforce.".php";
	/*******/
		$selectTo = isset($_POST["selectTo"])?$_POST["selectTo"]:array();
		$state = "";
		foreach($selectTo as $value){
			if($value!=""){
				if($state!="") $state .= ",";
				$state .= $value;
			}
		}
	/*******/
		$url = $fileRpt.'?rptType='.$rptType.'&lvlType='.$lvlType.'&chemType='.$chemType.'&rptYear='.$rptYear.'&state='.$state.$ex_param;
		echo '
			<script language="Javascript">
			funcPopup("'.$url.'","1000","800");
			</script>
			';
	}
	/*******/
	if($rptType=="rpt02" && ( $lvlType!="" || $lvlType=="" ))
		$display = "";
	elseif($rptType=="rpt01" && $lvlType!="")
		$display = "";
	else
		$display = "none";
	/*******/
	if($rptType=="rpt02")
		$display2 = "none";
	else
		$display2 = "";
?>
	<script language="Javascript">
	$(document).ready(function() {
		$("#choose").click(function() {
			$("#selectFrom option:selected").remove().appendTo("#selectTo");
			sortingOption("selectTo");
			fn_selectToAll();
			reloadComp();
		});
		$("#remove").click(function() {
			$("#selectTo option:selected").remove().appendTo("#selectFrom");
			sortingOption("selectFrom");
			fn_selectToAll();
			reloadComp();
		});
		$("#chooseall").click(function(){
			$("#selectFrom option").prop("selected",true);
			$("#selectFrom option:selected").remove().appendTo("#selectTo");
			reloadComp();			
		});
		$("#removeall").click(function() {
			$("#selectTo option").prop("selected",true);
			$("#selectTo option:selected").remove().appendTo("#selectFrom");
			reloadComp();
		});
		
		$("#lvlType").change( function(){
			$("#comp").hide();
			fn_selectToAll();
			reloadComp();
		});
		$("#lvlType").trigger("change");
		$("button").removeClass("btn-new-style").button();
		
		$("#send").live("click",function(){
			fn_selectToAll();
			
			var all = "";
			if( $("#lvlType").val()=="all" ){
				all = "_all";
			}
			
			var rptFile = "";
			if( $("#rptType").val()=="rpt01" ) 
				rptFile = "report01"+ all + $("#enforce").val() +".php";
			else rptFile = "report02.php";
			
			var url 	= rptFile +"?rptType="+ $("#rptType").val() +"&lvlType="+ $("#lvlType").val() 
						+"&chemType="+ $("#chemType").val()
						+"&rptYear="+ $("#rptYear").val() +"&state="+ $("#selectTo").val() +"&comp="+ $("#comp").val();
						
			// alert( url );
			funcPopup( url,"1100","800");
			
		});
	});
	
	(function($){
		fn_selectToAll = function(){
			// alert("fn_selectToAll");
			$(".selectAll option").each(function() {  
				$(this).attr("selected", "selected");  
			});
		}

		reloadComp = function(){
			
			$.ajax({
				type: "POST",
				data: "type=" + $("#lvlType").val() + "&state=" + $("#selectTo").val() + "&comp=<?= $comp ?>",
				url: "check.php",
				success: function(msg){
					if (msg != ""){
						$("#comp").html(msg).show();
					}
				}
			});
		
		}
		
		sortingOption = function(id){
			var mylist = $("#"+id);
			var listitems = mylist.children("option").get();
			listitems.sort(function(a, b) {
			   var nameA = $(a).text().toUpperCase();
			   var nameB = $(b).text().toUpperCase();
			   return (nameA < nameB) ? -1 : (nameA > nameB) ? 1 : 0;
			});
			$.each(listitems, function(idx, itm) { mylist.append(itm); });
			return false;
		}
		
		showHide = function(){
			var rptType = $("#rptType").val();
			var lvlType = $("#lvlType").val();
			$("#tr_chemType, #tr_comp").hide();
			if(rptType=="rpt02" && ( lvlType=="" || lvlType!="" )){
				$("#send").show();
			}else if(rptType=="rpt01" && lvlType!="" ){
				$("#send").show();
			}else{
				$("#send").hide();
			}
			if(rptType=="rpt01"){
				$("#tr_chemType, #tr_comp").show();
			}
		}
	})(jQuery);
	</script>
	<?php
		// print_r($_SESSION);
	?>
	<?php func_window_open2( _LBL_REPORT .' :: '._LBL_LIST); ?>
	<form name="myForm" action="" method="post">
	<?php
		if($userLevelID==5) $enforce = "_state";
		else $enforce = "";
		echo form_input("enforce",$enforce,array("type"=>"hidden"));
	?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_REPORT_TYPE ?></td>
			<td width="80%"><?php
				$param1 = 'rptType';
				$param2 = '';
				$param3 = $arrReport;
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$rptType;
				$param5 = array('onChange'=>'showHide();');
				echo form_select($param1,$param2,$param3,$param4,$param5);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_LEVEL_TYPE ?></td>
			<td><?php
				$param1 = 'lvlType';
				$param2 = '';
				// $param3 = $arrLevel; //tgk dlm arrayCommon.php
				if(in_array($userLevelID,array(1,2,3))){
					$param3 = array_merge(array(array("value"=>"all","label"=>_LBL_ALL)),$arrLevel);
				}
				else{
					$param3 = $arrLevel;
				}
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$lvlType;
				$param5 = array('onChange'=>'showHide();');
				echo form_select($param1,$param2,$param3,$param4,$param5);
			?></td>
		</tr>
		<tr id="tr_chemType" style="display:<?= $display2 ?>" class="contents">
			<td class="label"><?= _LBL_CHEMICAL_TYPE ?></td>
			<td><?php
				$colTypeName = 'Type_Name'. (($fileLang=='may.php')?'_may':'');
				$param1 = 'chemType';
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Chemical_Type','Type_ID',$colTypeName,array('AND Active'=>'= 1'));
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$chemType;
				$param5 = '';
				echo form_select($param1,$param2,$param3,$param4,$param5);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATE ?></td>
			<td><?php
				if($userLevelID!=5){
					//======================
					$param6 = 'selectTo';
					$param7 = '';
					$param9 = isset($_POST[$param6])?$_POST[$param6]:array();
					//======================
					$ids = '0';
					foreach($param9 as $id){ if($id!=''){ $ids .= ','.$id; }}
					//======================
					$param8 = _get_arraySelect('sys_State','State_ID','State_Name',array('AND isDeleted'=>'= 0','AND State_ID'=>'IN ('.$ids.')'));
					$param0 = array('style'=>'width:100%','size'=>'4','class'=>'selectAll');
					//======================
					echo '<table width="100%"><tr><td width="40%">';
					//======================
					$param1 = 'selectFrom';
					$param2 = '';
					$param3 = _get_arraySelect('sys_State','State_ID','State_Name',array('AND isDeleted'=>'= 0','AND State_ID'=>'NOT IN ('.$ids.')'));
					$param4 = isset($_POST[$param1])?$_POST[$param1]:array();
					$param5 = array('style'=>'width:100%','size'=>'4');
					echo form_multiselect($param1,$param2,$param3,$param4,$param5);
					//======================
					echo '</td><td width="20%" align="center"><a href="#" id="chooseall">'. _LBL_ADD_ALL .'&gt;&gt;</a><br /><a href="#" id="choose">'. _LBL_ADD .'&gt;</a><br /><a href="#" id="remove">&lt;'. _LBL_REMOVE .'</a><br /><a href="#" id="removeall">&lt;&lt;'. _LBL_REMOVE_ALL .'</a></td><td width="40%">';
					//======================
					echo form_multiselect($param6,$param7,$param8,$param9,$param0);
					//======================
					echo '</td></tr></table>';
					//======================
					$state = $ids;
				}else{
					$state = _get_StrFromCondition('sys_User_Staff','State_ID','Usr_ID',$Usr_ID);
					echo '<label>'._get_StrFromCondition('sys_State','State_Name','State_ID',$state).'</label>';
					echo '<input type="hidden" id="selectTo" name="selectTo[]" value="'.$state.'">';
				}
			?></td>
		</tr>
		<tr id="tr_comp" style="display:<?= $display2 ?>" class="contents">
			<td class="label"><?= _LBL_COMPANY_NAME ?></td>
			<td><select id="comp" name="comp"><option value="0"><?= _LBL_SELECT ?></option></select></td>
		</td>
		<tr class="contents">
			<td class="label"><?= _LBL_YEAR ?></td>
			<td><?php
				/*****/
				$sql	= "SELECT YEAR(Date_Last) AS year, Date_ID AS id FROM sys_SubmitDate"
						. " WHERE 1"
						. " GROUP BY year ORDER BY year DESC";
				$res	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				/*****/
				$arr_options = array();
				while($row = $db->sql_fetchrow($res)){
					$value = $row['year'];
					$arr_options[$value]['label'] = $value-1;
					$arr_options[$value]['value'] = $value;
				}
				//========================================================================
				$param1 = 'rptYear';
				$param2 = '';
				$param3 = $arr_options;
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$rptYear;
				$param5 = ''; //array('onChange'=>'document.myForm.submit();');
				echo form_select($param1,$param2,$param3,$param4,$param5);
				//========================================================================
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
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
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'reportList.php\');');
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