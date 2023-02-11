<?php
	/*********************************************************************************
		File name		:		sys_config.php
		Date created	:		21 July 2005
		Last modify		:		21 July 2005
		Description		:		Common system configuration
	**********************************************************************************/

	/*** system ***/

	$sys_config["system_name"]				=		"Chemical Information Management System";
	$sys_config["version"]					=		"1.0";
	$sys_config["system_status"]			=		"1";	// 0 - Halt
															// 1 - Single user mode (super admin only)
															// 2 - Multiple user mode
	$sys_config["organization"]				= 		"JKKP";		// organization name
	$sys_config["host_name"]				= 		"localhost";		// to be edited soon on server
	$sys_config["system_entry"]				= 		"index.php";			// system entry
	$sys_config["system_folder"]			=		"";
	$sys_config["system_url"]				=		"../";

	$sys_config["default_language"]			=		"eng";					//default language  : may (ISO 639 Language Codes)
	$sys_config["language"]					=		"";						//selected language : (ISO 639 Language Codes)
	$sys_config["session_timeout"]			=		0;						// 0 - disabled / minute
	$sys_config["email_notification"]		=		false;					// enable/disable e-mail notification
	$sys_config["debug"]					=		false;					// enable/disable debuging information

	/*** path ***/
	$sys_config["document_root"]			=		"../";
	$sys_config["classes_path"]				=		$sys_config["document_root"] . "classes/";		// system classes path
	$sys_config["images_path"]				=		$sys_config["document_root"] . "images/";		// system images path
	$sys_config["includes_path"]			=		$sys_config["document_root"] . "includes/";		// system includes path
	$sys_config["languages_path"]			=		$sys_config["document_root"] . "languages/";	// system languages path
	$sys_config["authentication_path"]		=		$sys_config["document_root"] . "authentication/";		// system menu path
	$sys_config["menu_path"]				=		$sys_config["document_root"] . "menu/";		// system menu path
	$sys_config["frame_path"]				=		$sys_config["document_root"] . "frame/";		// system menu path
	$sys_config["home_path"]				=		$sys_config["document_root"] . "home/";		// system home path
	$sys_config["admin_path"]				=		$sys_config["document_root"] . "admin/";		// system home path
	$sys_config["report_path"]				=		$sys_config["document_root"] . "report/";		// system report path
	$sys_config["inventory_path"]			=		$sys_config["document_root"] . "inventory/";		// system report path
	$sys_config["submission_path"]			=		$sys_config["document_root"] . "submission/";		// system report path
	$sys_config["upload_path"]				=		$sys_config["document_root"] . "upload/";		// system report path
	$sys_config["user_path"]				=		$sys_config["document_root"] . "user/";		// system report path
	$sys_config["dl_path"]					=		$sys_config["document_root"] . "download/";		// system report path
	$sys_config["support_path"]				=		$sys_config["document_root"] . "support/";		// system report path
	
	$sys_config["email_admin"]				= 		"cimsjkkp@mohr.gov.my";
	$sys_config["organization_name"]		= 		"Department of Occupational Safety and Health";
	$sys_config["organization_info"]		= 		"JKKP<br />Jabatan Keselamatan dan Kesihatan Pekerjaan, Malaysia";
	$sys_config["org_info"]					= 		"Director,<br />Chemical Management Division,<br />"
											.		"Department of Occupational Safety and Health<br />(Ministry of Human Resource)<br />"
											.		"Level 2, 3, 4, Block D3, Complex D,<br />"
											.		"Federal Government Administrative Centre<br />"
											.		"62530 W.P. Putrajaya<br /><br />E-mail: ". $sys_config['email_admin'];
	$sys_config["org_phone_no"]				=		"+603-8886 5343";
	$sys_config["org_fax_no"]				=		"+603-8889 2339";
	$sys_config['disclaimer_info']			= 		"For enquiry, please call: ". $sys_config["org_phone_no"] ." or email: ". $sys_config['email_admin'] 
											.		"<br />Copyright © 2016 DOSH<br />Disclaimer Statement<br />"
											.		"This site is best viewed with FireFox 3.6 above in 1024 × 768 screen resolution.";
	
	$sys_config["submit_start"]	= date("Y") ."-01-01";
	$sys_config["submit_end"]	= date("Y") ."-03-31";
		
	session_start();
	if(isset($_SESSION["user"])){
		define("_VALID_ACCESS","This is the only way to access the system");
	}
?>