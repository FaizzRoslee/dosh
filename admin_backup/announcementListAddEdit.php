<?php
	//****************************************************************/
    // linuxhouse__20220701
	// filename: announcementListAddEdit.php
	// description: add/edit announcement information
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
	include_once $sys_config["includes_path"]."func_date.php";// used by func_ymd2dmy()
	//==========================================================
	func_header("",
				$sys_config['includes_path']."css/global.css,". // css	
				// existing datepicker css
				$sys_config["includes_path"]."jquery/datepicker/css/datepicker.css,". // css
				"",
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."javascript/js_validate.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				
				
				// existing datepicker js
                $sys_config["includes_path"]."jquery/datepicker/js/bootstrap-datepicker.js,". // javascript
                // tinymce
                $sys_config['includes_path']."tinymce/js/tinymce/tinymce.min.js,".// javascript
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
	if(isset($_POST['stateID'])) $stateID = $_POST['stateID'];
	elseif(isset($_GET['stateID'])) $stateID = $_GET['stateID'];
	else $stateID = 0;
	//==========================================================
	if($stateID!=0) $addEdit = _LBL_EDIT;
	else $addEdit = _LBL_ADD;
	//==========================================================
	
	if(isset($_POST['save'])){
		$Title        = isset($_POST['Title'])?$_POST['Title']:'';
		$Description  = isset($_POST['Description'])?$_POST['Description']:'';
		$startingDate = isset($_POST['startingDate'])?$_POST['startingDate']:'';
		$endingDate   = isset($_POST['endingDate'])?$_POST['endingDate']:'';
		$active       = isset($_POST['active'])?$_POST['active']:0;
		//==========================================================
		$updBy = $_SESSION['user']['Usr_ID'];
		//==========================================================
		$param1 = 'tbl_Announcement';
		$param2 = array('AND Title'=>"= ".quote_smart($Title),'AND Announcement_ID'=>" != ".quote_smart($stateID),
						'AND Active'=>'= 1','AND isDeleted'=>'= 0');
		$param3 = '';
		$rowExist = _get_RowExist($param1,$param2,$param3);
		//==========================================================
		if($rowExist!=0){
			echo '
				<script language="Javascript">
				alert("'. _LBL_TITLE_EXIST .'");
				//history.back();
				</script>
				';
		}
		else{
            if($stateID!=0){
                // update data
				$sqlUpd	= "UPDATE tbl_Announcement SET"
						. " Title = ".quote_smart($Title).","
						. " Description = ".quote_smart($Description).","
						
						// convert dmy to ymd
						. " StartingDate = ".quote_smart(func_dmy2ymd($startingDate))."," 
						. " EndingDate = ".quote_smart(func_dmy2ymd($endingDate))."," 

						. " UpdateBy = ".quote_smart($updBy).","
						. " UpdatedAt = NOW()," 
						. " Active = ".quote_smart($active).""
						. " WHERE Announcement_ID = ".quote_smart($stateID)." LIMIT 1";
				$resUpd	= $db->sql_query($sqlUpd,END_TRANSACTION) or die(print_r($db->sql_error()));
				//==========================================================
				$msg = ($resUpd) ? 'Update Successfully' : 'Update Unsuccessfully';
				//==========================================================
            }
            else{
                // insert data
				$sqlIns	= "INSERT INTO tbl_Announcement ("
						. " Title, Description,"
						. " StartingDate, EndingDate,"
						. " RecordBy, CreatedAt,"
						. " Active" 
						. " ) VALUES ("
						. " ".quote_smart($Title).",".quote_smart($Description).","
						
						// convert dmy to ymd
						. " ".quote_smart(func_dmy2ymd($startingDate)).",".quote_smart(func_dmy2ymd($endingDate)).","
						
						. " ".quote_smart($updBy).", NOW(),"
						. " ".quote_smart($active).""
						. " )";
				$resIns	= $db->sql_query($sqlIns,END_TRANSACTION) or die(print_r($db->sql_error()));
				$stateID = $db->sql_nextid();
				//==========================================================
				$msg = ($resIns) ? 'Insert Successfully' : 'Insert Unsuccessfully'; 
				//==========================================================
            }
            // record activity
			//==========================================================
			func_add_audittrail($updBy,$msg.' ['.$stateID.']','Admin-State','tbl_Announcement,'.$stateID);//($recordBy='',$recordDesc='',$recordMod='',$recordOther='')
			echo '
				<script language="Javascript">
				alert("'.$msg.'");
				location.href="announcementList.php";
				</script>
				';
			exit();
			//==========================================================
		}
	}
	// fetch data
	//==========================================================
	$Title = '';
	$Description = '';
    $startingDate = '';
    $endingDate = '';
	$active = 1;
	//==========================================================
	if($stateID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Announcement';
		$dataWhere = array("AND Announcement_ID" => " = ".quote_smart($stateID)."");
		$arrayData = _get_arrayData($dataColumns,$dataTable,'',$dataWhere);
		//==========================================================
		foreach($arrayData as $detail){
			$Title = $detail['Title'];
			$Description = $detail['Description'];
            $startingDate = func_ymd2dmy($detail['StartingDate']);
            $endingDate   = func_ymd2dmy($detail['EndingDate']);
			$active = $detail['Active'];
		}
	}
	//==========================================================
?>
	
	<style>
	.error 	{ float: none; color: red; padding-left: .5em; }
	</style>
	
	
	<script language="Javascript">
	$(document).ready(function() {
		$('a[rel]').each(function() {
			$(this).qtip({
				content: { url: $(this).attr('rel'), title: { text: $(this).attr('name') } },
				position: { corner: { tooltip: 'topMiddle', target: 'bottomMiddle' } },
				style: { name: 'blue', border: { width: 0, radius: 4  }, tip: true, width: { max: 350, min: 0 } }
			});
		});
		/* $("button").removeClass("btn-new-style").button(); */
		
		
		
        // for date pickers
        // what happens if submit button is pressed
		$('#save').on('click', function(){
            // validate input fields
			$('#Title, #Description, #startingDate, #endingDate').closest('td').find('.error').remove();
			
            // declare variables
            var input_value = '';
            var input_value_without_spaces = '';
            
            // get value based on id
			input_value = $('#Title').val();
			
            // remove the spaces using the .replace() method
			input_value_without_spaces = input_value.replace(/\s+/g, '');
			
            // start prevent input field from accepting white spaces as the only input
			if((input_value_without_spaces).length == 0)
				$('#Title').closest('td').append('<span class="error danger">This is required field</span>');

			if(($('#Description').val().replace(/\s+/g, '')).length == 0)
				$('#Description').closest('td').append('<span class="error danger">This is required field</span>');
            // end prevent input field from accepting white spaces as the only input
			
			if(($('#startingDate').val()).length == 0)
				$('#startingDate').closest('td').append('<span class="error danger">This is required field</span>');
			
			if(($('#endingDate').val()).length == 0)
				$('#endingDate').closest('td').append('<span class="error danger">This is required field</span>');
            
            if($('#endingDate').val() < $('#startingDate').val())
                $('#endingDate').closest('td').append('<span class="error danger">Ending date cannot be less than starting date</span>');
			
			if($('span.error').length>0)
				return false;
			else return true;
		});
		
        
        // get date of today
        var dateToday = new Date(); 
        
        
        // datepicker class
		$('.datepicker').datepicker({
			format: 'dd-mm-yyyy',
			autoclose: true,
			todayHighlight: true,
			calendarWeeks: true,
			weekStart: 0,
			startDate: dateToday, // set starting date on calender to date of today
		});
	});


	</script>
	<?php func_window_open2(_LBL_MESSAGE .' :: '. $addEdit); ?>
	<form name="myForm" action="" method="post" autocomplete="off">
	<?= form_input('stateID',$stateID,array('type'=>'hidden')); ?>
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td class="label"><?= _LBL_TITLE ?></td>
			<td><?php
				$param1 = 'Title';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$Title;
				$param3 = array("type"=>"text","style"=>"width:443px");
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_DESCRIPTION ?></td>
			<td><?php
				$param1 = 'Description';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$Description;
				$param3 = array("rows"=>"2","cols"=>"60");
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>
		
		
		
		
<!--        <script>
        tinymce.init({
            selector: '#Description',
            
    /* theme of the editor */
	theme: "modern",
	skin: "lightgray",
	
	/* width and height of the editor */
	width: "100%",
	height: 300,
	
	/* display statusbar */
	statubar: true,
	
	/* plugin */
	plugins: [
		"advlist autolink link image lists charmap print preview hr anchor pagebreak",
		"searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking",
		"save table contextmenu directionality emoticons template paste textcolor"
	],

	/* toolbar */
	toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons | fontselect | fontsizeselect",

    /* font types */
    font_formats:
    "Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago; Oswald=oswald; Symbol=symbol; Tahoma=tahoma,arial,helvetica,sans-serif; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,Arial, Helvetica, sans-serif",
    
    /* font formats */
    fontsize_formats:
    "8pt 8.5pt 8.6pt 8.62pt 9pt",
	
	/* style */
	style_formats: [
		{title: "Headers", items: [
			{title: "Header 1", format: "h1"},
			{title: "Header 2", format: "h2"},
			{title: "Header 3", format: "h3"},
			{title: "Header 4", format: "h4"},
			{title: "Header 5", format: "h5"},
			{title: "Header 6", format: "h6"}
		]},
		{title: "Inline", items: [
			{title: "Bold", icon: "bold", format: "bold"},
			{title: "Italic", icon: "italic", format: "italic"},
			{title: "Underline", icon: "underline", format: "underline"},
			{title: "Strikethrough", icon: "strikethrough", format: "strikethrough"},
			{title: "Superscript", icon: "superscript", format: "superscript"},
			{title: "Subscript", icon: "subscript", format: "subscript"},
			{title: "Code", icon: "code", format: "code"}
		]},
		{title: "Blocks", items: [
			{title: "Paragraph", format: "p"},
			{title: "Blockquote", format: "blockquote"},
			{title: "Div", format: "div"},
			{title: "Pre", format: "pre"}
		]},
		{title: "Alignment", items: [
			{title: "Left", icon: "alignleft", format: "alignleft"},
			{title: "Center", icon: "aligncenter", format: "aligncenter"},
			{title: "Right", icon: "alignright", format: "alignright"},
			{title: "Justify", icon: "alignjustify", format: "alignjustify"}
		]}
	]
        });
        </script>
		<tr class="contents">
			<td class="label"><?= _LBL_DESCRIPTION ?></td>
			<td><?php
				$param1 = 'Description';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$Description;
				$param3 = array("rows"=>"2","cols"=>"60");
				echo form_textarea($param1,$param2,$param3);
			?></td>
		</tr>-->
		
		
		
		
		<tr class="contents">
			<td class="label"><?= _LBL_STARTING_DATE ?></td>
			<td><?php
				$param1 = 'startingDate';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$startingDate;
				$param3 = array('type'=>'text','class' => 'datepicker');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_ENDING_DATE ?></td>
			<td><?php
				$param1 = 'endingDate';
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$endingDate;
				$param3 = array('type'=>'text','class' => 'datepicker');
				echo form_input($param1,$param2,$param3);
			?></td>
		</tr>
		<tr class="contents">
			<td class="label"><?= _LBL_STATUS ?></td>
			<td><?php
				$param1 = 'active';
				$param2 = $arrStatusAktif;
				$param3 = isset($_POST[$param1])?$_POST[$param1]:$active;
				$param4 = '&nbsp;';
				echo form_radio($param1,$param2,$param3,$param4,'');
			?></td>
		</tr>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				$param1 = 'save';
				$param2 = _LBL_SAVE;
				$param3 = array('type'=>'submit');
				echo form_button($param1,$param2,$param3);
				echo '&nbsp;';
				$param1 = 'cancel';
				$param2 = _LBL_CANCEL;
				$param3 = array('type'=>'button','onClick'=>'redirectForm(\'announcementList.php\');');
				echo form_button($param1,$param2,$param3);
			?></td>
		</tr>
	</table>
	<br />
	</form>
