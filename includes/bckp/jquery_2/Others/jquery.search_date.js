	jQuery.fn.dataTableExt.oSort['html-date-asc']  = function(a,b) {
		var ukDatea = a.split('-');
		var ukDateb = b.split('-');
		
		var yeara = (ukDatea[2]) ? ukDatea[2] : '0000';
		var montha = (ukDatea[1]) ? ukDatea[1] : '00';
		var daya = (ukDatea[0]) ? ukDatea[0] : '00';
		var x = ( yeara + montha + daya ) * 1;
		
		var yearb = (ukDateb[2]) ? ukDateb[2] : '0000';
		var monthb = (ukDateb[1]) ? ukDateb[1] : '00';
		var dayb = (ukDateb[0]) ? ukDateb[0] : '00';
		var y = ( yearb + monthb + dayb ) * 1;
		
		return ((x < y) ? -1 : ((x > y) ?  1 : 0));
	};

	jQuery.fn.dataTableExt.oSort['html-date-desc'] = function(a,b) {
		var ukDatea = a.split('-');
		var ukDateb = b.split('-');
		
		var year = (ukDatea[2]) ? ukDatea[2] : '0000';
		var month = (ukDatea[1]) ? ukDatea[1] : '00';
		var day = (ukDatea[0]) ? ukDatea[0] : '00';
		var x = ( year + month + day ) * 1;
		
		var year = (ukDateb[2]) ? ukDateb[2] : '0000';
		var month = (ukDateb[1]) ? ukDateb[1] : '00';
		var day = (ukDateb[0]) ? ukDateb[0] : '00';
		var y = ( year + month + day ) * 1;
		
		return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
	};
