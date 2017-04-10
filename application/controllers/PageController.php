<?php
/*-- old form when testing dynamic form by category---*/
class PageController extends Zend_Controller_Action
{
	public function indexAction()
    {
//     	$this->_helper->layout()->disableLayout();
    	$param = $this->getRequest()->getParam("param");
    	$db = new Application_Model_DbTable_DbGlobalselect();
    	$this->view->menu_info = $db->getMenuItemsByAlias($param);
    }
    function detailAction(){
//     	$this->_helper->layout()->disableLayout();
    }
}





