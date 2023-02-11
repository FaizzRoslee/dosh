<?php
/*
 * jQuery File Upload Plugin PHP Example
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * https://opensource.org/licenses/MIT
 */

   
         $u_dir = '/var/www/html/upload/login/';
         $u_url = '/upload/login/';
   

    $options = array(
        'upload_dir'=> $u_dir,
        'upload_url'=> $u_url
    );



error_reporting(E_ALL | E_STRICT);
require('UploadHandler.php');
$upload_handler = new UploadHandler($options);
