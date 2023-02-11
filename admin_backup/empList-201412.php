<?php
	//****************************************************************/
	// filename: empList.php
	// description: list of the employee
	//***************************************************************/
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
	/*********/
	func_header("",
				$sys_config["includes_path"]."jquery/DataTables/css/media.dataTable.css,". // css
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.css,". // css
				"", // css
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/FixedHeader/FixedHeader.js,". // javascript
				$sys_config["includes_path"]."jquery/Others/jquery.search.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.js,". // javascript
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
	if(isset($_POST["status"])) $status = $_POST["status"];
	elseif(isset($_GET["status"])) $status = $_GET["status"];
	else $status = 1;
	/*********/
	if(isset($_POST["checkbox"]) && (isset($_POST["active"]) || isset($_POST["inactive"]))){
		/*********/
		if(isset($_POST["active"])) $active = 1;
		else $active = 0;
		/*********/
		$countChk = count($_POST["checkbox"]);
		$success = 0;
		$updBy = $_SESSION["user"]["Usr_ID"];
		/*********/
		foreach($_POST["checkbox"] as $value){
			$sqlDel = "UPDATE sys_User SET"
					. " Active = ".quote_smart($active).","
					. " UpdateBy = ".quote_smart($updBy).","
					. " UpdateDate = NOW()"
					. " WHERE Usr_ID = ".quote_smart($value)." LIMIT 1"; //set active==0 instead delete from table;
			$resDel = $db->sql_query($sqlDel,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
			if($resDel){ $msg = "Update Active/Inactive Successfully"; $success++; }
			else{ $msg = "Update Active/Inactive Unsuccessfully"; }
			/*********/
			func_add_audittrail($updBy,$msg." [".$value."]","Admin-User","sys_User,".$value);//($recordBy="",$recordDesc="",$recordMod="",$recordOther="")
			/*********/
		}
		if($countChk==$success){ $msg = "Selected Record(s) Updated Successfully"; }
		else{ $msg = "Selected Record(s) Updated Unsuccessfully"; }
		/*********/
		echo '
			<script languages="Javascript">
			alert("'.$msg.'");
			</script>
			';
	}
	/*********/
	if(isset($_POST["checkbox"]) && isset($_POST["delete"])){
		/*********/
		$countChk = count($_POST["checkbox"]);
		$success = 0;
		$updBy = $_SESSION["user"]["Usr_ID"];
		/*********/
		foreach($_POST["checkbox"] as $value){
			$sqlDel = "UPDATE sys_User SET"
					. " Usr_LoginID = NULL,"
					. " Active = 0, isDeleted = 1,"
					. " UpdateBy = ".quote_smart($updBy).","
					. " UpdateDate = NOW()"
					. " WHERE Usr_ID = ".quote_smart($value)." LIMIT 1"; //set active==0 instead delete from table;
			$resDel = $db->sql_query($sqlDel,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
			if($resDel){ $msg = "Update isDeleted Successfully"; $success++; }
			else{ $msg = "Update isDeleted Unsuccessfully"; }
			/*********/
			func_add_audittrail($updBy,$msg." [".$value."]","Admin-User","sys_User,".$value);
		}
		if($countChk==$success){ $msg = "Selected Record(s) Deleted Successfully"; }
		else{ $msg = "Selected Record(s) Deleted Unsuccessfully"; }
		/*********/
		echo '
			<script languages="Javascript">
			alert("'.$msg.'");
			</script>
			';
	}
	/*********/
	if(isset($_POST["checkbox"]) && isset($_POST["reset"])){
		/*********/
		$countChk = count($_POST["checkbox"]);
		$success = 0;
		$updBy = $_SESSION["user"]["Usr_ID"];
		$defaultPswd = "password";
		/*********/
		foreach($_POST["checkbox"] as $value){
			$sqlDel = "UPDATE sys_User SET"
					// . " Usr_Password = ".quote_smart(md5($defaultPswd)).","
					. " Usr_Password = md5(Usr_LoginID),"
					. " UpdateBy = ".quote_smart($updBy).","
					. " UpdateDate = NOW()"
					. " WHERE Usr_ID = ".quote_smart($value)." LIMIT 1";
			$resDel = $db->sql_query($sqlDel,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
			if($resDel){ $msg = "Update Reset Password Successfully"; $success++; }
			else{ $msg = "Update Reset Password Unsuccessfully"; }
			/*********/
			func_add_audittrail($updBy,$msg." [".$value."]","Admin-User","sys_User,".$value);
		}
		if($countChk==$success){ $msg = "Selected Record(s) Update Successfully\\n"; }
		else{ $msg = "Selected Record(s) Update Unsuccessfully\\n"; }
		// $msg .= "Default password: ".$defaultPswd;
		$msg .= "Default password is user Login ID";
		/*********/
		echo '
			<script languages="Javascript">
			alert("'.$msg.'");
			</script>
			';
	}
	/*********/
?>
	<script type="text/javascript" charset="utf-8">
	var asInitVals = new Array();
	$(document).ready(function() {
	
		var new_width = '';
		if( $(document).width() < 800 ) new_width = 800;
		else new_width = $(document).width() * 0.9;
		$("#dt_example, #container").css({ 'width': new_width + 'px', 'margin': '5px auto', 'padding':'0' });
		
		var oTable = $('#example').dataTable( {
			"oLanguage": {  <?php include $sys_config['languages_path']."dataTablesLanguage.php" ?>  },
			// "sScrollY": "auto",
			"bScrollCollapse": false,
			"bPaginate": true,
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
 			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "empList_get.php?status=<?= $status ?>",
			"aoColumns": [
				{ "sName": " ", "bSortable": false},
				{ "sName": "us.User_StaffID" },
				{ "sName": "us.User_Fullname" },
				{ "sName": "us.User_Designation" },
				{ "sName": "l.Level_Name" },
				{ "sName": "u.Active" },
				{ "sName": " ", "bSortable": false }
 			],
			"aaSorting": [[ 1, 'asc' ]],
			"bSortClasses": true
		});
		new FixedHeader( oTable );
		
		$("tfoot input")
			.keyup(function(){
				/* Filter on the column (the index) of this element */
				oTable.fnFilter( this.value, $("tfoot input").index(this) );
			})
			.each(function(i){
				asInitVals[i] = this.value;
			})
			.focus(function(){
				if ( this.className == "search_init" ){
					this.className = "";
					this.value = "";
				}
			})
			.blur(function(i){
				if( this.value == "" ){
					this.className = "search_init";
					this.value = asInitVals[$("tfoot input").index(this)];
				}
			});
		
		$().UItoTop({ easingType: "easeOutQuart" });
		$("button").removeClass("btn-new-style").button();
	} );

	</script>
	<?php func_window_open2(_LBL_STAFF_LIST); ?>
	<form name="myForm" action="" method="post">
	<body id="dt_example">
		<div id="container">
			<div class="demo_jui">
				<table style="width:100%">
					<tr>
						<td width="40%"><?= _LBL_STATUS ?>: <?php
							$param1 = "status";
							$param2 = "";
							$param3 = $arrStatusAktif;
							$param4 = $status;
							$param5 = array("onChange"=>"document.myForm.submit();");
							echo form_select($param1,$param2,$param3,$param4,$param5);
						?></td>
						<td width="60%" align="right"><?php
							/*********/
							$dOthers = array("type"=>"button","onClick"=>"redirectForm('empAddEdit.php')");
							echo form_button("add",_LBL_ADD,$dOthers);
							/*********/
							echo "&nbsp;";
							/*********/
							if($status==1){
								$param1 = "inactive";
								$param2 = _LBL_INACTIVE;
							}else{
								$param1 = "active";
								$param2 = _LBL_ACTIVE;
							}
							$dOthers = array("type"=>"submit");
							echo form_button($param1,$param2,$dOthers);
							/*********/
							echo "&nbsp;";
							/*********/
							$dOthers = array("type"=>"submit");
							echo form_button("reset",_LBL_RESET_PASSWORD,$dOthers);
							/*********/
							echo "&nbsp;";
							/*********/
							$dOthers = array("type"=>"submit","onClick"=>"return confirm_msgbox();");
							echo form_button("delete",_LBL_DELETE,$dOthers);
							/*********/
						?></td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
					<thead>
						<tr>
							<th width="4%" class="center">&nbsp;<input type="checkbox" name="checkAll" id="checkAll" onClick="javascript: func_checkAll(this);" /></th>
							<th width="16%"><?= _LBL_STAFF_ID ?></th>
							<th width="25%"><?= _LBL_NAME ?></th>
							<th width="15%"><?= _LBL_DESIGNATION ?></th>
							<th width="15%"><?= _LBL_LEVEL ?></th>
							<th width="10%"><?= _LBL_STATUS ?></th>
							<th width="10%"><?= _LBL_ACTIVITY ?></th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<th><input type="hidden" name="search_index" value="Search indexes" class="search_init" /></th>
							<th><input type="text" name="search_staffid" value="<?= _LBL_SEARCH .' '. _LBL_STAFF_ID ?>" class="search_init" /></th>
							<th><input type="text" name="search_name" value="<?= _LBL_SEARCH .' '. _LBL_NAME ?>" class="search_init" /></th>
							<th><input type="text" name="search_design" value="<?= _LBL_SEARCH .' '. _LBL_DESIGNATION ?>" class="search_init" /></th>
							<th><input type="text" name="search_level" value="<?= _LBL_SEARCH .' '. _LBL_LEVEL ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_level" value="<?= _LBL_SEARCH .' '. _LBL_STATUS ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_activity" value="<?= _LBL_SEARCH .' '. _LBL_ACTIVITY ?>" class="search_init" /></th>
						</tr>
					</tfoot>

					<tbody>
					<tr>
						<td colspan="7" class="dataTables_empty"><?= _LBL_LOADING_FROM_SERVER ?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</body>
	</form>
	<?php func_window_close2(); ?>