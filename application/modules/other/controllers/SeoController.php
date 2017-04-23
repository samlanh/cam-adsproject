<?php
class Other_SeoController extends Zend_Controller_Action {
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
				$db = new Other_Model_DbTable_DbSeo();
				$rs= $db->getAllSEO($search);
				$list = new Application_Form_Frmtable();
				$collumns = array("Url","Key Word","Description");
				$link=array(
						'module'=>'other','controller'=>'seo','action'=>'edit',
				);
				
			}catch (Exception $e){
				Application_Form_FrmMessage::message("Application Error");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
			$this->view->list=$list->getCheckList(0, $collumns, $rs,array('key_word'=>$link,'url'=>$link));
			
		}
	function addAction()
		{
			if($this->getRequest()->isPost()){//check condition return true click submit button
				$_data = $this->getRequest()->getPost();
				$_dbmodel = new Other_Model_DbTable_DbSeo();
				try {
					$_dbmodel->addseo($_data);
					if(!empty($_data['save_new'])){
						Application_Form_FrmMessage::message("INSERT_SUCCESS");
					}else{
						Application_Form_FrmMessage::Sucessfull(("INSERT_SUCCESS"),self::REDIRECT_URL . "/seo");
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
			$_db = new Other_Model_DbTable_DbSeo();
			if($this->getRequest()->isPost()){//check condition return true click submit button
				$_data = $this->getRequest()->getPost();
				try {
					$_db->updateSEO($_data);
					Application_Form_FrmMessage::Sucessfull(("EDIT_SUCCESS"),self::REDIRECT_URL . "/seo");
				}catch (Exception $e) {
					Application_Form_FrmMessage::message("INSERT_FAIL");
					$err =$e->getMessage();
					Application_Model_DbTable_DbUserLog::writeMessageError($err);
				}
			}
			$id = $this->getRequest()->getParam("id");
			$this->view->row = $_db->getSEOId($id);
		}
}

