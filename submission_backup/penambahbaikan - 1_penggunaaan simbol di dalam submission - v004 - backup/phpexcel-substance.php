<?php	
	/*
	 *
	 */
	include_once '../includes/sys_config.php';
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
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
	if(isset($_SESSION['lang'])){ $fileLang = $_SESSION['lang'].'.php'; }
	else{ $fileLang = 'eng.php'; }
	include_once $sys_config['languages_path'].$fileLang;
	
	error_reporting(E_ALL);

	require_once $sys_config['includes_path'] ."Classes/PHPExcel/IOFactory.php";

	$inputFileName = $_POST['fpath'];

	$ex_filename = explode('.',$inputFileName);

	$ext = $ex_filename[ count($ex_filename)-1 ];

	if($ext=='xls')
		$inputFileType = 'Excel5';

// Excel5

	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
 // $objReader->setInputEncoding('iso-8859-1');              // linuxhouse_debug_20211104
	$objReader->setLoadAllSheets();

	$objPHPExcel = $objReader->load($inputFileName);
 // $objPHPExcel->setInputEncoding('ISO-8859-1');              // linuxhouse_debug_20211104		
	$loadedSheetNames = $objPHPExcel->getSheetNames();

	$str = '';
	$count_data = 1;
	
	$sheetData = $objPHPExcel->setActiveSheetIndex(0)->toArray(null,false,false,true);
	
	$str .= '
		<hr />
		<table cellpadding="3" class="ui-widget">
			<tr>
				<td class="ui-widget-header" width="20%">'. _LBL_REMARK .'</td>
				<td>'. $sheetData['2']['B'] .'<input type="hidden" name="data-remark" value="'. $sheetData['2']['B'] .'" /></td>
			</tr>
		</table>
		<table cellpadding="3" class="ui-widget">
			<tr class="ui-widget-header">
				<td>'. _LBL_BIL .'</td>
				<!--<td>'. _LBL_REMARK .'</td>-->
				<td>'. _LBL_PHYSICAL_FORM .'</td>
				<td>'. _LBL_TRADE_PRODUCT .'</td>
				<td>'. _LBL_CAS_NO .'</td>
				<td>'. _LBL_PHY_HAZARD_CLASS .'</td>
				<td>'. _LBL_HEALTH_HAZARD_CLASS .'</td>
				<td>'. _LBL_ENV_HAZARD_CLASS .'</td>
				<td>'. _LBL_TOTAL_QUANTITY_SUPPLY .'</td>
				<td>'. _LBL_TOTAL_QUANTITY_USE .'</td>
				<td><input type="checkbox" name="ch_confirm1_all[]" class="cl-confirm1_all" /></td>
			</tr>';
		
	$i = 5;
	while( isset($sheetData[$i]['A']) && $sheetData[$i]['A'] != ''){
		$bil = $sheetData[$i]['A'];
		
		$import 	= number_format($sheetData[$i]['H'],1,'.',',');
		$manufact 	= number_format($sheetData[$i]['I'],1,'.',',');
		
		$chName = $sheetData[$i]['C'];
		$chCas	= $sheetData[$i]['D'];
		
		$phClass = $sheetData[$i]['E'];
		$heClass = $sheetData[$i]['F'];
		$enClass = $sheetData[$i]['G'];

		$pHazard = '';
		$hHazard = '';
		$eHazard = '';
		
		$chCatID 	= getChemicalCategory($chName,$chCas);
		
		if(!empty($chCatID)){
			$sqlC = "SELECT CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID),Category_Name) AS clName1 FROM tbl_Hazard_Category WHERE Category_ID IN (". quote_smart($chCatID,false) .") AND Type_ID = 1";
			$resC = $db->sql_query($sqlC,END_TRANSACTION) or die(print_r($db->sql_error()));
			while($rowC = $db->sql_fetchrow($resC)){
				$pHazard .= $rowC['clName1'] .'; ';
			}
			$phClass = $pHazard;
			
			$sqlC = "SELECT CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID),Category_Name) AS clName2 FROM tbl_Hazard_Category WHERE Category_ID IN (". quote_smart($chCatID,false) .") AND Type_ID = 2";
			$resC = $db->sql_query($sqlC,END_TRANSACTION) or die(print_r($db->sql_error()));
			while($rowC = $db->sql_fetchrow($resC)){
				$hHazard .= $rowC['clName2'] .'; ';
			}
			$heClass = $hHazard;
			
			$sqlC = "SELECT CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID),Category_Name) AS clName3 FROM tbl_Hazard_Category WHERE Category_ID IN (". quote_smart($chCatID,false) .") AND Type_ID = 3";
			$resC = $db->sql_query($sqlC,END_TRANSACTION) or die(print_r($db->sql_error()));
			while($rowC = $db->sql_fetchrow($resC)){
				$eHazard .= $rowC['clName3'] .'; ';
			}
			$enClass = $eHazard;
		}
		
		$str .= '
			<tr>
				<td style="text-align:center;">'. $bil .'</td>
				<!--<td>'. $sheetData[$i]['B'] .'<input type="hidden" name="data-remark['. $bil .']" value="'. $sheetData[$i]['B'] .'" /></td>-->
				<td>'. getForm($sheetData[$i]['B'],'Form_Name') .'<input type="hidden" name="data-form['. $bil .']" value="'. $sheetData[$i]['B'] .'" /></td>
				<td>'. $chName .'<input type="hidden" name="data-chemical['. $bil .']" value="'. $chName .'" /></td>
				<td>'. $chCas .'<input type="hidden" name="data-casno['. $bil .']" value="'. $chCas .'" /></td>
				<td>'. $phClass .'<input type="hidden" name="data-pHazard['. $bil .']" value="'. $phClass .'" /></td>
				<td>'. $heClass .'<input type="hidden" name="data-hHazard['. $bil .']" value="'. $heClass .'" /></td>
				<td>'. $enClass .'<input type="hidden" name="data-eHazard['. $bil .']" value="'. $enClass .'" /></td>
				<td style="text-align:right;">'. $import .'<input type="hidden" name="data-import['. $bil .']" value="'. $import .'" /></td>
				<td style="text-align:right;">'. $manufact .'<input type="hidden" name="data-manufact['. $bil .']" value="'. $manufact .'" /></td>
				<td style="text-align:center;"><input type="checkbox" name="ch_confirm1[]" value="'. $bil .'" class="cl-confirm1" /></td>
			</tr>';
			
		$i++;
	}
	
	$str .= '
		</table>';
	
	$arr = array(
			'excel' => $str
			);
	echo json_encode($arr);
	
	
	function getChemicalCategory($name='',$casno=''){
		global $db;
	
		$name = trim($name);
		$casno = str_replace(" ","", $casno);
		$sql = "SELECT IFNULL((SELECT Category_ID FROM tbl_Chemical_Classified WHERE Chemical_ID = tbl_Chemical.Chemical_ID),0) AS cat"
			. " FROM tbl_Chemical WHERE isActive = 1 AND isNew = 0 AND isDeleted = 0"
			. " AND TRIM(Chemical_Name) = ". quote_smart( $name )
			. " AND TRIM(Chemical_CAS) = ". quote_smart( $casno )
			;
		$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));

		if($db->sql_numrows($res) > 0){
			$row = $db->sql_fetchrow($res);
			$catID = $row['cat'];
		}
		else
			$catID = '';
			
		return $catID;
	}
?>
