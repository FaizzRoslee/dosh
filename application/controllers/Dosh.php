<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Dosh extends CI_Controller {
    
    function __construct() {
        
       parent::__construct();
        $this->load->library('session');  
       	if (!isset($this -> session -> userdata['admin_in'])) {
                
		header("location: /reportadmin");
              
    	}
            
    }
    function index(){
	$cam = array(
		'isActive' => 1
	);
	$data['cham'] = $this->datasistem->kira('tbl_chemical',$cam)->jumlah;	

	$sub = array(
                'ApprovedDate !=' => null
        );      
        $data['subm'] = $this->datasistem->kira('tbl_submission',$sub)->jumlah;

	$data['visit'] = $this->datasistem->kira('tbl_visitor_counter',null)->jumlah;
	$data['client'] = $this->datasistem->kira('sys_user_client',null)->jumlah;
	$data['terkini'] = $this->datasistem->datatekini();
	$this->load->view('report/panel',$data);
    }
    function laporan1(){

	$this->load->view("report/report1");

    }
    function laporan2(){
	$data['negeri'] = $this->datasistem->list_all('sys_state',null,null)->result();
	$this->load->view("report/report2",$data);
    }
    function laporan3(){
        $data['negeri'] = $this->datasistem->list_all('sys_state',null,null)->result();
        $this->load->view("report/report3",$data);
    }	
    function paparlaporan1(){
        
        $data['senarai'] = $this->datasistem->report($this->input->post('dari'),$this->input->post('hingga'));
	
	//echo $this->input->post('dari');
	//$filename = 'cims_'.date('Ymd').'.csv'; 
	//header("Content-Description: File Transfer"); 
	//header("Content-Disposition: attachment; filename=$filename"); 
	//header("Content-Type: application/csv; ");
	
		
	$this->load->view('report/subreport',$data);

    }
    function paparlaporan2(){
	
	$d1 = $this->datasistem->report2($this->input->post('dari'),$this->input->post('hingga'),$this->input->post('negeri'));

	$d2 = array();
	$bil = 0;
	foreach ($d1 as $d){

		$d2[$bil]['namasyarikat'] = $d->Company_Name;
		$d2[$bil]['alamat'] = $d->Address_Reg;



		$produk = $this->datasistem->get_product($this->input->post('dari'),$this->input->post('hingga'),$d->Usr_ID);
		$kp = 0;
		$d2[$bil]['jumlahp'] = count($produk);		
		foreach($produk as $p){
			$d2[$bil]['produk'][$kp]['nama'] = $p->Detail_ProductName;
			$d2[$bil]['produk'][$kp]['rcorddate'] = $p->rd;
			$d2[$bil]['produk'][$kp]['aprovedate'] = $p->ad;
			$d2[$bil]['produk'][$kp]['total'] = $p->Detail_TotalSupply;
			$d2[$bil]['produk'][$kp]['totaluse'] = $p->Detail_TotalUse;
			$d2[$bil]['produk'][$kp]['ph'] = $p->Category_ID_ph;
			$d2[$bil]['produk'][$kp]['hh'] = $p->Category_ID_hh;
			$d2[$bil]['produk'][$kp]['eh'] = $p->Category_ID_eh;

			$bahan = $this->datasistem->get_bahan($p->Detail_ID);
			
			$kb = 0;
			foreach ($bahan as $b){
				$d2[$bil]['produk'][$kp]['bahan'][$kb]['namabahan'] = $b->Chemical_Name;
				$d2[$bil]['produk'][$kp]['bahan'][$kb]['cas'] = $b->Chemical_CAS;
				$kb++;
			}
			
			$kp++;
		}
		
	 $bil++;
	}

	$data['negeri'] = $this->datasistem->get_negeri($this->input->post('negeri'));
        $data['senarai'] = $d2;


        $this->load->view('report/subreport2',$data);

    }

    function paparlaporan3(){

        $d1 = $this->datasistem->report2($this->input->post('dari'),$this->input->post('hingga'),$this->input->post('negeri'));

        $d2 = array();
        $bil = 0;
        foreach ($d1 as $d){

                $d2[$bil]['namasyarikat'] = $d->Company_Name;
                $d2[$bil]['alamat'] = $d->Address_Reg;
		$d2[$bil]['bandar'] = $d->City_Postal;
                $d2[$bil]['poskod'] = $d->Postcode_Reg;
                $d2[$bil]['phone'] = $d->Phone_No;
                $d2[$bil]['fax'] = $d->Fax_No;
                $d2[$bil]['email'] = $d->Email;
                $d2[$bil]['cname'] = $d->Contact_Name;
                $d2[$bil]['cdesc'] = $d->Contact_Designation;
                $d2[$bil]['cmphone'] = $d->Contact_Mobile_No;
                $d2[$bil]['cemail'] = $d->Contact_Email;


                $produk = $this->datasistem->get_product($this->input->post('dari'),$this->input->post('hingga'),$d->Usr_ID);
                $kp = 0;
                $d2[$bil]['jumlahp'] = count($produk);
                foreach($produk as $p){
                        $d2[$bil]['produk'][$kp]['nama'] = $p->Detail_ProductName;
                        $d2[$bil]['produk'][$kp]['rcorddate'] = $p->rd;
                        $d2[$bil]['produk'][$kp]['aprovedate'] = $p->ad;
                        $d2[$bil]['produk'][$kp]['total'] = $p->Detail_TotalSupply;
                        $d2[$bil]['produk'][$kp]['totaluse'] = $p->Detail_TotalUse;
                        $d2[$bil]['produk'][$kp]['ph'] = $p->Category_ID_ph;
                        $d2[$bil]['produk'][$kp]['hh'] = $p->Category_ID_hh;
                        $d2[$bil]['produk'][$kp]['eh'] = $p->Category_ID_eh;

                        $bahan = $this->datasistem->get_bahan($p->Detail_ID);

                        $kb = 0;
                        foreach ($bahan as $b){
				$d2[$bil]['produk'][$kp]['bahan'][$kb]['namabahan'] = $b->Chemical_Name;
                                $d2[$bil]['produk'][$kp]['bahan'][$kb]['cas'] = $b->Chemical_CAS;
                                $kb++;
                        }

                        $kp++;
                }

         $bil++;
        }

        $data['negeri'] = $this->datasistem->get_negeri($this->input->post('negeri'));
        $data['senarai'] = $d2;


        $this->load->view('report/subreport3',$data);

    }

}
?>
