<link rel="stylesheet" type="text/css" href="/includes/jquery/DataTables/css/media.dataTable.css"/>
<script type="text/javascript" src="/includes/jquery/DataTables/js/jquery.dataTables.min.js"></script>

<button class="btn btn-default" onclick="baru()">Add New</button>
<hr/>
<table class="table" border="1">
	<thead>
		<tr>
			<th></th>
			<th>Message</th>
			<th>position</th>
		</tr>
	</thead>
	<tbody>
					<?php foreach ($splash as $s) { ?>

		<tr>
			<td onclick="padam(<?=$s->splash_id?>);">X</td>
			<td onclick="edit(<?=$s->splash_id?>);"><?=$s->ayat?></td>
			<td><?=$s->kedudukan?></td>
			
			
		</tr><?php	
			}
			?>
	</tbody>
</table>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script>
	function padam(id){
		
		var c = confirm('Anda Pasti ? ');
		
		if (c == true){
		$('#papar').load('/welcome/del_splash',{id:id});
		$('#papar').load('/welcome/splash',{id:id});
		}
		
	}
	function baru(){
		$('#papar').load('/welcome/form_splash');
	}
	function edit(id){
		$('#papar').load('/welcome/form_splash',{id:id});
	}
</script>


