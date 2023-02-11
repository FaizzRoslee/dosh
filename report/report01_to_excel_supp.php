<?php
	//============================================================
	// linuxhouse__20220710
	// FILE BEGIN 
	// filename: report01_to_excel_supp.php
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
	// $lvlType = isset($_GET["lvlType"]) ? $_GET["lvlType"] : "";
	$chemType = isset($_GET["chemType"]) ? $_GET["chemType"] : "";
	$rptYear = isset($_GET["rptYear"]) ? $_GET["rptYear"] : "";
	// $state = isset($_GET["state"]) ? $_GET["state"] : "";
	// $comp = isset($_GET["comp"]) ? $_GET["comp"] : "";
	
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

	$str = '<style>'
			.'.header3 { background-color: #33FFFF; }'
			.'.header4 { background-color: #33CCFF; }'
			.'.center { text-align: center; }'
			.'.rotate {
			  /* FF3.5+ */
			  -moz-transform: rotate(-90.0deg);
			  /* Opera 10.5 */
			  -o-transform: rotate(-90.0deg);
			  /* Saf3.1+, Chrome */
			  -webkit-transform: rotate(-90.0deg);
			  /* IE6,IE7 */
			  filter: progid: DXImageTransform.Microsoft.BasicImage(rotation=0.083);
			  /* IE8 */
			  -ms-filter: "progid:DXImageTransform.Microsoft.BasicImage(rotation=0.083)";
			  /* Standard */
			  transform: rotate(-90.0deg);
			}'
			.'</style>';
	$str .= '<h4>' . strtoupper(_LBL_REPORT) .': '. strtoupper(_LBL_REPORT_OVERALL) . '</h4>';
	// $str .= '<br />';
	$str .= '<table border="1">'
			.	'<tr>'
			.		'<td rowspan="2" style="width: 40px;" class="header3">#</td>'
			.		'<td rowspan="2" style="width: 150px;" class="header3">'. _LBL_SUBMISSION_ID .'</td>'
			.		'<td rowspan="2" style="width: 200px;" class="header3">'. _LBL_COMPANY_NAME .'</td>'
			.		'<td rowspan="2" style="width: 150px;" class="header3">'. _LBL_STATE .'</td>'
			.		'<td rowspan="2" style="width: 200px;" class="header3">'. _LBL_PRODUCT_NAME .'</td>'
			.		'<td rowspan="2" style="width: 200px;" class="header3">'. _LBL_CHEMICAL_NAME .'</td>';
			
	// if($lvlType=='all') $lvlType = '6,7,8';
	
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
					// ,"AND uc.State_Reg" => "IN ($state)"
					,"AND s.isDeleted" => "= 0"
					// ,"AND u.Level_ID" => "IN ($lvlType)"
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
	$submission_ids = array_unique($submission_ids);
	
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
	
	$hazard = '';
	foreach($arrHazard as $id => $h){
		$hazard_type = $arrHazardType[$id];
		
		$str .= 	'<td colspan="'. count($arrHazard[$id]) .'" class="header3">'. $hazard_type .'</td>';
		foreach($h as $cat_id => $cat_desc){
			$hazard .= '<td class="header4">'. $cat_desc .'</td>';
		}
	}
		
/*	
	$physical = array_unique($physical);
	$health = array_unique($health);
	$environment = array_unique($environment);

	$hazard = '';
	if(count($physical)>0){
		$str .= 	'<td colspan="'. count($physical) .'" class="header3">'. _LBL_PHY_HAZARD .'</td>';
		foreach($physical as $ph){
			$hazard .= '<td class="header4">'. getClassCategory($ph) .'</td>';
		}
	}
	if(count($health)>0){
		$str .= 	'<td colspan="'. count($health) .'" class="header3">'. _LBL_HEALTH_HAZARD .'</td>';
		foreach($health as $hh){
			$hazard .= '<td class="header4">'. getClassCategory($hh) .'</td>';
		}
	}
	if(count($environment)>0){
		$str .= 	'<td colspan="'. count($environment) .'" class="header3">'. _LBL_ENV_HAZARD .'</td>';
		foreach($environment as $eh){
			$hazard .= '<td class="header4">'. getClassCategory($eh) .'</td>';
		}
	}
*/	
	$str .= '</tr>';
	if(!empty($hazard)){
		$str .= '<tr>'
				. $hazard
				. '</tr>';
	}
	
	$bil = 0;
	$content = '';
	foreach($submission_ids as $sub){
		
		$dataColumns = 's.Submission_Submit_ID, uc.Company_Name, st.State_Name, c.Chemical_Name, sd.Detail_TotalSupply, sd.Detail_TotalUse, sc.Chemical_ID, sd.Detail_ID, sd.Detail_ProductName';
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
					"AND sd.isDeleted" => "= 0",
					// "AND s.Status_ID" => "IN (31,32)",
				);
		$dataOrder = 'Detail_ID ASC';
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere,$dataOrder);
		
		$current_detail_id = '';
		foreach($arrayData as $data){
			
			if($current_detail_id!=$data['Detail_ID']){
				$bil++;
				$current_detail_id = $data['Detail_ID'];
				
				if(!empty($content)){
					$content .= '</tr>';
					$content = str_replace('{chemical_name}', $chemical_name, $content);
				}
				
				$content .= '<tr>'
							. '<td class="center">'. $bil .'</td>'
							. '<td class="">'. $data['Submission_Submit_ID'] .'</td>'							
							. '<td class="">'. $data['Company_Name'] .'</td>'							
							. '<td class="">'. $data['State_Name'] .'</td>'							
							. '<td class="">'. (!empty($data['Detail_ProductName']) ? $data['Detail_ProductName'] : '-') .'</td>'					
							. '<td class="">{chemical_name}</td>'					
							;
			
				$total = floatval($data['Detail_TotalSupply']) + floatval($data['Detail_TotalUse']);
											
				foreach($arrHazard as $id => $h){
					foreach($h as $cat_id => $cat_desc){
						if($id==1) $t = 'ph';
						elseif($id==2) $t = 'hh';
						elseif($id==3) $t = 'eh';
						
						if(checkChemicalHazard($t, $data['Chemical_ID'], $data['Detail_ID'], $cat_id))
							$content .= '<td>'. $total .'</td>';
						else $content .= '<td>-</td>';
					}
				}
				/*
				foreach($physical as $ph){					
					if(checkChemicalHazard('ph', $data['Chemical_ID'], $data['Detail_ID'], $ph))
						$content .= '<td>'. $total .'</td>';
					else $content .= '<td>-</td>';
				}
				
				foreach($health as $hh){
					if(checkChemicalHazard('hh', $data['Chemical_ID'], $data['Detail_ID'], $hh))
						$content .= '<td>'. $total .'</td>';
					else $content .= '<td>-</td>';	
				}
				
				foreach($environment as $eh){					
					if(checkChemicalHazard('eh', $data['Chemical_ID'], $data['Detail_ID'], $eh))
						$content .= '<td>'. $total .'</td>';
					else $content .= '<td>-</td>';
				}
				*/				
				$chemical_name = '';
			}
			
			$chemical_name .= $data['Chemical_Name'] .';<br />';
		}
	}
	
	if($bil==0){
		$str .= '<tr><td colspan="6">'. _LBL_NO_RECORD .'</td></tr>';
	} else {
		if(!empty($content)){
			$content .= '</tr>';
			$content = str_replace('{chemical_name}', $chemical_name, $content);
		}
		$str .= $content;
	}
	
	$str .= '</table>';	
	
	
	$fileName = str_replace(' ', '_', _LBL_REPORT_OVERALL_DETAILED) .".xls";
	header("Content-Type:   application/vnd.ms-excel; charset=utf-8");
	header("Content-Disposition: attachment; filename=$fileName");  //File name extension was wrong
	header("Expires: 0");
	header("Cache-Control: cache must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private",false);
	echo $str;
?>
 
