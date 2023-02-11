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
<button onclick="exportTableToExcel('hasil','cims-report')">Export Table Data To Excel File</button>

<table width="100%" border="1" id="hasil" class="table table-striped table-bordered datatable" style="width:100%">
<thead>
<tr>
    <th rowspan="2">Bil</th>
    <th rowspan="2">NAMA SYARIKAT</th>
    <th rowspan="2">ALAMAT</th>
    <th rowspan="2">POSKOD</th>
    <th rowspan="2">BANDAR</th>
    <th rowspan="2">NEGERI</th>
    <th rowspan="2">Phone No. </th>
    <th rowspan="2">Fax No. </th>
    <th rowspan="2">Email</th>
    <th rowspan="2">Contact Name</th>
    <th rowspan="2">Contact Designation</th>
    <th rowspan="2">Contact Mobile No</th>
    <th rowspan="2">Contact Email</th>
    <th rowspan="2">NAMA BAHAN</th>
    <th rowspan="2">CAS NO</th>
    <th colspan="2">KUANTITI (TAN/TAHUN)</th>
    
</tr>

<tr>
    <th>IMPORT</th>
    <th>Guna</th>
</tr>
</thead>
<tbody>
<?php foreach($senarai as $s){ ?>


    <tr>
        <td><?=$bil?></td>
        <td><?=$s->Company_Name?></td>
        <td><?=$s->Address_Reg?></td>
        <td><?=$s->Postcode_Reg?></td>
        <td><?=$s->City_Postal?></td>
        <td><?=$s->negeri?></td>
	<td><?=$s->Phone_No?></td>
	<td><?=$s->Fax_No?></td>
	<td><?=$s->Email?></td>
	<td><?=$s->Contact_Name?></td>
	<td><?=$s->Contact_Designation?></td>
	<td><?=$s->Contact_Mobile_No?></td>
	<td><?=$s->Contact_Email?></td>
        <td><?php 
        

        
        $bahan = explode(',',$s->bahan);

      
        $bahan_rujukan = explode(',',$s->bahan_rujukan);
            foreach($bahan_rujukan as $br){

                if($br !== ''){
                    echo $this->datasistem->get_bahan_name($br)."<br/>";
                }
            }       
     
            foreach($bahan as $b){

                if ($b !== ''){
                    echo $b."<br/>";
                }
            }
        
        ?></td>
        <td><?php
        
        $cas = explode(',',$s->bahan_dtl);

        foreach ($cas as $c){
            echo $this->datasistem->get_cas($c)."<br/>";
        }
        
        ?></td>
        <td><?=$s->jumlah_i?></td>
        <td><?=$s->jumlah?></td>
    </tr>

<?php

$bil++;

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
