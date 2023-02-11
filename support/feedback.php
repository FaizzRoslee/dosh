<?php
	//****************************************************************/
	// filename: renewList.php
	// description: list of the renew submission
	//***************************************************************/
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
	$lang = $_SESSION['lang'];
	//==========================================================	
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'formElement.php';
	include_once $sys_config['includes_path'].'arrayCommon.php';
	//==========================================================
	$Usr_ID = $_SESSION['user']['Usr_ID'];
	$levelID = $_SESSION['user']['Level_ID'];
	// if($levelID==5) exit();
	//==========================================================
	func_header("",
				$sys_config['includes_path']."jquery/DataTables/css/media.dataTable.css,". // css
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.css,". // css
				$sys_config['includes_path']."jquery/css/demos.css,". // css
				"", // css
				// $sys_config['includes_path']."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				// $sys_config['includes_path']."jquery/DataTables/FixedHeader/FixedHeader.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/js/1.7.6/jquery.dataTables.min.js,". // javascript
				$sys_config["includes_path"]."jquery/DataTables/FixedHeader-2.0.4/fixedHeader.dataTables.js,". // javascript
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
				// $sys_config['includes_path']."jquery/Others/jquery.multi-open-accordion-1.5.3.js,". // javascript
				$sys_config['includes_path']."Javascript/js_common.js,". // javascript
				// $sys_config['includes_path']."jquery/Others/jquery.validate.js,". // javascript
				$sys_config["includes_path"]."jquery/jquery.validation.1.8.1/jquery.validate.js,". // javascript
				$sys_config["includes_path"]."jquery/Tabbed/jquery-ui-tabs.js,". // javascript
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
	$spLoad = '<span class="sp-load"> <img src="'. $sys_config['images_path'] .'ui-anim_basic_16x16.gif"> </span>';
?>
	<style>
	.left { text-align:left; padding: 5px; }
	label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
	</style>
	<script type="text/javascript" charset="utf-8">
	var asInitVals = new Array();
	var gaiSelected =  [];
	$(document).ready(function() {
		$().UItoTop({ easingType: 'easeOutQuart' });
	
		var new_width = $(document).width() * 0.9;
		$("#demo, #container").css({ 'width': new_width + 'px', 'margin': '5px auto', 'padding':'0' });
		
		/* $("button").removeClass("btn-new-style").button(); */
		<?php if( $levelID<6 ){ ?>
		oTable = $('#example').dataTable( {
			"bSortClasses": true,
			"bJQueryUI": true,
			"bPaginate": true,
			"sPaginationType": "full_numbers",
			"oLanguage": { <?php include $sys_config['languages_path']."dataTablesLanguage.php" ?> },
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "feedback-get.php?levelID=<?= $levelID ?>&Usr_ID=<?= $Usr_ID ?>",
			
			"aoColumns": [
				{ "sName": " ", "bSortable": false, "bSearchable": false },
				{ "sName": " ", "bSortable": false, "bSearchable": false, "sClass": "center" },
				{ "sName": "Feedback_Name" },
				// { "sName": "Feedback_Contact" },
				// { "sName": "Feedback_Email" },
				{ "sName": "Type_Name_<?= $lang ?>" },
				{ "sName": "Feedback_Subject" },
				{ "sName": "IF(Status = 2, '<?= _LBL_REPLIED ?>', '<?= _LBL_NEW ?>')" },
				{ "sName": "RecordDate" },
				{ "sName": " ", "bSortable": false, "bSearchable": false }
				
 			],
			"aaSorting": [[ 1, 'asc' ],[ 2, 'asc' ]]
			,"fnDrawCallback": function ( oSettings ) {
				var page = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength); 
				for ( var i=0, iLen = oSettings.aiDisplay.length; i<iLen ; i++ ) {
					this.fnUpdate( (i+1)+(oSettings._iDisplayLength * page), oSettings.aiDisplay[i], 1, false, false );
				}
			}
		});
		// new FixedHeader( oTable );
		
		$("tfoot input").keyup( function () {
			/* Filter on the column (the index) of this element */
			oTable.fnFilter( this.value, $("tfoot input").index(this) );
		}).each( function (i) {
			asInitVals[i] = this.value;
		}).focus( function () {
			if ( this.className == "search_init" ) {
				this.className = "";
				this.value = "";
			}
		}).blur( function (i) {
			if ( this.value == "" ) {
				this.className = "search_init";
				this.value = asInitVals[$("tfoot input").index(this)];
			}
		});
		$("#myTab").tabs();

		$("input[name=chckAll]").on("click",function(){
			$(".chk-list").prop("checked",$(this).is(":checked") );
			disabledDelete();			
		});
		
		$(".chk-list").live("click",function(){
			disabledDelete();			
		});
				
		$("#delete").on("click",function(){
			r = confirm("<?= _LBL_RECORD_DELETE_CONFIRMATION ?>");
			if(r==true){
				$.ajax({
					url: "support-ajax.php",
					type : "POST",
					dataType : "json",
					data: "fn=deletefeedback&id="+ $("#deleteid").val(), 
					success: function( msg ) {
						alert( msg.result );
						$("#delete").button("disable");
						$("#deleteid").val("");
						oTable.fnClearTable(0);
						oTable.fnDraw(); 
					},
					error: function(xhr, ajaxOptions, thrownError){
						alert(xhr.statusText);
						alert(thrownError);
					}
				});
			}
		}).button("disable");
		<?php } ?>
		
		$("#fCategory").change(function(){
			$("input[name=fCategory-label]").val( $(this).find("option:selected").text() );
		});
		
		$(".sp-load").hide();
		
		$("#send").on("click",function(){
			$(this)
				.button("disable")
				.find(".sp-load").show()
				;
			$("#myForm").submit();
			if( $("label.error:visible").length > 0 ){
				$(this)
					.button("enable")
					.find(".sp-load").hide()
					;
				
			}
		});
		
		$("#myForm").validate({
			rules: {
				fName: "required",
				fContact: "required",
				fEmail: {
					required: true,
					email: true
				},
				fCategory: "required",
				fSubject: "required",
				fMessage: "required"
			},
			messages: {
				fName: "<?= _LBL_REQUIRED_FIELD ?>",
				fContact: "<?= _LBL_REQUIRED_FIELD ?>",
				fEmail: {
					required: "<?= _LBL_REQUIRED_FIELD ?>",
					email: "<?= _LBL_NOTVALID_EMAIL ?>"
				},
				fCategory: "<?= _LBL_REQUIRED_FIELD ?>",
				fSubject: "<?= _LBL_REQUIRED_FIELD ?>",
				fMessage: "<?= _LBL_REQUIRED_FIELD ?>"
			},
			submitHandler: function(form) {
				// return false
				$.ajax({
					url: 'support-ajax.php',
					type : 'POST',
					dataType : 'json',
					data: $('#myForm').serializeArray(), 
					success: function( msg ) {
						alert( msg.result );
						$("#send").button("enable").find(".sp-load").hide();
						$("#cancel").trigger("click");
					},
					error: function(xhr, ajaxOptions, thrownError){
						alert(xhr.statusText);
						alert(thrownError);
					}
				});
				return false;
			}
		
		});
	
	});	
	(function($) {
		disabledDelete = function(){
			// alert("masuk");
			$("#delete").button("disable");
			if( $(".chk-list:checked").length > 0)
				$("#delete").button("enable");
			
			var id = "";
			$(".chk-list:checked").each(function(){
				if(id!="") id += ","; 
				id += $(this).val() 
			});
			$("#deleteid").val( id );
		};
	})(jQuery);
	</script>
	<?php func_window_open2(_LBL_SUPPORT .' :: '. _LBL_FEEDBACK); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<input type="hidden" name="fn" value="feedback" />
	<input type="hidden" name="ftype" value="add" />
	<input type="hidden" name="Usr_ID" value="<?= $Usr_ID ?>" />
	<input type="hidden" name="fCategory-label" value="" />
	<input type="hidden" id="deleteid" name="deleteid" value="" />
	
	<?php if( $levelID<6 ){ ?>
	<div id="myTab">
		<ul>
			<li><a href="#tabs-1"><?= _LBL_LIST ?></a></li>
			<li><a href="#tabs-2"><?= _LBL_FEEDBACK_FROM ?></a></li>
		</ul>
		
		
		<div id="tabs-1">
		
			<div id="demo">
				<div id="container">
					<div class="demo_jui">
						<table style="width:100%">
							<tr>
								<td width="10%">&nbsp;</td>
								<td width="90%" align="right"><?php
									echo form_button("delete",_LBL_DELETE,array("type"=>"button"));
								?></td>
							</tr>
						</table>
						<table id="example" class="display" cellpadding="0" cellspacing="0" border="0">
						
							<thead>
							<tr>
								<th width="3%" class="center"><input type="checkbox" name="chckAll" /></th>
								<th width="3%" class="center"><?= '' ?></th>
								<th width="15%" class="center"><?= _LBL_NAME ?></th>
								<?php /*
								<th width="10%" class="center"><?= _LBL_CONTACT_NO ?></th>
								<th width="15%" class="center"><?= _LBL_EMAIL ?></th>
								*/ ?>
								<th width="15%" class="center"><?= _LBL_TYPE ?></th>
								<th width="25%" class="center"><?= _LBL_SUBJECT ?></th>
								<th width="10%" class="center"><?= _LBL_STATUS ?></th>
								<th width="10%" class="center"><?= _LBL_DATE ?></th>
								<th width="7%"><?= _LBL_ACTIVITY ?></th>
							</tr>
							</thead>

							
							<?php $arrHidden = array('type'=>'hidden','class'=>'search_init'); ?>
							<?php $arrText = array('type'=>'text','class'=>'search_init'); ?>
							
							<tfoot>
							<tr>
								<th><?= form_input('search_0','',$arrHidden);?></th>
								<th><?= form_input('search_1',_LBL_NAME,$arrText);?></th>
								<?php /*
								<th><?= form_input('search_2',_LBL_CONTACT_NO,$arrText);?></th>
								<th><?= form_input('search_3',_LBL_EMAIL,$arrText);?></th>
								*/ ?>
								<th><?= form_input('search_4',_LBL_TYPE,$arrText);?></th>
								<th><?= form_input('search_5',_LBL_SUBJECT,$arrText);?></th>
								<th><?= form_input('search_10',_LBL_STATUS,$arrText);?></th>
								<th><?= form_input('search_6',_LBL_DATE,$arrText);?></th>
								<th><?= form_input('search_9',_LBL_ACTIVITY,$arrHidden);?></th>
							</tr>
							</tfoot>
							
							
						</table>
					</div>
				</div>
			</div>
			
		</div>
		
		<div id="tabs-2">
		<?php } ?>
		
			<div id="users-contain" class="ui-widget">
			<table class="ui-widget ui-widget-content">
			<caption class="ui-widget-header ui-corner-top left"><?= _LBL_FEEDBACK_FROM ?></caption>
			<tbody>
				<tr>
					<td class="ui-widget-header" width="20%"><?= _LBL_NAME ?></td>
					<td width="80%"><?= form_input('fName','',array('style'=>'width:50%;')); ?></td>
				</tr>
				<tr>
					<td class="ui-widget-header"><?= _LBL_CONTACT_NO ?></td>
					<td><?= form_input('fContact','',array('style'=>'width:50%;')); ?></td>
				</tr>
				<tr>
					<td class="ui-widget-header"><?= _LBL_EMAIL ?></td>
					<td><?= form_input('fEmail','',array('style'=>'width:50%;')); ?></td>
				</tr>
				<tr>
					<td class="ui-widget-header"><?= _LBL_TYPE ?></td>
					<td><?php
						$param1 = 'fCategory';
						$param2 = '';
						$param3 = _get_arraySelect('tbl_Feedback_Type','Type_ID','Type_Name_'.$lang,array('AND isActive'=>'= 1'),'Type_ID'); // $arrFeedback;
						$param4 = '';
						$param5 = '';
						echo form_select($param1,$param2,$param3,$param4,$param5);
					?></td>
				</tr>
				<tr>
					<td class="ui-widget-header"><?= _LBL_SUBJECT ?></td>
					<td><?= form_input('fSubject','',array('style'=>'width:100%;')); ?></td>
				</tr>
				<tr>
					<td class="ui-widget-header"><?= _LBL_MESSAGE ?></td>
					<td><?php 
						$param1 = 'fMessage';
						$param2 = '';
						$param3 = array('style'=>'width:100%;','rows'=>'5');
						echo form_textarea($param1,$param2,$param3);
					?></td>
				</tr>
			</tbody>
			</table>
			<table class="ui-widget ui-widget-content">
				<tr>
					<td style="text-align:center;"><?php
						echo form_button('send',_LBL_SAVE . $spLoad,array("type"=>"button"));
						echo '&nbsp;';
						echo form_button('cancel',_LBL_CANCEL,array('type'=>'reset'));
					?></td>
				</tr>
			</table>
			</div>
	
	<?php if($levelID<5){ ?>
		</div>
		
	</div>
	<?php } ?>
	<br />
	<br />
	</form>
	<?php func_window_close2(); ?>