<?php
	//****************************************************************/
	// filename: empList.php
	// description: list of the employee
	//****************************************************************/
	include_once '../includes/sys_config.php';
	//==========================================================
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'func_rand_str.php';
	include_once $sys_config['includes_path'].'func_valid_login.php';
	include_once $sys_config['includes_path'].'func_get_user.php';
	//==========================================================
	if(isset($_POST['language'])) $language = $_POST['language'];
	elseif(isset($_GET['language'])) $language = $_GET['language'];
	else{
		if(isset($_SESSION['lang'])) $language = $_SESSION['lang'];
		else $language = 'eng';
	}
	if(empty($language)) $language = 'eng';
	$fileLang = $language.'.php';
	include_once $sys_config['languages_path'].$fileLang;
	//==========================================================
	include_once $sys_config['includes_path'].'formElement.php'; //need file language
	include_once $sys_config['includes_path'].'arrayCommon.php'; //need file language
	//==========================================================
	if(isset($_POST['language'])){
		$_SESSION['lang'] = $language;
	}
	
	if (isset($_SESSION['user'])){	// user already logged in, check session expired
		//if (!func_is_session_expired())  // if session not expired, redirect user to home page
		//{
			header("Location: " . $sys_config['authentication_path'] . "home.php");
			exit();
		//}
	}

	//======================================================================================================================
	// form pass back process (begin)-----------------------------------------------------------------------
	if (isset($_POST['userName'])){
		// print_r($_POST);die;
	
		$error = 0;
		if (!isset($_SESSION['password_field'])){ // wrong session redirect to new session
			//header("Location: " . $sys_config['system_url']);
			//exit();
		}
		
		$passField = $_SESSION['password_field']; // the magic password field name from session
		if (!isset($_POST[$passField]))
		{	// cannot find the magic password field from the submitted form, 
			// this because user submit the form at wrong session
			// redirect user to new session
			//header("Location: " . $sys_config['system_url']);
			//exit();
		}
		if($_POST['rbType']==1){ $userType = 'sys_User_Staff'; }
		else{ $userType = 'sys_User_Client'; }
		
		//--login via systems--//
		if(func_is_valid_login($_POST['userName'],$_POST[$passField], $_SESSION['magic_string'],$userType))
		{	// valid user login
			$_SESSION['user']		= func_get_user($_POST['userName'],$userType);	// store user id into session
			$_SESSION['last_login']		= time(); // get current timestamp
			$_SESSION['lang'] 		= $language;
			
			unset($_SESSION['password_field']);		// clear session variables used during login process
			unset($_SESSION['magic_string']);		// clear session variables used during login process
			
                        echo "1";
			//exit();

		}
		//--end login via systems --//
		else	// invalid user login
		{

			echo 'Wrong User ID  or Password !!!';
		}
	}
        else {
            echo "no data";
        }
	
?>

	
    
	