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
	 
		
		$("#papar").load('/welcome/bana');
		
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
	<form id="fileupload" action="<?=$link?>" method="POST" enctype="multipart/form-data">

	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_INFO_FOR ?></td>
			<td width="80%"><label><?= (($infoFor!='')?$infoFor:'-') ?></label></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_IMAGE ?></td>
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
        
       
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
        </form>
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
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'infopageView.php?infoID='.$infoID.'\');');
				echo form_button($param1,$param2,$param3);
				//========================================
			?></td>
		</tr>
	</table>
	<br />
	
        
      <script>tinymce.init({
  selector: 'textarea',
  height: 500,
  theme: 'modern',
  file_picker_types : 'image',
  plugins: [
    'advlist autolink lists link image charmap print preview hr anchor pagebreak',
    'searchreplace wordcount visualblocks visualchars code fullscreen',
    'insertdatetime media nonbreaking save table contextmenu directionality',
    'emoticons template paste textcolor colorpicker textpattern imagetools codesample toc help'
  ],
  toolbar1: 'undo redo image | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
  toolbar2: 'print preview media | forecolor backcolor emoticons | codesample help',
  images_upload_base_path : '/upload/',
  image_advtab: true,
  templates: [
    { title: 'Test template 1', content: 'Test 1' },
    { title: 'Test template 2', content: 'Test 2' }
  ],
  content_css: [
    '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
    '//www.tinymce.com/css/codepen.min.css'
  ]
 });</script>
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
        <td>
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
<!-- The main application script -->
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