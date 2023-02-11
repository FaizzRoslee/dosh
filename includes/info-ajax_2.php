<?php
	if(!isset($_POST['searchWord'])){
		header("HTTP/1.0 404 Not Found");
		exit();
	}

	
	if(isset($_POST['searchWord'])){
		$searchWord = trim($_POST['searchWord']);
		
		global $db;
		include 'sys_config.php';
		include $sys_config['includes_path'].'db_config.php';
		include $sys_config['includes_path'].'func_master.php';
		
		if(isset($_SESSION['lang'])){ $fileLang = $_SESSION['lang'].'.php'; }
		else{ $fileLang = 'eng.php'; }
		include_once $sys_config['languages_path'].$fileLang;
		
		$arrayData = array();
		if(!empty($searchWord)){
			$dataColumns = 'ch.Chemical_CAS,ch.Chemical_Name,ch.Chemical_IUPAC,ch.Chemical_ID';
			$dataTable = 'tbl_Chemical ch';
			$dataJoin = '';
			$dataWhere = array(
						'AND (ch.Chemical_Name'=>"LIKE '%". quote_smart($searchWord,false) ."%'",
						'OR ch.Chemical_IUPAC'=>"LIKE '%". quote_smart($searchWord,false) ."%'",
						'OR ch.Chemical_CAS'=>"LIKE '%". quote_smart($searchWord,false) ."%')",
						'AND ch.isNew'=>"= 0",
						'AND ch.isActive'=>"= 1"
						); 
			$arrayData = _get_arrayData2($dataColumns,$dataTable,$dataJoin,$dataWhere);
		}

		// echo '<pre>';
		// print_r($arrayData['result']);
		// print_r($arrayData['sql']);
		// echo '</pre>';
		
		$str = '';
		$bil = 0;
		
		if(!empty($arrayData['result'])){
		
			foreach($arrayData['result'] as $row){
				$bil++;
				$isClassified = _get_RowExist('tbl_Chemical_Classified',array('AND Chemical_ID'=>'= '.quote_smart($row['Chemical_ID']),'AND Active'=>'= 1'));
				
				$dataColumns2 	= 'Synonym_Name';
				$dataTable2 	= 'tbl_Chemical_Synonyms';
				$dataJoin2 		= '';
				$dataWhere2 	= array('AND Chemical_ID'=>'= '.quote_smart($row['Chemical_ID']));
				$arrayData2 = _get_arrayData($dataColumns2,$dataTable2,$dataJoin2,$dataWhere2);
				$synName = '';
				foreach($arrayData2 as $detail){
					if($detail['Synonym_Name']!=''){
						if($synName!='') $synName .= ', ';
						$synName .= '['. nl2br(func_fn_escape($detail['Synonym_Name'])) .']';
					}
				}
				if($synName=='') $synName = '-';
				
				$str .= '<tr height="25">'
					. 		'<td>'. (!empty($row['Chemical_CAS'])?$row['Chemical_CAS']:'-') .'<span class="infosyn">'. $synName .'</span></td>'
					.		'<td>'. (!empty($row['Chemical_Name'])?nl2br(func_fn_escape($row['Chemical_Name'])):'-') .'</td>'
					.		'<td>'. (!empty($row['Chemical_IUPAC'])?nl2br(func_fn_escape($row['Chemical_IUPAC'])):'-') .'</td>'
					.		'<td>'. (($isClassified>0)?'<img src="'.$sys_config['images_path'].'icons/tick.gif" />':'&nbsp;') .'</td>'
					.		'<td><a href="chemicalView_Search.php?chemicalID='. $row['Chemical_ID'] .'&searchWord='. $searchWord .'" >'. strtoupper(_LBL_VIEW) .'</a></td>'
					.		'<td>'. $synName .'</td>'
					.	'</tr>';
			}
		}
		
		$str_table = '';
		
		$str_table .= '<div class="demo_jui">'
					. 	'<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">'
					.	'<thead>'
					.	'<tr height="30">'
					.		'<th width="12%">'. _LBL_CAS_NO .'</th>'
					.		'<th width="40%">'. _LBL_TRADE_PRODUCT .'</th>'
					.		'<th width="32%">'. _LBL_CHEM_IUPAC .'</th>'
					.		'<th width="8%">'. _LBL_CLASSIFIED .'</th>'
					.		'<th width="8%">'. _LBL_VIEW .'</th>'
					.		'<th>'. _LBL_SYNONYM .'/th>'
					.	'</tr>'
					.	'</thead>'
					.	'<tbody>'. $str .'</tbody>'
					.	'</table>'
					. '</div>';
		// echo $str_table;
		
		$arr = array(
			'result' => $str_table,
			'sql' => isset($arrayData['sql'])?$arrayData['sql']:'',
			'collapsetrue' => ($bil==0) ? true : false
		);
		
		echo json_encode($arr);
	}