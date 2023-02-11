<?php
	if(isset($_SESSION['userID'])){
		$time = mktime();
		$sql = "UPDATE tbl_User SET Times = '".$time."' WHERE User_ID = '".$_SESSION['userID']."'";
		$res = $db->sql_query($sql, END_TRANSACTION) or die ('Error in query: ' . $db->sql_error());
	}
?>

		</td></tr></table>
	</table>
	<address><table width="100%" cellspacing="0"><tr><td><center></center></td></tr></table></address>
	</body>
	</html>