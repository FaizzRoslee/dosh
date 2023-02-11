<?php
	//****************************************************************/
	// filename: hazCheckAddEdit.php
	// description: list of the hazard checking
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
				$sys_config["includes_path"]."css/global.css,".// css
				$sys_config["includes_path"]."jquery/jquery-autocomplete/jquery.autocomplete.css,". // css
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.css,". // css
				"", // css
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				// $sys_config["includes_path"]."jquery/jquery-autocomplete/lib/jquery.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery-autocomplete/lib/jquery.bgiframe.min.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery-autocomplete/lib/jquery.ajaxQueue.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery-autocomplete/jquery.autocomplete.js,". // javascript
				$sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
				// $sys_config["includes_path"]."jquery/jquery.livequery.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.totop/ui.totop.js,". // javascript
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
	if(isset($_POST["hidrow1"])) $hidrow1 = $_POST["hidrow1"];
	else $hidrow1 = 1;
	/*********/
	if(isset($_POST["newhidrow1"])) $newhidrow1 = $_POST["newhidrow1"];
	else $newhidrow1 = 1;
	/*********/
	if(isset($_POST["hidrow2"])) $hidrow2 = $_POST["hidrow2"];
	else $hidrow2 = 1;
	/*********/
	if(isset($_POST["newhidrow2"])) $newhidrow2 = $_POST["newhidrow2"];
	else $newhidrow2 = 1;
	/*********/
	if(isset($_POST["hidrow3"])) $hidrow3 = $_POST["hidrow3"];
	else $hidrow3 = 1;
	/*********/
	if(isset($_POST["newhidrow3"])) $newhidrow3 = $_POST["newhidrow3"];
	else $newhidrow3 = 1;
	/*********/
	if($chemicalID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	/*********/

	if(isset($_POST["save"])){
		$codeID 		= isset($_POST["codeID"])?$_POST["codeID"]:"";
		$statementID 	= isset($_POST["statementID"])?$_POST["statementID"]:array();
		$signalID 		= isset($_POST["signalID"])?$_POST["signalID"]:"";

		if($signalID=="") $signalID = 0;
		$pictogramID	= isset($_POST["pictogramID"])?$_POST["pictogramID"]:array();
		$updBy = $_SESSION["user"]["Usr_ID"];
		$msg = "";

		/*********/
		$categoryIDs	= isset($_POST["catIDs"])?$_POST["catIDs"]:array();
		$arrCategoryIDs	= "";
		/*********/
		foreach($categoryIDs as $id){
			if($id!=""){
				if($arrCategoryIDs!=""){ $arrCategoryIDs .= ","; }
				$arrCategoryIDs .= $id;
			}
		}
		/*********/
		$arrStatementIDs = '';
		foreach($statementID as $detail){
			if($arrStatementIDs!='') $arrStatementIDs .= ',';
			$arrStatementIDs .= $detail;
		}
		/*********/
		$arrPictogramIDs = '';
		foreach($pictogramID as $detail){
			if($arrPictogramIDs!='') $arrPictogramIDs .= ',';
			$arrPictogramIDs .= $detail;
		}

		/*********/
		$signalID = isset($_POST["signalID"])?$_POST["signalID"]:"";
		if($signalID=="") $signalID = 0;
		/*********/
		$active = isset($_POST["active"])?$_POST["active"]:0;
		$updBy = $_SESSION["user"]["Usr_ID"];
		/*********/
		if($codeID!=0){
			$sqlUpd	= "UPDATE tbl_Hazard_Codes SET"
					. " Category_ID = ".quote_smart($arrCategoryIDs).","
					. " Signal_ID = ".quote_smart($lbl_signalID).","
					. " Pictogram_ID = ".quote_smart($arrPictogramIDs).","
					. " Active = ".quote_smart($active).","
					. " UpdateBy = ".quote_smart($updBy).","
					. " UpdateDate = NOW()"
					. " WHERE Code_ID = ".quote_smart($codeID)." LIMIT 1";
			//echo $sqlUpd;
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
			$msg = ($resUpd) ? 'Update Successfully' : 'Update Unsuccessfully';
			/*********/
		}else{
			$sqlIns	= "INSERT INTO tbl_Hazard_Codes ("
					. " Category_ID,Signal_ID,Pictogram_ID,"
					. " Chemical_ID,"
					. " RecordBy,RecordDate,UpdateBy,UpdateDate,"
					. " Active"
					. " ) VALUES ("
					. " ".quote_smart($arrCategoryIDs).",".quote_smart($lbl_signalID).",".quote_smart($arrPictogramIDs).","
					. " ".quote_smart($chemicalID).","
					. " ".quote_smart($updBy).",NOW(),".quote_smart($updBy).",NOW(),"
					. " 1"
					. " )";
			//echo $sqlIns;
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$codeID = $db->sql_nextid();
			/*********/
			if($resIns) $msg = "Insert Successfully";
			else $msg = "Insert Unsuccessfully";
			/*********/
			$sqlUpd	= "UPDATE tbl_Hazard_Codes SET"
					. " Active = 0, UpdateBy = ".quote_smart($updBy).", UpdateDate = NOW()"
					. " WHERE Code_ID != ".quote_smart($codeID)." AND Chemical_ID = ".quote_smart($chemicalID)."";
			$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
			/*********/
		}
		/*********/
		func_add_audittrail($updBy,$msg." [".$codeID."]","Admin-Hazard Checking","tbl_Hazard_Codes,".$codeID);
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href="hazCheckList.php";
			</script>
			';
		exit();
	}
	/*********/
	$chemName 		= "";
	$chemIUPAC 		= "";
	$chemCAS 		= "";
	$classifiedID 	= "";
	$categoryID1 	= "";
	$categoryID2 	= "";
	$categoryID3 	= "";
	$signalID	 	= "";
	$pictogramID	= "";
	$arrClass[]		= "";

	/*********/
	if($chemicalID!=0){
		$dataColumns = "*";
		$dataTable = "tbl_Chemical c";
		$dataJoin = array("tbl_Hazard_Codes hc"=>"c.Chemical_ID = hc.Chemical_ID");
		$dataWhere = array("AND c.Chemical_ID"=>"= ".quote_smart($chemicalID),"AND hc.Active"=>"= 1");
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere);
		//==========================================================
		foreach($arrayData as $detail){
			$chemName 		= $detail["Chemical_Name"];
			$chemIUPAC 		= $detail["Chemical_IUPAC"];
			$chemCAS 		= $detail["Chemical_CAS"];
			$classifiedID 	= $detail["Classified_ID"];

			$catIDs	= $detail["Category_ID"];
			$ex_catIDs	= explode(",",$catIDs);

			foreach($ex_catIDs as $id){
				$tID = _get_StrFromCondition("tbl_Hazard_Category","Type_ID","Category_ID",$id);
				if($tID==1){
					$categoryID1 .= ($categoryID1!="") ? ",".$id : $id;
				}
				elseif($tID==2){
					$categoryID2 .= ($categoryID2!="") ? ",".$id : $id;
				}
				elseif($tID==3){
					$categoryID3 .= ($categoryID3!="") ? ",".$id : $id;
				}
			}
			$signalID	 	= $detail["Signal_ID"];
			$pictogramID 	= $detail["Pictogram_ID"];
		}
	}
	/*********/
	$str_onClick = "getChemID();";
?>
	<script language="Javascript">

	function addRow(n){
		var d = document;
		var row = d.getElementById(n).value;
		d.getElementById(n).value = parseInt(row) + 1;
		d.myForm.submit();
	}
	function emptyRow(el){
		var d = document;
		d.getElementById(el).value = '';
	}
	function formSubmit(){
		document.myForm.submit();
	}

	$(document).ready(function() {
		function formatItem(row) {
			return row[0] + " (<?= _LBL_DESCRIPTION ?>: <em>" + row[1] + "</em>)";
		}
		function formatResult(row) {
			return row[0].replace(/(<.+?>)/gi, "");
		}

		reloadFunction();
		reloadTip();

		$(".cls-AutoComplete").each(function(){
			if($(this).data("qtip")) $(this).qtip("destroy");
			if($(this).val()!=""){
				var inputAutoComplete = $(this).attr("id");
				$.ajax({
					url: "<?= $sys_config["admin_path"] ."admin-ajax.php"; ?>",
					type : "POST",
					dataType : "json",
					data: {
						"fn":"autocomplete",
						"ids": function(){ return $("#"+inputAutoComplete).val() }
					},
					success: function( msg ) {
						if($("#"+inputAutoComplete).data("qtip")) $("#"+inputAutoComplete).qtip("destroy");
						$("#"+inputAutoComplete)
							.attr("title",msg.sttmnt)
							.qtip({
								position: { corner: { tooltip: "bottomLeft", target: "topLeft" } },
								style: { name: "blue", border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
							});

					}
				});
			}
		});

		$("#typeID").change(function(){
			$.ajax({
				url: "<?= $sys_config["admin_path"] ."admin-ajax.php"; ?>",
				type : "POST",
				dataType : "json",
				data: {
					"fn":"codes",
					"typeID": function(){ return $("#typeID").val() },
					"codeID": function(){ return $("#codeID").val() }
				},
				success: function( msg ) {
					$("#sp-pictogram").html( msg.dPict );
					$("#sp-statement").html( msg.dState );
					$("#submission-details").html( msg );
					reloadTip();
				}
			});
		}).trigger("change");

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
		$('#imgDisplay a[rel]').each(function() {
			$(this).qtip({
				content: {
					url: $(this).attr('rel'), // Use the rel attribute of each element for the url to load
					title: {
						text: '<?= _LBL_PICTOGRAM_FOR ?>: ' + $(this).text(), // Give the tooltip a title using each elements text
						//button: 'Close' // Show a close link in the title
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

		function reloadFunction(){

			$(".cls-AutoComplete").autocomplete("search.php", {
				width: 400,
				multiple: true,
				selectFirst: false,
				formatItem: formatItem,
				formatResult: formatResult
			});

			$(".cls-AutoComplete").blur(function(){
				if($(this).data("qtip")) $(this).qtip("destroy");
				if($(this).val()!=""){
					var inputAutoComplete = $(this).attr("id");
					$.ajax({
						url: "<?= $sys_config["admin_path"] ."admin-ajax.php"; ?>",
						type : "POST",
						dataType : "json",
						data: {
							"fn":"autocomplete",
							"ids": function(){ return $("#"+inputAutoComplete).val() }
						},
						success: function( msg ) {

							if($("#"+inputAutoComplete).data("qtip")) $("#"+inputAutoComplete).qtip("destroy");
							$("#"+inputAutoComplete)
								.attr("title",msg.sttmnt)
								.qtip({
									position: { corner: { tooltip: "bottomLeft", target: "topLeft" } },
									style: { name: "blue", border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
								});

						}
					});
				}
			});
		}

		function reloadTip(){
			$("#div_img a[rel]").each(function() {
				$(this).qtip({
					content: {
						url: $(this).attr("rel"), // Use the rel attribute of each element for the url to load
						title: {
						   text: "<?= _LBL_PICTOGRAM_FOR ?>: " + $(this).text() // Give the tooltip a title using each elements text
						}
					},
					position: { corner: { tooltip: "topMiddle", target: "bottomMiddle" } },

					style: {
						name: "blue", // Give it the preset dark style //dark/light/cream/green/red/blue
						border: { width: 0, radius: 4  },
						tip: true, // Apply a tip at the default tooltip corner
						textAlign: "center"
					}
				});
			});
			$("#div_hs a[rel]").each(function() {
				$(this).qtip({
					content: { url: $(this).attr("rel"), title: { text: "Description for: " + $(this).text() } },
					position: { corner: { tooltip: "topMiddle", target: "bottomMiddle" } },
					style: { name: "blue", border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
				});
			});
			$("#span_info1 a[rel],#span_info2 a[rel]").each(function() {
				$(this).qtip({
					content: { url: $(this).attr("rel"), title: { text: $(this).attr("title") } },
					position: { corner: { tooltip: "topMiddle", target: "bottomMiddle" } },
					style: { name: "blue", border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
				});
			});
		}

		function recalculateRow(obj,pr){
			var i = 0;
			obj.each(function(){
				i++;
				$(this).find("td:first").html(i);
				$(this).find("td:eq(1) input").attr({
					name: "test_"+ pr +"_"+ i,
					id: "test_"+ pr +"_"+ i
				});
			});
			$("#hidrow"+pr).val(i);
		}

		$().UItoTop({ easingType: "easeOutQuart" });
		/* $("button").removeClass("btn-new-style").button(); */
	});

	function getChemID(){
		url = '<?= $sys_config['inventory_path'] ?>chemicalSearch.php';
		funcPopup(url,'900','600');
	}
	</script>

	<!-- Add Hazard Checking Pages -->
	<?php func_window_open2(_LBL_HAZARD_CHECKING ." :: ". $addEdit); ?>
	<form name="myForm" action="" method="post">
	<?= form_input("chemicalID",$chemicalID,array("type"=>"hidden")); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_CHEMICAL_NAME ?></td>
			<td colspan="3" class="nama"><?php
				$param1 = 'chemName';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemName;
				$param3 = array('type'=>'text','readonly'=>'readonly','style'=>'width:50%;','onClick'=>$str_onClick);
				echo form_input($param1,$param2,$param3); ?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td colspan="3" class="nama"><?php
				$param1 = 'chemCAS';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$chemCAS;
				$param3 = array('type'=>'text','readonly'=>'readonly','onClick'=>$str_onClick);
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SIGNAL_WORD ?></td>
			<td><?php
				$param1 = 'signalID';
				$param2 = '';
				$param3 = _get_arraySelect('tbl_Hazard_Signal','Signal_ID','Signal_Name',array('AND Active'=>'= 1'));
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$signalID;
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
	</table>
	<br />

	<?= form_input('hidrow1',$hidrow1,array('type'=>'hidden')); ?>
	<?= form_input('hidrow2',$hidrow2,array('type'=>'hidden')); ?>
	<?= form_input('hidrow3',$hidrow3,array('type'=>'hidden')); ?>

	<?php
		//=============================================
		$countRec1 = 0;
		if($categoryID1!=''){
			$arrCategoryID1 = explode(',',$categoryID1);
			$countRec1 = count($arrCategoryID1);
		}
		$rowspan1 = $hidrow1+$countRec1;
		//=============================================
		$countRec2 = 0;
		if($categoryID2!=''){
			$arrCategoryID2 = explode(',',$categoryID2);
			$countRec2 = count($arrCategoryID2);
		}
		$rowspan2 = $hidrow2+$countRec2;
		//=============================================
		$countRec3 = 0;
		if($categoryID3!=''){
			$arrCategoryID3 = explode(',',$categoryID3);
			$countRec3 = count($arrCategoryID3);
		}
		$rowspan3 = $hidrow3+$countRec3;
		//=============================================
		$subSelect = '(SELECT Class_Name FROM tbl_Hazard_Classification hc WHERE Class_ID = tbl_Hazard_Category.Class_ID LIMIT 0,1)';
		//=============================================
	?>

	<!-- Hazard Classification Codes -->
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="30">
			<td class="label title" colspan="2"><?= _LBL_HAZARD_TYPE ?></td>
		</tr>

		<tr class="contents">
			<td class="label" <?= ($rowspan1>1)?'rowspan="'. $rowspan1 .'"':'' ?> width="20%"><?= _LBL_PHY_HAZARD ?> <a href="#" onClick="addRow('hidrow1');">Add</a></td>
			<?php
				$i = 1;

				if(isset($arrCategoryID1)){
					foreach($arrCategoryID1 as $value){
						//==========================================================================================
						if($i>1){ echo '<tr class="contents" width="80%">'; }
						echo '<td>';
						//==========================================================================================
						$param1 = 'categoryID1_'.$i;
						// $param2 = _get_arrayParent('tbl_Hazard_Type','Type_ID','Type_Name',array('AND Active'=>'= 1'),'Type_ID');
						$param2 = '';
						$param3 = _get_arraySelectParent('tbl_Hazard_Category','Category_ID',
									"CONCAT_WS(': ',".$subSelect.",Category_Name)",
									'Type_ID',array('AND Active'=>'= 1','AND Type_ID'=>'= 1'),
									$subSelect.', Category_Name'
								);
						$param4 = isset($_POST[$param1])?$_POST[$param1]:$value;
						$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
						//==========================================================================================
						echo form_select($param1,$param2,$param3,$param4,$param5);
						//==========================================================================================
						if($param4!=''){
							$arrClass[] = $param4;
							echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
						}
						echo '</td></tr>';
						$i++;
					}
				}
				if($i==0) $i++;
				else $newhidrow1 = $hidrow1+$i;
				for(;$i<$newhidrow1;$i++){
					if($i>1){ echo '<tr class="contents" width="80%">'; }
					echo '<td>';
					//==========================================================================================
					$param1 = 'categoryID1_'.$i;
					$param2 = '';
					$param3 = _get_arraySelectParent('tbl_Hazard_Category','Category_ID',
								"CONCAT_WS(': ',".$subSelect.",Category_Name)",
								'Type_ID',array('AND Active'=>'= 1','AND Type_ID'=>'= 1'),
								$subSelect.', Category_Name'
							);
					$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
					//==========================================================================================
					echo form_select($param1,$param2,$param3,$param4,$param5);
					//==========================================================================================
					if($param4!=''){
						$arrClass[] = $param4;
						echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
					}
					echo '</td></tr>';
				}

			?>
		<tr class="contents">
			<td class="label" <?= ($rowspan2>1)?'rowspan="'. $rowspan2 .'"':'' ?> width="20%"><?= _LBL_HEALTH_HAZARD ?> <a href="#" onClick="addRow('hidrow2');">Add</a></td>
			<?php
				$i = 1;

				if(isset($arrCategoryID2)){
					foreach($arrCategoryID2 as $value){
						//==========================================================================================
						if($i>1){ echo '<tr class="contents" width="80%">'; }
						echo '<td>';
						//==========================================================================================
						$param1 = 'categoryID2_'.$i;
						$param2 = '';
						$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
									"CONCAT_WS(': ',".$subSelect.",Category_Name)",
									array('AND Active'=>'= 1','AND Type_ID'=>'= 2'),
									$subSelect.', Category_Name'
								);
						$param4 = isset($_POST[$param1])?$_POST[$param1]:$value;
						$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
						//==========================================================================================
						echo form_select($param1,$param2,$param3,$param4,$param5);
						//==========================================================================================
						if($param4!=''){
							$arrClass[] = $param4;
							echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
						}
						echo '</td></tr>';
						$i++;
					}
				}
				if($i==0) $i++;
				else $newhidrow2 = $hidrow2+$i;
				for(;$i<$newhidrow2;$i++){
					if($i>1){ echo '<tr class="contents" width="80%">'; }
					echo '<td>';
					//==========================================================================================
					$param1 = 'categoryID2_'.$i;
					$param2 = '';
					$param3 = _get_arraySelect('tbl_Hazard_Category','Category_ID',
								"CONCAT_WS(': ',".$subSelect.",Category_Name)",
								array('AND Active'=>'= 1','AND Type_ID'=>'= 2'),
								$subSelect.', Category_Name'
							);
					$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
					//==========================================================================================
					echo form_select($param1,$param2,$param3,$param4,$param5);
					//==========================================================================================
					if($param4!=''){
						$arrClass[] = $param4;
						echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
					}
					echo '</td></tr>';
				}

			?>
		<tr class="contents">
			<td class="label" <?= ($rowspan3>1)?'rowspan="'. $rowspan3 .'"':'' ?> width="20%"><?= _LBL_ENV_HAZARD ?> <a href="#" onClick="addRow('hidrow3');">Add</a></td>
			<?php
				$i = 1;

				if(isset($arrCategoryID3)){
					foreach($arrCategoryID3 as $value){
						//==========================================================================================
						if($i>1){ echo '<tr class="contents" width="80%">'; }
						echo '<td>';
						//==========================================================================================
						$param1 = 'categoryID3_'.$i;
						$param2 = '';
						$param3 = _get_arraySelectParent('tbl_Hazard_Category','Category_ID',
									"CONCAT_WS(': ',".$subSelect.",Category_Name)",
									'Type_ID',array('AND Active'=>'= 1','AND Type_ID'=>'= 3'),
									$subSelect.', Category_Name'
								);
						$param4 = isset($_POST[$param1])?$_POST[$param1]:$value;
						$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
						//==========================================================================================
						echo form_select($param1,$param2,$param3,$param4,$param5);
						//==========================================================================================
						if($param4!=''){
							$arrClass[] = $param4;
							echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
						}
						echo '</td></tr>';
						$i++;
					}
				}
				if($i==0) $i++;
				else $newhidrow3 = $hidrow3+$i;
				for(;$i<$newhidrow3;$i++){
					if($i>1){ echo '<tr class="contents" width="80%">'; }
					echo '<td>';
					//==========================================================================================
					$param1 = 'categoryID3_'.$i;
					$param2 = '';
					$param3 = _get_arraySelectParent('tbl_Hazard_Category','Category_ID',
								"CONCAT_WS(': ',".$subSelect.",Category_Name)",
								'Type_ID',array('AND Active'=>'= 1','AND Type_ID'=>'= 3'),
								$subSelect.', Category_Name'
							);
					$param4 = isset($_POST[$param1])?$_POST[$param1]:'';
					$param5 = array('onChange'=>'formSubmit();','style'=>'width:400px'); //others
					//==========================================================================================
					echo form_select($param1,$param2,$param3,$param4,$param5);
					//==========================================================================================
					if($param4!=''){
						$arrClass[] = $param4;
						echo '<input type="hidden" name="catIDs[]" value="'. $param4 .'" />';
					}
					echo '</td></tr>';
				}

			?>

		<?php
			$countArr = count($arrClass);
			// print_r($arrClass);
		?>
		<?= form_input('classifiedID',$classifiedID,array('type'=>'hidden')); ?>
	</table>

	<?= form_input('newhidrow1',$newhidrow1,array('type'=>'hidden')); ?>
	<?= form_input('newhidrow2',$newhidrow2,array('type'=>'hidden')); ?>
	<?= form_input('newhidrow3',$newhidrow3,array('type'=>'hidden')); ?>
	<br />

	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				$dOptions = array("type"=>"submit");
				echo form_button("save",_LBL_SAVE,$dOptions);
				echo "&nbsp;";
				$dOptions = array("type"=>"button","onClick"=>"redirectForm('hazCheckList.php');");
				echo form_button("cancel",_LBL_CANCEL,$dOptions);
			?></td>
		</tr>
	</table>
	<br />
	</form>
