<?php

class MenuManager_Model_DbTable_DbCategory extends Zend_Db_Table_Abstract
{

    protected $_name = 'vd_category';
    public static function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    public function getAllCategory($parent = 0, $spacing = '', $cate_tree_array = '',$status=''){
    	$db=$this->getAdapter();
    	if (!is_array($cate_tree_array))
    		$cate_tree_array = array();
    	
    	$sql="SELECT c.`id`,
    	(SELECT cd.title FROM `vd_category_detail` AS cd WHERE cd.category_id = c.`id` AND cd.languageId=1 LIMIT 1) AS name,
    	c.`parent` FROM `vd_category` AS c WHERE c.`status`>-1 AND c.`parent`=$parent ";
    	if ($status!=''){
    		$sql.=" AND c.`status`=$status";
    	}
    	$sql.=" ORDER BY id ASC";
    	$query = $db->fetchAll($sql);
    	$stmt = $db->query($sql);
    	$rowCount = count($query);
    	$id='';
    	if ($rowCount > 0) {
    		foreach ($query as $row){
    			$cate_tree_array[] = array("id" => $row['id'], "name" => $spacing . $row['name']);
    			$cate_tree_array = $this->getAllCategory($id=$row['id'], $spacing . ' - ', $cate_tree_array,$status);
    		}
    	}
    	return $cate_tree_array;
    }
    function addCategory($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$dbglobal = new Application_Model_DbTable_DbVdGlobal();
    		$lang = $dbglobal->getLaguage();
    		if (!empty(trim($data['title_alias']))){
    			$alias = $data['title_alias'];
    		}else{
    			$alias = md5(date("Y-m-d H:i:s"));
    		}
	    	$arr = array(
	    			'parent'=>$data['parent'],
					'alias_category'=>$alias,
	    			'status'=>$data['status'],
					'modify_date'=>date("Y-m-d"),
	    			'description'=>$data['description'],
	    			'user_id'=>$this->getUserId(),
	    		);
	    		 $this->_name="vd_category";
	    	 if (!empty($data['id'])){
	    		 	$where=" id=".$data['id'];
	    		 	$this->update($arr, $where);
	    		 	$cate_id =$data['id'];
	    		 	
	    		 	$this->_name="vd_category_detail";
	    		 	$where1=" category_id=".$data['id'];
	    		 	$this->delete($where1);
	    	}else{
	    			$this->_name="vd_category";
	    		 	$arr['create_date']= date("Y-m-d");
	    			$cate_id = $this->insert($arr);
	    	}
	    	if(!empty($lang)) foreach($lang as $row){
	    			$title = str_replace(' ','',$row['title']);
	    			$arr_cate = array(
	    					'category_id'=>$cate_id,
	    					'title'=>$data['title'.$title],
	    					'languageId'=>$row['id'],
	    			);
	    			 $this->_name="vd_category_detail";
	    			$this->insert($arr_cate);
	    		}
	    	$db->commit();
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
	}
	function getCategoryById($id){
		$db= $this->getAdapter();
		$sql="SELECT * FROM `vd_category` WHERE id =".$id;
		return $db->fetchRow($sql);
	}
	function getCategoryTitleByLang($cate_id,$lang){
		$db = $this->getAdapter();
		$sql="SELECT cd.`title`,cd.`languageId` FROM `vd_category_detail` AS cd WHERE cd.`category_id`=$cate_id AND cd.`languageId`=$lang";
		return $db->fetchRow($sql);
	}
	public function CheckTitleAlias($alias){
		$db =$this->getAdapter();
		$sql = "SELECT c.`id` FROM `vd_category` AS c WHERE c.`alias_category`= '$alias'";
		return $db->fetchRow($sql);
	}
	function deleteCategory($id){
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

