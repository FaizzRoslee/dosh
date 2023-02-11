<?php
	if($numrows > 0){
		if($pageno==1){
			$pages = "FIRST | PREVIOUS ";
		}else{
			$pages  = '<a href="'.$formName.'?pageno=1'.$param.'">FIRST</a> | ';
			$pages .= '<a href="'.$formName.'?pageno='.($pageno-1).$param.'" >PREVIOUS</a>';
		}
		
		$pages .= " ( Page ".$pageno." of ".$lastpage." ) ";
		
		if($pageno == $lastpage){
			$pages .= " NEXT | LAST <br>";
		}else{
			$pages .= '<a href="'.$formName.'?pageno='.($pageno+1).$param.'">NEXT</a> | ';
			$pages .= '<a href="'.$formName.'?pageno='.$lastpage.$param.'">LAST</a><br>';
		}
		
		for($i=1; $i<=$lastpage; $i++){
			if($i!=$pageno)
				$pages .= '<a href="'.$formName.'?pageno='.$i.$param.'">'.$i.'</a>&nbsp';
			else
				$pages .= '<a href="'.$formName.'?pageno='.$i.$param.'" ><font color="red">'.$i.'</a>&nbsp';
		}
	}else{
		$pages = '';
	}	
?>