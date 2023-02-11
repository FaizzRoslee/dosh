<?php
	//****************************************************************/
	// filename: chemicalView.php
	// description: view details of the chemical
	//****************************************************************/
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
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'formElement.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	func_header("",
				$sys_config['includes_path']."css/global.css,". // css	
				"",
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				
				
				
                // linuxhouse_20220603
				/** start javascript: add-chemical-list-fields-task **/
				$sys_config['includes_path']."javascript/check-click-download-1.js,". // javascript
				/** end javascript: add-chemical-list-fields-task **/
								
				
				"",
				true, // menu
				false, // portlet
				true, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false // int top for [enter]
				);
	//==========================================================
	if(isset($_POST['chemicalID'])) $chemicalID = $_POST['chemicalID'];
	// elseif(isset($_GET['chemicalID'])) $chemicalID = $_GET['chemicalID'];
	else $chemicalID = 0;
	//==========================================================
	if(isset($_POST['searchWord'])) $searchWord = mysqli_real_escape_string($db->db_connect_id, $_POST['searchWord']);
	// elseif(isset($_GET['searchWord'])) $searchWord = mysqli_real_escape_string($db->db_connect_id, $_GET['searchWord']);
	else $searchWord = '';
	//==========================================================
	$chemName 		= '';
	$chemIUPAC 		= '';
	$chemCAS 		= '';
	$chemMol 		= '';
	$chemStructure 	= '';
	$chemWeight 	= '';
	$chemFormula 	= '';
	$chemMol1 		= '';
	$chemStructure1 = '';
	//===================================
	$pelMas 		= '';
	// $pelRecommend 	= '';
	// $pelJustify 	= '';
	//===================================
	$beiRecommend 	= '';
	// $beiJustify 	= '';
	//===================================
	$vamDesc	 	= '';
	//===================================
	$propDesc	 	= '';
	$propAppearance	= '';
	$propBptc		= '';
	$propMptc		= '';
	$propOdour		= '';
	$propVap		= '';
	$propSolubility	= '';
	$propUses		= '';
	
	
	// linuxhouse_20220603
	// start table fields variables: add-chemical-list-fields-task
    $propFlhPt      = "";
	$propPhyHaz     = "";
	$propHlthHaz	= "";
	$propEnvHaz		= "";
	$propPdf	    = "";
	// end table fields variables: add-chemical-list-fields-task	
	
	
	$chemImage		= '';
	$chemSchedule	= '';
	$chemHsCode		= '';
	$cwc			= 0;
	$registered		= 0;
	//==========================================================
	if($chemicalID!=0){
//		$dataColumns = 'Chemical_Name, Chemical_IUPAC, Chemical_CAS, Chemical_Mol, Chemical_Structure, Chemical_Weight, Chemical_Formula, Chemical_Mol1, Chemical_Structure1, Chemical_Schedule, PEL_Malaysia, BEI_Recommend, VAM_Desc, Prop_Desc, Prop_Appearance, Prop_BptC, Prop_MptC, Prop_Odour, Prop_VapPressure, Prop_Solubility, Prop_Uses, isNew, isCWC, HS_Code, Chemical_Image';
		
		
        // linuxhouse_20220603
		// start add data columns: add-chemical-list-fields-task
		$dataColumns = "Chemical_Name, Chemical_IUPAC, Chemical_CAS, Chemical_Mol, Chemical_Structure, Chemical_Weight, Chemical_Formula, Chemical_Mol1, Chemical_Structure1, Chemical_Schedule, PEL_Malaysia, BEI_Recommend, VAM_Desc, Prop_Desc, Prop_Appearance, Prop_BptC, Prop_MptC, Prop_Odour, Prop_VapPressure, Prop_Solubility, Prop_Uses, Prop_FlhPoint, Prop_PhyHazard, Prop_HlthHazard, Prop_EnvHazard, Prop_PDF, isNew, isCWC, HS_Code, Chemical_Image";		
        // end add data columns: add-chemical-list-fields-task		
		
		
		$dataTable = 'tbl_Chemical c';
		$dataJoin = '';
		$dataWhere = array("AND c.Chemical_ID" => " = ".quote_smart($chemicalID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
		//==========================================================
		foreach($arrayData as $detail){
			$chemName 		= $detail['Chemical_Name'];
			$chemIUPAC 		= $detail['Chemical_IUPAC'];
			$chemCAS 		= $detail['Chemical_CAS'];
			$chemMol 		= $detail['Chemical_Mol'];
			$chemStructure 	= $detail['Chemical_Structure'];
			$chemWeight 	= $detail['Chemical_Weight'];
			$chemFormula 	= $detail['Chemical_Formula'];
			$chemMol1 		= $detail['Chemical_Mol1'];
			$chemStructure1 = $detail['Chemical_Structure1'];
			//===================================
			$pelMas 		= $detail['PEL_Malaysia'];
			// $pelRecommend 	= $detail['PEL_Recommend'];
			// $pelJustify 	= $detail['PEL_Justification'];
			//===================================
			$beiRecommend 	= $detail['BEI_Recommend'];
			// $beiJustify 	= $detail['BEI_Justification'];
			//===================================
			$vamDesc	 	= $detail['VAM_Desc'];
			//===================================
			$propDesc	 	= $detail['Prop_Desc'];
			$propAppearance	= $detail['Prop_Appearance'];
			$propBptc		= $detail['Prop_BptC'];
			$propMptc		= $detail['Prop_MptC'];
			$propOdour		= $detail['Prop_Odour'];
			$propVap		= $detail['Prop_VapPressure'];
			$propSolubility	= $detail['Prop_Solubility'];
			$propUses		= $detail['Prop_Uses'];
			
			
			// linuxhouse_20220603
            /** start table fields: add-chemical-list-fields-task **/
            $propFlhPt      = $detail["Prop_FlhPoint"];
            $propPhyHaz     = $detail["Prop_PhyHazard"];
            $propHlthHaz	= $detail["Prop_HlthHazard"];
            $propEnvHaz		= $detail["Prop_EnvHazard"];
            $propPdf	    = $detail["Prop_PDF"];
            /** end table fields: add-chemical-list-fields-task **/			
			
			
			//===================================
			//===================================
			$chemSchedule	= $detail['Chemical_Schedule'];
			$chemHsCode		= $detail['HS_Code'];
			$chemImage		= $detail['Chemical_Image'];
			$cwc			= $detail['isCWC'];
			$registered		= $detail['isNew'];
		}
		//==========================================================
	}
	//==========================================================
?>
	<script language="Javascript">
	$(document).ready(function() {
		$('a[title]').each(function() {
			$(this).qtip({
				content: {
					//url: $(this).attr('rel'), // Use the rel attribute of each element for the url to load
					title: {
					   text: '<?= _LBL_DESCRIPTION_FOR ?>: ' + $(this).text(), // Give the tooltip a title using each elements text
					  // button: 'Close' // Show a close link in the title
					},
				},
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: { max: 350, min: 0 },
				},
			});
		});
		$('label[title]').each(function() {
			$(this).qtip({
				position: { corner: { tooltip: 'topLeft', target: 'bottomLeft' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					//width: { max: 350, min: 0 },
					width: 350,
				},
			});
		});
		$('#imgDisplay a[rel]').each(function() {
			$(this).qtip({
				content: {
					url: $(this).attr('rel'), // Use the rel attribute of each element for the url to load
					title: {
					   text: '<?= _LBL_PICTOGRAM_FOR ?>: ' + $(this).text(), // Give the tooltip a title using each elements text
						//button: 'Close'  // Show a close link in the title
					},
				},
				position: { corner: { tooltip: 'bottomMiddle', target: 'topMiddle' } },
				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					textAlign: 'center',
				},
			});
		});
		
		$("#img-formula").live("click",function(){
			window.location.href = $(this).attr("src");
		}).attr('style','cursor:pointer;cursor:hand;');
		
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<center>
	<?php func_window_open2(_LBL_CHEMICAL .' :: '. _LBL_VIEW,'70%'); ?>
	<form name="myForm" id="myForm" action="complete_search.php" method="post">
	<?= form_input('chemicalID',$chemicalID,array('type'=>'hidden')); ?>
	<?= form_input('searchWord',$searchWord,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_CHEMICAL_DETAILS ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td colspan="3"><label><?= !empty($chemName) ? $chemName : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_IUPAC ?></td>
			<td colspan="3"><label><?= !empty($chemIUPAC) ? $chemIUPAC : $chemName; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td colspan="3"><label><?= !empty($chemCAS) ? $chemCAS : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_WEIGHT ?></td>
			<td colspan="3"><label><?= !empty($chemWeight) ? $chemWeight : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_FORMULA ?></td>
			<td colspan="3"><label><?= !empty($chemFormula) ? $chemFormula : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_MOL ?></td>
			<td width="30%"><label><?= !empty($chemMol) ? $chemMol : '-'; ?></label></td>
			<td width="20%" class="label"><?= _LBL_CHEM_MOL ?></td>
			<td width="30%"><label><?= !empty($chemMol1) ? $chemMol1 : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CHEM_STRUCTURE ?></td>
			<td><label><?= !empty($chemStructure) ? $chemStructure : '-'; ?></label></td>
			<td class="label"><?= _LBL_CHEM_STRUCTURE ?></td>
			<td><label><?= !empty($chemStructure1) ? $chemStructure1 : '-'; ?><label></td>
		</tr>
		<?php if( $_SESSION['user']['Level_ID'] < 5 ){ ?>
		<tr class="contents">
			<td class="label"><?= _LBL_HS_CODE ?></td>
			<td colspan="3"><label><?= !empty($chemHsCode) ? $chemHsCode : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_IMAGE ?></td>
			<td colspan="3"><label><?= !empty($chemImage) ? '<img src="'. $chemImage .'" title="'. _LBL_FORMULA_IMAGE .'" alt="'. _LBL_FORMULA_IMAGE .'" id="img-formula" width="200"  />': '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SCHEDULE ?></td>
			<td colspan="3"><label><?= !empty($chemSchedule) ? $chemSchedule : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CWC ?></td>
			<td colspan="3"><label><?= ($cwc==1) ? _LBL_YES : _LBL_NO; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_REGISTERED ?></td>
			<td colspan="3"><label><?= ($registered!=1) ? _LBL_REGISTERED : '-'; ?></label></td>
		</tr>
		<?php } ?>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_PEL_DESC ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_MAS ?></td>
			<td colspan="3"><label><?= !empty($pelMas) ? nl2br($pelMas) : '-'; ?></label></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_RECOMMENDATION ?></td>
			<td colspan="3"><label><?= !empty($pelRecommend) ? nl2br($pelRecommend) : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_JUSTIFICATION ?></td>
			<td colspan="3"><label><?= !empty($pelJustify) ? nl2br($pelJustify) : '-'; ?></label></td>
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
			<td colspan="3"><label><?= !empty($beiRecommend) ? nl2br($beiRecommend) : '-'; ?></label></td>
		</tr>
		<!--
		<tr class="contents">
			<td class="label"><?= _LBL_JUSTIFICATION ?></td>
			<td colspan="3"><label><?= !empty($beiJustify) ? nl2br($beiJustify) : '-'; ?></label></td>
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
			<td colspan="3"><label><?= !empty($vamDesc) ? nl2br($vamDesc) : '-'; ?></label></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_PROP_USES ?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_PROP_DESCRIPTION ?></td>
			<td colspan="3"><label><?= !empty($propDesc) ? nl2br($propDesc) : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PROP_APPEARANCE ?></td>
			<td colspan="3"><label><?= !empty($propAppearance) ? $propAppearance : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_BPTC ?></td>
			<td width="30%"><label><?= !empty($propBptc) ? $propBptc : '-'; ?></label></td>
			<td width="20%" class="label"><?= _LBL_MPTC ?></td>
			<td width="30%"><label><?= !empty($propMptc) ? $propMptc : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_ODOUR ?></td>
			<td><label><?= !empty($propOdour) ? $propOdour : '-'; ?></label></td>
			<td class="label"><?= _LBL_VAP_PRESSURE ?></td>
			<td><label><?= !empty($propVap) ? $propVap : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SOLUBILITY ?></td>
			<td colspan="3"><label><?= !empty($propSolubility) ? $propSolubility : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_USES ?></td>
			<td colspan="3"><label><?= !empty($propUses) ? nl2br($propUses) : '-'; ?></label></td>
		</tr>
		
		
		<!-- linuxhouse_20220603 -->
		<!-- start html: add-chemical-list-fields-task -->
		<tr class="contents">
			<td class="label"><?= _LBL_FLASH_POINT ?></td>
			<td colspan="3"><label><?= !empty($propFlhPt) ? nl2br($propFlhPt) : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_PHYSICAL_HAZARD ?></td>
			<td colspan="3"><label><?= !empty($propPhyHaz) ? nl2br($propPhyHaz) : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HEALTH_HAZARD ?></td>
			<td colspan="3"><label><?= !empty($propHlthHaz) ? nl2br($propHlthHaz) : '-'; ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_ENVIRONMENTAL_HAZARD ?></td>
			<td colspan="3"><label><?= !empty($propEnvHaz) ? nl2br($propEnvHaz) : '-'; ?></label></td>
		</tr>
        <tr class="contents">
			<td class="label"><?= _LBL_DOWNLOAD_PDF_FILE ?></td>
			<td colspan="3">
                <span class="formInfo">
                    <?= !empty($propPdf) ? '<a href="'. $sys_config['inventory_path'].'download1.php?file='. nl2br($propPdf) .'" id="a-formula2" onclick="return checkClickDownload1()" class="btn btn-info btn-sm active">'. _LBL_DOWNLOAD_FILE .'</a>' : '-' ?>
                </span>
            </td>
		</tr>
        <!-- end html: add-chemical-list-fields-task -->		
		
		
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="4" style="vertical-align:middle;"><?= _LBL_SYNONYM_NAME ?></td>
		</tr>
		<?php 
			$param1 = 'Synonym_ID,Synonym_Name';
			$param2 = 'tbl_Chemical_Synonyms';
			$param3 = '';
			$param4	= array("AND Chemical_ID" => "= ".quote_smart($chemicalID),"AND Active" => "= 1");
			$arrSynonym = _get_arrayData($param1,$param2,$param3,$param4);
			//print_r($arrSynonym);
			$i = 1;
			foreach($arrSynonym as $detail){
				echo form_input('chemSynID'.$i,$detail['Synonym_ID'],array('type'=>'hidden'));
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SYNONYM ." ". $i ?></td>
			<td colspan="3"><label><?= $detail['Synonym_Name']; ?></label></td>
		</tr>
		<?php
				$i++;
			}
			if($i==1){
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_SYNONYM ." ". $i ?></td>
			<td colspan="3"><label><?= '-'; ?></label></td>
		</tr>
		<?php 
			}
		?>
	</table>
	<?php
		$param1 = '*';
		$param2 = 'tbl_Chemical_Classified';
		$param3 = '';
		$param4	= array("AND Chemical_ID" => "= ".quote_smart($chemicalID),"AND Active" => "= 1");
		$arrClassified = _get_arrayData($param1,$param2,$param3,$param4);
		$classified = count($arrClassified);
		if($classified>0){
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="2" style="vertical-align:middle;"><?= _LBL_CLASSIFIED ?></td>
		</tr>
		<?php 
			$i = 1;
			foreach($arrClassified as $detail){
				$statementID 	= $detail['Lbl_Statement_ID'];
				$signalID	 	= $detail['Lbl_Signal_ID'];
				$pictogramID 	= $detail['Lbl_Pictogram_ID'];
				$categoryID 	= $detail['Category_ID'];
				if($categoryID!=''){
					$arrCategoryID = explode(',',$categoryID);
				}
		?>
		<!--
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_STATEMENT_CODE ?></td>
			<td width="80%"><?php
				//==========================================================================================
				if($statementID!=''){
					$lbl_statementID = array();
					if($statementID!='') $lbl_statementID = explode(',',$statementID);
					$param2 = _get_arraySelect(
									'tbl_Hazard_Statement','Statement_ID','Statement_Code',
									array('AND Statement_ID'=>'IN ('.$statementID.')','AND Active'=>'= 1')
								);
					//==========================================================================================
					$param5 = '';
					$form_rc = '';
					$i=0;
					if(is_array($param2)){
						$maxArr = count($param2);
						$form_rc .= '<table width="100%">';
						if($i%8==1){ $form_rc .= '<tr>'; }
						//====================================
						foreach($param2 as $opt_val){
							$i++;
							$form_rc .= '<td width="12.5%"><label><a href="#"';
							$form_rc .= ' title="'._get_StrFromCondition('tbl_Hazard_Statement','Statement_Desc','Statement_ID',$opt_val['value']).'"';
							$form_rc .= ' >'.$opt_val['label'].'</label>';
							$form_rc .= '</td>';
							//====================================
							if($maxArr==$i && $i%8!=0){
								$emptyCell = 8-($i%8); 
								for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
							}
							//====================================
							if($i%8==0){ $form_rc .= '</tr>'; }
						}
						//====================================
						$form_rc .= '</table>';
						//====================================
					}
					echo $form_rc;
				}else{ echo '<label>-</label>'; }
				//==========================================================================================
			?></td>
		</tr>
		-->
		<tr class="contents">
			<td class="label" width="20%"><?= _LBL_HAZARD_CLASSIFICATION ?></td>
			<td width="80%"><label><?php
				if(isset($arrCategoryID)){
					$tyIDs = 0;
					foreach($arrCategoryID as $value){
						//==========================================================================================
						$tyID = _get_StrFromCondition('tbl_Hazard_Category','Type_ID','Category_ID',$value);
						if($tyID!=$tyIDs){
							if($tyID==1){ $clName = _LBL_PHY_HAZARD; }
							elseif($tyID==2){ $clName = _LBL_HEALTH_HAZARD; }
							elseif($tyID==3){ $clName = _LBL_ENV_HAZARD; }
							else{ $clName = '-'; }
							echo '<strong><u>'.$clName.'</u>:-</strong><br />';
						}
						$tyIDs = $tyID;
						//==========================================================================================
						echo _get_StrFromCondition('tbl_Hazard_Category',"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 0,1),Category_Name)",'Category_ID',$value);
						//==========================================================================================
						echo ';<br />';
					}
				}
			?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SIGNAL_WORD ?></td>
			<td><label><?= ($signalID!=0) ? _get_StrFromCondition('tbl_Hazard_Signal','Signal_Name','Signal_ID',$signalID) : '-'; ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HAZARD_PICTOGRAM ?></td>
			<td><div id="imgDisplay"><?php 
				//==========================================================================================
				if($pictogramID!=''){
					$lbl_pictogramID = array();
					if($pictogramID!='') $lbl_pictogramID = explode(',',$pictogramID);
					$param2 = _get_arraySelect(
									'tbl_Hazard_Pictogram','Pictogram_ID','Pictogram_Desc',
									array('AND Pictogram_ID'=>'IN ('.$pictogramID.')','AND Active'=>'= 1')
								);
					//==========================================================================================
					$form_rc = '';
					$i=0;
					if(is_array($param2)){
						$maxArr = count($param2);
						$form_rc .= '<table width="100%">';
						//====================================
						foreach($param2 as $opt_val){
							$i++;
							if($i%4==1){ $form_rc .= '<tr>'; }
							$form_rc .= '<td width="25%" align="center"><label><img';
							$form_rc .= ' src="' ._get_StrFromCondition('tbl_Hazard_Pictogram','Pictogram_Url','Pictogram_ID',$opt_val['value']) .'"';
							$form_rc .= ' width="80%" /><br /><strong>'.$opt_val['label'].'</strong></label>';
							$form_rc .= '</td>';
							if($maxArr==$i && $i%4!=0){
								$emptyCell = 4-($i%4); 
								for($j=0;$j<$emptyCell;$j++){ $form_rc .= '<td>&nbsp;</td>'; }
							}
							if($i%4==0){ $form_rc .= '</tr>'; }
						}
						//====================================
						$form_rc .= '</table>';
						//====================================
					}
					echo $form_rc;
				}else{ echo '<label>-</label>'; }
				//==========================================================================================
			?></div></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_STATEMENT_CODE .' : '. _LBL_HAZARD_STATEMENT ?></td>
			<td width="80%"><?php
				//==========================================================================================
				if($statementID!=''){
					$param2 = _get_arraySelect(
									'tbl_Hazard_Statement','Statement_ID','Statement_Code',
									array('AND Statement_ID'=>'IN ('.$statementID.')','AND Active'=>'= 1')
								);
					//==========================================================================================
					$param5 = '';
					$form_rc = '';
					$i=0;
					if(is_array($param2)){
						//====================================
						$form_rc .= '<table>';
						foreach($param2 as $opt_val){
							$form_rc .= '<tr>';
							$form_rc .= '<td width="10%"><label><strong>'. $opt_val['label'] .' :-</strong></label></td>';
							$form_rc .= '<td width="90%"><label>'.trim(_get_StrFromCondition('tbl_Hazard_Statement','Statement_Desc','Statement_ID',$opt_val['value'])) .';</label></td>';
							$form_rc .= '</tr>';
						}
						$form_rc .= '</table>';
						//====================================
					}
					echo $form_rc;
				}else{ echo '-'; }
				//==========================================================================================
			?></td>
		</tr>
		<?php 
			}
		?>
	</table>
	<?php
		}
	?>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				//====================================================
				$param1 = 'cancel';
				$param2 = _LBL_BACK;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
				//====================================================
			?></td>
		</tr>
	</table>
	<br />
	</form>
	</center>
