<?php

class Application_Model_DbTable_DbStore extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	public static function getClientID(){
		$client_session=new Zend_Session_Namespace('client');
		return $client_session->client_id;
	}
	static function getCurrentLang(){
		$session_lang=new Zend_Session_Namespace('lang');
		return $session_lang->lang_id;
	}
	function countStoreByClient(){
		$client_id = $this->getClientID();
		$db = $this->getAdapter();
		$sql="SELECT COUNT(cs.`id`) FROM `vd_client_store` AS cs WHERE cs.`status`=1 and cs.`client_id` = $client_id";
		return $db->fetchOne($sql);
	}
	function generateStoreAlias(){
		$db = new Application_Model_DbTable_DbClient();
		$client= $db->getClientInfo(null,$this->getClientID());
		$title = str_replace(' ','',$client['user_name']);
		return $title.$client['id'].($this->countStoreByClient()+1);
	}
	function addStore($data){
		$client_id = $this->getClientID();
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
			$part= PUBLIC_PATH.'/images/store/';
			$newName='';
			if (!empty($_FILES["logo_image"]["tmp_name"])){
				$photo = $_FILES["logo_image"];
				$temp = explode(".", $photo["name"]);
				$newName = "store".date("Y").date("m").date("d").round(microtime(true)).$client_id.'.'.end($temp);
				move_uploaded_file($_FILES["logo_image"]["tmp_name"], $part . $newName);
			}
			$arr = array(
					'alias_store'=>$this->generateStoreAlias(),
					'client_id'=>$client_id,
					'template_id'=>$data['templatess'],
					'logo_store'=>$newName,
					'store_address'=>$data['address'],
					'store_phone'=>$data['phone'],
					'store_about_us'=>$data['abouts'],
					'status'=>1,
					'template_color'=>$data['templatesscolor'],
					'font_color'=>$data['fontsscolor'],
					'create_date'=>date("Y-m-d"),
					'modify_date'=>date("Y-m-d"),
					);
			$this->_name="vd_client_store";
			$this->insert($arr);
			$db->commit();
			return 1;
		}catch(exception $e){
			Application_Form_FrmMessage::message("Your Submit Error!");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
	function getIdStoreByAlias($alias){
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_client_store` AS cs WHERE cs.`alias_store`='$alias' LIMIT 1";
		return $db->fetchRow($sql);
	}
	function updateStore($data){
		$client_id = $this->getClientID();
		$db = $this->getAdapter();
		$db->beginTransaction();
		try{
			$alias = $this->getIdStoreByAlias($data['alias']);
			$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
			$part= PUBLIC_PATH.'/images/store/';
			$newName='';
			if (!empty($_FILES["logo_image"]["tmp_name"])){
				$photo = $_FILES["logo_image"];
				$temp = explode(".", $photo["name"]);
				$newName = "store".date("Y").date("m").date("d").round(microtime(true)).$client_id.'.'.end($temp);
				move_uploaded_file($_FILES["logo_image"]["tmp_name"], $part . $newName);
			}
			$arr = array(
					//'alias_store'=>$this->generateStoreAlias(),
					'client_id'=>$client_id,
					'template_id'=>$data['templatess'],
					'logo_store'=>$newName,
					'store_address'=>$data['address'],
					'store_phone'=>$data['phone'],
					'store_about_us'=>$data['abouts'],
					'status'=>1,
					'template_color'=>$data['templatesscolor'],
					'font_color'=>$data['fontsscolor'],
// 					'create_date'=>date("Y-m-d"),
					'modify_date'=>date("Y-m-d"),
			);
			$this->_name="vd_client_store";
			$where = ' id = '.$alias['id'];
			$this->update($arr, $where);
			$db->commit();
			return 2;
		}catch(exception $e){
			Application_Form_FrmMessage::message("Your Submit Error!");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			$db->rollBack();
		}
	}
}
?>