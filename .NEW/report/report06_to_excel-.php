<?php
	//============================================================
	// FILE BEGIN 
	// filename: report03_to_pdf.php
	// description: overall summary report convert to pdf
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
	include_once $sys_config['includes_path'].'PHPExcel-1.7.7/Classes/PHPExcel.php';
	//==========================================================
	$uLevelID = $_SESSION['user']['Level_ID'];
	// if($uLevelID==5) exit();
	//==========================================================
	/*****/
	if(isset($_GET["rptType"])) $rptType = $_GET["rptType"];
	else $rptType = "";
	/*****/
	if(isset($_GET["lvlType"])) $lvlType = $_GET["lvlType"];
	else $lvlType = "";
	/*****/
	if(isset($_GET["chemType"])) $chemType = $_GET["chemType"];
	else $chemType = "";
	/*****/
	if(isset($_GET["rptYear"])) $rptYear = $_GET["rptYear"];
	else $rptYear = "";
	/*****/
	if(isset($_GET["state"])) $state = $_GET["state"];
	else $state = "";
	/*****/
	if(isset($_GET["comp"])) $comp = $_GET["comp"];
	else $comp = "";
	/*****/

	function cellsToMergeByColsRow($start = NULL, $end = NULL, $row = NULL){
		$merge = 'A1:A1';
		if($start && $end && $row){
			$start = PHPExcel_Cell::stringFromColumnIndex($start);
			$end = PHPExcel_Cell::stringFromColumnIndex($end);
			$merge = "$start{$row}:$end{$row}";

		}

		return $merge;
	}
	
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
		
	$style["title"] = array(
        "font"  => array(
			"underline"  => PHPExcel_Style_Font::UNDERLINE_SINGLE,
			"bold"  => true,
			"size"  => 15
		),
		"alignment" => array(
			"horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
		)
	);
	
	$style["subcaption1"] = array(
        "font"  => array(
			"bold"  => true
		),
		"alignment" => array(
			"horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
		)
	);
	
	$style["center"] = array(
		"alignment" => array(
			"horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
		)
	);
	
	$style["right"] = array(
		"alignment" => array(
			"horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
		)
	);
	
	$style["subcaption3"] = array(
        "font"  => array(
			"bold"  => true,
			"size"  => 10
		)
	);
	
	$style["border"] = array(
		"borders" => array(
			"bottom" => array(
			  "style" => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
	
	$style["borderall"] = array(
		"borders" => array(
			"allborders" => array(
			  "style" => PHPExcel_Style_Border::BORDER_THIN
			)
		)
	);
		
	$style["data1"] = array(
        "font"  => array(
			"size"  => 10
		)
	);
		
	$style["data2"] = array(
        "font"  => array(
			"size"  => 10
		),
		"alignment" => array(
			"horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
		)
	);

	$style["header1"] = array(
		"fill" => array(
			"type" => PHPExcel_Style_Fill::FILL_SOLID,
			"color" => array("rgb" => "33FF99")
		)
	);

	$style["header2"] = array(
		"fill" => array(
			"type" => PHPExcel_Style_Fill::FILL_SOLID,
			"color" => array("rgb" => "00CC66")
		)
	);

	$style["header3"] = array(
		"fill" => array(
			"type" => PHPExcel_Style_Fill::FILL_SOLID,
			"color" => array("rgb" => "33FFFF")
		)
	);

	$style["header4"] = array(
		"fill" => array(
			"type" => PHPExcel_Style_Fill::FILL_SOLID,
			"color" => array("rgb" => "33CCFF")
		),
		"font" => array(
			"bold" => true
		)
	);
	
	//==========================================================
	$titleRpt = strtoupper(_LBL_REPORT) .': '. strtoupper(_LBL_REPORT_OVERALL);
	//==========================================================
	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("CIMS")
								 ->setLastModifiedBy("CIMS")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
								 ->setKeywords("office 2007 openxml php")
								 ->setCategory("Test result file");
								 
	
	$sheetIndex = 0;
	
	
	$objPHPExcel->setActiveSheetIndex($sheetIndex);
	
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(8);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(20);
	
	
	$objPHPExcel->setActiveSheetIndex($sheetIndex)->mergeCells("A2:A3");
	$objPHPExcel->setActiveSheetIndex($sheetIndex)->mergeCells("B2:B3");
	$objPHPExcel->setActiveSheetIndex($sheetIndex)->mergeCells("C2:C3");
	$objPHPExcel->setActiveSheetIndex($sheetIndex)->mergeCells("D2:D3");
	$objPHPExcel->setActiveSheetIndex($sheetIndex)->mergeCells("E2:E3");
	$objPHPExcel->getActiveSheet()->getStyle("A2:A3")->applyFromArray(array_merge($style["borderall"],$style["center"],$style["header4"]));
	$objPHPExcel->getActiveSheet()->getStyle("B2:E3")->applyFromArray(array_merge($style["borderall"],$style["header4"]));
	
	
	$objPHPExcel->setActiveSheetIndex($sheetIndex)
				->setCellValue("A2", "#")
				->setCellValue("B2", _LBL_SUBMISSION_ID)
				->setCellValue("C2", _LBL_COMPANY_NAME)
				->setCellValue("D2", _LBL_STATE)
				->setCellValue("E2", _LBL_CHEMICAL_NAME)
				;
	
	if($lvlType=='all') $lvlType = '6,7,8';
	
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
	$submission_ids = array();
	$physical = array();
	$health = array();
	$environment = array();
	foreach($arrayData1 as $data){
		$submission_ids[] = $data['Submission_ID'];
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
	}
	$submission_ids = array_unique($submission_ids);
	$physical = array_unique($physical);
	$health = array_unique($health);
	$environment = array_unique($environment);
	
	$currentrow = 2;	
	/*********************************/
	$col = 5;
	// var_dump($physical);
	if(count($physical)>0){
		foreach($physical as $ph){
			if(!isset($col_start)) $col_start = $col;
			
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $currentrow+1)->getAlignment()->setTextRotation(90)->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $currentrow+1)->applyFromArray(array_merge($style["borderall"],$style["header3"]));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $currentrow+1, getClassCategory($ph));
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $currentrow)->applyFromArray(array_merge($style["borderall"],$style["header4"]));		
			$col++;
		}
		$objPHPExcel->getActiveSheet()->mergeCells(cellsToMergeByColsRow($col_start, $col-1, $currentrow));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_start, $currentrow, _LBL_PHY_HAZARD);
		// $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col_start, $currentrow)->applyFromArray($style["borderall"]);
		unset($col_start);		
	}
	// var_dump($health);
	if(count($health)>0){
		foreach($health as $hh){
			if(!isset($col_start)) $col_start = $col;
			
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $currentrow+1)->getAlignment()->setTextRotation(90)->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $currentrow+1)->applyFromArray(array_merge($style["borderall"],$style["header3"]));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $currentrow+1, getClassCategory($hh));	
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $currentrow)->applyFromArray(array_merge($style["borderall"],$style["header4"]));	
			$col++;
		}
		$objPHPExcel->getActiveSheet()->mergeCells(cellsToMergeByColsRow($col_start, $col-1, $currentrow));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_start, $currentrow, _LBL_HEALTH_HAZARD);
		// $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col_start, $currentrow)->applyFromArray($style["borderall"]);
		unset($col_start);
	}
	// var_dump($environment);
	if(count($environment)>0){
		foreach($environment as $eh){
			if(!isset($col_start)) $col_start = $col;
			
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $currentrow+1)->getAlignment()->setTextRotation(90)->setWrapText(true);
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $currentrow+1)->applyFromArray(array_merge($style["borderall"],$style["header3"]));
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $currentrow+1, getClassCategory($eh));
			$objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $currentrow)->applyFromArray(array_merge($style["borderall"],$style["header4"]));	    
			$col++;
		}
		$objPHPExcel->getActiveSheet()->mergeCells(cellsToMergeByColsRow($col_start, $col-1, $currentrow));
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col_start, $currentrow, _LBL_ENV_HAZARD);
		// $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col_start, $currentrow)->applyFromArray($style["borderall"]);
		unset($col_start);
	}
	/*********************************/
	
	$currentrow = 3;
	$bil = 0;
	foreach($submission_ids as $sub){
		
		$dataColumns = 's.Submission_Submit_ID, uc.Company_Name, st.State_Name, c.Chemical_Name, sd.Detail_TotalSupply, sd.Detail_TotalUse, sc.Chemical_ID, sd.Detail_ID';
		$dataTable = 'tbl_submission_detail sd';
		$dataJoin = array(
					'tbl_submission s' => 'sd.Submission_ID = s.Submission_ID', 
					'tbl_submission_chemical sc' => 'sd.Detail_ID = sc.Detail_ID', 
					'tbl_chemical c' => 'sc.Chemical_ID = c.Chemical_ID', 
					"sys_user_client uc" => "s.Usr_ID = uc.Usr_ID",
					"sys_state st" => "uc.State_Reg = st.State_ID"
					);
		$dataWhere = array(
					"AND s.Submission_ID" => "= $sub",
					"AND sd.isDeleted" => "= 0"
				);
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
		foreach($arrayData as $data){
			$bil++;
			$col = 0;
			$currentrow++;
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, $bil);
			$objPHPExcel->getActiveSheet()->getStyle("A$currentrow")->applyFromArray($style["center"]);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, $data['Submission_Submit_ID']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, $data['Company_Name']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, $data['State_Name']);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, $data['Chemical_Name']);
			
			$total = floatval($data['Detail_TotalSupply']) + floatval($data['Detail_TotalUse']);
						
			foreach($physical as $ph){
				if(!isset($col_start)) $col_start = $col;
				
				if(checkChemicalHazard('ph', $data['Chemical_ID'], $data['Detail_ID'], $ph))
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, $total);
				else $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, '-');
			}
			unset($col_start);
			
			foreach($health as $hh){
				if(!isset($col_start)) $col_start = $col;
				
				if(checkChemicalHazard('hh', $data['Chemical_ID'], $data['Detail_ID'], $hh))
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, $total);
				else $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, '-');		
			}
			unset($col_start);
			
			foreach($environment as $eh){
				if(!isset($col_start)) $col_start = $col;
				
				if(checkChemicalHazard('eh', $data['Chemical_ID'], $data['Detail_ID'], $eh))
					$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, $total);	
				else $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col++, $currentrow, '-');
			}
			unset($col_start);
		}
	}
	
	if($bil==0){
		$currentrow++;
		$objPHPExcel->setActiveSheetIndex($sheetIndex)->setCellValue("A$currentrow", _LBL_NO_RECORD);
	}	
	
	$objPHPExcel->setActiveSheetIndex(0);

	$fileName = str_replace(' ', '_', _LBL_REPORT_OVERALL) .".xls";
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="'. $fileName .'"');
	header('Cache-Control: max-age=0');
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	
	$objWriter->save("php://output");	
	exit;	
?>