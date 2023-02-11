	(function($) {
		redirectFormIe = function(url){
			location.href = url;
		}
	})(jQuery);
	
	function redirectForm(url){
		location.href = url;
	}
        function redirectFormtab(url,nama){
		//location.href = url;
                window.open(url, nama); 

	}
	
	function funcPopup(url,w,h){
		x = 100;
		y = 100;
		statusWindow = "resizable=no,toolbar=no,statusbar=yes,scrollbars=yes,directories=no,menubar=no,maximize=no,width="+ w +",height="+ h +",top="+y+",left="+x;
		mywindow = window.open(url,"popup",statusWindow);
		mywindow.moveTo(80,80);
		if (window.focus) {mywindow.focus()}
	}
	
	function disableEnterKey(e){
		var key;

		if(window.event)
			key = window.event.keyCode;     //IE
		else
			key = e.which;     //firefox

		if(key == 13) return false;
		else return true;
	}
	
	function func_checkAll(chk_obj)	{
		e = document.myForm;
		len = e.elements.length;
		var i;
		for (i=0; i < len; i++) {
			if (e.elements[i].name=='checkbox[]') {
				e.elements[i].checked=chk_obj.checked;
			}
		}
	}
	
	function confirm_msgbox() {
		e = document.myForm;
		count=0;
		for(c=0; c<e.elements.length; c++) {
			if(e.elements[c].name=="checkbox[]" && e.elements[c].checked) 
				count++;
		}       
		if(count==0)  {
			alert("Choose record to delete.");
			return false;
		} else  {
			if(confirm("Delete " + count + " record(s)?"))
				return true;
			else return false;
		}
	}