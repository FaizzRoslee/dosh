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
	//==========================================================
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'faq.php';
	
	func_header("",
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.css,". // css
				$sys_config['includes_path']."jquery/css/demos.css,". // css
				"", // css
				$sys_config['includes_path']."jquery/jquery.totop/ui.totop.js,". // javascript
				$sys_config['includes_path']."jquery/Others/jquery.multi-open-accordion-1.5.3.js,". // javascript
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
	jQuery(document).ready(function(){
	
		
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
	});
	</script>
	<?php func_window_open2(_LBL_FREQUENTLY_ASK_Q .' ('. _LBL_FAQ .')'); ?>
	<form>
	<center>
	<div id="users-contain" class="ui-widget" style="width:95%;text-align:left;">
		<?php
			echo '<div id="multiOpenAccordion">';
			$l = $_SESSION['lang'];
			foreach($arr_faq as $index => $faq){
				if(is_array($faq) && isset($faq[ $l ][0]) && isset($faq[ $l ][1])){
					// echo '<div id="accordion'. $index .'" class="cl_accordion">';
					echo '<h3><a href="#">Q :'. $faq[ $l ][0] .'</a></h3>';
					echo '<div><p>A :'. $faq[ $l ][1] .'</p></div>';
					// echo '</div>';
				}
			}
			echo '</div>';
		?>
	</div>
	</center>
	<br />
	<br />
	</form>
	<?php func_window_close2(); ?>