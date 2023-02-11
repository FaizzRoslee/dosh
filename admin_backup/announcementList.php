<?php
	//****************************************************************/
    // linuxhouse__20220701
	// filename: announcementList.php
	// description: list of the announcements
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
	/*********/
	func_header("",
				$sys_config['includes_path']."jquery/DataTables/css/media.dataTable.css,". // css
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.css,". // css
				"",
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				// $sys_config['includes_path']."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				// $sys_config['includes_path']."jquery/DataTables/FixedHeader/FixedHeader.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/js/1.7.6/jquery.dataTables.min.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/FixedHeader-2.0.4/fixedHeader.dataTables.js,". // javascript
				$sys_config['includes_path']."jquery/Others/jquery.search.js,". // javascript
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
				"",
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
	$userLevelID = $_SESSION['user']['Level_ID'];
	if($userLevelID>2) exit();
	//==========================================================
	if(isset($_POST['checkbox']) && (isset($_POST['active']) || isset($_POST['inactive']))){
		if(isset($_POST['active'])) $active = 1;
		else $active = 0;
		//==========================================================
		$countChk = count($_POST['checkbox']);
		$success = 0;
		$updBy = $_SESSION['user']['Usr_ID'];
		//==========================================================
		foreach($_POST['checkbox'] as $value){
			$sqlDel = "UPDATE tbl_Announcement SET"
					. " Active = ".quote_smart($active).","
					. " UpdateBy = ".quote_smart($updBy).","
					. " UpdatedAt = NOW()"
					. " WHERE Announcement_ID = ".quote_smart($value)." LIMIT 1"; //set active==0 instead delete from table;
			$resDel = $db->sql_query($sqlDel,END_TRANSACTION) or die(print_r($db->sql_error()));
			//==========================================================
			if($resDel){ $msg = 'Update Active/Inactive Successfully'; $success++; }
			else{ $msg = 'Update Active/Inactive Unsuccessfully'; }
			//==========================================================
			func_add_audittrail($updBy,$msg.' ['.$value.']','Admin-State','tbl_Announcement,'.$value);
		}
		if($countChk==$success){ $msg = 'Selected Record(s) Updated Successfully'; }
		else{ $msg = 'Selected Record(s) Updated Unsuccessfully'; }
		//==========================================================
		echo '
			<script languages="Javascript">
			alert("'.$msg.'");
			</script>
			';
	}
	//==========================================================
	if(isset($_POST['checkbox']) && isset($_POST['delete'])){
		//==========================================================
		$countChk = count($_POST['checkbox']);
		$success = 0;
		$updBy = $_SESSION['user']['Usr_ID'];
		//==========================================================
		foreach($_POST['checkbox'] as $value){
			$sqlDel = "UPDATE tbl_Announcement SET"
					. " Active = 0, isDeleted = 1,"
					. " UpdateBy = ".quote_smart($updBy).","
					. " UpdatedAt = NOW()"
					. " WHERE Announcement_ID = ".quote_smart($value)." LIMIT 1"; //set active==0 instead delete from table;
			$resDel = $db->sql_query($sqlDel,END_TRANSACTION) or die(print_r($db->sql_error()));
			//==========================================================
			if($resDel){ $msg = 'Update isDeleted Successfully'; $success++; }
			else{ $msg = 'Update isDeleted Unsuccessfully'; }
			//==========================================================
			func_add_audittrail($updBy,$msg.' ['.$value.']','Admin-State','tbl_Announcement,'.$value);
		}
		if($countChk==$success){ $msg = 'Selected Record(s) Deleted Successfully'; }
		else{ $msg = 'Selected Record(s) Deleted Unsuccessfully'; }
		//==========================================================
		echo '
			<script languages="Javascript">
			alert("'.$msg.'");
			</script>
			';
	}
	//==========================================================
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
			"sAjaxSource": "announcementList_get.php",
			"aoColumns": [
				{ "sName": " ", "bSortable": false},
				{ "sName": "Title" },
				{ "sName": "Description" },
				{ "sName": "StartingDate" },
				{ "sName": "EndingDate" },
				{ "sName": "Active" },
				{ "sName": " ", "bSortable": false }
 			],
			"aaSorting": [[ 1, 'asc' ]],
			"bSortClasses": true,
			"fnDrawCallback": function (oSettings) {
				$('[title]').tooltip();
			}
		} );
		new FixedHeader( oTable );
		
		$("tfoot input").keyup( function () {
			/* Filter on the column (the index) of this element */
			oTable.fnFilter( this.value, $("tfoot input").index(this) );
		} );
		
		/*
		 * Support functions to provide a little bit of 'user friendlyness' to the textboxes in 
		 * the footer
		 */
		$("tfoot input").each( function (i) {
			asInitVals[i] = this.value;
		} );
		
		$("tfoot input").focus( function () {
			if ( this.className == "search_init" )
			{
				this.className = "";
				this.value = "";
			}
		} );
		
		$("tfoot input").blur( function (i) {
			if ( this.value == "" )
			{
				this.className = "search_init";
				this.value = asInitVals[$("tfoot input").index(this)];
			}
		} );
		
		$().UItoTop({ easingType: 'easeOutQuart' });
		/* $("button").removeClass("btn-new-style").button(); */
	} );

	</script>
	<?php func_window_open2(_LBL_MESSAGE_LIST); ?>
	<form name="myForm" action="" method="post">
	<body id="dt_example">
		<div id="container">
			<div class="demo_jui">
				<table style="width:100%">
					<tr>
						<td width="60%">&nbsp;</td>
						<td width="40%" align="right"><?php
							//=======================================
							$param1 = 'add';
							$param2 = _LBL_ADD;
							$param3 = array('type'=>'button','onClick'=>'redirectForm(\'announcementListAddEdit.php\')');
							echo form_button($param1,$param2,$param3);
							//=======================================
							echo '&nbsp;';
							//=======================================
							$param1 = 'active';
							$param2 = _LBL_ACTIVE;
							$param3 = array('type'=>'submit');
							echo form_button($param1,$param2,$param3);
							//=======================================
							echo '&nbsp;';
							//=======================================
							$param1 = 'inactive';
							$param2 = _LBL_INACTIVE;
							$param3 = array('type'=>'submit');
							echo form_button($param1,$param2,$param3);
							//=======================================
							echo '&nbsp;';
							//=======================================
							$param1 = 'delete';
							$param2 = _LBL_DELETE;
							$param3 = array('type'=>'submit','onClick'=>'return confirm_msgbox();');
							echo form_button($param1,$param2,$param3);
							//=======================================
						?></td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
					<thead>
						<tr>
							<th width="4%" class="center">&nbsp;<input type="checkbox" name="checkAll" id="checkAll" onClick="javascript: func_checkAll(this);" /></th>
							<th width="30%"><?= _LBL_TITLE ?></th>
							<th width="36%"><?= _LBL_DESCRIPTION ?></th>
							<th width="12%"><?= _LBL_STARTING_DATE ?></th>
							<th width="12%"><?= _LBL_ENDING_DATE ?></th>
							<th width="8%"><?= _LBL_STATUS ?></th>
							<th width="10%"><?= _LBL_ACTIVITY ?></th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<th><input type="hidden" name="search_index" value="Search indexes" class="search_init" /></th>
							<th><input type="text" name="search_title" value="<?= _LBL_SEARCH .' '. _LBL_TITLE ?>" class="search_init" /></th>
							<th><input type="text" name="search_description" value="<?= _LBL_SEARCH .' '. _LBL_DESCRIPTION ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_date" value="<?= _LBL_SEARCH .' '. _LBL_STARTING_DATE ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_level" value="<?= _LBL_SEARCH .' '. _LBL_ENDING_DATE ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_level" value="<?= _LBL_SEARCH .' '. _LBL_STATUS ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_activity" value="<?= _LBL_SEARCH .' '. _LBL_ACTIVITY ?>" class="search_init" /></th>
						</tr>
					</tfoot>

					<tbody>
					<tr>
						<td colspan="5" class="dataTables_empty"><?= _LBL_LOADING_FROM_SERVER ?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</body>
	</form>
	<?php func_window_close2(); ?>
