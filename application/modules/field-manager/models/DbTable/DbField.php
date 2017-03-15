<?php

class FieldManager_Model_DbTable_DbField extends Zend_Db_Table_Abstract
{

    protected $_name = 'vd_field_type';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    public function getAllField($search){
    	$db=$this->getAdapter();
    	$sql="SELECT 
			ft.`id`,ft.`title`,ft.`type`,ft.`is_require`,ft.`status`
			FROM `vd_field_type` AS ft WHERE ft.`status`>-1";
    	$where='';
    	if ($search['advance_search']!=""){
    		$s_where = array();
			$s_search = addslashes(trim($search['advance_search']));
			$s_where[] = " ft.`title` LIKE '%{$s_search}%'";
			$sql .=' AND ('.implode(' OR ',$s_where).')';
    	}
    	if ($search['status_search']!=""){
    		$where.=" AND ft.`status` =".$search['status_search'];
    	}
    	if (!empty($search['field_type'])){
    		$where.=" AND ft.`type` ='".$search['field_type']."'";
    	}
    	$order = "  ORDER BY ft.`id` DESC";
    	return $db->fetchAll($sql.$where.$order);
    }
    function addField($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		
    		$cate = "";
    		if ($data['category'])foreach($data['category'] as  $row){
    			if (empty($cate)){ $cate = $row;}else{ $cate = $cate.",".$row;}
    		}
    		$arr = array(
    			"title"=>$data['title'],
    			"label_name"=>$data['label'],
    			"type"=>$data['field_type'],
    			"status"=>$data['status'],
    			"category"=>$cate,
    			"description"=>$data['note'],
    			"is_require"=>$data['is_require'],
    			"create_date"=>date("Y-m-d"),
    			"modify_date"=>date("Y-m-d"),
    			"user_id"=>$this->getUserId(),
    				);
    		$arr['field_parent']="";
    		if($data['field_type']=="cascade"){
    			$arr['field_parent']=$data['field_parent'];
    		}
    		 if (!empty($data['id'])){
    		 	$this->_name = "vd_field_type";
    		 	$where=" id=".$data['id'];
    		 	$this->update($arr, $where);
    		 	$typeid = $data['id'];
    		 	
    		 	$this->_name="vd_field_value";
    		 	$where1=" field_type_id=".$data['id'];
    		 	$this->delete($where1);
    		 }else{
    		 	$this->_name = "vd_field_type";
	    		$typeid = $this->insert($arr);
    		 }
    		
    		if($data['field_type']=="select"){// Option filter select
    			$ids = explode(',', $data['identity']);
    			foreach ($ids as $i){
    				$arr = array(
    					"field_type_id"=>$typeid,
    					"name"=>$data['name'.$i],
    					"value"=>$data['value'.$i],
    					"ordering"=>$i,
    				);
    				$this->_name = "vd_field_value";
    				$this->insert($arr);
    			}
    		}elseif($data['field_type']=="cascade"){// Option filter select has parent option
    			$ids = explode(',', $data['identity']);
    			foreach ($ids as $i){
    				$arr = array(
    					"field_type_id"=>$typeid,
    					"name"=>$data['name'.$i],
    					"value"=>$data['value'.$i],
    					"parent"=>$data['parent'.$i],
    					"ordering"=>$i,
    				);
    				$this->_name = "vd_field_value";
    				$this->insert($arr);
    			}
    		}
    		
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function getFieldById($id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM $this->_name WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	function getFieldValueByFileid($field_id){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_field_value` AS fv WHERE fv.`field_type_id`=$field_id ORDER BY fv.`ordering` ASC";
		return $db->fetchAll($sql);
	}
	function deleteField($id){
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$arr = array(
					'status'=>-1,
			);
				$where = " id =".$id;
				$this->update($arr, $where);
			$db->commit();
		}catch(exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}

