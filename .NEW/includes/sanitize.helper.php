<?php
/*

    Sanitize class

    Copyright (C) 2007 CodeAssembly.com



    This program is free software: you can redistribute it and/or modify

    it under the terms of the GNU General Public License as published by

    the Free Software Foundation, either version 3 of the License, or

    (at your option) any later version.



    This program is distributed in the hope that it will be useful,

    but WITHOUT ANY WARRANTY; without even the implied warranty of

    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

    GNU General Public License for more details.



    You should have received a copy of the GNU General Public License

    along with this program.  If not, see http://www.gnu.org/licenses/

*/

/**

 * Sanitize only one variable .

 * Returns the variable sanitized according to the desired type or true/false

 * for certain data types if the variable does not correspond to the given data type.

 *

 * NOTE: True/False is returned only for telephone, pin, id_card data types

 *

 * @param mixed The variable itself

 * @param string A string containing the desired variable type

 * @return The sanitized variable or true/false

 */



function sanitizeOne($var, $type)

{

        switch ( $type ) {

                        case 'int': // integer

                        $var = (int) $var;

                        break;



                        case 'str': // trim string

                        $var = trim ( $var );

                        break;



                        case 'nohtml': // trim string, no HTML allowed

                        $var = htmlentities ( trim ( $var ), ENT_QUOTES );

                        break;



                        case 'plain': // trim string, no HTML allowed, plain text

                        $var =  htmlentities ( trim ( $var ) , ENT_NOQUOTES )  ;

                        break;



                        case 'upper_word': // trim string, upper case words

                        $var = ucwords ( strtolower ( trim ( $var ) ) );

                        break;



                        case 'ucfirst': // trim string, upper case first word

                        $var = ucfirst ( strtolower ( trim ( $var ) ) );

                        break;



                        case 'lower': // trim string, lower case words

                        $var = strtolower ( trim ( $var ) );

                        break;



                        case 'urle': // trim string, url encoded

                        $var = urlencode ( trim ( $var ) );

                        break;



                        case 'trim_urle': // trim string, url decoded

                        $var = urldecode ( trim ( $var ) );

                        break;



                        case 'telephone': // True/False for a telephone number

                        $size = strlen ($var) ;

                        for ($x=0;$x<$size;$x++)

                        {

                                if ( ! ( ( ctype_digit($var[$x] ) || ($var[$x]=='+') || ($var[$x]=='*') || ($var[$x]=='p')) ) )

                                {

                                        return false;

                                }

                        }

                        return true;

                        break;



                        case 'pin': // True/False for a PIN

                        if ( (strlen($var) != 13) || (ctype_digit($var)!=true) )

                        {

                                return false;

                        }

                        return true;

                        break;



                        case 'id_card': // True/False for an ID CARD

                        if ( (ctype_alpha( substr( $var , 0 , 2) ) != true ) || (ctype_digit( substr( $var , 2 , 6) ) != true ) || ( strlen($var) != 8))

                        {

                                return false;

                        }

                        return true;

                        break;



                        case 'sql': // True/False if the given string is SQL injection safe

                        //  insert code here, I usually use ADODB -> qstr() but depending on your needs you can use mysql_real_escape();

                        return mysqli_real_escape_string($db->db_connect_id, $var);

                        break;

                }

        return $var;



}





/**

 * Sanitize an array.

 *

 * sanitize($_POST, array('id'=>'int', 'name' => 'str'));

 * sanitize($customArray, array('id'=>'int', 'name' => 'str'));

 *

 * @param array $data

 * @param array $whatToKeep

 */



function sanitize( &$data, $whatToKeep )

{

        $data = array_intersect_key( $data, $whatToKeep );



        foreach ($data as $key => $value)

        {

                $data[$key] = sanitizeOne( $data[$key] , $whatToKeep[$key] );

        }

}

?>