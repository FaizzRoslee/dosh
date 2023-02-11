<?php
	/************************************************************************************************************************/
	// filename: func_master.php
	// purpose: common function that can be used in all forms
	/************************************************************************************************************************/
	
	include("sanitize.helper.php");
	//get common==============================================================================================================
	/**************/
	function _get_StrFromCondition($table="", $column="", $colID="",$id=0){
		global $db;
		
		$val = "";
		if($table!="" || $column!="" || $colID!=""){
			$sql = "SELECT ".$column." FROM ".$table." WHERE ".$colID." = ". quote_smart($id);
			// echo $sql.'<br />';
			$res = $db->sql_query($sql, END_TRANSACTION) or die(print_r($db->sql_error()));
			if($db->sql_numrows($res) > 0){
				$row = $db->sql_fetchrow($res);
				$val = $row[$column];
			}
		}
		return $val;
	}
	/**************/
	function _get_StrFromManyConditions($table="",$column="",$where=array()){
		global $db;

		$val = "";
		if($table!="" || $column!=""){
			$sql = "SELECT ".$column." FROM ".$table." WHERE 1";
			if(is_array($where)){
				foreach($where as $key => $detail){
					$sql .= " ".$key ." ".$detail; //$condition[0] [AND/OR column], $condition[1] [!=/=/IS NULL/IS NOT NULL/etc]
				}
			}
			// echo '<br />'.$sql.'<br />';
			$res = $db->sql_query($sql, END_TRANSACTION) or die(print_r($db->sql_error()));
			if($db->sql_numrows($res) > 0){
				$row = $db->sql_fetchrow($res);
				$val = $row[$column];
			}
		}
		return $val;
	}
	/**************/
	function _get_RowExist($table='', $where=array(), $join=array()){
		global $db;

		$rowExist = 0;
		if($table!='' || $where!=''){
			$sql = "SELECT COUNT(*) AS totalRow FROM ".$table."";
			if(is_array($join)){
				foreach($join as $value){
					$sql .= " ".$value;
				}
			}
			$sql	.= " WHERE 1";
			if(is_array($where)){
				foreach($where as $key => $detail){
					$sql .= " ".$key ." ".$detail; //$key [AND/OR column], $detail [!=/=/IS NULL/IS NOT NULL/etc]
				}
			}
			// echo '<br />'.$sql.'<br />';
			$res = $db->sql_query($sql, END_TRANSACTION) or die(print_r($db->sql_error()));
			if($db->sql_numrows($res) > 0){
				$row = $db->sql_fetchrow($res);
				$rowExist = $row['totalRow'];
			}
		}
		return $rowExist;
	}
	/**************/
	//get common==============================================================================================================
	
	//get selection===========================================================================================================
	//========================================================================================================================
	function _get_arrayParent($tblName='',$colID='',$colDesc='',$colWhere=array(),$colSorting=''){
		global $db;
		
		$arr_options = array();
		if($tblName!='' || $colDesc!='' || $colID!=''){
			$sql = "SELECT ".$colDesc." AS `desc`,".$colID." AS `id`";
			$sql .=	" FROM ".$tblName." WHERE 1";
			if(is_array($colWhere)){
				foreach($colWhere as $key => $detail){
					$sql .= " ".$key." ".$detail; //$key [AND/OR column], $detail [!=/=/IS NULL/IS NOT NULL/etc]
				}
			}
			
			$sql .= " ORDER BY ". (($colSorting!='')?$colSorting:"`desc`") ." ASC";
			//echo $sql.'<br />';
			$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));

			while($row = $db->sql_fetchrow($res)){
				$id = $row['id'];
				$label = $row['desc'];
				
				$arr_options[$id] = $label;
			}
		}
		return $arr_options;
	}
	//=========================================================================================================
	function _get_arraySelectParent($tblName='',$colID='',$colDesc='',$colParent='',$colWhere=array(),$colSorting=''){
		global $db;
		
		$arr_options = array();
		if($tblName!='' || $colDesc!='' || $colID!=''){
			$sql = "SELECT ".$colDesc." AS `desc`,".$colID." AS `id`,".$colParent." AS `parent`";
			$sql .=	" FROM ".$tblName." WHERE 1";
			if(is_array($colWhere)){
				foreach($colWhere as $key => $detail){
					$sql .= " ".$key." ".$detail; //$key [AND/OR column], $detail [!=/=/IS NULL/IS NOT NULL/etc]
				}
			}
			$sql .= " ORDER BY ". (($colSorting!='')?$colSorting:"`desc`") ." ASC";
			//echo $sql.'<br />';
			$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));

			while($row = $db->sql_fetchrow($res)){
				$id = $row['id'];
				$label = $row['desc'];
				$parent = $row['parent'];
				
				$arr_options[$id]['label'] = $label;
				$arr_options[$id]['value'] = $id;
				$arr_options[$id]['parent'] = $parent;
			}
		}
		return $arr_options;
	}
	//=========================================================================================================
	function _get_arraySelect($tblName='',$colID='',$colDesc='',$colWhere=array(),$colSorting='',$hCat=FALSE){
		global $db, $sys_config;
		
		$arr_options = array();
		if($tblName!='' || $colDesc!='' || $colID!=''){
			$sql = "SELECT ".$colDesc." AS `desc`,".$colID." AS `id`";
			$sql .=	" FROM ".$tblName." WHERE 1";
			if(is_array($colWhere)){
				foreach($colWhere as $key => $detail){
					$sql .= " ".$key." ".$detail; //$key [AND/OR column], $detail [!=/=/IS NULL/IS NOT NULL/etc]
				}
			}
			
			$sql .= " ORDER BY ". (($colSorting!='')?$colSorting:"`desc`") ." ASC";
			// echo $sql.'<br />';
			$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));

			while($row = $db->sql_fetchrow($res)){
				$id = $row['id'];
				$label = $row['desc'];
				
				$arr_options[$id]['label'] = $label;
				$arr_options[$id]['value'] = $id;
				if($hCat==TRUE){
					$arr_options[$id]['title'] = $sys_config['inventory_path'].'getCategoryDetails.php?categoryID='.$id;
				}
			}
		}
		return $arr_options;
	}
	//=========================================================================================================
	function _get_arrayData($dataColumns='',$dataTable='',$dataJoin=array(),$dataWhere=array(),$dataOrder='',$dataLimit=''){
		global $db;
		
		if($dataColumns!='' || $dataTable!=''){
			$sql = "SELECT ".$dataColumns." FROM ".$dataTable;
			if(is_array($dataJoin)){
				foreach($dataJoin as $key => $detail){
					$sql .= " LEFT JOIN ".trim($key)." ON ".trim($detail);
				}
			}
			$sql .= " WHERE 1";
			//print_r($dataWhere);
			if(is_array($dataWhere)){
				foreach($dataWhere as $key => $detail){
					$sql .= " ".trim($key)." ".trim($detail)."";
				}
			}
			if($dataOrder!='') $sql .= " ORDER BY ".$dataOrder;
			if($dataLimit!='') $sql .= " LIMIT ".$dataLimit;
		}
		// echo '<br />'. $sql .'<br />';
		$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
		
		$dataSet = array();
		while($row = $db->sql_fetchrow($res)){
			$dataSet[] = $row;
		}
		return $dataSet;
	}
	function _get_arrayData2($dataColumns='',$dataTable='',$dataJoin=array(),$dataWhere=array(),$dataOrder='',$dataLimit=''){
		global $db;
		
		if($dataColumns!='' || $dataTable!=''){
			$sql = "SELECT ".$dataColumns." FROM ".$dataTable;
			if(is_array($dataJoin)){
				foreach($dataJoin as $key => $detail){
					$sql .= " LEFT JOIN ".trim($key)." ON ".trim($detail);
				}
			}
			$sql .= " WHERE 1";
			//print_r($dataWhere);
			if(is_array($dataWhere)){
				foreach($dataWhere as $key => $detail){
					$sql .= " ".trim($key)." ".trim($detail)."";
				}
			}
			if($dataOrder!='') $sql .= " ORDER BY ".$dataOrder;
			if($dataLimit!='') $sql .= " LIMIT ".$dataLimit;
		}
		// echo '<br />'. $sql .'<br />';
		$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
		
		$dataSet = array();
		while($row = $db->sql_fetchrow($res)){
			$dataSet[] = $row;
		}
		return array('result'=>$dataSet,'sql'=>$sql);
	}
	//====================================================================================================================
	//get selection=======================================================================================================
	
	//====================================================================================================================
	function _get_imagebutton($imgPath='',$titleAlt='',$func=''){
		$imgBtn = "<img src=\"".$imgPath."\" title=\"".$titleAlt."\" alt=\"".$titleAlt."\""
				. " style=\"cursor:hand;cursor:pointer;\" onClick=\"".$func."\" />";
		
		return $imgBtn;
	}
	//====================================================================================================================
	function _get_imagebutton2($imgPath='',$titleAlt='',$func='',$others=array()){
		$imgBtn = "<img src=\"".$imgPath."\" title=\"".$titleAlt."\" alt=\"".$titleAlt."\""
				. " style=\"cursor:hand;cursor:pointer;\" onClick=\"".$func."\"";
		if(is_array($others)){
			foreach($others as $attribute => $detail)
				$imgBtn .= ' '.$attribute.'="'.$detail.'"';
		}
		$imgBtn .= " />";
		
		return $imgBtn;
	}	
	//====================================================================================================================
	function _get_imagedoc($titleAlt='',$func='',$others=array()){
		global $sys_config;
		$imgBtn = "<img src=\"".$sys_config['images_path']."icons/download.gif\" title=\"".$titleAlt."\" alt=\"".$titleAlt."\""
				. " style=\"cursor:hand;cursor:pointer;\" onClick=\"".$func."\"";
		if(is_array($others)){
			foreach($others as $attribute => $detail)
				$imgBtn .= ' '.$attribute.'="'.$detail.'"';
		}
		$imgBtn .= " />";
		
		return $imgBtn;
	}
	//====================================================================================================================
	function func_add_audittrail($recordBy='',$recordDesc='',$recordMod='',$recordOther='') {
		global $db;
				
		$sql	= "INSERT INTO sys_AuditTrail ("
				. " AuditTrail_Desc, AuditTrail_Mod,"
				. " AuditTrail_Other,"
				. " RecordBy, RecordDate, IP_Address"
				. " ) VALUES ("
				. " ".quote_smart($recordDesc).",".quote_smart($recordMod).","
				. " ".quote_smart($recordOther).","
				. " ".quote_smart($recordBy).", NOW(), ".quote_smart($_SERVER['REMOTE_ADDR']).""
				. ")";
		$res	= $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
	}
	//====================================================================================================================
	function quote_smart($value,$quote=true) {
		global $db;
		
		// Stripslashes
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		
		// Quote if not a number or a numeric string
		if($quote==true){
			if (!is_numeric($value)) {
				$value = "'" . mysqli_real_escape_string($db->db_connect_id, $value) . "'";
			}
		}
		else{
			if (!is_numeric($value)) {
				$value = mysqli_real_escape_string($db->db_connect_id, $value);
			}
		}
		
		return $value;	
	} 
	//====================================================================================================================
	function createRandomString($max=30) {
		$chars = "abcdefghijkmnopqrstuvwxyzABCDEFGHIJKMNOPQRSTUVWXYZ023456789";
		srand((double)microtime()*1000000);

		$i = 0;

		$pass = '' ;
		while ($i <= $max) {
			$num = rand() % 58;
			$tmp = substr($chars, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}
	//====================================================================================================================
	function _avoidComma($val){
		$arr_val = explode(',', $val);
		
		if(is_array($arr_val)){
			$new_val = '';
			foreach($arr_val as $value){ $new_val .= $value; }
		}else{ $new_val = $arr_val; }
		
		return $new_val;
	}
	
	function _createComma($val){
		$arr_val = explode('.', $val);
		
		if(is_array($arr_val)){
			$new_val = '';
			$count = 0;
			$arr_length = strlen($arr_val[0]);
			if($arr_length > 0){
				for($i=$arr_length; $i>0; $i--){
					$new_val = $arr_val[0][$i-1].$new_val;

					$count++;
					if(($count%3)==0 && $count!=$arr_length) $new_val = ','.$new_val;
				}
			}else{ $new_val = 0; }
			
			if(strlen($arr_val[1])==0){ $new_val2 = '00';
			}elseif(strlen($arr_val[1])==1){ $new_val2 = $arr_val[1].'0';
			}else{
				if(strlen($arr_val[1])>2){
					$_3rdNo = substr($arr_val[1],3,1);
					
					$_rounded = 0;
					if($_3rdNo > 5) $_rounded = 1;

					$new_val2 = $arr_val[1]+$_rounded;
				}else{ $new_val2 = $arr_val[1]; }
			}
			$new_val = $new_val.'.'.$new_val2;
		}else{ $new_val = $arr_val; }
		
		return $new_val;
	}
	//====================================================================================================================
	function _get_User($usrID=0){
		global $db;

		$sqlSel	= "SELECT User_Fullname AS `name` FROM sys_User_Staff WHERE Usr_ID = ".quote_smart($usrID)." LIMIT 1";
		$resSel	= $db->sql_query($sqlSel,END_TRANSACTION) or die(print_r($db->sql_error()));
		if($db->sql_numrows($resSel)>0){
			$rowSel	= $db->sql_fetchrow($resSel);
			return $rowSel['name'];
		}

		$sqlSel	= "SELECT Company_Name AS `name` FROM sys_User_Client WHERE Usr_ID = ".quote_smart($usrID)." LIMIT 1";
		$resSel	= $db->sql_query($sqlSel,END_TRANSACTION) or die(print_r($db->sql_error()));
		if($db->sql_numrows($resSel)>0){
			$rowSel	= $db->sql_fetchrow($resSel);
			return $rowSel['name'];
		}
	}
	/**************/
	function _check_HeadForApproval($usrID=0){
		global $db;

		$sqlSel	= "SELECT User_Head FROM sys_User_Staff WHERE Usr_ID = ".quote_smart($usrID)." LIMIT 1";
		$resSel	= $db->sql_query($sqlSel,END_TRANSACTION) or die(print_r($db->sql_error()));
		if($db->sql_numrows($resSel)>0){
			$rowSel	= $db->sql_fetchrow($resSel);
			return $rowSel["User_Head"];
		}else{
			return "";
		}
	}
	/**************/
	function func_fn_escape($str_escape='') {
		if(!empty($str_escape)) {
			$str_escape = str_replace("\n", " ", $str_escape);
			$str_escape = str_replace("\n ", " ", $str_escape);
			$str_escape = str_replace("\r", " ", $str_escape);
			$str_escape = str_replace("\r ", " ", $str_escape);
			$str_escape = str_replace("–", "-", $str_escape);
			$str_escape = str_replace("®", "&reg;", $str_escape);
			$str_escape = str_replace("ß", "&szlig;", $str_escape);
			$str_escape = str_replace("«", "&laquo;", $str_escape);
			$str_escape = str_replace("<", "&lt;", $str_escape);
			$str_escape = str_replace(">", "&gt;", $str_escape);
			return $str_escape;
		}
	}
	/**************/
	function _getSubmitDate($submissionID=0){ 
		$where = array("AND Submission_ID"=>"= ".quote_smart($submissionID),"AND Sub_Record_Status"=>"IN (11,12)","ORDER BY"=>"Sub_Record_ID DESC");
		$submitdate = _get_StrFromManyConditions("tbl_Submission_Record","DATE_FORMAT(Sub_Record_Date,'%d-%m-%Y')",$where);
		
		return $submitdate;
	}
	/**************/
	function _getExpiredDate($submissionID=0,$year=5){
	
		$submitdate = _getSubmitDate($submissionID);

		if($submitdate!=""){
			$arr_date = explode("-",$submitdate);
			/**************/
			/*
			$exp_d = $arr_date[0];
			$exp_m = $arr_date[1];
			$exp_y = $arr_date[2]+$year;
			$exp_date = date("d-m-Y", time(0, 0, 0, $exp_m, $exp_d, $exp_y));
			$exp_date = date("d-m-Y", strtotime($exp_y ."/". $exp_m ."/". $exp_d));
			*/
			/**************/
			$exp_date = date("d-m-Y",strtotime( $arr_date[2] ."-". $arr_date[1] ."-". $arr_date[0] ." +5 year"));
			
			return $exp_date;
		}else{
			return '';
		}

	}
	/**************/
	function isExpiredWithin($submissionID=0,$year=5,$withinmonth=3){ 
		global $db;
		
		$submitdate = _getSubmitDate($submissionID);

		if($submitdate!=""){
			$arr_date = explode("-",$submitdate);
	/**************/
			$curr_Date	= date("Y-m-d");
	/**************/
			$exp_d = $arr_date[0];
			$exp_m = $arr_date[1];
			$exp_y = $arr_date[2]+$year;
			$exp_date = date("Y-m-d", mktime(0, 0, 0, $exp_m, $exp_d, $exp_y));
	/**************/
			$warn_d = $exp_d;
			$warn_m = $arr_date[1]-$withinmonth;
			$warn_y = $exp_y;
			$warn_date = date("Y-m-d", mktime(0, 0, 0, $warn_m, $warn_d, $warn_y));
	/**************/
			if($curr_Date>=$warn_date){
				return TRUE;
			}else{
				return TRUE;
				//return FALSE;
			}
		}else{
			return FALSE;
		}
	}
	/**************/
	function extractArray($info=array()){
		echo "<pre>";
		print_r($info);
		echo "</pre>";
	}
	/**************/
	function addNote($note=''){
		return '<span style="color:blue;font-weight:bold"> '. $note .' </span>';
	}
	/**************/
	
	function getForm($form="",$col="Form_ID"){
		global $db;
		
		$pForm = trim($form);
		$sql = "SELECT ". $col ." FROM tbl_Chemical_Form WHERE Form_Code = ". quote_smart($pForm);
		$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
		$row = $db->sql_fetchrow($res);
		return $row[$col];
	}
	
	function getComposition($composition="",$col="value"){
		global $arrComposition;
		
		$pComp = trim($composition);
		
		foreach($arrComposition as $comp){
			if( $pComp == $comp["code"] )
				return $comp[ $col ];
		}
		
		return number_format($pComp,3,".",",");
	}
	
	function getSource($source="",$col="value"){
		global $arrSource;
		
		$pSrc = trim($source);
		
		foreach($arrSource as $src){
			if( $pSrc == $src["code"] )
				return $src[ $col ];
		}
		
		return "";
	}
	
	function getHazard($hazard='',$type=0){
		global $db;
		
		$ex_hazard = explode(';',$hazard);
		$category = '';
		if(count($ex_hazard)>0){
			foreach($ex_hazard as $data){
				if(empty($data)) continue;
				$data1 = trim($data);
				$sql = "SELECT Category_ID"
					. " FROM tbl_Hazard_Category"
					. " WHERE 1"
					. " AND Type_ID = ". quote_smart($type)
					. " AND Active = 1"
					. " AND isDeleted = 0"
					. " AND TRIM(CONCAT_WS(': ', (SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID), Category_Name)) = ". quote_smart($data1)
					;
				$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
				$row = $db->sql_fetchrow($res);
				$category .= !empty($row['Category_ID']) ? $row['Category_ID'].',' : '';
			}
			if(!empty($category)) $category = substr_replace( $category, "", -1 );
		}
		return $category;
	}
	
	function getChemical($name='',$casno='',$usrid=0){
		global $db;
	
		$name = trim($name);
		$casno = str_replace(" ","", $casno);
		$sql = "SELECT Chemical_ID FROM tbl_Chemical WHERE isActive = 1 AND isNew = 0 AND isDeleted = 0"
			. " AND TRIM(Chemical_Name) = ". quote_smart( $name )
			. " AND TRIM(Chemical_CAS) = ". quote_smart( $casno )
			;
		$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));

		if($db->sql_numrows($res) > 0){
			$row = $db->sql_fetchrow($res);
			$cID = $row['Chemical_ID'];
		}
		else{
			$sql = "INSERT INTO tbl_Chemical ("
				. " Chemical_Name, Chemical_CAS, isNew, RecordBy, RecordDate"
				. " ) VALUES ("
				. quote_smart(trim($name)) .", ". quote_smart($casno) .", 1, ". quote_smart($usrid) .", NOW()" 
				. ")";
			$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
			$cID = $db->sql_nextid();
		}
		return $cID;
	}
	
	function getValidTime($submissionID=0,$status=0){
		global $sys_config;
		
		$year = date("Y");
		$prevYear = $year-1;
		$yr = array($year, $prevYear);
		
		if($submissionID!=0){
			$submityear = _get_StrFromCondition("tbl_Submission_Submit","YEAR(SubmitTime)","Submission_ID",$submissionID);
			if(!empty($submityear)){
				if(in_array($submityear,$yr) && $status==2)
					return true;
				else return false;
			}
		}
		
		$idDate		= _get_StrFromManyConditions("sys_SubmitDate","Date_ID",array("AND Submit_Year"=>"=". quote_smart($year)));
		$startDate	= _get_StrFromCondition("sys_SubmitDate","Date_Start","Date_ID",$idDate);
		$endDate	= _get_StrFromCondition("sys_SubmitDate","Date_Last","Date_ID",$idDate);
		
		// if($sys_config["submit_start"] <= date("Y-m-d") && date("Y-m-d") <= $sys_config["submit_end"]){
		if(date("Y-m-d") >= $startDate && date("Y-m-d") <= $endDate){
			return true;
		}
		else{
			return false;
		}
	}
	
	/** get the label from the given array
	 *
	 *
	 *
	 */
	function _getLabel($val="",$arr=array()){
		$str = "";
		if(!empty($arr)){
			foreach($arr as $data){
				if( $val==$data["value"] ){
					$str = $data["label"]; 
					break;
				}
			}
		}
		return $str;
	}
	
	/** get the label from the given array
	 *
	 *
	 *
	 */
	function _generateSubmissionID($submitID=0){
		global $db;
		
		$sqlCount = "SELECT COUNT(Submit_ID) AS row FROM tbl_Submission_Submit WHERE YEAR(SubmitTime) = ". quote_smart(date("Y")) ." AND Submit_ID <= ". quote_smart($submitID);
		$resCount = $db->sql_query($sqlCount,END_TRANSACTION) or die(print_r($db->sql_error()));
		$rowCount = $db->sql_fetchrow($resCount);
		$count = $rowCount["row"];
		/*****/
		$submitID = "DOSH/". date("Y") ."/". substr("000000000".$count,-7); 
		
		return $submitID;
	}
?>