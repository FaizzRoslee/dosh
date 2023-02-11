<?php
	/*********************************************************************************
		File name		:		func_date.php
		Function name	:		func_ymd2dmy(str_date)
								func_dmy2ymd(str_date)
		Parameter		:		str_date	(string)	eg: %d-%m-%Y or %Y-mm-dd
		Return			:		(string)
		Date created	:		1 August 2005
		Last modify		:		1 August 2005
		Description		:		func_ymd2dmy is a function to convert %Y-mm-dd
								to %d-%m-%Y
								func_dmy2ymd is a function to convert %d-%m-%Y
								to %Y-mm-dd
	**********************************************************************************/


	function func_ymd2dmy($str_date = '')	{
	
		$str_date	=	trim($str_date);
		if (empty($str_date) == true)
			return '';
		if ((is_numeric(substr($str_date,0,4)) == true) &&
			(is_numeric(substr($str_date,5,2)) == true) &&
			(is_numeric(substr($str_date,-2)) == true))
			return (substr($str_date,8,2) . "-" . substr($str_date,5,2) . "-" . substr($str_date,0,4));
		else
			return '';
	}

	
	function func_dmy2ymd($str_date = '')	{
	
		$str_date	=	trim($str_date);
		if (empty($str_date) == true)
			return '';
		if ((is_numeric(substr($str_date,0,2)) == true) &&
			(is_numeric(substr($str_date,3,2)) == true) &&
			(is_numeric(substr($str_date,-4)) == true))
			return (substr($str_date,6,4) . "-" . substr($str_date,3,2) . "-" . substr($str_date,0,2));
		else
			return '';
	}


	function func_mdy2ymd($str_date = '')	{
	
		$str_date	=	trim($str_date);
		if (empty($str_date) == true)
			return '';
		if ((is_numeric(substr($str_date,0,2)) == true) &&
			(is_numeric(substr($str_date,3,2)) == true) &&
			(is_numeric(substr($str_date,-4)) == true))
			return (substr($str_date,6,4) . "-" .substr($str_date,0,2). "-" .substr($str_date,3,2));
		else
			return '';
	}
	


	function func_ymd2dmytime($str_date = '')	{
	
		$str_date	=	trim($str_date);
		if (empty($str_date) == true)
			return '';
		if ((is_numeric(substr($str_date,0,4)) == true) &&
			(is_numeric(substr($str_date,5,2)) == true) &&
			(is_numeric(substr($str_date,-2)) == true))
			return (substr($str_date,8,2) . "-" . substr($str_date,5,2) . "-" . substr($str_date,0,4). substr($str_date,10,18));
		else
			return '';
	}

	
	function func_ymd2timestamp($str_date = '', $int_hour = 0, $int_min = 0, $int_second = 0)	{
	
		$str_date	=	trim($str_date);
		if (empty($str_date) == true)
			return '';

		$int_day = substr($str_date,8,2);
		$int_mon = substr($str_date,5,2);
		$int_year = substr($str_date,0,4);

		if ((is_numeric($int_day) == true) &&
			(is_numeric($int_mon) == true) &&
			(is_numeric($int_year) == true))
			return mktime($int_hour,$int_min,$int_second,$int_mon,$int_day,$int_year);
		else
			return '';
	
	}

	
	function func_get_day_desc($int_day = 0)	{
	
		switch($int_day)	{	
			case 0:		
				return 'Ahad';
				break;
			case 1:		
				return 'Isnin';
				break;
			case 2:		
				return 'Selasa';
				break;
			case 3:		
				return 'Rabu';
				break;
			case 4:		
				return 'Khamis';
				break;
			case 5:		
				return 'Jumaat';
				break;
			case 6:		
				return 'Sabtu';
				break;
		}
	}


	function func_get_month_name($int_month = 0)	{
	
		switch($int_month)	{
			case 1:		
				return 'Januari';
				break;
			case 2:		
				return 'Februari';
				break;
			case 3:		
				return 'Mac';
				break;
			case 4:		
				return 'April';
				break;
			case 5:		
				return 'Mei';
				break;
			case 6:		
				return 'Jun';
				break;
			case 7:		
				return 'July';
				break;
			case 8:		
				return 'Ogos';
				break;
			case 9:		
				return 'September';
				break;
			case 10:		
				return 'Oktober';
				break;
			case 11:		
				return 'November';
				break;
			case 12:		
				return 'Disember';
				break;
		}
	}


?>