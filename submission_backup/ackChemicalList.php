<?php
	//****************************************************************/
	// filename: approvedRenewList.php
	// description: list of the renew submission that has been approved 
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
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
	$levelID = $_SESSION["user"]["Level_ID"];
	/*********/
	if($levelID>2 && $levelID!=5) exit();
	/*********/
	
	
	
	
    // linuxhouse_20220623
    // start penambahbaikan - 11_carian senarai pembekal bahan kimia - v001
    // get chemical type value: 1=substance and 2=mixture
    
    if(isset($_POST["chemical_type"])) $chemical_type = $_POST["chemical_type"];
	elseif(isset($_GET["chemical_type"])) $chemical_type = $_GET["chemical_type"];
	else $chemical_type = "";// for -SELECT- or list all 'mixture' and 'substance' chemicals
	
	// end penambahbaikan - 11_carian senarai pembekal bahan kimia - v001
	
	
	
	
    /*********/
	func_header("",
				$sys_config["includes_path"]."jquery/DataTables/css/media.dataTable.css,". // css
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.css,". // css
				"", // css
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				// $sys_config["includes_path"]."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				// $sys_config["includes_path"]."jquery/DataTables/FixedHeader/FixedHeader.min.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/js/1.7.6/jquery.dataTables.min.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/FixedHeader-2.0.4/fixedHeader.dataTables.js,". // javascript
				$sys_config["includes_path"]."jquery/Others/jquery.search_date.js,". // javascript
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
?>
	<script type="text/javascript" charset="utf-8">
	var asInitVals = new Array();

// 	$(document).ready(function() {	
// 	
// 		var new_width = "";
// 		if( $(document).width() < 800 ) new_width = 800;
// 		else new_width = $(document).width() * 0.9;
// 		$("#dt_example, #container").css({ "width": new_width + "px", "margin": "5px auto", "padding":"0" });
// 		
// 		var oTable = $("#example").dataTable( {
// 			"bAutoWidth": false,
// 			// "sScrollY": "auto",
// 			"oLanguage": {    },
// 			"bScrollCollapse": true,
// 			"sPaginationType": "full_numbers",
// 			"bPaginate": true,
// 			"bJQueryUI": true,
//  			"bProcessing": true,
// 			"bServerSide": true,
// 			"sAjaxSource": "ackChemicalList_get.php?levelID=",
// 			"aoColumns": [
// 				{ "sName": " ", "bSortable": false, "bSearchable": false },
// 				{ "sName": "c.Chemical_CAS" },
// 				{ "sName": "c.Chemical_Name" },
// 				{ "sName": "c.Chemical_IUPAC" },
// 				{ "sName": " ", "bSortable": false, "bSearchable": false },
// 				{ "sName": " ", "bSortable": false, "bSearchable": false }
//  			],
// 			"aaSorting": [[ 1, "asc" ],[ 2, "asc" ]],
// 			"bSortClasses": true
// 		});
// 		new FixedHeader( oTable );
// 		
// 		$("tfoot input").keyup( function () {
// 			/* Filter on the column (the index) of this element */
// 			oTable.fnFilter( this.value, $("tfoot input").index(this) );
// 		}).each( function (i) {
// 			asInitVals[i] = this.value;
// 		}).focus( function () {
// 			if ( this.className == "search_init" ) {
// 				this.className = "";
// 				this.value = "";
// 			}
// 		}).blur( function (i) {
// 			if ( this.value == "" ) {
// 				this.className = "search_init";
// 				this.value = asInitVals[$("tfoot input").index(this)];
// 			}
// 		});
// 		
// 		$().UItoTop({ easingType: 'easeOutQuart' });
// 	} );
	
	
	
	
    // linuxhouse_20220623
    // start penambahbaikan - 11_carian senarai pembekal bahan kimia - v001
    // setting up datatable 
    
	$(document).ready(function() {	
	
		var new_width = "";
		if( $(document).width() < 800 ) new_width = 800;
		else new_width = $(document).width() * 0.9;
		$("#dt_example, #container").css({ "width": new_width + "px", "margin": "5px auto", "padding":"0" });
		
		
		// get selected chemical type option from dropdown
		// pass php string into javascript
		var selected_option = <?= json_encode($chemical_type) ?>;
		

		// for loading items
		var defaultConfig = {
			"bAutoWidth": false,
			// "sScrollY": "auto",
			"oLanguage": {  <?php include $sys_config["languages_path"]."dataTablesLanguage.php" ?>  },
			"bScrollCollapse": true,
			"sPaginationType": "full_numbers",
			"bPaginate": true,
			"bJQueryUI": true,
 			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "ackChemicalList_get.php?levelID=<?= $levelID ?>&Usr_ID=<?= $Usr_ID ?>",
			"aoColumns": [
				{ "sName": " ", "bSortable": false, "bSearchable": false },
				{ "sName": "c.Chemical_CAS" },
				{ "sName": "c.Chemical_Name" },
				{ "sName": "c.Chemical_IUPAC" },
				{ "sName": " ", "bSortable": false, "bSearchable": false },
				{ "sName": " ", "bSortable": false, "bSearchable": false }
 			],
			"aaSorting": [[ 1, "asc" ],[ 2, "asc" ]],
			"bSortClasses": true
		};
        
		// for search item: it produces only on result upon search
		var searchConfig = {
			"bAutoWidth": false,
			// "sScrollY": "auto",
			"oLanguage": {  <?php include $sys_config["languages_path"]."dataTablesLanguage.php" ?>  },
			"bScrollCollapse": true,
			"sPaginationType": "full_numbers",
			"bPaginate": true,
			"bDestroy": true,
			"bJQueryUI": true,
 			"bProcessing": true,
			"bServerSide": true,
            // "iDisplayLength": 1, // overrides sql limit here
			"sAjaxSource": "ackChemicalList_get.php?levelID=<?= $levelID ?>&Usr_ID=<?= $Usr_ID ?>",
			"aoColumns": [
				{ "sName": " ", "bSortable": false, "bSearchable": false },
				{ "sName": "c.Chemical_CAS" },
				{ "sName": "c.Chemical_Name" },
				{ "sName": "c.Chemical_IUPAC" },
				{ "sName": " ", "bSortable": false, "bSearchable": false },
				{ "sName": " ", "bSortable": false, "bSearchable": false }
 			],
			"aaSorting": [[ 1, "asc" ],[ 2, "asc" ]],
			"bSortClasses": true
		};
		
		// for chemical type search
        var chemicalTypeConfig = {
			"bAutoWidth": false,
			// "sScrollY": "auto",
			"oLanguage": {  <?php include $sys_config["languages_path"]."dataTablesLanguage.php" ?>  },
			"bScrollCollapse": true,
			"sPaginationType": "full_numbers",
			"bPaginate": true,
			"bDestroy": true,
			"bJQueryUI": true,
 			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "ackChemicalList_chemical_type_get.php?levelID=<?= $levelID ?>&Usr_ID=<?= $Usr_ID ?>&ChemicalType=<?= $chemical_type ?>",
			"aoColumns": [
				{ "sName": " ", "bSortable": false, "bSearchable": false },
				{ "sName": "c.Chemical_CAS" },
				{ "sName": "c.Chemical_Name" },
				{ "sName": "c.Chemical_IUPAC" },
				{ "sName": " ", "bSortable": false, "bSearchable": false },
				{ "sName": " ", "bSortable": false, "bSearchable": false }
 			],
			"aaSorting": [[ 1, "asc" ],[ 2, "asc" ]],
			"bSortClasses": true
        };
		
		
		// check selected option
        if(selected_option.length == 0)
		{
            var oTable = $("#example").dataTable(defaultConfig);
            new FixedHeader( oTable );
            
            // keyup takes inputs from users
            $("tfoot input").keyup( function () {
                /* Filter on the column (the index) of this element */	
                // destroy dataTable
                // $("#example").dataTable().fnDestroy();
                // calls seach configuration for searching
                // var oTable = $("#example").dataTable(searchConfig);
                // create new data table to display new data
                // new FixedHeader( oTable );
                // submits search value
                oTable.fnFilter( this.value, $("tfoot input").index(this) );
                
            }).each( function (i) {
                asInitVals[i] = this.value;
            }).focus( function () {
                if ( this.className == "search_init" ) {
                    this.className = "";
                    this.value = "";
                }
            }).blur( function (i) {
                if ( this.value == "" ) {
                    this.className = "search_init";
                    this.value = asInitVals[$("tfoot input").index(this)];
                }
            });
		}
        else
		{
            // destroy dataTable
            $("#example").dataTable().fnDestroy();
            
            var oTable = $("#example").dataTable(chemicalTypeConfig);
            new FixedHeader( oTable );
            
            // keyup takes inputs from users
            $("tfoot input").keyup( function () {
                /* Filter on the column (the index) of this element */	
                // destroy dataTable
                // $("#example").dataTable().fnDestroy();
                // calls seach configuration for searching
                // var oTable = $("#example").dataTable(searchConfig);
                // create new data table to display new data
                // new FixedHeader( oTable );
                // submits search value
                oTable.fnFilter( this.value, $("tfoot input").index(this) );
                
            }).each( function (i) {
                asInitVals[i] = this.value;
            }).focus( function () {
                if ( this.className == "search_init" ) {
                    this.className = "";
                    this.value = "";
                }
            }).blur( function (i) {
                if ( this.value == "" ) {
                    this.className = "search_init";
                    this.value = asInitVals[$("tfoot input").index(this)];
                }
            });
		}
		
		$().UItoTop({ easingType: 'easeOutQuart' });
	} );
	
	// end penambahbaikan - 11_carian senarai pembekal bahan kimia - v001
	
	
	

	</script>
	<?php func_window_open2(_LBL_SUBMISSION_ACKNOWLEDGE ." :: ". _LBL_LIST_CHEMICAL); ?>
	<form name="myForm" action="" method="post">
	<body id="dt_example">
		<div id="container">
			<div class="demo_jui">
			
			
			
			
                <!--linuxhouse_20220623-->
                <!--start penambahbaikan - 11_carian senarai pembekal bahan kimia - v001-->
                <!-- choose substance or chemical -->
				<table style="width:100%">
					<tr>
						<td width="40%"><?= _LBL_CHEMICAL_TYPE ?>: 
						<?php
							$param1 = "chemical_type";
							$param2 = "";
							$param3 = $arrChemicalType;
							$param4 = $chemical_type;
							$param5 = array("onChange"=>"document.myForm.submit();");
							echo form_select($param1,$param2,$param3,$param4,$param5);
						?>
						</td>
					</tr>
				</table>
				<br>
				<!--start penambahbaikan - 11_carian senarai pembekal bahan kimia - v001-->
			
			
			
			
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
					<thead>
						<tr>
							<th width="1%">&nbsp;<input type="checkbox" name="checkAll" id="checkAll" onClick="javascript: func_checkAll(this);" disabled /></th>
							<th width="12%"><?= _LBL_CAS_NO ?></th>
							<th width="31%"><?= _LBL_CHEMICAL_NAME ?></th>
							<th width="40%"><?= _LBL_CHEM_IUPAC ?></th>
							<th width="8%"><?= _LBL_CLASSIFIED ?></th>
							<th width="8%"><?= _LBL_ACTIVITY ?></th>
						</tr>
					</thead>
					
					<?php
						$arrOthHid	= array("type"=>"hidden","class"=>"search_init");
						$arrOthText = array("type"=>"text","class"=>"search_init");
					?>
					<tfoot>
						<tr>
							<th><?= form_input("search_check",_LBL_SEARCH,$arrOthHid); ?></th>
							<th><?= form_input("search_id",_LBL_SEARCH ." ". _LBL_CAS_NO,$arrOthText); ?></th>
							<th><?= form_input("search_name",_LBL_SEARCH ." ". _LBL_CHEMICAL_NAME,$arrOthText); ?></th>
							<th><?= form_input("search_stype",_LBL_SEARCH ." ". _LBL_CHEM_IUPAC,$arrOthText); ?></th>
							<th><?= form_input("search_classified",_LBL_SEARCH ." ". _LBL_CLASSIFIED,$arrOthHid); ?></th>
							<th><?= form_input("search_activity",_LBL_SEARCH ." ". _LBL_ACTIVITY,$arrOthHid); ?></th>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
		<br />
	</body>
	</form>
	<?php func_window_close2(); ?>
