<?php
	/*********************************************************************************************
		File name		:		func_header.php
		Function name	:		func_header(str_title,str_css_filename,str_javascript_filename,bln_menu)
								func_add_css(str_css_filename)
								func_add_javascript(str_javascript_filename)
								func_system_menu()
		Parameter		:		str_title (string), str_css_filename (string),
								str_javascript_filename (string)
		Return			:		none
		Date created	:		21 July 2005
		Description		:		func_header function to print Page header
								(require /includes/sys_config.php)
								func_add_css function generate external css include string
								func_add_javascript function generate external javascript 
								include string
		Require			:		menu_user		(menu_user.php)
	*********************************************************************************************/
	require_once $sys_config['includes_path'].'menu_user.php';
	
	function func_header(
				$str_title = '',
				$str_css_filename = '',
				$str_javascript_filename = '', 
				$bln_menu = true,
				$bln_portlet = true,
				$bln_header = true,
				$bln_frame = false,
				$str_frame_top = '',
				$str_frame_left = '',
				$str_frame_content = '', 
				$int_top = 1, 
				$str_on_load = '', 
				$int_print = false
	) {		
		global $sys_config,$mod_config;

		// header string
		$str_header	= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 "
					. "Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\"> \n"
					. "<html> \n"
					. "<head> \n"
					. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"> \n"
					. "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\"> \n"
					. "<link rel=\"shortcut icon\" href=\"". $sys_config['images_path'] ."favicon.ico\">"
					. "<title>" . $sys_config['system_name'] . " version " . $sys_config['version'] . " : " 
					. $str_title . "</title> \n"
					//. "<script src=\"".$sys_config['includes_path']."jquery.js\" type=\"text/javascript\"></script>\n"
					//. "<script src=\"".$sys_config['includes_path']."tooltip.js\" type=\"text/javascript\"></script>\n"
					. "<style type=\"text/css\">\n"
					. "<!--\n"
					. "body,td,th { \n"
					. "	font-family: Verdana, Arial, Helvetica, sans-serif; \n"
					. "	font-size: 11px; \n"
					. "} \n"
					. "body { \n"
					. "	margin-left: 0px;\n"
					. "	margin-top: 0px;\n"
					. "	margin-right: 0px;\n"
					. "	margin-bottom: 0px;\n"
					. "	background-color: #"
					. ";\n"
					. "} \n"
					. ".style1 { \n"
					. "	font-size: 13px; \n"
					. "	font-weight: bold; \n"
					. "} \n"
					. ".style2 {color: #FFFFFF} \n"
					. ".style5 {font-size: 9px} \n"
					. ".style6 { \n"
					. "	color: #C79C9C; \n"
					. "	font-weight: bold; \n"
					. "} \n"
					. " div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; } \n"
					. " div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; } \n"

					. "--> \n"
					. "</style> \n"
					.	func_add_css($sys_config['includes_path']."css/css_jkkp.css")
					.	func_add_css($sys_config['includes_path']."jquery/css/media.themes.css")
					.	func_add_css($sys_config['includes_path']."twitter/css/bootstrap.css")
					.	func_add_css($sys_config['includes_path']."font-awesome/css/font-awesome.css")
					.	func_add_css($str_css_filename) 
					// .	"<script type=\"text/javascript\" src=\"https://getfirebug.com/firebug-lite.js\"></script>"
					// .	func_add_javascript($sys_config['includes_path']."jquery/js/jquery-1.7.1.min.js")
					// .	func_add_javascript($sys_config['includes_path']."jquery/jquery-ui-1.8.9/jquery-ui-1.8.9.custom.min.js")
					.	func_add_javascript($sys_config['includes_path']."jquery/js/jquery-1.9.1.min.js") 
					.	func_add_javascript($sys_config['includes_path']."jquery/jquery-ui-1.8.22/jquery-ui.js")
					.	func_add_javascript($sys_config['includes_path']."jquery/Others/jquery.alphanumeric.js")
					.	func_add_javascript($sys_config['includes_path']."twitter/js/bootstrap.js")
					// .	func_add_javascript($sys_config['includes_path']."jquery/js/jquery-migrate-1.1.1.js")
					.	func_add_javascript($sys_config['includes_path']."jquery/js/jquery-migrate-1.4.1.min.js")
					. 	func_add_javascript($str_javascript_filename)
					. '
					<!--[if lt IE 9]>
						<script src="'. $sys_config['includes_path'] .'/javascript/html5shiv.js"></script>
						<script src="'. $sys_config['includes_path'] .'/javascript/respond.js"></script>
					<![endif]-->
					<script>
					$(document).ready(function(){
						if($("button").hasClass("btn-new-style"))
							$("button").removeClass("btn-new-style").addClass("btn btn-primary btn-sm");
						
						$("[title]").tooltip({ html: true });
					});
					</script>
					<meta http-equiv="X-UA-Compatible" content="IE=edge">'
					. "</head> \n";
		if ($bln_frame==true)	{
				$str_cols = "270,1002";
			
			$str_header	.= "<frameset bordercolor=\"#FFFFFF\" rows=\"128,530\" cols=\"*\" frameborder=\"no\" >\n"
						. "<frame name=\"top\" src=\"".$str_frame_top."\" scrolling=\"no\"/>\n"
						. "<frameset rows=\"*\" cols=\"".$str_cols."\">\n"
						. "<frame name=\"left\" src=\"".$str_frame_left."\"/>\n"
						. "<frame name=\"right\" src=\"".$str_frame_content."\" />\n"
						. " </frameset>"
						. "</frameset>\n"
						. "<noframes>\n";
		}

		$str_header .= "<body onload=\"".$str_on_load."\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" "
					. "marginheight=\"0\" "
					. "> \n"
					. "<table height=\"100%\" width=\"100%\" border=\"0px\" cellpadding=\"0px\" "
					. "cellspacing=\"0px\" align=\"center\"> \n"
					. "<tr><td height=\"85%\" align=\"left\" valign=\"top\"> \n"	
					// 1st master table (header & content) + (footer)

					. "\t <table height=\"100%\" width=\"100%\" border=\"0px\" cellpadding=\"0px\" cellspacing=\"0px\"> \n"
					. "\t <tr><td height=\"\" align=\"left\" valign=\"top\">";	// 2nd master table (header) + (content)

		if ($bln_header==true)	{	
			// page header (begin)--------------------------------------------------------------
			$str_header .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> "
  						. "<tr> \n"
  						. "    <td width=\"1%\" valign=\"top\" "
						. " background=\"".$sys_config['images_path']."design/bg1.jpg\"><a href=\"".$sys_config['system_url']."\" target=\"_top\" ><img "
						. " src=\"".$sys_config['images_path']."design/logo1.gif\" width=\"215\" height=\"100\" " //logo ltk di sini: replace with this below line
						//. " src=\"".$sys_config['images_path']."design/bg1.jpg\" width=\"215\" height=\"100\" "
						. " alt=\"\" style=\"border:none\" /></td> \n"
  						. "    <td width=\"98%\" valign=\"bottom\" "
						. " background=\"".$sys_config['images_path']."design/bg.gif\">"
						. "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> \n"
						. "      <tr> \n"
						. "        <td width=\"78%\" valign=\"bottom\""
						//. " background=\"".$sys_config['images_path']."design/banner_middle.gif\""
						//. " style=\"background-repeat:no-repeat\">\n"
						. " >\n"
						. " <span class=\"style2\">&nbsp;<br /> \n"
						. "            <strong>"
						. ((isset($_SESSION['user']['name'])) ? $_SESSION['user']['name'] : "")
						. "" //replace with above after this
						."<br /> \n"
						//.((isset($_SESSION['user']['Level_ID'])) ? _get_StrFromCondition('sys_Level','Level_Name','Level_ID',$_SESSION['user']['Level_ID']) : "")
						. ((isset($_SESSION['user']['Level_Name'])) ? $_SESSION['user']['Level_Name'] : "")
						//. "" //replace with above after this
						//."<br /> \n"
						.((isset($_SESSION['user'])) ? "" : _LBL_GUEST)
						//.((isset($_SESSION['user']['branch_id'])) ? func_get_branch_name($_SESSION['user']['branch_id']) : _LBL_GUEST)
						//. "" //replace with above after this
						."</strong></span><br></td> \n"
						. "        <td width=\"22%\" height=\"37\" valign=\"bottom\" align=\"right\" "
						//. " class=\"style2\"><img src=\"".$sys_config['images_path']."jata.gif\" " //logo
						. " class=\"style2\"><img src=\"".$sys_config['images_path']."design/banner.gif\" " //logo
						. " width=\"514\" height=\"64\" /></td> \n"
						//. " width=\"120\" height=\"64\" /></td> \n"
						//. "  /></td> \n"
						. "      </tr> \n"
						. "    </table> \n"
						. "      <table width=\"100%\" border=\"0\" cellpadding=\"0\" "
						. " cellspacing=\"0\" background=\"".$sys_config['images_path']."design/bg_menu.gif\"> \n"
						. "      <tr> \n"
						. "        <td width=\"2%\" "
						. " background=\"".$sys_config['images_path']."design/bg_menu.gif\"><img "
						. " src=\"".$sys_config['images_path']."design/bg_menu2.gif\" width=\"14\" height=\"36\" alt=\"\" /></td> \n"
						. "        <td width=\"98%\" "
						. " background=\"".$sys_config['images_path']."design/bg_menu.gif\"><span "
						. " class=\"style1\">"						
						// .(($bln_menu == true)? func_system_menu() : '&nbsp;')
						.(($bln_menu == true)? func_system_menu() : func_system_menu())
						. "<br><img src=\"".$sys_config['images_path']."design/transparent.gif\" width=\"12\" height=\"7\" alt=\"\" />"
						. "</span></td> \n"
						. "      </tr> \n"
						. "    </table></td> \n"
						. "  </tr> \n"
						. "</table> \n"
						. "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> \n"
						. "  <tr> \n"
						//. "    <td background=\"".$sys_config['images_path']."design/blank.gif\" "
						//. "><img src=\"".$sys_config['images_path']."design/blank.gif\" "
						//. " width=\"12\" height=\"14\" alt=\"\" /></td> \n"
						. " 	<td>&nbsp;</td> \n"
						. "  </tr> \n"
						. "</table> \n";
					// page header (end)----------------------------------------------------------------
		}
		
		$str_header	.= "\t </td></tr> \n"
					. "\t <tr><td height=\"91%\" align=\"left\" valign=\"top\"> \n"

					// page content table start here ---------- 3rd master table (left content) + (right content)
					. "\t\t <table height=\"100%\" width=\"100%\" border=\"0px\" cellpadding=\"0px\" cellspacing=\"0px\" >\n"
					. "\t\t <tr>\n";
					if ($bln_portlet == true)
					{
						// left menu ----------------------------------------------------------------(begin)
						$str_header	.=	"<td width=\"25%\" align=\"left\" valign=\"top\">&nbsp;</td>\n";
						// left menu ----------------------------------------------------------------(end)
					}



		$str_header	.= "\t\t <td align=\"left\" valign=\"top\" > \n"
					. (($int_top==1)? "<br><br>" : "")
					." \n";
						// page content (begin)-------------------------------------------------------------

		// print page header
		echo $str_header;
		
	}
        
        function func_header_new(
				$str_title = '',
				$str_css_filename = '',
				$str_javascript_filename = '', 
				$bln_menu = true,
				$bln_portlet = true,
				$bln_header = true,
				$bln_frame = false,
				$str_frame_top = '',
				$str_frame_left = '',
				$str_frame_content = '', 
				$int_top = 1, 
				$str_on_load = '', 
				$int_print = false
	) {		
		global $sys_config,$mod_config;

		// header string
		$str_header	= "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 "
					. "Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\"> \n"
					. "<html> \n"
					. "<head> \n"
					. "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\"> \n"
					. "<meta http-equiv=\"X-UA-Compatible\" content=\"IE=Edge\"> \n"
					. "<link rel=\"shortcut icon\" href=\"". $sys_config['images_path'] ."favicon.ico\">"
					. "<title>" . $sys_config['system_name'] . " version " . $sys_config['version'] . " : " 
					. $str_title . "</title> \n"
					//. "<script src=\"".$sys_config['includes_path']."jquery.js\" type=\"text/javascript\"></script>\n"
					//. "<script src=\"".$sys_config['includes_path']."tooltip.js\" type=\"text/javascript\"></script>\n"
					. "<style type=\"text/css\">\n"
					. "<!--\n"
					. "body,td,th { \n"
					//. "	font-family: Verdana, Arial, Helvetica, sans-serif; \n"
					//. "	font-size: 11px; \n"
					. "} \n"
					. "body { \n"
					. "	margin-left: 0px;\n"
					. "	margin-top: 0px;\n"
					. "	margin-right: 0px;\n"
					. "	margin-bottom: 0px;\n"
					. "	background-color: #"
					. ";\n"
					. "} \n"
					. ".style1 { \n"
					. "	font-size: 13px; \n"
					. "	font-weight: bold; \n"
					. "} \n"
					. ".style2 {color: #FFFFFF} \n"
					. ".style5 {font-size: 9px} \n"
					. ".style6 { \n"
					//. "	color: #C79C9C; \n"
					//. "	font-weight: bold; \n"
					. "} \n"
					. " div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; } \n"
					. " div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; } \n"

					. "--> \n"
					. "</style> \n"
					//.	func_add_css($sys_config['includes_path']."css/css_jkkp.css")
					.	func_add_css($sys_config['includes_path']."jquery/css/media.themes.css")
					//.	func_add_css($sys_config['includes_path']."twitter/css/bootstrap.css")
					//.	func_add_css($sys_config['includes_path']."font-awesome/css/font-awesome.css")
					//.	func_add_css($str_css_filename) 
					// .	"<script type=\"text/javascript\" src=\"https://getfirebug.com/firebug-lite.js\"></script>"
					// .	func_add_javascript($sys_config['includes_path']."jquery/js/jquery-1.7.1.min.js")
					// .	func_add_javascript($sys_config['includes_path']."jquery/jquery-ui-1.8.9/jquery-ui-1.8.9.custom.min.js")
					.	func_add_javascript($sys_config['includes_path']."jquery/js/jquery-1.9.1.min.js") 
					.	func_add_javascript($sys_config['includes_path']."jquery/jquery-ui-1.8.22/jquery-ui.js")
					.	func_add_javascript($sys_config['includes_path']."jquery/Others/jquery.alphanumeric.js")
					.	func_add_javascript($sys_config['includes_path']."twitter/js/bootstrap.js")
					// .	func_add_javascript($sys_config['includes_path']."jquery/js/jquery-migrate-1.1.1.js")
					.	func_add_javascript($sys_config['includes_path']."jquery/js/jquery-migrate-1.4.1.min.js")
					. 	func_add_javascript($str_javascript_filename)
					. '
					<!--[if lt IE 9]>
						<script src="'. $sys_config['includes_path'] .'/javascript/html5shiv.js"></script>
						<script src="'. $sys_config['includes_path'] .'/javascript/respond.js"></script>
					<![endif]-->
					<script>
					$(document).ready(function(){
						if($("button").hasClass("btn-new-style"))
							$("button").removeClass("btn-new-style").addClass("btn btn-primary btn-sm");
						
						$("[title]").tooltip({ html: true });
					});
					</script>
					<meta http-equiv="X-UA-Compatible" content="IE=edge">'
					. "</head> \n";
		if ($bln_frame==true)	{
				$str_cols = "270,1002";
			
			$str_header	.= "<frameset bordercolor=\"#FFFFFF\" rows=\"128,530\" cols=\"*\" frameborder=\"no\" >\n"
						. "<frame name=\"top\" src=\"".$str_frame_top."\" scrolling=\"no\"/>\n"
						. "<frameset rows=\"*\" cols=\"".$str_cols."\">\n"
						. "<frame name=\"left\" src=\"".$str_frame_left."\"/>\n"
						. "<frame name=\"right\" src=\"".$str_frame_content."\" />\n"
						. " </frameset>"
						. "</frameset>\n"
						. "<noframes>\n";
		}

		$str_header .= "<body onload=\"".$str_on_load."\" leftmargin=\"0\" topmargin=\"0\" marginwidth=\"0\" "
					. "marginheight=\"0\" "
					. "> \n"
					. "<table height=\"100%\" width=\"100%\" border=\"0px\" cellpadding=\"0px\" "
					. "cellspacing=\"0px\" align=\"center\"> \n"
					. "<tr><td height=\"85%\" align=\"left\" valign=\"top\"> \n"	
					// 1st master table (header & content) + (footer)

					. "\t <table height=\"100%\" width=\"100%\" border=\"0px\" cellpadding=\"0px\" cellspacing=\"0px\"> \n"
					. "\t <tr><td height=\"\" align=\"left\" valign=\"top\">";	// 2nd master table (header) + (content)

		if ($bln_header==true)	{	
			// page header (begin)--------------------------------------------------------------
			$str_header .= "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> "
  						. "<tr> \n"
  						. "    <td width=\"1%\" valign=\"top\" "
						. " background=\"".$sys_config['images_path']."design/bg1.jpg\"><a href=\"".$sys_config['system_url']."\" target=\"_top\" ><img "
						. " src=\"".$sys_config['images_path']."design/logo1.gif\" width=\"215\" height=\"100\" " //logo ltk di sini: replace with this below line
						//. " src=\"".$sys_config['images_path']."design/bg1.jpg\" width=\"215\" height=\"100\" "
						. " alt=\"\" style=\"border:none\" /></td> \n"
  						. "    <td width=\"98%\" valign=\"bottom\" "
						. " background=\"".$sys_config['images_path']."design/bg.gif\">"
						. "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> \n"
						. "      <tr> \n"
						. "        <td width=\"78%\" valign=\"bottom\""
						//. " background=\"".$sys_config['images_path']."design/banner_middle.gif\""
						//. " style=\"background-repeat:no-repeat\">\n"
						. " >\n"
						. " <span class=\"style2\">&nbsp;<br /> \n"
						. "            <strong>"
						. ((isset($_SESSION['user']['name'])) ? $_SESSION['user']['name'] : "")
						. "" //replace with above after this
						."<br /> \n"
						//.((isset($_SESSION['user']['Level_ID'])) ? _get_StrFromCondition('sys_Level','Level_Name','Level_ID',$_SESSION['user']['Level_ID']) : "")
						. ((isset($_SESSION['user']['Level_Name'])) ? $_SESSION['user']['Level_Name'] : "")
						//. "" //replace with above after this
						//."<br /> \n"
						.((isset($_SESSION['user'])) ? "" : _LBL_GUEST)
						//.((isset($_SESSION['user']['branch_id'])) ? func_get_branch_name($_SESSION['user']['branch_id']) : _LBL_GUEST)
						//. "" //replace with above after this
						."</strong></span><br></td> \n"
						. "        <td width=\"22%\" height=\"37\" valign=\"bottom\" align=\"right\" "
						//. " class=\"style2\"><img src=\"".$sys_config['images_path']."jata.gif\" " //logo
						. " class=\"style2\"><img src=\"".$sys_config['images_path']."design/banner.gif\" " //logo
						. " width=\"514\" height=\"64\" /></td> \n"
						//. " width=\"120\" height=\"64\" /></td> \n"
						//. "  /></td> \n"
						. "      </tr> \n"
						. "    </table> \n"
						. "      <table width=\"100%\" border=\"0\" cellpadding=\"0\" "
						. " cellspacing=\"0\" background=\"".$sys_config['images_path']."design/bg_menu.gif\"> \n"
						. "      <tr> \n"
						. "        <td width=\"2%\" "
						. " background=\"".$sys_config['images_path']."design/bg_menu.gif\"><img "
						. " src=\"".$sys_config['images_path']."design/bg_menu2.gif\" width=\"14\" height=\"36\" alt=\"\" /></td> \n"
						. "        <td width=\"98%\" "
						. " background=\"".$sys_config['images_path']."design/bg_menu.gif\"><span "
						. " class=\"style1\">"						
						// .(($bln_menu == true)? func_system_menu() : '&nbsp;')
						.(($bln_menu == true)? func_system_menu() : func_system_menu())
						. "<br><img src=\"".$sys_config['images_path']."design/transparent.gif\" width=\"12\" height=\"7\" alt=\"\" />"
						. "</span></td> \n"
						. "      </tr> \n"
						. "    </table></td> \n"
						. "  </tr> \n"
						. "</table> \n"
						. "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\"> \n"
						. "  <tr> \n"
						//. "    <td background=\"".$sys_config['images_path']."design/blank.gif\" "
						//. "><img src=\"".$sys_config['images_path']."design/blank.gif\" "
						//. " width=\"12\" height=\"14\" alt=\"\" /></td> \n"
						. " 	<td>&nbsp;</td> \n"
						. "  </tr> \n"
						. "</table> \n";
					// page header (end)----------------------------------------------------------------
		}
		
		$str_header	.= "\t </td></tr> \n"
					. "\t <tr><td height=\"91%\" align=\"left\" valign=\"top\"> \n"

					// page content table start here ---------- 3rd master table (left content) + (right content)
					. "\t\t <table height=\"100%\" width=\"100%\" border=\"0px\" cellpadding=\"0px\" cellspacing=\"0px\" >\n"
					. "\t\t <tr>\n";
					if ($bln_portlet == true)
					{
						// left menu ----------------------------------------------------------------(begin)
						$str_header	.=	"<td width=\"25%\" align=\"left\" valign=\"top\">&nbsp;</td>\n";
						// left menu ----------------------------------------------------------------(end)
					}



		$str_header	.= "\t\t <td align=\"left\" valign=\"top\" > \n"
					. (($int_top==1)? "<br><br>" : "")
					." \n";
						// page content (begin)-------------------------------------------------------------

		// print page header
		echo $str_header;
		
	}

	function func_add_css($str_css_filename='')
	{
		if (!empty($str_css_filename))
		{
			$str_external_link	=	'';
			$str_filename		=	explode(',',$str_css_filename);	// sperate the filename into array
			$int_max			=	count($str_filename);	// get total number of filename
			foreach($str_filename as $value)
			{
				if (is_file(trim($value)))	// check file exist
				{	// external css file exist, append to the string
					$str_external_link	.=	"<link rel=\"stylesheet\" type=\"text/css\" href=\"".$value."\">\n";
				}
			}
			return $str_external_link;	// returning external link string
		}
		return '';
	}

	// since this function only call by func_header function
	// so it is place together in this file (func_header.php)
	// seperate multiple javascript file with comma (,) for the parameter
	// eg:  "file1.js,file2.js,file3.js"
	function func_add_javascript($str_javascript_filename='')	{
		if (!empty($str_javascript_filename))
		{
			$str_external_link	=	'';
			$str_filename		=	explode(',',$str_javascript_filename);	// sperate the filename into array
			$int_max			=	count($str_filename);	// get total number of filename
			foreach($str_filename as $value)
			{
				if (is_file(trim($value)))	// check file exist
				{	// external javascript file exist, append to the string
					$str_external_link	.=	"<script type=\"text/javascript\" src=\"".$value."\"></script>\n";
				}
			}
			return $str_external_link;	// returning external link string
		}
		return '';
	}

	// since this function only call by func_header function
	// so it is place together in this file (func_header.php)
	// return system menu string according to user level
	// require menu_admin.php, menu_userRW.php, menu_userRO.php
	function func_system_menu()
	{
		global $sys_config;
		if (!isset($_SESSION['user']))
			return menu_manual();

		if (!isset($_SESSION['user']['Level_ID']))
			return '';

		$str_menu	=	menu_user($_SESSION['user']['Level_ID']);
		//$str_menu	=	menu_user(1);

		return $str_menu;
	}
?>
