<?php

class Other_Model_DbTable_DbSlideshow extends Zend_Db_Table_Abstract
{

    protected $_name = 'vd_banneravertise';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    }
    function getAllSLIDE($search=null){
    	$db = $this->getAdapter();
    	$sql = "SELECT id ,banner_name,ordering,image,
    	start_date,end_date,
    	(SELECT name_en FROM `vd_view` WHERE TYPE=2 AND key_code=position_id LIMIT 1) AS position,
    	(SELECT name_en FROM `vd_view` WHERE TYPE=3 AND key_code =vd_banneravertise.`asign_page`) AS asign_page,
    	(SELECT name_en FROM `vd_view` WHERE TYPE=1 AND key_code =vd_banneravertise.`status`) AS status
    	FROM `vd_banneravertise` WHERE 1 ";
    	$where="";
    
    	if(!empty($search['title'])){
    		$s_where=array();
    		$s_search=addslashes($search['adv_search']);
    		$s_where[]=" banner_name LIKE '%{$s_search}%'";
    		$where.=' AND ('.implode(' OR ',$s_where).')';
    	}
    	$order=' ORDER BY ordering ASC, position_id ASC';
    	return $db->fetchAll($sql.$where.$order);
    }
    function addsledeshow($_data){
    	$img=str_replace(" ", "_", $_data['title']) . '.jpg';
    	$upload=new Zend_File_Transfer();
    	$a=$upload->addFilter('Rename',
    			array('target'=>PUBLIC_PATH .'/images/slideshow/'.$img,'overwrite'=>true));
    	$recieve=$upload->receive();
    	if($recieve){
    		$imgs=$img;
    	}
    	else{
    		$imgs="";
    	}
    	$_arr = array(
    			'banner_name'=>$_data['title'],
    			'ordering'=>$_data['ordering'],
    			'position_id'=>$_data['position'],
    			'asign_page'=>$_data['page_asign'],
    			'start_date'=>$_data['start_date'],
    			'end_date'=>$_data['end_date'],
    			'category'=>$_data['category'],
    			'image'=>$imgs,
    			'link_url'=>$_data['link'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId()
    			);
    	$this->insert($_arr);//insert data
    }
    function updateSlideshow($_data){
    	$img=str_replace(" ", "_", $_data['title']) . '.jpg';
    	$upload=new Zend_File_Transfer();
    	$a=$upload->addFilter('Rename',
    			array('target'=>PUBLIC_PATH .'/images/slideshow/'.$img,'overwrite'=>true));
    	$recieve=$upload->receive();
    	if($recieve){
    		$imgs=$img;
    	}
    	else{
    		$imgs=$_data['old_photo'];
    	}
    	$_arr = array(
    			'banner_name'=>$_data['title'],
    			'ordering'=>$_data['ordering'],
    			'position_id'=>$_data['position'],
    			'asign_page'=>$_data['page_asign'],
    			'start_date'=>$_data['start_date'],
    			'end_date'=>$_data['end_date'],
    			'image'=>$imgs,
    			'link_url'=>$_data['link'],
    			'status'=>$_data['status'],
    			'user_id'=>$this->getUserId()
    			);
    	$where=$this->getAdapter()->quoteInto("id=?", $_data['id']);
    	$this->update($_arr, $where);//insert data
    }
    function getSlidshowById($id){
    	$db = $this->getAdapter();
    	$sql = "SELECT *  FROM $this->_name ";
    	$sql.= " WHERE id= $id";
   	return $db->fetchRow($sql);
    }

}  
	  

