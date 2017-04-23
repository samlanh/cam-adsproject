<?php

class Other_Model_DbTable_Dbfaq extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_faq';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addfaq($_data){
    	$_arr = array(
    			'answer'=>$_data['faq_answer'],
    			'question'=>$_data['faq_question'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);//insert data
    }
    function updatefaq($_data){
    	$_arr = array(
    			'answer'=>$_data['faq_answer'],
    			'question'=>$_data['faq_question'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId()
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
    function getAllFAQ($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id ,question,answer,
						(SELECT name_en FROM `ldc_view` WHERE type=2 AND key_code =ldc_faq.`status`  ) AS status
 							FROM `ldc_faq` WHERE question!=''
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
    function getAllFAQInfrontend(){
    	$db = $this->getAdapter();
    	$sql = "SELECT id ,question,answer
    	
    	FROM `ldc_faq` WHERE question!='' AND status=1";
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
    }
    
 function getFAQById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM
    	$this->_name ";
    	$where = " WHERE `id`= $id LIMIT 1" ;
   		return $db->fetchRow($sql.$where);
    }
}  
	  

