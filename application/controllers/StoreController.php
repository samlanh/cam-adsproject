<?php

class StoreController extends Zend_Controller_Action
{
    public function indexAction()
    {
       $this->_helper->layout()->disableLayout();
       $client_session=new Zend_Session_Namespace('client');
       $db = new Application_Model_DbTable_DbGlobalselect();
       	$param = $this->getRequest()->getParams();
		if (!empty($param['store'])){
	       	$temp = explode(".", $param['store']);
			$store = $db->getMyStoreByAlias($client_session->client_id,$temp[0]);
	      	$this->view->store = $store;
	      	
	      	$dbadsstore = new Application_Model_DbTable_DbStore();
	       $this->view->myadsmostview = $dbadsstore->getAdsByUserid($client_session->client_id,1,null,$store['id']);
	       $this->view->myadsfeature = $dbadsstore->getAdsByUserid($client_session->client_id,null,1,$store['id']);
	       
	       $this->view->parent_cate = $dbadsstore->getParentCategory($client_session->client_id,$store['id']);
		}
    }
    public function adsAction()
    {
    	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$param = $this->getRequest()->getParams();
    	if (!empty($param['store'])){
    		$temp = explode(".", $param['store']);
    		$store = $db->getMyStoreByAlias($client_session->client_id,$temp[0]);
    		$this->view->store = $store;
    
    		$dbadsstore = new Application_Model_DbTable_DbStore();
    		$this->view->parent_cate = $dbadsstore->getParentCategory($client_session->client_id,$store['id']);
    		$page = explode(".", $param['page']);
    		if ($page[0]=="allads"){
    			$this->view->myadsfeature = $dbadsstore->getAdsByUserid($client_session->client_id,null,null,$store['id']);
    		}else{
    			$this->view->myadsfeature = $dbadsstore->getAllAdsByName($client_session->client_id,$page[0],$store['id']);
    		}
    		
    	}
    }
    function detailsAction(){
    	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$param = $this->getRequest()->getParams();
    	if (!empty($param['store'])){
    		$temp = explode(".", $param['store']);
    		$store = $db->getMyStoreByAlias($client_session->client_id,$temp[0]);
    		$this->view->store = $store;
    	
    		$dbadsstore = new Application_Model_DbTable_DbStore();
    		$alias = explode(".", $param['ads']);
    		$adsdetail = $dbadsstore->getAdsDetail($alias[0]);
    		$this->view->ads_detail = $adsdetail;
    		if (!empty($adsdetail['id'])){
    			
    			$this->view->rsattr = $db->getAdsDetailById($adsdetail['id']);
    			$this->view->relate_pro = $dbadsstore->getRelatedAds($adsdetail,$store['id']);
    		}
    		
    	}
    }
	public  function errorAction(){
		$this->_helper->layout()->disableLayout();
		
		
	}
	
}





