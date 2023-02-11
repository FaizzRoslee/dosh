<?php	
	//****************************************************************/
	// filename: chemicalList.php
	// description: list of the chemical
	//***************************************************************/
	include_once "../includes/sys_config.php";
	//==========================================================
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	//==========================================================
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	//==========================================================
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_notification.php";
	include_once $sys_config["includes_path"]."func_date.php";
	include_once $sys_config["includes_path"]."func_email.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."func_hazard.php";

	if(isset($_POST["fn"])){
		$arr = array();
		if($_POST["fn"]=="category"){
			$typeID 		= isset($_POST["typeID"]) 		? $_POST["typeID"] 		: "";
			$categoryID 	= isset($_POST["categoryID"]) 	? $_POST["categoryID"] 	: "";
			$classID 		= "";
			$pictogramID 	= array();
			$hStatementID	= array();

			if($categoryID!=0){
				$classID 		= _get_StrFromCondition("tbl_Hazard_Category","Class_ID","Category_ID",$categoryID);
				$getPict 		= _get_StrFromCondition("tbl_Hazard_Category","Pictogram_ID","Category_ID",$categoryID);
				$pictogramID 	= explode(",",$getPict);
				$getSttmnt 		= _get_StrFromCondition("tbl_Hazard_Category","Statement_ID","Category_ID",$categoryID);
				$hStatementID 	= explode(",",$getSttmnt);
			}
			
			$dOptions = _get_arraySelect("tbl_Hazard_Classification","Class_ID","Class_Name",array("AND Type_ID"=>"= ". quote_smart($typeID),"AND Active"=>"= 1"));
			$str_class = form_select("classID",$classID,$dOptions,$classID,"");
			
			
			$dOptions 	= _get_arraySelect("tbl_Hazard_Pictogram","Pictogram_ID","Pictogram_Desc",array("AND Type_ID"=>"= ". quote_smart($typeID),"AND Active"=>"= 1"));
			$form_rc 	= "";
			$i=0;
			if(is_array($dOptions)){
				$maxArr = count($dOptions);
				$form_rc .= '<table width="100%">';
				foreach($dOptions as $opt_val){
					$i++;
					if($i%4==1){ $form_rc .= '<tr>'; }
					$form_rc .= '<td width="25%"><label><input type="checkbox" name="pictogramID[]" id="pictogramID" ';
					$form_rc .= in_array($opt_val['value'],$pictogramID)?'checked':'';
					$form_rc .= ' value="'.$opt_val['value'].'" /><a href="#"';
					$form_rc .= ' rel="'.$sys_config['includes_path'].'getImage.php?id='.$opt_val['value'].'">'.$opt_val['label'].'</a></label>';
					$form_rc .= '</td>';
					if($maxArr==$i && $i%4!=0){
						$emptyCell = 4-($i%4); 
						for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
					}
					if($i%4==0){ $form_rc .= '</tr>'; }
				}
				$form_rc .= '</table>';
			}
			if($i!=0) 
				$str_pictogram = '<div id="div_img">'.$form_rc.'</div>';
			else $str_pictogram = '<span id="span_info1" class="formInfo"><a href="#" rel="help.php?help=pic" title="'. _LBL_HELP_APPEAR_IF_TITLE .'" >?</a></span>';

			
			$dOptions 	= _get_arraySelect("tbl_Hazard_Statement","Statement_ID","Statement_Code",array("AND Type_ID"=>"= ". quote_smart($typeID),"AND Active"=>"= 1"));
			$form_rc 	= "";
			$i=0;
			if(is_array($dOptions)){
				$maxArr = count($dOptions);
				$form_rc .= '<table width="100%">';
				foreach($dOptions as $opt_val){
					$i++;
					if($i%8==1){ $form_rc .= '<tr>'; }
					$form_rc .= '<td width="12.5%"><label><input type="checkbox" name="hStatementID[]" id="hStatementID" ';
					$form_rc .= in_array($opt_val['value'],$hStatementID)?'checked':'';
					$form_rc .= ' value="'.$opt_val['value'].'" /><a';
					$form_rc .= ' href="#" rel="help.php?help=showSttmnt&sID='.$opt_val['value'].'"';
					$form_rc .= ' >'.$opt_val['label'].'</a></label>';
					$form_rc .= '</td>';
					if($maxArr==$i && $i%8!=0){
						$emptyCell = 8-($i%8); 
						for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
					}
					if($i%8==0){ $form_rc .= '</tr>'; }
				}
				$form_rc .= '</table>';
			}	
			if($i!=0)
				$str_statement = '<div id="div_hs">'.$form_rc.'</div>';
			else $str_statement = '<span id="span_info2" class="formInfo"><a href="#" rel="help.php?help=hSttmnt" title="'. _LBL_HELP_APPEAR_IF_TITLE .'" >?</a></span>';
			
		
			$arr = array(
					"dClass" => $str_class,
					"dPict" => $str_pictogram,
					"dState" => $str_statement
				);
		}
		elseif($_POST["fn"]=="autocomplete"){
			$ids = isset($_POST["ids"]) ? $_POST["ids"] : "";
			
			$sttmnt = _get_Hazard_Statement($ids);
			
			$arr = array(
					"sttmnt" => $sttmnt
				);
		}
		elseif($_POST["fn"]=="submitdate"){
			$year = isset($_POST["year"]) ? $_POST["year"] : "";
			
			$start = _get_StrFromCondition("sys_submitdate","Date_Start","Submit_Year", $year);
			$end = _get_StrFromCondition("sys_submitdate","Date_Last","Submit_Year", $year);
			
			$arr = array(
					"start" => func_ymd2dmy($start),
					"end" => func_ymd2dmy($end),
				);
		}
		
		echo json_encode($arr);
	}