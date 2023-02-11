<?php
	//
	// wrote total, sub total and grand total
	//
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
	
	
	/*****/
	$lvlName = "";
	if($lvlType=="all"){
		foreach($arrLevel as $detail){
			$lvlName .= $detail["label"] .", ";
            // echo info: print supplier types; Importer, Importer, Manufacturer, Importer, Manufacturer, Importer & Manufacturer, etc
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
	/*****/
	// $rptFilename = "CIMS/RPT/04/01";
	// echo info: print summary, supplier type, year, file name
	/*****/
	
	
	
	// image paths variables
	$logo_image_path        = $sys_config['images_path'].'jata.gif';
	$logo_image_name        = 'CIMS';
	$logo_image_description = '';
	$heading                = '';
	$label                  = '';
	
	
	
	// declare and intialise variables
	$grand_totalSupply = 0;
	$grand_totalUse = 0;
	
    
    
    $dateStart 	= _get_StrFromCondition("sys_SubmitDate","Date_Start","YEAR(Date_Last)",$rptYear);
    $dateLast 	= _get_StrFromCondition("sys_SubmitDate","Date_Last","YEAR(Date_Last)",$rptYear);
	/*****/
	$arr_state = explode(',',$state);// converts state to array
	/*****/
	
	
	
    $countExist = 0;
    $showExport = 0;
	
	
	
    // functions
    // writes data in excel file
    // PHPExcel $objPHPExcel = PHPExcel Object
    // $cell = cell like A(n) where n = 1, 2, 3,... etc
    function write_data_to_excel(PHPExcel $objPHPExcel, $cell, $text)
    {
        // get the active sheet and set values to it
        $objPHPExcel->getActiveSheet()->setCellValue($cell, $text);
    }
    
    // merges cells in excels file
    // $cell_range = range of cells to be merged
    function merge_cells(PHPExcel $objPHPExcel, $cell_range)
    {
        $objPHPExcel->getActiveSheet()->mergeCells($cell_range);
    }
    
    // set column width in excels file
    // $column = letter of excel file columns
    // $size   = integer of the column size
    function set_column_dimension(PHPExcel $objPHPExcel, $column, $size)
    {
        $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setWidth($size);
    }
    
    // set column width in excels file
    // $column = letter of excel file columns
    function auto_set_column_dimension(PHPExcel $objPHPExcel, $column)
    {
        $objPHPExcel->getActiveSheet()->getColumnDimension($column)->setAutoSize(true);
    }
    
    // wraps and automatically fits data in cell
    function wrap_data_in_cell(PHPExcel $objPHPExcel, $cell)
    {
        $objPHPExcel->getActiveSheet()->getStyle($cell)->getAlignment()->setWrapText(true);
    }
    
    // adds style to cell
    // $cell here can also take range of cells
    function add_style_to_cell(PHPExcel $objPHPExcel, $cell, $style)
    {
        $objPHPExcel->getActiveSheet()->getStyle($cell)->applyFromArray($style);
    }
    
    // forms heading
    // $row = integer
    function form_heading(PHPExcel $objPHPExcel, $array_of_columns, $array_of_headings, $row)
    {
        // heading row value
        $heading_row = $row;
        
        // intialise varaibles
        $cell = '';
        $label = '';
        
        // counts columns and headings
        $count_columns  = count($array_of_columns);
        
        // loops through the 'letters of column'
        for($x = 0; $x < $count_columns; $x++)
        {
            // forms cell by concatenating column and and row
            $cell = $array_of_columns[$x] . $heading_row;
            
            // forms label
            $label = $array_of_headings[$x];
            
            // writes data to cell
            write_data_to_excel($objPHPExcel, $cell, $label);
            // set text to wrap text in next line in cell if it is too long
            wrap_data_in_cell($objPHPExcel, $cell);
        }
    }
    
    // saves file
    // $excel_file_version = e.g 'Excel2007' saves file as Excel 2007 file
    function save_and_download_file(PHPExcel $objPHPExcel, $excel_file_version, $file_name)
    {
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $excel_file_version);
        // clean any content of the output buffer
        ob_clean(); 
        
        // pass the file header information to browser
        header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=$file_name"); // pass 'filename' here
        header("Expires: 0");
        header("Cache-Control: cache must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: private",false);
        
        // Write file to the browser
        $objWriter->save('php://output'); 
    }
    
    // forms the name of file
    // $file_extension = e.g '.xlsx' for excel files
    function form_file_name($name, $file_extension)
    {
        // get file name 
        $date_today = date("d-m-Y"); // gets date today in the form d-m-Y
        $time_today = date('H:i:s'); // gets time in 24 hours format
        $time_today = str_replace(':', '.', $time_today); // replace : with .
        // adds date and time
        $date_time      = $date_today . ' ' . $time_today;
        $name_date_time = $name . ' ' . $date_time;
        $name_date_time = str_replace(' ', '_', $name_date_time);
        $fileName       = $name_date_time . $file_extension;
        
        return $fileName;
    }

	
	
	// create PHPExcel object
	$objPHPExcel = new PHPExcel();
	// selecting active sheet
	$objPHPExcel->setActiveSheetIndex(0);
	
	
	
    // letters of column
    // range('A', 'O') gets all letters of the alphabet within A - O
    $arr_of_columns = range('A', 'L');
    
    // gets the first and last element of array
    $first_element = reset($arr_of_columns);
    $last_element  = end($arr_of_columns);
    
    // gets the first and last index of array
    $first_index   = reset(array_keys($arr_of_columns));
    $last_index    = end(array_keys($arr_of_columns));
    
    // count columns of arrays
    $arr_of_columns_count  = count($arr_of_columns);
    
    
        // labels for heading
    $label_qty_supply = _LBL_TOTAL_QUANTITY_SUPPLY . "\n" . '(' . _LBL_TONNE_YEAR . ')';
    $label_qty_use    = _LBL_TOTAL_QUANTITY_USE . "\n" . '(' .  _LBL_TONNE_YEAR . ')';
    $arr_of_headings  = array(
        _LBL_NO, _LBL_SUBMISSION_ID, 
        _LBL_CHEMICAL_TYPE, _LBL_PRODUCT_NAME, _LBL_CHEMICAL_NAME, _LBL_CAS_NO, 
        _LBL_APPROVED_DATE, $label_qty_supply, $label_qty_use, 
        _LBL_PHY_HAZARD, _LBL_HEALTH_HAZARD, _LBL_ENV_HAZARD,
    );
	
	
	

	
    
    
    // 1. populate the data into file
    $state_name = '';
    $arrayData  = array();
    $all_states = array();
    $all_company_names = array();
    $all_bil = array();
    $all_prdt_nams = array();
    
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
                // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS,
                // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_GENERAL,
                // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
                // 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                // 'vertical' => PHPExcel_Style_Alignment::VERTICAL_JUSTIFY,
                // 'vertical' => PHPExcel_Style_Alignment::VERTICAL_BOTTOM,
            )
     );
     
     
     
    $data_style1 = array(
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
     
     
     
    $data_style2 = array(
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
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                // 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            )
     );
     
     
     
    $imp_mfg_data_style = array(
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
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                // 'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            )
     );
     
     
     
    // give borders to data
    // style to align text in the middle of cells
    $total_data_style = array(
            // set borders
            'borders' => array(
                'outline' => array( 
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'vertical' => array( 
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            // background color: gainsboro
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'DCDCDC')
            ),
            // align text horizontally at the center
            'alignment' => array(
                // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            )
     );
     
     
     
    // give borders to data
    // style to align text in the middle of cells
    $no_records_data_style = array(
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
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
     );
     
     
     
    // style to align text in the middle of cells
    $state_heading_style = array(
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
    );
    
    
    
    // style to align text in the middle of cells
    $state_heading_style1 = array(
            // set borders
            'borders' => array(
                'outline' => array( 
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                ),
                'vertical' => array( 
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            // set fonts
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
    );
    
    
    
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
    
    
    
    
    
    
    
    //////////////////////////////////////////////////////////////////////////
    // get all the level types names using the level type id and a for loop
    //////////////////////////////////////////////////////////////////////////
    
    // level types name(s) variables
    $all_level_types_names = array();
    // prints level type(s);

    
    
    // convert items in array to a comma separated string in PHP
    $level_types_string = '';
    $level_types_string = implode(', ', $levelTypes);



    
    
    
    
    ///////////////////////////////////////////////////////////////////
    // new codes from report_01_all.php and report_01_all_to_pdf.php
    ///////////////////////////////////////////////////////////////////
    
    
    
    
    
    
    // all variables
    $state_name = "";
    $arr_state_name = array();
    $rows_state_name = array();
    
    $company_info = "";
    $arr_company_info = array();
    $rows_company_info = array();
    
    $arr_level_name = array();
    $rows_level_name = array();
    
    $chemical_type = "";
    $arr_chemical_type = array();
    $rows_chemical_type = array();
    
    $bil_value = 0;
    $rows_bil_value = array();
    
    $submission_id = "";
    $arr_submission_id = array();
    $rows_submission_id = array();
    
    $product_name = "";
    $rows_product_name = array();
    
    $chemimal_name = "";
    $chemical_cas = "";
    $rows_chemical_cas = array();
    
    $hazard_category_name = $hazard_category_id = "";
    $ph_category_name = "";
    $hh_category_name = ""; 
    $eh_category_name = ""; 
    $rows_hazard_category_id = array();
    $rows_phy_hzd = array();
    $rows_hlt_hzd = array();
    $rows_env_hzd = array();
    $hazard_class_name = "";
    
    $approved_date = "";
    $rows_approved_date = array();
    
    $total_supply = 0;
    $rows_total_supply = array();
    
    $total_use = 0;
    $rows_total_use = array();
    $rows_total_info = array();
    $rows_total_sub_total = array();
    
    $sub_total_supply = 0;
    $sub_total_use = 0;
    $sum_total_supply = 0;
    $sum_total_use = 0;
    $grand_total_supply = 0;
    $grand_total_use = 0;

    // starting data row, ending row no records and 
	// status for no records variables
    $starting_data_row = 16;
    $records_status = 0; // 0=no records, 1=has records
    
    // stores the rows used in loop.
    // this will be used to create the cell 
    // for sub-total and total by getting 
    // the last element and adding one to it.
    $rows_used_in_loop = array();
    
    // store the rows not used
    $rows_not_used_in_loop = array();
    
    
    
    
    
    // set row value
    $row = $starting_data_row;
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    // get all state ids
    foreach($arr_state as $value){
        // echo $value . '<br><br>';
        // echo '<br><br><br><br>'.$row;
        
        
        
        
        if($value!=0){
            $countExist++;
            /*****/
            $dTable 	= "sys_User_Client";
            $dColumns 	= "$dTable.Usr_ID,Level_ID";
            $dJoin 		= array("sys_User"=>"sys_User.Usr_ID = $dTable.Usr_ID");
            $dWhere 	= array(
                            "AND Level_ID"=>"IN (". quote_smart($levelType,false) .")",
                            // "AND Usr_ID" => "IN (". $sub .")",
                            "AND Active" => "= 1",
                            "AND isDeleted" => "= 0",
                            "AND State_Reg"=>"= ". quote_smart($value)
                        );
            if( $comp!="" ) $dWhere = array_merge($dWhere,array("AND $dTable.Usr_ID"=>"=" . quote_smart($comp)));
            $dOrder		= "Level_ID ASC, Company_Name ASC";
            $arrayData = _get_arrayData($dColumns,$dTable,$dJoin,$dWhere,$dOrder);
            $bil = 0;
            /*****/
            if( $comp!="" ){
                if(count($arrayData)==0)
                    continue;
            }
            
            
            $state_name = _get_StrFromCondition('sys_State','State_Name','State_ID',$value);
            $state_name = ($state_name) ? $state_name : '-';
            // echo "<br/><br/><br/> prints state name where state id = value: <br/>".$state_name;
            
            // write/save state name in file here
            $cell = 'A' . $row;
            write_data_to_excel($objPHPExcel, $cell, $state_name);
            // sets the cell (or group of cells) to enable text wrap in those cells
            wrap_data_in_cell($objPHPExcel, $cell);
            // save data
            $arr_state_name[] = $state_name;
            // save row
            $rows_state_name[] = $row;
            
            // merge cells in the rows of state
            // form cell
            $form_cell = $first_element . $row . ':' . $last_element . $row;
            // merges cells 
            $cell = $form_cell;
            merge_cells($objPHPExcel, $cell);
            // apply style to cell
            add_style_to_cell($objPHPExcel, $cell, $state_heading_style);
            
            // stores incremented row numbers used
            $rows_used_in_loop[] = $row;
            
            
            
            
            /*****/
            $sum_totalSupply = 0;
            $sum_totalUse = 0;
            $countSecond = 0;
            /*****/
            foreach($arrayData as $detail){
                $uID = $detail['Usr_ID'];
                $lvlID = $detail["Level_ID"];
                // $bil = 0;
                $number_count = 0;
                /*****/
                
                $dColumns1 = "Detail_EntityTo,Detail_EntityFrom,Detail_ID,Form_ID,Detail_ProductName,"
                            .	" Category_ID_ph,Category_ID_hh,Category_ID_eh,Detail_TotalSupply,Detail_TotalUse,s.ApprovedDate,"
                            .	" s.Submission_Submit_ID";
                $dTable1 	= "tbl_Submission s";
                $dJoin1 	= array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID","tbl_Submission_Submit ss"=>"s.Submission_ID = ss.Submission_ID");
                $dWhere1 	= array(
                                "AND s.Usr_ID" =>"= ".quote_smart($uID),
                                // "AND s.Status_ID"=>"IN (31,32,33,41,42,43)",
                                "AND s.Status_ID"=>"IN (31)",
                                "AND s.Type_ID"=>"= ".quote_smart($chemType),
                                // ,"AND DATE_FORMAT(s.ApprovedDate,'%Y')"=>"= ".quote_smart($rptYear)										
                                // "AND (ss.SubmitTime >= ". quote_smart($dateStart) => "AND ss.SubmitTime <= ". quote_smart($dateLast)  .")"
                                "AND sd.isDeleted" => "= 0",
                                "AND YEAR(ss.SubmitTime)" => "= ". quote_smart($rptYear)
                            );
                $dOrder1 = "s.ApprovedDate DESC";
                $arrayData1 = _get_arrayData($dColumns1,$dTable1,$dJoin1,$dWhere1,$dOrder1); 
                $countData1 = count($arrayData1);
                
                //=====================================================
                $new_rowspan1 = 0; // ignore these
                //=====================================================
                $sub_totalSupply = 0;
                $sub_totalUse = 0;
                $countFirst = 0;
                //=====================================================
                
                
                
                
                foreach($arrayData1 as $key => $detail1){
                    $countFirst++;
                    $bil++;
                    $number_count++;
                    //=====================================================
                    $entityTo 		= $detail1['Detail_EntityTo'];
                    $ex_entityTo 	= explode(',',$entityTo);
                    $count_entityTo	= 0;
                    $entityFrom 	= $detail1['Detail_EntityFrom'];
                    $ex_entityFrom 	= explode(',',$entityFrom);
                    $count_entityFrom	= 0;
                    //=====================================================
                    $arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
                    $count_arrayChem = count($arrayChem);
                    
                    //=====================================================
                    $rowspan1 = max($count_entityTo,$count_entityFrom,$count_arrayChem);
                    if($rowspan1==0 || $rowspan1=='') $rowspan1 = 1;// ignore these
                    //=====================================================
                    
                    for($i=0;$i<$rowspan1;$i++){// ignore these
                        $td_rowspan = ($rowspan1>1) ? ' rowspan="'.$rowspan1.'"' : '';// ignore these
                        
                        if($key==0&&$i==0){
                            $compName 	= _get_StrFromCondition("sys_User_Client","Company_Name","Usr_ID",$uID);
                            $add1 		= _get_StrFromCondition("sys_User_Client","Address_Reg","Usr_ID",$uID);
                            $add2 		= _get_StrFromCondition("sys_User_Client","Postcode_Reg","Usr_ID",$uID);
                            $add3 		= _get_StrFromCondition("sys_User_Client","City_Reg","Usr_ID",$uID);
                            $compAdd	= "<br />". nl2br($add1) . "<br />$add2 $add3";
								
                            $lvlName = _getLabel($lvlID,$arrLevel);
                            
                            // replace <br /> with "\n" in string
                            $compName = str_replace("<br />", "\n", $compName);
                            $compAdd = str_replace("<br />", "\n", $compAdd);
                            $company_info = ($compName) . $compAdd;
                            
                            // save data
                            $arr_company_info[] = ($company_info) ? $company_info : '';// used when any state info is empty
                            // save row
                            $rows_company_info[] = $row;
                            
                            // save data
                            $arr_level_name[] = $lvlName;
                            // save row
                            $rows_level_name[] = $row; 
                            
                            
                            
                            // increament row value
                            $row++;
                            
                            
                            
                            // increament row value by n to contain type and company name
                            $n = 6;
                            $row = $row+$n;
                            
                            
                            
                            // assign rows
                            $row1 = $row-$n; // subtract n to get row 1
                            $row2 = $row; // assign row+n to get row 2
                            
                            // write/save data in file here
                            // $cell = 'A' . $row;
                            // write_data_to_excel($objPHPExcel, $cell, $lvlName." \n\n".$company_info);
                            // write_data_to_excel($objPHPExcel, $cell, (':: '.$lvlName.' ::'));
                            // wrap_data_in_cell($objPHPExcel, $cell);
                            // merge cells in the rows of state
                            $lvlName = (':: '. $lvlName. ' ::');
                            $data = "\n" . $lvlName . "\n" . $company_info . "\n\n";
                            $cell = 'A' . ($row1);
                            write_data_to_excel($objPHPExcel, $cell, $data);
                            wrap_data_in_cell($objPHPExcel, $cell);
                            // form cell
                            $form_cell = $first_element . $row1 . ':' . $last_element . $row2;
                            // merges cells 
                            $cell = $form_cell;
                            merge_cells($objPHPExcel, $cell);
                            // apply style to cell
                            add_style_to_cell($objPHPExcel, $cell, $state_heading_style1);
                            
                            
                            
                            // increament row value
                            // $row++;
                            // $row = $row+2;
                            
                            
                            
                        } // =========================================if($key==0&&$i==0)=====================================\\
                        
                        
                        
                        // increament row value
                        $row++;
                        
                        
                        
                        // increament row value by n to make enough space for the hazard types
                        $n = 10;
                        $row = $row + $n;
                        
                        
                        
                        if($i==0){
                            
                            // assign rows
                            $row1 = $row-$n; // subtract n to get row 1
                            $row2 = $row; // assign row+n to get row 2
                            
                            $bil_value = $bil;
                            // write/save bil in file here
                            $cell = 'A' . $row1;
                            // write_data_to_excel($objPHPExcel, $cell, $bil_value);
                            write_data_to_excel($objPHPExcel, $cell, $number_count);
                            // save row
                            $rows_bil_value[] = $row1; 
                            
                            // merge from cell1 to cell2
                            $cell = 'A' . $row1 .':'. 'A' . $row2;
                            merge_cells($objPHPExcel, $cell);
                            // apply style to from cell1 to cell2
                            add_style_to_cell($objPHPExcel, $cell, $data_style);
                            // Set the cell (or group of cells) to enable text wrap in those cells
                            wrap_data_in_cell($objPHPExcel, $cell);
                            
                        } // ================================================if($i==0)================================================\\
                        
                        
                        
                        // if($i==0&&$chemType==2){ // according to report_01_all_to_pdf.php
                        if($i==0){
                        
                            // write/save submission submit ID in file here
                            $column = 'B';
                            $submission_id = ($detail1['Submission_Submit_ID']) ? $detail1['Submission_Submit_ID'] : '-';
                            $cell = $column . $row1;
                            write_data_to_excel($objPHPExcel, $cell, $submission_id);
                            // save data
                            $arr_submission_id[] = $submission_id;
                            // save row
                            $rows_submission_id[] = $row1; 
                            
                            // merge from cell1 to cell2
                            $cell = $column . $row1 .':'. $column . $row2;
                            merge_cells($objPHPExcel, $cell);
                            add_style_to_cell($objPHPExcel, $cell, $data_style);
                            wrap_data_in_cell($objPHPExcel, $cell);
                        
                        
                        
                            $product_name = ($detail1['Detail_ProductName']) ? $detail1['Detail_ProductName'] : '-';
                            // write/save product name in file here
                            $column = 'D';
                            $cell = $column . $row1;
                            write_data_to_excel($objPHPExcel, $cell, $product_name);
                            // save row
                            $rows_product_name[] = $row1; 
                            
                            // merge from cell1 to cell2
                            $cell = $column . $row1 .':'. $column . $row2;
                            merge_cells($objPHPExcel, $cell);
                            add_style_to_cell($objPHPExcel, $cell, $data_style);
                            wrap_data_in_cell($objPHPExcel, $cell);
                            
                            
                            
                            // write/save chemical type in file here
                            // strtolower() - converts a string to lowercase
                            // ucfirst() - converts the first character of a string to lowercase
                            $chemical_type = ($typeName) ? (ucfirst(strtolower($typeName))) : '-';
                            $column = 'C';
                            $cell = $column . $row1;
                            write_data_to_excel($objPHPExcel, $cell, $chemical_type);
                            // save data
                            // $arr_chemical_type[] = $chemical_type;
                            // save row
                            $rows_chemical_type[] = $row1; 
                            
                            // merge from cell1 to cell2
                            $cell = $column . $row1 .':'. $column . $row2;
                            merge_cells($objPHPExcel, $cell);
                            add_style_to_cell($objPHPExcel, $cell, $data_style);
                            wrap_data_in_cell($objPHPExcel, $cell);
                            
                        } // ================================================if($i==0&&$chemType==2)================================================\\
                        
                        
                        
                        $chemimal_name = (isset($arrayChem[$i]['Chemical_ID'])?nl2br(func_fn_escape(_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$arrayChem[$i]['Chemical_ID']))):'&nbsp;');
                        // write/save chemical name in file here
                        $column = 'E';
                        $cell = $column . $row1;
                        write_data_to_excel($objPHPExcel, $cell, $chemimal_name);
                        
                        // merge from cell1 to cell2
                        $cell = $column . $row1 .':'. $column . $row2;
                        merge_cells($objPHPExcel, $cell);
                        // apply style to from cell1 to cell2
                        add_style_to_cell($objPHPExcel, $cell, $data_style);
                        // Set the cell (or group of cells) to enable text wrap in those cells
                        wrap_data_in_cell($objPHPExcel, $cell);
                            
                            
                            
                        $chemical_cas = (isset($arrayChem[$i]['Chemical_ID'])?_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$arrayChem[$i]['Chemical_ID']):'&nbsp;');
                        // write/save chemical cas in file here
                        $cell = 'F' . $row1;
                        write_data_to_excel($objPHPExcel, $cell, $chemical_cas);
                        // save row
                        $rows_chemical_cas[] = $row1; 
                        
                        // merge from cell1 to cell2
                        $cell = 'F' . $row1 .':'. 'F' . $row2;
                        merge_cells($objPHPExcel, $cell);
                        // apply style to from cell1 to cell2
                        add_style_to_cell($objPHPExcel, $cell, $data_style);
                        // Set the cell (or group of cells) to enable text wrap in those cells
                        wrap_data_in_cell($objPHPExcel, $cell);
                        
                        
                        
                       if($i==0){
                            //=========================================
                            $phCategory = $detail1['Category_ID_ph'];
                            $hhCategory = $detail1['Category_ID_hh'];
                            $ehCategory = $detail1['Category_ID_eh'];
                            //=========================================
                            if ( $phCategory == null ) { $phCategory = '84'; }
                            if ( $hhCategory == null ) { $hhCategory = '87'; }
                            if ( $ehCategory == null ) { $ehCategory = '90'; }

                            //=========================================
                            $totalSupply = $detail1['Detail_TotalSupply'];
                            $sub_totalSupply += $totalSupply;
                            //=========================================



            // get name
            $cat_name = "";
            $arr_of_cat_id = explode(',', $phCategory);
            $column = 'J';
            if(count($arr_of_cat_id) > 0)
            {
                foreach($arr_of_cat_id as $catid){
                
                    $cat_name .= "-". _get_StrFromCondition(
                                                "tbl_Hazard_Category",
                                                "CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
                                                "Category_ID",$catid
                                        );
                    $cat_name .= "\n";// for nextline in excel
                
                }
                
                // write data
                // $cell = 'M' . $row;
                $cell = $column . $row1;
                write_data_to_excel($objPHPExcel, $cell, $cat_name);
                // Set the cell (or group of cells) to enable text wrap in those cells
                wrap_data_in_cell($objPHPExcel, $cell);
                // save row
                $rows_phy_hzd[] = $row1; 
                
            }
            // merge from cell1 to cell2
            $cell = $column . $row1 .':'. $column . ($row2);
            merge_cells($objPHPExcel, $cell);
            // apply style to from cell1 to cell2
            add_style_to_cell($objPHPExcel, $cell, $data_style);
            
            
            
            $cat_name = "";
            $arr_of_cat_id = explode(',', $hhCategory);;
            $column = 'K';
            if(count($arr_of_cat_id) > 0)
            {                
                foreach($arr_of_cat_id as $catid){
                
                    $cat_name .= "-". _get_StrFromCondition(
                                                "tbl_Hazard_Category",
                                                "CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
                                                "Category_ID",$catid
                                        );
                    $cat_name .= "\n";// for nextline in excel
                }
                
                // write data
                $cell = $column . $row1;
                write_data_to_excel($objPHPExcel, $cell, $cat_name);
                wrap_data_in_cell($objPHPExcel, $cell);
                // save row
                $rows_hlt_hzd[] = $row1; 
                
            }
            // merge from cell1 to cell2
            $cell = $column . $row1 .':'. $column . $row2;
            merge_cells($objPHPExcel, $cell);
            // apply style to from cell1 to cell2
            add_style_to_cell($objPHPExcel, $cell, $data_style);
            
            
            
            $cat_name = "";
            $arr_of_cat_id = explode(',', $ehCategory);;
            $column = 'L';
            if(count($arr_of_cat_id) > 0)
            {
                foreach($arr_of_cat_id as $catid){
                
                    $cat_name .= "-". _get_StrFromCondition(
                                                "tbl_Hazard_Category",
                                                "CONCAT_WS(':',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
                                                "Category_ID",$catid
                                        );
                    $cat_name .= "\n";// for nextline in excel
                }
                
                
                // write data
                $cell = $column . $row1;
                write_data_to_excel($objPHPExcel, $cell, $cat_name);
                wrap_data_in_cell($objPHPExcel, $cell);
                // save row
                $rows_env_hzd[] = $row1; 
            }
            // merge from cell1 to cell2
            $cell = $column . $row1 .':'. $column . $row2;
            merge_cells($objPHPExcel, $cell);
            // apply style to from cell1 to cell2
            add_style_to_cell($objPHPExcel, $cell, $data_style);
                            
                            
                            
		$approved_date = func_ymd2dmy($detail1['ApprovedDate']);
        // write/save approved_date in file here
        $cell = 'G' . $row1;
        write_data_to_excel($objPHPExcel, $cell, $approved_date);
        // save row
        $rows_approved_date[] = $row1; 
        
        // merge from cell1 to cell2
        $cell = 'G' . $row1 .':'. 'G' . $row2;
        merge_cells($objPHPExcel, $cell);
        // apply style to from cell1 to cell2
        add_style_to_cell($objPHPExcel, $cell, $data_style);
        // Set the cell (or group of cells) to enable text wrap in those cells
        wrap_data_in_cell($objPHPExcel, $cell);
                                        
                                        
                                        
        $total_supply = number_format($totalSupply,1);
        // write/save totalSupply in file here
        $cell = 'H' . $row1;
        write_data_to_excel($objPHPExcel, $cell, $total_supply);
        // save row
        $rows_total_supply[] = $row1; 
        
        // merge from cell1 to cell2
        $cell = 'H' . $row1 .':'. 'H' . ($row2);
        merge_cells($objPHPExcel, $cell);
        // apply style to cell
        add_style_to_cell($objPHPExcel, $cell, $imp_mfg_data_style);
        // Set the cell (or group of cells) to enable text wrap in those cells
        wrap_data_in_cell($objPHPExcel, $cell);

        
        
									//=========================================
									$totalUse = $detail1['Detail_TotalUse'];
									$sub_totalUse += $totalUse;
									//=========================================
		$total_use = number_format($totalUse,1);

		// write/save totalUse in file here
        $cell = 'I' . $row1;
        write_data_to_excel($objPHPExcel, $cell, $total_use);
        // save row
        $rows_total_use[] = $row1; 
        
        // merge from cell1 to cell2
        $cell = 'I' . $row1 .':'. 'I' . ($row2);
        merge_cells($objPHPExcel, $cell);
        // apply style to cell
        add_style_to_cell($objPHPExcel, $cell, $imp_mfg_data_style);
        // Set the cell (or group of cells) to enable text wrap in those cells
        wrap_data_in_cell($objPHPExcel, $cell);

		
				
                        }// ================================================if($i==0)================================================\\
                        
                        
                        
                        // stores incremented row numbers used
                        $rows_used_in_loop[] = $row;
                      
                      
                      
                        // increament row value for chemical name, chemical cas, 
                        // $row++;
                        
                        
                        
                    } // ====================================for($i=0;$i<$rowspan1;$i++)=======================================\\
                    
                    
                    
                    
                } // ===================================foreach($arrayData1 as $key => $detail1)======================================\\
                
                
                
                
					if($countFirst>0){ 
						if($uLevelID!=5){
						
						
						
                // increament row value for chemical name, chemical cas, 
                $row++;

                
                
							$sum_totalSupply += $sub_totalSupply;
							$sum_totalUse += $sub_totalUse;

        $sub_total_supply = (number_format($sub_totalSupply,1));
        $sub_total_use = (number_format($sub_totalUse,1));
                                        
        // $row++;
        // write/save label for sub_totalSupply/sub_totalUse in file here
        // get the last element in array and add one to it to get the cell
        $row_total = $row;
        $cell = 'G' . $row_total;
        $label = _LBL_TOTAL;
        write_data_to_excel($objPHPExcel, $cell, $label);
        
        $cell = 'H' . $row_total;
        $label = $sub_total_supply;
        write_data_to_excel($objPHPExcel, $cell, $label);
        
        $cell = 'I' . $row_total;
        $label = $sub_total_use;
        write_data_to_excel($objPHPExcel, $cell, $label);
        
        // merge cells
        $form_cell = $first_element . $row . ':' . 'F' . $row;
        $cell = $form_cell;
        merge_cells($objPHPExcel, $cell);
        // merge cells
        $form_cell = 'J' . $row . ':' . 'L' . $row;
        $cell = $form_cell;
        merge_cells($objPHPExcel, $cell);
        
        // apply style to cell
        $cell = $first_element . $row_total .':'. $last_element . $row_total;
        add_style_to_cell($objPHPExcel, $cell, $total_data_style);
        
        // save row
        $rows_total_info[] = $row_total;
        // save row to be used for merging
        // used for merging in state name column
        $rows_total_sub_total[] = $row_total;
        $rows_used_in_loop[] = $row;
        
						}
					}
					$countSecond += $countFirst;
                
                
                
            }// ===========================================foreach($arrayData as $detail)=============================================\\
            
            
            
            
            
            
            
				if($countSecond>0){ 
					if($uLevelID!=5){
					
					
					
                // increament row value for chemical name, chemical cas, 
                $row++;
					
					
					
						$grand_totalSupply += $sum_totalSupply;
						$grand_totalUse += $sum_totalUse;

        $sum_total_supply = (number_format($sum_totalSupply,1));
        $sum_total_use = (number_format($sum_totalUse,1));
        
        // write/save sub_totalSupply in file here
        // get the last element in array and add one to it to get the cell
        $row_sub_total = $row;
        $cell = 'G' . $row_sub_total;
        $label = _LBL_SUBTOTAL;
        write_data_to_excel($objPHPExcel, $cell, $label);
        
        $cell = 'H' . $row_sub_total;
        $label = $sum_total_supply;
        write_data_to_excel($objPHPExcel, $cell, $label);
        
        $cell = 'I' . $row_sub_total;
        $label = $sum_total_use;
        write_data_to_excel($objPHPExcel, $cell, $label);
        
        // merge cells
        $form_cell = $first_element . $row . ':' . 'F' . $row;
        $cell = $form_cell;
        merge_cells($objPHPExcel, $cell);
        // merge cells
        $form_cell = 'J' . $row . ':' . 'L' . $row;
        $cell = $form_cell;
        merge_cells($objPHPExcel, $cell);
        
        // apply style to cell
        $cell = $first_element . $row_sub_total .':'. $last_element . $row_sub_total;
        add_style_to_cell($objPHPExcel, $cell, $total_data_style);
        
        // save row
        // $rows_total_info[] = $row_total;
        // save row to be used for merging
        // used for merging in state name column
        $rows_total_sub_total[] = $row_sub_total;
        $rows_used_in_loop[] = $row;

					}
				}
				
				
				
				
				
		if($bil==0){
			if($uLevelID!=5){
                
                
                
                // increament $row to add a column for no records
                $row++;
                
                
                
                // write no records
                $cell = 'A' . $row;
                // echo '<br><br> cell = '.$cell;
                $data = _LBL_NO_RECORD;
                write_data_to_excel($objPHPExcel, $cell, $data);
                
                // form cell
                $form_cell = $first_element . $row . ':' . $last_element . $row;
                // merges cells 
                $cell = $form_cell;
                merge_cells($objPHPExcel, $cell);
                // apply style to cell
                add_style_to_cell($objPHPExcel, $cell, $no_records_data_style);
                
                // save row
                $rows_used_in_loop[] = $row;
                
			}// end if($uLevelID!=5)
			
		}// end if($bil==0)
        
        
        
        
        } // ================================================if($value!=0)================================================\\
                
                
                
                
                // increament row value for chemical name, chemical cas, 
                $row++;
                
                
                
                
    } // ==================================state array looping: foreach($arr_state as $value)=================================\\
    // exit();
    
		
		
    
    
    if($bil>0){
        if($uLevelID!=5){
            $grand_total_supply = (number_format($grand_totalSupply,1));
            $grand_total_use = (number_format($grand_totalUse,1));

            // write/save grand_totalSupply in file here
            // get the last element in array and add one to it to get the cell
            $row_grand_total = $row;
                
                $cell = 'G' . $row_grand_total;
                $label = _LBL_GRANDTOTAL;
                write_data_to_excel($objPHPExcel, $cell, $label);
                
                $cell = 'H' . $row_grand_total;
                $label = $grand_total_supply;
                write_data_to_excel($objPHPExcel, $cell, $label);
                
                $cell = 'I' . $row_grand_total;
                $label = $grand_total_use;
                write_data_to_excel($objPHPExcel, $cell, $label);
                
                // merge cells
                $form_cell = $first_element . $row . ':' . 'F' . $row;
                $cell = $form_cell;
                merge_cells($objPHPExcel, $cell);
                // merge cells
                $form_cell = 'J' . $row . ':' . 'L' . $row;
                $cell = $form_cell;
                merge_cells($objPHPExcel, $cell);
                
                // apply style to cell
                $cell = $first_element . $row_grand_total .':'. $last_element . $row_grand_total;
                add_style_to_cell($objPHPExcel, $cell, $total_data_style);
                
                // save row
                $rows_used_in_loop[] = $row;
        }
    }
		
		
		
	
	
	if($countExist==0){
            
		}
    
    
    
    
    
    
    
    // sets columns' wdith
    $column_letter = '';
    $size          = 0;
    // loops through the 'letters of column'
    for($x = 0; $x < $arr_of_columns_count; $x++)
    {
        // forms cell by assigning column
        $column_letter = $arr_of_columns[$x];
        
        // resize specific columns
        if($x == 0)
            $size = 5;
        else if(($x == 2) || ($x == 6)) // C G
            $size = 20;
        else
            $size = 30;

        // sets column width size
        set_column_dimension($objPHPExcel, $column_letter, $size);
    }
    
	
	
	
	

	
    // 2. make table headers
    // row value for heading 
    $row_for_heading = 15;
    form_heading($objPHPExcel, $arr_of_columns, $arr_of_headings, $row_for_heading);
    // exit();
    
    // forms cell value
    $form_cell = $first_element . $row_for_heading . ':' . $last_element . $row_for_heading;
    
    // apply style to cell
    $cell = $form_cell;
    // add_style_to_cell(PHPExcel $objPHPExcel, $cell, $style)
    add_style_to_cell($objPHPExcel, $cell, $table_header_style);
	
	
	
	
	
	
	
    // 3. make table title
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
    // get first colun and first index
    $form_starting_cell = $first_element . ($first_index + 1);
    // $cell = 'A1';
    $cell = $form_starting_cell;
    // writes data to cell
    write_data_to_excel($objPHPExcel, $cell, $heading);
        
    // sets the cell (or group of cells) to enable text wrap in those cells
    wrap_data_in_cell($objPHPExcel, $cell);
    
    // forms cell value
    // adds one to first index to make it 1 since 
    // there is no zero in excel cell value
    $first_index = $first_index + 1; 
    $row_end_title = 14;
    $form_cell = $first_element. $first_index . ':' . $last_element . $row_end_title;
    
    // merges cells A1 to M14
    $cell = $form_cell;
    merge_cells($objPHPExcel, $cell);
    
    // apply style to cell
    add_style_to_cell($objPHPExcel, $cell, $table_heading_style);
    
    
    
    
    
    
    
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
    
    // check if there were no records and change x and y axis for logo
    // no records from database
    $x_axis = 20;
    $y_axis = 3000;
    
    // add some margin between a cell and the image
    $objDrawing->setOffsetX($x_axis);  // x-axis or horizontal
    $objDrawing->setOffsetY($y_axis);    // y-axis or vertical
    $objDrawing->setResizeProportional(true);
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());// save image
	
	
	
	
	
	
	
	// forms file name
	$excel_version = 'Excel2007';
	$name_label    = _LBL_REPORT_OVERALL_DETAILED;
	$file_extension= '.xlsx';
	$name_of_file  = form_file_name($name_label, $file_extension);
	
	
	// saves and downloads file
	save_and_download_file($objPHPExcel, $excel_version, $name_of_file);
    exit();
	
?>
 
