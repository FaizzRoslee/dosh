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
	
	include_once $sys_config['includes_path'].'arrayCommon.php';
	
	error_reporting(E_ALL);

	require_once $sys_config['includes_path'] ."Classes/PHPExcel/IOFactory.php";

	$inputFileName = $_POST['fpath'];

	$ex_filename = explode('.',$inputFileName);

	$ext = $ex_filename[ count($ex_filename)-1 ];

	if($ext=='xls')
		$inputFileType = 'Excel5';	

	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
 //$objReader->setInputEncoding('UTF-8');              // linuxhouse_debug_20211104
	$objReader->setLoadAllSheets();

	$objPHPExcel = $objReader->load($inputFileName);
 // $objPHPExcel->setInputEncoding('ISO-8859-1');              // linuxhouse_debug_20211104
	$loadedSheetNames = $objPHPExcel->getSheetNames();

	$str 		= '';
	$arrData 	= array();
	
	foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {

		if( $sheetIndex > 1 ) break;
		
		$sheetData = $objPHPExcel->setActiveSheetIndex($sheetIndex)->toArray(null,false,false,true);
		
			
		if( $sheetIndex == 0 ){
			
			$remark = $sheetData['2']['B'];
		
			$i = 5;
			while( isset($sheetData[$i]['A']) && $sheetData[$i]['A'] != '' ){
				$bil 		= $sheetData[$i]['A'];
				// $remark 	= ''; // $sheetData[$i]['B'];
				$product 	= $sheetData[$i]['B'];
				$form 		= $sheetData[$i]['C'];
				$idnumber	= $sheetData[$i]['D'];
				$ingredient	= $sheetData[$i]['E'];
				$pHazard	= $sheetData[$i]['F'];
				$hHazard	= $sheetData[$i]['G'];
				$eHazard	= $sheetData[$i]['H'];
				$import		= $sheetData[$i]['I'];
				$manufact	= $sheetData[$i]['J'];
				
				$arrData[ $bil ]['details'] = array(
												'bil' => $bil,
												'remark' => '', // $remark,
												'product' => $product,
												'form' => $form,
												'idnumber' => $idnumber,
												'ingredient' => $ingredient,
												'pHazard' => $pHazard,
												'hHazard' => $hHazard,
												'eHazard' => $eHazard,
												'import' => $import,
												'manufact' => $manufact
											);
				
				$i++;
			}
		}
		else{
			$i = 2;
			while( isset($sheetData[$i]['A']) && $sheetData[$i]['A'] != '' ){			
				$key 		= $sheetData[$i]['A'];
				$chemical 	= $sheetData[$i]['B'];
				$casno 		= $sheetData[$i]['C'];
				$composition = $sheetData[$i]['D'];
				$source		= $sheetData[$i]['E'];
			
				$arrData[ $key ]['mixture'][] = array(
												'key' => $key,
												'chemical' => $chemical,
												'casno' => $casno,
												'composition' => $composition,
												'source' => $source
											);
											
				$i++;
			}
		}
	}
	
	$str .= '
		<hr />
		<table cellpadding="3" class="ui-widget">
			<tr>
				<td class="ui-widget-header" width="20%">'. _LBL_REMARK .'</td>
				<td>'. $remark .'<input type="hidden" name="data-remark" value="'. $remark .'" /></td>
			</tr>
		</table>
		<table cellpadding="3" class="ui-widget">
			<tr class="ui-widget-header">
				<td>'. _LBL_BIL .'</td>
				<!--<td>'. _LBL_REMARK .'</td>-->
				<td>'. _LBL_PRODUCT_NAME .'</td>
				<td>'. _LBL_PHYSICAL_FORM .'</td>
				<td>'. _LBL_ID_NUMBER .'</td>
				<td>'. _LBL_NO_OF_INGREDIENT .'</td>
				<td>'. _LBL_PHY_HAZARD_CLASS .'</td>
				<td>'. _LBL_HEALTH_HAZARD_CLASS .'</td>
				<td>'. _LBL_ENV_HAZARD_CLASS .'</td>
				<td>'. _LBL_TOTAL_QUANTITY_SUPPLY .'</td>
				<td>'. _LBL_TOTAL_QUANTITY_USE .'</td>
				<td><input type="checkbox" name="ch_confirm2_all[]" class="cl-confirm2_all" /></td>
			</tr>';
		
	foreach($arrData as $bil => $data){
		$import 	= number_format($data['details']['import'],1,'.',',');
		$manufact 	= number_format($data['details']['manufact'],1,'.',',');
		
		$str .= '
			<tr>
				<td rowspan="2" style="text-align:center;">'. $bil .'</td>
				<!--<td>'. $data['details']['remark'] .'<input type="hidden" name="data-remark['. $bil .']" value="'. $data['details']['remark'] .'" /></td>-->
				<td>'. $data['details']['product'] .'<input type="hidden" name="data-product['. $bil .']" value="'. $data['details']['product'] .'" /></td>
				<td>'. getForm($data['details']['form'],'Form_Name') .'<input type="hidden" name="data-form['. $bil .']" value="'. $data['details']['form'] .'" /></td>
				<td>'. $data['details']['idnumber'] .'<input type="hidden" name="data-idnumber['. $bil .']" value="'. $data['details']['idnumber'] .'" /></td>
				<td>'. $data['details']['ingredient'] .'<input type="hidden" name="data-ingredient['. $bil .']" value="'. $data['details']['ingredient'] .'" /></td>
				<td>'. $data['details']['pHazard'] .'<input type="hidden" name="data-pHazard['. $bil .']" value="'. $data['details']['pHazard'] .'" /></td>
				<td>'. $data['details']['hHazard'] .'<input type="hidden" name="data-hHazard['. $bil .']" value="'. $data['details']['hHazard'] .'" /></td>
				<td>'. $data['details']['eHazard'] .'<input type="hidden" name="data-eHazard['. $bil .']" value="'. $data['details']['eHazard'] .'" /></td>
				<td style="text-align:right;">'. $import .'<input type="hidden" name="data-import['. $bil .']" value="'. $data['details']['import'] .'" /></td>
				<td style="text-align:right;">'. $manufact .'<input type="hidden" name="data-manufact['. $bil .']" value="'. $data['details']['manufact'] .'" /></td>
				<td rowspan="2"><input type="checkbox" name="ch_confirm2[]" value="'. $bil .'" class="cl-confirm2" /></td>
			</tr>
			<tr>
				<td colspan="9"><table cellpadding="3" class="ui-widget">
					<tr class="ui-widget-header">
						<td style="text-align:center;">#</td>
						<td>'. _LBL_CHEMICAL_NAME .'</td>
						<td style="text-align:center;">'. _LBL_CAS_NO .'</td>
						<td>'. _LBL_COMPOSITION .'</td>
						<td>'. _LBL_CHEMICAL_SOURCE .'</td>
					</tr>';
			
			$i = 0;
			foreach($data['mixture'] as $data2){
				$i++;
				
				$chName = $data2['chemical'];
				$chCas	= $data2['casno'];
				
				$composition = ((multi_in_array($data2['composition'],$arrComposition)) ? getComposition($data2['composition'],'label') : number_format((float)$data2['composition'],3,'.',','));
				
				$str .= '
					<tr>
						<td style="text-align:center;">'. $i .'</td>
						<td>'. $chName .'<input type="hidden" name="data-chemical['. $bil .']['. $i .']" value="'. $chName .'" /></td>
						<td style="text-align:center;">'. $chCas .'<input type="hidden" name="data-casno['. $bil .']['. $i .']" value="'. $chCas .'" /></td>
						<td>'. $composition .'<input type="hidden" name="data-composition['. $bil .']['. $i .']" value="'. $data2['composition'] .'" /></td>
						<td>'. getSource($data2['source'],'label') .'<input type="hidden" name="data-source['. $bil .']['. $i .']" value="'. $data2['source'] .'" /></td>
					</tr>	
				';
			}
					
		$str .= '
				</table></td>
			</tr>';
	}



	$arr = array(
			'excel' => $str
			);
	echo json_encode($arr);
	
	
?>
