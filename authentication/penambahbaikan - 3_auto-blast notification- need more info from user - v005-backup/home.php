<?php 
	//****************************************************************/
	// filename: home.php
	// description: home page
	//****************************************************************/
	include_once "../includes/sys_config.php";
	//==========================================================
	if(isset($_SESSION["lang"])){ $fileLang = $_SESSION["lang"].".php"; }
	else{ $fileLang = "eng.php"; }
	include_once $sys_config["languages_path"].$fileLang;
	//==========================================================
	include_once $sys_config["includes_path"]."db_config.php";
	include_once $sys_config["includes_path"]."func_master.php";
	include_once $sys_config["includes_path"]."func_header.php";
	include_once $sys_config["includes_path"]."func_window.php";
	include_once $sys_config["includes_path"]."formElement.php";
	include_once $sys_config["includes_path"]."arrayCommon.php";
	include_once $sys_config["includes_path"]."func_email.php";
	//==========================================================
        if (!isset($_SESSION['user'])){	
			header("Location: /index.php");
			exit();
        }
	func_header(_LBL_HOME,
				// $sys_config["includes_path"]."jquery/Qtip/qtip.css,". // css
				"",
				$sys_config["includes_path"]."javascript/js_common.js,". // javascript
				$sys_config["includes_path"]."javascript/js_validate.js,". // javascript
				// $sys_config["includes_path"]."jquery/Qtip/jquery.qtip.min.js,". // javascript
				$sys_config["includes_path"]."twitter/js/bootstrap.js,". // javascript
				"",
				true, // menu
				false, // portlet
				true, // header
				false, // frame
				"", // str url frame top
				"", // str url frame left
				"", // str url frame content
				false // int top for [enter]
				);
	//==========================================================
	
	$levelID = $_SESSION["user"]["Level_ID"];
	$Usr_ID = $_SESSION["user"]["Usr_ID"];
?>
  <?php	if($levelID<=3){ ?>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>

	<script language="Javascript">
	$(document).ready(function() {
		/* 		
		$("#div_title [title]").each(function() {
			$(this).qtip({
				position: { corner: { tooltip: "topMiddle", target: "bottomMiddle" } },
				style: {
					name: "blue", // Give it the preset dark style //dark/light/cream/green/red/blue
					border: { width: 0, radius: 4  }, 
					tip: true, // Apply a tip at the default tooltip corner
					width: { max: 350, min: 0 }
				}
			});
		}); 
		*/
	});
	</script>
	<center>
	<?php func_window_open2(_LBL_MENU_HOME,"90%"); ?>
	<?php
	
		$today_label = date('D');
		$today = _get_RowExist('tbl_visitor_counter', array("AND DATE_FORMAT(visit_time, '%d-%m-%Y')" => " = ". quote_smart(date('d-m-Y'))));
		
		$d1_label = date('D', strtotime("-1 day"));
		$d1 = _get_RowExist('tbl_visitor_counter', array("AND DATE_FORMAT(visit_time, '%d-%m-%Y')" => " = ". quote_smart(date('d-m-Y', strtotime("-1 day")))));
		
		$d2_label = date('D', strtotime("-2 day"));
		$d2 = _get_RowExist('tbl_visitor_counter', array("AND DATE_FORMAT(visit_time, '%d-%m-%Y')" => " = ". quote_smart(date('d-m-Y', strtotime("-2 day")))));
		
		$d3_label = date('D', strtotime("-3 day"));
		$d3 = _get_RowExist('tbl_visitor_counter', array("AND DATE_FORMAT(visit_time, '%d-%m-%Y')" => " = ". quote_smart(date('d-m-Y', strtotime("-3 day")))));
		
		$d4_label = date('D', strtotime("-4 day"));
		$d4 = _get_RowExist('tbl_visitor_counter', array("AND DATE_FORMAT(visit_time, '%d-%m-%Y')" => " = ". quote_smart(date('d-m-Y', strtotime("-4 day")))));
		
		$d5_label = date('D', strtotime("-5 day"));
		$d5 = _get_RowExist('tbl_visitor_counter', array("AND DATE_FORMAT(visit_time, '%d-%m-%Y')" => " = ". quote_smart(date('d-m-Y', strtotime("-5 day")))));
		
		$d6_label = date('D', strtotime("-6 day"));
		$d6 = _get_RowExist('tbl_visitor_counter', array("AND DATE_FORMAT(visit_time, '%d-%m-%Y')" => " = ". quote_smart(date('d-m-Y', strtotime("-6 day")))));
		
	?>
	
	<div class="col-xs-8">
		<!-- <canvas id="myChart" width="400" height="200"></canvas> -->
<script>
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["<?=$d6_label?>", "<?=$d5_label?>", "<?=$d4_label?>", "<?=$d3_label?>", "<?=$d2_label?>", "<?=$d1_label?>","<?=$today_label?>"],
        datasets: [{
            label: 'SYSTEM USER',
            data: [<?=$d6?>,<?=$d5?>,<?=$d4?>,<?=$d3?>,<?=$d2?>,<?=$d1?>,<?=$today?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(255, 199, 194, 0.8)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(255, 109, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>
<?php } ?>

		<div class="panel panel-default"><div class="panel-body"><?php
			$homeInfo	= _get_StrFromCondition("sys_Info","Info_Desc","Info_ID","1");
			echo $homeInfo;
		?></div></div>
	</div>
	<div class="col-xs-4"><?php 	
		
		if($levelID<6){
			$checkHead = _check_HeadForApproval();
			/*********/
			$newCheck_onClick = "redirectForm('".$sys_config['submission_path']."main.php?menu=2');";
			$newCheck = _get_RowExist("tbl_Submission",array("AND Status_ID"=>"= 11"),"");
			/*********/
			if($levelID<=2) 
				$cond = array("AND s.Status_ID"=>"= 21");
			else $cond = array("AND s.Status_ID"=>"= 21","AND us.User_Head"=>"= ".quote_smart($Usr_ID));
			/*********/
			$newAprv_onClick = "redirectForm('".$sys_config['submission_path']."main.php?menu=6');";
			$newAprv = _get_RowExist("tbl_Submission s",$cond,array("LEFT JOIN sys_User_Staff us ON s.CheckedBy = us.Usr_ID"));
			/*********/
			$newAcknow_onClick = "redirectForm('".$sys_config['submission_path']."main.php?menu=3');";
			$newAcknow = _get_RowExist("tbl_Submission",array("AND Status_ID"=>"IN (31,32,33)"),"");
			/*********/
			$newReject_onClick = "redirectForm('".$sys_config['submission_path']."main.php?menu=4');";
			$newReject = _get_RowExist("tbl_Submission",array("AND Status_ID"=>"= 2"),"");
			/*********/
			$newChem = _get_RowExist("tbl_Chemical",array("AND isNew"=>"= 1","AND isDeleted" => "=0"),"");
			$newChem_onClick = "redirectForm('".$sys_config['inventory_path']."main.php?menu=5');";
			if($newChem>0){
				$newChem_img = '<img src="'.$sys_config['images_path'].'icons/new.gif" onClick="'.$newChem_onClick.'" style="cursor:pointer;cursor:hand;" />';
			}
			else $newChem_img = "";
			/*********/
		?>
		
		<div class="panel panel-default">
			<div class="panel-heading"><b><?= _LBL_NOTIFICATION ?></b></div>
			<table class="table">
					<?php if($levelID<=4){?>
					<tr class="info">
						<th><?= _LBL_SUBMISSION ?></th>
					</tr>
					<?php	if($levelID<=3){ ?>
					<tr>
						<td><a href="#" onClick="<?= $newCheck_onClick ?>"><?= _LBL_WAITING_CHECKING ?> <span class="badge"><?= $newCheck; ?></span></a></td>
					</tr>
					<?php	} ?>
					<?php	if($levelID!=3){ ?>
					<tr>
						<td><a href="#" onClick="<?= $newAprv_onClick ?>"><?= _LBL_WAITING_APPROVAL ?> <span class="badge"><?= $newAprv; ?></span></a></td>
					</tr>
					<?php	} ?>
					<tr>
						<td><a href="#" onClick="<?= $newAcknow_onClick ?>"><?= _LBL_ACKNOWLEGED ?> <span class="badge"><?= $newAcknow; ?></span></a></td>
					</tr>
					<tr>
						<td><a href="#" onClick="<?= $newReject_onClick ?>"><?= _LBL_REJECTED ?> <span class="badge"><?= $newReject; ?></span></a></td>
					</tr>
					<?php } ?>
					<tr class="info">
						<td><a href="#" onClick="<?= $newChem_onClick ?>"><?= _LBL_NEW_CHEMICAL ?> <span class="badge"><?= $newChem; ?></span> <?= $newChem_img ?></a></td>
					</tr>
			</table>
		</div>
		<?php
		} else {
		?>
		<div class="panel panel-default">
			<?php			
				/*********/
				$newAcknow_onClick = "redirectForm('".$sys_config['submission_path']."main.php?menu=3');";
				$newAcknow = _get_RowExist('tbl_Submission',array('AND Status_ID'=>'IN (31,32,33)','AND Usr_ID'=>'= '.quote_smart($Usr_ID)),'');
				/*********/
				$newReject_onClick = "redirectForm('".$sys_config['submission_path']."main.php?menu=4');";
				$newReject = _get_RowExist('tbl_Submission',array('AND Status_ID'=>'IN (2)','AND Usr_ID'=>'= '.quote_smart($Usr_ID)),'');
			?>
			<div class="panel-heading"><b><?= _LBL_NOTIFICATION ?></b></div>
			<table class="table">
				<tr class="info">
					<th><?= _LBL_SUBMISSION ?></th>
				</tr>
				<tr>
					<td><a href="#" onClick="<?= $newAcknow_onClick ?>"><?= _LBL_ACKNOWLEGED ?> <span class="badge"><?= $newAcknow; ?></span></a></td>
				</tr>
				<tr>
					<td><a href="#" onClick="<?= $newReject_onClick ?>"><?= _LBL_REJECTED ?> <span class="badge"><?= $newReject; ?></span></a></td>
				</tr>
			</table>
		</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading"><b><?= _LBL_CHEMICAL_INFO_SEARCH ?></b></div>
			<div class="panel-body text-center"><?php
				$imgParam1 = $sys_config["images_path"]."Search.png";
				$imgParam2 = _LBL_SEARCH_REG_CHEM;
				$imgParam3 = "location.href='". $sys_config["inventory_path"] ."complete_search.php'";
				$imgParam4 = array("width"=>"140","height"=>"110");
				echo _get_imagebutton2($imgParam1,$imgParam2,$imgParam3,$imgParam4);
			?></div>
		</div>
		<?php
		$linkInfo	= _get_StrFromCondition("sys_Info","Info_Desc","Info_ID","20");
		if(!empty($linkInfo)){
		?>
		<div class="panel panel-default">
			<div class="panel-heading"><b><?= _LBL_RELATED_LINKS ?></b></div>
			<div class="panel-body"><?php echo $linkInfo ?></div>
		</div>
		<?php } ?>
	</div>
	
	<?php func_window_close2(); ?>
	</center>
