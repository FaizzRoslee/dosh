<?php
	/*********************************************************************************
		File name		:		func_window.php
		Function name	:		func_window_open(str_title,str_width,str_height,str_align)
								func_window_close()
		Parameter		:		str_title (string), str_width (string)
								str_height (string), str_align (string)
		Return			:		none
		Date created	:		30 July 2005
		Last modify		:		30 July 2005
		Description		:		func_window_open and func_window_close function to
								generate a window box.
		Require			:		$sys_config		(sys_config.php)
	**********************************************************************************/

	//=========================================================================
	function func_window_open($str_title = '', $str_width = '', $str_height ='', $str_align ='') {
		global $sys_config;
		
		echo '
			<table bgcolor="#FFFFFF" width="' . (empty($str_width) ? "100%" : $str_width) . '" align="' . (empty($str_align) ? '' : $str_align) . '" border="0" cellspacing="0" cellpadding="0">"
				<tr>
					<td align="left" valign="top"><img src="'.$sys_config['images_path'].'"window/top_left.gif" border="0"></td>
					<td class="title" style="background-image:url("'.$sys_config['images_path'].'window/top_mid.gif"); background-repeat: repeat-x; background-position:top;">"'. $str_title . '"</td>
					<td align="right" valign="top"><img src="'.$sys_config['images_path'].'window/top_right.gif" border="0"></td>
				</tr>
				<tr>
					<td align="left" valign="top" style="background-image:url("'.$sys_config['images_path'].'window/mid_left.gif"); background-repeat: repeat-y;background-position:top left"><img src="'.$sys_config['images_path'].'window/pixel.gif" border="0" height="' . (empty($str_height) ? "1px" : $str_height) . '" width="1px"></td>
					<td align="left" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="5"><tr><td align="left" valign="top">
			';
	}
	//===============================
	function func_window_close() {
		global $sys_config;
		
		echo '
					</td>
				</tr>
			</table></td>
			<td align="right" valign="top" style="background-image:url("'.$sys_config['images_path'].'window/mid_right.gif"); background-repeat: repeat-y;background-position:top right"><img src="'.$sys_config['images_path'].'window/pixel.gif" border="0" height="1px" width="1px"></td>
		</tr>
		<tr>
			<td width="1px" align="left" valign="bottom"><img src="'.$sys_config['images_path'].'window/bottom_left.gif" border="0"></td>
			<td style="background-image:url("'.$sys_config['images_path'].'window/bottom_mid.gif"); background-repeat: repeat-x; background-position:bottom\">
			<img src="'.$sys_config['images_path'].'window/pixel.gif" border="0" height="1px" width="1px"></td>
			<td width="1px" align="right" valign="bottom"><img src="'.$sys_config['images_path'].'window/bottom_right.gif" border="0"></td>
		</tr>
		</table>
			';
	}
	//=========================================================================
	function func_window_open2($str_title = '', $str_width = '', $str_height ='', $str_align ='') {
		global $sys_config;
		
		echo '
				<table width="' . (empty($str_width) ? "100%" : $str_width) . '" border="0" cellspacing="0" cellpadding="0" align="' . (empty($str_align) ? '' : $str_align) . '" height="' . (empty($str_height) ? "1px" : $str_height) . '" >
					<tr>
						<td style="width:2%" background="'.$sys_config['images_path'].'design/pq_26.gif" height="30" ></td>
						<td style="width:98%" background="'.$sys_config['images_path'].'design/pq_26.gif" class="style1">'.$str_title.'</td>
					</tr>
					<tr>
						<td colspan="2" valign="top" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top"><br />
			';
	}
	//===============================
	function func_window_close2() {
		global $sys_config;
		
		echo '
								</td>
							</tr>
						</table></td>
					</tr>
				</table>
			';
	}
	//=========================================================================
	function func_window_open_print($str_title = '', $str_width = '', $str_height ='', $str_align ='') {
		global $sys_config;
		
		echo '
				<br />
				<table width="' . (empty($str_width) ? "100%" : $str_width) . '" border="0" cellspacing="0" cellpadding="0" align="' . (empty($str_align) ? '' : $str_align) . '" height="' . (empty($str_height) ? "1px" : $str_height) . '" >
					<tr>
						<td width="100%" align="center" height="30" class="style1">'.$str_title.'</td>
					</tr>
					<tr>
						<td valign="top" align="center"><table width="90%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td valign="top">
								<br />
			';
	}
	//===============================
	function func_window_close_print() {
		global $sys_config;
		
		echo '
								</td>
							</tr>
						</table></td>
					</tr>
				</table>
			';
	}
	//=========================================================================
?>