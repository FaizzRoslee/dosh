<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
	{
                session_start();
                
                if (isset($_SESSION['user'])){	
			header("Location: /authentication/home.php");
			exit();
                }
                else {
                
                $_SESSION['password_field'] = 'txt_' . $this->datasistem->func_rand_str(32);		// magic password field name
                $_SESSION['magic_string'] = $this->datasistem->func_rand_str(64);
        
                $data['link'] = $this->datasistem->get_all('sys_Info','Info_ID','20')->row();
                
                $data['banner'] = $this->datasistem->get_all('sys_info_image','Info_ID','30')->result();
				
				$data['splash'] = $this->datasistem->get_all('sys_info_splash',null,null,'kedudukan ASC')->result();
                
				$this->load->view('welcome_message',$data);
                $this->load->view('footer');
                }
	}
	public function stat(){
			
		session_start();
		
		if (isset($_SESSION["user"])){
			
			$this->load->view('status');
			
		}
		
	}
	public function keputusan(){

		$dari = $this->input->post('dari');
		$hingga = $this->input->post('hingga');
		
		
		$this->load->view('graf');
	}
	function bana(){
		session_start();
                
        if (isset($_SESSION['user'])){
        	
			if ($_SESSION['user']['Level_ID'] == 1){
					
				$data['banner'] = $this->datasistem->get_all('sys_info_image','Info_ID','30')->result();
				
				$this->load->view('banner',$data);
			}
			else {
				echo "Nak buat apa";
			}
		}
	}
	function edit_bana(){
		
		
		session_start();
                
        if (isset($_SESSION['user'])){
        	
			if ($_SESSION['user']['Level_ID'] == 1){
					
				$data['banner'] = $this->datasistem->get_all('sys_info_image','Image_ID',$this->input->post('id'))->row();
				
				//echo $this->input->post('id');
				$this->load->view('edit_banner',$data);
			}
			else {
				echo "Nak buat apa";
			}
		}
		
	}
	function new_bana(){
		
		
		session_start();
                
        if (isset($_SESSION['user'])){
        	
			if ($_SESSION['user']['Level_ID'] == 1){
					

				$this->load->view('new_banner');
			}
			else {
				echo "Nak buat apa";
			}
		}
		
	}
	function del_bana(){
		session_start();
                
        if (isset($_SESSION['user'])){
        	
			if ($_SESSION['user']['Level_ID'] == 1){
					
				//$data['banner'] = $this->datasistem->get_all('sys_info_image','Info_ID','30')->result();
				
				//echo $this->input->post('id');
				//$this->load->view('edit_banner',$data);
				echo $this->datasistem->del('sys_info_image','Image_ID',$this->input->post('id'));
			}
			else {
				echo "Nak buat apa";
			}
		}
	}
	function save_bana(){
		
		
		session_start();
                
        if (isset($_SESSION['user'])){
        	
			if ($_SESSION['user']['Level_ID'] == 1){
					
				if ($this->input->post('jenis') == 1){
					
				$data = array(
					'Info_ID' => 30, 
					'url' => $this->input->post('url'),
					'Image_URL' => $this->input->post('gambar'),
					'RecordBy' => 1,
					'keterangan' => $this->input->post('info')
				);	
				echo $this->datasistem->add('sys_info_image',$data);
				}
				if ($this->input->post('jenis') == 2){
					
				$data = array(
					'Info_ID' => 30, 
					'url' => $this->input->post('url'),
					'Image_URL' => $this->input->post('gambar'),
					'RecordBy' => 1,
					'keterangan' => $this->input->post('info')
				);	
				echo $this->datasistem->edit('sys_info_image',$data,'Image_ID',$this->input->post('id'));
				}

			}
			else {
				echo "Nak buat apa";
			}
		}
		else {
			echo "kehadiran anda tidak di undang !!!";
		}
		
	}
	function splash(){
		session_start();
                
        if (isset($_SESSION['user'])){
        	
			if ($_SESSION['user']['Level_ID'] == 1){
							
			
				$data['splash'] = $this->datasistem->get_all('sys_info_splash',null,null,'kedudukan ASC')->result();
		
				$this->load->view('splash',$data);	
			}
		}
	}
	function del_splash(){
		session_start();
                
        if (isset($_SESSION['user'])){
        	
			if ($_SESSION['user']['Level_ID'] == 1){
					echo "test";
			}
		}
	}
	function form_splash(){
		session_start();
                
        if (isset($_SESSION['user'])){
        	
			if ($_SESSION['user']['Level_ID'] == 1){
					echo "test";
			}
		}
	}
	function save_splash(){
		session_start();
                
        if (isset($_SESSION['user'])){
        	
			if ($_SESSION['user']['Level_ID'] == 1){
					echo "test";
			}
		}

	}
}
