<?php
	//****************************************************************/
	// filename: classifiedList.php
	// description: list of the classified chemical
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
				$sys_config["includes_path"]."jquery/DataTables/css/media.dataTable.css,". // css
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.css,". // css
				"", // css
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				// $sys_config["includes_path"]."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				// $sys_config["includes_path"]."jquery/DataTables/FixedHeader/FixedHeader.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/js/1.7.6/jquery.dataTables.min.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/FixedHeader-2.0.4/fixedHeader.dataTables.js,". // javascript
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
	if($userLevelID>=6) exit();
	/*********/
	if(isset($_POST["checkbox"])){
		if(isset($_POST["active"])) $active = 1;
		else $active = 0;
		/*********/
		$updBy = $_SESSION["user"]["Usr_ID"];
		/*********/
		foreach($_POST["checkbox"] as $value){
			$sqlDel = "UPDATE tbl_Hazard_Statement SET"
					. " Active = ". quote_smart($active) .","
					. " UpdateBy = ". quote_smart($updBy) .","
					. " UpdateDate = NOW()"
					. " WHERE Statement_ID = ". quote_smart($value) ." LIMIT 1"; //set active==0 instead delete from table;
			$resDel = $db->sql_query($sqlDel,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
			if($resDel) $msg = "Update Active/Inactive Successfully";
			else $msg = "Update Active/Inactive Unsuccessfully";
			/*********/
			func_add_audittrail($updBy,$msg." [".$value."]","Admin-Hazard Statement","tbl_Hazard_Statement".$value);
		}
	}
	/*********/
?>

	<script type="text/javascript" charset="utf-8">
	<!--
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
			"sAjaxSource": "classifiedList_get.php?userLevelID=<?= $userLevelID ?>",
			"aoColumns": [
				{ "sName": " ", "bSortable": false},
				{ "sName": "c.Chemical_CAS"},
				//{ "sName": "c.Chemical_CAS", "bSearchable": false, "bVisible": false},
				{ "sName": "c.Chemical_Name" },
				{ "sName": "cc.Lbl_Statement_ID", "bSortable": false },
				{ "sName": " ", "bSortable": false }
 			],
			"aaSorting": [[ 2, 'asc' ]],
			"bSortClasses": true,
			"fnDrawCallback": function (oSettings) {
				$('[title]').tooltip();
			}
		} );
		new FixedHeader( oTable );
		
		$("tfoot input").keyup( function () {
			/* Filter on the column (the index) of this element */
			oTable.fnFilter( this.value, $("tfoot input").index(this) );
		} ).each( function (i) {
			asInitVals[i] = this.value;
		} ).focus( function () {
			if ( this.className == "search_init" ) {
				this.className = "";
				this.value = "";
			}
		} ).blur( function (i) {
			if ( this.value == "" ) {
				this.className = "search_init";
				this.value = asInitVals[$("tfoot input").index(this)];
			}
		} );
		
		$().UItoTop({ easingType: "easeOutQuart" });
		/* $("button").removeClass("btn-new-style").button(); */
	} );
	-->
	</script>
	<?php func_window_open2(_LBL_CLASSIFIED_CHEM); ?>
	<form name="myForm" action="" method="post">
	<body id="dt_example">
		<div id="container">
			<div class="demo_jui">
				<?php 	if($userLevelID<=2){ ?>
				<table style="width:100%">
					<tr>
						<td width="60%">&nbsp;</td>
						<td width="40%" align="right"><?= form_button("add",_LBL_ADD,array("type"=>"button","onClick"=>"redirectForm('classifiedAddEdit.php')")); ?></td>
					</tr>
				</table>
				<?php 	}
						$disabled = ($userLevelID>2) ? "disabled" : "" ;
				?>
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
					<thead>
						<tr>
							<th width="4%" class="center">&nbsp;<input type="checkbox" name="checkAll" id="checkAll" disabled onClick="javascript: func_checkAll(this);" /></th>
							<th width="10%"><?= _LBL_CAS_NO ?></th>
							<th width="40%"><?= _LBL_CHEMICAL_NAME ?></th>
							<th width="36%"><?= _LBL_H_CODE_LABELLING ?></th>
							<th width="10%"><?= _LBL_ACTIVITY ?></th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<th><input type="hidden" name="search_index" value="Search indexes" class="search_init" /></th>
							<th><input type="text" name="search_name" value="<?= _LBL_SEARCH .' '. _LBL_CAS_NO ?>" class="search_init" /></th>
							<th><input type="text" name="search_iupac" value="<?= _LBL_SEARCH .' '. _LBL_CHEMICAL_NAME ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_cas" value="<?= _LBL_SEARCH .' '. _LBL_H_CODE_LABELLING ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_activity" value="<?= _LBL_SEARCH .' '. _LBL_ACTIVITY ?>" class="search_init" /></th>
						</tr>
					</tfoot>

					<tbody>
					<tr>
						<td colspan="6" class="dataTables_empty"><?= _LBL_LOADING_FROM_SERVER ?></td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</body>
	</form>
	<br />
	<?php func_window_close2(); ?>