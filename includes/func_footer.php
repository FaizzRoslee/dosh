<?php
	/*********************************************************************************
		File name		:		func_footer.php
		Function name	:		func_footer()
		Parameter		:		none
		Return			:		none
		Date created	:		22 July 2005
		Last modify		:		26 May 2005
		Description		:		func_footer function to print page footer
	**********************************************************************************/
	/*** page protection ***/
	// this protect the user direct access to the page,
	// this should be included in every page, except /index.php (system entry)
	
	function func_footer($bln_footer = true, $bln_frame = false)
	{					// page content (end)-------------------------------------------------
		global $sys_config;

   		$str_footer	= "\t\t </td></tr>"
					. "\t\t </table>"	// (end) 3rd master table (left content) + (right content)
					. "\t </td></tr></table> \n"		// (end) 2nd master table (header) + (content)
					. "</td></tr> \n"
					. "<tr><td height=\"4%\" align=\"left\" valign=\"bottom\"> \n";
					// page footer (begin) -------------------------------------------------
		if($bln_footer==true)		{
			$str_footer	.= "<table width=\"100%\" border=\"0px\" cellpadding=\"0px\" cellspacing=\"0px\"> \n"
						. "<tr style=\"background-image:url('" . $sys_config['images_path'] . "banner/footer.gif')\"> \n"
						. "    <td width=\"1\" valign=\"middle\" align=\"right\"> \n"
						. " <img src=\"" . $sys_config['images_path'] . "window/pixel.gif\" width=\"1px\" height=\"30px\"></td> \n"
						. "    <td class=\"copyright\" valign=\"middle\" align=\"center\"> \n"
						. "    Hak Milik &copy; ".$sys_config['organization']."</td> \n"
						. "</tr></table>";
		}
						// page footer (end) ---------------------------------------------------
		$str_footer .= "\n </td></tr> \n"
					. "</table> \n"		// (end) 1st master table (header & content) + (footer)
					. "</body> \n"
					. (($bln_frame==true)? "</noframes>\n" : '')
					. "</html> \n"
					. "<script language=\"javascript\"> \n"
					. "<!--// \n"
					. "if(document.all && !document.getElementById) \n"
					. "{ \n\t document.getElementById = function(id){  return document.all[id];  } \n} \n"
					. "//--> \n"
					. "</script> \n";
				
	
		if ($sys_config['debug'] == true && $bln_frame = true)
		{
			echo "<br><pre style=\"background-color:#EEEEEE\">Debuging information:";	// debuging purpose
			echo "\n\n\$_SESSION\n";	// debuging purpose
			var_dump($_SESSION);			// debuging purpose
			echo "\n\n\$_GET\n";		// debuging purpose
			var_dump($_GET);				// debuging purpose
			echo "\n\n\$_POST\n";		// debuging purpose
			var_dump($_POST);				// debuging purpose
			echo "</pre>";
		}

	echo $str_footer;
	}




?>
