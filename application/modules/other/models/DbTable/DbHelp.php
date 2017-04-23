<?php

class Other_Model_DbTable_DbHelp extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_help';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addhelp($_data){
    	$_arr = array(
    			'title'=>$_data['help_title'],
    			'description'=>$_data['description'],
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);//insert data
    }
    public function updateHelp($_data){
        	$_arr = array(
        	    'title'=>$_data['help_title'],
    			'description'=>$_data['description'],
    			'user_id'=>$this->getUserId(),
        		'status'=>$_data['status']
        			);
        	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
        	$this->update($_arr, $where);
        }
    function getAllHELP($search=null){
    	$db = $this->getAdapter();
    	$sql = " SELECT id ,title,
				(SELECT name_en FROM `ldc_view` WHERE type=2 AND key_code =ldc_help.`status`  ) AS status
    	from ldc_help where 1 ";
    	 
    	//     	if($search['status_search']>-1){
    	//     		$where.= " AND b.status = ".$search['status_search'];
    	//     	}
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
    function addDelivery($_data){
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
    	$this->_name='ldc_otherinformation';
    	$this->insert($_arr);//insert data
    }
    function getAllDelivery($search=null){
    	$db = $this->getAdapter();
    	$sql = " SELECT id ,title,
    	(SELECT name_en FROM `ldc_view` WHERE type=2 AND key_code =ldc_otherinformation.`status`  ) AS status
    	from ldc_otherinformation where 1 ";
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
    function getDeliveryById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM
    	ldc_otherinformation ";
    	$where = " WHERE `id`= $id" ;
    	$db = $this->getAdapter();
    	return $db->fetchRow($sql.$where);
    }
    public function updateDelivery($_data){
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
    	$this->_name='ldc_otherinformation';
    	$this->update($_arr, $where);
    }
 function getHelpById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM
    	$this->_name ";
    	$where = " WHERE `id`= $id" ;
    	$db = $this->getAdapter();
    	return $db->fetchRow($sql.$where);
    }
    function getTermCodition(){
      $db = $this->getAdapter();
      $sql = "SELECT * FROM $this->_name WHERE title!='' ";
      return $db->fetchAll($sql);
    }
	function getDeliveryinfo(){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM ldc_otherinformation WHERE title!='' LIMIT 3 ";
      return $db->fetchAll($sql);
	}
	public function updateLabelAnimation($_data){
        	$_arr = array(
        	    'text_value'=>$_data['title'],
    			'date'=>date("Y-m-d"),
    			'user_id'=>$this->getUserId(),
        		'status'=>$_data['status']
        			);
			$this->_name ="ldc_config";
        	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
        	$this->update($_arr, $where);
        }
	function getConfigByid(){
		$db =$this->getAdapter();
		$sql= "SELECT * FROM `ldc_config` AS c WHERE c.`id`=1";
		return $db->fetchRow($sql);
	}
}  
	  

