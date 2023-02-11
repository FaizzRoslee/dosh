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
	
	$dSubmit = "(SELECT Sub_Record_Date FROM tbl_Submission_Record WHERE Submission_ID = s.Submission_ID AND Sub_Record_Status = 11 ORDER BY Sub_Record_Date DESC LIMIT 1)";
	
	$levelID = $_GET['levelID'];
	$colTypeName = $_GET['type'];
	
	if($levelID<5){
		$aColumns = array( ' ','s.Submission_Submit_ID','uc.Company_Name','sl.Level_Name',$colTypeName,$dSubmit,"ADDDATE(".$dSubmit.", INTERVAL 5 YEAR)",'s.BulkSubmission','ss.Status_Desc','s.CheckedBy',' ');
		$indexDate = 5;
		$i_datesubmit = 5;
		$i_dateexp = 6;
		$i_status = 8;
	}
	else{
		$aColumns = array( ' ','s.Submission_Submit_ID',$colTypeName,$dSubmit,"ADDDATE(".$dSubmit.", INTERVAL 5 YEAR)",'s.BulkSubmission','ss.Status_Desc','s.CheckedBy',' ');
		$indexDate = 3;
		$i_datesubmit = 3;
		$i_dateexp = 4;
		$i_status = 6;
	}
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "s.Submission_ID";
	
	$aColumns2 = $aColumns;
	array_push($aColumns2,'s.Status_ID','ss.Status_Type',$sIndexColumn);
	
	/* DB table to use */
	$sTable = "tbl_Submission s";
	
	/*
	 * Names - this is specific to server-side column re-ordering
	 */
	$aNames = explode( ',', $_GET['sColumns'] );
	
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ) {
		$sLimit = "LIMIT ". mysql_real_escape_string( $_GET['iDisplayStart'] ) .", ". mysql_real_escape_string( $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ) {
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ) {
				$iColumnIndex = array_search( $aNames[intval( $_GET['iSortCol_'.$i] )], $aColumns );
				// if( intval( $_GET['iSortCol_'.$i] ) == $i_dateexp)
					// $sOrder .= $aColumns[ $i_dateexp ]."	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
				// else
					$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."	".mysql_real_escape_string( $_GET['sSortDir_'.$i] ) .", ";
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
	$sJoin .= ' LEFT JOIN sys_User u ON s.Usr_ID = u.Usr_ID ';
	$sJoin .= ' LEFT JOIN sys_User_Client uc ON s.Usr_ID = uc.Usr_ID ';
	$sJoin .= ' LEFT JOIN tbl_Chemical_Type ct ON s.Type_ID = ct.Type_ID ';
	$sJoin .= ' LEFT JOIN tbl_Submission_Status ss ON s.Status_ID = ss.Status_ID ';
	$sJoin .= ' LEFT JOIN sys_Level sl ON u.Level_ID = sl.Level_ID ';
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	 
	
	$Usr_ID = $_GET['Usr_ID'];
	
	// $sWhereCountAll = " WHERE 1 AND isDeleted = 0 AND Ngo_ID < 100 ";
	$sWhereCountAll = " WHERE 1 ";
	if($levelID==5){ $sWhereCountAll .= " AND = 0 "; }
	else{
		if(in_array($levelID,array(6,7,8)))
			$sWhereCountAll .= " AND s.Usr_ID = ". quote_smart($Usr_ID) ." ";
	}
	$sWhere = $sWhereCountAll;
		
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere .= " AND (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if($aColumns[$i]!=' '){
				if($i==$indexDate)
					$sWhere .= "DATE_FORMAT(". $aColumns[$i] .",'%d-%m-%Y') LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%' ";
				elseif($i==$i_dateexp)
					$sWhere .= "DATE_FORMAT(". $aColumns[$i] .",'%d-%m-%Y') LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%' ";
				else
					$sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch'])."%' ";
				
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
			$iColumnIndex = array_search( $aNames[$i], $aColumns );
			// echo '$i='. $i;
			// echo '$aNames[$i]='. $aNames[$i];
			// echo '$iColumnIndex='. $iColumnIndex;
			if($iColumnIndex==$indexDate)
				$sWhere .= "DATE_FORMAT(".$aColumns[$iColumnIndex].",'%d-%m-%Y') LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
			elseif($i==$i_dateexp)
				$sWhere .= "DATE_FORMAT(".$aColumns[$i].",'%d-%m-%Y') LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
			else
				$sWhere .= $aColumns[ $i ]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
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
		$statusID = $aRow[ 'Status_ID' ];
		
		$row = array();
		for ( $i=0 ; $i<$max_column ; $i++ ) {
			$col = explode('.',$aColumns[$i]);
			$countCol = count($col);
		
			$colname = $col[$countCol-1];
			if ( $colname != ' ' ) {
				if($colname=='Level_ID') {
					$lvlName = '-';
					foreach($arrLevel as $level){
						if( $level['value'] == $aRow[ $colname ] ){ $lvlName = $level['label']; break; }
					}
					$row[] = $lvlName;
				} elseif($i == $i_datesubmit) {
					// $datesub = _getSubmitDate($uniqueID);
					$datesub = func_ymd2dmy($aRow[ $dSubmit ]);
					$row[] = $datesub;
				} elseif($i == $i_status) {
					$row[] = ( ($aRow[ $colname ]!='') ? nl2br( trim($aRow[ $colname ]) ).'<br />['. $aRow[ 'Status_Type' ] .']' : '-' );
				} elseif($i == $i_dateexp) {
					$datesub = func_ymd2dmy($aRow[ $aColumns[$i] ]);
					$row[] = $datesub;
				} else {
					$row[] = ( ($aRow[ $colname ]!='') ? nl2br( trim($aRow[ $colname ]) ) : '-' );
				}
			}
			else
			{
				if($i == 0) {
					if($statusID==61||$statusID==62)
						$chbx = '&nbsp;';
					else
						$chbx = '<input type="checkbox" name="checkbox[]" value="'.$uniqueID.'" '. (($levelID==1)?'':'disabled') .' />';
						
					$row[] =  $chbx;
				} elseif($i == ($max_column-1) ) { //last
					$activity = '';
					//====================================================
					$activity = '';
					$imgParam1 = $sys_config['images_path'].'icons/preview.gif';
					$imgParam2 = _LBL_VIEW;
					$imgParam3 = 'redirectForm(\'submissionView.php?submissionID='.$uniqueID.'\')';
					$imgParam4 = array('class'=>'cl_image');
					$activity .= _get_imagebutton2($imgParam1,$imgParam2,$imgParam3,$imgParam4);
					//====================================================
					if(in_array($statusID,array(31,32,33,41,42,43))){
						$url_pdf = $sys_config['report_path'].'submissionApproved_pdf.php?submissionID='.$uniqueID;
						$imgParam1 = _LBL_CERTIFICATION;
						$imgParam2 = 'funcPopup(\''.$url_pdf.'\',1000,600)';
						$imgParam3 = array('class'=>'cl_image');
						$activity .= '&nbsp;'._get_imagedoc($imgParam1,$imgParam2,$imgParam3);
					}
					//====================================================
					$row[] = $activity;
				} elseif($i == $i_datesubmit) {
					// $datesub = _getSubmitDate($uniqueID);
					$datesub = func_ymd2dmy($aRow[ $dSubmit ]);
					$row[] = $datesub;
				} elseif($i == $i_dateexp) {
					$dateexp = _getExpiredDate($uniqueID);
					$row[] = $dateexp;
				} else {
					$row[] = $bil;
				}
			}
		}
		$output['aaData'][] = $row;
	}
	echo json_encode( $output );
?>