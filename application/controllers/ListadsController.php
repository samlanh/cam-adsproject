<?php
/*-- old form when testing dynamic form by category---*/
class listadsController extends Zend_Controller_Action
{
	public function indexAction()
    {
    	//$this->_helper->layout()->disableLayout();
    	$id = $this->getRequest()->getParam("cateid");
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$this->view->rsads = $db->getAllAdsByName($id);
    	$this->view->cate_info = $db->getCategoryInfo($id);
    	
    }
    function detailAction(){
//     	$this->_helper->layout()->disableLayout();
    }
}





