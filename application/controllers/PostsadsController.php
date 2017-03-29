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
    public function chooseCategoryAction(){
    	$this->_helper->layout()->disableLayout();
    	$db = new Application_Model_DbTable_DbVdGlobal();
    	$this->view->parentcate = $db->getCategoryParent();
    }
    public function writePostAction(){
    	$this->_helper->layout()->disableLayout();
    	$param = $this->getRequest()->getParam('category');
    	echo $param;
    	exit();
    	$db = new Application_Model_DbTable_DbVdGlobal();
    	$this->view->parentcate = $db->getCategoryParent();
    }
}





