<?php
/**************************************************************************************************
		  File name		  :		/languages/eng.php
		  Date created	:		30 November 2010
		  Description		:		english language (ISO 639 Language Codes: may/msa)

    Update 20211101 : LinuxHouse is currently reponsible for the maintenance of this CIMS
                      system ( 11 years old ). 

                      Our contract ends approx 202310.

                      With regards to this labels file, pls note that not all labels
                      are present here and they are actually scattered throughout the project folder.

                      Labels here also contain formatting info which shouldnt be the case.
  
	**************************************************************************************************/	
	// common
	define("_LBL_LANGUAGE","Language");
	define("_LBL_MALAY","Bahasa Malaysia");
	define("_LBL_ENGLISH","English Language");
	
	define("_LBL_LOGIN","Login");
	define("_LBL_USERID","User ID");
	define("_LBL_PASSWORD","Password");
	define("_LBL_REGISTER","Register");
	define("_LBL_FORGOT_PASSWORD","Forgot Password");

	define("_LBL_YES","Yes");
	define("_LBL_NO","No");
	define("_LBL_OK","OK");
	define("_LBL_CANCEL","Cancel");

	define("_LBL_ADD","Add");
	define("_LBL_REMOVE","Remove");
	define("_LBL_DELETE","Delete");
	define("_LBL_SAVE","Save");
	define("_LBL_EDIT","Edit");
	define("_LBL_SEARCH","Search");
	define("_LBL_PRINT","Print");
	define("_LBL_EXPORT","Export");
	define("_LBL_BACK","Back");
	define("_LBL_RESET","Reset");
	define("_LBL_SUBMIT","Submit");
	define("_LBL_INFO_DETAIL","Details information");
	define("_LBL_EMPTY","Empty");
	define("_LBL_GO","Proceed");
	define("_LBL_VIEW","View");
	define("_LBL_RENEW","Renewal");
	define("_LBL_CLOSE","Close");
	define("_LBL_BIL","No");
	
	define("_LBL_SELECT","-SELECT-");
	define("_LBL_CHOOSE","SELECT");
	
	define("_LBL_NUMBER","No.");
	define("_LBL_STATUS","Status");
	define("_LBL_ACTIVITY","Activity");
	define("_LBL_LOADING_FROM_SERVER","Loading data from server");
	define("_LBL_ACTIVE","Active");
	define("_LBL_INACTIVE","Inactive");
	
	define("_LBL_SEARCH_ALL_COL","Search all columns:");
	
	define("_LBL_MSG_CANNOT_EMPTY","Field cannot empty : ");
	define("_LBL_CODE_EXIST","Code already exist. Please enter another code");

	// day
	define("_DAY_0","Sunday");
	define("_DAY_1","Monday");
	define("_DAY_2","Tuesday");
	define("_DAY_3","Wednesday");
	define("_DAY_4","Thursday");
	define("_DAY_5","Friday");
	define("_DAY_6","Saturday");

	// month
	define("_MONTH_01","January");
	define("_MONTH_02","February");
	define("_MONTH_03","March");
	define("_MONTH_04","April");
	define("_MONTH_05","May");
	define("_MONTH_06","June");
	define("_MONTH_07","July");
	define("_MONTH_08","August");
	define("_MONTH_09","September");
	define("_MONTH_10","October");
	define("_MONTH_11","November");
	define("_MONTH_12","December");

	// confirmation message
	define("_ADD_CONFIRMATION","Are you sure you want to add this record ?");
        define("_ADD_SUBMISSION_CHECKING","Are you sure you want to Proses?");
	define("_EDIT_CONFIRMATION","Are you sure you want to save the changes ?");
	define("_DELETE_CONFIRMATION","Are you sure you want to delete this record ?");
	define("_CANCEL_CONFIRMATION","You will lose all changes. Are you sure you want to cancel ?");

	// failure message
	define("_DELETE_FAILURE","Sorry, system was unable to delete this record !!!");

	// tips
	define("_SEARCH_TIPS","You only need to fill in any field that you want to search.");
	define("_REQUIRE_TIPS","Items mark with <font color=\"#FF0000\">*</font> are required.");
	define("_CHECK_TIPS","Plese click given checkbox to make changes.");

	// page
	define("_PAGE","Page: ");
	define("_OF_TOTAL_PAGE","of <TotalPage>");
	
	//head
	define("_LBL_GUEST","Guest");
	
	//menu utama
	define("_LBL_MENU_HOME","HOME");
	define("_LBL_MENU_ADMIN","ADMINISTRATION");
	define("_LBL_MENU_INVENTORY","INVENTORY");
	define("_LBL_MENU_SUBMISSION","SUBMISSION");
	define("_LBL_MENU_REPORT","REPORT");
	define("_LBL_MENU_PROFILE","PROFILE");
	define("_LBL_MENU_LOGOUT","LOGOUT");
	define("_LBL_PAUTAN","OTHER LINK");
	define("_LBL_PAUTAN_INFO","OTHER LINK");
	
	//login
	define("_LBL_SEARCH_REG_CHEM","Search for Chemical Information");
	
	define("_LBL_LOGIN_STAFF","Staff");
	define("_LBL_LOGIN_USER","Importer/Manufacturer");
	
	define("_LBL_MSG_USERNAME_EMPTY","Please Enter Your User ID");
	define("_LBL_MSG_STAFFID_EMPTY","Please Enter Your Staff ID");
	define("_LBL_MSG_PASSWORD_EMPTY","Please Enter Your Password");
	define("_LBL_MSG_NOT_ACTIVE","User is Not Active. Contact Administrator or Verify your Registration");
	define("_LBL_MSG_NOT_REG","User is Not Register. Contact Administrator or Please Register First");
	define("_LBL_MSG_NOT_VALID","Invalid Login Information");
	
	//registration
	define("_LBL_REGISTRATION_TYPE","Registration Type");
	define("_LBL_REGISTRATION_NO","DOSH Registration No");
	define("_LBL_REGISTRATION","Registration");
	define("_LBL_REGISTRATION_ID","Registration ID");
	
	define("_LBL_COMPANY_INFO","Company's Detail");
	define("_LBL_CONTACT_DETAIL","Contact Detail");
	define("_LBL_CONTACT_PERSON","Contact Person");
	define("_LBL_COMPANY","Company");
	define("_LBL_COMPANY_NO","Company No");
	define("_LBL_COMPANY_NAME","Company Name");
	define("_LBL_REG_ADDRESS","Registered Address");
	define("_LBL_POSTAL_ADDRESS","Postal Address");
	define("_LBL_SAME_AS_ABOVE","Same as above");
	define("_LBL_ADDRESS","Address");
	define("_LBL_CITY","City");
	define("_LBL_POSTCODE","Postcode");
	define("_LBL_CONTACT_NO","Contact No");
	define("_LBL_PHONE_NO","Phone No");
	define("_LBL_FAX_NO","Fax No");
	define("_LBL_EMAIL","Email");
	define("_LBL_MOBILE_NO","Mobile No");
	
	define("_LBL_USER_ID_PASSWORD","User ID and Password");
	define("_LBL_RETYPE_PASSWORD","Retype Password");
	define("_LBL_PASSWORD_DOESNOT_MATCH","Password Does Not Match");
	define("_LBL_ANSWER_IF_FORGOT_ID_PASSWORD","If you forgot your User ID and Password");
	define("_LBL_SECURITY_QUESTION","Security Question");
	define("_LBL_YOUR_ANSWER","Your Answer");
	
	define("_LBL_VERIFICATION_CODE","Verification Code");
	define("_LBL_SECURITY_CODE","Security Code");
	
	//admin===============================================================================
	define("_LBL_GENERAL","General");
	define("_LBL_USER_LIST","User List");
	define("_LBL_USER_VIEW","User View");
	define("_LBL_USER","Users");
	define("_LBL_STAFF","DOSH Staff");
	define("_LBL_SUPP_IMPORTER","Importer");
	define("_LBL_INDUSTRIAL_MANUF","Manufacturer");
	define("_LBL_SUPP_MANUF","Importer & Manufacturer");
	define("_LBL_ADMINISTRATION","Administration");
	define("_LBL_STATE","State");
	define("_LBL_PHY_HAZARD","Physical Hazard");
	define("_LBL_PHY","Physical");
	define("_LBL_HEALTH_HAZARD","Health Hazard");
	define("_LBL_HEALTH","Health");
	define("_LBL_ENV_HAZARD","Environment Hazard");
	define("_LBL_ENV","Environment");
	define("_LBL_CLASSIFICATION","Classification");
	define("_LBL_CATEGORY","Category");
	
	define("_LBL_HAZARD_CLASSIFICATION","Hazard Class");
	define("_LBL_HAZARD_CATEGORY","Hazard Category");
	
	define("_LBL_PRECAUTIONARY_STATEMENT","Precautionary Statement");
	define("_LBL_PREVENTION","Prevention");
	define("_LBL_RESPONSE","Response");
	define("_LBL_STORAGE","Storage");
	define("_LBL_DISPOSAL","Disposal");
	
	define("_LBL_OTHERS","Others");
	define("_LBL_HAZARD_PICTOGRAM","Hazard Pictogram");
	define("_LBL_HAZARD_STATEMENT","Hazard Statement");
	define("_LBL_SIGNAL_WORD","Signal Word");
	
	define("_LBL_AUDIT_TRAIL","Audit Trail");
	define("_LBL_RESET_PASSWORD","Reset Password");
	define("_LBL_NEW_USER","New User");
	//============================================================================
	define("_LBL_STAFF_LIST","Staff List");
	define("_LBL_STAFF_ID","Staff ID");
	define("_LBL_NAME","Name");
	define("_LBL_DESIGNATION","Designation");
	define("_LBL_LEVEL","Level");
	define("_LBL_HEAD","Head");
	//=========================================
	define("_LBL_TITLE_STAFFID","This will be used for <strong>Login</strong>");
	define("_LBL_TITLE_HEAD","This will be used for <strong>Submission</strong> process");
	//=========================================
	define("_LBL_STATE_LIST","State List");
	define("_LBL_STATE_CODE","State Code");
	define("_LBL_STATE_NAME","State Name");
	//=========================================
	define("_LBL_TYPE_NAME","Hazard Type");
	//=========================================
	define("_LBL_PICTOGRAM_DESC","Pictogram Description");
	define("_LBL_PICTOGRAM_UPLOAD","Upload Pictogram");
	define("_LBL_PICTOGRAM_FILE","Pictogram File");
	define("_LBL_PICTOGRAM_FOR","Pictogram for");
	define("_LBL_NO_PICTOGRAM","No Pictogram");
	//=========================================
	define("_LBL_STATEMENT_CODE","H-Code");
	define("_LBL_STTMNT_CODE","Statement Code");
	define("_LBL_STATEMENT_DESC","Hazard Statement");
	//=========================================
	define("_LBL_SIGNAL","Signal");
	//=========================================
	define("_LBL_PRECAUTION_TYPE","Precaution Type");
	define("_LBL_PRECAUTION_CODE","Code");
	define("_LBL_PRECAUTION_DESC","Precautionary Statement");
	//============================================================================
	define("_LBL_HELP_STATE_CODE_TITLE","State Code Format");
	define("_LBL_HELP_STATE_CODE","1) Only number was accepted<br />2) Maximum can be is 2");
	define("_LBL_HELP_PICTOGRAM_TITLE","Pictogram Format");
	define("_LBL_HELP_PICTOGRAM","[.gif] [.png] [.jpg] [.jpeg] [.bmp] [.pjpeg]");
	define("_LBL_HELP_APPEAR_IF_TITLE","Appear Only If");
	define("_LBL_HELP_APPEAR_IF","1) Hazard Type has been selected; and<br />2) There is record in the database");
	//=========================================
	define("_LBL_HELP_COMP_NO_TITLE","Company No Format");
	define("_LBL_HELP_COMP_NO","Company Registration Number with SSM");
	define("_LBL_HELP_EMAIL_TITLE","Email Format");
	define("_LBL_HELP_POSTCODE_TITLE","Postcode Format");
	define("_LBL_HELP_POSTCODE","1) Only number was accepted<br />2) Maximum can be is 5");
	define("_LBL_HELP_PHONE_TITLE","Phone No Format");
	define("_LBL_HELP_FAX_TITLE","Fax No Format");
	define("_LBL_HELP_MOBILE_TITLE","Mobile No Format");
	define("_LBL_HELP_CHOOSE","Field below is only applicable if either one of this choices is chosen.");
	//============================================================================
	define("_LBL_SEARCH_BY","Search By");
	define("_LBL_SEARCH_BY_CAS","CAS No");
	define("_LBL_SEARCH_BY_NAME","Product Name");
	define("_LBL_SEARCH_BY_SYNONYM","Synonyms");
	define("_LBL_SEARCH_TITLE_CAS","CAS No");
	define("_LBL_SEARCH_TITLE_NAME","Product Name");
	define("_LBL_SEARCH_TITLE_SYNONYM","Synonyms");
	//=========================================
	define("_LBL_INVENTORY","Inventory");
	define("_LBL_LIST_CHEMICAL","Chemical List");
	define("_LBL_LIST_SYNONYM","Synonyms List");
	define("_LBL_CHEMICAL","Chemical");
	define("_LBL_SYNONYM","Synonyms");
	define("_LBL_SYNONYM_NAME", "Synonyms");
	//=========================================
	define("_LBL_CAS_NO","CAS No");
	define("_LBL_CHEMICAL_NAME","Chemical Name");
	define("_LBL_CHEM_IUPAC","IUPAC Name");
	define("_LBL_CHEM_WEIGHT","Weight");
	define("_LBL_CHEM_FORMULA","Formula");
	define("_LBL_CHEM_MOL","Mol");
	define("_LBL_CHEM_STRUCTURE","Structure");
	//=========================================
	define("_LBL_PEL","PEL");
	define("_LBL_PEL_DESC","Permissible Exposure Limits"." ("._LBL_PEL.")");
	//=========================================
	define("_LBL_MAS","Malaysia");
	//=========================================
	define("_LBL_RECOMMENDATION","Recommendation");
	define("_LBL_JUSTIFICATION","Justification");
	//=========================================
	define("_LBL_BEI","Biological Exposure Index (BEI)");
	define("_LBL_VAM","Validated Analytical Method (VAM)");
	//=========================================
	define("_LBL_DESCRIPTION","Descriptions");
	define("_LBL_DESCRIPTION_FOR","Descriptions for");
	//=========================================
	define("_LBL_PROP","Properties");
	define("_LBL_USES","Uses");
	define("_LBL_APPEARANCE", "Appearance");
	//=========================================
	
	
	// linuxhouse_20220524
    // start add-chemical-list-fields-task
    
	define("_LBL_FLASH_POINT","Flash Point");
	define("_LBL_PHYSICAL_HAZARD","Physical Hazard");
	// health hazard label already exist
	define("_LBL_ENVIRONMENTAL_HAZARD", "Environmental Hazard");
	define("_LBL_UPLOAD_PDF_FILE", "Upload PDF File");
	define("_LBL_DOWNLOAD_FILE", "Download File");
	define("_LBL_DOWNLOAD_PDF_FILE", "Download PDF File");
	define("_LBL_HELP_PDF_TITLE","PDF Format");
	define("_LBL_HELP_PDF","[.pdf]");
	
    // end add-chemical-list-fields-task
    
    
	//=========================================	
	define("_LBL_PROP_DESCRIPTION", _LBL_PROP ." ". _LBL_DESCRIPTION );
	define("_LBL_PROP_USES", _LBL_PROP ." and ". _LBL_USES);
	define("_LBL_PROP_APPEARANCE", _LBL_PROP ." ". _LBL_APPEARANCE);
	//=========================================
	define("_LBL_BPTC", "Boiling Point &deg;C (BptC)");
	define("_LBL_MPTC", "Melting Point &deg;C (MptC)");
	define("_LBL_ODOUR", "Odour");
	define("_LBL_VAP_PRESSURE", "Vapour Pressure");
	define("_LBL_SOLUBILITY", "Solubility");
	//=========================================
	define("_LBL_CLASSIFIED", "Hazard Classification");
	define("_LBL_NO_OF_SUPPLIER", "No Of Supplier");
	define("_LBL_CLASSIFIED_CHEM", "Classified Chemicals");
	define("_LBL_UNCLASSIFIED_CHEM", "Unclassified Chemicals");
	define("_LBL_H_CODE_LABELLING", "H-Code Labelling");
	define("_LBL_LABELLING", "Labelling");
	define("_LBL_CODE", "Code");
	define("_LBL_CLASSIFICATION_CODE", _LBL_CLASSIFICATION." "._LBL_CODE);
	//============================================================================
	define("_LBL_LIST","List");
	define("_LBL_REPORT","Reports");
	define("_LBL_TYPE","Type");
	define("_LBL_REPORT_TYPE",_LBL_REPORT ." ". _LBL_TYPE);
	define("_LBL_YEAR","Year");
	define("_LBL_LEVEL_TYPE",_LBL_LEVEL ." ". _LBL_TYPE);
	define("_LBL_RESULT","Result");
	//============================================================================
	define("_LBL_SUBMISSION","Submission");
	define("_LBL_ALL_SUBMISSION","All ". _LBL_SUBMISSION);
	define("_LBL_ALL_LIST","All ". _LBL_LIST);
	define("_LBL_SUBMISSION_ID",_LBL_SUBMISSION .' ID');
	define("_LBL_NEW","New");
	define("_LBL_NEW_SUBMISSION",_LBL_NEW .' '. _LBL_SUBMISSION);
	define("_LBL_RENEW_SUBMISSION",_LBL_SUBMISSION .' '. _LBL_RENEW);
	define("_LBL_CHEMICAL_TYPE",_LBL_CHEMICAL .' '. _LBL_TYPE);
	define("_LBL_SEARCH_CHEMICAL",_LBL_SEARCH .' '. _LBL_CHEMICAL);
	define("_LBL_COMPOSITION","Composition");
	define("_LBL_REMARK","Remarks");
	define("_LBL_NO_OF_SUBSTANCE","No of Substance");
	define("_LBL_NO_OF_CHEMICAL","No of Chemical");
	define("_LBL_NO_OF_MIXTURE","No of Mixture");
	define("_LBL_NO_OF_INGREDIENT","No of Ingredient");
	define("_LBL_INGREDIENT","Ingredient");
	define("_LBL_ID_NUMBER","ID Number (if any)");
	define("_LBL_PRODUCT_NAME","Product ". _LBL_NAME);
	//define("_LBL_TRADE_PRODUCT","Product/". _LBL_CHEMICAL .' '. _LBL_NAME);
 define("_LBL_TRADE_PRODUCT", _LBL_CHEMICAL .' '. _LBL_NAME);
	define("_LBL_PHYSICAL_FORM","Physical Form");


 // LinuxHouse - 20211107
 // start 

	define("_LBL_PHY_HAZARD_CLASS",_LBL_PHY_HAZARD .' '. _LBL_CLASSIFICATION); 
	define("_LBL_PHY_HAZARD_CLASS_FOOTNOTE", "Select Physical Hazard Category");  

	define("_LBL_HEALTH_HAZARD_CLASS",_LBL_HEALTH_HAZARD .' '. _LBL_CLASSIFICATION);
	define("_LBL_HEALTH_HAZARD_CLASS_FOOTNOTE", "Select Health Hazard Category"); 

	define("_LBL_ENV_HAZARD_CLASS",_LBL_ENV_HAZARD .' '. _LBL_CLASSIFICATION);
	define("_LBL_ENV_HAZARD_CLASS_FOOTNOTE", "Select Environment Hazard Category"); 

	define("_LBL_QUANTITY_IMPORTED_FOOTNOTE", "Enter Tonnage Per Year"); 
 define("_LBL_QUANTITY_MANUFACTURED_FOOTNOTE", "Enter Tonnage Per Year"); 

 // end 

	define("_LBL_QUANTITY","Quantity");
	define("_LBL_TOTAL_QUANTITY_SUPPLY","Quantity Imported");
	define("_LBL_LIST_SUPPLIED_ENTITY","Chemical User(s) (Company)");
	define("_LBL_TOTAL_QUANTITY_USE","Quantity Manufactured");
	define("_LBL_LIST_SUPPLIER_ENTITY","Chemical User(s) (Company)");
	define("_LBL_TONNE_YEAR","tonne/year");
	define("_LBL_NUMBER_ONLY","Numbers Only");
	define("_LBL_MAXLENGTH_2","Maxlength 2 Characters");
	define("_LBL_MAXLENGTH_6","Maxlength 6 Characters");
	define("_LBL_JUST_CLICK_ADDEDIT","Just Click To Add or Edit");
	define("_LBL_FILL_UP","Fill up no of substance/mixture to be submit. Please ensure that only hazardous chemicals with quantity of 1 metric tonne and above are submitted in the inventory.");
	define("_LBL_CAN_BE_2","Can be Chemical Name or CAS No");
	define("_LBL_CAN_BE_3","Can be Chemical Name/CAS No/IUPAC Name");
	define("_LBL_LOCALLY_MANUFACTURED","Locally Manufactured");
	
	
	
	
    // linuxhouse_20220614
    // start penambahbaikan - 8_ruangan chemical source - v004: add 'No Information' label
    define("_LBL_NO_INFORMATION","No Information");
    // end penambahbaikan - 8_ruangan chemical source - v004: add 'No Information' label
    
    
    
    
	define("_LBL_IMPORTED","Imported");
	define("_LBL_LOCALLY_MANUFACTURED_IMPORTED","Locally Manufactured and Imported");
	define("_LBL_MANUFACTURED","Manufactured");
	define("_LBL_BOTH_IMPORTED_MANUFACTURED","Both (". _LBL_IMPORTED ."/". _LBL_MANUFACTURED .")");
	define("_LBL_CHEMICAL_SOURCE","Chemical Source");
	define("_LBL_CHEMICAL_DETAILS","Chemical Details");
	//=========================================
	define("_LBL_WAITING_CHECKING","Waiting for Checking");
	define("_LBL_WAITING_APPROVAL","Waiting for Acknowledgement");
	//=========================================
	define("_LBL_DETAIL","Details");
	define("_LBL_HISTORY","History");
	define("_LBL_DATE","Date");
	define("_LBL_UPDATE","Update");
	define("_LBL_RECORD","Record");
	define("_LBL_RECORD_BY_DATE",_LBL_RECORD. " By/". _LBL_DATE);
	define("_LBL_RECORD_UPDATE",_LBL_RECORD ." ". _LBL_UPDATE);
	define("_LBL_RECORD_BY",_LBL_RECORD. " By");
	define("_LBL_RECORD_DATE",_LBL_RECORD ." ". _LBL_DATE);
	define("_LBL_SUBMITTED","Submitted");
	define("_LBL_ACKNOWLEGE","Acknowledged");
	define("_LBL_ACKNOWLEGED","Acknowledged");
	define("_LBL_ACTION","Action");
	define("_LBL_APPROVED","Acknowledged");
	define("_LBL_APPROVAL","Acknowledgement");
	define("_LBL_APPROVED_DATE",_LBL_ACKNOWLEGED ." ". _LBL_DATE);
	define("_LBL_ACKNOWLEGED_LIST",_LBL_ACKNOWLEGED ." ". _LBL_LIST);
	define("_LBL_CHECKING","Checking");
	define("_LBL_CHECKED","Checked");
	define("_LBL_CHECKED_BY",_LBL_CHECKED ." By");
	define("_LBL_CHECKED_DATE",_LBL_CHECKED ." ". _LBL_DATE);
	define("_LBL_NO_RECORD","No Record(s) Found");
	define("_LBL_SUBMISSION_DATE",_LBL_SUBMISSION ." ". _LBL_DATE);
	define("_LBL_SHOW_HIDE_COLUMN","Show/Hide columns");
	define("_LBL_EXPIRED_DATE","Expired ". _LBL_DATE);
	define("_LBL_EXPIRED","Expired");
	define("_LBL_REJECTED","Rejected");
	define("_LBL_REJECTED_LIST",_LBL_REJECTED ." ". _LBL_LIST);
	define("_LBL_SEND_BACK","Send back to Supplier");
	define("_LBL_REJECTED_SEND_BACK",_LBL_REJECTED ." (". _LBL_SEND_BACK .")");
	define("_LBL_REASON","Reason/Notes");
	define("_LBL_REMARK_REASON_REJECT",_LBL_REMARK ." & ". _LBL_REASON ." ". _LBL_REJECTED);
	//============================================================================
	define("_LBL_CIMS","Chemical Information Management System");
	define("_LBL_DOSH","Department of Occupational Safety and Health");
	define("_LBL_MOHR","Ministry of Human Resource");
	define("_LBL_DOSH_INFO", _LBL_DOSH ."<br />(". _LBL_MOHR .")<br />Level 4,  Block D4, Complex D,<br />Federal Government Administrative Centre<br />62530 W.P. Putrajaya<br /><br />Phone no: 03-8886 5343<br />Fax no: 03-8890 1315<br />E-mail: ". $sys_config['email_admin']);
	define("_LBL_REPORT_OVERALL_DETAILED","Overall Detailed Report");
	define("_LBL_REPORT_OVERALL_SUMMARY","Overall Summary Report");
	define("_LBL_REPORT_OVERALL","Overall Report");
	define("_LBL_SUMMARY","Summary");
	define("_LBL_TOTAL","Total");
	define("_LBL_SUBTOTAL","Sub Total");
	define("_LBL_GRANDTOTAL","Grand Total");
	define("_LBL_CONVERT_TO_PDF","Convert To PDF");
	define("_LBL_PAGE","Page");
	define("_LBL_AS_OF","As of");
	define("_LBL_PRINTED_ON","Printed On");
	define("_LBL_COMPANY_TYPE","Company "._LBL_TYPE);
	define("_LBL_SUPPLIER_NAME","Supplier "._LBL_NAME);
	define("_LBL_SUPPLIER_TYPE","Supplier "._LBL_TYPE);
	define("_LBL_MODULE","Module");
	define("_LBL_IP_ADDRESS","IP Address");
	//============================================================================
	//datatables
	define("_LBL_PROCESSING","Processing....");
	define("_LBL_SHOW","Show");
	define("_LBL_SHOWING","Showing");
	define("_LBL_ENTRIES","Entries");
	define("_LBL_TOTAL_ENTRIES","Total Entries");
	define("_LBL_NO_MATCHING_RECORD","No matching records found");
	define("_LBL_TO","to");
	define("_LBL_OF","of");
	define("_LBL_FILTERED_FROM","Filtered From");
	define("_LBL_FIRST","First");
	define("_LBL_PREVOIUS","Previous");
	define("_LBL_NEXT","Next");
	define("_LBL_LAST","Last");
	//============================================================================
	define("_LBL_USER_PROFILE","User Profile");
	define("_LBL_PROFILE","Profile");
	define("_LBL_CHANGE_PASSWORD","Change ". _LBL_PASSWORD);
	define("_LBL_CURRENT_PASSWORD","Current ". _LBL_PASSWORD);
	define("_LBL_NEW_PASSWORD",_LBL_NEW ." ". _LBL_PASSWORD);
	define("_LBL_PASSWORD_DOESNOT_MATCH_CURRENT","Does not match with ". _LBL_CURRENT_PASSWORD);
	define("_LBL_PASSWORD_DOESNOT_MATCH_NEW","Does not match with ". _LBL_NEW_PASSWORD);
	define("_LBL_CHEMICAL_USERS","Chemical Users (Company)");
	//============================================================================
	define("_LBL_HOME","Home");
	define("_LBL_NOTIFICATION","Notification");
	define("_LBL_NEW_CHEMICAL",_LBL_NEW ." ". _LBL_CHEMICAL);
	define("_LBL_REGISTERED_CHEMICAL","Registered Chemical");
	define("_LBL_REGISTERED","Registered");
	define("_LBL_CERTIFICATION","Certification");
	//============================================================================
	define("_LBL_SEND_MAIL_SUCCESS","Email was sent successfully to");
	define("_LBL_SEND_MAIL_FAILED","Email cannot be send");
	define("_LBL_NO_EMAIL","No Sender/Receiver Email");
	//============================================================================
	define("_LBL_INFO_PAGE","Info Page");
	define("_LBL_INFO_FOR","Info For");
	define("_LBL_INFO_DESC","Info Descriptions");
	define("_LBL_IMAGE","Image");
	define("_LBL_IMAGE_ADD","Add Image");
	define("_LBL_NO_IMAGE","No Image");
	define("_LBL_IMAGE_FOR","Image For");
	define("_LBL_IMAGE_URL","Image URL");
	define("_LBL_DELETE_IMAGE","Delete Image");
	define("_LBL_FILENAME","Filename");
	define("_LBL_UPLOAD","Upload");
	define("_LBL_IMAGE_SIZE","Image Size: Max 1MB");
	define("_LBL_IMAGE_BROWSE","Browse Image");
	define("_LBL_IMAGE_NOTE","*Note: To use the above image. Copy and Paste the 'Image URL' and put inside the 'Image URL' when you want to insert the image in the editor below.");
	
	//============================================================================
	define("_LBL_HAZARD_CLASSIFICATION_EMPTY","Hazard Class can not be left blank. If no, select No Hazard Classification");
	define("_LBL_DATE_NOLONGER","Date (if no longer supplied/imported)");
	define("_LBL_NOLONGER","No Longer Supplied / Imported");
	define("_LBL_NOTVALID_EMAIL","Not valid E-mail");
	define("_LBL_DISCLAIMER","Disclaimer");
	define("_LBL_TERM_COND","Terms and Conditions");
	define("_LBL_DISCLAIMER_DETAIL","By submitting the above information, I have verified that all the content are correct. I understand that the document will be printed EXACTLY as it appears here, that I cannot make any changes once submitted and that I assume all responsibility in complying with the CLASS Regulations.");
	//============================================================================
	//for certificate
	define("_LBL_CERT_SUBJ_INFO","This certificate is to acknowledge that the company is registered with Chemical Information Management Sistem, Department of Occupational Safety and Health.<br />This certificate is not transferable.<br /><br />For reference, please contact:<br /><br />");
	define("_LBL_CERT_ORG_INFO","Director,<br />Chemical Management Division,<br />Department of Occupational Safety and Health<br />(Ministry of Human Resource)<br />Level 4, Block D4, Complex D,<br />Federal Government Administrative Centre<br />62530 W.P. Putrajaya<br />Phone no: ". $sys_config["org_phone_no"] ."<br />Fax no: ". $sys_config["org_fax_no"] ."<br /><br />E-mail: ". $sys_config['email_admin']);
	define("_LBL_CERT_TITLE","Registration Certificate");
	define("_LBL_CERT_REG_ID",_LBL_REGISTRATION_ID);
	define("_LBL_CERT_REG_DATE","Registration Date");
	define("_LBL_CERT_ACTIVATION_DATE","Activation Date");
	define("_LBL_CERT_COMP_NAME",_LBL_COMPANY_NAME);
	define("_LBL_CERT_COMP_REG_NO","Company Registration No");
	define("_LBL_CERT_TYPE_SUPPIER","Type of Supplier");
	//===================================================
	define("_LBL_CERT_SUBMISSION_INFO",_LBL_SUBMISSION. " Info");
	define("_LBL_CERT_SUBMISSION_ID",_LBL_SUBMISSION_ID);
	define("_LBL_CERT_SUBMISSION_DATE",_LBL_SUBMISSION_DATE);
	define("_LBL_CERT_EXPIRED_DATE",_LBL_EXPIRED_DATE);
	define("_LBL_CERT_CHEMICAL_TYPE",_LBL_CHEMICAL_TYPE);
	define("_LBL_CERT_ACKNWLG_DATE",_LBL_APPROVED_DATE);
	define("_LBL_CERT_RENEW_SUBMISSION",_LBL_RENEW_SUBMISSION);
	define("_LBL_CERT_SUBMISSION",_LBL_SUBMISSION);
	define("_LBL_CERT_SUBMISSION_TITLE","Inventory Acknowledgement Certificate");
	define("_LBL_CERT_RESUBMISSION_TITLE",_LBL_RENEW_SUBMISSION ." ". _LBL_APPROVED);
	define("_LBL_CERT_SUBMISSION_SUBJ1","This certificate is to acknowledge that your");
	define("_LBL_CERT_SUBMISSION_SUBJ2","of chemical inventory as above mention was ACKNOWLEDGED by the ". _LBL_DOSH .".<br />Please refer next page for details.<br /><br />For reference, please contact:<br /><br />");
	//============================================================================
	define("_LBL_PARAMETER","Parameter");
	define("_LBL_SUBMISSION_ACKNOWLEDGE","Submission Acknowledged");
	define("_LBL_SUPLLIER","Supplier");
	define("_LBL_LIST_SUPPLIER",_LBL_LIST ." of ". _LBL_SUPLLIER);
	define("_LBL_REQUIRED_FIELD","This field is required.");
	define("_LBL_CHECK_ONE","Please check atleast one.");
	define("_LBL_CHECKED_SAVING","Check for saving/Uncheck for ignoring.");
	//============================================================================
	define("_LBL_SELECT_ALL","Select All");
	define("_LBL_PENDING_ACKNOWLEDGEMENT","Pending Acknowledgement");
	
	define("_LBL_DL_MANUAL","Download Manual");
	define("_LBL_USER_MANUAL","USER MANUAL");
	
	define("_LBL_EMEL_NOTE1","[* To receive registration activation email from CIMS]");
	define("_LBL_EMEL_NOTE2","[* To receive any notification email from CIMS]");
	
	define("_LBL_UPLOAD_SUBMISSION",_LBL_UPLOAD .' '. _LBL_SUBMISSION);
	define("_LBL_SUBSTANCE","Substance");
	define("_LBL_MIXTURE","Mixture");
	define("_LBL_OR","Or");
	define("_LBL_REFERENCE","Reference");
	define("_LBL_DOWNLOAD","Download");
	define("_LBL_FORMAT","Format");
	define("_LBL_HS_CODE","HS Code");
	define("_LBL_FORMULA_IMAGE","Formula Image");
	define("_LBL_CWC","CWC");
	define("_LBL_CWC_LIST","CWC List");
	define("_LBL_FREQUENTLY_ASK_Q","Frequently Asked Question");
	define("_LBL_FAQ","FAQ");
	define("_LBL_BULK_SUBMISSION","Bulk Submission");
	define("_LBL_UPLOAD_BULK_SUBMISSION","Upload Bulk Submission");
	define("_LBL_SCHEDULE","Schedule");
	define("_LBL_GUIDELINE","Guideline");
	define("_LBL_GUIDELINE_STEP","
			<ol type=\"1\">
				<li>Download Reference File & Format accordingly (Mixture slightly different from Substance)</li>
				<li>Follow example on how to fill the data</li>
				<li>Save the file</li>
				<li>Select & Upload by Submission Type</li>
			</ol>
	");
	
	define("_LBL_SUPPORT","Support");
	define("_LBL_CONTACT_US","Contact Us");
	define("_LBL_FEEDBACK_FROM","Feedback Form");
	define("_LBL_FEEDBACK","Feedback");
	define("_LBL_SUBJECT","Subject");
	define("_LBL_MESSAGE","Message");
	define("_LBL_ENQUIRIES","Enquiries");
	define("_LBL_COMMENTS","Comments");
	define("_LBL_COMPLAINTS","Complaints");
	define("_LBL_QUESTION","Question");
	define("_LBL_ANSWER","Answer");
	define("_LBL_THANK_FEEDBACK","Thank you for your Feedback.");
	define("_LBL_FAILED_FEEDBACK","Feedback cannot be send.");
	define("_LBL_PREVIEW","Preview");
	
	define("_LBL_USED_AS_COMMUNICATION","will be used as Communication");
	define("_LBL_DUPLICATION_SUCCESS","Duplication Success");
	define("_LBL_DUPLICATION_FAILED","Duplication Failed");
	define("_LBL_DUPLICATE_SUBMISSION","Duplicate Submission");
	define("_LBL_DUPLICATE_SUBMISSION_CONFIRMATION","Are you sure you want to Duplicate this Submission?");
	define("_LBL_DO_NOT_LEAVE_EMPTY","Do not leave these empty:");
	
	define("_LBL_ALL","All");
	define("_LBL_ADD_ALL",_LBL_ADD ." ". _LBL_ALL);
	define("_LBL_REMOVE_ALL",_LBL_REMOVE ." ". _LBL_ALL);
	
	define("_LBL_CHEMICAL_SUBMITTED_SCREENED","Chemical submitted will be screened by officer before included in the list");
	
	define("_LBL_PERSON_IN_CHARGE","Person In Change");
	define("_LBL_CONTACT_INFO","Contact Info");
	define("_LBL_ENFORCEMENT_CONTACT_INFO","Enforcement Contact Info");
	define("_LBL_CHEMICAL_INFO_SEARCH","Chemical Information Search");
	define("_LBL_NOT_EXCEED_20","Should not exceed 20");
	define("_LBL_PROCEED_RESET","You are about to reset the password of these selected User(s).\\nAre you sure to proceed?");
	define("_LBL_REPLY","Reply");
	define("_LBL_REPLIED","Replied");
	define("_LBL_INFO_WILL_BE_SEND_TO_SENDER","will be sent to sender's email");	
	
	define("_LBL_DISCLAIMER_INFORMATION","CIMS V2 <br/>For enquiry, please call: ". $sys_config["org_phone_no"] ." or email: ". $sys_config["email_admin"] 
										. "<br />Copyright &copy; 2016 DOSH<br />Disclaimer: The Government of Malaysia shall not be liable for any loss or damage caused by the usage of any information obtained from this site.<br />"
										. "This site is best viewed with the latest version of web browser in 1024 x 768 screen resolution.");
	define("_LBL_BY","By");
	define("_LBL_RECORD_DELETE_SUCCESS","Record deleted successfully");
	define("_LBL_RECORD_DELETE_FAILED","Record failed to delete");
	define("_LBL_RECORD_DELETE_CONFIRMATION","Are you sure you want to delete these record(s)?");
	
	define("_LBL_MSG_REQUIRED_DEFAULT_MANUFACTURED","<i><b>(Please insert 0 if chemical is not manufactured)</b></i>"); // (Sila isikan 0 sekiranya bahan kimia tidak dikilang)
	define("_LBL_MSG_REQUIRED_DEFAULT_IMPORTED","<i><b>(Please insert 0 if chemical is not imported)</b></i>"); // (Sila isikan 0 sekiranya bahan kimia tidak import)
	
	define("_LBL_VISITOR", "Visitors");
	define("_LBL_TODAY_VISITOR", "Today Visitors");
	define("_LBL_TOTAL_VISITOR", "Total Visitors");
	define("_LBL_CONTACT_TO_CHANGE", "Contact Admin to change ");
	define("_LBL_RELATED_LINKS", "Related Links");
	define("_LBL_LAST_UPDATE", "Last Update");
	define("_LBL_PLEASE_INSERT_CHEMICAL_MANUALLY", "Please insert Chemical manually");
	define("_LBL_ADD_INGREDIENT", _LBL_ADD ." " ._LBL_INGREDIENT);
	define("_LBL_REMOVE_INGREDIENT", _LBL_REMOVE ." " ._LBL_INGREDIENT);
 define("_LBL_REMOVE_MIXTURE", _LBL_REMOVE ." " ._LBL_MIXTURE);
 define("_LBL_REMOVE_SUBSTANCE", _LBL_REMOVE ." " ._LBL_SUBSTANCE);
	define("_LBL_SUBMISSION_SUBMIT_DATE", _LBL_SUBMISSION ." Submit ". _LBL_DATE);
	define("_LBL_SUBMIT_START_DATE", "Start ". _LBL_DATE);
	define("_LBL_SUBMIT_END_DATE", "End ". _LBL_DATE);
 define("_LBL_NOOF_CHEMICAL","No Of Chemicals");
?>
