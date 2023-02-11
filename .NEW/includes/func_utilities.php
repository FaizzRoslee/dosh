<?php
	/**************************************************************************************
		File name		:		func_utilities.php
		Return			:		string with id in bracket e.g ('','')
		Date created	:		20 April 2006
		Last modify		:		20 April 2006
		Description		:		func_utilities function to provide utilities for system used
	***************************************************************************************/

	//==================================
	function func_title_menu($title='') {
		global $sys_config;
		
		$str = '
				<tr>
					<td width="6%" background="'.$sys_config['images_path'].'design/pq_24.gif"><img src="'.$sys_config['images_path'].'design/pq_22.gif" width="12" height="30" alt="" /></td>
					<td width="87%" background="'.$sys_config['images_path'].'design/pq_24.gif" class="style1">'.$title.'</td>
					<td width="7%" background="'.$sys_config['images_path'].'design/pq_24.gif"><div align="right"><img src="'.$sys_config['images_path'].'design/pq_25.gif" width="12" height="30" alt="" /></div></td>
				</tr>
			';
		return $str;
	}
	//==================================

	function func_left_menu($title='',$url='',$menu='',$int_menu='',$class='',$type=0) {
		global $sys_config;
		global $mod_config;
		
		$str_menu	=	"<tr>\n";
		
		$str_menu	.=	"<td background=\""
					.	$sys_config['images_path']."design/pq_"
					.	(($int_menu==$menu)? "34" : "28").".gif\">"
					.	"<img src=\"".$sys_config['images_path']."design/pq_"
					.	(($int_menu==$menu)? "33" : "27").".gif\" width=\"12\" height=\"22\" alt=\"\" >"
					.	"</td>\n";
		$str_menu	.=	"<td background=\"".$sys_config['images_path']."design/pq_"
					.	(($int_menu==$menu)? "34" : "28").".gif\" class=\"".$class."\">";
		if ($type == 0)
		{
			$str_menu	.=	"<a href=\"#\" onclick=\"javascript:redirect_to_content('"
						.	$url."','".$sys_config['frame_path']."left_".$mod_config['module_name'].".php?menu=".$menu."');\">";
		}		
		$str_menu	.=	$title
					.	"</a>"
					.	"</td>\n";
		$str_menu 	.=	"<td background=\"".$sys_config['images_path']."design/pq_"
					.	(($int_menu==$menu)? "34" : "28").".gif\" align=\"right\">"
					.	"<img src=\"".$sys_config['images_path']."design/pq_"
					.	(($int_menu==$menu)? "35" : "29").".gif\" width=\"12\" height=\"22\" alt=\"\" >"
					.	"</td>\n";
		$str_menu	.=	"</tr>\n";
		
		return $str_menu;
	}	
	
	function func_left_menu2($title='',$url='',$menu='',$int_menu='',$header=0) {
		global $sys_config,$mod_config;
		
		$str_menu	= "<li><a href=\"#\"";
		if($header==0){
			$str_menu	.= " onclick=\"javascript:redirect_to_content('".$url."','".$sys_config['frame_path']."left_".$mod_config['module_name'].".php?menu=".$menu."');\""
						. " ".(($int_menu==$menu)? " class=\"active\" " : ""). " style=\"margin-left:0.7em;\"";
		}else{
			$str_menu	.= " class=\"title\"";
		}
		$str_menu	.=	" >".$title."</a></li>";
		
		return $str_menu;
	}
	//==================================
	
	function is_valid_email($address)	{ // email validation
		// check an email address is possibly valid
		if (ereg('^[a-zA-Z0-9_\.\-]+@[a-zA-Z0-9\-]+\.[a-zA-Z0-9\-\.]+$', $address))
			return true;
		else 
			return false;
	}
	

	function func_table_info($str_info = '')	{ 

		echo "<table class=\"contents\" width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"5\">\n";
		echo "\t<tr class=\"contents\">\n";
	  	echo "\t\t<td valign=\"middle\" class=\"label1\">". stripslashes($str_info)."</td>\n";
	  	echo "\t</tr>\n";
	 	echo "</table>\n";
		
	}
		
	function string_wordml_escape($str_escape='') {
		if(!empty($str_escape)) {
			$str_escape = str_replace("&", '&amp;', $str_escape);
			$str_escape = str_replace("<", '&lt;', $str_escape);
			$str_escape = str_replace(">", '&gt;', $str_escape);
		}
		return $str_escape;
	}	

	function dateDiff($dformat, $endDate, $beginDate) {
		$date_parts1=explode($dformat, $beginDate);
		$date_parts2=explode($dformat, $endDate);
		$start_date=gregoriantojd($date_parts1[1], $date_parts1[0], $date_parts1[2]);
		$end_date=gregoriantojd($date_parts2[1], $date_parts2[0], $date_parts2[2]);
		return $end_date - $start_date;
	}
	
	function dateAdd($dFormat,$date,$int_inc)
	{
		$date_parts1 = explode($dFormat, $date);
		
		$new_date	=	$date_parts1[2]."-".$date_parts1[1]."-".$date_parts1[0];
		
		return (date('d-m-Y', strtotime($new_date.' +'.$int_inc.' days'))); 
	}

?>