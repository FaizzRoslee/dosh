<?php
	/****************************************************/
	// filename: menu_admin.php
	// 
	/****************************************************/
	require_once '../includes/sys_config.php';
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
	require_once $sys_config['includes_path'].'func_utilities.php'; //dlm ni ade func_left_menu
	require_once $sys_config['includes_path'].'func_header.php';
	require_once $sys_config['includes_path'].'func_footer.php';
	require_once $sys_config['includes_path'].'func_master.php';
	//==========================================================
	
	$mod_config['module_name'] = 'admin';
	
	if (isset($_GET['menu']))
		$int_menu = $_GET['menu'];
	else
		$int_menu = 1;
		
	func_header("",
				$sys_config['includes_path']."css/css_left_menu.css", // css	
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
?>
	<script language="javascript">
	function redirect_to_content(url,selfUrl) {
		parent.right.location.href = url;
		window.location.href = selfUrl;
	}

	function func_refresh() {
		window.location.reload();
	}
	</script>

	<?php // extractArray($_SERVER) ?>
	<?php // extractArray($sys_config) ?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="1%" rowspan="2" valign="top"><img src="<?= $sys_config['images_path']."design/pq_18.gif"; ?>" width="11" height="11" alt="" /></td>
		<td width="99%" valign="top" background="<?= $sys_config['images_path']."design/pq_20.gif"; ?>"><img src="<?= $sys_config['images_path']."design/pq_20.gif"; ?>" width="10" height="11" alt="" /></td>
	</tr>
	<tr>
		<td valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="1%" height="234" valign="top" background="">				
				<table width="1%" border="0" cellspacing="0" cellpadding="0">
					<tr><td>
						<ul id="menu"><?php
							//===============================================
							$title	=	_LBL_USER_LIST;
							$url	=	"";
							$menu	=	0;
							echo func_left_menu2($title,$url,$menu,$int_menu,1); // 1: is for header
							//===============================================
							$title	=	_LBL_STAFF;
							$url	=	$sys_config['admin_path']."empList.php";
							$menu	=	1;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_SUPP_IMPORTER;
							$url	=	$sys_config['admin_path']."userList.php?levelID=6";
							$menu	=	2;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_INDUSTRIAL_MANUF;
							$url	=	$sys_config['admin_path']."userList.php?levelID=7";
							$menu	=	3;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_SUPP_MANUF;
							$url	=	$sys_config['admin_path']."userList.php?levelID=8";
							$menu	=	4;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_GENERAL;
							$url	=	"";
							$menu	=	0;
							echo func_left_menu2($title,$url,$menu,$int_menu,1); // 1: is for header
							//===============================================
							$title	=	_LBL_AUDIT_TRAIL;
							$url	=	$sys_config['admin_path']."auditList.php";
							$menu	=	20;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_SUBMISSION_SUBMIT_DATE;
							$url	=	$sys_config['admin_path']."submission_date.php";
							$menu	=	22;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_INFO_PAGE;
							$url	=	$sys_config['admin_path']."infopageList.php";
							$menu	=	21;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_STATE;
							$url	=	$sys_config['admin_path']."stateList.php";
							$menu	=	5;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_HAZARD_PICTOGRAM;
							$url	=	$sys_config['admin_path']."hazPictogramList.php";
							$menu	=	6;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_HAZARD_STATEMENT;
							$url	=	$sys_config['admin_path']."hazStatementList.php";
							$menu	=	7;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_SIGNAL_WORD;
							$url	=	$sys_config['admin_path']."hazSignalList.php";
							$menu	=	8;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_PRECAUTIONARY_STATEMENT;
							$url	=	$sys_config['admin_path']."precautionStatementList.php";
							$menu	=	9;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							$title	=	_LBL_HAZARD_CLASSIFICATION;
							$url	=	$sys_config['admin_path']."hazClassList.php";
							$menu	=	10;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================							
							$title	=	_LBL_HAZARD_CATEGORY;
							$url	=	$sys_config['admin_path']."hazCategoryList.php";
							$menu	=	11;
							echo func_left_menu2($title,$url,$menu,$int_menu);		
							//===============================================
							$title	=	_LBL_HAZARD_CHECKING;
							$url	=	$sys_config['admin_path']."hazCheckList.php";
							$menu	=	12;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							//===============================================
							
							
							
							// linuxhouse__20220701
                            // start penambahbaikan - 3_auto-blast notification- need more info from user - v005
                            // message menu for the admin
                            $title	=	_LBL_MESSAGE;
							$url	=	$sys_config['admin_path']."announcementList.php";
							$menu	=	13;
							echo func_left_menu2($title,$url,$menu,$int_menu);
                            // end penambahbaikan - 3_auto-blast notification- need more info from user - v005
						?>
						</ul>
					</td></tr>
					<tr><td><img src="<?= $sys_config['images_path']."design/spacer.gif"; ?>" width="205" height="20" /></td></tr>
					<tr><td><div class="style5">&copy; <?php echo $sys_config['organization_info'] ?></div></td></tr>
					</table>

<?php	 
	// page footer
	func_footer(
			false, // image footer
			false // frame
		);
?>
