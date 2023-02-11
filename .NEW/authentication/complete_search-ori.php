<?php 
	//****************************************************************/
	// filename: empList.php
	// description: list of the employee
	//****************************************************************/
	include_once '../includes/sys_config.php';
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
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	if(isset($_POST['searchBy'])) $searchBy = $_POST['searchBy'];
	elseif(isset($_GET['searchBy'])) $searchBy = $_GET['searchBy'];
	else $searchBy = 0;
	//==========================================================
	func_header(_LBL_SEARCH_REG_CHEM,
				$sys_config['includes_path']."jquery/DataTables/css/media.dataTable.css,". // css
				// $sys_config['includes_path']."jquery/Qtip/qtip.css,". // css
				$sys_config['includes_path']."css/global.css,". // css
				"", // css
				$sys_config['includes_path']."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				"", // javascript
				false, // menu
				false, // portlet
				true, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false // int top for [enter]
				);
	//==========================================================
	if(isset($_POST['searchWord'])) $searchWord = mysql_real_escape_string($_POST['searchWord']);
	elseif(isset($_GET['searchWord'])) $searchWord = mysql_real_escape_string($_GET['searchWord']);
	else $searchWord = '';
	
	if(isset($_POST['go'])){
		$searchWord = sanitizeOne($searchWord,"plain");
		// $searchWord = sanitizeOne($_GET['searchWord'],"plain");
		echo $searchWord;
	}
	//==========================================================
	if($searchWord!=''){
		$dataColumns = 'ch.*';
		$dataTable = 'tbl_Chemical ch';
		//$dataJoin = array('tbl_Chemical_Synonyms chS'=>'ch.Chemical_ID = chS.Chemical_ID');
		$dataJoin = '';
		$dataWhere = array(
					'AND (ch.Chemical_Name'=>"LIKE '%". quote_smart($searchWord,false) ."%'",
					'OR ch.Chemical_IUPAC'=>"LIKE '%". quote_smart($searchWord,false) ."%'",
					'OR ch.Chemical_CAS'=>"LIKE '%". quote_smart($searchWord,false) ."%')",
					'AND ch.isNew'=>"= 0",
					'AND ch.isActive'=>"= 1"
					); 
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
	}
?>
	<script type="text/javascript" charset="utf-8">
	function fnFormatDetails ( oTable, nTr ) {
		var aData = oTable.fnGetData( nTr );
		var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
		sOut += '<tr><td><strong><?= _LBL_SYNONYM ?></strong>:</td><td>'+aData[6]+'</td></tr>';
		sOut += '</table>';
		
		return sOut;
	}
	
	$(document).ready(function() {
	
		var nCloneTh = document.createElement( 'th' );
		var nCloneTd = document.createElement( 'td' );
		nCloneTd.innerHTML = '<img src="<?= $sys_config['includes_path'] ?>jquery/DataTables/images/details_open.png" title="<?= _LBL_SYNONYM ?>">';
		nCloneTd.className = "center";
		
		$('#example thead tr').each( function () {
			this.insertBefore( nCloneTh, this.childNodes[0] );
		} );
		
		$('#example tbody tr').each( function () {
			this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
		} );
		
		var oTable = $('#example').dataTable( {
			"oLanguage": {  <?php include $sys_config['languages_path']."dataTablesLanguage.php" ?>  },
			"sScrollY": "300px",
			// "bScrollCollapse": true,
			"bPaginate": false,
			"bJQueryUI": true,
 			"aoColumnDefs": [
				{ "aTargets": [ 4,5 ], "bSortable": false },
				{ "aTargets": [ 0 ], "bSortable": false, "bWidth":"10px" },
				{ "aTargets": [ 6 ], "bVisible": false, "bSortable": false, "bSearchable":false }
			],
 			"bSortClasses": true,
			"bFilter": false,
			"sDom": '<"H"i><rt><"F"i>'
		} );
		
		$('#example tbody td img').live('click', function () {
			var nTr = this.parentNode.parentNode;
			if ( this.src.match('details_close') )
			{
				/* This row is already open - close it */
				this.src = "<?= $sys_config['includes_path'] ?>jquery/DataTables/images/details_open.png";
				oTable.fnClose( nTr );
			}
			else
			{
				/* Open this row */
				this.src = "<?= $sys_config['includes_path'] ?>jquery/DataTables/images/details_close.png";
				oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
			}
		} );
		
		$('a[rel]').each(function() {
			$(this).qtip({
				content: { url: $(this).attr('rel') },
				position: { corner: { tooltip: 'leftMiddle', target: 'rightMiddle' } },
				style: { border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		
		$('input[title], img[title]').each(function() {
			$(this).qtip({
				position: { corner: { tooltip: 'bottomMiddle', target: 'topMiddle' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: { max: 350, min: 0 },
				},
			});
		});
		
		$("#searchWord").alphanumeric({ichars:'"><'});
		$("#go").click(function(){
			var searchWord = $("#searchWord").val();
			alert(searchWord);
			$("#searchWord").val().replace('"', '');
			searchWord.replace('&quot;', '');
			searchWord.replace('>', '');
			searchWord.replace('&gt;', '');
			searchWord.replace('<', '');
			searchWord.replace('&lt;', '');
			alert( $("#searchWord").val().replace('>', '') );
			$("#searchWord").val( searchWord );
			return false;
		});
	} );
	</script>
	<?php 
	/*
	<!--
	<script language="Javascript">
	function enableSearchWord(val, c){
		var e = document.myForm;
		e.searchWord.disabled = false;
		if(val==1){
			document.getElementById('titleCas').style.display = '';
			document.getElementById('info').style.display = 'none';
			document.getElementById('titleSynonym').style.display = 'none';
		}else if(val==2){
			e.searchWord.disabled = false;
			document.getElementById('info').style.display = 'none';
			document.getElementById('titleCas').style.display = 'none';
			document.getElementById('titleSynonym').style.display = '';
		}else{
			e.searchWord.disabled = true;
			document.getElementById('info').style.display = '';
			document.getElementById('titleCas').style.display = '';
			document.getElementById('titleSynonym').style.display = '';
		}
	}
	</script>
	-->
	*/
	?>
	<center>
	<?php func_window_open2(_LBL_SEARCH_REG_CHEM,'70%'); ?>
    <form name="myForm" id="myForm" action="" method="post">
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td width="20%" class="label" style="vertical-align:middle"><?= _LBL_SEARCH ?></td>
			<td width="80%"><?php
				//=======================================
				$param1 = 'searchWord';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:quote_smart($searchWord,false);
				$param3 = array('type'=>'text','style'=>'width:150px;','title'=>_LBL_CAN_BE_3);
				echo form_input($param1,$param2,$param3);
				//=======================================
				echo '&nbsp;';
				//=======================================
				$param1 = 'go';
				$param2 = _LBL_GO;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
				//=======================================
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
		<tr class="contents">
			<td align="center"><?php
				//=======================================
				$param1 = 'cancel';
				$param2 = _LBL_BACK;
				$param3 = array('type'=>'button','onClick'=>"location.href='".$sys_config['document_root']."index.php'");
				echo form_button($param1,$param2,$param3);
				//=======================================
			?></td>
		</tr>
	</table>
	<?php
		echo '$searchWord='. $searchWord;
		if($searchWord!=''){
	?>
	<br />
	<!--<body id="dt_example">-->
		<div id="container">
			<div class="demo_jui">
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
					<thead>
						<tr height="30">
							<!--<th width="4%"><?= _LBL_NUMBER ?></th>-->
							<th width="12%"><?= _LBL_CAS_NO ?></th>
							<th width="40%"><?= _LBL_TRADE_PRODUCT ?></th>			
							<th width="32%"><?= _LBL_CHEM_IUPAC ?></th>			
							<th width="8%"><?= _LBL_CLASSIFIED ?></th>			
							<th width="8%"><?= _LBL_VIEW ?></th>			
							<th><?= _LBL_SYNONYM ?></th>			
						</tr>
					</thead>

					<tbody>
					<?php
						$bil = 0;
						foreach($arrayData as $row){
							$bil++;
							$isClassified = _get_RowExist('tbl_Chemical_Classified',array('AND Chemical_ID'=>'= '.quote_smart($row['Chemical_ID']),'AND Active'=>'= 1'));
					?>
						<tr height="25">
							<!--<td class="center"><?= $bil ?></td>-->
							<td><?= (!empty($row['Chemical_CAS'])?$row['Chemical_CAS']:'-'); ?></td>
							<td><?= (!empty($row['Chemical_Name'])?nl2br(func_fn_escape($row['Chemical_Name'])):'-'); ?></td>
							<td><?= (!empty($row['Chemical_IUPAC'])?nl2br(func_fn_escape($row['Chemical_IUPAC'])):'-'); ?></td>
							<td><?= ($isClassified>0)?'<img src="'.$sys_config['images_path'].'icons/tick.gif" />':'&nbsp;'; ?></td>
							<td><a href="chemicalView_Search.php?chemicalID=<?= $row['Chemical_ID'] ?>&searchWord=<?= $searchWord ?>" ><?= strtoupper(_LBL_VIEW); ?></a></td>
							<td><?php
								$dataColumns2 	= 'Synonym_Name';
								$dataTable2 	= 'tbl_Chemical_Synonyms';
								$dataJoin2 		= '';
								$dataWhere2 	= array('AND Chemical_ID'=>'= '.quote_smart($row['Chemical_ID']));
								$arrayData2 = _get_arrayData($dataColumns2,$dataTable2,$dataJoin2,$dataWhere2);
								$synName = '';
								foreach($arrayData2 as $detail){
									if($detail['Synonym_Name']!=''){
										if($synName!='') $synName .= ', ';
										$synName .= '['.$detail['Synonym_Name'].']';
									}
								}
								if($synName=='') $synName = '-';
								echo $synName;
							?></td>
						</tr>
					<?php
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
	<!--</body>-->
	<?php
		}
	?>
    </form>
	<br />
	<?php func_window_close2(); ?>
	</center>