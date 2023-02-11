<?php
/**************************************************************************************************
		 
    Update 20211101 : LinuxHouse is currently reponsible for the maintenance of this CIMS
                      system ( 11 years old ). 

                      Our contract ends approx 202310.

                      With regards to this  file, pls note that we setup an alternative
                      smtp server to facilitate quick testinng 
  
	**************************************************************************************************/	
	 
	// Email Settings
	$mail_config = array();
	

	 
	// Just in case we need to relay to a different server,
	// provide an option to use external mail server.
	// list mail server http://www.arclab.com/products/amlc/list-of-smtp-and-pop3-servers-mailserver-list.html
	
	// $mail_config["smtp_mode"] 	= "enabled"; 				// enabled or disabled
	// $mail_config["smtp_auth"] 	= true;
	// $mail_config["smtp_secure"] = "ssl";						// ssl: port 465; tls: port 587
	
	// $mail_config["smtp_host"] 	= "smtp.gmail.com"; 		// localhost / smtp.gmail.com / null
	// $mail_config["smtp_port"] 	= 465; 						// 465: set the SMTP port for the GMAIL server / null 587
	// $mail_config["smtp_username"] = "saiful@ikram.com.my";
	// $mail_config["smtp_password"] = "";	
	
	// $mail_config["smtp_mode"] 	= "disabled"; 					// enabled or disabled
	// $mail_config["smtp_auth"] 	= "";
	// $mail_config["smtp_secure"] = "";
	
	// $mail_config["smtp_host"] 	= "localhost";					// localhost / smtp.gmail.com / null
	// $mail_config["smtp_port"] 	= ""; 							      // 465: set the SMTP port for the GMAIL server / null
	// $mail_config["smtp_username"] = "";
	// $mail_config["smtp_password"] = "";


	$mail_config["debug"] = false;                                // enables SMTP debug information (for testing) { value 0,1,2 }	
	$mail_config["from_name"] 	= "Admin CIMS";                    // from email name
	$mail_config["from_email"] 	= $sys_config["email_admin"];     // from email address "cimsjkkp@mohr.gov.my"  
     
	
	$mail_config["smtp_mode"] 	 = "enabled"; 						 // enabled or disabled
	$mail_config["smtp_auth"]  	= false;							     // true if email and password were supplied
	$mail_config["smtp_secure"] = "";
	
	$mail_config["smtp_host"] 	= "postmaster.mygovuc.gov.my"; 		// localhost / smtp.gmail.com / null 10.17.237.55
	$mail_config["smtp_port"] 	= 25; 								// 465: set the SMTP port for the GMAIL server / null
	$mail_config["smtp_username"] = ""; 							// jkkp.mohr@1govuc.gov.my
	$mail_config["smtp_password"] = "";								// masukkan password

// linuxhouse_20211108
// debug smtp settings 

if( 1 === 2 ){                                                
                                                              // These credentials IS ONLY for DEBUGGING. Production server uses
                                                              // client ip whitelisting only and NOT smtp auth   for relaying emails             
                                                              // 1 === 1 => Debugging Only else set  1 === 2 


    $mail_config["debug"] = 3;                                // enables SMTP debug information (for testing) { value 0,1,2 }	
    $mail_config["from_name"] 	= "Admin CIMS";                // from email name
    $mail_config["from_email"] 	= "daemon_cims@linux-house.net";  // from email address    
        
    
    $mail_config["smtp_mode"] 	 = "enabled"; 						// enabled or disabled
    $mail_config["smtp_auth"]  	= true;							     // true if email and password were supplied
    $mail_config["smtp_secure"] = "tls";
    
  
    $mail_config["smtp_host"]    	= "mail.linux-house.net"; 		       // localhost / smtp.gmail.com / null 10.17.237.55
    $mail_config["smtp_port"] 	   = 587; 							                     // 465: set the SMTP port for the GMAIL server / null
    $mail_config["smtp_username"] = "daemon_cims@linux-house.net"; 		// jkkp.mohr@1govuc.gov.my
    $mail_config["smtp_password"] = "gxy99ffgghhyyrr88";							  	   // masukkan password


     

}



	
?>
