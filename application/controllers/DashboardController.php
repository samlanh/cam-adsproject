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
	function deleteAction(){
		$adsid = $this->getRequest()->getParam("adsid");
		if(!empty($adsid)){
			$db = new Application_Model_DbTable_DbGlobalselect();
			$id = $db->deleteMyadsById($adsid);
			$this->_redirect("/dashboard");
		}else{
			$this->_redirect("/dashboard");
		}
	}
	function renewAction(){
		$adsid = $this->getRequest()->getParam("adsid");
		if(!empty($adsid)){
			$db = new Application_Model_DbTable_DbGlobalselect();
			$id = $db->renewMyadsById($adsid);
			$this->_redirect("/dashboard");
		}else{
			$this->_redirect("/dashboard");
		}
	}
	function announceAtion(){
		
	}	
	function bannerAction(){
		
	}
	function myaccountAction(){
		
	}
	function myadsAction(){
		$client_session=new Zend_Session_Namespace('client');
		$db = new Application_Model_DbTable_DbGlobalselect();
		$ads = $db->getAdsByUserid($client_session->client_id);
		
		$paginator = Zend_Paginator::factory($ads);
		$paginator->setDefaultItemCountPerPage(5);
		$allItems = $paginator->getTotalItemCount();
		$countPages= $paginator->count();
		$p = $this->getRequest()->getParam('pages');
		 
		if(isset($p))
		{
			$paginator->setCurrentPageNumber($p);
		} else $paginator->setCurrentPageNumber(1);
		 
		$currentPage = $paginator->getCurrentPageNumber();
		 
		$this->view->myads = $paginator;
		$this->view->countItems = $allItems;
		$this->view->countPages = $countPages;
		$this->view->currentPage = $currentPage;
		 
		if($currentPage == $countPages)
		{
			$this->view->nextPage = $countPages;
			$this->view->previousPage = $currentPage-1;
		}
		else if($currentPage == 1)
		{
			$this->view->nextPage = $currentPage+1;
			$this->view->previousPage = 1;
		}
		else {
			$this->view->nextPage = $currentPage+1;
			$this->view->previousPage = $currentPage-1;
		}
	}
	function mypageAction(){
	}
	function mystoreAction(){
		$client_session=new Zend_Session_Namespace('client');
		$db = new Application_Model_DbTable_DbGlobalselect();
		
		$this->view->mystore = $db->getMyStore($client_session->client_id);
	}
	function storeAction(){
		$param = $this->getRequest()->getParams();
		$this->view->param = $param;
		
		$client_session=new Zend_Session_Namespace('client');
		$db = new Application_Model_DbTable_DbGlobalselect();
		if (!empty($param['store'])){
			$store = $db->getMyStoreByAlias($client_session->client_id,$param['store']);
			$this->view->store = $store;
		}
		$this->view->template = $db->getAlltemplate();
	}
	function policyAction(){
		
	}
	function announceAction(){
		
	}
	function addstoreAction(){
		if($this->getRequest()->isPost()){
			$data=$this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbStore();
			$store = $db->addStore($data);
			
			$this->_redirect("/dashboard/mystore");
		}
	}
	function updatestoreAction(){
		if($this->getRequest()->isPost()){
			$paramid = $this->getRequest()->getParam('store');
			$data=$this->getRequest()->getPost();
			$data['alias'] = $paramid;
			$db = new Application_Model_DbTable_DbStore();
			$store = $db->updateStore($data);
				
			$this->_redirect("/dashboard/mystore");
		}
	}
}





