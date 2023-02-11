<link rel="stylesheet" type="text/css" href="/includes/jquery/DataTables/css/media.dataTable.css"/>
<script type="text/javascript" src="/includes/jquery/DataTables/js/jquery.dataTables.min.js"></script>

<div class="demo_jui">
		<a class="btn btn-default" onclick="baru()">Add New</a>
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">

			<thead>
				<tr>
					<th></th>
					<th>Image</th>
					<th>url</th>
				</tr>
			</thead>
<?php
	foreach ($banner as $b) {
?>		
		<tr>
			<td onclick="padam('<?=$b->Image_ID?>')"><span class="glyphicon glyphicon-remove"></span></td>
			<td ><img onclick="ubah('<?=$b->Image_ID?>')" src="<?=$b->Image_URL?>" alt="dosh" width="100" ></td>
			<td><?=$b->url?></td>
		</tr>
		
		
<?php		
	}
	
?>	
</table>
</div>

<script>
	
	function padam(id){
		//alert('padam');
		var c = confirm('Anda Pasti ? ');
		
		if (c == true){
		$('#papar').load('/welcome/del_bana',{id:id});
		$('#papar').load('/welcome/bana',{id:id});
		}
		
	}
	
	function ubah(id){
		$('#papar').load('/welcome/edit_bana',{id:id});
	}
	function baru(){
		$('#papar').load('/welcome/new_bana');
	}
</script>