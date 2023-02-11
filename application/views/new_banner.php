
<div class="container">
		<h3>Edit Banner Infomation</h3>
		  <div class="form-group">
		  	
			  <label for="gambar">Select Image:</label>
			  <select class="form-control" id="gambar">
			  	<option>Select Image</option>
			  	<?php
			  	 define('IMAGEPATH', 'upload/banner/');
           
           			foreach(glob(IMAGEPATH.'*.jpg') as $filename){
           		?>
			    <option value="../upload/banner/<?=basename($filename)?>"><?=basename($filename)?></option>
			 	<?php } ?>
			  </select>
			  
			</div>
			
		  <div class="form-group">
		    <label class="sr-only" for="url">url:</label>
		    <input type="text" class="form-control" id="url" value="">
		  </div>
		  <div class="form-group">
		    <label class="sr-only" for="info">Infomation:</label>
		    <input type="info" class="form-control" id="info">
		  </div>
		 
		  		  <a class="btn btn-default" onclick="simpan()">Save</a>

	

</div>
<br /><br />

<center>
			<img id="img" src="" width="60%" /></center>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<script  >
	
	$("#gambar").change(function(){
		//alert('change');
		$('#img').attr('src',$(this).val());
	});
	function simpan(){
		//alert('simpan');
		$('#papar').load('/welcome/save_bana',{jenis:1,url:$("#url").val(),gambar:$("#gambar").val(),info:$("#info").val()});
		$('#papar').load('/welcome/bana',{id:30});
	}
</script>

