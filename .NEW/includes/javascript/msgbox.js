function msgbox() 
{
	e = document.form1;
	count=0;
	for(c=0; c<e.elements.length; c++) {
		if(e.elements[c].name=="checkbox[]" && e.elements[c].checked) 
			count++;
	}       
	if(count==0) {
	    alert("Choose record to delete.");
		return false;
	}else {
		if(confirm("Delete " + count + " record(s)?"))
			return true;
		else return false;
	}
}