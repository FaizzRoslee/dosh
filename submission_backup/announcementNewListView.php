 
<?php
	//****************************************************************/
    // linuxhouse__20220701
	// filename: announcementNewListView.php
	// description: add/edit announcement information
    //              copied from admin add/edit announcement 
	//****************************************************************/
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
	include_once $sys_config["includes_path"]."func_date.php";// used by func_ymd2dmy()
	include_once $sys_config["includes_path"]."func_make_url_to_link.php";// fine links in string
	//==========================================================
	func_header("",
				$sys_config['includes_path']."css/global.css,". // css	
				"",
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."javascript/js_validate.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				
				
				// existing datepicker
				$sys_config["includes_path"]."jquery/datepicker/css/datepicker.css,". // css
                $sys_config["includes_path"]."jquery/datepicker/js/bootstrap-datepicker.js,". // javascript
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
	$userLevelID = $_SESSION['user']['Level_ID'];
	$levelID = $_SESSION["user"]["Level_ID"];
	if($levelID==5) exit();
	//==========================================================
	if(isset($_POST['stateID'])) $stateID = $_POST['stateID'];
	elseif(isset($_GET['stateID'])) $stateID = $_GET['stateID'];
	else $stateID = 0;
	//==========================================================
	if($stateID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	//==========================================================
	

	// fetch data
	//==========================================================
	$Title = '';
	$Description = '';
	$active = 1;
	//==========================================================
	if($stateID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Announcement';
		$dataWhere = array("AND Announcement_ID" => " = ".quote_smart($stateID)."");
		$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere);
		//==========================================================
		foreach($arrayData as $detail){
			$Title = $detail['Title'];
			$Description = $detail['Description'];
		}
	}
	//==========================================================
?>
	

	<?php func_window_open2(_LBL_MESSAGE .' :: '. _LBL_LIST .' :: '. _LBL_VIEW); ?>
	<form name="myForm" action="" method="post" autocomplete="off">
	<?= form_input('stateID',$stateID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_TITLE ?></td>
			<td colspan="3"><label><?= !empty($Title) ? $Title : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_DESCRIPTION ?></td>
			<td colspan="3"><label><?= !empty($Description) ? func_make_url_to_link(nl2br($Description)) : '-'; ?></label></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				echo '&nbsp;';
				$param1 = 'cancel';
				$param2 = _LBL_BACK;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'announcementNewList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
