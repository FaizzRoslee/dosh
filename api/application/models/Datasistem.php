<?php

class Datasistem extends CI_Model{
	
	var $table = "tbl_Submission";
	
	function get_sub($set=null){
		
		$levelID = $_SESSION["user"]["Level_ID"];
		$Usr_ID = $_SESSION["user"]["Usr_ID"];
			
		$had = $this->input->get('iDisplayLength');
	
		$cari_umum = $this->input->get('sSearch');
		
		
		if ($cari_umum !== ''){
			$this->db->like('Company_Name',$cari_umum);
			$this->db->or_like('Level_Name',$cari_umum);
			$this->db->or_like('Type_Name',$cari_umum);
			$this->db->or_like('Status_Desc',$cari_umum);
		}
		if ($this->input->get('iSortCol_0') !== null){
				
			if ($this->input->get('iSortCol_0') == 1){
				$this->db->order_by('Submission_Submit_ID',$this->input->get('sSortDir_0'));
			}
			
			else if ($this->input->get('iSortCol_0')== 2){
				$this->db->order_by('Company_Name',$this->input->get('sSortDir_0'));
			}
			else if ($this->input->get('iSortCol_0')== 3){
				$this->db->order_by('Level_Name',$this->input->get('sSortDir_0'));
			}
			else if ($this->input->get('iSortCol_0')== 4){
				$this->db->order_by('Type_Name',$this->input->get('sSortDir_0'));
			}
			else if ($this->input->get('iSortCol_0')== 5){
				//$this->db->order_by('UpdateDate',$this->input->get('sSortDir_0'));
			}
			else if ($this->input->get('iSortCol_0')== 6){
				$this->db->order_by('BulkSubmission',$this->input->get('sSortDir_0'));
			}
			else if ($this->input->get('iSortCol_0')== 7){
				$this->db->order_by('Status_Desc',$this->input->get('sSortDir_0'));
			}
			else{
				
			}
		} 
		
		
		if ($this->input->get('sSearch_1') !== ''){
			$this->db->like('Submission_Submit_ID',$this->input->get('sSearch_1'));
		}
		if ($this->input->get('sSearch_2') !== ''){
			$this->db->like('Company_Name',$this->input->get('sSearch_2'));
		}
		if ($this->input->get('sSearch_3') !== ''){
			$this->db->like('Level_Name',$this->input->get('sSearch_3'));
		}
		if ($this->input->get('sSearch_4') !== ''){
			$this->db->like('Type_Name',$this->input->get('sSearch_4'));
		}
		//if ($this->input->get('sSearch_5') !== ''){
			//$this->db->like('UpdateDate',$this->input->get('sSearch_5'));
		//}
		if ($this->input->get('sSearch_6') !== ''){
			$this->db->like('BulkSubmission',$this->input->get('sSearch_6'));
		}
		if ($this->input->get('sSearch_7') !== ''){
			$this->db->like('Status_Desc',$this->input->get('sSearch_7'));
		}
		
		
		if ($this->input->get('jenis') !== 'all'){
				
			if($this->input->get('jenis') == '11'){
					
				$this->db->where('tbl_Submission.Status_ID',$this->input->get('jenis'));
					
				if($levelID==6 || $levelID==7 || $levelID==8){
					$this->db->where($this->table.'.Usr_ID',$Usr_ID);
				}
				
			}
			else if($this->input->get('jenis') == '21'){
				
				$this->db->where('tbl_Submission.Status_ID',$this->input->get('jenis'));
				
				if($levelID==6 || $levelID==7 || $levelID==8){
					$this->db->where($this->table.'Usr_ID',$Usr_ID);
				}
				else if($levelID==3){
					$this->db->where($this->table.'.CheckedBy',$Usr_ID);
				}
				else if($levelID==4){
					$this->db->where('sys_User_Staff.User_Head',$Usr_ID);
				}		
			}
			else if($this->input->get('jenis') == 'approved'){
				
				$stat = array(31,32,33,51);
				$this->db->where('tbl_Submission.isDeleted',0);
				$this->db->where_in('tbl_Submission.Status_ID',$stat);
				
				
			}
			else if($this->input->get('jenis') == 'approvedstat'){
				
				$stat = array(31,32,33,51);
				$this->db->where('tbl_Submission.isDeleted',0);
				$this->db->where_in('tbl_Submission.Status_ID',$stat);
				
				
				if($levelID==5){
					
					$stat2 = array(14,16);
						 
					//$state  = $this->get_stat();				

					//if($state==14 || $state==16){
						//$stat3 = array(4,16);
						//$this->db->where_in('sys_User_Client.State_Reg',$stat3);
					//}
					//else {
					$this->db->where_in('sys_User_Client.State_Reg',$stat2);
					//}
	
				}
				 
				
			}
			else {	
			$this->db->where('tbl_Submission.Status_ID',$this->input->get('jenis'));
			}
		}
		//else if($levelID <5){
			
		//}
		else{
			$this->db->where('tbl_Submission.Usr_ID',$Usr_ID);
		}
		
		$offset = $this->input->get('iDisplayStart');
		
		$this->db->select('*,(SELECT COUNT(*) FROM tbl_Submission_Detail WHERE tbl_Submission.Submission_ID=tbl_Submission_Detail.Submission_ID) AS total'); 
		$this->db->join('sys_User','tbl_Submission.Usr_ID = sys_User.Usr_ID','left');
		$this->db->join('sys_User_Client','tbl_Submission.Usr_ID = sys_User_Client.Usr_ID','left');
		$this->db->join('tbl_Chemical_Type','tbl_Submission.Type_ID = tbl_Chemical_Type.Type_ID','left');
		$this->db->join('tbl_Submission_Status','tbl_Submission.Status_ID = tbl_Submission_Status.Status_ID','left');
		$this->db->join('sys_Level','sys_User.Level_ID = sys_Level.Level_ID','left');
		//$this->db->limit($had);	
		return $this->db->get('tbl_Submission',$had,$offset);
		
		
	}
	function kira_jumlah($filter = null,$filter2=null){
		
		if ($filter !== null){
				
			$this->db->where($filter);
		}
		if ($filter2 !== null){
			$this->db->where_in($filter2);
		}
		$levelID = $_SESSION["user"]["Level_ID"];
		$Usr_ID = $_SESSION["user"]["Usr_ID"];
			
		if($levelID <5){
			
		}
		else{
			$this->db->where('tbl_Submission.Usr_ID',$Usr_ID);
		}

        return $this->db->get($this->table)->num_rows();
	}
	function HeadForApproval($id=null){
		
	
		$this->db->select('User_Head');
		$this->db->where('User_ID',$id);
		$hasil = $this->db->get('sys_User_Staff');
		
		if($hasil->num_rows()>0){
				
			$hantar = $hasil->row();
			return $hantar->User_Head;
		}
		else{
		return "";
		}
	}
	function get_stat(){
		$Usr_ID = $_SESSION["user"]["Usr_ID"];
		$this->db->select('State_ID')->where('Usr_ID',$Usr_ID)->get('sys_User_Staff');
	}
	function map(){
		//SELECT *,(SELECT Company_Name FROM sys_user_client WHERE sys_user_client.Usr_ID=tbl_submission.Usr_ID ) FROM `tbl_submission` LEFT JOIN tbl_submission_detail ON tbl_submission.Submission_ID=tbl_submission_detail.Submission_ID LEFT JOIN tbl_submission_chemical ON tbl_submission_chemical.Detail_ID=tbl_submission_detail.Detail_ID WHERE tbl_submission_chemical.Chemical_ID=59867 AND tbl_submission.Status_ID IN (31,32,33,51,41,42,43,52) LIMIT 10
		return $this->db->query('SELECT *,(SELECT Company_Name FROM sys_user_client WHERE sys_user_client.Usr_ID=tbl_submission.Usr_ID ) FROM `tbl_submission` LEFT JOIN tbl_submission_detail ON tbl_submission.Submission_ID=tbl_submission_detail.Submission_ID LEFT JOIN tbl_submission_chemical ON tbl_submission_chemical.Detail_ID=tbl_submission_detail.Detail_ID WHERE tbl_submission_chemical.Chemical_ID=59867 LIMIT 10');
		//$this->db->limit('10');
		//return $this->db->query();
		
		
		//SELECT * FROM `tbl_submission`  
		//LEFT JOIN tbl_submission_detail ON tbl_submission.Submission_ID=tbl_submission_detail.Submission_ID  
		//LEFT JOIN tbl_submission_chemical ON tbl_submission_chemical.Detail_ID=tbl_submission_detail.Detail_ID 
		//WHERE tbl_submission_chemical.Chemical_ID=314 AND tbl_submission.Status_ID 
		//IN (31,32,33,51,41,42,43,52) LIMIT 10
	}
        function get_chemical(){
            $q = "SELECT c.Chemical_CAS,c.Chemical_Name,c.Chemical_IUPAC ,(SELECT count(1) FROM `tbl_submission` LEFT JOIN tbl_submission_detail ON tbl_submission.Submission_ID=tbl_submission_detail.Submission_ID LEFT JOIN tbl_submission_chemical ON tbl_submission_chemical.Detail_ID=tbl_submission_detail.Detail_ID WHERE tbl_submission_chemical.Chemical_ID=sc.Chemical_ID AND tbl_submission.Status_ID IN (31,32,33,51,41,42,43,52) ) AS total FROM tbl_Submission_Chemical sc LEFT JOIN tbl_Chemical c ON sc.Chemical_ID = c.Chemical_ID LEFT JOIN tbl_Submission_Detail sd ON sc.Detail_ID = sd.Detail_ID WHERE 1 AND sd.Submission_ID IN (SELECT Submission_ID FROM tbl_Submission s WHERE 1 AND s.isDeleted = 0 AND s.Status_ID IN (31,32,33,51,41,42,43,52)) AND isCWC = 0 LIMIT 10";
            return $this->db->query($q)->result_array();
            //return $this->db->get()->result();
        }
} 