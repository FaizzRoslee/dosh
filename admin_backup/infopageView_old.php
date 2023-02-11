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
				$sys_config['includes_path']."jquery/Qtip/qtip.css,". // css
				"", // css	
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
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
	$Usr_ID = $_SESSION['user']['Usr_ID'];
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
	//$infoID = '1';
	$infoFor = '';
	$infoDesc = '';
	$active = '';
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
	if(isset($_GET['imageID'])){
		$imageID = $_GET['imageID'];
		$imageURL = _get_StrFromCondition('sys_Info_Image','Image_URL','Image_ID',$imageID);
		if(!empty($imageURL))
			unlink($imageURL);
		$sqlDel	= "DELETE FROM sys_Info_Image WHERE Image_ID = ".quote_smart($imageID)." LIMIT 1";
		$resDel	= $db->sql_query($sqlDel,END_TRANSACTION) or die(print_r($db->sql_error()));
		$msg = ($resDel) ? 'Delete Successfully' : 'Delete Unsuccessfully';
		//==========================================================
		echo '
			<script language="Javascript">
			alert("'.$msg.'");
			</script>
			';
		//==========================================================
		$msg .= ' - '.$imageURL;
		func_add_audittrail($Usr_ID,$msg.' ['.$imageID.']','Admin-Info Image','sys_Info_Image,'.$imageID);
	}
	//==========================================================
?>
	<script type="text/javascript">
	$(document).ready(function() {
		
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
		
		$('#div_img a[title]').each(function() {
			$(this).qtip({
				//content: { title: { text: $(this).attr('title') } },
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: { name: 'blue', border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_INFO_PAGE .' :: '. _LBL_VIEW); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('infoID',$infoID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_IMAGE_FOR ?></td>
			<td width="80%"><label><?= (($infoFor!='')?$infoFor:'-') ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_IMAGE ?>&nbsp;<a href="infopageImage.php?infoID=<?= $infoID ?>"><?= _LBL_IMAGE_ADD ?></a></td>
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
					echo 	'<td width="80%" class="label">'. _LBL_FILENAME .'</td>';
					echo 	'<td width="15%" class="label" style="text-align:center">'. _LBL_DELETE .'</td>';
					echo '</tr>';
					foreach($arrayData as $detail){
						$i++;
						$ex_url = explode('/',$detail['Image_URL']);
						$count_ex_url = count($ex_url);
						$filename = $ex_url[ $count_ex_url - 1 ];

						$imageID = $detail['Image_ID'];
						
						echo '<tr class="contents">';
						echo '<td align="center">'.$i.'</td>';
						echo '
							<td><label><a href="#" rel="'.$sys_config['includes_path'].'getImageInfo.php?id='.$imageID.'">'.$filename.'</label></a></td>
							';
						echo '<td align="center"><a href="#" title="'. _LBL_DELETE_IMAGE .'" onClick="redirectForm(\'infopageView.php?infoID='.$infoID.'&imageID='.$imageID.'\');">'. _LBL_DELETE .'</a></td>';
						echo '</tr>';
					}
					echo '</table></div>';
				}else{
					echo '<label>'. _LBL_NO_IMAGE .'</label>';
				}
			?></td>
		</tr>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_INFO_DESC ?></td>
			<td width="80%"><?= (($infoDesc!='')?$infoDesc:'-') ?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				//========================================
				$param1 = 'edit';
				$param2 = _LBL_EDIT;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'infopageAddEdit.php?infoID='. $infoID .'\');');
				echo form_button($param1,$param2,$param3);
				//========================================
				echo '&nbsp;';
				//========================================
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'infopageList.php\');');
				echo form_button($param1,$param2,$param3);
				//========================================
			?></td>
		</tr>
	</table>
	<br />
	</form>