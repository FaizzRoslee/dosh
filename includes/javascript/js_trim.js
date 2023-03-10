function Trim(TRIM_VALUE)
{
	if(TRIM_VALUE.length <= 0)
		return "";
    
	TRIM_VALUE = RTrim(TRIM_VALUE);
    TRIM_VALUE = LTrim(TRIM_VALUE);
	return TRIM_VALUE;
}

function RTrim(VALUE)
{
	var w_space = String.fromCharCode(32);
	var v_length = VALUE.length;
    var strTemp = "";
	
	if(v_length <= 0)
		return"";
    
	var iTemp = v_length -1;
	
	while(iTemp > -1)
	{
		if(VALUE.charAt(iTemp) != w_space)
		{
			strTemp = VALUE.substring(0,iTemp +1);
			break;
		}
		iTemp = iTemp-1;
	}
    
	return strTemp;
}

function LTrim(VALUE)
{
	var w_space = String.fromCharCode(32);
	var v_length = VALUE.length;
	var strTemp = "";
	
	if(v_length <= 0)
		return"";

	var iTemp = 0;

	while(iTemp < v_length)
	{
		if(VALUE.charAt(iTemp) != w_space)
		{
			strTemp = VALUE.substring(iTemp,v_length);
			break;
		}
		iTemp = iTemp + 1;
	}
	
    return strTemp;
}

function FTrim(VALUE)
{
	strTemp = Trim(VALUE);
	while (strTemp.search(' ') > 0)
		strTemp = strTemp.replace(' ','');
	return strTemp;
}