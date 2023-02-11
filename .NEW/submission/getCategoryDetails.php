<?php
	//****************************************************************/
	// filename: categoryDetails.php
	// description: tip for the hazard category
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
	//==========================================================


	if(isset($_POST['categoryID'])){
		$categoryID = $_POST['categoryID'];
	
		if($categoryID!=''){
			$dataColumns = 'Pictogram_ID,Signal_ID,Statement_ID,PreStatement_ID1,PreStatement_ID2,PreStatement_ID3,PreStatement_ID4';
			$dataTable = 'tbl_Hazard_Category';
			$dataJoin = '';
			$dataWhere = array("AND Category_ID"=>"= ".quote_smart($categoryID));
			$dataOrder = '';
			$dataLimit = '1';
			$arrayData = _get_arrayData($dataColumns,$dataTable,$dataJoin,$dataWhere,$dataOrder,$dataLimit); 
			foreach($arrayData as $detail){
				$pictogramID 	= $detail['Pictogram_ID'];
				$signalID 		= $detail['Signal_ID'];
				$hStatementID	= $detail['Statement_ID'];
				$pStatementID1 	= $detail['PreStatement_ID1'];
				$pStatementID2 	= $detail['PreStatement_ID2'];
				$pStatementID3 	= $detail['PreStatement_ID3'];
				$pStatementID4 	= $detail['PreStatement_ID4'];
			
				echo '
					<br />
					<table width="100%" cellspacing="1" cellpadding="3" class="contents">
					';
				echo'
						<tr class="contents">
							<td width="17%" class="label" rowspan="2">'. _LBL_HAZARD_PICTOGRAM .'</td>
							<td width="33%" rowspan="2">
					';
					if($pictogramID!=''){
						echo '<table width="100%">';
						$ex_pictogramID = explode(',',$pictogramID);
						$i=0;
						$maxArr = count($ex_pictogramID);
						foreach($ex_pictogramID as $pID){				
							$i++;
							if($i%3==1){ echo '<tr>'; }
							echo '<td width="33%" align="center"><img src="'. _get_StrFromCondition('tbl_Hazard_Pictogram','Pictogram_Url','Pictogram_ID',$pID) .'" width="30px" /><br /><label>'. _get_StrFromCondition('tbl_Hazard_Pictogram','Pictogram_Desc','Pictogram_ID',$pID) .'</label></td>';
							if($maxArr==$i && $i%3!=0){
								$emptyCell = 3-($i%3); 
								for($j=0;$j<$emptyCell;$j++){ echo '<td>&nbsp;</td>'; }
							}
							if($i%3==0){ echo '</tr>'; }
						}
						echo '</table>';
					}else{
						echo '<label>'. _LBL_NO_PICTOGRAM .'</label>';
					}
					
					$signal = _get_StrFromCondition('tbl_Hazard_Signal','Signal_Name','Signal_ID',$signalID);
					if( $signal=='' || empty($signal) ) $signal = '-';
				echo'
							</td>
							<td width="17%" class="label">'. _LBL_SIGNAL_WORD .'</td>
							<td width="33%"><label>'. $signal .'</label></td>
						</tr>
					';
				echo'
						<tr class="contents">
							<td width="17%" class="label">'. _LBL_HAZARD_STATEMENT .'</td>
							<td width="33%"><label>
					';
					$ex_hStatementID = explode(',',$hStatementID);
					foreach($ex_hStatementID as $hsID){
						echo _get_StrFromCondition('tbl_Hazard_Statement','CONCAT_WS(": ",Statement_Code,Statement_Desc)','Statement_ID',$hsID) .'<br />';
					}
				echo'
							</label></td>
						</tr>
					';
				echo'
						<tr class="contents">
							<td colspan="4" class="label title">'. _LBL_PRECAUTIONARY_STATEMENT .'</td>
						</tr>
					';
				echo'
						<tr class="contents">
							<td width="17%" class="label">'. _LBL_PREVENTION .'</td>
							<td width="33%"><label>
					';
					$ex_pStatementID1 = explode(';',$pStatementID1);
					$empty = TRUE;
					foreach($ex_pStatementID1 as $psID){
						if($psID!=''){
							$empty = FALSE;
							$ex_psID = explode('+',$psID);
							$i=0;
							$sCode = '';
							$sDesc = '';
							foreach($ex_psID as $sID){
								if($i==0){ $i=1; }
								else{ $sCode .= ' + ';$sDesc .= ' + '; }
								$sCode .= _get_StrFromCondition('tbl_Precaution_Statement','Statement_Code','Statement_ID',$sID);
								$sDesc .= _get_StrFromCondition('tbl_Precaution_Statement','Statement_Desc','Statement_ID',$sID);
							}
							echo '<strong>'. $sCode .':</strong> [ '.$sDesc.' ]<br />';
						}
					}
					if($empty==TRUE) echo '-';
				echo'
							</label></td>
							<td width="17%" class="label">'. _LBL_RESPONSE .'</td>
							<td width="33%"><label>
					';
					$empty = TRUE;
					$ex_pStatementID2 = explode(';',$pStatementID2);
					foreach($ex_pStatementID2 as $psID){
						if($psID!=''){
							$empty = FALSE;
							$ex_psID = explode('+',$psID);
							$i=0;
							$sCode = '';
							$sDesc = '';
							foreach($ex_psID as $sID){
								if($i==0){ $i=1; }
								else{ $sCode .= ' + ';$sDesc .= ' + '; }
								$sCode .= _get_StrFromCondition('tbl_Precaution_Statement','Statement_Code','Statement_ID',$sID);
								$sDesc .= _get_StrFromCondition('tbl_Precaution_Statement','Statement_Desc','Statement_ID',$sID);
							}
							echo '<strong>'. $sCode .':</strong> [ '.$sDesc.' ]<br />';
						}
					}
					if($empty==TRUE) echo '-';
				echo'
							</label></td>
						</tr>
					';
				echo'
						<tr class="contents">
							<td width="17%" class="label">'. _LBL_STORAGE .'</td>
							<td width="33%"><label>
					';
					$empty = TRUE;
					$ex_pStatementID3 = explode(';',$pStatementID3);
					foreach($ex_pStatementID3 as $psID){
						if($psID!=''){
							$empty = FALSE;
							$ex_psID = explode('+',$psID);
							$i=0;
							$sCode = '';
							$sDesc = '';
							foreach($ex_psID as $sID){
								if($i==0){ $i=1; }
								else{ $sCode .= ' + ';$sDesc .= ' + '; }
								$sCode .= _get_StrFromCondition('tbl_Precaution_Statement','Statement_Code','Statement_ID',$sID);
								$sDesc .= _get_StrFromCondition('tbl_Precaution_Statement','Statement_Desc','Statement_ID',$sID);
							}
							echo '<strong>'. $sCode .':</strong> [ '.$sDesc.' ]<br />';
						}
					}
					if($empty==TRUE) echo '-';
				echo'
							</label></td>
							<td width="17%" class="label">'. _LBL_DISPOSAL .'</td>
							<td width="33%"><label>
					';
					$empty = TRUE;
					$ex_pStatementID4 = explode(';',$pStatementID4);
					foreach($ex_pStatementID4 as $psID){
						if($psID!=''){
							$empty = FALSE;
							$ex_psID = explode('+',$psID);
							$i=0;
							$sCode = '';
							$sDesc = '';
							foreach($ex_psID as $sID){
								if($i==0){ $i=1; }
								else{ $sCode .= ' + ';$sDesc .= ' + '; }
								$sCode .= _get_StrFromCondition('tbl_Precaution_Statement','Statement_Code','Statement_ID',$sID);
								$sDesc .= _get_StrFromCondition('tbl_Precaution_Statement','Statement_Desc','Statement_ID',$sID);
							}
							echo '<strong>'. $sCode .':</strong> [ '.$sDesc.' ]<br />';
						}
					}
					if($empty==TRUE) echo '-';
				echo'
							</label></td>
						</tr>
					';
				echo'
					</table>
					<br />
					';
			}
		}
	}
