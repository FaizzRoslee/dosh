<?php
	//****************************************************************/
	// filename: stateAddEdit.php
	// description: add/edit state information
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
				$sys_config['includes_path']."jquery/jHtmlArea/jHtmlArea.css,". // css	
				"", // css	
				// $sys_config['includes_path']."jquery/jHtmlArea/scripts/jquery-1.3.2.min.js,". // javascript
				// $sys_config['includes_path']."jquery/jHtmlArea/scripts/jquery-ui-1.7.2.custom.min.js,". // javascript
				$sys_config['includes_path']."jquery/jHtmlArea/js/jHtmlArea-0.7.0.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				"", // javascript
				false, // menu
				false, // portlet
				false, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false // int top for [enter]
				);
	//==========================================================
	$userLevelID = $_SESSION['user']['Level_ID'];
	if($userLevelID>2) exit();
	//==========================================================
	if(isset($_POST['infoID'])) $infoID = $_POST['infoID'];
	elseif(isset($_GET['infoID'])) $infoID = $_GET['infoID'];
	else $infoID = 0;
	//==========================================================
	if($infoID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	//==========================================================
	if(isset($_POST['save'])){
		$infoDesc = isset($_POST['infoDesc'])?$_POST['infoDesc']:'';
		$updBy = $_SESSION['user']['Usr_ID'];
		//===================================
		$sqlUpd	= "UPDATE sys_Info SET"
				. " Info_Desc = ".quote_smart($infoDesc).","
				. " UpdateBy = ".quote_smart($updBy).","
				. " UpdateDate = NOW()"
				. " WHERE Info_ID = ".quote_smart($infoID)." LIMIT 1";
		$resUpd	= $db->sql_query($sqlUpd, END_TRANSACTION) or die(print_r($db->sql_error()));
		//===================================
		$msg = ($resUpd) ? 'Update Successfully' : 'Update Unsuccessfully';
		//===================================
		func_add_audittrail($updBy,$msg.' ['.$infoID.']','Admin-Info','sys_Info,'.$infoID);//($recordBy='',$recordDesc='',$recordMod='',$recordOther='')
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			location.href="infopageView.php?infoID='.$infoID.'";
			</script>
			';
		exit();
	}
	//==========================================================
	$infoDesc = '';
	$infoFor = '';
	$active = 1;
	//==========================================================
	if($infoID!=0){
		$dataColumns = '*';
		$dataTable = 'sys_Info';
		$dataWhere = array("AND Info_ID" => " = ".quote_smart($infoID)."");
		$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere);
		//==========================================================
		foreach($arrayData as $detail){
			$infoDesc = $detail['Info_Desc'];
			$infoFor = $detail['Info_For'];
			$active = $detail['Active'];
		}
	}
	//==========================================================
?>
    <style type="text/css">
        /* body { background: #ccc;} */
        div.jHtmlArea .ToolBar ul li a.custom_disk_button 
        {
            background: url(images/disk.png) no-repeat;
            background-position: 0 0;
        }
        
        div.jHtmlArea { border: solid 1px #ccc; }
    </style>
	<script language="Javascript">
	$(document).ready(function() {
	
		$("#infoDesc").htmlarea(); // Initialize all TextArea's as jHtmlArea's with default values
		$("#clear").click(function() {
			$("#infoDesc").htmlarea("dispose");
			$("#infoDesc").htmlarea();
		});
		
		$('#div_img a[rel]').each(function() {
			$(this).qtip({
				content: {
					url: $(this).attr('rel'), // Use the rel attribute of each element for the url to load
					title: {
					   text: '<?= _LBL_IMAGE_FOR ?>: ' + $(this).text(), // Give the tooltip a title using each elements text
					   button: '<?= _LBL_CLOSE ?>' // Show a close link in the title
					}
					
				},
				position: { 
					corner: { tooltip: 'topMiddle', target: 'bottomMiddle' },
					adjust: { 
						screen: true // Keep the tooltip on-screen at all times
					}  },

				style: {
					name: 'blue', // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					textAlign: 'center',
					width: 630

				},
				show: {
					when: 'click', // Show it on click...
					solo: true // ...but hide all others when its shown
				},
				hide: 'unfocus'
			});
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_INFO_PAGE .' :: '. $addEdit); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('infoID',$infoID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_INFO_FOR ?></td>
			<td width="80%"><label><?= (($infoFor!='')?$infoFor:'-') ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_IMAGE ?></td>
			<td><?php
				$dataColumns = '*';
				$dataTable = 'sys_Info_Image';
				$dataWhere = array("AND Info_ID" => " = ".quote_smart($infoID)."");
				$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere);
				$count_arrayData = count($arrayData);
				//==========================================================
				if($count_arrayData>0){
					$i=0;
					echo '<div id="div_img"><table border="0" width="100%" cellpadding="3" cellspacing="1" class="contents">';
					echo '<tr class="contents">';
					echo 	'<td width="5%" class="label">'. _LBL_NUMBER .'</td>';
					echo 	'<td width="35%" class="label">'. _LBL_FILENAME .'</td>';
					echo 	'<td width="60%" class="label">'. _LBL_IMAGE_URL .'</td>';
					echo '</tr>';
					foreach($arrayData as $detail){
						$i++;
						$ex_url = explode('/',$detail['Image_URL']);
						$count_ex_url = count($ex_url);
						$filename = $ex_url[ $count_ex_url - 1 ];

						
						echo '<tr class="contents">';
						echo '<td align="center">'.$i.'</td>';
						echo '
							<td><label><a href="#" rel="'.$sys_config['includes_path'].'getImageInfo.php?id='.$detail['Image_ID'].'">'.$filename.'</label></a></td>
							';
						echo '<td>'. $detail['Image_URL'] .'</td>';
						echo '</tr>';
					}
					echo '</table></div>';
					echo '<br />';
					echo addNote(_LBL_IMAGE_NOTE);
				}else{
					echo '<label>'. _LBL_NO_IMAGE .'</label>';
				}
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_INFO_DESC ?></td>
			<td width="80%"><?php
				$param1 = 'infoDesc';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$infoDesc;
				$param3 = array('cols'=>'90','rows'=>'20');
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				//========================================
				$param1 = 'save';
				$param2 = _LBL_SAVE;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
				//========================================
				echo '&nbsp;';
				//========================================
				$param1 = 'clear';
				$param2 = 'Clear';
				$param3 = array('type'=>'button');
				echo form_button($param1,$param2,$param3);
				//========================================
				echo '&nbsp;';
				//========================================
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'infopageView.php?infoID='.$infoID.'\');');
				echo form_button($param1,$param2,$param3);
				//========================================
			?></td>
		</tr>
	</table>
	<br />
	</form>