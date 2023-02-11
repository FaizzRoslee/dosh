<?php 
	//****************************************************************/
	// filename: empList.php
	// description: list of the employee
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
	if(isset($_POST['searchBy'])) $searchBy = $_POST['searchBy'];
	// elseif(isset($_GET['searchBy'])) $searchBy = $_GET['searchBy'];
	else $searchBy = 0;
	//==========================================================
	func_header(_LBL_SEARCH_REG_CHEM,
				$sys_config['includes_path']."jquery/DataTables/css/media.dataTable.css,". // css
				$sys_config['includes_path']."css/global.css,". // css
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.css,". // css
				"", // css
				// $sys_config['includes_path']."jquery/DataTables/js/jquery.dataTables.min.js,". // javascript
				$sys_config['includes_path']."jquery/DataTables/js/1.7.6/jquery.dataTables.min.js,". // javascript
				$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
				// $sys_config['includes_path']."jquery/Others/jquery.tooltip.js,". // javascript
				"",
				true, // menu
				false, // portlet
				true, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false // int top for [enter]
				);
	//==========================================================
	if(isset($_POST['searchWord'])) $searchWord = mysqli_real_escape_string($db->db_connect_id, $_POST['searchWord']);
	// elseif(isset($_GET['searchWord'])) $searchWord = mysqli_real_escape_string($db->db_connect_id, $_GET['searchWord']);
	else $searchWord = '';
	//==========================================================
?>
	<style>
/* 	#tooltip{
		position: absolute;
		display: none;
		border: 1px solid #333;
		background: #BDCDFF;
		padding: 5px 20px;
		color: #333;
		
		border-radius: 5px;
		-webkit-border-radius: 5px;
		-moz-border-radius: 5px;
	} */
	</style>
	<script type="text/javascript" charset="utf-8">
	jQuery(document).ready(function() {
		var oTable;
		$('#container, span.infosyn').hide();
		$('[title]').tooltip();
		
		function fnFormatDetails ( oTable, nTr ) {
			var aData = oTable.fnGetData( nTr );
			var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
			sOut += '<tr><td><strong><?= _LBL_SYNONYM ?></strong>:</td><td>'+ aData[6] +'</td></tr>';
			sOut += '</table>';
			
			return sOut;
		}
		
		$("#spinner-load").hide();
		$("#go").click(function(){
			if($('#searchWord').val() != ''){
				$('#container').show();
				$("#spinner-load").show();
			}
			else $('#container').hide();
				
			$.ajax({
				url: '<?= $sys_config['includes_path'] ?>info-ajax.php',
				type : 'POST',
				dataType : 'json',
				data: $('#myForm').serializeArray(), 
				success: function( msg ) {
					$("#spinner-load").hide();
					// alert( msg.sql );
					$("#container").html('');
					$("#container").append( msg.result );
					$('span.infosyn').hide();
					fn_oTable( msg.collapsetrue );
				},
				error: function(xhr, ajaxOptions, thrownError){
					alert(xhr.statusText);
					alert(thrownError);
				} 
			});
			
			return false;
		}).trigger('click');
		
		$().UItoTop({ easingType: 'easeOutQuart' });
		
		/* $("button").removeClass("btn-new-style").button(); */
		
		if($.browser.msie && $.browser.version <= 9){
			func_placeholder();
		}
	} );
	
	function fn_oTable( collapsetrue ){
		
		var nCloneTh = document.createElement( 'th' );
		var nCloneTd = document.createElement( 'td' );
		nCloneTd.innerHTML = '<img src="<?= $sys_config['includes_path'] ?>jquery/DataTables/images/details_open.png" title="<?= _LBL_SYNONYM ?>">';
		nCloneTd.className = "center";
		
		$('#example thead tr').each( function () {
			this.insertBefore( nCloneTh, this.childNodes[0] );
		} );
		
		$('#example tbody tr').each( function () {
			this.insertBefore(  nCloneTd.cloneNode( true ), this.childNodes[0] );
		} );

		$('#example').dataTable().fnDestroy();
		
		oTable = $('#example').dataTable( {
			"oLanguage": {  <?php include $sys_config['languages_path']."dataTablesLanguage.php" ?>  },
			"sScrollY": "700px",
			// "bScrollCollapse": collapsetrue,
			"bScrollCollapse": true,
			"bPaginate": false,
			"bJQueryUI": true,
			"aoColumnDefs": [
				{ "aTargets": [ 4,5 ], "bSortable": false },
				{ "aTargets": [ 0 ], "bSortable": false, "bWidth":"10px" },
				{ "aTargets": [ 6 ], "bVisible": false, "bSortable": false, "bSearchable":false }
			],
			"bSortClasses": true,
			"fnDrawCallback": function (oSettings) {
				$('[title]').tooltip({ html: true });
			},
			"bFilter": false,
			"sDom": '<"H"i><rt><"F"i>'
		});
		
		$('#example tbody td img').on('click', function () {
			var nTr = this.parentNode.parentNode;
			
			// console.log(oTable.fnGetData( nTr )[6]);

			if ( this.src.match('details_close') ) {
				this.src = "<?= $sys_config['includes_path'] ?>jquery/DataTables/images/details_open.png";
				oTable.fnClose( nTr );
			} else {
				var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
				sOut += '<tr><td><strong><?= _LBL_SYNONYM ?></strong>:</td><td>'+ oTable.fnGetData( nTr )[6] +'</td></tr>';
				sOut += '</table>';
				
				this.src = "<?= $sys_config['includes_path'] ?>jquery/DataTables/images/details_close.png";
				// oTable.fnOpen( nTr, fnFormatDetails(oTable, nTr), 'details' );
				oTable.fnOpen( nTr, sOut, 'details' );
			}
		} );
		
		$('#example tbody tr').each( function() {
			var sTitle;
			// var nTds = $('td', this);
			// var sSynonims = $(nTds[6]).text();
			var sSynonims = $(this).find('span:first').text();
			
			sTitle =  '<?= _LBL_SYNONYM ?>:<br />' + sSynonims;
			
			// this.setAttribute( 'title', sTitle );
			
			if(($.browser.msie && $.browser.version > 9) || !$.browser.msie){
				this.setAttribute( 'data-toggle', "popover" );
				this.setAttribute( 'data-placement', "top" );
				this.setAttribute( 'data-html', true );
				this.setAttribute( 'data-content', sTitle );
			}
			
			// $(this).closest('tr[title]').tooltip({ html: true });
		} );
		
		if(($.browser.msie && $.browser.version > 9) || !$.browser.msie){
			$('#example tbody').on('mouseenter', 'tr', function() {
				$(this).popover('show');
			});
			$('#example tbody').on('mouseleave', 'tr', function() {
				$(this).popover('hide');    
			});
		}
		
		/* Apply the tooltips */
/* 		$('#example tbody tr[title]').tooltip( {
			"delay": 0,
			"track": true,
			"fade": 250
		} ); */

		$(".cl_view").on('click', function(){
			var id = $(this).attr('rel').split('-')[1];
			$("#chemicalID").val(id);
			$("#myForm").submit();
		});
	}
	(function($) { 
		func_placeholder = function(){
			$('input[placeholder]').each(function () {
				var obj = $(this);

				if (obj.attr('placeholder') != '') {
					obj.addClass('IePlaceHolder');
					if (($.trim(obj.val()) == '' || $.trim(obj.val()) != obj.attr('placeholder')) && obj.attr('type') != 'password') {
						obj.val(obj.attr('placeholder'));
					}
				}
			});

			$('.IePlaceHolder').on('focus', function () {
				var obj = $(this);
				if (obj.val() == obj.attr('placeholder')) {
					obj.val('');
				}
			});

			$('.IePlaceHolder').on('blur', function () {
				var obj = $(this);
				if ($.trim(obj.val()) == '') {
					obj.val(obj.attr('placeholder'));
				}
			});
			
			$('[placeholder]').closest('form').submit(function() {
				$(this).find('[placeholder]').each(function() {
					var input = $(this);
					if (input.val() == input.attr('placeholder')) {
						input.val('');
					}
				})
			});
		}
	})(jQuery);
	</script>
	<center>
	<?php func_window_open2(_LBL_SEARCH_REG_CHEM,'75%'); ?>
    <form name="myForm" id="myForm" action="chemicalView_Search.php" method="post">
	<?= form_input('chemicalID','',array('type'=>'hidden')); ?>
	<div class="container">
		<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
			<tr class="contents">
				<td width="20%" class="label" style="vertical-align:middle"><?= _LBL_SEARCH ?></td>
				<td width="80%"><?php
					//=======================================
					$param1 = 'searchWord';
					$param2 = isset($_POST[$param1])?$_POST[$param1]:$searchWord;
					$param3 = array('type'=>'text','style'=>'width:50%;','title'=>_LBL_CAN_BE_3,'placeholder'=>_LBL_CAN_BE_3);
					echo form_input($param1,$param2,$param3);
					//=======================================
					echo '&nbsp;';
					//=======================================
					$param1 = 'go';
					$param2 = _LBL_GO;
					$param3 = array('type'=>'submit');
					echo form_button($param1,$param2,$param3);
					//=======================================
					echo '&nbsp;';
					echo '<img src="'. $sys_config['images_path'] .'spinner.gif" width="24" height="24" id="spinner-load" />';
				?></td>
			</tr>
		</table>
	</div>
	<br />
	<div class="container">
		<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
			<tr class="contents">
				<td align="center"><?php
					//=======================================
					$param1 = 'cancel';
					$param2 = _LBL_BACK;
					$param3 = array('type'=>'button','onClick'=>"location.href='".$sys_config['document_root']."index.php'");
					echo form_button($param1,$param2,$param3);
					//=======================================
				?></td>
			</tr>
		</table>
	</div>
	<br />
	<div id="container" class="container"></div>
    </form>
	<br />
	<?php func_window_close2(); ?>
	</center>