<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function panel(){
		echo "helooo";
	}
	public function old()
	{
		//$this->load->view('welcome_message');
		$iTotal = 500;
		$iFilteredTotal = 500;
		
		$output = array(
		"sEcho" => intval($_GET["sEcho"]),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,
		"aaData" => array()
		);
	
		
		
		$output["aaData"] = $this->datasistem->test()->result();
		
		echo json_encode( $output );
	}
	public function index(){
		
		session_start();
		
		if (isset($_SESSION["user"])){
			
		$Usr_ID = $_SESSION["user"]["Usr_ID"];
		$levelID = $_SESSION["user"]["Level_ID"];
		$colTypeName = $_GET["type"];
		
		if ($this->input->get('jenis') == 'all'){
	 			
	 		$ikira = $this->datasistem->kira_jumlah();
			$iFilteredTotal = $this->datasistem->kira_jumlah();
		}
		else if ($this->input->get('jenis') == 'approved'){
				
			$filter = array(
				
				'tbl_Submission.isDeleted' => 0
			);
			
			$filter2 = array(
				'tbl_Submission.Status_ID' => array(31,32,33,51)
			);
			
			$ikira = $this->datasistem->kira_jumlah($filter);
			$iFilteredTotal = $this->datasistem->kira_jumlah($filter);
		}
		else if ($this->input->get('jenis') == 'approved'){
				
			$filter = array(
				
				'tbl_Submission.isDeleted' => 0
			);
			
			$filter2 = array(
				'tbl_Submission.Status_ID' => array(31,32,33,51)
			);
			
			$ikira = $this->datasistem->kira_jumlah($filter);
			$iFilteredTotal = $this->datasistem->kira_jumlah($filter);
		}
		else if ($this->input->get('jenis') == 'approvedstat'){
				
			$filter = array(
				
				'tbl_Submission.isDeleted' => 0
			);
			
			$filter2 = array(
				'tbl_Submission.Status_ID' => array(14,16)
			);
			
			$ikira = $this->datasistem->kira_jumlah($filter);
			$iFilteredTotal = $this->datasistem->kira_jumlah($filter);
		}   
		else {
			
			
			$filter = array(
				'tbl_Submission.Status_ID' => $this->input->get('jenis')
			);
			$ikira = $this->datasistem->kira_jumlah($filter);
			$iFilteredTotal = $this->datasistem->kira_jumlah($filter);
		}
		
		
		
		$list = $this->datasistem->get_sub()->result();
        $data = array();
        foreach ($list as $s) {
            $statusID = $s->Status_ID;
			$uniqueID = $s->Status_ID;
            $row = array();
			if($levelID==1){
				if($statusID==61||$statusID==62){
					$chbx = '&nbsp;';
				}
				else{	
					$chbx = '&nbsp;<input type="checkbox" name="checkbox[]" value="'.$s->Submission_ID.'" class="cls-chck" />';
				}
			}
			else {
				
			$chbx = '<input type="checkbox" name="checkbox[]" value="'.$uniqueID.'" '. (($statusID==1||$statusID==2)?'class="cls-chck"':'disabled') .' />';	
				
			}
			
            $row[] = $chbx;
			if ($s->Submission_Submit_ID == ''){
			$row[] = '-';
			}
			else {
            $row[] = $s->Submission_Submit_ID;
			}
			if($levelID<=5){
            $row[] = $s->Company_Name;
            $row[] = $s->Level_Name;
			}
			
			if($this->input->get('jenis') == 'approvedstat'){
			$row[] = $s->total;
			$row[] = $s->UpdateDate;
			}
			else {
            $row[] = $s->Type_Name;
			$row[] = $s->UpdateDate;
			$row[] = $s->BulkSubmission;
			}
            
            
			$row[] = $s->Status_Desc;
			$row[] = '-';
			
					//$statusID = $s->Status_ID;
					$activity = "";
					/*********/
					$imgParam1 = "/images/icons/preview.gif";
					$imgParam2 = 'View';
					if($levelID == 5){
					$imgParam3 = "redirectFormIe('submissionView_state.php?submissionID=$s->Submission_ID&new=1')";	
					}
					else {
					$imgParam3 = "redirectFormIe('submissionView.php?submissionID=$s->Submission_ID&new=1')";
					}
					$activity .= $this->imagebutton($imgParam1,$imgParam2,$imgParam3);
					
					if(($statusID==1 || $statusID==2) && ($levelID<=2 || $levelID>=6)){
						$imgParam1 = "/images/icons/edit.gif";
						$imgParam2 = 'Edit';
						$imgParam3 = "redirectForm('submissionAddEdit.php?submissionID=$s->Submission_ID&new=1')";
						$activity .= "&nbsp;".$this->imagebutton($imgParam1,$imgParam2,$imgParam3);
					}
					
					if($statusID==11 && $levelID<=3){
						$imgParam1 = "/images/icons/check.gif";
						$imgParam2 = 'Checking';
						$imgParam3 = "redirectForm('submissionChecking.php?submissionID=$s->Submission_ID')";
						$activity .= "&nbsp;".$this->imagebutton($imgParam1,$imgParam2,$imgParam3);
					}
			
					$checkHead = $this->datasistem->HeadForApproval($s->CheckedBy);
					if($statusID==21 && (($levelID==4 && $Usr_ID==$checkHead) || $levelID<=2)){
						$imgParam1 = "/images/icons/verify.gif";
						$imgParam2 = "Approve";
						$imgParam3 = "redirectForm('submissionApprove.php?submissionID=$uniqueID')";
						$activity .= "&nbsp;".$this->imagebutton($imgParam1,$imgParam2,$imgParam3);
					}
					
					if(in_array($statusID,array(31,32,33,41,42,43))){
						$url_pdf = "/report/submissionApproved_pdf.php?submissionID=".$uniqueID;
						$imgParam1 = 'Certification';
						$imgParam2 = "funcPopup('$url_pdf',1000,600)";
						$imgParam3 = array("class"=>"cl_image");
						$activity .= "&nbsp;".$this->get_imagedoc($imgParam1,$imgParam2,$imgParam3);
					}
					
					$row[] = $activity;
			
			
 
            $data[] = $row;
        }
 		
        $output = array(
                        "sEcho" => intval($_GET["sEcho"]),
                        "iTotalRecords" => $ikira,
                        "iTotalDisplayRecords" => $iFilteredTotal,
                        "aaData" => $data,
                );
        //output to json format
        //$output["aaData"] = $output;
        echo json_encode($output);
        //print_r($list);
        }
	}
        function chamical(){
            
            //print_r($this->datasistem->get_chemical());
            
            
        }
	function imagebutton($imgPath='',$titleAlt='',$func=''){
		$imgBtn = "<img src=\"".$imgPath."\" title=\"".$titleAlt."\" alt=\"".$titleAlt."\""
		." style=\"cursor:hand;cursor:pointer;\" onClick=\"".$func."\" />";
		
		
		return $imgBtn;
	}
	function get_imagedoc($titleAlt='',$func='',$others=array()){
		/*
		$img = "/images/";
		$imgBtn = "<img src=\"".$img."icons/download.gif\" title=\"".$titleAlt."\" alt=\"".$titleAlt."\""
				. " style=\"cursor:hand;cursor:pointer;\" onClick=\"".$func."\"";
		if(is_array($others)){
			foreach($others as $attribute => $detail)
				$imgBtn .= ' '.$attribute.'="'.$detail.'"';
		}
		$imgBtn .= " />";
		
		return $imgBtn;
                 * *
                 */
	}
	
	function test(){
		session_start();
		
		echo $Usr_ID = $_SESSION["user"]["Usr_ID"];
		echo $levelID = $_SESSION["user"]["Level_ID"];
		
		//print_r($_SESSION['']);
	}
	function db_map(){
		
		//$hasil = $this->datasistem->map()->result();
		
		//print_r($hasil);
	}
	function padam_mix(){
            
                session_start();
		
		if (isset($_SESSION["user"])){
			
		$Usr_ID = $_SESSION["user"]["Usr_ID"];
		$levelID = $_SESSION["user"]["Level_ID"];
                
                    echo "berjaya ".$Usr_ID;
                    
                    $tables = array('tbl_submission_detail', 'tbl_submission_chemical');
                    $this->db->where('Detail_ID', $this->input->post('id'));
                    $this->db->delete($tables);
                
                }
                else {
                    echo "No Login";
                }
                
            
        }
        function padam_cem(){
            
                session_start();
		
		if (isset($_SESSION["user"])){
			
		$Usr_ID = $_SESSION["user"]["Usr_ID"];
		$levelID = $_SESSION["user"]["Level_ID"];
                
                    echo "berjaya ".$Usr_ID;
                    
                    $tables = array('tbl_submission_detail', 'tbl_submission_chemical');
                    $this->db->where('Detail_ID', $this->input->post('id'));
                    $this->db->delete($tables);
                
                }
                else {
                    echo "No Login";
                }
                
            
        }
}
