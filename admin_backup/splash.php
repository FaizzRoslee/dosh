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
				//$sys_config['includes_path']."jquery/jHtmlArea/jHtmlArea.css,". // css	
				"", // css	
				// $sys_config['includes_path']."jquery/jHtmlArea/scripts/jquery-1.3.2.min.js,". // javascript
				// $sys_config['includes_path']."jquery/jHtmlArea/scripts/jquery-ui-1.7.2.custom.min.js,". // javascript
				//$sys_config['includes_path']."jquery/jHtmlArea/js/jHtmlArea-0.7.0.js,". // javascript
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
  <script src="/includes/tinymce/js/tinymce/tinymce.min.js"></script>

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
	 
		
		$("#papar").load('/welcome/splash');
		
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
        <link rel="stylesheet" href="/includes/jquery/jquery-upload/css/style.css">
<link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
<link rel="stylesheet" href="/includes/jquery/jquery-upload/css/jquery.fileupload.css">
<link rel="stylesheet" href="/includes/jquery/jquery-upload/css/jquery.fileupload-ui.css">
<noscript><link rel="stylesheet" href="/includes/jquery/jquery-upload/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="/includes/jquery/jquery-upload/css/jquery.fileupload-ui-noscript.css"></noscript>
<?php
    if ($infoID == 30){
        $link = '/includes/jquery/jquery-upload/server/php/index.php';
    }
    else if ($infoID == 10){
        $link = '/includes/jquery/jquery-upload/server/php/contectus.php';
    }
    else if ($infoID == 1){
        $link = '/includes/jquery/jquery-upload/server/php/home.php';
    }
    else if ($infoID == 2){
        $link = '/includes/jquery/jquery-upload/server/php/login.php';
    }
    else if ($infoID == 20){
        $link = '/includes/jquery/jquery-upload/server/php/contectus.php';
    }
?>
	<?php func_window_open2(_LBL_INFO_PAGE .' :: '. $addEdit); ?>
 
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_INFO_FOR ?></td>
			<td width="80%"><label><?= (($infoFor!='')?$infoFor:'-') ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_IMAGE ?></td>
                        <td>
     
        
       
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
   
		</tr>


                        </td>
		</tr>
                    
		<tr class="contents">
			 
			 
			<td width="20%" class="label">Main Page Banner</td>
			<td width="80%">
				<div id="papar"></div>
		 
            </td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				//========================================
				
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
	
        
  
 
<!-- The template to display files available for download -->
 
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="/includes/jquery/jquery-upload/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="/includes/jquery/jquery-upload/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="/includes/jquery/jquery-upload/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="/includes/jquery/jquery-upload/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="/includes/jquery/jquery-upload/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="/includes/jquery/jquery-upload/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="/includes/jquery/jquery-upload/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="/includes/jquery/jquery-upload/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="/includes/jquery/jquery-upload/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script>


    
    
</script>