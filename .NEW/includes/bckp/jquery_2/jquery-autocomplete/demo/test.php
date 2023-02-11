
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<?php
	echo '<pre>';
	print_r($_POST);
	echo '</pre>';
?>

<script type="text/javascript" src="../lib/jquery.js"></script>
<script type='text/javascript' src='../lib/jquery.bgiframe.min.js'></script>
<script type='text/javascript' src='../lib/jquery.ajaxQueue.js'></script>
<script type='text/javascript' src='../lib/thickbox-compressed.js'></script>
<script type='text/javascript' src='../jquery.autocomplete.js'></script>
<script type='text/javascript' src='localdata.js'></script>
<link rel="stylesheet" type="text/css" href="main.css" />
<link rel="stylesheet" type="text/css" href="../jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="../lib/thickbox.css" />

<?php
	if(isset($_POST['hidrow'])) $hidrow = $_POST['hidrow'];
	else $hidrow = 1;
?>

<head>

  <title>Add Row v2 (Javascript)</title>
  <script type="text/javascript" src="addrow-v2.js"></script>

</head>
<script type="text/javascript">
function addingRow(){
	var d = document;
	var hidrow = d.getElementById('hidrow').value;
	hidrow = parseInt(hidrow)+1;
	d.getElementById('hidrow').value = hidrow;
	d.myForm.submit();
}

$().ready(function() {

	function formatItem(row) {
		return row[0] + " (<strong>description: " + row[1] + "</strong>)";
	}
	function formatResult(row) {
		return row[0].replace(/(<.+?>)/gi, '');
	}
	$("#singleBirdRemote").autocomplete("search.php", {
		width: 260,
		selectFirst: false
	});
	<?php for($i=1;$i<=$hidrow;$i++){ ?>
	$("#test<?= $i ?>").autocomplete("search.php", {
		width: 260,
		multiple: true,
		selectFirst: false
	});
	$("#suggest<?= $i ?>").autocomplete('search.php', {
		width: 450,
		multiple: true,
		matchContains: true,
		formatItem: formatItem,
		formatResult: formatResult
	});
	<?php } ?>
	$(":text, textarea").result().next().click(function() {
		$(this).prev().search();
	});
	<?php for($i=1;$i<=$hidrow;$i++){ ?>
	$("#test<?= $i ?>").result(function(event, data, formatted) {
		if (data)
			$(this).parent().next().find("input").val(data[1]);
	});
	$("#suggest<?= $i ?>").result(function(event, data, formatted) {
		var hidden = $(this).parent().next().find(">:input");
		hidden.val( (hidden.val() ? hidden.val() + ";" : hidden.val()) + data[1]);
	});
	<?php } ?>
});
</script>
<body>
  

<h1>Add Row (Javascript)</h1>
<h2>Version 2</h2>

<form name="myForm" id="myForm" action="" method="post" autocomplete="off">

<table>
<tr>
  <td><span class="things_to_clone"><input type="text" name="name[0]" id="singleBirdRemote" style="width:100px" /> <a onclick="addCell(); return false;" href="#" style="text-decoration:none">+</a> </span></td>
</tr>

</table>

<a href="#" onclick="addingRow();">Add</a>
<table>
	<tr>
		<td><?php for($i=1; $i<=$hidrow; $i++){ if($i>1) echo ' + '; ?><input type="text" name="test<?= $i ?>" id="test<?= $i ?>" value="<?= isset($_POST['test'.$i])?$_POST['test'.$i]:''; ?>" style="width:400px" /><?php }?><input size="10px" /></td>
	</tr>
	<?php for($i=1; $i<=$hidrow; $i++){ ?>
	<tr>
		<td><textarea id="suggest<?= $i ?>" name="suggest<?= $i ?>"><?= isset($_POST['suggest'.$i])?$_POST['suggest'.$i]:''; ?></textarea><textarea></textarea></td>
	</tr>
	<?php } ?>
</table>
<div id="result"></div>
hidrow: <input type="text" name="hidrow" id="hidrow" value="<?= $hidrow ?>" />

<div><input type="submit" /></div>



</form>


</body>

</html>

