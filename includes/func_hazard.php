<?php
	/************************************************************************************************************************/
	// filename: func_hazard.php
	// purpose: common function that can be used in all forms
	/************************************************************************************************************************/

	/*********/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*********/
	function _get_Hazard_Statement($str=""){ //$str= "H200, H201, H202"
		$new_str = "";
		$ex_str = explode(",",$str);
		//print_r($ex_str);
		if(is_array($ex_str)){
			foreach($ex_str as $value){
				$value = trim($value);
				if($value!=""){
					$new_str .= "<strong>".$value."</strong>: "._get_StrFromCondition("tbl_Precaution_Statement","Statement_Desc","Statement_Code",$value)."<br />";
				}
			}
		}
		//echo $new_str;
		return $new_str;
	}
	/*********/
?>