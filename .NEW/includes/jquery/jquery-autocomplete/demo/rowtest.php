
<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<script type="text/javascript" src="../lib/jquery.js"></script>
<script type='text/javascript' src='../lib/jquery.bgiframe.min.js'></script>
<script type='text/javascript' src='../lib/jquery.ajaxQueue.js'></script>
<script type='text/javascript' src='../lib/thickbox-compressed.js'></script>
<script type='text/javascript' src='../jquery.autocomplete.js'></script>
<script type='text/javascript' src='localdata.js'></script>
<link rel="stylesheet" type="text/css" href="main.css" />
<link rel="stylesheet" type="text/css" href="../jquery.autocomplete.css" />
<link rel="stylesheet" type="text/css" href="../lib/thickbox.css" />
	
<head>

  <title>Add Row v2 (Javascript)</title>
  <script type="text/javascript" src="addrow-v2.js"></script>

</head>
<script type="text/javascript">
$().ready(function() {
	$("#singleBirdRemote").autocomplete("search.php", {
		width: 260,
		selectFirst: false
	});
	<?php for($i=1;$i<10;$i++){ ?>
	$("#singleBirdRemote<?= $i ?>").autocomplete("search.php", {
		width: 260,
		selectFirst: false
	});
	<?php } ?>
});
</script>
<body>
  

<h1>Add Row (Javascript)</h1>
<h2>Version 2</h2>

<form action="" method="get" autocomplete="off">

<table>
<tr>
  <td><span class="things_to_clone"><input type="text" name="name[0]" id="singleBirdRemote" style="width:100px" /> <a onclick="addCell(); return false;" href="#" style="text-decoration:none">+</a> </span></td>
</tr>
<input type="text" name="" id="singleBirdRemote1" style="width:100px" />

</table>

<div><input type="submit" /></div>



</form>


</body>

</html>

