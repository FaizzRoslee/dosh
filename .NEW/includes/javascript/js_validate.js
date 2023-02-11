// JavaScript Document
	function echeck(str,message) {
		var at="@"
		var dot="."
		var lat=str.indexOf(at)
		var lstr=str.length
		var ldot=str.indexOf(dot)
		/* var message="Not valid E-mail!" */
		
		if (str.indexOf(at)==-1){
		   alert(message)
		   return false
		}

		if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
		   alert(message)
		   return false
		}

		if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		    alert(message)
		    return false
		}

		if (str.indexOf(at,(lat+1))!=-1){
		    alert(message)
		    return false
		}

		if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		    alert(message)
		    return false
		}

		if (str.indexOf(dot,(lat+2))==-1){
		    alert(message)
			return false
		}
		
		if (str.indexOf(" ")!=-1){
			alert(message)
			return false
		}

		return true					
	}

	function numbersonly(myfield, e, dec)	{
		var key;
		var keychar;
		if (window.event)
			key = window.event.keyCode;
		else if (e)
			key = e.which;
		else
			return true;
		keychar = String.fromCharCode(key);
	
		// control keys
		if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) )
			return true;
		// numbers
		else if ((("0123456789").indexOf(keychar) > -1))
			return true;
		// decimal point jump
		else if (dec && (keychar == "."))
		{
			myfield.form.elements[dec].focus();
			return false;
		}
		else	{
			return false;
		}
	}

	var isNN = (navigator.appName.indexOf("Netscape")!=-1);
	
	function autoTab(input,len, e) {
		var keyCode = (isNN) ? e.which : e.keyCode; 
		var filter = (isNN) ? [0,8,9] : [0,8,9,16,17,18,37,38,39,40,46];
		if(input.value.length >= len && !containsElement(filter,keyCode)) {
			input.value = input.value.slice(0, len);
		input.form[(getIndex(input)+1) % input.form.length].focus();
	}
	
	function containsElement(arr, ele) {
		var found = false, index = 0;
		while(!found && index < arr.length)
		if(arr[index] == ele)
			found = true;
		else
			index++;
		
		return found;
	}

	function getIndex(input) {
		var index = -1, i = 0, found = false;
		while (i < input.form.length && index == -1)
		if (input.form[i] == input)index = i;
			else i++;
			return index;
		}
		return true;
	}
