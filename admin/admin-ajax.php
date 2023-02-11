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
		// hazard checking
		elseif($_POST["fn"]=="codes"){
			$typeID 		= isset($_POST["typeID"]) 	? $_POST["typeID"] 		: "";
			$codeID 		= isset($_POST["codeID"]) 	? $_POST["codeID"] 		: "";
			$hStatementID	= array();

			$str = "";
			$str_onClick = "";
			$requiredField = '<span style="color:red"> * </span>';

			// foot notes
			$footNotePhysicalHazardClassification    = '<br><br><span style=" color:blue; font-weight:normal; ">'._LBL_PHY_HAZARD_CLASS_FOOTNOTE.'</span>';
			$footNoteHealthHazardClassification      = '<br><br><span style=" color:blue; font-weight:normal; ">'._LBL_HEALTH_HAZARD_CLASS_FOOTNOTE.'</span>';
			$footNoteEnvironmentHazardClassification = '<br><br><span style=" color:blue; font-weight:normal; ">'._LBL_ENV_HAZARD_CLASS_FOOTNOTE.'</span>';

			if($codeID!=0){
				$getSttmnt 		= _get_StrFromCondition("tbl_Hazard_Codes","Statement_ID","Code_ID",$codeID);
				$hStatementID 	= explode(",",$getSttmnt);
			}

			// Hazard Statements
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
				"dPict" => $str_pictogram,
				"dState" => $str_statement
			);
		}
		// hazard class selection
		elseif($_POST["fn"]=="hazardclass") {
			$id 	= isset($_POST["id"]) 	? $_POST["id"] 		: "";
			$row 	= isset($_POST["row"]) 	? $_POST["row"] 	: "";
			$type 	= isset($_POST["type"]) ? $_POST["type"]	: "";

			// get from database
			$hazard = _get_StrFromManyConditions("tbl_chemical_classified","Category_ID",array("AND Chemical_ID"=>" = ". quote_smart($id),"AND Active"=>" = 1"));

			$fromPH = $toPH = "";
			$fromHH = $toHH = "";
			$fromEH = $toEH = "";

			if(empty($id)) $hazard = "";

			if(!empty($hazard)){
				$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 1","AND Active"=>"= 1","AND Category_ID"=>"NOT IN ($hazard)"),"",TRUE
				);
				foreach($dOptions as $dOpts){
						$fromPH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
				}

				$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										array("AND Type_ID"=>"= 1","AND Active"=>"= 1","AND Category_ID"=>"IN ($hazard)"),"",TRUE
				);
				foreach($dOptions as $dOpts){
						$toPH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
				}

				$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 2","AND Active"=>"= 1","AND Category_ID"=>"NOT IN ($hazard)"),"",TRUE
				);
				foreach($dOptions as $dOpts){
						$fromHH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
				}

				$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										array("AND Type_ID"=>"= 2","AND Active"=>"= 1","AND Category_ID"=>"IN ($hazard)"),"",TRUE
				);
				foreach($dOptions as $dOpts){
						$toHH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
				}

				$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 3","AND Active"=>"= 1","AND Category_ID"=>"NOT IN ($hazard)"),"",TRUE
				);
					foreach($dOptions as $dOpts){
						$fromEH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}

					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
										"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
										array("AND Type_ID"=>"= 3","AND Active"=>"= 1","AND Category_ID"=>"IN ($hazard)"),"",TRUE
					);
					foreach($dOptions as $dOpts){
						$toEH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}
			}
			else {
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 1","AND Active"=>"= 1"),"",TRUE
								);
					foreach($dOptions as $dOpts){
						$fromPH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}

					//query hazard dari sejarah pengguna
					$getPH = _get_datalama("Category_ID_ph",$id,$_SESSION['user']['Usr_ID']);
					//$getPH = "1,2,3,4,5,6,7,8";

					if (!empty($getPH)){
						$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
											"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
											array("AND Type_ID"=>"= 1","AND Active"=>"= 1","AND Category_ID"=>"IN ($getPH)"),"",TRUE
						);

						foreach($dOptions as $dOpts){
							//ubah di sini
							$toPH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
						}
					}
					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 2","AND Active"=>"= 1"),"",TRUE
								);
					foreach($dOptions as $dOpts){
						$fromHH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}
					//ambil data dari sejarah
					$getHH = _get_datalama("Category_ID_hh",$id,$_SESSION['user']['Usr_ID']);
					if(!empty($getHH)){
						$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
											"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
											array("AND Type_ID"=>"= 2","AND Active"=>"= 1","AND Category_ID"=>"IN ($getHH)"),"",TRUE
									);
						foreach($dOptions as $dOpts){
							$toHH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
						}
					}

					$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
									"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
									array("AND Type_ID"=>"= 3","AND Active"=>"= 1"),"",TRUE
								);
					foreach($dOptions as $dOpts){
						$fromEH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
					}
					//ambil data dari sejarah
					$getEH = _get_datalama("Category_ID_eh",$id,$_SESSION['user']['Usr_ID']);
					if(!empty($getEH)){
						$dOptions 	= _get_arraySelect("tbl_Hazard_Category","Category_ID",
											"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 1),Category_Name)",
											array("AND Type_ID"=>"= 3","AND Active"=>"= 1","AND Category_ID"=>"IN ($getEH)"),"",TRUE
									);
						foreach($dOptions as $dOpts){
							$toEH .= '<option value="'. $dOpts["value"] .'" title="'. $dOpts["title"] .'">'. $dOpts["label"] .'</option>';
						}
					}
					//kemasukan baru , sistem akan mencari rekod lama untuk di masukkan by staff dosh
			}

			if(!empty($hazard)) {
				$dviewx = "1";
			} else{
				if(empty($getEH) AND empty($getHH) AND empty($getPH) ){
					$dviewx = "0";
				} else {
					$dviewx = "2";
				}
			}

			$arr = array(
					"classified" => $dviewx,
					"row" => $row,
					"fromPH" => $fromPH,
					"toPH" => $toPH,
					"fromHH" => $fromHH,
					"toHH" => $toHH,
					"fromEH" => $fromEH,
					"toEH" => $toEH
			);
			echo json_encode($arr);
		}
		
		echo json_encode($arr);
	}
