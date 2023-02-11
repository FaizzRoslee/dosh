<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends CI_Controller {
    
    
    function about(){
        
        
        $data['info'] = $this->datasistem->get_all('sys_info','Info_ID','2')->row();
		//$data['banner'] = $this->datasistem->get_all('sys_info_image','Info_ID','30')->row();
        
        $this->load->view('page',$data);
        
    }
    
}
