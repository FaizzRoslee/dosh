<html>
<head>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
<script>

$(document).ready( function () {
    //$('#laporan').DataTable();
});

</script>
</head>
<body>
<?php $bil =1; ?>
<button onclick="exportTableToExcel('hasil','cims-report-<?=$negeri?>')">Export Table Data To Excel File</button>

<table width="100%" border="1" id="hasil" class="table table-striped table-bordered datatable" style="width:100%">
<thead>
<tr>
    <th>No</th>
    <th>Product Name</th>
    <th>Chemical Name</th>
    <th>CAS No</th>
    <th>Hazard Class</th>
    <th>Acknowledged Date</th>
    <th>Quantity Imported <br/> (tonne/year) </th>
    <th>Quantity Import <br/>(tonne/year)</th>
    

</tr>
<tr>
    <th colspan="9"><?=$negeri?></th>
</tr>
</thead>
<tbody>
<?php 
	//print_r($senarai);
	foreach($senarai as $s){ ?>
    <tr>
	<td colspan="9">
	   ::Impoter::<br/>
	   <?=$s['namasyarikat']?><br/>
	   <?=$s['alamat']?><br/>
	   <?=$s['poskod']?> <?=$negeri?><br/>
	</td>
    </tr>
   <?php 
	$bil = 1;
	foreach($s['produk'] as $p) { ?>

	<?php if (count($p['bahan']) == 1) { ?>	
    		<tr>
        	<td><?=$bil?></td>
        	<td>
		<?php 
			if($p['nama'] == ''){
				echo $p['bahan'][0]['namabahan'];
			}
			else{
				echo $p['nama'];
			}

		?> </td>
		<td><?=$p['bahan'][0]['namabahan']?></td>
		<td><?=$p['bahan'][0]['cas']?></td>
		<td></td>
		<td><?=$p['aprovedate']?></td>
        	<td><?=$p['total']?></td>
        	<td><?=$p['totaluse']?></td>
    		</tr>
	<?php }else{ ?>
			<?php 
			$kbahan = 0;
			$jb = count($p['bahan']);
			foreach ($p['bahan'] as $b) { 
				if ($kbahan == 0){  	
			?>	
				<tr>
		      		<td rowspan="<?=$jb?>" ><?=$bil?></td>
                		<td rowspan="<?=$jb?>" ><?=$p['nama']?></td>
                		<td><?=$p['bahan'][$kbahan]['namabahan']?></td>
                		<td><?=$p['bahan'][$kbahan]['cas']?></td>
                		<td rowspan="<?=$jb?>"></td>
                		<td rowspan="<?=$jb?>"><?=$p['aprovedate']?></td>
                		<td rowspan="<?=$jb?>"><?=$p['total']?></td>
                		<td rowspan="<?=$jb?>"><?=$p['totaluse']?></td>
				</tr>
			<?php
				}else{
				   ?>
					<tr> 	
					<td><?=$p['bahan'][$kbahan]['namabahan']?></td>
                                	<td><?=$p['bahan'][$kbahan]['cas']?></td>
					</tr>
					<?php	
				}
			$kbahan++;
			}	
		      ?>
	<?php } ?>

<?php

$bil++;
}
} ?>
</tbody>
</table>



</body>
</html>

<script>

function exportTableToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    // Specify file name
    filename = filename?filename+'.xls':'excel_data.xls';
    
    // Create download link element
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    
        // Setting the file name
        downloadLink.download = filename;
        
        //triggering the function
        downloadLink.click();
    }
}
</script>
