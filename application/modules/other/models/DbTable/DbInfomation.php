<?php

class Other_Model_DbTable_DbInfomation extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_information';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addInformation($_data){
    	$adapter = new Zend_File_Transfer_Adapter_Http();
    	$part= PUBLIC_PATH.'/images/promotion';
    	$adapter->setDestination($part);
    	$adapter->receive();
    	$photo = $adapter->getFileInfo();
    	if(!empty($photo['photo']['name'])){
    		$_data['photo']=$photo['photo']['name'];
    	}else{
    		$_data['photo']='';
    	}
    
    	$_arr = array(
    			'title'=>$_data['help_title'],
    			'image'=>$_data['photo'],
    			'description'=>$_data['description'],
    			'user_id'=>$this->getUserId()
    	);
    	$this->_name='ldc_information';
    	$this->insert($_arr);//insert data
    }
    function getAllInfor($search=null){
    	$db = $this->getAdapter();
    	$sql = " SELECT id ,title,image,
    	(SELECT name_en FROM `ldc_view` WHERE type=2 AND key_code =ldc_information.`status`  ) AS status
    	from ldc_information where 1 ";
    	$where="";
    
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['adv_search']);
    		$s_where[]=" title LIKE '%{$s_search}%'";
    		$s_where[]=" description LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY id DESC';
    	return $db->fetchAll($sql.$where.$order);
    }
    function getInfoById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM
    	ldc_information ";
    	$where = " WHERE `id`= $id" ;
    	$db = $this->getAdapter();
    	return $db->fetchRow($sql.$where);
    }
    public function updateInfo($_data){
    	$adapter = new Zend_File_Transfer_Adapter_Http();
    	$part= PUBLIC_PATH.'/images/promotion';
    	$adapter->setDestination($part);
    	$adapter->receive();
    	$photo = $adapter->getFileInfo();
    	if(!empty($photo['photo']['name'])){
    		$_data['photo']=$photo['photo']['name'];
    	}else{
    		$_data['photo']=$_data['old_photo'];
    	}
    	$_arr = array(
    			'title'=>$_data['help_title'],
    			'image'=>$_data['photo'],
    			'description'=>$_data['description'],
    			'user_id'=>$this->getUserId(),
    			'status'=>$_data['status']
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->_name='ldc_information';
    	$this->update($_arr, $where);
    }
	function getInfo(){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM ldc_information WHERE title!='' LIMIT 3";
      return $db->fetchAll($sql);
	}
	
}  
	  

