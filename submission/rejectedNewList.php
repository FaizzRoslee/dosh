<?php
	//****************************************************************/
	// filename: rejectedNewList.php
	// description: list of the new submission that has been rejected 
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
	if($levelID==5) exit();
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
	/*********/
	$langdesc = (($fileLang=="may.php")?"_may":"");
	$colTypeName = "ct.Type_Name". $langdesc;
	/*********/
	$dSubmit = "(SELECT Sub_Record_Date FROM tbl_Submission_Record WHERE Submission_ID = s.Submission_ID AND Sub_Record_Status = 11 ORDER BY Sub_Record_Date DESC LIMIT 1)";
	$dReason = "(SELECT Sub_Record_Reason FROM tbl_Submission_Record WHERE Submission_ID = s.Submission_ID AND Sub_Record_Status = 2 ORDER BY Sub_Record_ID DESC LIMIT 1)";
	$dContact = "(SELECT Sub_Record_Contact FROM tbl_Submission_Record WHERE Submission_ID = s.Submission_ID AND Sub_Record_Status = 2 ORDER BY Sub_Record_ID DESC LIMIT 1)";
	/*********/
?>
	<script type="text/javascript" charset="utf-8">
	var asInitVals = new Array();
	$(document).ready(function() {
	
		var new_width = "";
		if( $(document).width() < 800 ) new_width = 800;
		else new_width = $(document).width() * 0.9;
		$("#dt_example, #container").css({ "width": new_width + "px", "margin": "5px auto", "padding":"0" });
		
		var oTable = $("#example").dataTable( {
			"bAutoWidth": false,
			// "sScrollY": "auto",
			"oLanguage": {  <?php include $sys_config["languages_path"]."dataTablesLanguage.php" ?>  },
			"bScrollCollapse": true,
			"sPaginationType": "full_numbers",
			"bPaginate": true,
			"bJQueryUI": true,
 			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "rejectedNewList_get.php?type=<?= $colTypeName ?>&levelID=<?= $levelID ?>&Usr_ID=<?= $Usr_ID ?>&langdesc=<?= $langdesc ?>",
			"aoColumns": [
				{ "sName": " ", "bSortable": false, "bSearchable": false },
				{ "sName": "s.Submission_Submit_ID" },
				<?php if($levelID<5){ ?>
				{ "sName": "uc.Company_Name" },
				{ "sName": "sl.Level_Name<?= $langdesc ?>" },
				<?php } ?>
				//{ "sName": "<?= $colTypeName ?>" },
				//{ "sName": "<?= $dSubmit ?>" },
				//{ "sName": "<?= $dReason ?>" },
				//{ "sName": "ss.Status_Desc<?= $langdesc ?>" },
				//{ "sName": "<?= $dContact ?>", "bVisible": false },
				null,
				null,
				null,
				null,
				null,
				{ "sName": "s.CheckedBy", "bSortable": false, "bSearchable":false, "bVisible": false },
				{ "sName": " ", "bSortable": false, "bSearchable":false }
 			],
			"aaSorting": [[ 1, "desc" ]],
			"bSortClasses": true
		});
		new FixedHeader( oTable );
		
		$("tfoot input").keyup( function () {
			/* Filter on the column (the index) of this element */
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
		
		$().UItoTop({ easingType: "easeOutQuart" });
	});

	</script>
	<?php func_window_open2(_LBL_SUBMISSION ." :: ". _LBL_REJECTED); ?>
	<form name="myForm" action="" method="post">
	<body id="dt_example">
		<div id="container">
			<div class="demo_jui">

				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
					<thead>
						<tr>
							<th width="1%">&nbsp;<input type="checkbox" name="checkAll" id="checkAll" onClick="javascript: func_checkAll(this);" disabled /></th>
							<th><?= _LBL_SUBMISSION_ID ?></th>
							<?php if($levelID<5){ ?>
							<th><?= _LBL_COMPANY_NAME ?></th>
							<th><?= _LBL_SUPPLIER_TYPE ?></th>
							<?php } ?>
							<th><?= _LBL_CHEMICAL_TYPE ?></th>
							<th><?= _LBL_SUBMISSION_DATE ?></th>
							<th><?= _LBL_REASON ?></th>
							<th><?= _LBL_STATUS ?></th>
							<th><?= "Contact" ?></th>
							<th><?= "CheckedBy" ?></th>
							<th><?= _LBL_ACTIVITY ?></th>
						</tr>
					</thead>
					
					<?php
						$arrOthHid	= array("type"=>"hidden","class"=>"search_init");
						$arrOthText = array("type"=>"text","class"=>"search_init");
					?>
					<tfoot>
						<tr>
							<th><?= form_input("search_check",_LBL_SEARCH,$arrOthHid); ?></th>
							<th><?= form_input("search_id",_LBL_SEARCH ." ". _LBL_SUBMISSION_ID,$arrOthText); ?></th>
							<?php if($levelID<5){ ?>
							<th><?= form_input("search_name",_LBL_SEARCH ." ". _LBL_COMPANY_NAME,$arrOthText); ?></th>
							<th><?= form_input("search_stype",_LBL_SEARCH ." ". _LBL_SUPPLIER_TYPE,$arrOthText); ?></th>
							<?php } ?>
							<th><?= form_input("search_ctype",_LBL_SEARCH ." ". _LBL_CHEMICAL_TYPE,$arrOthText); ?></th>
							<th><?= form_input("search_sdate",_LBL_SEARCH ." ". _LBL_SUBMISSION_DATE,$arrOthText); ?></th>
							<th><?= form_input("search_reason",_LBL_SEARCH ." ". _LBL_REASON,$arrOthText); ?></th>
							<th><?= form_input("search_status",_LBL_SEARCH ." ". _LBL_STATUS,$arrOthText); ?></th>
							<th><?= form_input("search_contact","",$arrOthHid); ?></th>
							<th><?= form_input("search_check","",$arrOthHid); ?></th>
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