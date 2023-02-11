<?php
	/*********************************************************************************
		File name		:		func_get_user.php
		Function name	:		func_get_user(str_loginid)
		Parameter		:		str_loginid (string)
		Return			:		(array)
		Date created	:		14 December 2010
		Description		:		func_get_user function to get the user
								information and return in array format
								require database object $db
	**********************************************************************************/
	
	function func_get_user($str_loginid='',$usrTable=''){
		global $db;

		if (empty($str_loginid)==TRUE)
			return NULL;	// no user id given

		if (!$db)
			return NULL;	// no database object found
		
		if($usrTable == 'sys_User_Staff'){
			$colName = 'User_Fullname';
			$colID = 'User_StaffID';
		}else{
			$colName = 'Company_Name';
			$colID = 'Company_No';
		}	
		// setup sql string
		$sql = "SELECT u.*,l.Level_Name, ut.".$colName." AS `name`, ut.".$colID." AS id FROM sys_User u"
			. " LEFT JOIN ". $usrTable ." ut ON u.Usr_ID = ut.Usr_ID"
			. " LEFT JOIN sys_Level l ON u.Level_ID = l.Level_ID"
			. " WHERE Usr_LoginID = ".quote_smart($str_loginid) ." AND u.isDeleted = 0 AND u.Active = 1";
		$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
		$row = $db->sql_fetchrow($res);	
		
		return $row;
	}
?>