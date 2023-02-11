<html>
<head>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
<script type="text/javascript" src="https://unpkg.com/xlsx@0.15.1/dist/xlsx.full.min.js"></script>

<script>

$(document).ready( function () {
    //$('#hasil').DataTable();
});

</script>
</head>
<body>
<?php $bil =1; ?>
<button onclick="exportTableToExcel('hasil','cims-report-<?=$negeri?>')">Export Table Data To Excel File</button>
<button onclick="ExportToExcel('xlsx')">Export table to excel V2</button>
<table width="50%" border="1" id="hasil" class="table2excel table table-striped table-bordered datatable">
<thead>
<tr>
    <th>No</th>
    <th>Nama Syarikat</th>
    <th>Alamat</th>
    <th>Poskod</th>
    <th>Bandar</th>
    <th>Negeri</th>
    <th>Phone No.</th>
    <th>Fax No</th>
    <th>Email</th>
    <th>Contact Name</th>
    <th>Contact Designation</th>
    <th>Contact Mobile No</th>
    <th>Contact Email</th>
    <th>Product Name</th>
    <th>Chemical Name</th>
    <th>CAS No</th>
    <th>Submission Date</th>
    <th>Acknowledged Date</th>
    <th>Quantity Imported <br/> (tonne/year) </th>
    <th>Quantity Import <br/>(tonne/year)</th>
    

</tr>
</thead>
<tbody>
<?php 
	//print_r($senarai);
	$bil = 1;

	foreach($senarai as $s){ ?>
   <?php 
	foreach($s['produk'] as $p) { ?>

	<?php if (count($p['bahan']) == 1) { ?>	
    		<tr>
        	<td><?=$bil?></td>
        	
		<td><?=$s['namasyarikat']?></td>
		<td><?=$s['alamat']?></td>
		<td><?=$s['poskod']?></td>
		<td><?=$s['bandar']?></td>
		<td><?=$negeri?></td>
		<td><?=$s['phone']?></td>
		<td><?=$s['fax']?></td>
		<td><?=$s['email']?></td>
		<td><?=$s['cname']?></td>
		<td><?=$s['cdesc']?></td>
		<td><?=$s['cmphone']?></td>
		<td><?=$s['cemail']?></td>
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
		<td><?=$p['rcorddate']?></td>
		<td><?=$p['aprovedate']?></td>
        	<td><?=$p['total']?></td>
        	<td><?=$p['totaluse']?></td>
    		</tr>
	<?php 
	$bil++;
	}else
	{ 

	?>
			<?php 
			$kbahan = 0;
			$jb = count($p['bahan']);
			foreach ($p['bahan'] as $b) { 
				if ($kbahan == 0){  	
			?>	
				<tr>
		      		<td rowspan="<?=$jb?>" ><?=$bil?></td>
				<td rowspan="<?=$jb?>" ><?=$s['namasyarikat']?></td>
                <td rowspan="<?=$jb?>" ><?=$s['alamat']?></td>
                <td rowspan="<?=$jb?>" ><?=$s['poskod']?></td>
                <td rowspan="<?=$jb?>" ><?=$s['bandar']?></td>
                <td rowspan="<?=$jb?>" ><?=$negeri?></td>
                <td rowspan="<?=$jb?>" ><?=$s['phone']?></td>
                <td rowspan="<?=$jb?>" ><?=$s['fax']?></td>
                <td rowspan="<?=$jb?>" ><?=$s['email']?></td>
                <td rowspan="<?=$jb?>" ><?=$s['cname']?></td>
                <td rowspan="<?=$jb?>" ><?=$s['cdesc']?></td>
                <td rowspan="<?=$jb?>" ><?=$s['cmphone']?></td>
		<td rowspan="<?=$jb?>" ><?=$s['cemail']?></td>
                		<td rowspan="<?=$jb?>" ><?=preg_replace('/[^A-Za-z0-9\-]/', '',$p['nama'])?></td>
                		<td><?=$p['bahan'][$kbahan]['namabahan']?></td>
                		<td><?=$p['bahan'][$kbahan]['cas']?></td>
                		<td rowspan="<?=$jb?>"><?=$p['rcorddate']?></td>
                		<td rowspan="<?=$jb?>"><?=$p['aprovedate']?></td>
                		<td rowspan="<?=$jb?>"><?=$p['total']?></td>
                		<td rowspan="<?=$jb?>"><?=$p['totaluse']?></td>
				</tr>
			<?php $bil++;
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

}
} ?>
</tbody>
</table>



</body>
</html>

<script>
function ExportToExcel(type, fn, dl) {
       var elt = document.getElementById('hasil');
       var wb = XLSX.utils.table_to_book(elt, { sheet: "sheet1" });
       return dl ?
         XLSX.write(wb, { bookType: type, bookSST: true, type: 'base64' }):
         XLSX.writeFile(wb, fn || ('laporan-cims-v2.' + (type || 'xlsx')));
}
function exportTableToExcel(tableID, filename = ''){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    //var dataType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    var tableSelect = document.getElementById(tableID);
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    //var tableHTML = tableSelect.outerHTML.replace(/ /g, "%20");   
    // Specify file name
    filename = filename?filename+'.xlsx':'excel_data.xlsx';
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
