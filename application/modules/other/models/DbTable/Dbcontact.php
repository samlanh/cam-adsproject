<?php

class Other_Model_DbTable_Dbcontact extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_manytable';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addcontact($_data){
    	$_arr = array(
    			'title'=>$_data['title'],
    			'description'=>$_data['note'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId(),
    			'type'=>3
    			);
    	$this->insert($_arr);//insert data
    }
    function getAllContact(){
    	$db = $this->getAdapter();
    	$sql=" select id,title,description,status from ldc_manytable where type=3";
    	return $db->fetchAll($sql);
    }
    function getContactById($id){
    	$db = $this->getAdapter();
    	$sql="select id,title,description,status from ldc_manytable where id =$id";
    	return $db->fetchRow($sql);
    }
    function updatecontact($_data){
    	$_arr = array(
    			'title'=>$_data['title'],
    			'description'=>$_data['note'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId()
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
}  
	  

