<?php
class Other_textanimationController extends Zend_Controller_Action {
	const REDIRECT_URL='/other';
	protected $tr;
	public function init()
	{
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$db = new Other_Model_DbTable_DbHelp();
		try{
			
			if($this->getRequest()->isPost()){
				$data = $this->getRequest()->getPost();
				$data['id']=1;
				$db->updateLabelAnimation($data);
				Application_Form_FrmMessage::Sucessfull(("EDIT_SUCCESS"),self::REDIRECT_URL . "/textanimation");
			}
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			echo $e->getMessage();
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
		$this->view->label = $db->getConfigByid();
	}
}
