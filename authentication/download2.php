 <?php
 

	//****************************************************************/
	// linuxhouse_20220603
	// filename: download2.php
	// description: downloads pdf files for 
	//              /authentication/chemicalView_Search.php
	//****************************************************************/
	include_once '../includes/sys_config.php';
	//==========================================================
// 	if (!defined("_VALID_ACCESS"))
// 	{	// denied direct access to the page
// 		header("HTTP/1.0 404 Not Found");
// 		exit();	// process terminated
// 	}
// 	==========================================================
// 	if(isset($_SESSION['lang'])){ $fileLang = $_SESSION['lang'].'.php'; }
// 	else{ $fileLang = 'eng.php'; }
// 	include_once $sys_config['languages_path'].$fileLang;
// 	==========================================================
//     include_once $sys_config["includes_path"]."db_config.php";
// 	include_once $sys_config["includes_path"]."func_master.php";


if(!empty($_GET['file'])){
    // get file name
    $fileName  = basename($_GET['file']);
    // folder path where file was uploaded
    $filePath  = $sys_config["upload_path"]."/chemical-prop-pdf/".$fileName;
    // $filePath  = $sys_config["upload_path"]."/chemical/".$fileName;
    
    if(!empty($fileName) && file_exists($filePath)){
        //define header
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$fileName");
        // header("Content-Type: application/zip");
        header("Content-Type: application/pdf");// content type header for pdf files
        header("Content-Transfer-Encoding: binary");
        
        //reads and download file
        readfile($filePath);
        exit;
    }
    else{
        // alert message and redirect after click ok then go to another page
        // $message = "File does not exist on server";
        // echo "<script>alert('$message');window.location.href='http://devcims.dosh.gov.my/';</script>";
        // exit;
        
        
        // alert message and redirect after click ok then go back to previous page
        $message = "File does not exist on server";
        echo "<script type='text/javascript'>alert('$message');history.go(-1);</script>";
        exit;
    }
}
else
{
    // alert message and redirect after click ok then go back to previous page
    $message = "There was a problem downloading this file";
    echo "<script type='text/javascript'>alert('$message');history.go(-1);</script>";
    exit;
}
