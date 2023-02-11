<?php
	/*********/
	if($Usr_ID!=0){
		$dataColumns = '*';
		$dataTable = 'tbl_Client_Company';
		$dataJoin = '';
		$dataWhere = array("AND Usr_ID" => "= ".quote_smart($Usr_ID),"AND isDeleted" => "= 0");
		$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere); 
	}
	/*********/
?>
	<script language="Javascript">
	</script>

	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents" height="28">
			<td class="label title" colspan="3" style="vertical-align:middle"><?= _LBL_CHEMICAL_USERS ?> <a href="#" onClick="addRow();"><?= _LBL_ADD ?></a></td>
		</tr>
		<?php 
			$i = 1;
			foreach($arrayData as $detail){
				echo form_input("companyID".$i,$detail["Company_ID"],array("type"=>"hidden"));
		?>
		<tr class="contents">
			<td width="20%" class="label"><?= _LBL_COMPANY ." ". $i ?></td>
			<td width="70%"><?php
				/*********/
				$param1 = "companyName".$i;
				$param2 = isset($_POST[$param1])?$_POST[$param1]:$detail["Company_Name"];
				$param3 = array("type"=>"text","style"=>"width:300px");
				echo form_input($param1,$param2,$param3);
				/*********/
				$emptyParam = $param1;
				/*********/
				echo "&nbsp;";
				/*********/
				$param1 = "active".$i;
				$param2 = $arrStatusAktif;
				$param3 = isset($_POST[$param1])?$_POST[$param1]:$detail["Active"];
				$param4 = "&nbsp;";
				echo form_radio($param1,$param2,$param3,$param4,"");
				/*********/
			?></td>
			<td width="10%"><?php
				$imgParam1 = $sys_config["images_path"]."icons/delete.gif";
				$imgParam2 = _LBL_EMPTY;
				$imgParam3 = "emptyRow('$emptyParam');";
				echo _get_imagebutton($imgParam1,$imgParam2,$imgParam3);
			?>
			</td>
		</tr>
		<?php
				$i++;
			}
		?>
	</table>
	<br />
	<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%">
		<tr class="contents">
			<td align="center"><?php
				/*********/
				$dOthers = array("type"=>"button","onClick"=>"redirectForm('userList.php?levelID=$levelID');");
				echo form_button("cancel",_LBL_CANCEL,$dOthers);
				/*********/
			?></td>
		</tr>
	</table>
