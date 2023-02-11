<?php
	//****************************************************************/
	// filename: chemicalSearch.php
	// description: list of the chemical for adding the classified list
	//***************************************************************/
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
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'func_header.php';	
	include_once $sys_config['includes_path'].'formElement.php';
	//==========================================================
	if(isset($_POST['search'])) $search = mysqli_real_escape_string($db->db_connect_id, $_POST['search']);
	elseif(isset($_GET['search'])) $search = mysqli_real_escape_string($db->db_connect_id, $_GET['search']);
	else $search = '';	
	//==========================================================
	func_header(_LBL_SEARCH_CHEMICAL,
				$sys_config['includes_path']."jquery/DataTables/css/media.dataTable.css,". // css
				"", // css
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				$sys_config['includes_path']."jquery/Others/jquery.search.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				"",
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false, // int top for [enter]
				'' //onLoad
				);
	//==========================================================
	$userLevelID = $_SESSION['user']['Level_ID'];
	if($userLevelID>2) exit();
	//==========================================================
?>
	<script type="text/javascript" charset="utf-8">
	var asInitVals = new Array();
	$(document).ready(function() {
		var oTable = $('#example').dataTable( {
			"oLanguage": {  <?php include $sys_config['languages_path']."dataTablesLanguage.php" ?>  },
			"sScrollY": "220",
			"bScrollCollapse": true,
			"bJQueryUI": true,
 			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "chemicalSearch_get.php?search=<?= $search ?>",
			"aoColumns": [
				{ "sName": "Chemical_CAS" },
				{ "sName": "Chemical_Name" },
				{ "sName": "Chemical_IUPAC" },
				{ "sName": " ", "bSortable": false, "sClass": "center" },
 			],
			"aaSorting": [[ 1, 'asc' ]],
			"bSortClasses": true,
			"bPaginate": false,
			"bFilter": true,
			//"sDom": '<"top"i>rt<"bottom"><"clear">'
			"sDom": '<"H"i><rt><"F">',
		} );
		
		$('input[title]').each(function() {
			$(this).qtip({
				position: { corner: { tooltip: 'leftMiddle', target: 'rightMiddle' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: { max: 350, min: 0 },
				},
			});
		});
	} );
	</script>
	<script language="Javascript">
	function submitToParent(){
		var d = document;
		var odm = opener.document;
		
		var chemicalID = d.getElementById("chemicalID").value;
		var chemicalName = d.getElementById("chemicalName").value;
		var casNo = d.getElementById("casNo").value;
		
		odm.getElementById("chemicalID").value = chemicalID;
		odm.getElementById("chemName").value = chemicalName;
		odm.getElementById("chemCAS").value = casNo;
		
		window.close();
	}
	function selectChemical(id,name,cas) {
		var d = document;
		d.getElementById('chemicalID').value = id;
		d.getElementById('chemicalName').value = name;
		d.getElementById('casNo').value = cas;
		submitToParent();
	}
	</script>
	<center>
	<?php func_window_open2(_LBL_LIST_CHEMICAL,'90%'); ?>
	<form name="myForm" action="" method="post">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SEARCH ?></td>
			<td width="80%"><?php
				$param1 = 'search';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$search;
				$param3 =  array('type'=>'text','title'=>_LBL_CAN_BE_3);
				echo form_input($param1,$param2,$param3);
				echo '&nbsp;';
				$param1 = 'go';
				$param2 = _LBL_GO;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<?php //if($search!=''){ ?>
	<br />
	<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
		<thead>
			<tr>
				<th width="20%"><?= _LBL_CAS_NO ?></th>
				<th width="35%"><?= _LBL_CHEMICAL_NAME ?></th>
				<th width="35%"><?= _LBL_CHEM_IUPAC ?></th>
				<th width="10%"><?= _LBL_ACTIVITY ?></th>
			</tr>
		</thead>
		<tbody>
		<tr>
			<td colspan="4" class="dataTables_empty"><?= _LBL_LOADING_FROM_SERVER ?></td>
		</tr>
		</tbody>
	</table>
	<?php
		$param1 = 'chemicalID';
		$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
		$param3 =  array('type'=>'hidden');
		echo form_input($param1,$param2,$param3);
		//===================================================
		$param1 = 'chemicalName';
		$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
		$param3 =  array('type'=>'hidden');
		echo form_input($param1,$param2,$param3);
		//===================================================
		$param1 = 'casNo';
		$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
		$param3 =  array('type'=>'hidden');
		echo form_input($param1,$param2,$param3);
	?>
	<?php //} ?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				$param1 = 'close';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>"window.close();");
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	</form>
	<?php func_window_close2(); ?>
	</center>
