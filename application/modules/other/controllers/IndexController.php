<?php
class Other_IndexController extends Zend_Controller_Action {
	protected $tr;
	const REDIRECT_URL='/other';
	public function init()
	{
		$this->tr=Application_Form_FrmLanguages::getCurrentlanguage();
		header('content-type: text/html; charset=utf8');
		defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		$this->_redirect('other/slideshow');
		
	}
}
