<?php
	//****************************************************************/
	// filename: chemicalList.php
	// description: list of the chemical
	//***************************************************************/
	include_once '../includes/sys_config.php';
	//==========================================================
	/*
	if (!defined("_VALID_ACCESS"))
	{	// denied direct access to the page
		header("HTTP/1.0 404 Not Found");
		exit();	// process terminated
	}
	*/
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
	include_once $sys_config['includes_path'].'faq.php';
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
				$sys_config['includes_path']."jquery/Others/jquery.multi-open-accordion-1.5.3.js,". // javascript
				$sys_config['includes_path']."Javascript/js_common.js,". // javascript
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
	
?>
	<script language="Javascript">
	var asInitVals = new Array();
	
	jQuery(document).ready(function(){
	
		var new_width = $(document).width() * 0.9;
		$("#demo, #container").css({ 'width': new_width + 'px', 'margin': '5px auto', 'padding':'0' });
	
		oTable = $('#example').dataTable( {
			"bSortClasses": true,
			"bJQueryUI": true,
			"bPaginate": true,
			"sPaginationType": "full_numbers",
			"oLanguage": { <?php include $sys_config['languages_path']."dataTablesLanguage.php" ?> },
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": "faq-get.php?levelID=<?= $levelID ?>&Usr_ID=<?= $Usr_ID ?>",
			
			"aoColumns": [
				{ "sName": " ", "bSortable": false, "bSearchable": false, "sClass": "center" },
				{ "sName": "Faq_Q_<?= $lang ?>" },
				{ "sName": "Faq_A_<?= $lang ?>" },
				{ "sName": " ", "sClass": "center" },
				{ "sName": " ", "bSortable": false, "bSearchable": false }
				
 			],
			"aaSorting": [[ 2, 'asc' ]]
			// ,"fnDrawCallback": function ( oSettings ) {
				// var page = Math.ceil(oSettings._iDisplayStart / oSettings._iDisplayLength); 
				// for ( var i=0, iLen = oSettings.aiDisplay.length; i<iLen ; i++ ) {
					// this.fnUpdate( (i+1)+(oSettings._iDisplayLength * page), oSettings.aiDisplay[i], 1, false, false );
				// }
			// }
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
		
		var icons = {
			header: "ui-icon-circle-arrow-e",
			headerSelected: "ui-icon-circle-arrow-s"
		};
		
		/*
		$( ".cl_accordion" ).each(function(){
			var id = $(this).attr('id');
			$( '#'+id  ).accordion({
				icons: icons,
				collapsible: true,
				autoHeight: false,
				active: false
			});
        });
		*/
		$('#multiOpenAccordion').multiOpenAccordion({
			active: false
		});
		
		$().UItoTop({ easingType: 'easeOutQuart' });
		
		$("#delete").live("click", function(){
			if( $("input.chk-list:checked").length ){
				$.ajax({
					url: 'support-ajax.php',
					type : 'POST',
					dataType : 'json',
					data: 'fn=faq&ftype=delete&' + $('#myForm').serialize(), 
					success: function( msg ) {
						alert( msg.result );
						oTable.fnClearTable(0);
						oTable.fnDraw();
					},
					error: function(xhr, ajaxOptions, thrownError){
						alert(xhr.statusText);
						alert(thrownError);
					}					
				});
			}
			// alert( $("input.chk-list:checked").length );
			// alert( $(".chk-list").length );
			return false;
		});
		/* $("button").removeClass("btn-new-style").button(); */
	});
	</script>
	<?php func_window_open2(_LBL_SUPPORT .' :: '. _LBL_FREQUENTLY_ASK_Q .' ('. _LBL_FAQ .')'); ?>
	<form name="myForm" id="myForm" action="" method="post">
	<?= form_input("Usr_ID",$Usr_ID,array('type'=>'hidden')) ?>
	
	<?php if( $levelID<6 ){ ?>
	<div id="myTab">
		<ul>
			<li><a href="#tabs-1"><?= _LBL_LIST ?></a></li>
			<li><a href="#tabs-2"><?= _LBL_FAQ ?></a></li>
		</ul>
		
		
		<div id="tabs-1">
		
			<div id="demo">
				<div id="container">
					<div class="demo_jui">
						<?php if( $levelID<5 ){ ?>
						<table width="100%" class="ui-widget">
							<tr>
								<td width="60%">&nbsp;</td>
								<td width="40%" align="right"><?php 
									echo form_button('add',_LBL_ADD, array('type'=>'button','onClick'=>'redirectForm(\'faq-viewedit.php\')'));
									echo '&nbsp;';
									echo form_button('delete',_LBL_DELETE, array('type'=>'button'));
								?></td>
							</tr>
						</table>
						<?php } ?>
						<table id="example" class="display" cellpadding="0" cellspacing="0" border="0">
						
							<thead>
							<tr>
								<th width="3%" class="center"><?= '' ?></th>
								<th width="35%" class="center"><?= _LBL_QUESTION ?></th>
								<th width="50%" class="center"><?= _LBL_ANSWER ?></th>
								<th width="5%" class="center"><?= _LBL_ACTIVE ?></th>
								<th width="7%"><?= _LBL_ACTIVITY ?></th>
							</tr>
							</thead>

							
							<?php $arrHidden = array('type'=>'hidden','class'=>'search_init'); ?>
							<?php $arrText = array('type'=>'text','class'=>'search_init'); ?>
							
							<tfoot>
							<tr>
								<th><?= form_input('search_0','',$arrHidden);?></th>
								<th><?= form_input('search_1',_LBL_QUESTION,$arrText);?></th>
								<th><?= form_input('search_2',_LBL_ANSWER,$arrText);?></th>
								<th><?= form_input('search_3',_LBL_ACTIVE,$arrText);?></th>
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

		<?php
			$dCols 	= 'Faq_Q_'. $lang .',Faq_A_'. $lang;
			$dTable = 'tbl_Faq';
			$dJoin 	= '';
			$dWhere = array("AND isActive" => "= 1","AND isDeleted" => "= 0");
			$arrayData = _get_arrayData($dCols,$dTable,$dJoin,$dWhere); 

			
			echo '<div id="multiOpenAccordion">';
			$l = $_SESSION['lang'];
			/*
			foreach($arr_faq as $index => $faq){
				if(is_array($faq) && isset($faq[ $l ][0]) && isset($faq[ $l ][1])){
					// echo '<div id="accordion'. $index .'" class="cl_accordion">';
					echo '<h3><a href="#">Q :'. $faq[ $l ][0] .'</a></h3>';
					echo '<div><p>A :'. $faq[ $l ][1] .'</p></div>';
					// echo '</div>';
				}
			*/
			foreach($arrayData as $index){
				if( !empty($index[ 'Faq_Q_'. $lang ]) ){
					echo '<h3><a href="#">Q :'. $index[ 'Faq_Q_'. $lang ] .'</a></h3>';
					echo '<div><p>A :'. $index[ 'Faq_A_'. $lang ] .'</p></div>';
				}
			}
			echo '</div>';
		?>
			</div>
	
	<?php if($levelID<5){ ?>
		</div>
		
	</div>
	<?php } ?>
	<br />
	<br />
	</form>
	<?php func_window_close2(); ?>