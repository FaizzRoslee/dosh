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
<link rel="stylesheet" href="/includes/jquery/jquery-upload/css/style.css">
<link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
<link rel="stylesheet" href="/includes/jquery/jquery-upload/css/jquery.fileupload.css">
<link rel="stylesheet" href="/includes/jquery/jquery-upload/css/jquery.fileupload-ui.css">
<noscript><link rel="stylesheet" href="/includes/jquery/jquery-upload/css/jquery.fileupload-noscript.css"></noscript>
<noscript><link rel="stylesheet" href="/includes/jquery/jquery-upload/css/jquery.fileupload-ui-noscript.css"></noscript>

	<?php func_window_open2(_LBL_INFO_PAGE .' :: '. _LBL_VIEW); ?>
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
<form id="fileupload" action="<?=$link?>" method="POST" enctype="multipart/form-data">

	<?= form_input('infoID',$infoID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_IMAGE_FOR ?></td>
			<td width="80%"><label><?= (($infoFor!='')?$infoFor:'-') ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label">
                        <td>
                            
                             <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel upload</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle">
                <!-- The global file processing state -->
                <span class="fileupload-process"></span>
            </div>
            <!-- The global progress state -->
            <div class="col-lg-5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress state -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" border="0" width="100%" cellpadding="3" cellspacing="1"   class="table table-striped "><tbody class="files"></tbody></table>
                            
                        </td>
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
			
			    if($infoID == 30){
			    $param1 = 'edit';
				$param2 = _LBL_EDIT;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'infopageAddEditnew.php?infoID='. $infoID .'\');');
				echo form_button($param1,$param2,$param3);	
			    }
				else {
				//========================================
				$param1 = 'edit';
				$param2 = _LBL_EDIT;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'infopageAddEdit.php?infoID='. $infoID .'\');');
				echo form_button($param1,$param2,$param3);
				}
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

<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td align="center">
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
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
<script>


$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: '<?=$link?>'
    });

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );
   
        // Load existing files:
        $('#fileupload').addClass('fileupload-processing');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: $('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: $('#fileupload')[0]
        }).always(function () {
            $(this).removeClass('fileupload-processing');
        }).done(function (result) {
            $(this).fileupload('option', 'done')
                .call(this, $.Event('done'), {result: result});
        });
    

});
    
    
</script>