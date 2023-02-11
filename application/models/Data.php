<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Data extends CI_Model {

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
        
}