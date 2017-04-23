<?php
class Other_Model_DbTable_DbPhoto extends Zend_Db_Table_Abstract
{

    protected $_name = 'ldc_photo';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function addphoto($_data){
    	$photo_name=str_replace(" ", "_", $_data['title']) . '.jpg';
    	$upload=new Zend_File_Transfer();
    	$a=$upload->addFilter('Rename',
    			array('target'=>PUBLIC_PATH .'/slider/'.$photo_name,'overwrite'=>true));
    	$recieve=$upload->receive();
    	if($recieve){
    		$img=$photo_name;
    	}
    	else{
    		$img="";
    	}
    	$_arr = array(
    			'title'=>$_data['title'],
    			'status'=>$_data['status'],
    			//'description'=>$_data['note'],
    			'photo_name'=>$img,
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);//insert data
    }
    public function updatePhoto($_data){
    	$photo_name=str_replace(" ", "_", $_data['title']) . '.jpg';
    	$upload=new Zend_File_Transfer();
    	$a=$upload->addFilter('Rename',
    			array('target'=>PUBLIC_PATH .'/slider/'.$photo_name,'overwrite'=>true));
    	$recieve=$upload->receive();
    	if($recieve){
    		$img=$photo_name;
    	}
    	else{
    		$img=$_data['old_photo'];
    	}
    	$_arr = array(
    			'title'=>$_data['title'],
    			'status'=>$_data['status'],
    			//'description'=>$_data['note'],
    			'photo_name'=>$img,
    			'user_id'=>$this->getUserId()
    	);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);
    }
    function getAllPhoto($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id ,title,photo_name,
    	(SELECT name_en FROM `ldc_view` WHERE type=2 AND key_code =ldc_photo.`status`  ) AS status
    	FROM `ldc_photo` WHERE 1 ";
    	$where="";
    	 
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['adv_search']);
    		$s_where[]=" photo_name LIKE '%{$s_search}%'";
    		$s_where[]=" status LIKE '%{$s_search}%'";
    		$s_where[]=" ordering LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY id DESC';
//     	echo $sql.$where.$order;exit();
    	return $db->fetchAll($sql.$where.$order);
    }
 function getPhtotoId($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT * FROM
    	$this->_name ";
    	$where = " WHERE `id`= $id" ;
   		return $db->fetchRow($sql.$where);
    }

}  
	  

