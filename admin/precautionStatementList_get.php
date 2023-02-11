<?php
	//****************************************************************/
	// filename: precautionStatementList_get.php
	// description: get list of the precaution statement
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
	$sIndexColumn = "ps.Statement_ID";
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	$aColumns = array( ' ','pt.Precaution_Name','ps.Statement_Code','ps.Statement_Desc','ps.Active',' ' );
	$aColumns2 = $aColumns;
	array_push($aColumns2,$sIndexColumn);

	/* DB table to use */
	$sTable = "tbl_Precaution_Statement ps";

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
	$sJoin = ' LEFT JOIN tbl_Precaution_Type pt ON ps.Precaution_ID = pt.Precaution_ID';
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	$sWhereCountAll = " WHERE 1 AND ps.isDeleted = 0 ";
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
	
	while ( $aRow = $db->sql_fetchrow($rResult) ) {
		$row = array();
		$uniqueID = $aRow['Statement_ID'];
		foreach($aColumns as $key => $detail){
			if($detail==' '){
				if($key==0) $row[] = '&nbsp;<input type="checkbox" name="checkbox[]" value="'.$uniqueID.'" />';
				else{
					$activity = '';
					//edit info====================================================
					$imgParam1 = $sys_config['images_path'].'icons/edit.gif';
					$imgParam2 = _LBL_EDIT;
					$imgParam3 = 'redirectForm(\'precautionStatementAddEdit.php?statementID='.$uniqueID.'\')';
					$activity .= _get_imagebutton($imgParam1,$imgParam2,$imgParam3);
					//edit info====================================================
					//$activity .= '&nbsp;';
					//delete detail================================================
					$imgParam1 = $sys_config['images_path'].'icons/delete.gif';
					$imgParam2 = _LBL_DELETE;
					$imgParam3 = '';
					//$activity .= _get_imagebutton("$imgParam1","$imgParam2","$imgParam3");
					//delete detail================================================
					$row[] = $activity;
				}
			}else{
				//gune bwh ni if ltk nama tbl kt dpn ex: lab.NoKerja===================================
				$col = explode('.',$detail);
				$countCol = count($col);
				if($col[$countCol-1]=='Active')
					$row[] = (($aRow[ $col[$countCol-1] ]==1)?_LBL_ACTIVE:_LBL_INACTIVE);
				else
					$row[] = !empty($aRow[ $col[$countCol-1] ])?$aRow[ $col[$countCol-1] ]:'-';
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