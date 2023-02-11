<?php
	/*********************************************************************************
	    linuxhouse__20220710
		File name		:		set_time_and_memory_limits.php
		Function name	:		non
		Parameter		:		non
		Return			:		non
		Date created	:		10 July 2022
		Last modify		:		10 July 2022
		Description		:		Sets maximum execution time or 
                                memory limit. This will apply to only individual 
                                scripts using it.
	**********************************************************************************/
	
	$max_execution_time = '600'; // in seconds, 600 = 10 minutes, unlimited = '-1'(but may slow down the server)
	$memory_limit       = '2048M';  // in bytes or MB or 1G, 2048M = 2G, unlimited = '-1'(but may slow down the server)
	

?> 
