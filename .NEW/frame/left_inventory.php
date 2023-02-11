<?php
	/****************************************************/
	// filename: menu_admin.php
	// 
	/****************************************************/
	require_once "../includes/sys_config.php";
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
	require_once $sys_config["includes_path"]."func_utilities.php"; //dlm ni ade func_left_menu
	require_once $sys_config["includes_path"]."func_header.php";
	require_once $sys_config["includes_path"]."func_footer.php";
	/*********/
	
	$mod_config["module_name"] = "inventory";
	
	if (isset($_GET["menu"]))
		$int_menu = $_GET["menu"];
	else $int_menu = 1;
		
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
							/*********/
							$title	=	_LBL_INVENTORY;
							$url	=	"";
							$menu	=	0;
							echo func_left_menu2($title,$url,$menu,$int_menu,1); // 1: is for header
							/*********/
							$title	=	_LBL_LIST_CHEMICAL;
							$url	=	$sys_config["inventory_path"] ."chemicalList.php";
							$menu	=	1;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							/*********/
							$title	=	_LBL_LIST_SYNONYM;
							$url	=	$sys_config["inventory_path"] ."synonymList.php";
							$menu	=	2;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							/*********/
							$title	=	_LBL_CLASSIFIED_CHEM;
							$url	=	$sys_config["inventory_path"] ."classifiedList.php";
							$menu	=	3;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							/*********/
							$title	=	_LBL_UNCLASSIFIED_CHEM;
							$url	=	$sys_config["inventory_path"] ."unclassifiedList.php";
							$menu	=	4;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							/*********/
							$title	=	_LBL_CWC_LIST;
							$url	=	$sys_config["inventory_path"] ."cwcList.php";
							$menu	=	6;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							/*********/
							$title	=	_LBL_NEW_CHEMICAL;
							$url	=	$sys_config["inventory_path"] ."newchemicalList.php";
							$menu	=	5;
							echo func_left_menu2($title,$url,$menu,$int_menu);
							/*********/
						?>
						</ul>
					</td></tr>
					<tr><td><img src="<?= $sys_config["images_path"]."design/spacer.gif"; ?>" width="205" height="20" /></td></tr>
					<tr><td><div class="style5">&copy; <?php echo $sys_config["organization_info"] ?></div></td></tr>
					</table>
<?php	 
	// page footer
	func_footer(
			false, // image footer
			false // frame
		);
?>