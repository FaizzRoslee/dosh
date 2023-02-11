<?php
	//****************************************************************/
	// filename: renewList.php
	// description: list of the renew submission
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
				$sys_config['includes_path']."jquery/DataTables/FixedHeader/FixedHeader.js,". // javascript
				$sys_config['includes_path']."jquery/Others/jquery.search_date.js,". // javascript
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
	if(isset($_POST['delete']) && isset($_POST['checkbox'])){
		//===========================
		//print_r($_POST['checkbox']);
		$updBy = $Usr_ID;
		$arrCheck = count($_POST['checkbox']);
		$arrDelete = 0;
		//===========================
		foreach($_POST['checkbox'] as $value){
			$sqlDel = "UPDATE tbl_Submission SET"
					. " Status_ID = 62,"
					. " isDeleted = 1,"
					. " UpdateBy = ".quote_smart($updBy).","
					. " UpdateDate = NOW()"
					. " WHERE Submission_ID = ".quote_smart($value)." LIMIT 1"; //set isDeleted==1 instead delete from table;
			$resDel = $db->sql_query($sqlDel,END_TRANSACTION) or die(print_r($db->sql_error()));				
			if($resDel){ 
				$arrDelete++;
				$msg = 'Delete Submission Successfully';
				//==========================================================
				$sqlIns = "INSERT INTO tbl_Submission_Record ("
						. " Sub_Record_Desc,Sub_Record_Remark,Sub_Record_By,Sub_Record_Date,Sub_Record_Status,Submission_ID"
						. " ) VALUE ("
						. " ".quote_smart($msg).",'Delete Submission',".quote_smart($updBy).",NOW(),62,".quote_smart($value).""
						. " )";
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				//==========================================================
			}else{ $msg = 'Delete Submission Unsuccessfully'; }
			func_add_audittrail($updBy,$msg.' ['.$value.']','Submission-Delete','tbl_Submission,'.$value);//($recordBy='',$recordDesc='',$recordMod='',$recordOther='')
		}
		//==========================================================
		if($arrCheck==$arrDelete) $msg = 'Delete Submission(s) Successfully';
		else $msg = 'Delete Submission(s) Partially';
		//==========================================================
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			</script>
			';
		//==========================================================
	}
	//==========================================================
	$colTypeName = 'ct.Type_Name'. (($fileLang=='may.php')?'_may':'');
	//==========================================================
	$dSubmit = "(SELECT Sub_Record_Date FROM tbl_Submission_Record WHERE Submission_ID = s.Submission_ID AND Sub_Record_Status = 12 ORDER BY Sub_Record_Date DESC LIMIT 1)";
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
			"sAjaxSource": "renewList_get.php?type=<?= $colTypeName ?>&levelID=<?= $levelID ?>&Usr_ID=<?= $Usr_ID ?>",
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
				{ "sName": "ss.Status_Desc" },
				{ "sName": "s.CheckedBy", "bSortable": false, "bSearchable":false, "bVisible": false },
				{ "sName": " ", "bSortable": false, "bSearchable":false }
 			],
			"aaSorting": [[1,'desc']],
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
		
		$().UItoTop({ easingType: 'easeOutQuart' });
	} );
	</script>	
	<?php func_window_open2(_LBL_RENEW_SUBMISSION .' :: '. _LBL_LIST); ?>
	<form name="myForm" action="" method="post">
	<body id="dt_example">
		<div id="container">
			<div class="demo_jui">
				<table style="width:100%">
					<tr>
						<td width="60%">&nbsp;</td>
						<td width="40%" align="right">&nbsp;<?php
							if($levelID<=2 || $levelID>=6){
								echo form_button('add',_LBL_ADD,array('type'=>'button','onClick'=>'redirectForm(\'approvedList.php\')'));
								echo '&nbsp;';
								echo form_button('delete',_LBL_DELETE,array('type'=>'submit'));
							}
						?></td>
					</tr>
				</table>
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
					<thead>
						<tr>
							<th class="center">&nbsp;<input type="checkbox" name="checkAll" id="checkAll" onClick="javascript: func_checkAll(this);" <?= (($levelID>=6||$levelID==1)?'':'disabled'); ?> /></th>
							<th><?= _LBL_SUBMISSION_ID ?></th>
							<?php if($levelID<5){ ?>
							<th><?= _LBL_COMPANY_NAME ?></th>
							<th><?= _LBL_SUPPLIER_TYPE ?></th>
							<?php } ?>
							<th><?= _LBL_CHEMICAL_TYPE ?></th>
							<th><?= _LBL_SUBMISSION_DATE ?></th>
							<th><?= _LBL_EXPIRED_DATE ?></th>
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
							<th><?= form_input('search_status',_LBL_SEARCH .' '. _LBL_STATUS,$arrOthText); ?></th>
							<th><?= form_input('search_check','',$arrOthHid); ?></th>
							<th><?= form_input('search_activity',_LBL_SEARCH .' '. _LBL_ACTIVITY,$arrOthHid); ?></th>
						</tr>
					</tfoot>
<!--				
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
							$imgParam3 = 'redirectForm(\'submissionView.php?submissionID='.$uniqueID.'&new=11\')';
							$activity .= _get_imagebutton($imgParam1,$imgParam2,$imgParam3);
							//=================================
							if(($statusID==3 || $statusID==4) && ($levelID<=2 || $levelID>=6)){
								$imgParam1 = $sys_config['images_path'].'icons/edit.gif';
								$imgParam2 = _LBL_EDIT;
								$imgParam3 = 'redirectForm(\'renewAddEdit.php?submissionID='.$uniqueID.'\')';
								$activity .= '&nbsp;'._get_imagebutton($imgParam1,$imgParam2,$imgParam3);
							}
							//=================================
							if($statusID==12 && $levelID<=3){
								$imgParam1 = $sys_config['images_path'].'icons/check.gif';
								$imgParam2 = _LBL_CHECKING;
								$imgParam3 = 'redirectForm(\'renewChecking.php?submissionID='.$uniqueID.'\')';
								$activity .= '&nbsp;'._get_imagebutton($imgParam1,$imgParam2,$imgParam3);
							}
							//=================================
							$checkHead = _check_HeadForApproval($detail['CheckedBy']);
							if($statusID==22 && (($levelID==4 && $Usr_ID==$checkHead) || $levelID<=2)){
								$imgParam1 = $sys_config['images_path'].'icons/verify.gif';
								$imgParam2 = _LBL_APPROVAL;
								$imgParam3 = 'redirectForm(\'renewApprove.php?submissionID='.$uniqueID.'\')';
								$activity .= '&nbsp;'._get_imagebutton($imgParam1,$imgParam2,$imgParam3);
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
							if($levelID==1){
								if($statusID==61||$statusID==62)
									$chbx = '&nbsp;';
								else
									$chbx = '&nbsp;<input type="checkbox" name="checkbox[]" value="'.$uniqueID.'" />';
							}else
								$chbx = '<input type="checkbox" name="checkbox[]" value="'.$uniqueID.'" '. (($statusID==3||$statusID==4)?'':'disabled') .' />';
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
									<td>'. $dateexp .'</td>
									<td>'. (($sDesc!='') ? $sDesc : '-') .'</td>
									<td>'. $activity .'</td>
								</tr>
								';
							//====================================================
						}
					?>
					</tbody>
-->
				</table>
			</div>
		</div>
	</body>
	</form>
	<?php func_window_close2(); ?>