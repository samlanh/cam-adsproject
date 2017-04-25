<?php

class Application_Model_DbTable_DbGlobalsetting extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	function getSystemSetting($string,$return=0){
		$db = $this->getAdapter();
		$sql="SELECT value FROM `vd_system_setting` WHERE keycode='".$string."'";
		$setting =  $db->fetchOne($sql);
		if($return==1){
			return $setting;
		}
		if($string =='allow_post'){
			
		}
		elseif($string =='allow_store'){
				
		}
	}
	function CountAdsByUser(){
		$user_id = $this->getUserId();
		$db= $this->getAdapter();
		$sql="SELECT COUNT(id) FROM vd_ads WHERE user_id = $user_id ";
		return $db->fetchOne($sql);
	}
	function getStoreByuserId(){
		$db = $this->getAdapter();
		$user_id  =$this->getUserId();
		$sql=" SELECT COUNT(id) FROM `vd_client_store` WHERE client_id= ".$user_id;
		return $db->fetchOne($sql);
	}
	
}
?>