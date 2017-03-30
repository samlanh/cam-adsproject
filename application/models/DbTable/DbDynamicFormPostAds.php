<?php

class Application_Model_DbTable_DbDynamicFormPostAds extends Zend_Db_Table_Abstract
{
	public function setName($name){
		$this->_name=$name;
	}
	public static function getUserId(){
		$session_user=new Zend_Session_Namespace('auth');
		return $session_user->user_id;
	}
	
	function getAllFormTypeCateid($cate_id){// old test form
		$db = $this->getAdapter();
		$sql="SELECT * FROM `vd_field_type` AS ft WHERE FIND_IN_SET($cate_id,ft.`category`) AND ft.`status`=1";
		$row =  $db->fetchAll($sql);
		$form='';
		if (!empty($row)) foreach ($row as $rs){
			if($rs["is_require"]==1){
				$isreq= 'required="required"';
				$sigrequir='<span class="sign_req">*</span>';
			}else{ $isreq='';$sigrequir='';
			}
			if ($rs["type"]=="select"){
				$form.=$this->OptionSelect($rs["id"],$rs["title"],$rs["is_require"],$rs["label_name"]);
			}elseif ($rs["type"]=="cascade"){
				$form.='<div class="form-row">';
					$form.='<div class="form-label"> <label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="form-value">';
						$form.='<select onChange="getCascadeValue('.$rs["title"].')"  id="'.$rs["title"].'" name="'.$rs["title"].'" '.$isreq.' class="form-select">';
						$form.='</select>';
					$form.='</div>';
				$form.='</div>';
			}elseif ($rs["type"]=="text"){
				$form.='<div class="form-row">';
					$form.='<div class="form-label"> <label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="form-value">';
						$form.='<input class="form-control " type="text" value="" placeholder="'.$rs["label_name"].'"  '.$isreq.' id="'.$rs["title"].'" name="'.$rs["title"].'" />';
					$form.='</div>';
				$form.='</div>';
			}elseif ($rs["type"]=="number"){
				$form.='<div class="form-row">';
					$form.='<div class="form-label"> <label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="form-value">';
						$form.='<input class="form-control " onkeypress="return isNumber(event);" type="text" value="" placeholder="'.$rs["label_name"].'"  '.$isreq.' id="'.$rs["title"].'" name="'.$rs["title"].'" />';
					$form.='</div>';
				$form.='</div>';
			}elseif ($rs["type"]=="textarea"){
				$form.='<div class="form-row">';
					$form.='<div class="form-label"> <label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="form-value">';
						$form.=' <textarea class="form-control " id="'.$rs["title"].'" placeholder="'.$rs["label_name"].'"  name="'.$rs["title"].'" '.$isreq.' ></textarea>';
					$form.='</div>';
				$form.='</div>';
			}elseif ($rs["type"]=="emailaddress"){
				$form.='<div class="form-row">';
					$form.='<div class="form-label"> <label>'.$rs["label_name"].$sigrequir.'</label> </div>';
					$form.='<div class="form-value">';
						$form.=' <input type="email" id="'.$rs["title"].'" name="'.$rs["title"].'" '.$isreq.' autocomplete="off" placeholder="'.$rs["label_name"].'" />';
					$form.='</div>';
				$form.='</div>';
			}
		}
		return $form;
	}
	
	function OptionSelect($fielid,$name,$require,$labelname){ // old form test
		if($require==1){
			$isreq= 'required="required"';
			$sigrequir='<span class="sign_req">*</span>';
		}else{ $isreq=''; $sigrequir='';}
		$functionOnchage='';
		if ($this->checkFieldHasChild($fielid)>0){
			$controlname = "'".$name."'";
			$functionOnchage='onChange="getCascadeValue('.$controlname.')"';
		}
		$value = $this->getValueofOption($fielid);
		$string='';
		$string.='	<div class="form-row">';
			$string.='<div class="form-label"><label>'.$labelname.$sigrequir.'</label> </div>';
				$string.='<div class="form-value">';	
					$string.='<select '.$functionOnchage.'   id="'.$name.'" name="'.$name.'" '.$isreq.' class="form-select" >';
						$string.='<option value=""></option>';
						if (!empty($value)) foreach ($value as $rs){
						$string.='<option value="'.$rs['name'].'">'.$rs['name'].'</option>';
						}
					$string.='</select>';
				$string.='</div>';
		$string.='</div>';
		return $string;
	}
	function getValueofOption($fielid){
		$db = $this->getAdapter();
		$sql="SELECT fv.`id`,fv.`name` FROM `vd_field_value` AS fv WHERE fv.`field_type_id`=".$fielid;
		return $db->fetchAll($sql);
	}
	function checkFieldHasChild($parent){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(ft.`id`) AS counter FROM `vd_field_type` AS ft WHERE  ft.`status`=1 AND ft.`field_parent`=$parent";
		return $db->fetchOne($sql);
	}
	function checkChild($id){
		$db = $this->getAdapter();
		$sql="SELECT COUNT(c.`id`) AS r FROM `vd_category` AS c WHERE c.`parent`=$id";
		return $db->fetchOne($sql);
	}
	function getCascadeOption($parent){
		$db= $this->getAdapter();
		$sql="SELECT *,
			(SELECT ft.title FROM `vd_field_type` AS ft WHERE ft.id = fv.`field_type_id` LIMIT 1) AS fieldname
		 FROM `vd_field_value` AS fv WHERE fv.`parent`='$parent' ORDER BY fv.ordering ASC";
		$rows=$db->fetchAll($sql);
		$options = '';
		$fieldname='';
		if(!empty($rows))foreach($rows as $value){
			$fieldname=$value['fieldname'];
			$options .= '<option value="'.$value['name'].'" >'.htmlspecialchars($value['value'], ENT_QUOTES).'</option>';
		}
		$array = array("fieldname"=>$fieldname,"value"=>$options,);
		return $array;
	}

}
?>