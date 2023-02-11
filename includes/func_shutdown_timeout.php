<?php
	/*********************************************************************************
	    linuxhouse__20220710
		File name		:		func_shutdown_timeout.php
		Function name	:		func_shutdown_timeout()
		Parameter		:		non
		Return			:		non
		Date created	:		10 July 2022
		Last modify		:		10 July 2022
		Description		:		Closes page when noticed 'Maximum execution time' or 
                                'Allowed memory size of' has exceeded the actual limit.
	**********************************************************************************/


    function func_shutdown_timeout() 
    { 
        $error = error_get_last();

/*        if ($error['type'] === E_ERROR) {

        //do your shutdown stuff here
        //be care full do not call any other function from within shutdown function
        //as php may not wait until that function finishes
        //its a strange behavior. During testing I realized that if function is called 
        //from here that function may or may not finish and code below that function
        //call may or may not get executed. every time I had a different result. 

        // e.g.

        // other_function();

        //code below this function may not get executed
        
        echo '<script>alert("'._LBL_TIME_LIMIT_EXCEEDED.'")</script>';
        exit();

        }*/ 
        
        
        // types of error messages
        $time_limit_error_message = 'Maximum execution time of';
        $memory_limit_error_message = 'Allowed memory size of';
        
        
        // checks if error message is available
        if (isset($error['type']))
        {
            // checks the type of error message gotten and close or refresh window
            if ($error['type'] === E_ERROR && strpos($error['message'], $time_limit_error_message) === 0) {
                echo '<script type="text/javascript">
                    if (confirm("'._LBL_TIME_LIMIT_EXCEEDED.'")){
                        window.history.back();
                    }
                    else
                    {
                        window.close();
                    }
                    </script>';
            }
            
            
            if ($error['type'] === E_ERROR && strpos($error['message'], $memory_limit_error_message) === 0) {
                echo '<script type="text/javascript">
                    if (confirm("'._LBL_MEMORY_LIMIT_EXCEEDED.'")){
                        window.history.back();
                    }
                    else
                    {
                        window.close();
                    }
                    </script>';
            }
            
            
        }
        

    }
?> 

