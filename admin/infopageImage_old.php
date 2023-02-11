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
				$sys_config['includes_path']."jquery/jquery-uploadify/css/uploadify.css,". // css
				$sys_config['includes_path']."jquery/jquery-uploadify/css/uploadify.jGrowl.css,". // css
				$sys_config['includes_path']."jquery/Qtip/qtip.css,". // css
				"", // css	
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				$sys_config['includes_path']."jquery/jquery-uploadify/js/jquery.uploadify.js,". // javascript
				$sys_config['includes_path']."jquery/jquery-uploadify/js/jquery.jgrowl_minimized.js,". // javascript
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
		
		$("#fileUpload1").fileUpload({
			'uploader': '<?= $sys_config['includes_path'] ?>jquery/jquery-uploadify/uploadify/uploader.swf',
			'cancelImg': '<?= $sys_config['includes_path'] ?>jquery/jquery-uploadify/uploadify/cancel.png',
			'script': 'uploadify_upload.php',
			'fileDesc': 'Image Files',
			'fileExt': '*.jpg;*.jpeg;*.png;*.gif',
			'multi': true,
			'simUploadLimit': 2,
			'sizeLimit': 1048576, //limit size to 1MB
			'buttonText': '<?= _LBL_IMAGE_BROWSE ?>',
			'displayData': 'speed',
			'scriptData': {'id':'<?= $infoID ?>','usrID':'<?= $Usr_ID ?>'},
			onError: function (event, queueID ,fileObj, errorObj) {
				var msg;
				if (errorObj.status == 404) {
					alert('Could not find upload script. Use a path relative to: '+'<?= getcwd() ?>');
					msg = 'Could not find upload script.';
				} else if (errorObj.type === "HTTP")
					msg = errorObj.type+": "+errorObj.status;
				else if (errorObj.type ==="File Size")
					msg = fileObj.name+'<br>'+errorObj.type+' Limit: '+Math.round(errorObj.sizeLimit/1024)+'KB';
				else
					msg = errorObj.type+": "+errorObj.text;
				$.jGrowl('<p></p>'+msg, {
					theme: 	'error',
					header: 'ERROR',
					sticky: true
				});			
				$("#fileUpload1" + queueID).fadeOut(250, function() { $("#fileUpload1" + queueID).remove()});
				return false;
			},
			onCancel: function (a, b, c, d) {
				var msg = "Cancelled uploading: "+c.name;
				$.jGrowl('<p></p>'+msg, {
					theme: 	'warning',
					header: 'Cancelled Upload',
					life:	4000,
					sticky: false
				});
			},
			onClearQueue: function (a, b) {
				var msg = "Cleared "+b.fileCount+" files from queue";
				$.jGrowl('<p></p>'+msg, {
					theme: 	'warning',
					header: 'Cleared Queue',
					life:	4000,
					sticky: false
				});
			},			
			onComplete: function (a, b ,c, d, e) {
				var size = Math.round(c.size/1024);
				//alert("Successfully uploaded: "+d);
				$.jGrowl('<p></p>'+c.name+' - '+size+'KB', {
					theme: 	'success',
					header: 'Upload Complete',
					life:	4000,
					sticky: false
				});	
			}
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_INFO_PAGE .' :: '. _LBL_IMAGE); ?>
	<form name="myForm" action="" method="post">
	<?= form_input('infoID',$infoID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_IMAGE_FOR ?></td>
			<td width="80%"><label><?= _get_StrFromCondition('sys_Info','Info_For','Info_ID',$infoID); ?></label></td>
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
					echo 	'<td class="label">'. _LBL_NUMBER .'</td>';
					echo 	'<td class="label">'. _LBL_FILENAME .'</td>';
					echo 	'<td class="label" style="text-align:center">'. _LBL_DELETE .'</td>';
					echo '</tr>';
					foreach($arrayData as $detail){
						$i++;
						$ex_url = explode('/',$detail['Image_URL']);
						$count_ex_url = count($ex_url);
						$filename = $ex_url[ $count_ex_url - 1 ];
	
						$imageID = $detail['Image_ID'];
						
						echo '<tr class="contents">';
						echo '<td width="5%" align="center">'.$i.'</td>';
						echo '
							<td width="80%"><label><a href="#" rel="'.$sys_config['includes_path'].'getImageInfo.php?id='.$imageID.'">'.$filename.'</label></a></td>
							';
						echo '<td width="15%" align="center"><a href="#" title="'. _LBL_DELETE_IMAGE .'" onClick="redirectForm(\'infopageImage.php?infoID='.$infoID.'&imageID='.$imageID.'\');">'. _LBL_DELETE .'</a></td>';
						echo '</tr>';
					}
					echo '</table></div>';
				}else{
					echo '<label>'. _LBL_NO_IMAGE .'</label>';
				}
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_UPLOAD .'<br />'. addNote(_LBL_IMAGE_SIZE); ?></td>
			<td><div id="fileUpload1">You have a problem with your javascript or you do not have Flash Player</div></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				//========================================
				$param1 = 'start';
				$param2 = 'Start Upload';
				$param3 = array('type'=>'button','onClick'=>'javascript:$(\'#fileUpload1\').fileUploadStart();');
				echo form_button($param1,$param2,$param3);
				//========================================
				echo '&nbsp;';
				//========================================
				$param1 = 'clear';
				$param2 = 'Clear Queue';
				$param3 = array('type'=>'button','onClick'=>'javascript:$(\'#fileUpload1\').fileUploadClearQueue();');
				echo form_button($param1,$param2,$param3);
				//========================================
				echo '&nbsp;';
				//========================================
				$param1 = 'refresh';
				$param2 = 'Refresh';
				$param3 = array('type'=>'button','onClick'=>'history.go();');
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