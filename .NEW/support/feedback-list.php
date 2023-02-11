<?php
	//****************************************************************/
	// filename: allList.php
	// description: list all the submissions
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
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'formElement.php';
	//==========================================================
	$Usr_ID = $_SESSION['user']['Usr_ID'];
	$levelID = $_SESSION['user']['Level_ID'];
	if($levelID==5) exit();
	//==========================================================
	func_header("",
				$sys_config['includes_path']."jquery/DataTables/css/media.dataTable.css,". // css
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.css,". // css
				"", // css
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."javascript/js_validate.js,". // javascript
				$sys_config['includes_path']."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				$sys_config['includes_path']."jquery/DataTables/FixedHeader/FixedHeader.js,". // javascript
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
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
			"bAutoWidth": false,
			// "sScrollY": "auto",
			"oLanguage": {  <?php include $sys_config['languages_path']."dataTablesLanguage.php" ?>  },
			"bScrollCollapse": true,
			"sPaginationType": "full_numbers",
			"bPaginate": true,
			"bJQueryUI": true,
 			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "allList_get.php?type=<?= $colTypeName ?>&levelID=<?= $levelID ?>&Usr_ID=<?= $Usr_ID ?>",
			"aoColumns": [
				{ "sName": " ", "bSortable": false, "bSearchable": false },
				{ "sName": "s.Submission_Submit_ID" },
				<?php if($levelID<5){ ?>
				{ "sName": "uc.Company_Name" },
				{ "sName": "sl.Level_Name" },
				<?php } ?>
				{ "sName": "<?= $colTypeName ?>" },
				{ "sName": "<?= $dSubmit ?>" },
				{ "sName": "ADDDATE(<?= $dSubmit ?>, INTERVAL 5 YEAR)" },
				{ "sName": "s.BulkSubmission" },
				{ "sName": "ss.Status_Desc" },
				{ "sName": "s.CheckedBy", "bSortable": false, "bSearchable":false, "bVisible": false },
				{ "sName": " ", "bSortable": false, "bSearchable":false }
 			],
			"aaSorting": [[ 1, 'desc' ]],
			"bSortClasses": true
		});
		// new FixedHeader( oTable );
		
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
		
		$().UItoTop({ easingType: 'easeOutQuart' });
	});

	</script>
	<?php func_window_open2(_LBL_ALL_SUBMISSION .' :: '. _LBL_LIST); ?>
	<form name="myForm" action="" method="post">
	<body id="dt_example">
		<div id="container">
			<div class="demo_jui">
				<?php // if($levelID==1){ ?>
				<table style="width:100%">
					<tr>
						<td width="60%">&nbsp;</td>
						<td width="40%" align="right"><?php
							if($levelID>=6 || $levelID<=2){
								echo form_button('add',_LBL_ADD . ' ' . _LBL_SUBMISSION ,array('type'=>'button','onClick'=>'redirectForm(\'submissionAddEdit.php?new=2\')'));
								echo '&nbsp;';
							}
							if($levelID==1){ echo form_button('delete',_LBL_DELETE,array('type'=>'submit')); }
						?></td>
					</tr>
				</table>
				<?php // } ?>
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
					<thead>
						<tr>
							<th><input type="checkbox" name="checkAll" id="checkAll" onClick="javascript: func_checkAll(this);" <?= (($levelID==1)?'':'disabled') ?> /></th>
							<th><?= _LBL_SUBMISSION_ID ?></th>
							<?php if($levelID<5){ ?>
							<th><?= _LBL_COMPANY_NAME ?></th>
							<th><?= _LBL_SUPPLIER_TYPE ?></th>
							<?php } ?>
							<th><?= _LBL_CHEMICAL_TYPE ?></th>
							<th><?= _LBL_SUBMISSION_DATE ?></th>
							<th><?= _LBL_EXPIRED_DATE ?></th>
							<th><?= _LBL_BULK_SUBMISSION ?></th>
							<th><?= _LBL_STATUS ?></th>
							<th><?= 'CheckedBy' ?></th>
							<th width="5%"><?= _LBL_ACTIVITY ?></th>
						</tr>
					</thead>
					
					<?php
						$arrOthHid	= array('type'=>'hidden','class'=>'search_init');
						$arrOthText = array('type'=>'text','class'=>'search_init');
					?>
					<tfoot>
						<tr>
							<th><?= form_input('search_check',_LBL_SEARCH,$arrOthHid); ?></th>
							<th><?= form_input('search_id',_LBL_SEARCH .' '. _LBL_SUBMISSION_ID,$arrOthText); ?></th>
							<?php if($levelID<5){ ?>
							<th><?= form_input('search_name',_LBL_SEARCH .' '. _LBL_COMPANY_NAME,$arrOthText); ?></th>
							<th><?= form_input('search_stype',_LBL_SEARCH .' '. _LBL_SUPPLIER_TYPE,$arrOthText); ?></th>
							<?php } ?>
							<th><?= form_input('search_ctype',_LBL_SEARCH .' '. _LBL_CHEMICAL_TYPE,$arrOthText); ?></th>
							<th><?= form_input('search_sdate',_LBL_SEARCH .' '. _LBL_SUBMISSION_DATE,$arrOthText); ?></th>
							<th><?= form_input('search_edate',_LBL_SEARCH .' '. _LBL_EXPIRED_DATE,$arrOthText); ?></th>
							<th><?= form_input('search_bulk',_LBL_SEARCH .' '. _LBL_BULK_SUBMISSION,$arrOthText); ?></th>
							<th><?= form_input('search_status',_LBL_SEARCH .' '. _LBL_STATUS,$arrOthText); ?></th>
							<th><?= form_input('search_check','',$arrOthHid); ?></th>
							<th><?= form_input('search_activity',_LBL_SEARCH .' '. _LBL_ACTIVITY,$arrOthHid); ?></th>
						</tr>
					</tfoot>
<!--
					<tbody>
					<?php
					/*
						foreach($arrayData as $detail){
							$uniqueID = $detail['Submission_ID'];
							$statusID = $detail['Status_ID'];
							//====================================================
							$activity = '';
							$imgParam1 = $sys_config['images_path'].'icons/preview.gif';
							$imgParam2 = _LBL_VIEW;
							$imgParam3 = 'redirectForm(\'submissionView.php?submissionID='.$uniqueID.'\')';
							$imgParam4 = array('class'=>'cl_image');
							$activity .= _get_imagebutton2($imgParam1,$imgParam2,$imgParam3,$imgParam4);
							//====================================================
							if(in_array($statusID,array(31,32,33,41,42,43))){
								$url_pdf = $sys_config['report_path'].'submissionApproved_pdf.php?submissionID='.$uniqueID;
								$imgParam1 = _LBL_CERTIFICATION;
								$imgParam2 = 'funcPopup(\''.$url_pdf.'\',1000,600)';
								$imgParam3 = array('class'=>'cl_image');
								$activity .= '&nbsp;'._get_imagedoc($imgParam1,$imgParam2,$imgParam3);
							}
							//====================================================
							$lvlName = '-';
							foreach($arrLevel as $level){
								if( $level['value'] == $detail['Level_ID'] ){ $lvlName = $level['label']; break; }
							}
							//====================================================
							$datesubmit	= _getSubmitDate($uniqueID);
							$dateexp	= _getExpiredDate($uniqueID);
							//====================================================
							if($statusID==61||$statusID==62)
								$chbx = '&nbsp;';
							else
								$chbx = '<input type="checkbox" name="checkbox[]" value="'.$uniqueID.'" '. (($levelID==1)?'':'disabled') .' />';
							//====================================================
							$cName = _get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$detail['Usr_ID']);
							$tName = _get_StrFromCondition('tbl_Chemical_Type',$colTypeName,'Type_ID',$detail['Type_ID']);
							$sDesc = _get_StrFromCondition('tbl_Submission_Status','Status_Desc','Status_ID',$statusID);
							$sType = _get_StrFromCondition('tbl_Submission_Status','Status_Type','Status_ID',$statusID);
							//====================================================
							echo '
								<tr>
									<td>'. $chbx .'</td>
									<td>'. (!empty($detail['Submission_Submit_ID'])?$detail['Submission_Submit_ID']:'-') .'</td>
								';
							if($levelID<5){
								echo '	<td>'. (($cName!='') ? $cName : '-') .'</td>';
								echo '	<td>'. $lvlName .'</td>';
							}
							echo '	
									<td>'. (($tName!='') ? $tName : '-') .'</td>
									<td>'. (($datesubmit!='') ? $datesubmit : '-') . '</td>
									<td>'. (($dateexp!='') ? $dateexp : '-') .'</td>
									<td>'. (($sDesc!='') ? $sDesc.'<br />['. $sType .']' : '-') .'</td>
									<td>'. $activity .'</td>
								</tr>
								';
							//====================================================
						}
						*/
					?>
					</tbody>
-->
				</table>
			</div>
		</div>
		<br />
	</body>
	</form>
	<?php func_window_close2(); ?>