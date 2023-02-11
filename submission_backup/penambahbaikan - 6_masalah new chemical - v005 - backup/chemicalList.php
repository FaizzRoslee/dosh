<?php
	//****************************************************************/
	// filename: chemicalList.php
	// description: list of the chemical
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
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."func_header.php";	
	include_once $sys_config["includes_path"]."formElement.php";
	/*********/
	if(isset($_POST["i"])) $i = $_POST["i"];
	elseif(isset($_GET["i"])) $i = $_GET["i"];
	else $i = 1;
	/*********/
	if(isset($_POST["j"])) $j = $_POST["j"];
	elseif(isset($_GET["j"])) $j = $_GET["j"];
	else $j = 1;
	/*********/
	if(isset($_POST["c"])) $c = $_POST["c"];
	elseif(isset($_GET["c"])) $c = $_GET["c"];
	else $c = "";
	/*********/
	if(isset($_POST["search_name"])) $search_name = mysqli_real_escape_string($db->db_connect_id, $_POST["search_name"]);
	elseif(isset($_GET["search_name"])) $search_name = mysqli_real_escape_string($db->db_connect_id, $_GET["search_name"]);
	else $search_name = "";	
	/*********/
	if(isset($_POST["search_cas"])) $search_cas = mysqli_real_escape_string($db->db_connect_id, $_POST["search_cas"]);
	elseif(isset($_GET["search_cas"])) $search_cas = mysqli_real_escape_string($db->db_connect_id, $_GET["search_cas"]);
	else $search_cas = "";	
	/*********/
	func_header(_LBL_SEARCH_CHEMICAL,
				$sys_config["includes_path"]."jquery/DataTables/css/media.dataTable.css,". // css
				"", // css
				$sys_config["includes_path"]."javascript/js_validate.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				// $sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
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
	
	$where = array(
		"AND isDeleted"=>"= 0",
		"AND isActive"=>"= 1",
		"AND isNew"=>"= 0"
	);
	
	
	if(!empty($search_name)){
		$where = array_merge($where, array("AND Chemical_Name"=>"LIKE '%". $search_name ."%'"));
	}
	if(!empty($search_cas)){
		$where = array_merge($where, array("AND Chemical_CAS"=>"LIKE '%". $search_cas ."%'"));
	}
	
	$recordExist = _get_RowExist("tbl_Chemical", $where
						/* array(
							"AND (Chemical_Name"=>"LIKE '%". $search ."%'",
							"OR Chemical_IUPAC"=>"LIKE '%". $search ."%'",
							"OR Chemical_CAS"=>"LIKE '%". $search ."%')",
							"AND isDeleted"=>"= 0",
							"AND isActive"=>"= 1",
							"AND isNew"=>"= 0") */
						);
		//if($search!=''){ 
	?>
	<style>
	.dataTables_wrapper { min-height: 0; }
	</style>
	<script type="text/javascript" charset="utf-8">
	var asInitVals = new Array();
	$(document).ready(function() {
		var oTable = $('#example').dataTable( {
			"oLanguage": {  <?php include $sys_config['languages_path']."dataTablesLanguage.php" ?>  },
			"sScrollY": "300px",
			"bScrollCollapse": true,
			"bJQueryUI": true,
 			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "chemicalList_get.php?name=<?= $search_name ?>&cas=<?= $search_cas ?>&i=<?= $i?>&j=<?= $j?>",
			"aoColumns": [
				{ "sName": "Chemical_Name" },
				{ "sName": "Chemical_CAS" },
				//{ "sName": "(SELECT COUNT(*) FROM tbl_Chemical_Classified WHERE Chemical_ID = tbl_Chemical.Chemical_ID AND Active=1) AS Classified", "bSortable": true, "bSearchable": false },
				{ "sName": "test" },
				{ "sName": "Chemical_IUPAC", "bSortable": false, "bSearchable": false, "bVisible": false },
				{ "sName": " ", "bSortable": false, "bSearchable": false }
 			],
			"aaSorting": [[ 0, "asc" ]],
			"bSortClasses": true,
			"bPaginate": false,
			"sDom": '<"H"i><rt><"F">'
		} );
		
		$("#example").on("click",'.cls-choose', function(){			
			$("#chemicalID").val( $(this).closest("tr").find(".chemical-id").val() );
			$("#chemicalName").val( $(this).closest("tr").find(".chemical-name").val() );
			$("#casNo").val( $(this).closest("tr").find(".chemical-cas").val() );
			
			submitToParent( $(this).closest("tr").find(".chemical-i").val(), $(this).closest("tr").find(".chemical-j").val() );
		});
/*
		function submitToParent(i,j){
			
			var chemicalID = $("#chemicalID").val();
			var chemicalName = $("#chemicalName").val();
			var casNo = $("#casNo").val();
			
			window.opener.$("#chemicalID_" + i + "_" + j).val( chemicalID );
			window.opener.$("#chemicalName_" + i + "_" + j).val( chemicalName );

			if(casNo!='')
				window.opener.$("#casNo_" + i + "_" + j).val( casNo );
			else
				window.opener.$("#casNo_" + i + "_" + j).val("");
			
			window.opener.getHazardClass(i,chemicalID);
			window.close();
		}
*/
		/* $("button").removeClass("btn-new-style").button(); */
		
		$("#save").on('click', function(){
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: 'submission-ajax.php',
				data: {
					'fn': 'checkexist',
					'cas': $("#casNo").val(),
				},
				success: function(data){
					if(data.exist==0){						
						var r = confirm("<?= _LBL_CHEMICAL_SUBMITTED_SCREENED ?>");
						if (r == true) {
							submitToParent('<?= $i ?>','<?= $j ?>');
						}
					} else {
						var match_chemical = data.chemical_name + ' (' + data.chemical_cas + ')'; 
						var r = confirm("<?= 'System have found a match.\nIs these the Chemical you want to Key In:\n\n' ?>" + match_chemical);
						if (r == true) {	
							$("#chemicalID").val( data.chemical_id );
							$("#chemicalName").val( data.chemical_name );
							$("#casNo").val( data.chemical_cas );
							submitToParent('<?= $i ?>','<?= $j ?>');
						}
					}
				}
			});
		});
		
		if($.browser.msie && $.browser.version <= 9){
			func_placeholder();
		}
	});
// /*
	(function($) {
		submitToParent = function(i,j){
			
			var chemicalID = $("#chemicalID").val();
			var chemicalName = $("#chemicalName").val();
			var casNo = $("#casNo").val();
			
			window.opener.$("#chemicalID_" + i + "_" + j).val( chemicalID );
			window.opener.$("#chemicalName_" + i + "_" + j).val( chemicalName );

			if(casNo!='')
				window.opener.$("#casNo_" + i + "_" + j).val( casNo );
			else
				window.opener.$("#casNo_" + i + "_" + j).val("");
			
			window.opener.getHazardClass(i,chemicalID);
			window.close();
			
		}
		
		func_placeholder = function(){
			$('input[placeholder]').each(function () {
				var obj = $(this);

				if (obj.attr('placeholder') != '') {
					obj.addClass('IePlaceHolder');
					if ($.trim(obj.val()) == '') {
						obj.val(obj.attr('placeholder'));
					}
				}
			});

			$('.IePlaceHolder').on('focus', function () {
				var obj = $(this);
				if (obj.val() == obj.attr('placeholder')) {
					obj.val('');
				}
			});

			$('.IePlaceHolder').on('blur', function () {
				var obj = $(this);
				if ($.trim(obj.val()) == '') {
					obj.val(obj.attr('placeholder'));
				}
			});
			
			$('[placeholder]').closest('form').submit(function() {
				$(this).find('[placeholder]').each(function() {
					var input = $(this);
					if (input.val() == input.attr('placeholder')) {
						input.val('');
					}
				})
			});
		}
	})(jQuery);
// */

	function signedZero(obj){
		if(obj.value!=""){
			var newVal;
			if(obj.value.length==1) newVal = "0"+obj.value;
			else newVal  = obj.value;
			
			obj.value = newVal;
		}
	}
	</script>
	<center>
	<?php func_window_open2(_LBL_LIST_CHEMICAL,"90%"); ?>
	<form name="myForm" action="" method="post">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SEARCH ?></td>
			<td width="80%"><?php
				/*********/
				$param1 = "search_name";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$search_name;
				$param3 =  array("type"=>"text","title"=>_LBL_CHEMICAL_NAME, 'placeholder' => _LBL_CHEMICAL_NAME, 'size' => '40%');
				echo form_input($param1,$param2,$param3);
				/*********/
				echo "&nbsp;";
				echo _LBL_OR;
				echo "&nbsp;";
				/*********/
				$param1 = "search_cas";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$search_cas;
				$param3 =  array("type"=>"text","title"=>_LBL_CAS_NO, 'placeholder' => _LBL_CAS_NO, 'size' => '20%');
				echo form_input($param1,$param2,$param3);
				/*********/
				echo "&nbsp;";
				/*********/
				$param1 = "go";
				$param2 = _LBL_GO;
				$param3 = array("type"=>"submit");
				echo form_button($param1,$param2,$param3);
				/*********/
			?></td>
		</tr>
	</table>
	<br />
	<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
		<thead>
			<tr>
				<th><?= _LBL_CHEMICAL_NAME ?></th>
				<th width="15%"><?= _LBL_CAS_NO ?></th>
				<th width="8%"><?= _LBL_CLASSIFIED ?></th>
				<th><?= _LBL_CHEM_IUPAC ?></th>
				<th width="8%"><?= _LBL_ACTIVITY ?></th>
			</tr>
		</thead>
		<tbody>
		<tr>
			<td colspan="3" class="dataTables_empty"><?= _LBL_LOADING_FROM_SERVER ?></td>
		</tr>
		</tbody>
	</table>
	<div id="div_detail" style="display:<?= ($recordExist==0)?"":"none"; ?>">
	<?php
			$param1 = "chemicalID";
			$param2 = isset($_POST[$param1])?$_POST[$param1]:"";
			$param3 = array("type"=>"hidden");
			echo form_input($param1,$param2,$param3);
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td colspan="2" class="label"><?= _LBL_PLEASE_INSERT_CHEMICAL_MANUALLY ?></td>
		</tr>
		<tr class="contents">
			<td width="30%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td width="70%"><?php
				$param1 = "chemicalName";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:"";
				$param3 =  array("type"=>"text","style"=>"width:90%");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td><?php
				$param1 = "casNo";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:"";
				$param3 =  array("type"=>"input");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	</div>
	<?php //} ?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				/*********/
				$param1 = "save";
				$param2 = _LBL_SUBMIT;
				// $param3 = array("type"=>"button","onClick"=>"submitToParent('$i','$j')","style"=>"display:".(($recordExist==0)?"":"none"));
				$param3 = array("type"=>"button","style"=>"display:".(($recordExist==0)?"":"none"));
				echo form_button($param1,$param2,$param3);
				/*********/
				echo "&nbsp;";
				/*********/
				$param1 = "close";
				$param2 = _LBL_CANCEL;
				$param3 = array("type"=>"button","onClick"=>"window.close();");
				echo form_button($param1,$param2,$param3);
				/*********/
			?></td>
		</tr>
	</table>
	<br />
	</form>
	<?php func_window_close2(); ?>
	</center>
