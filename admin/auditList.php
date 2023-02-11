<?php
	//****************************************************************/
	// filename: hazClassList.php [physical,health,environmental]
	// description: list of the hazard classification [physical,health,environmental]
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
				"",
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
	if($userLevelID>2) exit();
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
			//"bAutoWidth": false,
			// "sScrollY": "auto",
			"oLanguage": {  <?php include $sys_config["languages_path"]."dataTablesLanguage.php" ?>  },
			"bScrollCollapse": true,
			"sPaginationType": "full_numbers",
			"bPaginate": true,
			"bJQueryUI": true,
 			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "auditList_get.php",
			"aoColumns": [
				{ "sName": " ", "bSortable": false},
				{ "sName": "at.AuditTrail_Mod" },
				{ "sName": "at.AuditTrail_Desc" },
				{ "sName": " ", "bSortable": false, "sClass":"center" },
				{ "sName": "at.IP_Address", "sClass":"center" },
				{ "sName": "at.RecordDate", "bVisible": false  }
 			],
			"aaSorting": [[ 5, 'desc' ]],
			"bSortClasses": true
		} );
		new FixedHeader( oTable );
		
		$("tfoot input")
			.keyup(function(){
				/* Filter on the column (the index) of this element */
				oTable.fnFilter( this.value, $("tfoot input").index(this) );
			} )
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
				if(this.value == ""){
					this.className = "search_init";
					this.value = asInitVals[$("tfoot input").index(this)];
				}
			});
		
		$().UItoTop({ easingType: 'easeOutQuart' });
	} );

	</script>
	<?php func_window_open2(_LBL_AUDIT_TRAIL); ?>
	<form name="myForm" action="" method="post">
	<body id="dt_example">
		<div id="container">
			<div class="demo_jui">
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
					<thead>
						<tr>
							<th width="4%" class="center">&nbsp;<input type="checkbox" name="checkAll" id="checkAll" onClick="javascript: func_checkAll(this);" disabled /></th>
							<th width="20%"><?= _LBL_MODULE ?></th>
							<th width="30%"><?= _LBL_DESCRIPTION ?></th>
							<th width="21%"><?= _LBL_RECORD_BY_DATE ?></th>
							<th width="10%"><?= _LBL_IP_ADDRESS ?></th>
							<th width="15%"><?= _LBL_DATE ?></th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<th><input type="hidden" name="search_index" value="Search indexes" class="search_init" /></th>
							<th><input type="text" name="search_name" value="<?= _LBL_SEARCH .' '. _LBL_MODULE ?>" class="search_init" /></th>
							<th><input type="text" name="search_type" value="<?= _LBL_SEARCH .' '. _LBL_DESCRIPTION ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_level" value="<?= _LBL_SEARCH .' '. _LBL_RECORD_BY_DATE ?>" class="search_init" /></th>
							<th><input type="text" name="search_ip" value="<?= _LBL_SEARCH .' '. _LBL_IP_ADDRESS ?>" class="search_init" /></th>
							<th><input type="hidden" name="search_date" value="<?= _LBL_SEARCH .' '. _LBL_DATE ?>" class="search_init" /></th>
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
	<?php func_window_close2(); ?>