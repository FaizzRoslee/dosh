<?php
	//****************************************************************/
	// filename: approvalNewList.php
	// description: list all list of waiting for approval
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
	include_once $sys_config['includes_path'].'arrayCommon.php';
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
				$sys_config['includes_path']."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				$sys_config['includes_path']."jquery/DataTables/FixedHeader/FixedHeader.min.js,". // javascript
				$sys_config['includes_path']."jquery/Others/jquery.search_date.js,". // javascript
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
	$colTypeName = 'Type_Name'. (($fileLang=='may.php')?'_may':'');
	//==========================
	$dColumn = 's.Submission_Submit_ID,s.Submission_ID,s.Usr_ID,s.Type_ID,s.Status_ID,u.Level_ID';
	$dTable	= 'tbl_Submission s';
	//==========================
	$dJoin	= array(
				'sys_User u'=>'s.Usr_ID = u.Usr_ID',
				'sys_User_Staff us'=>'s.CheckedBy = us.Usr_ID'
			);
	//==========================
	$dWhere = '';
	if($levelID==5){ $dWhere = array("AND"=>"=0"); }
	else{
		$dWhere1 = array("AND s.Status_ID"=>"IN (22)");
		$dWhere2 = array();
		if($levelID==6 || $levelID==7 || $levelID==8)
			$dWhere2 = array("AND s.Usr_ID"=>"= ". quote_smart($Usr_ID));
		elseif($levelID==3)
			$dWhere2 = array("AND s.CheckedBy"=>"= ". quote_smart($Usr_ID));
		elseif($levelID==4)
			$dWhere2 = array("AND us.User_Head"=>"= ". quote_smart($Usr_ID));
			
		$dWhere = array_merge($dWhere1,$dWhere2);
	}
	//==========================
	$arrayData = _get_arrayData($dColumn,$dTable,$dJoin,$dWhere);
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
			"bPaginate": true,
			"bJQueryUI": true,
			"sPaginationType": "full_numbers",
 			"bProcessing": true,
			"aoColumns": [
				{ "bSortable": false, "bSearchable":false, "sClass": "center" },
				null,
				<?php if($levelID<5){ ?>
				null,
				null,
				<?php } ?>
				null,
				{ "sType": 'html-date' },
				null,
				{ "bSortable": false, "bSearchable":false }
			],
			"aaSorting": [[ 1, 'desc' ]],
			"bSortClasses": true
		});
		// new FixedHeader( oTable );
		
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
			if ( this.className == "search_init" ) {
				this.className = "";
				this.value = "";
			}
		} );
		
		$("tfoot input").blur( function (i) {
			if ( this.value == "" ) {
				this.className = "search_init";
				this.value = asInitVals[$("tfoot input").index(this)];
			}
		} );
	} );

	</script>
	<?php func_window_open2(_LBL_RENEW_SUBMISSION .' :: '. _LBL_APPROVAL); ?>
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
							<th><?= _LBL_STATUS ?></th>
							<th><?= _LBL_ACTIVITY ?></th>
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
							<th><?= form_input('search_status',_LBL_SEARCH .' '. _LBL_STATUS,$arrOthText); ?></th>
							<th><?= form_input('search_activity',_LBL_SEARCH .' '. _LBL_ACTIVITY,$arrOthHid); ?></th>
						</tr>
					</tfoot>

					<tbody>
					<?php
						foreach($arrayData as $detail){
							$uniqueID = $detail['Submission_ID'];
							$statusID = $detail['Status_ID'];
							//====================================================
							$activity = '';
							//====================================================
							$imgParam1 = $sys_config['images_path'].'icons/preview.gif';
							$imgParam2 = _LBL_VIEW;
							$imgParam3 = 'redirectForm(\'submissionView.php?submissionID='.$uniqueID.'&new=16\')';
							$activity .= _get_imagebutton($imgParam1,$imgParam2,$imgParam3);
							//=================================
							if($statusID==22 && ($levelID<=4&&$levelID!=3)){
								$imgParam1 = $sys_config['images_path'].'icons/verify.gif';
								$imgParam2 = _LBL_CHECKING;
								$imgParam3 = 'redirectForm(\'renewApprove.php?submissionID='.$uniqueID.'&new=16\')';
								$activity .= '&nbsp;'._get_imagebutton($imgParam1,$imgParam2,$imgParam3);
							}
							//====================================================
							$lvlName = '-';
							foreach($arrLevel as $level){
								if( $level['value'] == $detail['Level_ID'] ){ $lvlName = $level['label']; break; }
							}
							//====================================================
							$datesubmit	= _getSubmitDate($uniqueID);
							// $dateexp	= _getExpiredDate($uniqueID);
							//====================================================
							$chbx = '<input type="checkbox" name="checkbox[]" value="'.$uniqueID.'" disabled />';
							//====================================================
							$cName = _get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$detail['Usr_ID']);
							$tName = _get_StrFromCondition('tbl_Chemical_Type',$colTypeName,'Type_ID',$detail['Type_ID']);
							$sDesc = _get_StrFromCondition('tbl_Submission_Status','Status_Desc','Status_ID',$statusID);
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
									<td>'. $datesubmit . '</td>
									<td>'. (($sDesc!='') ? $sDesc : '-') .'</td>
									<td>'. $activity .'</td>
								</tr>
								';
							//====================================================
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<br />
	</body>
	</form>
	<?php func_window_close2(); ?>