<?php
class Other_PhotoController extends Zend_Controller_Action {
	const REDIRECT_URL='/other';
    public function init()
    {    	
     /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
			if($this->getRequest()->isPost()){
				$_data=$this->getRequest()->getPost();
			}else{
				$search= array(
						'title'=>''
				);
			}
	
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$db = new Other_Model_DbTable_DbPhoto();
		$this->view->rows=$db->getAllPhoto($search);
			
	}
	function addAction()
			{
				if($this->getRequest()->isPost()){//check condition return true click submit button
					$_data = $this->getRequest()->getPost();
					$_dbmodel = new Other_Model_DbTable_DbPhoto();
					try {
						$_dbmodel->addphoto($_data);
						if(!empty($_data['save_new'])){
							Application_Form_FrmMessage::message("INSERT_SUCCESS");
						}else{
							Application_Form_FrmMessage::Sucessfull(("INSERT_SUCCESS"),self::REDIRECT_URL . "/photo/index");
						}
					}catch (Exception $e) {
						Application_Form_FrmMessage::message("INSERT_FAIL");
						$err =$e->getMessage();
						Application_Model_DbTable_DbUserLog::writeMessageError($err);
					}
				}
			}
			function editAction()
			{
				$_db = new Other_Model_DbTable_DbPhoto();
				if($this->getRequest()->isPost()){//check condition return true click submit button
					$_data = $this->getRequest()->getPost();
					
					try {
						$_db->updatePhoto($_data);
					    Application_Form_FrmMessage::Sucessfull(("EDIT_SUCCESS"),self::REDIRECT_URL . "/photo");
					}catch (Exception $e) {
						Application_Form_FrmMessage::message("INSERT_FAIL");
						$err =$e->getMessage();
						Application_Model_DbTable_DbUserLog::writeMessageError($err);
					}
				}
				$id = $this->getRequest()->getParam("id");
				$this->view->row = $_db->getPhtotoId($id);
			}
			
}

