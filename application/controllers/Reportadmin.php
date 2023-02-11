<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Reportadmin extends CI_Controller {



	function index(){
		$_SESSION['password_field'] = 'txt_' . $this->datasistem->func_rand_str(32);	
		$this->load->view("report/login");

	}
	public function checklogin(){
		
  
		$maklumat = $this->ldap->check_login_admin($this->input->post('id'),$this->input->post('pass'));
                
  $checkid = $this->datasistem->list_all('admin_user','admin_name',$maklumat["username"])->num_rows();
   
       
                    $str_username = $this->input->post('id');
                    $str_password = $this->input->post('pass');                    
                    
                   	$str_username		=	trim($str_username); // username
                    $str_password		=	trim($str_password); // password    
                
                if ($checkid==0) {
		                
                   
                              

                     if (!empty($str_username) || !empty($str_password)) 
                     {
                     
                            
                            if ($str_username == "HASNAN" || $str_username == "SAIFULIDRIS" ) 
                            {
                             
                                         if ( $str_password == "cimsjkkpreport" )
                                          
                                          {
                                             
                                             $this->load->library('session');		
                                             $this->session->set_userdata('admin_in',$maklumat);
                                             
                                             echo "Successfully Logged in...";
                                             
                                          } else 
                                          {
                                           
                                           echo " Wrong Username or Password. Please try again";
                                           
                                          }
                             
                             
                            }else
                            {
                             
                             echo " Wrong Username or Password. Please try again...";
                             
                            }
                            
                     
                     }else { 

                            echo " Username or Password Cannot be EMPTY.";

                            }
                                          

                } else {
                        echo 0;
		}
		
                
	}
        public function logout(){
            $this->load->library('session'); 
            echo $this->session->unset_userdata('admin_in');
            echo $this -> session -> userdata['admin_in']['username'];
            header("location: /reportadmin");
        }

}

?>
