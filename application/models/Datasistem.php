<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Datasistem extends CI_Model {

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
        
        function get_all($table=null,$filter =null,$cari=null,$orderby=null){
       
            if ($filter != null){
                $this->db->where($filter,$cari);  
            }

            if($orderby != null){
             $this->db->order_by($orderby);
            }

            return $this->db->get($table);
        }

	function get_negeri($id=null){

		$this->db->where('State_ID',$id);
		return $this->db->get('sys_state')->row()->State_Name;
		
	}
        function add($table=null,$data = null){
        
        	$this->db->insert($table,$data);
		
			return $this->db->insert_id();
        
        }
		function del($table=null,$idname=null,$id=null){
				
			$this->db->where($idname,$id);
			return $this->db->delete($table);
		}
		function edit($table=null,$data=null,$idname=null,$id=null){
				
			$this->db->where($idname,$id);
			return $this->db->update($table,$data);
		}
		function report($dari=null,$hingga=null){

			$q = $this->db->query("SELECT SQL_CALC_FOUND_ROWS 
			s.RecordDate,
			s.Submission_Submit_ID,
			uc.Company_Name,
			uc.Address_Reg,
			uc.Postcode_Reg,
			uc.City_Postal,
			uc.Phone_No,
			uc.Fax_No,
			uc.Email,
			uc.Contact_Name,
			uc.Contact_Designation,
			uc.Contact_Mobile_No,
			uc.Contact_Email,
			(SELECT State_Name FROM sys_state WHERE State_ID = uc.State_Postal) AS negeri,
			(SELECT GROUP_CONCAT(Detail_ProductName) FROM tbl_submission_detail tsd WHERE tsd.Submission_ID=s.Submission_ID AND Detail_ProductName IS NOT NULL) AS bahan,
			(SELECT GROUP_CONCAT(Detail_ID) FROM tbl_submission_detail tsd WHERE tsd.Submission_ID=s.Submission_ID AND Detail_ProductName IS NULL) AS bahan_rujukan,
			(SELECT SUM(Detail_TotalUse) FROM tbl_submission_detail tsd WHERE tsd.Submission_ID=s.Submission_ID) AS jumlah,
			(SELECT SUM(Detail_TotalSupply) FROM tbl_submission_detail tsd WHERE tsd.Submission_ID=s.Submission_ID) AS jumlah_i,
			(SELECT GROUP_CONCAT(Detail_ID) FROM tbl_submission_detail tsd WHERE tsd.Submission_ID=s.Submission_ID ) AS bahan_dtl,
			sl.Level_Name,
			ct.Type_Name,
			Quantity,
			Disclaimer,
			ss.Status_Desc,
			s.CheckedBy,
			s.Status_ID,
			s.Submission_ID 
			FROM tbl_Submission s 
			LEFT JOIN sys_User u ON s.Usr_ID = u.Usr_ID 
			LEFT JOIN sys_User_Client uc ON s.Usr_ID = uc.Usr_ID  
			LEFT JOIN tbl_Chemical_Type ct ON s.Type_ID = ct.Type_ID  
			LEFT JOIN tbl_Submission_Status ss ON s.Status_ID = ss.Status_ID  
			LEFT JOIN sys_Level sl ON u.Level_ID = sl.Level_ID 
			WHERE 1  
			AND s.isDeleted = 0 
			AND s.Status_ID IN (31,32,33,51) 
			AND s.RecordDate <= '$hingga'
			AND s.RecordDate >= '$dari'");


			return $q->result();

		}
		function report2($dari=null,$hingga=null,$negeri=null){

                        $q = $this->db->query("SELECT SQL_CALC_FOUND_ROWS
                        s.RecordDate,
                        s.Submission_Submit_ID,
			s.Usr_ID,
                        uc.Company_Name,
                        uc.Address_Reg,
                        uc.Postcode_Reg,
                        uc.City_Postal,
                        uc.Phone_No,
                        uc.Fax_No,
                        uc.Email,
                        uc.Contact_Name,
                        uc.Contact_Designation,
                        uc.Contact_Mobile_No,
                        uc.Contact_Email,
			(SELECT State_Name FROM sys_state WHERE State_ID = uc.State_Postal) AS negeri,
                        sl.Level_Name,
                        ct.Type_Name,
                        Quantity,
                        Disclaimer,
                        ss.Status_Desc,
                        s.CheckedBy,
                        s.Status_ID,
                        s.Submission_ID,
                        s.RecordDate rd,
			s.ApprovedDate ad
                        FROM tbl_Submission s
                        LEFT JOIN sys_User u ON s.Usr_ID = u.Usr_ID
                        LEFT JOIN sys_User_Client uc ON s.Usr_ID = uc.Usr_ID
                        LEFT JOIN tbl_Chemical_Type ct ON s.Type_ID = ct.Type_ID
                        LEFT JOIN tbl_Submission_Status ss ON s.Status_ID = ss.Status_ID
                        LEFT JOIN sys_Level sl ON u.Level_ID = sl.Level_ID
                        WHERE 1
                        AND s.isDeleted = 0
                        AND s.Status_ID IN (31,32,33,51)
                        AND s.ApprovedDate <= '$hingga'
                        AND s.ApprovedDate >= '$dari'
			AND uc.State_Postal = $negeri
			GROUP BY User_ID");


                        return $q->result();

                }
		function get_product($dari=null,$hingga=null,$syrikat=null){
			
			$stat = array(31,32,33,51);
			$this->db->select("*,tbl_submission.RecordDate AS rd");
			$this->db->select("tbl_submission_detail.ApprovedDate AS ad");
			$this->db->where('tbl_submission.isDeleted',0);
			$this->db->where_in('tbl_submission.Status_ID ',$stat);
			$this->db->where('tbl_submission.ApprovedDate <=',$hingga);
			$this->db->where('tbl_submission.ApprovedDate >=',$dari);
			$this->db->where('tbl_submission.Usr_ID',$syrikat);					
			$this->db->join('tbl_submission_detail','tbl_submission_detail.Submission_ID=tbl_submission.Submission_ID','left');
                        return $this->db->get('tbl_submission')->result();
			
		}
		function get_bahan($did){

			$this->db->where('Detail_ID',$did);
			$this->db->join('tbl_chemical','tbl_chemical.Chemical_ID=tbl_submission_chemical.Chemical_ID','left');
			return $this->db->get('tbl_submission_chemical')->result();
		}
		function get_bahan_name($did){

			$this->db->select('Chemical_ID');
			$this->db->where('Detail_ID',$did);
			$cid = $this->db->get('tbl_submission_chemical')->row();

			$id = $cid->Chemical_ID;
			
			$this->db->select('Chemical_Name');
			$this->db->where('Chemical_ID',$id);
			$cn = $this->db->get('tbl_chemical')->row();

			return $cn->Chemical_Name;

		}
		function get_cas($did){

			$this->db->select('Chemical_ID,Sub_Chemical_CAS');
			$this->db->where('Detail_ID',$did);
			$cid = $this->db->get('tbl_submission_chemical')->row();

			if ($cid->Sub_Chemical_CAS == null){
				$this->db->select('Chemical_CAS');
				$this->db->where('Chemical_ID',$cid->Chemical_ID);
				$cn = $this->db->get('tbl_chemical')->row();

				return $cn->Chemical_CAS;
			}
			else{
				return $cid->Sub_Chemical_CAS;
			}

		}
		function list_all($table=null,$filter =null,$cari=null){
       
        		if ($filter != null){
          			$this->db->where($filter,$cari);  
        		}
        		return $this->db->get($table);
        
    		}
		function get_data_array($table=null,$filter=null,$order=null){

                	$this->db->where($filter);

                	if ($order !== null){
                   		$this->db->order_by($order,"ASC");
                	}
                	return $this->db->get($table);
        	}
		function kira($table=null,$filter=null){
			
			$this->db->select("count(*) AS jumlah");
			if ($filter != null){
				$this->db->where($filter);
			}
			return $this->db->get($table)->row();

		}
		function datatekini(){

			$this->db->limit(10);
			$this->db->where('Submission_Submit_ID !=',null);
			$this->db->order_by("RecordDate","DESC");
			return $this->db->get('tbl_submission')->result();

		}
        
    
        
        
}
