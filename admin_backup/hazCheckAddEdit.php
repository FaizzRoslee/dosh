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
	if(isset($_POST["codeID"])) $codeID = $_POST["codeID"];
	elseif(isset($_GET["codeID"])) $codeID = $_GET["codeID"];
	else $codeID = 0;
	/*********/
	if(isset($_POST["hidrow1"])) $hidrow1 = $_POST["hidrow1"];
	else $hidrow1 = 1;
	if(isset($_POST["hidrow2"])) $hidrow2 = $_POST["hidrow2"];
	else $hidrow2 = 1;
	if(isset($_POST["hidrow3"])) $hidrow3 = $_POST["hidrow3"];
	else $hidrow3 = 1;
	if(isset($_POST["hidrow4"])) $hidrow4 = $_POST["hidrow4"];
	else $hidrow4 = 1;
	/*********/
	if($codeID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	/*********/
	$casNo = "";
	$signalID = "";
	$pictogramID = array();
	$hStatementID = array();
	$pStatementID1 = array();
	$pStatementID2 = array();
	$pStatementID3 = array();
	$pStatementID4 = array();
	$typeID = 0;
	$active = 1;
	/*********/
	if($codeID!=0){
		$dataColumns = "*";
		$dataTable = "tbl_Hazard_Codes";
		$dataWhere = array("AND Code_ID"=>" = ".quote_smart($codeID));
		$arrayData = _get_arrayData($dataColumns,$dataTable,"",$dataWhere); 
		/*********/
		foreach($arrayData as $detail){
			$casNo = $detail["CAS_No"];
			$pictogramID = explode(",",$detail["Pictogram_ID"]);
			$signalID = $detail["Signal_ID"];
			$hStatementID = explode(",",$detail["Statement_ID"]);
			$pStatementID1 = explode(";",$detail["PreStatement_ID1"]);
			$pStatementID2 = explode(";",$detail["PreStatement_ID2"]);
			$pStatementID3 = explode(";",$detail["PreStatement_ID3"]);
			$pStatementID4 = explode(";",$detail["PreStatement_ID4"]);
			$typeID = $detail["Type_ID"];
			$active = $detail["Active"];
		}
	}
	/*********/
	if(isset($_POST["save"])){
		$typeID = isset($_POST["typeID"])?$_POST["typeID"]:0;
		/*********/
		$pictogramID = "";
		if(isset($_POST["pictogramID"])){
			$invComma = 0;
			foreach($_POST["pictogramID"] as $value){
				if($invComma==0){ $invComma = 1; }
				else{ $pictogramID .= ","; }
				$pictogramID .= $value;
			}
		}
		/*********/
		$signalID = isset($_POST["signalID"])?$_POST["signalID"]:"";
		if($signalID=="") $signalID = 0;
		/*********/
		$hStatementID = "";
		if(isset($_POST["hStatementID"])){
			$invComma = 0;
			foreach($_POST["hStatementID"] as $value){
				if($invComma==0){ $invComma = 1; }
				else{ $hStatementID .= ","; }
				$hStatementID .= $value;
			}
		}
		/*********/
		$pStatementID1="";$pStatementID2="";$pStatementID3="";$pStatementID4="";
		for($i=1;$i<=4;$i++){
			if($i==1){ $maxRow = $hidrow1; }
			elseif($i==2){ $maxRow = $hidrow2; }
			elseif($i==3){ $maxRow = $hidrow3; }
			else{ $maxRow = $hidrow4; }
			/*********/
			for($j=1;$j<=$maxRow;$j++){
				$inputNameID = "test_".$i."_".$j;
				$str = isset($_POST[$inputNameID])?$_POST[$inputNameID]:"";
				//echo $str."<br />";
				if($str!=""){
					$ex_str = explode(",",$str);
					//print_r($ex_str);
					//echo "<br />";
					$strStatement = (($j>1)?";":"");
					if(is_array($ex_str)){
						$invComma = 0;
						foreach($ex_str as $value){
							if(trim($value)!=""){
								if($invComma==0){ $invComma = 1; }
								else{ $strStatement .= "+"; }
								$strStatement .= _get_StrFromCondition("tbl_Precaution_Statement","Statement_ID","Statement_Code",trim($value));
							}
						}
					}
					/*********/
					if($i==1){ $pStatementID1 .= $strStatement; }
					elseif($i==2){ $pStatementID2 .= $strStatement; }
					elseif($i==3){ $pStatementID3 .= $strStatement; }
					else{ $pStatementID4 .= $strStatement;  }
				}
			}
		}
		/*********/
		$active = isset($_POST["active"])?$_POST["active"]:0;
		$updBy = $_SESSION["user"]["Usr_ID"];
		/*********/
		if($categoryID!=0){
			$sqlUpd	= "UPDATE tbl_Hazard_Codes SET"
					. " CAS_No = ".quote_smart($casNo).","
					. " Statement_ID = ".quote_smart($hStatementID)."," //hazard statement
					. " PreStatement_ID1 = ".quote_smart($pStatementID1).","
					. " PreStatement_ID2 = ".quote_smart($pStatementID2).","
					. " PreStatement_ID3 = ".quote_smart($pStatementID3).","
					. " PreStatement_ID4 = ".quote_smart($pStatementID4).","
					. " Signal_ID = ".quote_smart($signalID).","
					. " Type_ID = ".quote_smart($typeID).","
					. " Pictogram_ID = ".quote_smart($pictogramID).","
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
					. " CAS_No,Pictogram_ID,Signal_ID,"
					. " Statement_ID,Type_ID,"
					. " PreStatement_ID1,PreStatement_ID2,PreStatement_ID3,PreStatement_ID4,"
					. " Active,"
					. " RecordBy, RecordDate,"
					. " UpdateBy, UpdateDate"
					. " ) VALUES ("
					. " ".quote_smart($casNo).",".quote_smart($pictogramID).",".quote_smart($signalID).","
					. " ".quote_smart($hStatementID).",".quote_smart($typeID).","
					. " ".quote_smart($pStatementID1).",".quote_smart($pStatementID2).",".quote_smart($pStatementID3).",".quote_smart($pStatementID4).","
					. " ".quote_smart($active).","
					. " ".quote_smart($updBy).", NOW(),"
					. " ".quote_smart($updBy).", NOW()"
					. " )";
			//echo $sqlIns;
			$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
			$codeID = $db->sql_nextid();
			/*********/
			$msg = ($resIns) ? "Insert Successfully" : "Insert Unsuccessfully";
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
?>
	<script language="Javascript">
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
					"fn":"category",
					"typeID": function(){ return $("#typeID").val() },
					"codeID": function(){ return $("#codeID").val() }
				},
				success: function( msg ) {
					$("#sp-pictogram").html( msg.dPict );
					$("#sp-statement").html( msg.dState );
					reloadTip();
				}
			});
		}).trigger("change");

		$(".cls-add").live("click",function(){
			var id = $(this).attr("id");
			var pr = id.replace("pr-","");

			$(this).parent().find("table tbody tr:last").clone(false).insertAfter( $(this).parent().find("table tbody tr:last") );

			recalculateRow( $(this).parent().find("table tbody tr"), pr );
			$(this).parent().find("table tbody tr:last").find("input").val("");
			reloadFunction();

			return false;
		});

		$(".cls-empty").live("click",function(){
			var curr = $(this).parent().parent().parent().parent().parent();
			var id = curr.find(".cls-add").attr("id");
			var pr = id.replace("pr-","");

			// var test = $(this).parent().parent();
			// alert( test[0].nodeName );

			if( curr.find("tbody tr").length > 1 )
				$(this).parent().parent().remove();
			else curr.find("input").val("");

			recalculateRow( curr.find("tbody tr"), pr );
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

	</script>

	<!-- Add Hazard Checking Pages -->
	<?php func_window_open2(_LBL_HAZARD_CHECKING ." :: ". $addEdit); ?>
	<form name="myForm" action="" method="post">
	<?= form_input("codeID",$codeID,array("type"=>"hidden")); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td class="label"><?= _LBL_CAS_NO ?></td>
			<td><?php
				$param1 = "casNo";
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$casNo;
				$param3 = array("type"=>"text","style"=>"width:300px;");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_TYPE_NAME ?></td>
			<td width="80%"><?php
				$param1 = "typeID";
				$param2 = "";
				$param3 = _get_arraySelect("tbl_Hazard_Type","Type_ID","Type_Name",array("AND Active"=>"= 1"));
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$typeID;
				$param5 = array();
				echo form_select($param1,$param2,$param3,$param4,$param5);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HAZARD_PICTOGRAM ?></td>
			<td><div id="sp-pictogram"><span id="span_info1" class="formInfo"><a href="#" rel="help.php?help=pic" title="<?= _LBL_HELP_APPEAR_IF_TITLE ?>" >?</a></span></div></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_SIGNAL_WORD ?></td>
			<td><?php
				$param1 = "signalID";
				$param2 = "";
				$param3 = _get_arraySelect("tbl_Hazard_Signal","Signal_ID","Signal_Name",array("AND Active"=>"= 1"));
				$param4 = isset($_POST[$param1])?$_POST[$param1]:$signalID;
				
				echo form_select($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_HAZARD_STATEMENT ?></td>
			<td><div id="sp-statement"><span id="span_info2" class="formInfo"><a href="#" rel="help.php?help=hSttmnt" title="<?= _LBL_HELP_APPEAR_IF_TITLE ?>" >?</a></span></div></td>
		</tr>
		<tr class="contents">
			<td class="label title" colspan="2"><?= _LBL_PRECAUTIONARY_STATEMENT ?></td>
		</tr>
		<?php
			$arrPreStatement = _get_arraySelect("tbl_Precaution_Type","Precaution_ID","Precaution_Name",array("AND Active"=>"= 1"),"Precaution_ID");
			foreach($arrPreStatement as $detail){
				$pID = $detail["value"];
				echo '<input type="hidden" name="pID[]" id="pID" value="'.$pID.'">';
		?>
		<tr class="contents">
			<td class="label"><?= $detail["label"] ?></td>
			<td><a href="#" class="cls-add" id="pr-<?= $pID ?>"><?= _LBL_ADD ?></a><br /><?php
				if($pID==1){ $existingData = $pStatementID; }
				
				echo '<table width="90%" cellspacing="1" cellpadding="3" class="contents">';
				echo 	'<thead>';
				echo 	'<tr class="contents"><td width="5%" class="label" align="center">'. _LBL_NUMBER .'</td><td width="85%" class="label">'. _LBL_STTMNT_CODE .'</td><td width="10%" class="label">'. _LBL_DELETE .'</td></tr>';
				echo 	'</thead>';
				echo 	'<tbody>';
				
				$r = 0;
				foreach($existingData as $value){
					if($value!=""){
						$r++;
						/*********/
						$ex_value = explode("+",$value);
						$new_value = "";
						$new_title = "";
						foreach($ex_value as $value2){
							if($value2!="")
								$new_value .= _get_StrFromCondition("tbl_Precaution_Statement","Statement_Code","Statement_ID",trim($value2)).", ";
						}
						/*********/
						$inputNameID = "test_".$pID."_".$r;
						echo '<tr class="contents">';
						echo 	'<td align="center">'.$r.'</td>';

						$param1 = $inputNameID;
						$param2 = isset($_POST[$param1])?$_POST[$param1]:$new_value;
						$param3 = array("type"=>"text","style"=>"width:90%;","class"=>"cls-AutoComplete");
						echo 	'<td>'. form_input($param1,$param2,$param3) .'</td>';
						
						$imgParam1 = $sys_config["images_path"]."icons/delete.gif";
						$imgParam2 = _LBL_EMPTY;
						$imgParam3 = "";
						echo 	'<td><span class="cls-empty">'. _get_imagebutton($imgParam1,$imgParam2,$imgParam3) .'</span></td>';
						
						echo '</tr>';
					}
				}
				
				if($r==0){
					$r++;
					$inputNameID = "test_".$pID."_".$r;
					echo 	'<tr class="contents">';
					echo 		'<td align="center">'.$r.'</td>';
					
					$param1 = $inputNameID;
					$param2 = isset($_POST[$param1])?$_POST[$param1]:"";
					$param3 = array("type"=>"text","style"=>"width:90%","class"=>"cls-AutoComplete");
					echo 		'<td>'. form_input($param1,$param2,$param3) .'</td>';
					
					$imgParam1 = $sys_config["images_path"]."icons/delete.gif";
					$imgParam2 = _LBL_EMPTY;
					$imgParam3 = "";
					echo 		'<td><span class="cls-empty">'. _get_imagebutton($imgParam1,$imgParam2,$imgParam3) .'</span></td>';
					
					echo 	'</tr>';
				}
				echo 	'</tbody>';
				echo 	'</table>';
				
				if($pID==1) $hidrow1 = $r;
				elseif($pID==2) $hidrow2 = $r;
				elseif($pID==3) $hidrow3 = $r;
				else $hidrow4 = $r;
			?>
			</td>
		</tr>
		<?php
			}
		?>
		<?php
			echo form_input("hidrow1",$hidrow1,array("type"=>"hidden"));
			echo form_input("hidrow2",$hidrow2,array("type"=>"hidden"));
			echo form_input("hidrow3",$hidrow3,array("type"=>"hidden"));
			echo form_input("hidrow4",$hidrow4,array("type"=>"hidden"));
		?>
		<tr class="contents">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td><?php
				$param1 = "active";
				$param2 = $arrStatusAktif;
				$param3 = isset($_POST[$param1])?$_POST[$param1]:$active;
				$param4 = "&nbsp;";
				
				echo form_radio($param1,$param2,$param3,$param4,"");
			?></td>
		</tr>
	</table>
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
