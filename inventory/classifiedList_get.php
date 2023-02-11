<?php
	//****************************************************************/
	// filename: classifiedList_get.php
	// description: get list of the classified chemical
	//***************************************************************/
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
	//==========================================================
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Readme...
	 * 
	 * This script is basically the same as the server_processing.php script, but with a couple of
	 * modifications to show how column reordering can be handled by DataTables. This is in two
	 * parts:
	 * 
	 *   1. Data from server to client - notice that the $aColumns array below is in a different
	 * order to that from the array in server_processing.php, but DataTables will render the
	 * table in the same order. This reordering is done on the client-side by using the sNames 
	 * parameter which is passed back, stating the order which the server is sending data back in.
	 * DataTables will compaire this to the sName parameter for each column and reorder as needed.
	 * 
	 *   2. Data from client to server - in order that the columns can be sent to the server in
	 * any order (it is expected in this script that the server-side will always work with a static
	 * array which is not reordered dynamically) DataTables will send the parameter 'sNames' which
	 * like it's return counterpart is a comma seperated list of the column names, telling the
	 * server-side what order the columns are being shown on the client-side. As such it is now
	 * possible to realise what column sSearch_{i} (for example) refers to. This is done by a look
	 * up of the aColumns array from the names sent in. Note that I've used the same names for the
	 * db table columns and the html table columns to make things a little clearer, but this is not
	 * required.
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "c.Chemical_ID";
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	//$aColumns = array( ' ','c.Chemical_CAS','c.Chemical_Name','cc.Category_ID',' ' );
	$aColumns = array( ' ','c.Chemical_CAS','c.Chemical_Name','cc.Lbl_Statement_ID',' ' );
	$aColumns2 = $aColumns;
	array_push($aColumns2,$sIndexColumn);//add unique id (primary key) utk gune mase query data tgk line 141-150 only if unique id is not included in the above variables

	/* DB table to use */
	$sTable = "tbl_Chemical c";

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */

	 /*
	 * Names - this is specific to server-side column re-ordering
	 */
	$aNames = explode( ',', $_GET['sColumns'] );
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($db->db_connect_id,  $_GET['iDisplayStart'] ).", ".
			mysqli_real_escape_string($db->db_connect_id,  $_GET['iDisplayLength'] );
	}
	
	/*
	 * Ordering
	 */
	 $sOrder = "";
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$iColumnIndex = array_search( $aNames[intval( $_GET['iSortCol_'.$i] )], $aColumns );
				$sOrder .= $aColumns[ $iColumnIndex ]."
				 	".mysqli_real_escape_string($db->db_connect_id,  $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			$sOrder = "";
		}
	}
	
	/*
	* Joining
	*/
	$sJoin = ' RIGHT JOIN tbl_Chemical_Classified cc ON c.Chemical_ID = cc.Chemical_ID';
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhereCountAll = " WHERE 1 AND c.isActive = 1 AND c.isDeleted = 0 AND cc.Active = 1 AND c.isNew = 0 ";
	$sWhere = $sWhereCountAll;
//if(isset($_GET['sSearch'])){
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere .= " AND (";
		foreach($aColumns as $cols){
			if($cols!=' '){
				$sWhere .= $cols." LIKE '%".mysqli_real_escape_string($db->db_connect_id,  $_GET['sSearch'] )."%' OR ";
			}
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$iColumnIndex = array_search( $aNames[$i], $aColumns );
			$sWhere .= $aColumns[$iColumnIndex]." LIKE '%".mysqli_real_escape_string($db->db_connect_id, $_GET['sSearch_'.$i])."%' ";
		}
	}
//}

	$invComma = 0;
	$newCols = '';
	foreach($aColumns2 as $detail){
		if($detail!=' '){
			if($invComma==0) $invComma = 1;
			else $newCols .= ',';
			
			$newCols .= $detail;
		}	
	}

	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ".$newCols."
		FROM   $sTable
		$sJoin
		$sWhere
		$sOrder
		$sLimit
	";
	//echo $sQuery.'<br />';
	$rResult = $db->sql_query($sQuery,END_TRANSACTION) or die(print_r($db->sql_error()));
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS() AS foundRow
	";
	//echo $sQuery.'<br />';
	$rResultFilterTotal = $db->sql_query($sQuery,END_TRANSACTION) or die(print_r($db->sql_error()));
	$aResultFilterTotal = $db->sql_fetchrow($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal['foundRow'];

	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.") AS countAll
		FROM   $sTable
		$sJoin
		$sWhereCountAll
	";
	//echo $sQuery.'<br />';
	$rResultTotal = $db->sql_query($sQuery,END_TRANSACTION) or die(print_r($db->sql_error()));
	$aResultTotal = $db->sql_fetchrow($rResultTotal);
	$iTotal = $aResultTotal['countAll'];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		//"sColumns" => implode( ',', $aColumns ),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	$userLevelID = $_GET['userLevelID'];	
	$disabled = ($userLevelID>2) ? 'disabled' : '' ;
	while ( $aRow = $db->sql_fetchrow($rResult) ) {
		$row = array();
		$countArr = count($aColumns);
		$uniqueID = $aRow['Chemical_ID'];
		foreach($aColumns as $key => $detail){
			if($detail==' '){
				if($key==0) $row[] = '&nbsp;<input type="checkbox" name="checkbox[]" disabled value="'.$uniqueID.'" />';
				elseif($key==($countArr-1)){
					$activity = '';
					//====================================================
					$imgParam1 = $sys_config['images_path'].'icons/preview.gif';
					$imgParam2 = _LBL_VIEW;
					$imgParam3 = 'redirectForm(\'classifiedView.php?chemicalID='.$uniqueID.'\')';
					$activity .= _get_imagebutton($imgParam1,$imgParam2,$imgParam3);
					//====================================================
					if($userLevelID<=2){
						$imgParam1 = $sys_config['images_path'].'icons/edit.gif';
						$imgParam2 = _LBL_EDIT;
						$imgParam3 = 'redirectForm(\'classifiedAddEdit.php?chemicalID='.$uniqueID.'\')';
						$activity .= '&nbsp;'._get_imagebutton($imgParam1,$imgParam2,$imgParam3);
					}
					//====================================================
					$imgParam1 = $sys_config['images_path'].'icons/delete.gif';
					$imgParam2 = _LBL_DELETE;
					$imgParam3 = '';	
					//$activity .= _get_imagebutton("$imgParam1","$imgParam2","$imgParam3");
					//====================================================
					$row[] = $activity;
				}
				else $row[] = '';
			}else{
				//gune bwh ni if ltk nama tbl kt dpn ex: lab.NoKerja===================================
				$col = explode('.',$detail);
				$countCol = count($col);
				if($col[$countCol-1]=='Active'){
					$row[] = (($aRow[ $col[$countCol-1] ]==1)?_LBL_ACTIVE:_LBL_INACTIVE);
				}elseif($col[$countCol-1]=='Category_ID'){
					$det = $aRow[ $col[$countCol-1] ];
					$category = '';
					if($det!=''){
						$ex_det = explode(',',$det);
						foreach($ex_det as $value){
							$classID = _get_StrFromCondition('tbl_Hazard_Category','Class_ID','Category_ID',$value);
							$className = _get_StrFromCondition('tbl_Hazard_Classification','Class_Name','Class_ID',$classID);
							$catName = _get_StrFromCondition('tbl_Hazard_Category','Category_Name','Category_ID',$value);
							
							if($category!='') $category .= '; ';
							$category .= '<b>'.$className.'</b>:'.$catName;
						}
					}
					$row[] = $category;
				}elseif($col[$countCol-1]=='Lbl_Statement_ID'){
					$det = $aRow[ $col[$countCol-1] ];
					$statement = '';
					if($det!=''){
						$ex_det = explode(',',$det);
						foreach($ex_det as $value){
							$statementCode = _get_StrFromCondition('tbl_Hazard_Statement','Statement_Code','Statement_ID',$value);
							$statementDesc = _get_StrFromCondition('tbl_Hazard_Statement','Statement_Desc','Statement_ID',$value);

							if($statement!='') $statement .= '; ';
							$statement .= '<a href="#" title="'.$statementDesc.'">'.$statementCode.'</a>';
						}
					}
					$row[] = $statement;
				}else{
					$row[] = !empty($aRow[ $col[$countCol-1] ])?nl2br(func_fn_escape($aRow[ $col[$countCol-1] ])):'-';
				}
				//gune ats ni if ltk nama tbl kt dpn ex: lab.NoKerja===================================
			}
		}
		$output['aaData'][] = $row;
	}
	// echo '<pre>';
	// print_r($output);
	// echo '</pre>';
	// die;
	echo json_encode( $output );
?>