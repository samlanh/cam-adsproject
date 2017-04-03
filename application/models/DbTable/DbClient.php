<?php
/**
 * @Date 20/03/2017
 * @author vandy
 * @version 1.0
 */
class Application_Model_DbTable_DbClient extends Zend_Db_Table_Abstract
{
    protected $_name = 'vd_client';
    function clientRegister($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$arr = array(
    				"user_name"=>$data['user_name'],
    				"email"=>$data['email'],
    				"password"=>md5($data['password']),
    				"code_verify_acc"=>$data['code_random'],
    				"create_date"=>date("Y-m-d H:i:s"),
    				"modify_date"=>date("Y-m-d H:i:s"),
    				"status"=>1,
    		);
    		$this->_name = "vd_client";
    		$row= $this->insert($arr);
    		$db->commit();
    		return $row;
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    function checkVerifyCode($data){ // compare security code true or not
    	$db = $this->getAdapter();
    	$id = $data['id'];
    	$sql="SELECT c.`id` FROM `vd_client` AS c WHERE c.`id`= $id AND c.`code_verify_acc`='".$data['verify_code']."'";
    	$row= $db->fetchRow($sql);
    	if (!empty($row)){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }
    function activationClient($client_id){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$arr = array(
    				"is_verify_acc"=>1,
    				"modify_date"=>date("Y-m-d H:i:s"),
    				"status"=>1,
    		);
    		$this->_name = "vd_client";
    		$where = 'id = '.$client_id;
    		$this->update($arr, $where);
    		$row = $db->commit();
    		return $row;
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    public function checkEmailClient($email){ //check email has been use or not
    	$db = $this->getAdapter();
    	$sql='SELECT p.id FROM `vd_client` AS p WHERE p.`status`=1 AND p.`email`='."'$email'";
    	$row= $db->fetchRow($sql);
    	if (!empty($row)){
    		return 1;
    	}
    	else{
    		return 0;
    	}
    }
    public function getClientInfo($data=null,$id=null){ // use with dashboard and login
    	$db = $this->getAdapter();
    	$username=$data['user_name'];
    	$pasdsword= md5($data['password']);
    	if (empty($id)){
    		$sql ="SELECT * FROM `vd_client` AS c WHERE c.`status`=1 AND c.`user_name`='$username' AND c.`password` = '$pasdsword'";
    	}else{ // use with dashboard
    		$sql ="SELECT * FROM `vd_client` AS c WHERE  c.`id`=$id LIMIT 1";
    	}
    	return $db->fetchRow($sql);
    }
    public function cusAuthenticate($user_name,$password) // check when login
    {
    
    	$db_adapter = Application_Model_DbTable_DbUsers::getDefaultAdapter();
    	$auth_adapter = new Zend_Auth_Adapter_DbTable($db_adapter);
    
    	$auth_adapter->setTableName($this->_name = 'vd_client') // table where users are stored
    	->setIdentityColumn('user_name') // field name of user in the table
    	->setCredentialColumn('password') // field name of password in the table
    	->setCredentialTreatment('MD5(?) AND status=1'); // optional if password has been hashed
//     	->setCredentialTreatment('MD5(?)'); // optional if password has been hashed
    		
    	$auth_adapter->setIdentity($user_name); // set value of username field
    	$auth_adapter->setCredential($password);// set value of password field
    
    	//instantiate Zend_Auth class
    	$auth = Zend_Auth::getInstance();
    
    
    	$result = $auth->authenticate($auth_adapter);
    
    
    	if($result->isValid()){
    		return true;
    	}else{
    		return false;
    	}
    }
    function updateAccountInfo($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$arr = array(
    			"user_name"=>$data['user_name'],
    			"phone"=>$data['phone'],
    			"email"=>$data['email'],
    			"modify_date"=>date("Y-m-d H:i:s"),
	    	);
	    	$this->_name = "vd_client";
	    	$where = 'id = '.$data['id'];
    		$this->update($arr, $where);
    		$row = $db->commit();
    		return $row;
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    function updateAddtional($data){
    	$db = $this->getAdapter();
    	$db->beginTransaction();
    	try{
    		$arr = array(
    				"website"=>$data['website'],
    				"address"=>$data['address'],
    				"modify_date"=>date("Y-m-d H:i:s"),
    		);
    		$this->_name = "vd_client";
    		$where = 'id = '.$data['id'];
    		$this->update($arr, $where);
    		$row = $db->commit();
    		return $row;
    	}catch(exception $e){
    		Application_Form_FrmMessage::message("Application Error");
    		Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
    		$db->rollBack();
    	}
    }
    function uploadProfile($data){
    	$valid_formats = array("jpg", "png", "gif", "bmp","jpeg");
    	$part= PUBLIC_PATH.'/images/';
  
    	$session_id="";
    	$name = $_FILES['profile_image']['name'];
    	$size = $_FILES['profile_image']['size'];
    	$string='';
    	if(strlen($name)) {
    		list($txt, $ext) = explode(".", $name);
    		if(in_array($ext,$valid_formats)) {
    			if($size<(1024*1024)) {
    				$image_name = time().$data['id'].".".$ext;
    				$tmp = $_FILES['profile_image']['tmp_name'];
    				if(move_uploaded_file($tmp, $part.$image_name)){
    					$arr = array(
    							"photo"=>$image_name,
    							"modify_date"=>date("Y-m-d H:i:s"),
    					);
    					$this->_name = "vd_client";
    					$where = 'id = '.$data['id'];
    					$this->update($arr, $where);
    					$string= "<img src='".$part.$image_name."' >";
    				}
    				else
    					$string = "Image Upload failed";
    			}
    			else
    				$string = "Image file size max 1 MB";
    		}
    		else
    			$string= "Invalid file format..";
    	}
    	return $string;
    }
}


