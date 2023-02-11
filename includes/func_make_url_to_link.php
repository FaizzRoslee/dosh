<?php
	/*********************************************************************************
	    linuxhouse__20220701
		File name		:		func_make_url_to_link.php
		Function name	:		func_make_url_to_link(string)
		Parameter		:		string	(integer)	eg: "this is www.google.com"
		Return			:		(string)
		Date created	:		01 July 2022
		Last modify		:		01 July 2022
		Description		:		Find URLs in a string and make clickable links which
                                opens in a new tab.
	**********************************************************************************/
	
	
    function func_make_url_to_link($string) 
    {
        // The Regular Expression filter
        $reg_pattern = "/(((http|https|ftp|ftps)\:\/\/)|(www\.))[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\:[0-9]+)?(\/\S*)?/";
        
        // make the urls to hyperlinks
        return preg_replace($reg_pattern, '<a href="$0" target="_blank" rel="noopener noreferrer">$0</a>', $string);
    }
	
?>
