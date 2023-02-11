<?php
	//****************************************************************/
	// filename: hazCheckView.php
	// description: show detail of the hazard checking selected
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
	include_once $sys_config["includes_path"]."func_hazard.php";
	/*********/
	func_header("",
				"", // css"",
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
				"", // javascript
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
	$chemName 		= "";
	$chemIUPAC 		= "";
	$chemCAS 		= "";
	$classifiedID 	= "";
	$categoryID 	= "";
	$signalID	 	= "";
	/*********/
	/*********/
	if($chemicalID!=0){
		$dataColumns = "*";
		$dataTable 	= "tbl_Chemical c";
		$dataJoin 	= array("tbl_Hazard_Codes hc"=>"c.Chemical_ID = hc.Chemical_ID");
		$dataWhere 	= array("AND c.Chemical_ID" => " = ".quote_smart($chemicalID));
		$arrayData 	= _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
		/*********/
		foreach($arrayData as $detail){
			$chemName 		= $detail["Chemical_Name"];
			$chemIUPAC 		= $detail["Chemical_IUPAC"];
			$chemCAS 		= $detail["Chemical_CAS"];
			$classifiedID 	= $detail["Classified_ID"];
			$categoryID 	= $detail["Category_ID"];
			$signalID	 	= $detail["Signal_ID"];
			$pictogramID 	= $detail["Pictogram_ID"];
		}
	}
	/*********/
	$str_onClick = "getChemID();";
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
		});
	</script>

	<?php func_window_open2(_LBL_HAZARD_CHECKING .' :: '. _LBL_VIEW); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('codeID',$codeID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td colspan="3" class="nama"><?= $chemName; ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td colspan="3" class="nama"><?= $chemCAS; ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SIGNAL_WORD ?></td>
			<td><label><?= ($signalID!=0) ? _get_StrFromCondition('tbl_Hazard_Signal','Signal_Name','Signal_ID',$signalID) : '-'; ?></td>
		</tr>
	</table>
	<br />

	<!-- get the hazard category -->
	<?php
		$countRec = 0;
		if($categoryID!=''){
			$arrCategoryID = explode(',',$categoryID);
			$countRec = count($arrCategoryID);
		}
		$rowspan = $countRec;
	?>

	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td class="label" width="20%"><?= _LBL_HAZARD_TYPE ?></td>
			<td width="80%"><label><?php
				if(isset($arrCategoryID)){
					$tyIDs = 0;
					foreach($arrCategoryID as $value){
						/*********/
						$tyID = _get_StrFromCondition('tbl_Hazard_Category','Type_ID','Category_ID',$value);
						if($tyID!=$tyIDs){
							if($tyID==1){ $clName = _LBL_PHY_HAZARD; }
							elseif($tyID==2){ $clName = _LBL_HEALTH_HAZARD; }
							elseif($tyID==3){ $clName = _LBL_ENV_HAZARD; }
							else{ $clName = '-'; }
							echo '<strong><u>'.$clName.'</u>:-</strong><br />';
						}
						$tyIDs = $tyID;
						/*********/
						echo _get_StrFromCondition('tbl_Hazard_Category',"CONCAT_WS(': ',(SELECT Class_Name FROM tbl_Hazard_Classification WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 0,1),Category_Name)",'Category_ID',$value);
						/*********/
						echo ';<br />';
						$arrClass[] = $value;
					}
				}
			?></label></td>
		</tr>
		<?php
			$countArr = count($arrClass);
		?>
	</table>
	<br />

	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('hazCheckAddEdit.php?codeID=$codeID');");
				echo form_button("edit",_LBL_EDIT,$dOthers);
				/*********/
				echo "&nbsp;";
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('hazCheckList.php');");
				echo form_button("cancel",_LBL_CANCEL,$dOthers);
				/*********/
			?></td>
		</tr>
	</table>
	<br />
	</form>
