<?php

class Other_Model_DbTable_DbPromotion extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_promotion';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addPromotion($_data){
    	
    	$img=str_replace(" ", "_", $_data['title']) . '.jpg';
    	$upload=new Zend_File_Transfer();
    	$a=$upload->addFilter('Rename',
    			array('target'=>PUBLIC_PATH .'/images/promotion/'.$img,'overwrite'=>true));
    	$recieve=$upload->receive();
    	if($recieve){
    		$imgs=$img;
    	}
    	else{
    		$imgs="";
    	}
    	$_arr = array(
    			'title'=>$_data['title'],
    			'image_promotion'=>$imgs,
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);
    }
    function updatePromotion($_data){
    	$img=str_replace(" ", "_", $_data['title']) . '.jpg';
    	$upload=new Zend_File_Transfer();
    	$a=$upload->addFilter('Rename',
    			array('target'=>PUBLIC_PATH .'/images/promotion/'.$img,'overwrite'=>true));
    	$recieve=$upload->receive();
    	if($recieve){
    		$imgs=$img;
    	}
    	else{
    		$imgs=$_data['old_photo'];
    	}
    	$_arr = array(
    			'title'=>$_data['title'],
    			'image_promotion'=>$imgs,
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId()
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);//insert data
    }
    
    function getAllPromotion($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id ,title,image_promotion,
    	(SELECT name_en FROM `ldc_view` WHERE TYPE=2 AND key_code =ldc_promotion.`status` limit 1  ) AS status
    	FROM `ldc_promotion` WHERE 1 ";
    	 
    	$where="";
    	 
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['adv_search']);
    		$s_where[]=" title LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY id ASC';
    	return $db->fetchAll($sql.$where.$order);
    }
    function getPromotionById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT *  FROM $this->_name ";
    	$sql.= " WHERE id= $id";
   	return $db->fetchRow($sql);
    }

}  
	  

