<?php

class StoreController extends Zend_Controller_Action
{
    public function indexAction()
    {
//        $this->_helper->layout()->disableLayout();
       $client_session=new Zend_Session_Namespace('client');
       $db = new Application_Model_DbTable_DbGlobalselect();
       	$param = $this->getRequest()->getParams();
		if (!empty($param['store'])){
	       	$temp = explode(".", $param['store']);
			$store = $db->getMyStoreByAlias($temp[0]);
	      	$this->view->store = $store;
	      	if (!empty($store)){
		      	$dbadsstore = new Application_Model_DbTable_DbStore();
		       $this->view->myadsmostview = $dbadsstore->getAdsByUserid(1,null,$store['id']);
		       $this->view->myadsfeature = $dbadsstore->getAdsByUserid(null,1,$store['id']);
		       
		       $this->view->parent_cate = $dbadsstore->getParentCategory($store['id']);
	      	}else{
    			$this->_redirect("/index");
    		}
		}else{
			$this->_redirect("/index");
		}
		$this->view->param = $param;
    }
    public function adsAction()
    {
//     	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$param = $this->getRequest()->getParams();
    	if (!empty($param['store'])){
    		$temp = explode(".", $param['store']);
    		$store = $db->getMyStoreByAlias($temp[0]);
    		$this->view->store = $store;
    		if (!empty($store)){
	    		$dbadsstore = new Application_Model_DbTable_DbStore();
	    		$this->view->parent_cate = $dbadsstore->getParentCategory($store['id']);
	    		$page = explode(".", $param['page']);
	    		if ($page[0]=="allads"){
	    			$this->view->myadsfeature = $dbadsstore->getAdsByUserid(null,null,$store['id']);
	    		}else{
	    			$this->view->myadsfeature = $dbadsstore->getAllAdsByName($page[0],$store['id']);
	    		}
    		}else{
    			$this->_redirect("/index");
    		}
    	}else{
    		$this->_redirect("/index");
    	}
    }
    function detailsAction(){
//     	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$param = $this->getRequest()->getParams();
    	if (!empty($param['store'])){
    		$temp = explode(".", $param['store']);
    		$store = $db->getMyStoreByAlias($temp[0]);
    		$this->view->store = $store;
    		if (!empty($store)){
	    		$dbadsstore = new Application_Model_DbTable_DbStore();
	    		$alias = explode(".", $param['ads']);
	    		$adsdetail = $dbadsstore->getAdsDetail($alias[0]);
	    		$this->view->ads_detail = $adsdetail;
	    		if (!empty($adsdetail['id'])){
	    			$this->view->rsattr = $db->getAdsDetailById($adsdetail['id']);
	    			$this->view->relate_pro = $dbadsstore->getRelatedAds($adsdetail,$store['id']);
	    			$db_addview = new Application_Model_DbTable_DbGlobalselect();
	    			$db->addCountView($alias[0]);
	    		}
	    		}else{
	    			$this->_redirect("/index");
	    		}
    	}else{
    		$this->_redirect("/index");
    	}
    }
	public  function errorAction(){
		$this->_helper->layout()->disableLayout();
		
		
	}
	
}





