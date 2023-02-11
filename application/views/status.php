
<table>
	<tr>
		<td>From : <input type="text" id="datepicker"></td>
		<td>To : <input type="text" id="datepicker2"></td>
		<td><button id="hantar">Submit</button></td>
	</tr>	

</table>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">

 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
  <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.bundle.min.js"></script>

  <script>
  $( function() {
    $( "#datepicker" ).datepicker();
    $( "#datepicker2" ).datepicker();
  } );
  
  $("#hantar").click(function(){
  	
  		$("#keputusan").load("/welcome/keputusan",{dari:$("#datepicker").val(),hingga:$("#datepicker2").val()});
  });
  
  </script>
  
  <div id="keputusan"></div>
