<?php
	//****************************************************************/
	// filename: chemicalAddEdit.php
	// description: add & edit the chemical details
	//****************************************************************/
	include_once "../includes/sys_config.php";
	/*********/
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	/*********/
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	/*********/
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_header.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	/*********/
	func_header("",
				$sys_config["includes_path"]."css/global.css,". // css	
				$sys_config["includes_path"]."jquery/Uploader/fileuploader.css,". // css
				"",
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."javascript/js_validate.js,". // javascript
				$sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
				$sys_config["includes_path"]."jquery/Uploader/fileuploader.js,". // javascript
				"",
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				"", // str url frame top
				"", // str url frame left
				"", // str url frame content
				false // int top for [enter]
				);
	/*********/
	$userLevelID = $_SESSION["user"]["Level_ID"];
	if($userLevelID>2) exit();
	/*********/
	if(isset($_POST["chemicalID"])) $chemicalID = $_POST["chemicalID"];
	elseif(isset($_GET["chemicalID"])) $chemicalID = $_GET["chemicalID"];
	else $chemicalID = 0;
	/*********/
	if(isset($_POST["page"])) $page = $_POST["page"];
	elseif(isset($_GET["page"])) $page = $_GET["page"];
	else $page = "c";
	/*********/
	if($page=="uc"){ $formBack = "unclassifiedList.php"; $menu = 4; }
	elseif($page=="nc"){ $formBack = "newchemicalList.php"; $menu = 5; }
	elseif($page=="cwc"){ $formBack = "cwcList.php"; $menu = 6; }
	else{ $formBack = "chemicalList.php"; $menu = "1"; }
	/*********/
	if(isset($_POST["hidrow"])) $hidrow = $_POST["hidrow"];
	else $hidrow = 1;
	/*********/
	if(isset($_POST["newhidrow"])) $newhidrow = $_POST["newhidrow"];
	else $newhidrow = 1;
	/*********/
	if($chemicalID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	/*********/
	if(isset($_POST["save"])){
		/*********/
		$chemName 		= isset($_POST["chemName"])		? $_POST["chemName"]		: "";
		$chemIUPAC 		= isset($_POST["chemIUPAC"])	? $_POST["chemIUPAC"]		: "";
		
		$chemCAS 		= isset($_POST["chemCAS"])		? $_POST["chemCAS"]			: "";
		/*
		$chemCAS1 		= isset($_POST["chemCAS1"])?$_POST["chemCAS1"]:"";
		$chemCAS2 		= isset($_POST["chemCAS2"])?$_POST["chemCAS2"]:"";
		$chemCAS3 		= isset($_POST["chemCAS3"])?$_POST["chemCAS3"]:"";
		$chemCAS		= "";
		if($chemCAS1!=""||$chemCAS2!=""||$chemCAS3!="")
			$chemCAS = $chemCAS1."-".$chemCAS2."-".$chemCAS3;
		*/
		$chemMol 		= isset($_POST["chemMol"])		? $_POST["chemMol"]			: "";
		$chemStructure 	= isset($_POST["chemStructure"])? $_POST["chemStructure"]	: "";
		$chemWeight 	= isset($_POST["chemWeight"])	? $_POST["chemWeight"]		: "";
		$chemFormula 	= isset($_POST["chemFormula"])	? $_POST["chemFormula"]		: "";
		$chemMol1 		= isset($_POST["chemMol1"])		? $_POST["chemMol1"]		: "";
		$chemStructure1 = isset($_POST["chemStructure1"])? $_POST["chemStructure1"]	: "";
		/*********/
		$pelID			= isset($_POST["pelID"])		? $_POST["pelID"]			: "";
		$pelMas 		= isset($_POST["pelMas"])		? $_POST["pelMas"]			: "";
		// $pelRecommend 	= isset($_POST["pelRecommend"])?$_POST["pelRecommend"]:"";
		// $pelJustify 	= isset($_POST["pelJustify"])?$_POST["pelJustify"]:"";
		/*********/
		$beiID		 	= isset($_POST["beiID"])		?$_POST["beiID"]			: "";
		$beiRecommend 	= isset($_POST["beiRecommend"])	?$_POST["beiRecommend"]		: "";
		// $beiJustify 	= isset($_POST["beiJustify"])?$_POST["beiJustify"]:"";
		/*********/
		$vamID		 	= isset($_POST["vamID"])		? $_POST["vamID"]			: "";
		$vamDesc	 	= isset($_POST["vamDesc"])		? $_POST["vamDesc"]			: "";
		/*********/
		$propID		 	= isset($_POST["propID"])		? $_POST["propID"]			: "";
		$propDesc	 	= isset($_POST["propDesc"])		? $_POST["propDesc"]		: "";
		$propAppearance	= isset($_POST["propAppearance"])? $_POST["propAppearance"]	: "";
		$propBptc		= isset($_POST["propBptc"])		? $_POST["propBptc"]		: "";
		$propMptc		= isset($_POST["propMptc"])		? $_POST["propMptc"]		: "";
		$propOdour		= isset($_POST["propOdour"])	? $_POST["propOdour"]		: "";
		$propVap		= isset($_POST["propVap"])		? $_POST["propVap"]			: "";
		$propSolubility	= isset($_POST["propSolubility"])? $_POST["propSolubility"]	: "";
		$propUses		= isset($_POST["propUses"])		? $_POST["propUses"]		: "";
		
		$chemSchedule	= isset($_POST["chemSchedule"])	? $_POST["chemSchedule"]	: "";
		$chemHsCode		= isset($_POST["chemHsCode"])	? $_POST["chemHsCode"]		: "";
		$chemImage		= isset($_POST["fpath"])		? $_POST["fpath"]			: "";
		
		if(file_exists($chemImage)){
			
			$ex_file 	= explode("/",$chemImage);
			$last_index = count($ex_file) - 1;
			$filename 	= explode(".", $ex_file[ $last_index ] );
			
			$new_path = "";
			foreach($ex_file as $index => $path){
				if($index<($last_index-1) )
					$new_path .= $path ."/";
			}
			$new_filename = $new_path ."CWC-images/". $chemCAS .".". $filename[ count($filename) - 1 ];
			if( rename($chemImage,$new_filename) )
				$chemImage = $new_filename;
			else
				$chemImage = "";
		}
		/*********/
		$registered		= isset($_POST["registered"])?1:0;
		$cwc			= isset($_POST["cwc"])?1:0;
		/*********/
		$updBy = $_SESSION["user"]["Usr_ID"];
		$msg = "";
		/*********/
		if($chemicalID!=0){
			$sqlUpd	= "UPDATE tbl_Chemical SET"
					. " Chemical_Name = ".quote_smart($chemName).","
					. " Chemical_IUPAC = ".quote_smart($chemIUPAC).","
					. " Chemical_CAS = ".quote_smart($chemCAS).",";
			if($chemWeight!='')	$sqlUpd	.= " Chemical_Weight = ".quote_smart($chemWeight).",";
			$sqlUpd	.= " Chemical_Formula = ".quote_smart($chemFormula).","
					. " Chemical_Mol = ".quote_smart($chemMol).","
					. " Chemical_Structure = ".quote_smart($chemStructure).","
					. " Chemical_Mol1 = ".quote_smart($chemMol1).","
					. " Chemical_Structure1 = ".quote_smart($chemStructure1).","
					. " Chemical_Schedule = ".quote_smart($chemSchedule).","
					/*********/
					. " PEL_Malaysia = ".quote_smart($pelMas).","
					// . " PEL_Recommend = ".quote_smart($pelRecommend).","
					// . " PEL_Justification = ".quote_smart($pelJustify).","
					/*********/
					. " BEI_Recommend = ".quote_smart($beiRecommend).","
					// . " BEI_Justification = ".quote_smart($beiJustify).","
					/*********/
					. " VAM_Desc = ".quote_smart($vamDesc).","
					/*********/
					. " Prop_Desc = ".quote_smart($propDesc).","
					. " Prop_Appearance = ".quote_smart($propAppearance).","
					. " Prop_BptC = ".quote_smart($propBptc).","
					. " Prop_MptC = ".quote_smart($propMptc).","
					. " Prop_Odour = ".quote_smart($propOdour).","
					. " Prop_VapPressure = ".quote_smart($propVap).","
					. " Prop_Solubility = ".quote_smart($propSolubility).","
					. " Prop_Uses = ".quote_smart($propUses).","
					/*********/
					. " isNew = ".(($registered==1)?0:1).","
					. " isCWC = ".(($cwc==1)?1:0).","
					/*********/
					. " HS_Code = ".quote_smart($chemHsCode).",";
			if($chemImage!='')	$sqlUpd	.= " Chemical_Image = ". quote_smart($chemImage).",";
			$sqlUpd	.= " UpdateBy = ".quote_smart($updBy).","
					. " UpdateDate = NOW()"
					. " WHERE Chemical_ID = ".quote_smart($chemicalID)." LIMIT 1";
				//	echo $sqlUpd;die;
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			if($resUpd){ $msg = 'Update Successfully'; }
			else{ $msg = 'Update Unsuccessfully'; }
			/*********/
		}else{
			$sqlIns	= "INSERT INTO tbl_Chemical ("
					. " Chemical_Name,Chemical_IUPAC,Chemical_CAS,";
			if($chemWeight!='')	$sqlIns	.= " Chemical_Weight,";
			$sqlIns	.=  " Chemical_Formula,Chemical_Mol,Chemical_Structure,"
					. " Chemical_Mol1,Chemical_Structure1,"
					. " Chemical_Schedule,"
					/*********/
					. " PEL_Malaysia," // PEL_Recommend,PEL_Justification,"
					/*********/
					. " BEI_Recommend," // BEI_Justification,"
					/*********/
					. " VAM_Desc,isNew,"
					/*********/
					. " Prop_Desc,Prop_Appearance,Prop_BptC,Prop_MptC,"
					. " Prop_Odour,Prop_VapPressure,Prop_Solubility,Prop_Uses,"
					/*********/
					. " isCWC,HS_Code,";
			if($chemImage!='')	$sqlIns	.= " Chemical_Image,";
			$sqlIns	.= " RecordBy,RecordDate,UpdateBy,UpdateDate"
					. " ) VALUES ("
					. " ".quote_smart($chemName).",".quote_smart($chemIUPAC).",".quote_smart($chemCAS).",";
			if($chemWeight!='')	$sqlIns	.= " ".quote_smart($chemWeight).",";
			$sqlIns	.= " ".quote_smart($chemFormula).",".quote_smart($chemMol).",".quote_smart($chemStructure).","
					. " ".quote_smart($chemMol1).",".quote_smart($chemStructure1).","
					. " ".quote_smart($chemSchedule).","
					/*********/
					. " ".quote_smart($pelMas)."," // .quote_smart($pelRecommend).",".quote_smart($pelJustify).","
					/*********/
					. " ".quote_smart($beiRecommend)."," // .quote_smart($beiJustify).","
					/*********/
					. " ".quote_smart($vamDesc).",".(($registered==1)?0:1).","
					//===================================
					. " ".quote_smart($propDesc).", ".quote_smart($propAppearance).", ".quote_smart($propBptc).", ".quote_smart($propMptc).","
					. " ".quote_smart($propOdour).", ".quote_smart($propVap).", ".quote_smart($propSolubility).", ".quote_smart($propUses).","
					/*********/
					. " ". (($cwc==1)?1:0) .", ". quote_smart($chemHsCode).",";
			if($chemImage!='')	$sqlIns	.= " ".quote_smart($chemImage).",";
					/*********/
			$sqlIns	.= " ".quote_smart($updBy).", NOW(),".quote_smart($updBy).", NOW()"
					. " )";
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$chemicalID = $db->sql_nextid();
			if($resIns){ $msg = 'Insert Successfully'; }
			else{ $msg = 'Insert Unsuccessfully'; }
			/*********/			
		}
		func_add_audittrail($updBy,$msg.' ['.$chemicalID.']','Admin-Chemical','tbl_Chemical,'.$chemicalID);
		/*********/
		$chemSynIDs = ''; 
		
		foreach($_POST['chemSyn'] as $index => $data){
			// print_r($_POST['chemSyn']);print_r($_POST['chemSynID']);die;
			$chemSynID = $_POST['chemSynID'][$index];
			if($data!=''){
				if($chemSynID!=''){
					$sqlUpd	= "UPDATE tbl_Chemical_Synonyms SET"
							. " Synonym_Name = ".quote_smart($data).","
							. " UpdateBy = ".quote_smart($updBy).","
							. " UpdateDate = NOW()"
							. " WHERE Synonym_ID = ".quote_smart($chemSynID)." AND Chemical_ID = ".quote_smart($chemicalID)." LIMIT 1"
							;
					$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				}
				else{
					$sqlIns	= "INSERT INTO tbl_Chemical_Synonyms ("
							. " Synonym_Name,Chemical_ID,"
							. " RecordBy,RecordDate,UpdateBy,UpdateDate"
							. " ) VALUES ("
							. " ".quote_smart($data).",".quote_smart($chemicalID).","
							. " ".quote_smart($updBy).",NOW(),".quote_smart($updBy).",NOW()"
							. " )";
					$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
					$chemSynID = $db->sql_nextid();
				}
			}
			else{
				$sqlUpd	= "UPDATE tbl_Chemical_Synonyms SET"
						//. " Synonym_Name = ".quote_smart($chemSyn).","
						. " UpdateBy = ".quote_smart($updBy).","
						. " UpdateDate = NOW(),"
						. " Active = 0"
						. " WHERE Synonym_ID = ".quote_smart($chemSynID)." AND Chemical_ID = ".quote_smart($chemicalID)." LIMIT 1"
						;
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));			
			}
			
			$chemSynIDs .= $chemSynID .',';
		}
		$chemSynIDs = substr_replace( $chemSynIDs, "", -1 );
		func_add_audittrail($updBy,$msg.' ['.$chemSynIDs.']','Inventory-Chemical Synonynms','tbl_Chemical_Synonyms,'.$chemSynIDs);
		/*********/
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			parent.left.location.href="'.$sys_config['frame_path'].'left_inventory.php?menu='.$menu.'";
			location.href="'.$formBack.'";
			</script>
			';
		exit();
	}
	/*********/
	$chemName 		= "";
	$chemIUPAC 		= "";
	$chemCAS 		= "";
	$chemMol 		= "";
	$chemStructure 	= "";
	$chemWeight 	= "";
	$chemFormula 	= "";
	$chemMol1 		= "";
	$chemStructure1 = "";
	/*********/
	$pelMas 		= "";
	// $pelRecommend 	= "";
	// $pelJustify 	= "";
	/*********/
	$beiRecommend 	= "";
	// $beiJustify 	= "";
	/*********/
	$vamDesc	 	= "";
	/*********/
	$propDesc	 	= "";
	$propAppearance	= "";
	$propBptc		= "";
	$propMptc		= "";
	$propOdour		= "";
	$propVap		= "";
	$propSolubility	= "";
	$propUses		= "";
	/*********/
	$registered		= array(0);
	$cwc			= array(0);
	$chemHsCode		= "";
	$chemImage		= "";
	$chemSchedule	= "";
	/*********/
	if($chemicalID!=0){
		$dataColumns = "Chemical_Name, Chemical_IUPAC, Chemical_CAS, Chemical_Mol, Chemical_Structure, Chemical_Weight, Chemical_Formula, Chemical_Mol1, Chemical_Structure1, Chemical_Schedule, PEL_Malaysia, BEI_Recommend, VAM_Desc, Prop_Desc, Prop_Appearance, Prop_BptC, Prop_MptC, Prop_Odour, Prop_VapPressure, Prop_Solubility, Prop_Uses, isNew, isCWC, HS_Code, Chemical_Image";
		$dataTable = "tbl_Chemical c";
		$dataJoin = "";
		$dataWhere = array("AND c.Chemical_ID" => " = ".quote_smart($chemicalID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
		/*********/
		foreach($arrayData as $detail){
			$chemName 		= $detail["Chemical_Name"];
			$chemIUPAC 		= $detail["Chemical_IUPAC"];
			$chemCAS 		= $detail["Chemical_CAS"];
			$chemMol 		= $detail["Chemical_Mol"];
			$chemStructure 	= $detail["Chemical_Structure"];
			$chemWeight 	= $detail["Chemical_Weight"];
			$chemFormula 	= $detail["Chemical_Formula"];
			$chemMol1 		= $detail["Chemical_Mol1"];
			$chemStructure1 = $detail["Chemical_Structure1"];
			/*********/
			$pelMas 		= $detail["PEL_Malaysia"];
			// $pelRecommend 	= $detail["PEL_Recommend"];
			// $pelJustify 	= $detail["PEL_Justification"];
			/*********/
			$beiRecommend 	= $detail["BEI_Recommend"];
			// $beiJustify 	= $detail["BEI_Justification"];
			/*********/
			$vamDesc	 	= $detail["VAM_Desc"];
			/*********/
			$propDesc	 	= $detail["Prop_Desc"];
			$propAppearance	= $detail["Prop_Appearance"];
			$propBptc		= $detail["Prop_BptC"];
			$propMptc		= $detail["Prop_MptC"];
			$propOdour		= $detail["Prop_Odour"];
			$propVap		= $detail["Prop_VapPressure"];
			$propSolubility	= $detail["Prop_Solubility"];
			$propUses		= $detail["Prop_Uses"];
			/*********/
			$registered		= (($detail["isNew"]==1)?array(0):array(1));
			$cwc			= (($detail["isCWC"]==1)?array(1):array(0));
			
			$chemHsCode		= $detail["HS_Code"];
			$chemImage		= $detail["Chemical_Image"];
			$chemSchedule	= $detail["Chemical_Schedule"];
		}
		/*********/
	}
	/*********/
	// print_r($cwc);
?>
	<script language="Javascript">
	jQuery(document).ready(function() {
		$("#addRow").click(function(){
			
			$("#tbl-synonym tbody>tr:last").clone(false).insertAfter("#tbl-synonym tbody>tr:last");
			$("#tbl-synonym tbody>tr:last").find("input").val("");
			
			rowNumbering();
			return false;
		});
		
		rowNumbering();
		function rowNumbering(){
			var i = 0;
			$('#tbl-synonym tr td.cl_itemno').each(function(){
				i++;
				$(this).html( "<?= _LBL_SYNONYM ?> " + i );
			});
		}
		
		$(".delRow").live("click",function(){
			$(this).closest("tr").find("input[type=text]").val("");
		});
		
		$("#img-formula").live("click",function(){
			window.location.href = $(this).attr("src");
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_CHEMICAL ." :: ". $addEdit); ?>
	<form name="myForm" action="" method="post">
	<?= form_input("chemicalID",$chemicalID,array("type"=>"hidden")); ?>
	<?= form_input("page",$page,array("type"=>"hidden")); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_CHEMICAL_DETAILS ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td colspan="3"><?php
				$param1 = "chemName";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemName;
				$param3 = array("type"=>"text","style"=>"width:350px");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_IUPAC ?></td>
			<td colspan="3"><?php
				$param1 = "chemIUPAC";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemIUPAC;
				$param3 = array("type"=>"text","style"=>"width:350px");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td colspan="3"><?php
				$param1 = "chemCAS";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemCAS;
				$param3 = array("type"=>"text","style"=>"width:350px;");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_WEIGHT ?></td>
			<td colspan="3"><?php
				$param1 = "chemWeight";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemWeight;
				$param3 = array("type"=>"text");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_FORMULA ?></td>
			<td colspan="3"><?php
				$param1 = 'chemFormula';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemFormula;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_MOL ?></td>
			<td width="30%"><?php
				$param1 = 'chemMol';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemMol;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
			<td width="20%" class="label"><?= _LBL_CHEM_MOL ?></td>
			<td width="30%"><?php
				$param1 = 'chemMol1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemMol1;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_STRUCTURE ?></td>
			<td><?php
				$param1 = 'chemStructure';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemStructure;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
			<td class="label"><?= _LBL_CHEM_STRUCTURE ?></td>
			<td><?php
				$param1 = 'chemStructure1';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemStructure1;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HS_CODE ?></td>
			<td colspan="3"><?php
				$param1 = 'chemHsCode';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemHsCode;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<?php 
			// list($width, $height) = getimagesize($chemImage);
			// echo '$width='.$width;
			// echo '$height='.$height;
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_IMAGE ?></td>
			<td colspan="3"><label><?= !empty($chemImage) ? '<img src="'. $chemImage .'" title="'. _LBL_FORMULA_IMAGE .'" alt="'. _LBL_FORMULA_IMAGE .'" id="img-formula" width="200" />':'' ?></label><br /><br /><div id="file-uploader1">		
			<noscript><p>Please enable JavaScript to use file uploader.</p><!-- class="cl_hide" or put a simple form for upload here --></noscript>         
			</div><?php
				$param1 = 'fpath';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:'';
				$param3 = array('type'=>'hidden');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SCHEDULE ?></td>
			<td colspan="3"><?php
				$param1 = 'chemSchedule';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemSchedule;
				$param3 = array('type'=>'text');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CWC ?></td>
			<td colspan="3"><?php
				$param1 = 'cwc';
				$param2 = array(array('value'=>'1','label'=>_LBL_CWC));
				$param3 = isset($_POST[$param1])?$_POST[$param1]:$cwc;
				$param4 = '&nbsp;';
				echo form_checkbox($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_REGISTERED ?></td>
			<td colspan="3"><?php
				$param1 = 'registered';
				$param2 = array(array('value'=>'1','label'=>_LBL_REGISTERED));
				$param3 = isset($_POST[$param1])?$_POST[$param1]:$registered;
				$param4 = '&nbsp;';
				echo form_checkbox($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_PEL_DESC ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_MAS ?></td>
			<td colspan="3"><?php
				$param1 = 'pelMas';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$pelMas;
				$param3 = array('rows'=>'2','cols'=>'60');
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_RECOMMENDATION ?></td>
			<td colspan="3"><?php
				$param1 = 'pelRecommend';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$pelRecommend;
				$param3 = array('rows'=>'2','cols'=>'60');
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_JUSTIFICATION ?></td>
			<td colspan="3"><?php
				$param1 = 'pelJustify';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$pelJustify;
				$param3 = array('rows'=>'2','cols'=>'60');
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
		-->
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_BEI ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PARAMETER ?></td>
			<td colspan="3"><?php
				$param1 = 'beiRecommend';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$beiRecommend;
				$param3 = array('rows'=>'2','cols'=>'60');
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_JUSTIFICATION ?></td>
			<td colspan="3"><?php
				$param1 = 'beiJustify';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$beiJustify;
				$param3 = array('rows'=>'2','cols'=>'60');
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
		-->
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_VAM ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_DESCRIPTION ?></td>
			<td colspan="3"><?php
				$param1 = "vamDesc";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$vamDesc;
				$param3 = array("rows"=>"2","cols"=>"60");
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_PROP_USES ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PROP_DESCRIPTION ?></td>
			<td colspan="3"><?php
				$param1 = "propDesc";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$propDesc;
				$param3 = array("rows"=>"2","cols"=>"60");
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PROP_APPEARANCE ?></td>
			<td colspan="3"><?php
				$param1 = "propAppearance";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$propAppearance;
				$param3 = array("type"=>"text","style"=>"width:200px");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_BPTC ?></td>
			<td width="30%"><?php
				$param1 = "propBptc";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$propBptc;
				$param3 = array("type"=>"text","style"=>"width:200px");
				echo form_input($param1,$param2,$param3);
			?></td>
			<td width="20%" class="label"><?= _LBL_MPTC ?></td>
			<td width="30%"><?php
				$param1 = "propMptc";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$propMptc;
				$param3 = array("type"=>"text","style"=>"width:200px");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_ODOUR ?></td>
			<td><?php
				$param1 = "propOdour";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$propOdour;
				$param3 = array("type"=>"text","style"=>"width:200px");
				echo form_input($param1,$param2,$param3);
			?></td>
			<td class="label"><?= _LBL_VAP_PRESSURE ?></td>
			<td><?php
				$param1 = "propVap";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$propVap;
				$param3 = array("type"=>"text","style"=>"width:200px");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SOLUBILITY ?></td>
			<td colspan="3"><?php
				$param1 = "propSolubility";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$propSolubility;
				$param3 = array("type"=>"text","style"=>"width:200px");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_USES ?></td>
			<td colspan="3"><?php
				$param1 = "propUses";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$propUses;
				$param3 = array("rows"=>"2","cols"=>"60");
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />

	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" id="tbl-synonym">
		<thead>
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_SYNONYM_NAME ?> <a href="#" id="addRow"><?= _LBL_ADD ?></a></td>
		</tr>
		</thead>
		<tbody>
		<?php
			$param1 = "Synonym_ID,Synonym_Name";
			$param2 = "tbl_Chemical_Synonyms";
			$param3 = "";
			$param4	= array("AND Chemical_ID" => "= ".quote_smart($chemicalID),"AND Active" => "= 1");
			$arrSynonym = _get_arrayData($param1,$param2,$param3,$param4);		
		
			$i = 1;
			foreach($arrSynonym as $detail){
				
		?>
		<tr class="contents">
			<td width="20%" class="label cl_itemno"></td>
			<td colspan="3"><?php
				$param1 = "chemSyn[]";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$detail["Synonym_Name"];
				$param3 = array("type"=>"text","style"=>"width:60%");
				echo form_input($param1,$param2,$param3);
				echo form_input("chemSynID[]",$detail["Synonym_ID"],array("type"=>"hidden"));
				/*********/
				echo '<img src="'. $sys_config['images_path'] .'icons/delete.gif" title="'. _LBL_EMPTY .'" alt="'. _LBL_EMPTY .'" style="cursor:hand;cursor:pointer;" class="delRow" />';
			?></td>
		</tr>
		<?php
			}
			if(count($arrSynonym)==0){
		?>
		<tr class="contents">
			<td width="20%" class="label cl_itemno"></td>
			<td colspan="3"><?php
				$param1 = "chemSyn[]";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:"";
				$param3 = array("type"=>"text","style"=>"width:60%");
				echo form_input($param1,$param2,$param3);
				echo form_input("chemSynID[]","",array("type"=>"hidden"));
				/*********/
				echo '<img src="'. $sys_config['images_path'] .'icons/delete.gif" title="'. _LBL_EMPTY .'" alt="'. _LBL_EMPTY .'" style="cursor:hand;cursor:pointer;" class="delRow" />';
			?></td>
		</tr>
		<?php
			}
		?>
		</tbody>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				/*********/
				$dOthers = array("type"=>"submit");
				echo form_button("save",_LBL_SAVE,$dOthers);
				/*********/
				echo "&nbsp;";
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('".$formBack."');");
				echo form_button("cancel",_LBL_CANCEL,$dOthers);
				/*********/
			?></td>
		</tr>
	</table>
	<br />
	</form>	<script language="javascript">
	function createUploader(){
		var uploader1 = new qq.FileUploader({
			element: document.getElementById("file-uploader1"),
			multiple: false,
			action: "<?= $sys_config["includes_path"] . "class-uploader.php" ?>",
			params: { up_path: "<?= $sys_config["upload_path"] ."temp/" ?>" },
			sizeLimit: "2097152", // "1048576", // 1MB
			allowedExtensions:["jpg","jpeg","gif","wmf","tif","png"],
			debug: true,
			onComplete: function(id, fileName, result){
				
				if(result.path){
					$("#fpath").val( result.path );
					$(".qq-upload-list").html("");
					$("#img-formula").attr("src",$("#fpath").val());
				}
			}
		});
	}
	
	window.onload = function() {
		createUploader();
	};
	</script>
