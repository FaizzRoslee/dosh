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

	if($type == "sub"){
		/*
		 * Sheet No 1 - Substance
		 */
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle("Master-Substance");
		
		$objPHPExcel->getActiveSheet()->setCellValue("A2", "Remark");
		
		$objPHPExcel->getActiveSheet()->setCellValue("A4", "No");
		// $objPHPExcel->getActiveSheet()->setCellValue("B1", "Remark");
		$objPHPExcel->getActiveSheet()->setCellValue("B4", "Physical Form");
		$objPHPExcel->getActiveSheet()->setCellValue("C4", "Chemical Name");
		$objPHPExcel->getActiveSheet()->setCellValue("D4", "CAS No");
		$objPHPExcel->getActiveSheet()->setCellValue("E4", "Physical Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("F4", "Health Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("G4", "Environment Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("H4", "Quantity Imported - tonne/year");
		$objPHPExcel->getActiveSheet()->setCellValue("I4", "Quantity Manufactured - tonne/year");
		$objPHPExcel->getActiveSheet()->freezePane("A5");
		
		// Set cell Width
		// $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(60);
		// $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(30);
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
	}
	elseif($type == "mix"){
		/*
		 * Sheet No 1 - Submission
		 */
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle("Master-Mixture");	
		
		$objPHPExcel->getActiveSheet()->setCellValue("A2", "Remark");
		
		$objPHPExcel->getActiveSheet()->setCellValue("A4", "No");
		// $objPHPExcel->getActiveSheet()->setCellValue("B1", "Remark");
		$objPHPExcel->getActiveSheet()->setCellValue("B4", "Product Name");
		$objPHPExcel->getActiveSheet()->setCellValue("C4", "Physical Form");
		$objPHPExcel->getActiveSheet()->setCellValue("D4", "ID Number (if any)");
		$objPHPExcel->getActiveSheet()->setCellValue("E4", "No of Ingredient");
		$objPHPExcel->getActiveSheet()->setCellValue("F4", "Physical Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("G4", "Health Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("H4", "Environment Hazard Classification");
		$objPHPExcel->getActiveSheet()->setCellValue("I4", "Quantity Imported - tonne/year");
		$objPHPExcel->getActiveSheet()->setCellValue("J4", "Quantity Manufactured - tonne/year");
		$objPHPExcel->getActiveSheet()->freezePane("A5");
		
		// Set cell Width
		// $objPHPExcel->getActiveSheet()->getColumnDimension("A")->setWidth(60);
		// $objPHPExcel->getActiveSheet()->getColumnDimension("B")->setWidth(30);
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
		
		/*
		 * Sheet No 2 - Mixture
		 */
		$objPHPExcel->createSheet();  // Create a new sheet and set it as the active sheet
		$objPHPExcel->setActiveSheetIndex(1);
		$objPHPExcel->getActiveSheet()->setTitle("Sub-Master-Mixture");	
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
		$objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension("E")->setWidth(15);
		
		$objPHPExcel->getActiveSheet()->getStyle("D1")->applyFromArray($styleCO);
		$objPHPExcel->getActiveSheet()->getStyle("E1")->applyFromArray($styleCS);
		$objPHPExcel->getActiveSheet()->getStyle("A1:E1")->applyFromArray($styleArray);
	}


	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);

	/*

	// Redirect output to a client�s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="substance_reference.xls"');
	header('Cache-Control: max-age=0');

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;

	*/

	if($type=="sub")
		$filename = "Master-Substance";
	elseif($type=="mix")
		$filename = "Master-Mixture";
	else
		$filename = "Master";

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel5");
	$objWriter->save( $sys_config["upload_path"] . $filename . ".xls" );
	echo json_encode(array("url"=>$sys_config["upload_path"] . $filename . ".xls"));