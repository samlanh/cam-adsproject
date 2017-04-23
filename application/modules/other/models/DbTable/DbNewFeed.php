<?php

class Other_Model_DbTable_DbNewFeed extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_news_feed';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addNewfeed($_data){
    	$_arr = array(
    			'title'=>$_data['title'],
    			'description'=>$_data['description'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId(),
    			'created_date'=>date("Y-m-d H:i"),
    			'modify_date'=>date("Y-m-d H:i"),
    			);
    	$this->insert($_arr);//insert data
    }
    function getAllNewFeed(){
    	$db = $this->getAdapter();
    	$sql=" SELECT n.`id`,n.`title`,n.`modify_date`,n.`status` FROM `ldc_news_feed` AS n ";
    	return $db->fetchAll($sql);
    }
    function getNewFeedByID($id){
    	$db = $this->getAdapter();
    	$sql="select * from ldc_news_feed where id =$id";
    	return $db->fetchRow($sql);
    }
    function updateabout($_data){
    	$_arr = array(
    			'title'=>$_data['title'],
    			'description'=>$_data['description'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId(),
    			'modify_date'=>date("Y-m-d H:i"),
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
}  
	  

