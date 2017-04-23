<?php

class Other_Model_DbTable_DbSeo extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_seo';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addseo($_data){
    	$_arr = array(
    			'key_word'=>$_data['key_word'],
    			'url'=>$_data['url'],
    			'description'=>$_data['description'],
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateSEO($_data){
    	$_arr = array(
    			'key_word'=>$_data['key_word'],
    			'url'=>$_data['url'],
    			'description'=>$_data['description'],
    			'user_id'=>$this->getUserId()
    			);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
    	
    function getAllSEO($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id ,url,key_word,description FROM ldc_seo";
    	 
    	//     	if($search['status_search']>-1){
    	//     		$where.= " AND b.status = ".$search['status_search'];
    	//     	}
    	$where=" WHERE key_word!='' ";
    	 
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['adv_search']);
    		$s_where[]=" key_word LIKE '%{$s_search}%'";
    		$s_where[]=" url LIKE '%{$s_search}%'";
    		$s_where[]=" description LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$where.$order);
    }
 function getSEOId($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM
    	$this->_name ";
    	$where = " WHERE `id`= $id" ;
   		return $db->fetchRow($sql.$where);
    }
}
