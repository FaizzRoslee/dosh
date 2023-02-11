 <?php
 

	//****************************************************************/
	// linuxhouse_20220603
	// filename: download1.php
	// description: downloads pdf files for 
	//              /inventory/chemicalView.php
	//****************************************************************/
	include_once '../includes/sys_config.php';
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	//==========================================================
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
        
        // reads and download file
        readfile($filePath);
        exit;
    }
    else{
        $msg = "File does not exist on server";
        $formBack = "chemicalList.php"; $menu = "1"; 
        echo '
            <script language="Javascript">
                alert("'.$msg.'");
                parent.left.location.href="'.$sys_config['frame_path'].'left_inventory.php?menu='.$menu.'";
                location.href="'.$formBack.'";
            </script>
                ';
        exit();
    }
}
else
{   
    $msg = "There was a problem downloading this file";
    $formBack = "chemicalList.php"; $menu = "1"; 
    echo '
         <script language="Javascript">
			alert("'.$msg.'");
			parent.left.location.href="'.$sys_config['frame_path'].'left_inventory.php?menu='.$menu.'";
			location.href="'.$formBack.'";
        </script>
			';
    exit();
}
