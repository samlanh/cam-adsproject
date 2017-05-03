<?php 
Class MenuManager_Form_FrmPackage extends Zend_Dojo_Form {
	protected $tr;
	protected $tvalidate ;//text validate
	protected $filter;
	protected $text;
	protected $date;
	protected $tarea=null;
	public function init()
	{
		$this->tr = Application_Form_FrmLanguages::getCurrentlanguage();
		$this->tvalidate = 'dijit.form.ValidationTextBox';
		$this->filter = 'dijit.form.FilteringSelect';
		$this->text = 'dijit.form.TextBox';
		$this->date = 'dijit.form.DateTextBox';
		$this->tarea = 'dijit.form.SimpleTextarea';
	}
	public function FrmAddpackage($_data=null){
		$request=Zend_Controller_Front::getInstance()->getRequest();
		
		$_title = new Zend_Dojo_Form_Element_TextBox('adv_search');
		$_title->setAttribs(array('dojoType'=>$this->tvalidate,
				'onkeyup'=>'this.submit()',
				'placeholder'=>$this->tr->translate("SEARCH_HILIDAY_INFO")
		));
		$_title->setValue($request->getParam("adv_search"));
		
		$_status_search=  new Zend_Dojo_Form_Element_FilteringSelect('search_status');
		$_status_search->setAttribs(array('dojoType'=>$this->filter));
		$_status_opt = array(
				-1=>$this->tr->translate("ALL"),
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status_search->setMultiOptions($_status_opt);
		$_status_search->setValue($request->getParam("search_status"));
		
		$_btn_search = new Zend_Dojo_Form_Element_SubmitButton('btn_search');
		$_btn_search->setAttribs(array(
				'dojoType'=>'dijit.form.Button',
				'iconclass'=>'dijitIconSearch',
		));
		
		$_holiday_name = new Zend_Dojo_Form_Element_TextBox('package_name');
		$_holiday_name->setAttribs(array('dojoType'=>$this->tvalidate,'required'=>'true','class'=>'fullside',));
		
		$_startdate = new Zend_Dojo_Form_Element_DateTextBox('date');
		$_startdate->setAttribs(array('dojoType'=>$this->date,
				'class'=>'fullside',
				'onchange'=>'CalculateDate();'));
		$_date = $request->getParam("start_date");

		if(empty($_date)){
			$_date = date('Y-m-01');
		}
		$_startdate->setValue($_date);
		
		$_amount_day = new Zend_Dojo_Form_Element_NumberTextBox('allow_post');
		$_amount_day->setAttribs(array('dojoType'=>'dijit.form.NumberTextBox','required'=>'true',
				'class'=>'fullside',
				'onkeyup'=>'CalculateDate();',
				));
		$price = new Zend_Dojo_Form_Element_NumberTextBox('price');
		$price->setAttribs(array('dojoType'=>'dijit.form.NumberTextBox','required'=>'true',
				'class'=>'fullside',
				'onkeyup'=>'CalculateDate();',
		));
		
		$allow_show = new Zend_Dojo_Form_Element_NumberTextBox('allow_show');
		$allow_show->setAttribs(array('dojoType'=>'dijit.form.NumberTextBox','required'=>'true',
				'class'=>'fullside',
		));
		
		$amount_store = new Zend_Dojo_Form_Element_NumberTextBox('amount_store');
		$amount_store->setAttribs(array('dojoType'=>'dijit.form.NumberTextBox','required'=>'true',
				'class'=>'fullside',));
		
		$_note = new Zend_Dojo_Form_Element_TextBox('note');
		$_note->setAttribs(array('dojoType'=>'dijit.form.TextBox',
				'class'=>'fullside',));
		
		$_status=  new Zend_Dojo_Form_Element_FilteringSelect('status');
		$_status->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside',));
		$_status_opt = array(
				1=>$this->tr->translate("ACTIVE"),
				0=>$this->tr->translate("DACTIVE"));
		$_status->setMultiOptions($_status_opt);
		
		$is_free=  new Zend_Dojo_Form_Element_FilteringSelect('is_free');
		$is_free->setAttribs(array('dojoType'=>$this->filter,'class'=>'fullside',));
		$is_freeopt = array(
				1=>$this->tr->translate("Free"),
				0=>$this->tr->translate("Pay"));
		$is_free->setMultiOptions($is_freeopt);
		
		$_id = new Zend_Form_Element_Hidden('id');
		if(!empty($_data)){
			$_holiday_name->setValue($_data['package_name']);
			$_amount_day->setValue($_data['allow_post']);
			$_status->setValue($_data['status']);
			$_id->setValue($_data['id']);
			$_note->setValue($_data['description']);
			$is_free->setValue($_data['is_free']);
			$price->setValue($_data['price']);
			$allow_show->setValue($_data['allow_show']);
			$amount_store->setValue($_data['allow_store']);
		}
		$this->addElements(array($amount_store,$_status,$allow_show,$is_free,$price,$_btn_search,$_status_search,$_title,$_id,$_holiday_name,$_note,$_startdate,$_amount_day));
		return $this;
	}
	
}