<?php

class DashboardController extends Zend_Controller_Action
{
    public function indexAction()
    {
       //$this->_helper->layout()->disableLayout();
       $client_session=new Zend_Session_Namespace('client');
       $db = new Application_Model_DbTable_DbClient();
       $this->view->client_info = $db->getClientInfo(null,$client_session->client_id);
       
       $db = new Application_Model_DbTable_DbGlobalselect();
       $this->view->myads = $db->getAdsByUserid($client_session->client_id);
    }
	public  function errorAction(){
		$this->_helper->layout()->disableLayout();
	}
	public  function menuAction(){
		$this->_helper->layout()->disableLayout();
	}
	public function editAccAction(){// update account information by ajax on dashboard page
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$client_session=new Zend_Session_Namespace('client');
			$data['id']= $client_session->client_id;
			$db = new Application_Model_DbTable_DbClient();
			$rs = $db->updateAccountInfo($data);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
	public function editAddtionalAction(){// update account information by ajax on dashboard page
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$client_session=new Zend_Session_Namespace('client');
			$data['id']= $client_session->client_id;
			$db = new Application_Model_DbTable_DbClient();
			$rs = $db->updateAddtional($data);
			print_r(Zend_Json::encode($rs));
			exit();
		}
	}
	public function uploadAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$client_session=new Zend_Session_Namespace('client');
			$data['id']= $client_session->client_id;
			$db = new Application_Model_DbTable_DbClient();
			$rs = $db->uploadProfile($data);
			exit();
		}
	}
}





