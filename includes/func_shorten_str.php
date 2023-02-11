<?php
	/*********************************************************************************
	    linuxhouse__20220701
		File name		:		func_shorten_str.php
		Function name	:		func_shorten_str(string, integer)
		Parameter		:		int	(integer)	eg: 1,2,3..... 
		Return			:		(string)
		Date created	:		01 July 2022
		Last modify		:		01 July 2022
		Description		:		Returns the first $wordsreturned out of $string.
								If stringcontains more words than $wordsreturned,
								the entire string is returned.
	**********************************************************************************/
	/*** page protection ***/
	// this protect the user direct access to the page,
	// this should be included in every page, except /index.php (system entry)

    function func_shorten_str($string, $wordsreturned)
    {
        $retval = $string;	//	Just in case of a problem
        $array = explode(" ", $string);
        /*  Already short enough, return the whole thing*/
        if (count($array)<=$wordsreturned)
        {
            $retval = $string;
        }
        /*  Need to chop off some words*/
        else
        {
            array_splice($array, $wordsreturned);
            $retval = implode(" ", $array)." ...";
        }
        return $retval;
    }
?> 
