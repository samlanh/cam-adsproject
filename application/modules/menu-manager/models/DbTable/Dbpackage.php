<?php

class MenuManager_Model_DbTable_Dbpackage extends Zend_Db_Table_Abstract
{

    protected $_name = 'vd_package';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
	public function addNewPackage($_data){
		try {
			$_arr=array(
					'package_name'	=> $_data['package_name'],
					'allow_post'    => $_data['allow_post'],
					'allow_store'   => $_data['amount_store'],
					'allow_show'	=> $_data['allow_show'],
					'is_free' 		=> $_data['is_free'],
					'status'	    => $_data['status'],
					'date'   		=> $_data['date'],
					'description' 	=> $_data['note'],
					'price' 		=> $_data['price'],
					'user_id'	    => $this->getUserId()
			);
		if(!empty($_data['id'])){
			$where='id='.$_data['id'];
			$this->update($_arr, $where);
		}else{
			$this->insert($_arr);
		}
		}catch(Exception $e){
			echo $e->getMessage();exit();
		}
	}
	public function getPackageById($id){
		$db = $this->getAdapter();
		$sql=" SELECT * FROM $this->_name WHERE id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	function getAllPackage($search=null){
		$db = $this->getAdapter();
		$sql = "SELECT id,package_name,allow_post,allow_store,allow_show,price,is_free,description,
		(SELECT name_en FROM `vd_view` WHERE type=1 AND key_code=status LIMIT 1) As status
				FROM $this->_name ";
// 		if($search['search_status']>-1){
// 			$where.= " AND status = ".$search['search_status'];
// 		}
// 		if(!empty($search['adv_search'])){
// 			$s_where=array();
// 			$s_search=$search['adv_search'];
// 			$s_where[]= " amount_day LIKE '%{$s_search}%'";
// 			$s_where[]=" holiday_name LIKE '%{$s_search}%'";
// 			$s_where[]= " note LIKE '%{$s_search}%'";
// 			$where.=' AND ('.implode(' OR ', $s_where).')';
// 			//$where.=' AND ('.implode(' OR ',$s_where).')';
// 		}
		//echo $sql.$where;		
		return $db->fetchAll($sql);	
	}	
}

