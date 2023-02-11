<?php
	/*********************************************************************************
		File name		:		func_rand_str.php
		Function name	:		func_rand_str(int_length)
		Parameter		:		int_length	(integer)	eg: 1,2,3..... (default: 64)
		Return			:		(string)
		Date created	:		03 December 2004
		Last modify		:		12 May 2005
		Description		:		func_rand_str function will generate random string for
								a given lenght, if func_rand_str call with arguement 
								a default 64 character random string will be generated.
	**********************************************************************************/
	/*** page protection ***/
	// this protect the user direct access to the page,
	// this should be included in every page, except /index.php (system entry)

	function func_rand_str($int_length=64)
	{
		$str_rand	=	"";
		$str_magic	=	"0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		while(strlen($str_rand)<$int_length)
		{ // generate a magic string
			$str_magic	.= 	md5($str_rand . (string)time() . $str_magic);
			// random pick a character from magic string
			$str_rand	.=	substr($str_magic, rand(0,(strlen($str_magic)-1)),1);
		}
		return $str_rand; // return magic string
	}
?>