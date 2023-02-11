<?php	
	//****************************************************************/
	// filename: chemicalList.php
	// description: list of the chemical
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
	include_once $sys_config['includes_path'].'func_date.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	/*
	 * Script:    DataTables server-side script for PHP and MySQL
	 * Copyright: 2010 - Allan Jardine
	 * License:   GPL v2 or BSD (3-point)
	 */
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	
	$lang = $_SESSION['lang'];
	
	$aColumns = array( ' ','Faq_Q_'.$lang,'Faq_A_'.$lang,'IF(isActive=1, "'. _LBL_ACTIVE .'","'. _LBL_INACTIVE .'")',' ');
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "Faq_ID";
	
	$aColumns2 = $aColumns;
	array_push($aColumns2,$sIndexColumn);
	
	/* DB table to use */
	$sTable = "tbl_Faq";
	
	/*
	 * Names - this is specific to server-side column re-ordering
	 */
	$aNames = explode( ',', $_GET['sColumns'] );
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
		$sLimit = "LIMIT ". mysqli_real_escape_string($db->db_connect_id,  $_GET['iDisplayStart'] ) .", ". mysqli_real_escape_string($db->db_connect_id,  $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
				// $iColumnIndex = array_search( $aNames[intval( $_GET['iSortCol_'.$i] )], $aColumns );
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."	".mysqli_real_escape_string($db->db_connect_id,  $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" ) {
			$sOrder = "";
		}
	}
	
	/*
	* Joining
	*/
	$sJoin = " ";
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	 
	
	$levelID 	= $_GET['levelID'];
	$Usr_ID 	= $_GET['Usr_ID'];
	
	$sWhereCountAll = " WHERE 1 AND isDeleted = 0 AND isActive = 1 ";
	// if($levelID==5){ $sWhereCountAll .= " AND = 0 "; }
	$sWhere = $sWhereCountAll;
		
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere .= " AND (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if($aColumns[$i]!=' '){
				$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($db->db_connect_id, $_GET['sSearch'])."%' ";
				
				$sWhere .= " OR ";
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
			// $iColumnIndex = array_search( $aNames[$i], $aColumns );
			$sWhere .= $aColumns[ $i ]." LIKE '%".mysqli_real_escape_string($db->db_connect_id, $_GET['sSearch_'.$i])."%' ";
		}
	}
	
	$newCols = '';
	foreach($aColumns2 as $detail) {
		if($detail!=' ') {
			if($newCols!='') $newCols .= ',';
			$newCols .= $detail;
		}	
	}
	
	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS ". $newCols ."
		FROM   $sTable
		$sJoin
		$sWhere
		$sOrder
		$sLimit
	";
	// echo $sQuery;
	$rResult = $db->sql_query( $sQuery,END_TRANSACTION ) or die($db->sql_error());
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS() AS foundRow
	";
	// echo $sQuery;
	$rResultFilterTotal = $db->sql_query( $sQuery,END_TRANSACTION ) or die($db->sql_error());
	$aResultFilterTotal = $db->sql_fetchrow($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal['foundRow'];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.") AS countAll
		FROM   $sTable
		$sJoin
		$sWhereCountAll
	";
	// echo $sQuery;
	$rResultTotal = $db->sql_query( $sQuery,END_TRANSACTION ) or die($db->sql_error());
	$aResultTotal = $db->sql_fetchrow($rResultTotal);
	$iTotal = $aResultTotal['countAll'];
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
	);
	
	$bil = 0;
	while ( $aRow = $db->sql_fetchrow( $rResult ) ) {
	
		$iCol = explode('.',$sIndexColumn);
		$countiCol = count($iCol);
	
		$bil++;
		$max_column = count($aColumns);
		$uniqueID = $aRow[ $iCol[$countiCol-1] ];
		
		$row = array();
		for ( $i=0 ; $i<$max_column ; $i++ ) {
			$col = explode('.',$aColumns[$i]);
			$countCol = count($col);
		
			$colname = $col[$countCol-1];
			if ( $colname != ' ' ) {
				$row[] = ( ($aRow[ $colname ]!='') ? nl2br( trim($aRow[ $colname ]) ) : '-' );
			}
			else
			{
				if($i == 0) {
					$chbx = '<input type="checkbox" name="checkbox[]" class="chk-list" value="'.$uniqueID.'" '. (($levelID==1)?'':'disabled') .' />';
						
					$row[] =  $chbx;
				} elseif($i == ($max_column-1) ) { //last
					$activity = '';
					//====================================================
					$imgParam1 = $sys_config['images_path'].'icons/preview.gif';
					$imgParam2 = _LBL_VIEW;
					$imgParam3 = 'redirectForm(\'faq-viewedit.php?id='.$uniqueID.'&ftype=view\')';
					$activity .= _get_imagebutton2($imgParam1,$imgParam2,$imgParam3);
					//====================================================
					if($levelID<5){
					$imgParam1 = $sys_config['images_path'].'icons/edit.gif';
					$imgParam2 = _LBL_EDIT;
					$imgParam3 = 'redirectForm(\'faq-viewedit.php?id='.$uniqueID.'&ftype=edit\')';
					$activity .= '&nbsp;'._get_imagebutton($imgParam1,$imgParam2,$imgParam3);
					}
					//====================================================
					$row[] = $activity;
				} else {
					$row[] = $bil;
				}
			}
		}
		$output['aaData'][] = $row;
	}
	// print_r($output);
	echo json_encode( $output );
?>