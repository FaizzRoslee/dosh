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
?>
	<script language="Javascript">
	$(document).ready(function() {
		$("#choose").click(function() {
			$("#selectFrom option:selected").remove().appendTo("#selectTo");
			sortingOption("selectTo");
			fn_selectToAll();
		});
		$("#remove").click(function() {
			$("#selectTo option:selected").remove().appendTo("#selectFrom");
			sortingOption("selectFrom");
			fn_selectToAll();
		});
		$("#chooseall").click(function() {
			$("#selectFrom option").prop("selected",true);
			$("#selectFrom option:selected").remove().appendTo("#selectTo");
			sortingOption("selectTo");
			fn_selectToAll();
		});
		$("#removeall").click(function() {
			$("#selectTo option").prop("selected",true);
			$("#selectTo option:selected").remove().appendTo("#selectFrom");
			sortingOption("selectFrom");
			fn_selectToAll();
		});
		
		$("#lvlType").trigger("change");
		/* $("button").removeClass("btn-new-style").button(); */
		
		$("#send").live("click",function(){
			fn_selectToAll();
			
			
			// var all = "";
			// if( $("#lvlType").val()=="all" && $("#enforce").val()=="" ){
				// all = "_all";
			// }
			
			var rptFile = "report04.php";
			var url 	= rptFile +"?lvlType="+ $("#lvlType").val() 
						+"&state="+ $("#selectTo").val();
						
			// alert( url ); 
			funcPopup( url,"1100","800");
			return false;
			
		});
	});
	
	(function($){
		fn_selectToAll = function(){
			$(".selectAll option").prop("selected",true);
			// alert("fn_selectToAll");
			// $(".selectAll option").each(function() {  
				// $(this).attr("selected", "selected");  
			// });
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
			var lvlType = $("#lvlType").val();
			
			if( lvlType!="" ){
				$("#send").show();
			}else{
				$("#send").hide();
			}
		}
	})(jQuery);
	</script>
	<?php func_window_open2( _LBL_REPORT .' :: '._LBL_SUPLLIER); ?>
	<form name="myForm" action="" method="post">
	<?php
		if($userLevelID==5) $enforce = "_state";
		else $enforce = "";
		echo form_input("enforce",$enforce,array("type"=>"hidden"));
	?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_LEVEL_TYPE ?></td>
			<td width="80%"><?php
				$param1 = 'lvlType';
				$param2 = '';
				if(in_array($userLevelID,array(1,2,3,4,5))){
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
					echo '<table width="100%"><tr><td width="45%">';
					//======================
					$param1 = 'selectFrom';
					$param2 = '';
					$param3 = _get_arraySelect('sys_State','State_ID','State_Name',array('AND isDeleted'=>'= 0','AND State_ID'=>'NOT IN ('.$ids.')'));
					$param4 = isset($_POST[$param1])?$_POST[$param1]:array();
					$param5 = array('style'=>'width:100%','size'=>'4');
					echo form_multiselect($param1,$param2,$param3,$param4,$param5);
					//======================
					echo '</td><td width="20%" align="center"><a href="#" id="chooseall">'. _LBL_ADD_ALL .'&gt;&gt;</a><br /><a href="#" id="choose">'. _LBL_ADD .'&gt;</a><br /><a href="#" id="remove">&lt;'. _LBL_REMOVE .'</a><br /><a href="#" id="removeall">&lt;&lt;'. _LBL_REMOVE_ALL .'</a></td><td width="40%">';
					// echo '</td><td width="10%" align="center"><a href="#" id="choose">'. _LBL_ADD .'&gt;</a><br /><a href="#" id="remove">&lt;'. _LBL_REMOVE .'</a></td><td width="45%">';
					//======================
					echo form_multiselect($param6,$param7,$param8,$param9,$param0);
					//======================
					echo '</td></tr></table>';
					//======================
					$state = $ids;
				}
				else{
					$state = _get_StrFromCondition('sys_User_Staff','State_ID','Usr_ID',$Usr_ID);
					// echo '<label>'._get_StrFromCondition('sys_State','State_Name','State_ID',$state).'</label>';
					// echo '<input type="hidden" id="selectTo" name="selectTo[]" value="'.$state.'">';
					
					if($state==14 || $state==16)
						$state = "14,16";
					$ar_state = explode(",",$state);
					$strLabel = "";
					foreach($ar_state as $st){
						$strLabel .= _get_StrFromCondition('sys_State','State_Name','State_ID',$st) .", ";
					}
					$strInput = '<input type="hidden" id="selectTo" name="selectTo[]" value="'.$state.'" />';
					$strLabel = substr_replace( $strLabel, "", -2 );
					echo '<label>'. $strLabel .'</label>'. $strInput;
				}
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
				$param3 = array('type'=>'button');
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
	<br />
	</form>
	<?php func_window_close2(); ?>