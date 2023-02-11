<?php
	//============================================================
	// linuxhouse__20220710
	// FILE BEGIN 
	// filename: report02_to_excel.php
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
	//==========================================================
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_date.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	
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
	// $chemType = isset($_GET["chemType"]) ? $_GET["chemType"] : "";
	$rptYear = isset($_GET["rptYear"]) ? $_GET["rptYear"] : "";
	$state = isset($_GET["state"]) ? $_GET["state"] : "";
	// $comp = isset($_GET["comp"]) ? $_GET["comp"] : "";
	// echo info
	// echo $rptType.' - '.$lvlType.' - '.$chemType.' - '.$rptYear.' - '.$state.' - '.$comp;
	// exit();
	
	
	
	$colTypeName = "Type_Name". (($fileLang=="may.php")?"_may":"");
	// $typeName = ($chemType!="") ? strtoupper(_get_StrFromCondition("tbl_Chemical_Type",$colTypeName,"Type_ID",$chemType)) : "";
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
	$lvlName = "[". strtoupper($lvlName) ."]"; 
	
	$langdesc = (($fileLang=="may.php")?"_may":"");
	//==========================================================
	$rptName = "";
	foreach($arrReport as $detail){
		if($detail["value"]==$rptType) $rptName = strtoupper($detail["label"]); 
	}
	// $titleRpt = strtoupper(_LBL_SUMMARY) .": ".$rptName." [". $typeName ."]";
	$titleRpt = strtoupper(_LBL_SUMMARY) .': '.$rptName . $lvlName;
	/*****/
	// $titleSupp = strtoupper(_LBL_SUPPLIER_TYPE) .": ".strtoupper($lvlName);
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
    $arr_of_columns = range('A', 'G');
    
    // gets the first and last element of array
    $first_element = reset($arr_of_columns);
    $last_element  = end($arr_of_columns);
    
    // gets the first and last index of array
    $first_index   = reset(array_keys($arr_of_columns));
    $last_index    = end(array_keys($arr_of_columns));
    
    // count columns of arrays
    $arr_of_columns_count  = count($arr_of_columns);
    
    // labels for heading
    $arr_of_headings  = array(
        _LBL_NO, _LBL_COMPANY_NAME, _LBL_COMPANY_TYPE, _LBL_STATUS,
        _LBL_NUMBER, _LBL_CHEMICAL_NAME, _LBL_CAS_NO, 
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
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
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
    // new codes from report_02.php and report_02_to_pdf.php
    ///////////////////////////////////////////////////////////////////
    
    
    
    
    
    
    // all variables
    $state_name = "";
    $arr_state_name = array();
    $rows_state_name = array();
    
    $company_info = "";
    $arr_company_info = array();
    $rows_company_info = array();
    
    $level_type = "";
    $bil_value = 0;
    
    $chemimal_name = "";
    $rows_chemical_name = array();
    $chemical_cas = "";

    // starting data row, ending row no records and 
	// status for no records variables
    $starting_data_row = 16;
    
    // stores the rows used in loop.
    // this will be used to create the cell 
    // for sub-total and total by getting 
    // the last element and adding one to it.
    $rows_used_in_loop = array();
    
    // store the rows used for state and bil
    $rows_state_bil = array();
    
    // set row value
    $row = $starting_data_row;
    
    // get all state ids
			foreach($arr_state as $value){
				if($value!=0){
					$countExist++;


            $state_name = _get_StrFromCondition('sys_State','State_Name','State_ID',$value);
            $state_name = ($state_name) ? $state_name : '-';
            
            // write/save state name in file here
            $cell = 'A' . $row;
            write_data_to_excel($objPHPExcel, $cell, $state_name);
            
            // save data
            // $arr_state_name[] = $state_name;	
            
            // save row
            $rows_state_bil[] = $row;
            $rows_used_in_loop[] = $row;
            
            // form cell
            $form_cell = $first_element . $row . ':' . $last_element . $row;
            // merges cells 
            $cell = $form_cell;
            merge_cells($objPHPExcel, $cell);
            
            // apply style to cell
            add_style_to_cell($objPHPExcel, $cell, $state_heading_style);
            
            
            
					//=====================================================
					$dataColumns = 'u.Usr_ID,u.Level_ID,u.Active';
					$dataTable = 'sys_User_Client uc';
					$dataJoin = array('sys_User u'=>'uc.Usr_ID = u.Usr_ID');
					$dataWhere["AND uc.State_Reg"] = "= ".quote_smart($value);
					if($levelType!='')
						$dataWhere["AND Level_ID"] = "IN (". quote_smart($levelType,false) .")";
					$dataOrder		= "Level_ID ASC, Company_Name ASC";
					$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere,$dataOrder);
					$bil = 0;
					//=====================================================
					foreach($arrayData as $detail){
						$uID = $detail['Usr_ID'];
						$lID = $detail['Level_ID'];
						$aID = $detail['Active'];
						//=====================================================
						$dataColumns1 = '*';
						$dataTable1 = 'tbl_Submission s';
						$dataJoin1 = array("tbl_Submission_Detail sd"=>"s.Submission_ID = sd.Submission_ID");
						$dataWhere1 = array(
										"AND s.Usr_ID" =>"= ".quote_smart($uID),"AND s.Status_ID"=>"IN (31,32,33,41,42,43)","AND sd.isDeleted" => "= 0"
										,"AND DATE_FORMAT(s.ApprovedDate,'%Y')"=>"= ".quote_smart($rptYear)
									);
						$dataOrder1 = '';
						$arrayData1 = _get_arrayData($dataColumns1,$dataTable1,$dataJoin1,$dataWhere1); 
						$countData1 = count($arrayData1);
						//=====================================================
						$new_chemIDs = array();
						//=====================================================
						foreach($arrayData1 as $detail1){
							//=====================================================
							$arrayChem = _get_arrayData('Chemical_ID','tbl_Submission_Chemical','',array("AND Detail_ID"=>"= ".quote_smart($detail1['Detail_ID']) )); 
							//=====================================================
							foreach($arrayChem as $indChem => $detChem){
								if(! in_array($detChem,$new_chemIDs) ) array_push($new_chemIDs,$detChem);
							}
							//=====================================================
						}
						//=====================================================
						$rowspan = count($new_chemIDs);
						if($rowspan==0 || $rowspan=='') $rowspan = 1;
						$countFirst = 0;
						//=====================================================
						foreach($new_chemIDs as $key1 => $detail1){
							$countFirst++;
							//=====================================================
							$td_rowspan = ($rowspan>1) ? ' rowspan="'.$rowspan.'"' : '';

// write <td>



                        // increament row value
                        $row++;
                        $rows_used_in_loop[] = $row;
                        
                        
							if($key1==0){
								$bil++;

								
// write bil
                        // increament row value
                        // $row++;
                        
                            $bil_value = $bil;
                            $cell = 'A' . $row;
                            write_data_to_excel($objPHPExcel, $cell, $bil_value);
                            wrap_data_in_cell($objPHPExcel, $cell);
                            add_style_to_cell($objPHPExcel, $cell, $data_style);
                            // save row
                            $rows_state_bil[] = $row;



// write company name 
                            $compName = (_get_StrFromCondition('sys_User_Client','Company_Name','Usr_ID',$uID));
                            // replace <br /> with "\n" in string
                            $compName = str_replace("<br />", "\n", $compName);
                            $company_info = $compName;
                            $cell = 'B' . $row;
                            write_data_to_excel($objPHPExcel, $cell, $company_info);
                            wrap_data_in_cell($objPHPExcel, $cell);
                            add_style_to_cell($objPHPExcel, $cell, $data_style);



// write level type name
                            $lvlName = (_get_StrFromCondition('sys_Level','Level_Name'. $langdesc,'Level_ID',$lID));
                            $lvlName = ($lvlName) ? $lvlName : '-';
                            $level_type = $lvlName;
                            $cell = 'C' . $row;
                            write_data_to_excel($objPHPExcel, $cell, $level_type);
                            wrap_data_in_cell($objPHPExcel, $cell);
                            add_style_to_cell($objPHPExcel, $cell, $data_style);



								foreach($arrStatusAktif as $stat){
									if($stat['value']==$aID) 
									{
										// $tbl .= $stat['label'];
										// write status
                                        $status = ($stat['label']) ? $stat['label'] : '-';
                                        $cell = 'D' . $row;
                                        write_data_to_excel($objPHPExcel, $cell, $status);
                                        wrap_data_in_cell($objPHPExcel, $cell);
                                        add_style_to_cell($objPHPExcel, $cell, $data_style);
                                    }
								}
								

// write <td>



							}

							
// write No. or countFirst
                            $count_first_value = $countFirst;
                            $cell = 'E' . $row;
                            write_data_to_excel($objPHPExcel, $cell, $count_first_value);
                            wrap_data_in_cell($objPHPExcel, $cell);
                            add_style_to_cell($objPHPExcel, $cell, $data_style);



// write chemical name
            $chemimal_name = (_get_StrFromCondition('tbl_Chemical','Chemical_Name','Chemical_ID',$detail1['Chemical_ID']));
            // $chemimal_name = ($chemimal_name) ? nl2br(func_fn_escape($chemimal_name)) : '&nbsp;';
            // ltrim(): removes white spces from begining of a string
            $chemimal_name = ($chemimal_name) ? ltrim(nl2br(func_fn_escape($chemimal_name))) : '-';
            $cell = 'F' . $row;
            write_data_to_excel($objPHPExcel, $cell, $chemimal_name);
            wrap_data_in_cell($objPHPExcel, $cell);
            add_style_to_cell($objPHPExcel, $cell, $data_style);
            $rows_chemical_name[] = $row;



// write chemical cas number
            $chemical_cas = (_get_StrFromCondition('tbl_Chemical','Chemical_CAS','Chemical_ID',$detail1['Chemical_ID']));
            // $chemical_cas = ($chemical_cas) ? $chemical_cas : '&nbsp;';
            $chemical_cas = ($chemical_cas) ? ltrim($chemical_cas) : '-';
            $cell = 'G' . $row;
            write_data_to_excel($objPHPExcel, $cell, $chemical_cas);
            wrap_data_in_cell($objPHPExcel, $cell);
            add_style_to_cell($objPHPExcel, $cell, $data_style);

		
                                                        
						}
						
						
						
					}
					
					
					
					
					if($bil==0){

// write 'no records' when bil is 0
                            // row before
                            $cell = 'A' . $row;
                            // echo '<br><br> cell = '.$cell;
                            $data = $state_name;
                            write_data_to_excel($objPHPExcel, $cell, $data);
                            $rows_used_in_loop[] = $row;

                            // save row
                            $rows_used_in_loop[] = $row;
                            

                            // increament $row to add a column for no records
                            $row++;
                            
                            $cell = 'A' . $row;
                            // echo '<br><br> cell = '.$cell;
                            $data = _LBL_NO_RECORD;
                            write_data_to_excel($objPHPExcel, $cell, $data);
                            $rows_used_in_loop[] = $row;
                            
                            // form cell
                            $form_cell = $first_element . $row . ':' . $last_element . $row;
                            // merges cells 
                            $cell = $form_cell;
                            merge_cells($objPHPExcel, $cell);
                            // apply style to cell
                            add_style_to_cell($objPHPExcel, $cell, $no_records_data_style);
                            
                            // save row
                            $rows_used_in_loop[] = $row;
                            
                            // save row
                            $rows_state_bil[] = $row;

					}
					
					
                }// if($value!=0)
                      
                      
                      
                        // increament row value for chemical name, chemical cas, 
                        $row++;
			
			
			}// foreach($arr_state as $value)
			
			
			if($countExist==0){

// write 'no records' when countExist is 0


			}   
    
    
    
    
    


    function merge_write_in_file33(PHPExcel $objPHPExcel, $arr1, $arr2, $column, $style)
    {
        //- loop through the array
        //  - get the first element
        //  - get the second elent
        //  - if second element minus first element equals 1
		//		- ignore
		//		- increase array index by 1 to move to the next first 
		//		  element because of row state and then row company
        //  - if second element minus first element is greater or equals 2
        //		- assign first element to arrayX
		//		- assign second element - 1 to arrayX
		//		- increase array index by 1 to move to the next first 
		//		  element becuase of row state, then 1  or more row data and 
		//		  then row company
		//- reomove last element in arrayX
		//- insert item1 of array-state infront of the items in arrayX
		//- assign arrayX to arrayY
		//- sort arrayY in ascending order
		//- loop through arrayX
		//	- pair values in 2s
		//	- for rows and cells
		//	- merge cells   
    
        $array1 = $arr1; // $rows_state_bil;
        $array2 = $arr2; // $rows_used_in_loop;
        $array3 = array();
        $array4 = array();
        
        // remove first element
        // $array1 = array_shift($array1);
        // get last element of array2
        $last_item = end($array2) + 1;// bcos 1 will be subtracted from it in the logic below
        // add element at the end of array
        array_push($array1, $last_item);
        // sort array items in ascending order
        sort($array1);// sort array
        
        for($x=0; $x<count($array1); $x++)
        {
            // get starting and ending range for array 1
            $current_index = $x;// get current index in array1
            $next_index = $x+1;// get next index in array1
            
            $first_element = $array1[$current_index];
            // check if next index in array1 is less than 
            // or greater than array count in array1
            if($next_index < count($array1))
            {
                $second_element = $array1[$next_index];        
            }
            else {
                // code...
                $second_element = end($array1);//
            }
            
            // row total and then row sub total
            
            // row total, then more than 2 row data and then row total
            if($second_element - $first_element >= 2)
            {
                $array3[] = $first_element;
                $array3[] = $second_element - 1;
            }
            
        }
        
        $array4 = $array3;
        sort($array4);// sort array
        
        for($x=0; $x<count($array4); $x++)
        {
            $current_index = $x;// get current index in array1
            $next_index = $x+1;// get next index in array1
            
            $first_element = $array4[$current_index];
            // check if next index in array1 is less than 
            // or greater than array count in array1
            if($next_index < count($array4))
            {
                $second_element = $array4[$next_index];        
            }
            else {
                // code...
                $second_element = end($array4);//
            }
            
            // form rows and cells
            // $column = 'D';
            $row1 = $first_element;
            $row2 = $second_element;
            $cell = $column . $row1 . ':' . $column . $row2;
            // echo '<br><br><br><br>merge cells = '.$cell;
            
            // <br>
            merge_cells($objPHPExcel, $cell);
            add_style_to_cell($objPHPExcel, $cell, $style);
            
            $x++;// to enable movement to the next two elements
        }
        
        
    }
    // merge cells in 
    merge_write_in_file33($objPHPExcel, $rows_state_bil, $rows_used_in_loop, 'A', $data_style);
    merge_write_in_file33($objPHPExcel, $rows_state_bil, $rows_used_in_loop, 'B', $data_style);
    merge_write_in_file33($objPHPExcel, $rows_state_bil, $rows_used_in_loop, 'C', $data_style);
    merge_write_in_file33($objPHPExcel, $rows_state_bil, $rows_used_in_loop, 'D', $data_style);
    
    
    
    
    
    
    
    // sets columns' wdith
    $column_letter = '';
    $size          = 0;
    // loops through the 'letters of column'
    for($x = 0; $x < $arr_of_columns_count; $x++)
    {
        // forms cell by assigning column
        $column_letter = $arr_of_columns[$x];
        
        // resize specific columns
        if($x == 0 || $x == 4)
            $size = 5;
        else if($x == 3) // D E F
            $size = 15;
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
    // $heading .= "\n" . $titleSupp;
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
    $last_index  = $last_index + 8;
    $form_cell = $first_element. $first_index . ':' . $last_element . $last_index;
    // echo $form_cell;
    // exit();
    
    // merges cells A1 to M14
    $cell = $form_cell;
    merge_cells($objPHPExcel, $cell);
        
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
    add_style_to_cell($objPHPExcel, $cell, $table_heading_style);
    
    
    
    
    
    
    
    // 4. add logo to table heading
    // Create new picture object and insert picture
    $objDrawing = new PHPExcel_Worksheet_Drawing();
    $objDrawing->setName($logo_image_name);
    $objDrawing->setDescription($logo_image_description);
    $objDrawing->setPath($logo_image_path);
    $objDrawing->setHeight(100);
    // $objDrawing->setWidthAndHeight(100, 100);
    $cell = 'D1';
    $objDrawing->setCoordinates($cell);
    
    // check if there were no records and change x and y axis for logo
    // no records from database
    $x_axis = -5;
    $y_axis = 3000;
    
    // add some margin between a cell and the image
    $objDrawing->setOffsetX($x_axis);  // x-axis or horizontal
    $objDrawing->setOffsetY($y_axis);    // y-axis or vertical
    $objDrawing->setResizeProportional(true);
    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());// save image
	
	
	
	
	
	
	
	// forms file name
	$excel_version = 'Excel2007';
	$name_label    = _LBL_REPORT_OVERALL_SUMMARY;
	$file_extension= '.xlsx';
	$name_of_file  = form_file_name($name_label, $file_extension);
	
	
	// saves and downloads file
	save_and_download_file($objPHPExcel, $excel_version, $name_of_file);
	

?>
