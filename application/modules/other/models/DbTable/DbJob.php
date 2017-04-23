<?php

class Other_Model_DbTable_DbJob extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_job';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addjob($_data){
    	$_arr = array(
    			'job_title'=>$_data['job_title'],
    			'job_type'=>$_data['job_type'],
    			'salary'=>$_data['salary'],
    			'hiring'=>$_data['hiring'],
    			'posting_date'=>$_data['posting_date'],
    			'closting_date'=>$_data['closing_date'],
    			'introduction'=>$_data['introduction'],
    			'job_purpose'=>$_data['job_purpose'],//desction
    			'job_respone'=>$_data['job_respone'],
    			'job_requirement'=>$_data['job_requirement'],
    			'job_duty'=>$_data['job_duty'],
    			'benifit'=>$_data['benifit'],
    			'how_applay'=>$_data['how_apply'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateJob($_data){
    	$_arr = array(
    			'job_title'=>$_data['job_title'],
    			'job_type'=>$_data['job_type'],
    			'salary'=>$_data['salary'],
    			'hiring'=>$_data['hiring'],
    			'posting_date'=>$_data['posting_date'],
    			'closting_date'=>$_data['closing_date'],
    			'introduction'=>$_data['introduction'],
    			'job_purpose'=>$_data['job_purpose'],//desction
    			'job_respone'=>$_data['job_respone'],
    			'job_requirement'=>$_data['job_requirement'],
    			'job_duty'=>$_data['job_duty'],
    			'benifit'=>$_data['benifit'],
    			'how_applay'=>$_data['how_apply'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId()
    			);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
    	
	function getAllJOB($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,job_title,hiring,(SELECT name_en FROM `ldc_view` WHERE type=4 AND key_code =job_type LIMIT 1) AS job_type,salary,posting_date,closting_date,
		(SELECT name_en FROM `ldc_view` WHERE TYPE=2 AND key_code =`ldc_job`.`status`) 
		AS status FROM ldc_job
    	
    	 ";
    	
//     	if($search['status_search']>-1){
//     		$where.= " AND b.status = ".$search['status_search'];
//     	}
    	$where="";
    	
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['adv_search']);
    		$s_where[]=" question LIKE '%{$s_search}%'";
    		$s_where[]=" answer LIKE '%{$s_search}%'";
    		$s_where[]=" b.displayby LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY id DESC';
   		return $db->fetchAll($sql.$where.$order);
    }
    
 function getJobId($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM $this->_name ";
    	$where = " WHERE `id`= $id" ;
  
   		return $db->fetchRow($sql.$where);
    }
    public static function getBranchCode(){
    	$db = new Application_Model_DbTable_DbGlobal();
    	$sql = "SELECT COUNT(br_id) AS amount FROM `ln_branch`";
    	$acc_no= $db->getGlobalDbRow($sql);
    	$acc_no=$acc_no['amount'];
    	$new_acc_no= (int)$acc_no+1;
    	$acc_no= strlen((int)$acc_no+1);
    	$pre = "";
    	for($i = $acc_no;$i<3;$i++){
    		$pre.='0';
    	}
    	return "C-".$pre.$new_acc_no;
    }
}  
	  

