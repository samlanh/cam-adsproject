<?php

class Group_Model_DbTable_DbContractor extends Zend_Db_Table_Abstract
{

    protected $_name = 'ln_contract_person';
    public function getUserId(){
    	$session_user=new Zend_Session_Namespace('auth');
    	return $session_user->user_id;
    	 
    }
	public function addContractor($_data){
		try{
		    $_arr=array(
				'namekh'	  => $_data['name_kh'],
				'sex'	  => $_data['sex'],
				'age'	      => $_data['age'],
				'nationality'			=>$_data['nationality'],
		    	'nation_id'      => $_data['national_id'],
		    	'phone'      => $_data['phone'],
				'wife_namekh'      => $_data['hname_kh'],
		    	'wife_sex'      => $_data['ksex'],
				'wife_age'      => $_data['p_age'],
				'wife_nationlity'      => $_data['p_nationality'],
				'wife_nation_id'  => $_data['wife_nation_id'],
				'wife_phone'	  => $_data['lphone'],
// 				'province'	      => $_data['province'],
// 				'district'  =>$_data['district'],
// 				'commune'=>$_data['commune'],
// 		    	'village'=>$_data['village'],
// 	    		'group'  =>$_data['group'],
// 	    		'house_no'=>$_data['house'],
// 	    		'street'=>$_data['street'],
	    		'status'=>$_data['status'],
	    		'current_address'	  => $_data['current_address'],
		    	'user_id'      => $this->getUserId(),
		    	'date'=> date("Y-m-d"),
		);
		if(!empty($_data['id'])){
			$where = 'id = '.$_data['id'];
			$this->update($_arr, $where);
			return $_data['id'];
			 
		}else{
			return  $this->insert($_arr);
		}
		}catch(Exception $e){
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}
	}
	public function getContractorById($id){
		$db = $this->getAdapter();
		$sql = "SELECT * FROM $this->_name WHERE id = ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function getClientDetailInfo($id){
		$db = $this->getAdapter();
		$sql = "SELECT c.client_id , c.client_number ,c.`name_en`,c.`name_kh`,c.`sex`,
		c.`id_number`,c.`id_type`,c.`acc_number`,c.`phone`,c.`dob`,c.`pob`,c.`tel`,c.`email`,c.`street`,c.`house`,
		(SELECT v.name_en FROM `ln_view` AS v WHERE v.type=23 AND v.key_code = c.`client_d_type`) AS doc_name,
		(SELECT commune_name FROM `ln_commune` WHERE com_id = c.com_id   LIMIT 1) AS commune_name,
		(SELECT commune_namekh FROM `ln_commune` WHERE com_id = c.com_id   LIMIT 1) AS commune_namekh
		,(SELECT district_name FROM `ln_district` AS ds WHERE dis_id = c.dis_id  LIMIT 1) AS district_name
		,(SELECT district_namekh FROM `ln_district` AS ds WHERE dis_id = c.dis_id  LIMIT 1) AS district_namekh
		,(SELECT province_en_name FROM `ln_province` WHERE province_id= c.pro_id  LIMIT 1) AS province_en_name
                ,(SELECT province_kh_name FROM `ln_province` WHERE province_id= c.pro_id  LIMIT 1) AS province_kh_name		
		,(SELECT village_name FROM `ln_village` WHERE vill_id = c.village_id  LIMIT 1) AS village_name 
		,(SELECT village_namekh FROM `ln_village` WHERE vill_id = c.village_id  LIMIT 1) AS village_namekh , 
		(SELECT project_name FROM `ln_project` WHERE br_id =c.branch_id LIMIT 1) AS project_name ,
		 c.`remark`,c.`status`,
		 c.bname_kh,c.`hname_kh`,c.`lphone`,c.`ksex`,
		c.`dstreet`,c.`ghouse`,c.`nationality`,c.`nation_id`,c.`p_nationality`,c.`rid_no`,c.`arid_no`,c.refe_nation_id,
		(SELECT v.name_en FROM `ln_view` AS v WHERE v.type=23 AND v.key_code = c.`joint_doc_type`) AS join_doc_name,
		 c.photo_name FROM `ln_client` AS c WHERE client_id =  ".$db->quote($id);
		$sql.=" LIMIT 1 ";
		$row=$db->fetchRow($sql);
		return $row;
	}
	public function getClientCallateralBYId($client_id){
		$db = $this->getAdapter();
		$sql = " SELECT cc.id AS client_coll ,cd.* FROM `ln_client_callecteral` AS cc , `ln_client_callecteral_detail` AS cd WHERE  
		         cd.is_return=0 AND cd.client_coll_id = cc.id AND cc.client_id = ".$client_id;
		return $db->fetchAll($sql);
	}
    function getViewClientByGroupId($group_id){
    	$db = $this->getAdapter();
    	$sql=" SELECT * FROM $this->_name WHERE client_id=
    	(SELECT client_id FROM `ln_loan_member` WHERE group_id=".$db->quote($group_id)." LIMIT 1)";
    	$row=$db->fetchRow($sql);
    	return $row;
    }
	function getAllContractor($search = null){		
		$db = $this->getAdapter();
		$from_date =(empty($search['start_date']))? '1': "c.date >= '".$search['start_date']." 00:00:00'";
		$to_date = (empty($search['end_date']))? '1': "c.date <= '".$search['end_date']." 23:59:59'";
	
		$sql = "
		SELECT 
			c.`id`,c.`namekh`,
			(SELECT name_en FROM `ln_view` WHERE TYPE =11 AND c.`sex`=key_code LIMIT 1) AS sex,
			c.`nationality`,c.`age`,c.`nation_id`,c.`phone`,c.`current_address`,
			c.`date`,
			c.`status` FROM $this->_name  AS c WHERE c.`status`> -1";
		if(empty($search['show_all'])){
			$where = " AND c.namekh!='' AND ".$from_date." AND ".$to_date;
			if(!empty($search['adv_search'])){
				$s_where = array();
				$s_search = addslashes(trim($search['adv_search']));
				$s_where[] = " c.namekh LIKE '%{$s_search}%'";
				$s_where[] = " c.nationality LIKE '%{$s_search}%'";
				$s_where[] = " c.age LIKE '%{$s_search}%'";
				$s_where[] = " c.nation_id LIKE '%{$s_search}%'";
				$s_where[] = " c.phone LIKE '%{$s_search}%'";
				$s_where[] = " c.house_no LIKE '%{$s_search}%'";
				$where .=' AND ('.implode(' OR ',$s_where).')';
			}
			if($search['status']>-1){
				$where.= " AND status = ".$search['status'];
			}
		}else{$where ='';}
// 		if($search['province_id']>0){
// 			$where.=" AND pro_id= ".$search['province_id'];
// 		}
// 		if(!empty($search['district_id'])){
// 			$where.=" AND dis_id= ".$search['district_id'];
// 		}
// 		if(!empty($search['comm_id'])){
// 			$where.=" AND com_id= ".$search['comm_id'];
// 		}
// 		if(!empty($search['village'])){
// 			$where.=" AND village_id= ".$search['village'];
// 		}
		$order=" ORDER BY id DESC ";
		return $db->fetchAll($sql.$where.$order);	
	}
	public function getGroupCodeBYId($data){
		$db = $this->getAdapter();
			$sql = " SELECT *,
				(SELECT t.type_nameen FROM `ln_properties_type` as t WHERE t.id=property_type) As property_type
				FROM `ln_properties` 
			WHERE id = ".$data['land_id']." LIMIT 1" ;
			 $rs = $db->fetchRow($sql);
			if(empty($rs)){return ''; }else{
				return $rs;
			}
		
	}
	function getPrefixCode($branch_id){
		$db  = $this->getAdapter();
		$sql = " SELECT prefix FROM `ln_branch` WHERE br_id = $branch_id  LIMIT 1";
		return $db->fetchOne($sql);
	}	
	public function getClientCode(){//for get client by branch
		$db = $this->getAdapter();
			$sql = "SELECT COUNT(client_id) AS number FROM `ln_client`
			WHERE 1 ";
		$acc_no = $db->fetchOne($sql);
		$new_acc_no= (int)$acc_no+1;
		$acc_no= strlen((int)$acc_no+1);
		$pre ="";
		for($i = $acc_no;$i<6;$i++){
			$pre.='0';
		}
		return $pre.$new_acc_no;
	}
// 	public function adddoocumenttype($data){
		
// 		$db = $this->getAdapter();
// 		$document_type=array(
// 				'name_en'=>$data['clienttype_nameen'],
// 				'name_kh'=>$data['clienttype_namekh'],
// 				'displayby'=>1,
// 				'type'=>23,
// 				'status'=>1
				
// 		);
		
// 		$row= $this->insert($document_type);
// 		return $row;
// 	}
	public function addIndividaulClient($_data){
		
		$client_code = $this->getClientCode($_data['branch_id']);
			$_arr=array(
					'is_group'=>0,
					'group_code'=>'',
					'parent_id'=>0,
					'client_number'=>$client_code,
					'name_kh'	  => $_data['name_kh'],
					'name_en'	  => $_data['name_en'],
					'sex'	      => $_data['sex'],
					'sit_status'  => $_data['situ_status'],
					'dis_id'      => $_data['district'],
					'village_id'  => $_data['village'],
					'street'	  => $_data['street'],
					'house'	      => $_data['house'],
					'branch_id'  => $_data['branch_id'],
					'job'        =>$_data['job'],
					'phone'	      => $_data['phone'],
					'create_date' => date("Y-m-d"),
					'client_d_type'      => $_data['client_d_type'],
					'user_id'	  => $this->getUserId(),
					'dob'			=>$_data['dob_client'],	
					'pro_id'      => $_data['province'],
					'com_id'      => $_data['commune'],
					
			
			);
			
				$this->_name = "ln_client";
				$id =$this->insert($_arr);
				return array('id'=>$id,'client_code'=>$client_code);
	}
	function deleteContractor($id){
		$db = $this->getAdapter();
		$arr = array( 'status'=> -1);
		$where = ' id = '.$id;
		$this->_name = "ln_contract_person";
		$this->update($arr, $where);
	}
}

