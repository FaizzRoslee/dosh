<?php
	//============================================================
	// linuxhouse__20220710
	// FILE BEGIN 
	// filename: report01_all_to_excel.php
	// description: overall summary report convert to excel
	//============================================================
	include_once '../includes/sys_config.php';
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	
	//==========================================================
	if(isset($_SESSION['lang'])){ $fileLang = $_SESSION['lang'].'.php'; }
	else{ $fileLang = 'eng.php'; }
	include_once $sys_config['languages_path'].$fileLang;
	/*****/
	$uLevelID = $_SESSION["user"]["Level_ID"];
	if($uLevelID==5) exit();
	/*****/
	//==========================================================
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_date.php';
	include_once $sys_config["includes_path"]."arrayCommon.php";
	
	// including PHPExcel Library
	include_once $sys_config["includes_path"]."Classes/PHPExcel.php";
	
	// function to check for execution time limit and memory limits
	include_once $sys_config['includes_path'].'set_time_and_memory_limits.php';// checks execution time and memory limit
    include_once $sys_config['includes_path'].'func_shutdown_timeout.php';// checks timeout errors
    
    
    
    
    
    
    
	// make values global
	global $max_execution_time;
	global $memory_limit;
    // set maximum time execution
	ini_set('max_execution_time', $max_execution_time); // seconds
	// set memory limit
	ini_set('memory_limit', $memory_limit); // bytes or MB or 1G
	// check for timeouts
    register_shutdown_function('func_shutdown_timeout');
	
	
	
	
	$rptType = isset($_GET["rptType"]) ? $_GET["rptType"] : "";
	$lvlType = isset($_GET["lvlType"]) ? $_GET["lvlType"] : "";
	$chemType = isset($_GET["chemType"]) ? $_GET["chemType"] : "";
	$rptYear = isset($_GET["rptYear"]) ? $_GET["rptYear"] : "";
	$state = isset($_GET["state"]) ? $_GET["state"] : "";
	$comp = isset($_GET["comp"]) ? $_GET["comp"] : "";
	// echo info
	// echo $rptType.' - '.$lvlType.' - '.$chemType.' - '.$rptYear.' - '.$state.' - '.$comp;
	// exit();
	
	
	
	$colTypeName = "Type_Name". (($fileLang=="may.php")?"_may":"");
	$typeName = ($chemType!="") ? strtoupper(_get_StrFromCondition("tbl_Chemical_Type",$colTypeName,"Type_ID",$chemType)) : "";
	// echo info: chemical type; mixture or substance
	// echo $typeName;
	// exit();
	
	
	/*****/
	$lvlName = "";
	if($lvlType=="all"){
		foreach($arrLevel as $detail){
			$lvlName .= $detail["label"] .", ";
            // echo info: print supplier types; Importer, Importer, Manufacturer, Importer, Manufacturer, Importer & Manufacturer, etc
            // print_r($lvlName);
		}
		$lvlName = substr_replace($lvlName,"",-2);
		$levelType = "6,7,8";
	}
	else{
		$lvlName = _getLabel($lvlType,$arrLevel);
		$levelType = $lvlType;
		/*
		foreach($arrLevel as $detail){
			if($detail["value"]==$lvlType){
				$lvlName = $detail["label"]; 
				break; 
			}
		}
		*/
	}
	$levelTypes = explode(",",$levelType);
	// echo info: print level types saved in array
	// echo $levelTypes;
	// print_r($levelTypes);
	// exit();
	/*****/
	$rptName = "";
	foreach($arrReport as $detail){
		if($detail["value"]==$rptType) $rptName = strtoupper($detail["label"]); 
	}
	$titleRpt = strtoupper(_LBL_SUMMARY) .": ".$rptName." [". $typeName ."]";
	/*****/
	$titleSupp = strtoupper(_LBL_SUPPLIER_TYPE) .": ".strtoupper($lvlName);
	/*****/
	$titleYear = strtoupper(_LBL_YEAR) .": ". ($rptYear-1);
	$text_align_center = "text-align:center;";
	$text_align_right = "text-align:right;";
	/*****/
	// $rptFilename = "CIMS/RPT/04/01";
	// echo info: print summary, supplier type, year, file name
    // echo $titleRpt.' - '.$titleSupp.' - '.$titleYear.' - '.$rptFilename;
    // exit();
	/*****/
	
	
	
	// create PHPExcel object
	$objPHPExcel = new PHPExcel();
	// selecting active sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	
	
	// image paths variables
	$logo_image_path        = $sys_config['images_path'].'jata.gif';
	$logo_image_name        = 'CIMS';
	$logo_image_description = '';
	$heading                = '';
	$label                  = '';

	
	
	// start if chemical hazard exist
	function checkChemicalHazard($type,$chemical_id,$detail_id,$category_id){
		$classified = _get_StrFromCondition("tbl_chemical_classified", "Category_ID", "Chemical_ID",$chemical_id);
		$classified = str_replace(' ','',$classified);
		$arr_category = array();
		if(!empty($classified)){
			$arr_category = explode(',',$classified);
		} else {
			$hazard = _get_StrFromCondition("tbl_submission_detail", "Category_ID_$type", "Detail_ID",$detail_id);
			$hazard = str_replace(' ','',$hazard);
			if(!empty($hazard)){
				$arr_category = explode(',',$hazard);
			}			
		}
		
		if(in_array($category_id,$arr_category)){
			return true;
		} else {
			return false;
		}		
	}
	// end if chemical hazard exist
	

	
	// gets hazard category name
	function getClassCategory($category){
		$dataColumns = 'cat.Category_Name, cl.Class_Name';
		$dataTable = 'tbl_hazard_category cat';
		$dataJoin = array('tbl_hazard_classification cl' => 'cat.Class_ID = cl.Class_ID');
		$dataWhere = array(
					"AND cat.Category_ID" => "= ". quote_smart($category)
				);
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
		$class_category = '';
		foreach($arrayData as $data){
			$class_category = $data['Class_Name'] .', '. $data['Category_Name'];
		}
		return $class_category;
	}
	
	
	
    if($lvlType=='all') $lvlType = '6,7,8';
    // $lvlType = $levelTypes;
    // print_r($lvlType);
    // exit();
	
	
	
    // get category types
	$dataColumns1 = 'sd.Category_ID_ph, sd.Category_ID_hh, sd.Category_ID_eh, s.Submission_ID';
	$dataTable1 = 'tbl_Submission s';
	$dataJoin1 = array(
					"tbl_Submission_Detail sd" => "s.Submission_ID = sd.Submission_ID", 
					"sys_user_client uc" => "s.Usr_ID = uc.Usr_ID", 
					"sys_state st" => "uc.State_Reg = st.State_ID",
					"sys_user u" => "s.Usr_ID = u.Usr_ID"
				);
	$dataWhere1 = array(
					"AND s.Status_ID"=>"IN (31,32,33,41,42,43)"
					,"AND DATE_FORMAT(s.ApprovedDate,'%Y')"=>"= ".quote_smart($rptYear)
					,"AND uc.State_Reg" => "IN ($state)"
					,"AND s.isDeleted" => "= 0"
					,"AND u.Level_ID" => "IN ($lvlType)"
				);
	$dataOrder1 = 'st.State_ID';
	$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1,$dataOrder1); 
	// var_dump($arrayData1);
	// exit();
	
	
	
    // get submission ids
	$submission_ids = array();
	$physical = array();
	$health = array();
	$environment = array();
	foreach($arrayData1 as $data){
		$submission_ids[] = $data['Submission_ID'];
		/*
		if(!empty($data['Category_ID_ph'])){
			$arr_ph = explode(',', $data['Category_ID_ph']);
			$physical = array_merge($physical, $arr_ph);
		}
		if(!empty($data['Category_ID_hh'])){
			$arr_hh = explode(',', $data['Category_ID_hh']);
			$health = array_merge($health, $arr_hh);
		}
		if(!empty($data['Category_ID_eh'])){
			$arr_eh = explode(',', $data['Category_ID_eh']);
			$environment = array_merge($environment, $arr_eh);
		}			
		*/
	}
	// gets all submission ids
	// var_dump($submission_ids);
	// exit();
	
	

	// Remove duplicate values from an array
	$submission_ids = array_unique($submission_ids);
	// var_dump($submission_ids);
	// exit();
	
	
	
    // gets hazard types and hazard category and save them in an array
	$dataCols = 'Type_ID, Type_Name';
	$dataTbl = 'tbl_hazard_type';
	$dataJoin = array();
	$dataWhere = array("AND Active = 1" => "= 1");
	$dataOrder = 'Type_ID';
	$arrayDataType = _get_arrayData($dataCols,$dataTbl,$dataJoin,$dataWhere,$dataOrder);
	$arrHazard = array();
	$arrHazardType = array();
	foreach($arrayDataType as $type){
		$cols = 'cat.Category_Name, cl.Class_Name, cat.Category_ID';
		$table = 'tbl_hazard_category cat';
		$join = array('tbl_hazard_classification cl' => 'cat.Class_ID = cl.Class_ID');
		$where = array("AND cl.Type_ID" => "= ". quote_smart($type['Type_ID']), "AND cat.Active" => "= 1", "AND cat.isDeleted" => "= 0", "AND cl.Active" => "= 1", "AND cl.isDeleted" => "= 0");
		$order = 'cl.Class_Name, cat.Category_Name';
		$arrData = _get_arrayData($cols,$table,$join,$where,$order);
		foreach($arrData as $d){
			$arrHazard[$type['Type_ID']][$d['Category_ID']] = $d['Class_Name'] .', '. $d['Category_Name'];
		}
		$arrHazardType[$type['Type_ID']] = $type['Type_Name'];
	}
	// prints all raw hazard types
    // print_r($arrHazard);
	// exit();
	
	
	
	// $str .= '<tr>';
	// displays hazard types and hazard category in a <td colspan> and a normal <td> respectively
	// from the array that they we saved
	$hazard = '';
	foreach($arrHazard as $id => $h){
		$hazard_type = $arrHazardType[$id];
		
		$str .= 	'<td colspan="'. count($arrHazard[$id]) .'" class="header3">'. $hazard_type .'</td>';
		foreach($h as $cat_id => $cat_desc){
			$hazard .= '<td class="header4">'. $cat_desc .'</td>';
		}
	}
	// prints all raw hazard types in a <td>
    // print_r($hazard);
	// exit();




    // $str .= '</tr>';
	if(!empty($hazard)){
		$str .= '<tr>'
				. $hazard
				. '</tr>';
	}



	// NOTE: For each chemical, get the hazard type and the hazard category and display it 
	// under the rowspan for _LBL_HAZARD_CLASSIFICATION
	
	
	
    // for 'No'-column value
	$bil = 0;
	// for table contents
	$content = array();
	// $temp_arrayData = array();
	$arrayData = array();
	
	
	// excel file variables
	$row  = 0;
	$cell = '';
	
	
	// removes duplicated values from keys in array of arrays
    function array_key_value_unique1($arr, $key) {
        $uniquekeys = array();
        $output     = array();
        foreach ($arr as $item) {
            if (!in_array($item[$key], $uniquekeys)) {
                $uniquekeys[] = $item[$key];
                $output[]     = $item;
            }
        }
        return $output;
    }
    
    
	// removes duplicated values from keys in array of array of arrays
    function array_key_value_unique2($arr, $key) {
        $uniquekeys = array();
        $output     = array();
        foreach ($arr as $items) {
            foreach ($items as $item) {
                if (!in_array($item[$key], $uniquekeys)) {
                    $uniquekeys[] = $item[$key];
                    $output[]     = $item;
                }
            }
        }
        return $output;
    }
	
	
	// gets the Submission_Submit_ID, Company_Name, State_Name, Detail_ProductName, chemical_name 
	// for each submission id
	foreach($submission_ids as $sub){
		
		// $dataColumns = 's.Submission_Submit_ID, uc.Company_Name, st.State_Name, c.Chemical_Name, sd.Detail_TotalSupply, sd.Detail_TotalUse, sc.Chemical_ID, sd.Detail_ID, sd.Detail_ProductName';
		
		
		$dataColumns = 's.Submission_Submit_ID, uc.Company_Name, st.State_Name, c.Chemical_Name, su.Level_ID AS Level_Type, ct.Type_Name AS Chemical_Type, c.Chemical_CAS, s.ApprovedDate, sd.Detail_TotalSupply, sd.Detail_TotalUse, sc.Chemical_ID, sd.Detail_ID, sd.Detail_ProductName';
		
		$dataTable = 'tbl_submission_detail sd';
		
// 		$dataJoin = array(
// 					'tbl_submission s' => 'sd.Submission_ID = s.Submission_ID', 
// 					'tbl_submission_chemical sc' => 'sd.Detail_ID = sc.Detail_ID', 
// 					'tbl_chemical c' => 'sc.Chemical_ID = c.Chemical_ID', 
// 					"sys_user_client uc" => "s.Usr_ID = uc.Usr_ID",
// 					"sys_state st" => "uc.State_Reg = st.State_ID"
// 					);
					
		$dataJoin = array(
					'tbl_submission s' => 'sd.Submission_ID = s.Submission_ID', 
					'tbl_submission_chemical sc' => 'sd.Detail_ID = sc.Detail_ID', 
					'tbl_chemical c' => 'sc.Chemical_ID = c.Chemical_ID', 
					"sys_user_client uc" => "s.Usr_ID = uc.Usr_ID",
					"sys_state st" => "uc.State_Reg = st.State_ID",
					"tbl_chemical_type ct" => 'ct.Type_ID = s.Type_ID',
					"sys_user su" => "su.Usr_ID = s.Usr_ID"
					);
					
// 		$dataWhere = array(
// 					"AND s.Submission_ID" => "= $sub",
// 					"AND sd.isDeleted" => "= 0",
// 					// "AND s.Status_ID" => "IN (31,32)",
// 				);

		$dataWhere = array(
					"AND s.Submission_ID" => "= $sub",
					"AND sd.isDeleted" => "= 0",
					"AND s.Status_ID" => "IN (31)",
                    "AND ct.Type_ID" => "= $chemType",
				);
		// $dataOrder = 'Detail_ID ASC';
		// $dataOrder = 's.ApprovedDate DESC';
		$dataOrder = 's.ApprovedDate DESC, su.Level_ID ASC, uc.Company_Name ASC';
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere,$dataOrder);// gets an array of arrays
		// print_r($arrayData);
		// var_dump($arrayData);
		// exit();
		
		
		// remove all product name duplicates
// 		$parray = array();
// 		foreach($arrayData as $data)
//             print_r($data);
//             exit();
//             $parray[] = array_key_value_unique($data, "Detail_ProductName");
//         print_r($parray);
//         exit();
		
		
		// $content[] = array_merge($temp_arrayData, $arrayData); 
		// get an array of array of arrays
		$content[] = $arrayData; 
		// print_r($content);
		// var_dump($content);
		
	}
	// exit();
	
	
	// print content of array
	// print_r($arrayData);
	// exit();
	
	
	
    // print_r($content);
    // var_dump($content);
	// exit();
	
	
	// removes duplicated values from "Detail_ProductName".
	// and prints array 
    // $content = array_key_value_unique2($content, "Detail_ProductName");
    // print_r($content);
    // exit();
	
	
    // get data from database
    // $content = $arrayData;
	
    
    
    // 1. populate the data into file
	// checks is no record was returned when 'No'-column value = 0
	if(count($content) === 0){
	
        // merges cells A16 to M20
        $cell = 'A16:M20';
        $objPHPExcel->getActiveSheet()->mergeCells($cell);
        
        
        // add some text to it 
        $cell = 'A16';
        $no_record_message  = "\n"; // use "\n" and not '\n' to add newline in text
        $no_record_message .= "\n";
        $no_record_message .= "\n" . _LBL_NO_RECORD;
        $no_record_message .= "\n";
        $no_record_message .= "\n";
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $no_record_message);
        // Set the cell (or group of cells) to enable text wrap in those cells
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        
        // style to align text in the middle of cells
        $style_for_no_record = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '2F4F4F')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        // apply style to cell
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($style_for_no_record);
        
	} 
	else 
	{
        // set row value
        $row = 16;
        
        
        // give borders to data
        // style to align text in the middle of cells
        $data_style = array(
                // set borders
                'borders' => array(
                    'outline' => array( 
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    ),
                    'vertical' => array( 
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                ),
                // align text horizontally at the center
                'alignment' => array(
                    // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                )
         );
        
        
        // other variables
        $sub_total_qty_supply = 0;
        $sub_total_qty_use    = 0;
        $sum_total_qty_supply = 0;
        $sum_total_qty_use    = 0;
        
        
        // stores the rows used in loop.
        // this will be used to create the cell 
        // for sub-total and total by getting 
        // the last element and adding one to it.
        $rows_used_in_loop = array();

        // stores all product names in the form of a single
        // dimensional array
        $all_nos = array();
        $all_states = array();
        $all_submission_id = array();
        $all_prdt_nams = array();
        $all_ack_dates = array();
        $all_qty_supply = array();
        $all_qty_use = array();
        $all_company_names = array();
        // $all_level_types = array();
        // stores all unique product names
        $unique_prdt_nams = array();
        // stores product name counts
        $prdt_nams_counts = array();
        
        // get data from second array
		foreach($content as $datas)
		{

            // get all state names
            foreach($datas as $data)
            {
                // $all_states[] = $data['State_Name'];
                $all_states[] = ($data['State_Name']) ? ($data['State_Name']) : '-';
            }
            
            
            // get all company names
            foreach($datas as $data)
            {
                // $all_company_names[] = $data['Company_Name'];
                $all_company_names[] = ($data['Company_Name']) ? $data['Company_Name'] : '-';
            }
            
            
            // get all submission ids
            foreach($datas as $data)
            {
                // $all_submission_id[] = $data['Submission_Submit_ID'];
                $all_submission_id[] = ($data['Submission_Submit_ID']) ? $data['Submission_Submit_ID'] : '-';
            }
            
            
            // get a product names
            foreach($datas as $data)
            {
                // $all_prdt_nams[] = $data['Detail_ProductName'];
                $all_prdt_nams[] = ($data['Detail_ProductName']) ? ($data['Detail_ProductName']) : '-';
            }
            // print_r($prdt_nams);
            // exit();
            
            
            // get all acknowleged dates
            foreach($datas as $data)
            {
                // $all_ack_dates[] = $data['Chemical_Type'];
                $all_ack_dates[] = ($data['ApprovedDate']) ? func_ymd2dmy($data['ApprovedDate']) : '-';
            }
            
            
            // get quatity supplied
            foreach($datas as $data)
            {
                // $all_qty_supply = $data['Detail_TotalSupply'];
                $all_qty_supply[] = ($data['Detail_TotalSupply']) ? ($data['Detail_TotalSupply']) : 0;
            }
            
            
            // get quatity supplied
            foreach($datas as $data)
            {
                // $all_qty_use = $data['Detail_TotalUse'];
                $all_qty_use[] = ($data['Detail_TotalUse']) ? ($data['Detail_TotalUse']) : 0;
            }
            
            
		
            // get data from third array
		    foreach($datas as $data)
            {
		
            // increment counter
            // $bil++;
            
            // get data based on their keys
            // $state = ($data['State_Name']) ? $data['State_Name'] : '-';
            $company_name = ($data['Company_Name']) ? $data['Company_Name'] : '-';
            // $submission_submit_id = ($data['Submission_Submit_ID']) ? $data['Submission_Submit_ID'] : '-';
            
            // $level_type = _LBL_LEVEL_TYPE;
            // get level type name from database
            $level_type_name = _get_StrFromCondition('sys_level','Level_Name','Level_ID',$data['Level_Type']);
            $level_type = ($level_type_name) ? $level_type_name : '-';
            
            // $chem_type = _LBL_CHEMICAL_TYPE;
            $chem_type = ($data['Chemical_Type']) ? $data['Chemical_Type'] : '-';
            
            // $product_name = ($data['Detail_ProductName']) ? ($data['Detail_ProductName']) : '-';
            $chem_name = ($data['Chemical_Name']) ? nl2br($data['Chemical_Name']) : '-';
            
            // $cas_no = _LBL_CAS_NO;
            $cas_no = ($data['Chemical_CAS']) ? $data['Chemical_CAS'] : '-';
            
            $hzd_classification = _LBL_HAZARD_CLASSIFICATION;
            
            // $approved_date = _LBL_APPROVED_DATE;
            // $approved_date = ($data['ApprovedDate']) ? func_ymd2dmy($data['ApprovedDate']) : '-';
            
            // $qty_supply = _LBL_TOTAL_QUANTITY_SUPPLY .'('. _LBL_TONNE_YEAR .')';
            $qty_supply = $data['Detail_TotalSupply'];
            $sub_total_qty_supply += $qty_supply;
            // check level type and user id so that it matches 
            // the exclusion on the convert to pdf in report01_to_pdf.php file
            // if($lvlType!=7 && $uLevelID!=5){ 
                // $qty_supply = number_format($qty_supply, 1);
            // }
            $qty_supply = ($qty_supply) ? number_format($qty_supply, 1) : number_format($qty_supply=0, 1);

            // $qty_use = _LBL_TOTAL_QUANTITY_USE .'('. _LBL_TONNE_YEAR .')';
            $qty_use = $data['Detail_TotalUse'];
            $sub_total_qty_use += $qty_use;
            // check level type and user id so that it matches 
            // the exclusion on the convert to pdf in report01_to_pdf.php file
            // if($lvlType!=6 && $uLevelID!=5){ 
                // $qty_use = number_format($qty_use, 1);
            // }
            $qty_use = ($qty_use) ? number_format($qty_use, 1) : number_format($qty_use=0, 1);
            
            
            // set data from database into excel file
            // $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $bil );
            
            
            // $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $state );
            // $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $company_name );
            // $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $submission_submit_id );
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $level_type );
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $chem_type );
            
            
            // $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $product_name );
            
            // $cell = 'H'.$row;
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$row, $chem_name );
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$row, $cas_no );
            $objPHPExcel->getActiveSheet()->setCellValue('J'.$row, $hzd_classification );
            // $objPHPExcel->getActiveSheet()->setCellValue('K'.$row, $approved_date );
            // $objPHPExcel->getActiveSheet()->setCellValue('L'.$row, $qty_supply );
            // $objPHPExcel->getActiveSheet()->setCellValue('M'.$row, $qty_use );
			
			
            // apply style to cell
            $cell = 'A' . $row .':'. 'M'. $row;
            $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($data_style);
            
            // Set the cell (or group of cells) to enable text wrap in those cells
            $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
			
			
			// stores incremented row numbers used
			$rows_used_in_loop[] = $row;
			
			
			// increament row value
            $row++;
            
            }
            
		}
		// print array of rows used
		// print_r($rows_used_in_loop);
		// exit();
        
        
        
        
        
        
        
function add_merge_repeated_values(PHPExcel $objPHPExcel, $starting_row, $repeated_value, $column)
{
		// add product name data now and merge the neccessary rows
        // $row = 16;
        // $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, '$product_name' );
        // $cell = 'G' . $row . ':' . 'G26';
        // $objPHPExcel->getActiveSheet()->mergeCells($cell);
        // write column 6
        // $row = 16;
        $row = $starting_row;
        $occurance = 0;
        $all_occurance = array();
        // echo 'all_product_names: <br><br>';
        // print_r($all_prdt_nams);
		// exit();
		
		
		$all_prdt_nams = $repeated_value;
		// echo '<br><br><br> unique_product_names: <br><br>';
        $unique_prdt_nams = array_unique($all_prdt_nams);
        // print_r($unique_prdt_nams);
        // exit();
		
        // echo '<br><br><br> product_names_counts: <br><br>';
        $prdt_nams_counts = array_count_values($all_prdt_nams);
        // print_r($prdt_nams_counts);
        // exit();
		
        // for each unique value, write the value in a cell in excel file 
        // and then merge columns for the values that repeats
        // echo '<br><br><br> write product_names: <br><br>';
        foreach($unique_prdt_nams as $upn)
        {
            // increment counter
            // $bil++;
        
        
            // get array keys and values.
            // keys = names which are same with the ones in $product_names_counts.
            // value = number of times each name repeats itself in $product_names_counts.
            $key_id = $upn;
            if(array_key_exists($key_id, $prdt_nams_counts)) 
            {
                $occurance = $prdt_nams_counts[$key_id];
                // echo '<br><br> key = ' . $key_id . ' value = ' . $prdt_nams_counts[$key_id];
            } 
                
            // number of times each name repeated in the array
            // echo '<br> occurance for: ' . $upn . ' = ' . $occurance;
            
            // $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $bil );
                
            // write first column in excel file
            // $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $upn );
            $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $upn );
            // echo '<br> wrote: ' . $upn;
                    
            // get rows for mergeing cells
            $row1 = $row;
            $row2 = ($row + $occurance) - 1; 
                    
            // merges cells
            // $cell = 'A' . $row1 . ':' . 'A' . $row2;
            // $objPHPExcel->getActiveSheet()->mergeCells($cell);
            
            
            // $cell = 'G' . $row1 . ':' . 'G' . $row2;
            $cell = $column . $row1 . ':' . $column . $row2;
            $objPHPExcel->getActiveSheet()->mergeCells($cell);
            // echo '<br> merged: ' . $cell;
            
            
            // style to align text in the middle of cells
            $data_style = array(
                    // align text horizontally at the center
                    'alignment' => array(
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                    )
            );
            
            // apply style to cell
            // $cell = 'G' . $row1 .':'. 'G'. $row2;
            $cell = $column . $row1 .':'. $column. $row2;
            $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($data_style);
            
                    
            // set new row value
            $row = $row2;
                    
            // increament row value
            $row++;     
                
        }
        // print_r(array_sum($all_occurance));
        // print_r(count($unique_prdt_nams));
        // exit();
}





function write_no_and_any(PHPExcel $objPHPExcel, $starting_row, $repeated_value, $column, $no_column)
{
		// add product name data now and merge the neccessary rows
        // $row = 16;
        // $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, '$product_name' );
        // $cell = 'G' . $row . ':' . 'G26';
        // $objPHPExcel->getActiveSheet()->mergeCells($cell);
        // write column 6
        // $row = 16;
        $row = $starting_row;
        $occurance = 0;
        $all_occurance = array();
        // echo 'all_product_names: <br><br>';
        // print_r($all_prdt_nams);
		// exit();
		
		
		$all_prdt_nams = $repeated_value;
		// echo '<br><br><br> unique_product_names: <br><br>';
        $unique_prdt_nams = array_unique($all_prdt_nams);
        // print_r($unique_prdt_nams);
        // exit();
		
        // echo '<br><br><br> product_names_counts: <br><br>';
        $prdt_nams_counts = array_count_values($all_prdt_nams);
        // print_r($prdt_nams_counts);
        // exit();
		
        // for each unique value, write the value in a cell in excel file 
        // and then merge columns for the values that repeats
        // echo '<br><br><br> write product_names: <br><br>';
        foreach($unique_prdt_nams as $upn)
        {
            // increment counter
            $bil++;
        
        
            // get array keys and values.
            // keys = names which are same with the ones in $product_names_counts.
            // value = number of times each name repeats itself in $product_names_counts.
            $key_id = $upn;
            if(array_key_exists($key_id, $prdt_nams_counts)) 
            {
                $occurance = $prdt_nams_counts[$key_id];
                // echo '<br><br> key = ' . $key_id . ' value = ' . $prdt_nams_counts[$key_id];
            } 
                
            // number of times each name repeated in the array
            // echo '<br> occurance for: ' . $upn . ' = ' . $occurance;
            
            // $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $bil );
            $objPHPExcel->getActiveSheet()->setCellValue($no_column.$row, $bil );
                
            // write first column in excel file
            // $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $upn );
            $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $upn );
            // echo '<br> wrote: ' . $upn;
                    
            // get rows for mergeing cells
            $row1 = $row;
            $row2 = ($row + $occurance) - 1; 
                    
            // merges cells
            // $cell = 'A' . $row1 . ':' . 'A' . $row2;
            // $objPHPExcel->getActiveSheet()->mergeCells($cell);
            $cell = $no_column . $row1 . ':' . $no_column . $row2;
            $objPHPExcel->getActiveSheet()->mergeCells($cell);
            
            
            // $cell = 'G' . $row1 . ':' . 'G' . $row2;
            $cell = $column . $row1 . ':' . $column . $row2;
            $objPHPExcel->getActiveSheet()->mergeCells($cell);
            // echo '<br> merged: ' . $cell;
            
            
            // style to align text in the middle of cells
            $data_style = array(
                    // align text horizontally at the center
                    'alignment' => array(
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                    )
            );
            
            // apply style to cell
            // $cell = 'G' . $row1 .':'. 'G'. $row2;
            $cell = $column . $row1 .':'. $column. $row2;
            $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($data_style);
            
                    
            // set new row value
            $row = $row2;
                    
            // increament row value
            $row++;     
                
        }
        // print_r(array_sum($all_occurance));
        // print_r(count($unique_prdt_nams));
        // exit();
}
		
		
		
		
		
		
		
		
		
        // adds unique no the column having its heading
        foreach($rows_used_in_loop as $rows_used)
        {
            // increment counter
            $bil++;
        }
        
        // adds unique states in the column having its heading
        add_merge_repeated_values($objPHPExcel, 16, $all_states, 'B');
        
        // adds unique states in the column having its heading
        add_merge_repeated_values($objPHPExcel, 16, $all_company_names, 'C');
        
        // write_no_and_state(PHPExcel $objPHPExcel, $starting_row, $repeated_value, $column, $no_column)
        // write_no_and_state($objPHPExcel, 16, $all_states, 'B', 'A');
        
        // adds unique company names in the column having its heading
        // add_merge_repeated_values($objPHPExcel, 16, $all_company_names, 'C');
        
        // adds unique submission id in the column having its heading
        add_merge_repeated_values($objPHPExcel, 16, $all_submission_id, 'D');
		
		// adds unique product names in the column having its heading
        // add_merge_repeated_values($objPHPExcel, 16, $all_prdt_nams, 'G');
        write_no_and_any($objPHPExcel, 16, $all_prdt_nams, 'G', 'A');
        
		// adds unique acknoledged date in the column having its heading
        add_merge_repeated_values($objPHPExcel, 16, $all_ack_dates, 'K');
        
        // adds unique qty_supply in the column having its heading
        add_merge_repeated_values($objPHPExcel, 16, $all_qty_supply, 'L');
		
        // adds unique qty_use in the column having its heading
        add_merge_repeated_values($objPHPExcel, 16, $all_qty_use, 'M');
		
		
		
		
		
		
		
		
		// get row number for sub-total and total
		$row_sub_total = end($rows_used_in_loop) + 1;
		$row_total = $row_sub_total + 1;
		
        // merge cells to add sub-total
        // merges cells
        $cell = 'A' . $row_sub_total . ':' . 'J' .$row_sub_total;
        $objPHPExcel->getActiveSheet()->mergeCells($cell);
        
        // merge cells to add total
        // merges cells 
        $cell = 'A' . $row_total . ':' . 'J' .$row_total;
        $objPHPExcel->getActiveSheet()->mergeCells($cell);
        
        // get labels for sub-total and total
        $label_sub_total = _LBL_SUBTOTAL;
        $label_total     = _LBL_TOTAL;
        
        // set labels for sub-total and total into excel file
        $objPHPExcel->getActiveSheet()
                    ->setCellValue('K'.$row_sub_total, $label_total )
                    ->setCellValue('K'.$row_total, $label_sub_total )
                    ;
                    
//         $sub_total_qty_supply = 0;
//         $sub_total_qty_use    = 0;
//         $sum_total_qty_supply = 0;
//         $sum_total_qty_use    = 0;
                    
        // get values for total and sub-total
        $sum_total_qty_supply += $sub_total_qty_supply;
        $sum_total_qty_use    += $sub_total_qty_use;
                    
        // set values for sub-total and total into excel file
        $objPHPExcel->getActiveSheet()
                    ->setCellValue('L'.$row_sub_total, number_format($sum_total_qty_supply, 1) )
                    ->setCellValue('L'.$row_total, number_format($sum_total_qty_supply, 1) )
                    ->setCellValue('M'.$row_sub_total,  number_format($sum_total_qty_use, 1) )
                    ->setCellValue('M'.$row_total, number_format($sum_total_qty_use, 1) )
                    ;

        // apply style to cells
        $cell = 'A' . $row_sub_total .':'. 'M'. $row_sub_total;
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($data_style);
        $cell = 'A' . $row_sub_total .':'. 'M'. $row_total;
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($data_style);
        // Set the cell (or group of cells) to enable text wrap in those cells
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
		
        // set column width 
        // so that data will be displayed well inside the cells
        // sets width to fixed value
        // $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);

        // automatically sets wdith based on text text length
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(30);
        
        // get column widths after setting them to auto auto size
        // $objPHPExcel->getActiveSheet()->calculateColumnWidths('G');
        // $logo_column_dimen = $objPHPExcel->getActiveSheet()->getColumnDimension('G')->getWidth();
        // $int_logo_column_dimen = (int)$logo_column_dimen;
        
        
        // 2. make table headers
        //Get the active sheet and set values to it
        $cell = 'A15';
        $label = _LBL_NO;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        // set text to wrap text in next line in cell if it is too long
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'B15';
        $label = _LBL_STATE;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'C15';
        $label = _LBL_COMPANY_NAME;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'D15';
        $label = _LBL_SUBMISSION_ID;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'E15';
        $label = _LBL_LEVEL_TYPE;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'F15';
        $label = _LBL_CHEMICAL_TYPE;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'G15';
        $label = _LBL_PRODUCT_NAME;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'H15';
        $label = _LBL_CHEMICAL_NAME;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'I15';
        $label = _LBL_CAS_NO;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'J15';
        $label = _LBL_HAZARD_CLASSIFICATION;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'K15';
        $label = _LBL_APPROVED_DATE;
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'L15';
        $label = _LBL_TOTAL_QUANTITY_SUPPLY . "\n" . '(' . _LBL_TONNE_YEAR . ')';
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        $cell = 'M15';
        $label = _LBL_TOTAL_QUANTITY_USE . "\n" . '(' .  _LBL_TONNE_YEAR . ')';
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $label);
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        // style to align text in the middle of cells
        $table_header_style = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '2F4F4F')
            ),
            // background color: gainsboro
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'DCDCDC')
            ),
            // set borders
            'borders' => array(
                'allborders' => array( 
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            // align text horizontally at the center
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        // apply style to cell
        $cell = 'A15:M15';
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($table_header_style);
        
        
        // 3. make table title or heading
        // add some text to it 
        $heading  = "\n"; // use "\n" and not '\n' to add newline in text
        $heading .= "\n";
        $heading .= "\n" . strtoupper(_LBL_DOSH);
        $heading .= "\n" . strtoupper(_LBL_CIMS);
        $heading .= "\n" . $titleRpt;
        $heading .= "\n" . $titleSupp;
        $heading .= "\n" . $titleYear;
        $heading .= "\n";
        $heading .= "\n";
        $cell = 'A1';
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $heading);
        // Set the cell (or group of cells) to enable text wrap in those cells
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
        
        // merge the title
        // merges cells A1 to M14
        $cell = 'A1:M14';
        $objPHPExcel->getActiveSheet()->mergeCells($cell);
        
        // style to align text in the middle of cells
        $table_heading_style = array(
            'font' => array(
                'bold' => true,
                'color' => array('rgb' => '2F4F4F')
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        // apply style to cell
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($table_heading_style);
        
        
        // 4. add logo to table heading
        // Create new picture object and insert picture
        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName($logo_image_name);
        $objDrawing->setDescription($logo_image_description);
        $objDrawing->setPath($logo_image_path);
        $objDrawing->setHeight(100);
        // $objDrawing->setWidthAndHeight(100, 100);
        $cell = 'G1';
        $objDrawing->setCoordinates($cell);
        // add some margin between a cell and the image
        // $objDrawing->setOffsetX(100);  // x-axis or horizontal
        // $x_axis = $int_logo_column_dimen * 3;
        $objDrawing->setOffsetX(30);  // x-axis or horizontal
        $objDrawing->setOffsetY(3000);    // y-axis or vertical
        
        $objDrawing->setResizeProportional(true);
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());// save image

	}

	
    
    // get file name 
	// $fileName = str_replace(' ', '_', _LBL_REPORT_OVERALL_DETAILED) .".xls";
	$date_today = date("d-m-Y"); // gets date today in the form d-m-Y
	$time_today = date('H:i:s'); // gets time in 24 hours format
	$time_today = str_replace(':', '.', $time_today); // replace : with .
	// adds at between date and time
	$at_value   = 'at';
	// $date_at_time      = $date_today . ' ' . $at_value . ' ' . $time_today;
	$date_time      = $date_today . ' ' . $time_today;
	// adds name to date and time
	// $name_date_at_time = _LBL_REPORT_OVERALL_DETAILED . ' ' . $date_at_time;
	$name_date_time = _LBL_REPORT_OVERALL_DETAILED . ' ' . $date_time;
	// $name_date_at_time = str_replace(' ', '_', $name_date_at_time);
	$name_date_time = str_replace(' ', '_', $name_date_time);
	// forms file name with file extension
	$file_extension    = '.xlsx';
	// $fileName          = $name_date_at_time . $file_extension;
	$fileName          = $name_date_time . $file_extension;

	
	// Save Excel 2007 file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    // clean any content of the output buffer
	// ob_end_clean(); 
	ob_clean(); 
	
	
	// pass the file header information to browser
	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-Disposition: attachment; filename=$fileName"); // pass 'filename' here
	header("Expires: 0");
	header("Cache-Control: cache must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	
	
	// Write file to the browser
	$objWriter->save('php://output'); 
?>
