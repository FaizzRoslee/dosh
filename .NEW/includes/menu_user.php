<?php
	/*********************************************************************************
		File name		:		/includes/menu_user.php
		Date created	:		30 November 2010
		Description		:		menu_user() will return the READ WRITE user menu 
								in string.
		Require			:		$sys_config		(sys_config.php)
	**********************************************************************************/
	function menu_user($levelID=0){ //$level_id == level for user's current/session level
		global $sys_config;
		// items menu--------------------------------------------------------------------------------------
		$color_separate = "yellow";
		$color_link		= "white";
		
		//=======================================================
		$str_menu = "<font color=\"".$color_separate."\"><strong><a href=\"".$sys_config['authentication_path']."home.php\" style=\"color:".$color_link."\" target=\"_top\">". _LBL_MENU_HOME ."</a> ";
		//=======================================================
		if($levelID==1||$levelID==2){
			$str_menu .= " | <a href='".$sys_config['admin_path']."main.php' style=\"color:".$color_link."\" target=\"_top\">". _LBL_MENU_ADMIN ."</a>";
		}
		//=======================================================
		if($levelID<6){
			$str_menu .= " | <a href='".$sys_config['inventory_path']."main.php' style=\"color:".$color_link."\" target=\"_top\">". _LBL_MENU_INVENTORY ."</a>";
		}
		//=======================================================
		$str_menu .= " | <a href='".$sys_config['submission_path']."main.php' style=\"color:".$color_link."\" target=\"_top\">". _LBL_MENU_SUBMISSION ."</a>";
		//=======================================================
		//if($levelID<6){
			$str_menu .= " | <a href='".$sys_config['report_path']."main.php' style=\"color:".$color_link."\" target=\"_top\">". _LBL_MENU_REPORT ."</a>";
		//}
		//=======================================================
		$str_menu .= " | <a href='".$sys_config['user_path']."main.php' style=\"color:".$color_link."\" target=\"_top\">". _LBL_MENU_PROFILE ."</a>";
		//=======================================================
		$str_menu .= " | <a href='".$sys_config['support_path']."main.php' style=\"color:".$color_link."\" target=\"_top\">". strtoupper(_LBL_SUPPORT) ."</a>";
		//=======================================================
		$str_menu .= " | <a href='".$sys_config['authentication_path']."logout.php' style=\"color:".$color_link."\" target=\"_top\">". _LBL_MENU_LOGOUT ."</a>";
		//=======================================================
		$str_menu .= "</font>";
		//=======================================================
		
		if($levelID==1 || $levelID==2)
			$failname = '';
		elseif($levelID==3)
			$failname = $sys_config['dl_path'].'CIMS User Manual for Officer.pdf';
		elseif($levelID==4)
			$failname = $sys_config['dl_path'].'CIMS User Manual for Head.pdf';
		elseif($levelID==5)
			$failname = $sys_config['dl_path'].'CIMS User Manual for Enforecement Officer.pdf';
		else
			$failname = $sys_config['dl_path'].'CIMS User Manual for Supplier.pdf';
		
		if(is_file(trim($failname)))
			$a_url = 'href="'.$failname.'" target=\"_blank\"';
		else
			$a_url = 'href="#"';
			
		//=======================================================
		// $str_menu .= "<span style=\"vertical-align:middle;position:absolute;right:10px;\"><a ".$a_url."><img src=\"".$sys_config['images_path']."download.png\" style=\"border:none;\" alt=\"". _LBL_DL_MANUAL ."\" title=\"". _LBL_DL_MANUAL ."\" width=\"24\" height=\"24\" /></a></span>";
		$str_menu .= "<a ".$a_url." style=\"vertical-align:middle;text-decoration:none;\"><span style=\"position:absolute;right:30px;\"><font color=\"".$color_link."\">". _LBL_USER_MANUAL ."</font></span>&nbsp;<span style=\"position:absolute;right:5px;\"><img src=\"".$sys_config['images_path']."download.png\" style=\"border:none;\" alt=\"". _LBL_DL_MANUAL ."\" title=\"". _LBL_DL_MANUAL ."\" width=\"24\" height=\"24\" /></span></a>";
		//=======================================================

		return $str_menu;
	}
	
	//Administrator: 			General [Setting,Not. List], Users [Personal Info,Staff,User(Supplier,Manufacturer)], Administration, History, Report
	//Supplier/Manufacturer: 	General [Company Detail,Certificate,Not. List], Submission [Submission List,Approved List], History
	//Administrator: General [Setting,Notification List], Users [Personal Info,Staff,User(Supplier,Manufacturer)], Administration, History, Report
	
	function menu_manual(){
		global $sys_config;
		$color_link		= "white";
		$failname = $sys_config['dl_path'].'CIMS User Manual for Supplier.pdf';
		if(is_file(trim($failname)))
			$a_url = 'href="'.$failname.'" target=\"_blank\"';
		else
			$a_url = 'href="#"';		
		//=======================================================
		$str_menu = "<a ".$a_url." style=\"vertical-align:middle;text-decoration:none;\"><span style=\"position:absolute;right:30px;\"><font color=\"".$color_link."\">". _LBL_USER_MANUAL ."</font></span>&nbsp;<span style=\"position:absolute;right:5px;\"><img src=\"".$sys_config['images_path']."download.png\" style=\"border:none;\" alt=\"". _LBL_DL_MANUAL ."\" title=\"". _LBL_DL_MANUAL ."\" width=\"24\" height=\"24\" /></span></a>";
		//=======================================================		
		return $str_menu;
	}
?>