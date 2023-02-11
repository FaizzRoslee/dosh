<?php
	/**************************************************************************************
		File name		:		func_valid_login.php
		Function name	:		func_valid_user(str_loginid,str_password,str_magic)
		Parameter		:		str_loginid (string), str_password (string),
								str_magic (string)
		Return			:		(boolean)
		Date created	:		14 December 2010
		Description		:		func_is_valid_login function to validate user login
								str_loginid is username string
								str_password is MD5 string
								str_magic is a random generated string get session
								require database object $db
								require /[includes]/sys_constants.php
	***************************************************************************************/	

	function func_is_valid_login($str_loginid='',$str_password='',$str_magic='',$userTable='') {
		// get database object
		global $db;

		$str_loginid		=	trim($str_loginid); // username
		$str_password		=	trim($str_password); // md5 password
		$str_magic		=	trim($str_magic); // magic string from login form
		$str_md5_password	=	'';	// md5(md5 password + magic string), for matching purpose

		if (empty($str_loginid) || empty($str_password)) 
		        return FALSE;	// no username or password given
                        
		if (!$db) 
			return FALSE;	// no database object

		$sql_user	= "SELECT u.*"
					. " FROM sys_User u"
					. " RIGHT JOIN ".$userTable." uo ON u.Usr_ID = uo.Usr_ID"
					. " WHERE Usr_LoginID = ". quote_smart($str_loginid)
					. " AND u.Active = 1 AND u.isDeleted = 0";
		//die($sql_user);		
		$res_user	= $db->sql_query($sql_user,END_TRANSACTION) or die(print_r($db->sql_error()));

		if ($db->sql_numrows($res_user)==0){	
                    return FALSE;	// username not found
                }        
		$row_user	=	$db->sql_fetchrow($res_user);

		// md5( md5 password from database + magic string from session)
		$str_md5_password	=	md5($row_user['Usr_Password'].$str_magic);
		//$str_md5_password	=	strtoupper($str_md5_password);
                
                $r_pass = md5(md5($str_password).$str_magic);
		
                if (strcmp($r_pass,$str_md5_password)==0) {	// password match, update user last login
			//func_activate_usr($str_loginid);
			//if (($row_user['stat_id']==1) && (func_is_usr_level_id($str_loginid)==true) && (func_is_usr_active($str_loginid)==true))	{
			if ((func_is_usr_level_id($str_loginid,$userTable)==TRUE) && (func_is_usr_active($str_loginid,$userTable)==TRUE))	{
				$sql_user	= "UPDATE sys_User SET Usr_Lastlogin = NOW() "
							. " WHERE UPPER(Usr_LoginID) = ".quote_smart(strtoupper($str_loginid))
							. " AND Active = 1 AND isDeleted = 0";
				$db->sql_query($sql_user,END_TRANSACTION);
				 return TRUE;
			}else
				  
                            return FALSE;
		}else{
                        return FALSE;	// invalid username and password
		}
	}
	
	
	function func_is_usr_level_id($usr_loginid='',$userTable=''){
		global $db;
	
		$sql	= "SELECT Level_ID FROM sys_User u"
				. " RIGHT JOIN ".$userTable." uo ON u.Usr_ID = uo.Usr_ID"
				. " WHERE UPPER(Usr_LoginID) = ".quote_smart(strtoupper($usr_loginid))
				. " AND u.Active = 1 AND u.isDeleted = 0";
		$res 	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
		$row	= $db->sql_fetchrow($res);
		
		if ($row['Level_ID']==0)
			return FALSE;
		else
			return TRUE;
	}
	
	function func_id_extists($usr_loginid='',$userTable='')	{
		global $db;
		
		$sql	= "SELECT Usr_LoginID FROM sys_User u"
				. " RIGHT JOIN ".$userTable." uo ON u.Usr_ID = uo.Usr_ID"
				. " WHERE UPPER(Usr_LoginID) = ".quote_smart(strtoupper($usr_loginid))
				. " AND u.Active = 1 AND u.isDeleted = 0";
		$res	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
		
		if ($db->sql_numrows($res) <= 0)	
			return FALSE;	// username not found
		else
			return TRUE;
	}
	
	function func_is_usr_active($usr_loginid = '',$userTable='')	{
		global $db;
	
		$sql	= "SELECT Active FROM sys_User u"
				. " RIGHT JOIN ".$userTable." uo ON u.Usr_ID = uo.Usr_ID"
				. " WHERE LOWER(u.Usr_LoginID) = ".quote_smart(strtolower($usr_loginid))
				. " AND u.isDeleted = 0";
		// echo $sql;die;
		$res	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
		$row	= $db->sql_fetchrow($res);
		
		if ($row['Active']!=1)
			return FALSE;
		else
			return TRUE;
	}
?>