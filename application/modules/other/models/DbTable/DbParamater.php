<?php

class Other_Model_DbTable_DbParamater extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_paramater';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addParamater($data){
    	//print_r($data);exit();
    	$_arr = array(
    			'title'=>$data['paramater'],
    			'value'=>$data['values'],
    			'status'=>$data['status'],
    			'date'=>date("Y-m-d"),
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);
    }
    public function updateParamater($data){
    	$_arr = array(
    			'title'=>$data['paramater'],
    			'value'=>$data['values'],
    			'status'=>$data['status'],
    			'date'=>date("Y-m-d"),
    			'user_id'=>$this->getUserId()
    			);
    	$where=$this->getAdapter()->quoteInto("id=?", $data['id']);
    	$this->update($_arr, $where);
    }
    	
	function getAllParamater($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,title,`status` FROM $this->_name WHERE 1";
//     	if($search['status_search']>-1){
//     		$where.= " AND b.status = ".$search['status_search'];
//     	}
//     	$where="";
    	
//     	if(!empty($search['title'])){
//     		$s_where=array();
//     		$s_search=addslashes($search['adv_search']);
//     		$s_where[]=" title LIKE '%{$s_search}%'";
//     		$s_where[]=" value LIKE '%{$s_search}%'";
//     		$s_where[]=" b.displayby LIKE '%{$s_search}%'";
//     		$where.=' AND ('.implode(' OR ',$s_where).')';
//     	}
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$order);
//    		return $db->fetchAll($sql.$where.$order);
    }
    
 function getParamaterById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT id,title,`value`,`status` FROM ldc_paramater WHERE `id`=".$id;
   		return $db->fetchRow($sql);
    }

}  
	  

