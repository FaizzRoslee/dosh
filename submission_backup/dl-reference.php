<?php
	/*
	 *
	 */
	include_once "../includes/sys_config.php";
	/*
	 *
	 */
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*
	 *
	 */
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*
	 *
	 */
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*
	 *
	 */

	$pswd = "PHPExcel";
	$type = $_POST["type"];

	/**
	 * PHPExcel
	 *
	 * Copyright (C) 2006 - 2012 PHPExcel
	 *
	 * This library is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU Lesser General Public
	 * License as published by the Free Software Foundation; either
	 * version 2.1 of the License, or (at your option) any later version.
	 *
	 * This library is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	 * Lesser General Public License for more details.
	 *
	 * You should have received a copy of the GNU Lesser General Public
	 * License along with this library; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
	 *
	 * @category   PHPExcel
	 * @package    PHPExcel
	 * @copyright  Copyright (c) 2006 - 2012 PHPExcel (http://www.codeplex.com/PHPExcel)
	 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
	 * @version    1.7.7, 2012-05-19
	 */

	/** Error reporting */
	error_reporting(E_ALL);

	date_default_timezone_set("Asia/Kuala_Lumpur");

	/** Include PHPExcel */
	require_once $sys_config["includes_path"] ."Classes/PHPExcel.php";
	// require_once $sys_config["includes_path"] ."Classes/PHPExcel/IOFactory.php";

	$objPHPExcel = new PHPExcel();

		
	$styleArray = array(
			"borders" => array(
				"allborders" => array(
					"style" => PHPExcel_Style_Border::BORDER_THIN
				)
			)
	);
		
	$styleArray2 = array(
			"borders" => array(
				"outline" => array(
					"style" => PHPExcel_Style_Border::BORDER_THIN
				)
			)
	);
	
	$stylePF = array(
			"fill" => array(
				"type" => PHPExcel_Style_Fill::FILL_SOLID,
				"color" => array("rgb" => "B1A0C5")
			)
    );
	
	$stylePH = array(
			"fill" => array(
				"type" => PHPExcel_Style_Fill::FILL_SOLID,
				"color" => array("rgb" => "FFBF91")
			)
    );
	
	$styleHH = array(
			"fill" => array(
				"type" => PHPExcel_Style_Fill::FILL_SOLID,
				"color" => array("rgb" => "BEBEBE")
			)
    );
	
	$styleEH = array(
			"fill" => array(
				"type" => PHPExcel_Style_Fill::FILL_SOLID,
				"color" => array("rgb" => "B3CAE2")
			)
    );
	
	$styleCO = array(
			"fill" => array(
				"type" => PHPExcel_Style_Fill::FILL_SOLID,
				"color" => array("rgb" => "FFFF00")
			)
    );
	
	$styleCS = array(
			"fill" => array(
				"type" => PHPExcel_Style_Fill::FILL_SOLID,
				"color" => array("rgb" => "BE703B")
			)
    );

	$activeSheet = 0;
	
	/*
	 * Sheet No 1 - Chemical List
	 */
	$objPHPExcel->setActiveSheetIndex($activeSheet);
	$objPHPExcel->getActiveSheet()->setTitle("Chemical List");
	$objPHPExcel->getActiveSheet()->setCellValue("A1", "Chemical Name");
	$objPHPExcel->getActiveSheet()->setCellValue("B1", "Chemical CAS");
	$objPHPExcel->getActiveSheet()->setCellValue("C1", "Classified Chemical");
	$objPHPExcel->getActiveSheet()->setCellValue("D1", "Physical Hazard Classification");
	$objPHPExcel->getActiveSheet()->setCellValue("E1", "Health Hazard Classification");
	$objPHPExcel->getActiveSheet()->setCellValue("F1", "Environment Hazard Classification");
	$objPHPExcel->getActiveSheet()->freezePane("A2");

	$sql = "SELECT Chemical_ID, Chemical_Name, Chemical_CAS FROM tbl_Chemical WHERE isActive = 1 AND isNew = 0 AND isDeleted = 0 ORDER BY Chemical_Name";
	$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
	$i = 1;
	while($row = $db->sql_fetchrow($res)){
		$i++;
				
		$pHazard = "";
		$hHazard = "";
		$eHazard = "";
		
		$dCols 	= "Classified_ID,Category_ID";
		$dTable = "tbl_Chemical_Classified";
		$dJoin	= "";
		$dWhere = array("AND Chemical_ID" => "= ".quote_smart($row["Chemical_ID"]));
		$arrayData = _get_arrayData($dCols,$dTable,$dJoin,$dWhere);
		foreach($arrayData as $data){
			$classified = $data["Classified_ID"];
			$category 	= !empty($data["Category_ID"]) ? $data["Category_ID"] : 0;
		
			$sqlC = "SELECT CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID),Category_Name) AS clName1 FROM tbl_Hazard_Category WHERE Category_ID IN (". quote_smart($category,false) .") AND Type_ID = 1";
			$resC = $db->sql_query($sqlC,END_TRANSACTION) or die(print_r($db->sql_error()));
			while($rowC = $db->sql_fetchrow($resC)){
				$pHazard .= $rowC["clName1"] ."; ";
			}
			
			$sqlC = "SELECT CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID),Category_Name) AS clName2 FROM tbl_Hazard_Category WHERE Category_ID IN (". quote_smart($category,false) .") AND Type_ID = 2";
			$resC = $db->sql_query($sqlC,END_TRANSACTION) or die(print_r($db->sql_error()));
			while($rowC = $db->sql_fetchrow($resC)){
				$hHazard .= $rowC["clName2"] ."; ";
			}
			
			$sqlC = "SELECT CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID),Category_Name) AS clName3 FROM tbl_Hazard_Category WHERE Category_ID IN (". quote_smart($category,false) .") AND Type_ID = 3";
			$resC = $db->sql_query($sqlC,END_TRANSACTION) or die(print_r($db->sql_error()));
			while($rowC = $db->sql_fetchrow($resC)){
				$eHazard .= $rowC["clName3"] ."; ";
			}
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue( "A" . $i, trim($row["Chemical_Name"]) )
									  ->setCellValue( "B" . $i, trim($row["Chemical_CAS"]) )
									  ->setCellValue( "C" . $i, ((count($arrayData)>0)?"Classified":"") )
									  ->setCellValue( "D" . $i, $pHazard )
									  ->setCellValue( "E" . $i, $hHazard )
									  ->setCellValue( "F" . $i, $eHazard )
									  ;
	}

	// Set cell Width
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(80);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(40);
	$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
	$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(70);
	$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(70);
	$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(70);
	
	// Set sheet security
	$objPHPExcel->getActiveSheet()->getProtection()->setPassword($pswd);
	$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
	$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
	$activeSheet++;
	
	/*
	 * Sheet No 2 - Physical Form
	 */
	$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
	$objPHPExcel->setActiveSheetIndex($activeSheet);
	$objPHPExcel->getActiveSheet()->setTitle("Physical Form");	
	$objPHPExcel->getActiveSheet()->setCellValue("A1", "Physical Form");
	$objPHPExcel->getActiveSheet()->setCellValue("B1", "Code");
	$objPHPExcel->getActiveSheet()->freezePane("A2");
	
	
	$sql = "SELECT Form_Name, Form_Code FROM tbl_Chemical_Form WHERE Active = 1";
	$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
	$i = 1;
	while($row = $db->sql_fetchrow($res)){
		$i++;
		
		$objPHPExcel->getActiveSheet()->setCellValue( "A" . $i, $row["Form_Name"] )
									  ->setCellValue( "B" . $i, $row["Form_Code"] )
									  ;
	}
	$objPHPExcel->getActiveSheet()->getStyle("B1:B".$i)->applyFromArray($stylePF);

	// Set cell Width
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(30);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(15);

	// Set sheet security
	$objPHPExcel->getActiveSheet()->getProtection()->setPassword($pswd);
	$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
	$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
	$activeSheet++;
	
	/*
	 * Sheet No 3 - Physical Hazard Classification
	 */
	$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
	$objPHPExcel->setActiveSheetIndex($activeSheet);
	$objPHPExcel->getActiveSheet()->setTitle("Physical Hazard Classification");	
	$objPHPExcel->getActiveSheet()->setCellValue("A1", "Hazard Classification");
	$objPHPExcel->getActiveSheet()->setCellValue("B1", "Hazard Statement");
	// $objPHPExcel->getActiveSheet()->setCellValue("C1", "H-Code");
	$objPHPExcel->getActiveSheet()->freezePane("A2");
	
	$sql = "SELECT Category_ID, CONCAT_WS(': ', (SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID), Category_Name) AS clName,"
		. " Statement_ID"
		. " FROM tbl_Hazard_Category"
		. " WHERE 1"
		. " AND Type_ID = 1"
		. " AND Active = 1"
		. " AND isDeleted = 0"
		. " ORDER BY clName";
	$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
	$i = 1;
	while($row = $db->sql_fetchrow($res)){
		$i++;
		
		$stmt 		= explode(",",$row["Statement_ID"]);
		$hStatement = "";
		$hCode 		= "";
		if(count($stmt) > 0){
			foreach($stmt as $sId){
				$hStmt 		= trim(_get_StrFromCondition("tbl_Hazard_Statement","Statement_Desc","Statement_ID",$sId));
				$hStatement .= !empty($hStmt) ? "[". $hStmt ."], " : "";
				$hCode 		.= trim(_get_StrFromCondition("tbl_Hazard_Statement","Statement_Code","Statement_ID",$sId)) .", ";
			}
			$hStatement = substr_replace( $hStatement, "", -2 );
			$hCode 		= substr_replace( $hCode, "", -2 );
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue( "A" . $i, $row["clName"] )
									  ->setCellValue( "B" . $i, $hStatement )
									  // ->setCellValue( "C" . $i, $hCode )
									  ;
	}
	$objPHPExcel->getActiveSheet()->getStyle("A1:A".$i)->applyFromArray($stylePH);

	// Set cell Width
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(80);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(50);
	// $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);

	// Set sheet security
	$objPHPExcel->getActiveSheet()->getProtection()->setPassword($pswd);
	$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
	$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
	$activeSheet++;
	
	/*
	 * Sheet No 4 - Health Hazard Classification
	 */
	$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
	$objPHPExcel->setActiveSheetIndex($activeSheet);
	$objPHPExcel->getActiveSheet()->setTitle("Health Hazard Classification");	
	$objPHPExcel->getActiveSheet()->setCellValue("A1", "Hazard Classification");
	$objPHPExcel->getActiveSheet()->setCellValue("B1", "Hazard Statement");
	// $objPHPExcel->getActiveSheet()->setCellValue("C1", "H-Code");
	$objPHPExcel->getActiveSheet()->freezePane("A2");
	
	$sql = "SELECT Category_ID, CONCAT_WS(': ', (SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID), Category_Name) AS clName,"
		. " Statement_ID"
		. " FROM tbl_Hazard_Category"
		. " WHERE 1"
		. " AND Type_ID = 2"
		. " AND Active = 1"
		. " AND isDeleted = 0"
		. " ORDER BY clName";
	$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
	$i = 1;
	while($row = $db->sql_fetchrow($res)){
		$i++;
		
		$stmt 		= explode(",",$row["Statement_ID"]);
		$hStatement = "";
		$hCode 		= "";
		if(count($stmt) > 0){
			foreach($stmt as $sId){
				$hStmt 		= trim(_get_StrFromCondition("tbl_Hazard_Statement","Statement_Desc","Statement_ID",$sId));
				$hStatement .= !empty($hStmt) ? "[". $hStmt ."], " : "";
				$hCode 		.= trim(_get_StrFromCondition("tbl_Hazard_Statement","Statement_Code","Statement_ID",$sId)) .", ";
			}
			$hStatement = substr_replace( $hStatement, "", -2 );
			$hCode 		= substr_replace( $hCode, "", -2 );
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue( "A" . $i, $row["clName"] )
									  ->setCellValue( "B" . $i, $hStatement )
									  // ->setCellValue( "C" . $i, $hCode )
									  ;
	}
	$objPHPExcel->getActiveSheet()->getStyle("A1:A".$i)->applyFromArray($styleHH);

	// Set cell Width
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(80);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(50);
	// $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);

	// Set sheet security
	$objPHPExcel->getActiveSheet()->getProtection()->setPassword($pswd);
	$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
	$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
	$activeSheet++;
	
	/*
	 * Sheet No 5 - Environment Hazard Classification
	 */
	$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
	$objPHPExcel->setActiveSheetIndex($activeSheet);
	$objPHPExcel->getActiveSheet()->setTitle("Env. Hazard Classification");	
	$objPHPExcel->getActiveSheet()->setCellValue("A1", "Hazard Classification");
	$objPHPExcel->getActiveSheet()->setCellValue("B1", "Hazard Statement");
	// $objPHPExcel->getActiveSheet()->setCellValue("C1", "H-Code");
	$objPHPExcel->getActiveSheet()->freezePane("A2");
	
	$sql = "SELECT Category_ID, CONCAT_WS(': ', (SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID), Category_Name) AS clName,"
		. " Statement_ID"
		. " FROM tbl_Hazard_Category"
		. " WHERE 1"
		. " AND Type_ID = 3"
		. " AND Active = 1"
		. " AND isDeleted = 0"
		. " ORDER BY clName";
	$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
	$i = 1;
	while($row = $db->sql_fetchrow($res)){
		$i++;
		
		$stmt 		= explode(",",$row["Statement_ID"]);
		$hStatement = "";
		$hCode 		= "";
		if(count($stmt) > 0){
			foreach($stmt as $sId){
				$hStmt 		= trim(_get_StrFromCondition("tbl_Hazard_Statement","Statement_Desc","Statement_ID",$sId));
				$hStatement .= !empty($hStmt) ? "[". $hStmt ."], " : "";
				$hCode 		.= trim(_get_StrFromCondition("tbl_Hazard_Statement","Statement_Code","Statement_ID",$sId)) .", ";
			}
			$hStatement = substr_replace( $hStatement, "", -2 );
			$hCode 		= substr_replace( $hCode, "", -2 );
		}
		
		$objPHPExcel->getActiveSheet()->setCellValue( "A" . $i, $row["clName"] )
									  ->setCellValue( "B" . $i, $hStatement )
									  // ->setCellValue( "C" . $i, $hCode )
									  ;
	}
	$objPHPExcel->getActiveSheet()->getStyle("A1:A".$i)->applyFromArray($styleEH);

	// Set cell Width
	$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(80);
	$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(50);
	// $objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);

	// Set sheet security
	$objPHPExcel->getActiveSheet()->getProtection()->setPassword($pswd);
	$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
	$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
	$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
	$activeSheet++;

	if($type == 'mix'){
		/*
		 * Sheet No 6 - Environment Hazard Classification
		 */
		$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
		$objPHPExcel->setActiveSheetIndex($activeSheet);
		$objPHPExcel->getActiveSheet()->setTitle('Chemical Composition');	
		$objPHPExcel->getActiveSheet()->setCellValue('A1', "Composition");
		$objPHPExcel->getActiveSheet()->setCellValue('B1', "Code");
		$objPHPExcel->getActiveSheet()->freezePane('A2');
		
		$i = 1;
		foreach($arrComposition as $comp){
			$i++;
			
			$objPHPExcel->getActiveSheet()->setCellValue( "A" . $i, $comp["label2"] )
										  ->setCellValue( "B" . $i, $comp["code"] )
									  ;
		}
		$objPHPExcel->getActiveSheet()->getStyle("B1:B". ($i+1) )->applyFromArray($styleCO);
		
		$objPHPExcel->getActiveSheet()->setCellValue( "A" . ($i+1), "Number with 3 decimals format" )
									  ->setCellValue( "B" . ($i+1), "0.000" )
								  ;
		$objPHPExcel->getActiveSheet()->getStyle( "B". ($i+1) )->getNumberFormat()->setFormatCode("##0.000");
		

		// Set cell Width
		$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(10);

		// Set sheet security
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword($pswd);
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
		$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
		$activeSheet++;
		
		/*
		 * Sheet No 7 - Environment Hazard Classification
		 */
		$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
		$objPHPExcel->setActiveSheetIndex($activeSheet);
		$objPHPExcel->getActiveSheet()->setTitle("Chemical Source");	
		$objPHPExcel->getActiveSheet()->setCellValue("A1", "Chemical Source");
		$objPHPExcel->getActiveSheet()->setCellValue("B1", "Code");
		$objPHPExcel->getActiveSheet()->freezePane("A2");
		
		$i = 1;
		foreach($arrSource as $source){
			$i++;
			
			$objPHPExcel->getActiveSheet()->setCellValue( "A" . $i, $source["label"] )
										  ->setCellValue( "B" . $i, $source["code"] )
									  ;
		}
		$objPHPExcel->getActiveSheet()->getStyle("B1:B".$i)->applyFromArray($styleCS);

		// Set cell Width
		$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(10);

		// Set sheet security
		$objPHPExcel->getActiveSheet()->getProtection()->setPassword($pswd);
		$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true); // This should be enabled in order to enable any of the following!
		$objPHPExcel->getActiveSheet()->getProtection()->setSort(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setInsertRows(true);
		$objPHPExcel->getActiveSheet()->getProtection()->setFormatCells(true);
		$activeSheet++;
		
		
		/*
		 * Sheet No 8 - Example
		 */
		$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
		$objPHPExcel->setActiveSheetIndex($activeSheet);
		$objPHPExcel->getActiveSheet()->setTitle("Example Submission");
		$objPHPExcel->getActiveSheet()->setCellValue("A2", "Remark");
		$objPHPExcel->getActiveSheet()->setCellValue("B2", "Write your remark here");
		
		$objPHPExcel->getActiveSheet()->setCellValue("A4", "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B4", "Product Name");
		$objPHPExcel->getActiveSheet()->setCellValue("C4", "Physical Form");
		$objPHPExcel->getActiveSheet()->setCellValue("D4", "ID Number (if any)");
		$objPHPExcel->getActiveSheet()->setCellValue("E4", "No of Ingredient");
		$objPHPExcel->getActiveSheet()->setCellValue("F4", "Physical Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("G4", "Health Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("H4", "Environment Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("I4", "Quantity Imported - tonne/year");
		$objPHPExcel->getActiveSheet()->setCellValue("J4", "Quantity Imported - tonne/year");
		$objPHPExcel->getActiveSheet()->freezePane("A5");
		
		
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("J")->setWidth(20);
		
		$objPHPExcel->getActiveSheet()->getStyle("C4")->applyFromArray($stylePF);
		$objPHPExcel->getActiveSheet()->getStyle("F4")->applyFromArray($stylePH);
		$objPHPExcel->getActiveSheet()->getStyle("G4")->applyFromArray($styleHH);
		$objPHPExcel->getActiveSheet()->getStyle("H4")->applyFromArray($styleEH);
		
		$objPHPExcel->getActiveSheet()->getStyle("B2:E2")->applyFromArray($styleArray2);
		$objPHPExcel->getActiveSheet()->mergeCells("B2:E2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:J4")->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle("I5:I99")->getNumberFormat()->setFormatCode("#,###.0");
		$objPHPExcel->getActiveSheet()->getStyle("J5:J99")->getNumberFormat()->setFormatCode("#,###.0");
		
		$objPHPExcel->getActiveSheet()->setCellValue("A5", "1");
		$objPHPExcel->getActiveSheet()->setCellValue("B5", "Your Product Name");
		$objPHPExcel->getActiveSheet()->setCellValue("C5", "S/L/G");
		$objPHPExcel->getActiveSheet()->setCellValue("D5", "");
		$objPHPExcel->getActiveSheet()->setCellValue("E5", "2");
		$objPHPExcel->getActiveSheet()->setCellValue("F5", "Corrosive to Metals: Category 1; Explosives: Division 1.1 (use semi-colon \";\" if there is more than ONE hazard classification)");
		$objPHPExcel->getActiveSheet()->setCellValue("G5", "");
		$objPHPExcel->getActiveSheet()->setCellValue("H5", "");
		$objPHPExcel->getActiveSheet()->setCellValue("I5", "30.0");
		$objPHPExcel->getActiveSheet()->setCellValue("J5", "10.0");
		$activeSheet++;
		
		
		/*
		 * Sheet No 9 - Example Ingredient
		 */		
		$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
		$objPHPExcel->setActiveSheetIndex($activeSheet);
		$objPHPExcel->getActiveSheet()->setTitle("Example Ingredient");	
		$objPHPExcel->getActiveSheet()->setCellValue("A1", "Key");
		$objPHPExcel->getActiveSheet()->setCellValue("B1", "Chemical Name");
		$objPHPExcel->getActiveSheet()->setCellValue("C1", "CAS No");
		$objPHPExcel->getActiveSheet()->setCellValue("D1", "Composition");
		$objPHPExcel->getActiveSheet()->setCellValue("E1", "Chemical Source");
		$objPHPExcel->getActiveSheet()->freezePane("A2");
		
		// Set cell Width
		// $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(60);
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(50);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(35);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);
		
		$objPHPExcel->getActiveSheet()->getStyle("D1")->applyFromArray($styleCO);
		$objPHPExcel->getActiveSheet()->getStyle("E1")->applyFromArray($styleCS);
		
		$objPHPExcel->getActiveSheet()->getStyle("A1:E1")->applyFromArray($styleArray);
				
		$objPHPExcel->getActiveSheet()->setCellValue("A2", "1");
		$objPHPExcel->getActiveSheet()->setCellValue("B2", "Chemical Name 1");
		$objPHPExcel->getActiveSheet()->setCellValue("C2", "xxxxxx-xx-xxxx");
		$objPHPExcel->getActiveSheet()->setCellValue("D2", "A1/A2/A3/A4/A5/A6/A7/xx.000");
		$objPHPExcel->getActiveSheet()->setCellValue("E2", "LM/IM");
				
		$objPHPExcel->getActiveSheet()->setCellValue("A3", "1");
		$objPHPExcel->getActiveSheet()->setCellValue("B3", "Chemical Name 2");
		$objPHPExcel->getActiveSheet()->setCellValue("C3", "xxxxxx-xx-xxxx");
		$objPHPExcel->getActiveSheet()->setCellValue("D3", "A1/A2/A3/A4/A5/A6/A7/xx.000");
		$objPHPExcel->getActiveSheet()->setCellValue("E3", "LM/IM");
	}
	else{
		/*
		 * Sheet No 6 - Example Submission
		 */
		$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
		$objPHPExcel->setActiveSheetIndex($activeSheet);
		$objPHPExcel->getActiveSheet()->setTitle("Example Submission");
		$objPHPExcel->getActiveSheet()->setCellValue("A2", "Remark");
		$objPHPExcel->getActiveSheet()->setCellValue("B2", "Write your remark here");
		
		$objPHPExcel->getActiveSheet()->setCellValue("A4", "No");
		$objPHPExcel->getActiveSheet()->setCellValue("B4", "Physical Form");
		$objPHPExcel->getActiveSheet()->setCellValue("C4", "Product/Chemical Name");
		$objPHPExcel->getActiveSheet()->setCellValue("D4", "CAS No");
		$objPHPExcel->getActiveSheet()->setCellValue("E4", "Physical Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("F4", "Health Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("G4", "Environment Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("H4", "Quantity Imported - tonne/year");
		$objPHPExcel->getActiveSheet()->setCellValue("I4", "Quantity Manufactured - tonne/year");
		$objPHPExcel->getActiveSheet()->freezePane("A5");
		
		$objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("C")->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension("F")->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(20);
		
		$objPHPExcel->getActiveSheet()->getStyle("B4")->applyFromArray($stylePF);
		$objPHPExcel->getActiveSheet()->getStyle("E4")->applyFromArray($stylePH);
		$objPHPExcel->getActiveSheet()->getStyle("F4")->applyFromArray($styleHH);
		$objPHPExcel->getActiveSheet()->getStyle("G4")->applyFromArray($styleEH);
		
		$objPHPExcel->getActiveSheet()->getStyle("B2:E2")->applyFromArray($styleArray2);
		$objPHPExcel->getActiveSheet()->mergeCells("B2:E2");
		$objPHPExcel->getActiveSheet()->getStyle("A4:J4")->applyFromArray($styleArray);
		$objPHPExcel->getActiveSheet()->getStyle("H5:I99")->getNumberFormat()->setFormatCode("#,###.0");
		$objPHPExcel->getActiveSheet()->getStyle("I5:J99")->getNumberFormat()->setFormatCode("#,###.0");
		
		$objPHPExcel->getActiveSheet()->setCellValue("A5", "1");
		$objPHPExcel->getActiveSheet()->setCellValue("B5", "S/L/G");
		$objPHPExcel->getActiveSheet()->setCellValue("C5", "Your Product/Chemical Name");
		$objPHPExcel->getActiveSheet()->setCellValue("D5", "xxxxxx-xx-xxxx");
		$objPHPExcel->getActiveSheet()->setCellValue("E5", "Corrosive to Metals: Category 1; Explosives: Division 1.1 (use semi-colon \";\" if there is more than ONE hazard classification)");
		$objPHPExcel->getActiveSheet()->setCellValue("F5", "");
		$objPHPExcel->getActiveSheet()->setCellValue("G5", "");
		$objPHPExcel->getActiveSheet()->setCellValue("H5", "10.0");
		$objPHPExcel->getActiveSheet()->setCellValue("I5", "30.0");
	}

	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);
	

	/*
	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="substance_reference.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
	*/
	
	$objPHPExcel->getSecurity()->setLockWindows(true);
	$objPHPExcel->getSecurity()->setLockStructure(true);
	$objPHPExcel->getSecurity()->setWorkbookPassword($pswd);

	if($type=="sub")
		$filename = "Substance-Reference";
	elseif($type=="mix")
		$filename = "Mixture-Reference";
	else
		$filename = "Reference";

	// $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	// $objWriter->save( $sys_config['upload_path'] . $filename . '.xls' );
	// echo json_encode(array('url'=>$sys_config['upload_path'] . $filename . '.xls'));
	
	// $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
	// $objWriter->save( $sys_config['upload_path'] . $filename . '.xlsx' );
	// echo json_encode(array('url'=>$sys_config['upload_path'] . $filename . '.xlsx'));
	
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
	$objWriter->save( $sys_config["upload_path"] . $filename . ".xlsx" );
	echo json_encode(array("url"=>$sys_config["upload_path"] . $filename . ".xlsx"));