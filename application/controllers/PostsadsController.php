<?php

class PostsadsController extends Zend_Controller_Action
{

	const REDIRECT_URL = '/transfer';
	
    public function init()
    {
        /* Initialize action controller here */
    	header('content-type: text/html; charset=utf8');  
    	
    }

    public function indexAction()
    {
    	$this->_helper->layout()->disableLayout();
    }
    public function chooseCategoryAction(){ // choose category before go to write post ads
    	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	if(!empty($client_session->client_id)){ //check session has been have or not
	    	$db = new Application_Model_DbTable_DbVdGlobal();
	    	$this->view->parentcate = $db->getCategoryParent();
    	}else{
    		$this->_redirect("index/login");
    	}
    }
    public function writePostAction(){ // write post ads and submit ads to finish
    	$this->_helper->layout()->disableLayout();
    	$client_session=new Zend_Session_Namespace('client');
    	if(!empty($client_session->client_id)){ //check session has been have or not
	    	$param = $this->getRequest()->getParam('category');
	    	$db = new Application_Model_DbTable_DbVdGlobal();
	    	$this->view->parentcate = $db->getCategoryParent();
	    	$cate = $db->getCategoryIdbyAlias($param);
	    	$this->view->cate = $cate;
	    	$dbform = new Application_Model_DbTable_DbDynamicFormPostAds();
	    	$this->view->form = $dbform->getAllFormTypeCateid($cate['id']);
    	}else{
    		$this->_redirect("index/login");
    	}
    }
}





