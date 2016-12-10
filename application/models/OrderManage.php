<?php
class OrderManageModel {

	private $_flowOrder;
	private $_orderResult;
	private $_flowInfo;
	private $_flowLevel;
	private $_groupMan;

	public function __construct() {
		$this->_flowOrder = new FlowOrderModel();
		$this->_orderResult = new FlowOrderResultModel();
		$this->_flowInfo = new FlowInfoModel();
		$this->_flowLevel = new FlowLevelModel();
		$this->_groupMan = new GroupManageModel();
	}

	public function addOrder($p) {
		if (!array_key_exists('create_time', $p)) {
			$p['create_time'] = time();
		}
		if (!array_key_exists('update_time', $p)) {
			$p['update_time'] = time();
		}
		$orderid = $this->_flowOrder->insert($p);
		if($orderid){
			 $this->addOrderResult(array('orderid'=>$orderid, 'level'=>0));
		}
		return $orderid;
	}

	public function updateOrder($p) {
		unset($p['claimer']);
		unset($p['flow_id']);
		$where = $this->_flowOrder->getAdapter()->quoteInto('orderid = ?', $p['orderid']);
		return $this->_flowOrder->update($p, $where);
	}

	/*
	 * $p array('orderid'=>xx, 'level'=>yy)
	 */
	public function addOrderResult($p) {
		$where = $this->_flowOrder->getAdapter()->quoteInto('orderid = ?', $p['orderid']);
		$order = $this->_flowOrder->fetchRow($where)->toArray();

		$where = array();
		$where[] = $this->_flowLevel->getAdapter()->quoteInto('flow_id = ?', $order['flow_id']);
		$where[] = $this->_flowLevel->getAdapter()->quoteInto('level = ?', $p['level']);
		
		$flowLevel = $this->_flowLevel->fetchRow($where);
		if(!$flowLevel) return false;
		$flowLevel = $flowLevel->toArray();

		$audits = array();
		$approver = $flowLevel['approver'];//case 3
		if (strpos($approver, '@')) { // email
			$audits = explode(',', $approver);
		} else {
			$gp = $this->_groupMan->getGroup($approver);
			if ($gp && $gp['group_members']) {
				foreach ($gp['group_members'] as $members) {
					$audits[] = $members['user'];
				}
			}
		}

		foreach ($audits as $audit) {
			$data = array(
				'orderid'=>$p['orderid'],
				'level'=>$p['level'],
				'claimer'=>$order['claimer'],
				'status'=>22,
				'audit_info'=>'',
				'audit_user'=>$audit,
				'create_time'=>time(),
				'update_time'=>time(),
			);
			$this->_orderResult->insert($data);
		}
		return true;
	}

	/*
	 * $p array('orderid'=>xx)
	 */
	public function addNextOrderResult($p) {
		$where = $this->_flowOrder->getAdapter()->quoteInto('orderid = ?', $p['orderid']);
		$order = $this->_flowOrder->fetchRow($where)->toArray();

		$select = $this->_flowLevel->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('flow_id = ?', $order['flow_id'])
			->order('level desc')
			->limit(1);
		$orderMaxLevel = $this->_flowLevel->fetchRow($select);//->toArray()['level'];
		if (!$orderMaxLevel) return false;
		$orderMaxLevel = $orderMaxLevel->toArray()['level'];

		$select = $this->_orderResult->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('orderid = ?', $p['orderid'])
			->order('level desc')
			->limit(1);
		$resultMaxLevel = $this->_orderResult->fetchRow($select);
		$resultMaxLevel = $resultMaxLevel ? $resultMaxLevel->toArray()['level'] : -1;
		if ($orderMaxLevel == $resultMaxLevel) {
			return true;
		}

		$p['level'] = $resultMaxLevel + 1;


		$where = array();
		$where[] = $this->_flowLevel->getAdapter()->quoteInto('flow_id = ?', $order['flow_id']);
		$where[] = $this->_flowLevel->getAdapter()->quoteInto('level = ?', $p['level']);
		
		$flowLevel = $this->_flowLevel->fetchRow($where)->toArray();

		$audits = array();
		$approver = $flowLevel['approver'];//case 3
		if (strpos($approver, '@')) { // email
			$audits = explode(',', $approver);
		} else {
			$gp = $this->_groupMan->getGroup($approver);
			if ($gp && $gp['group_members']) {
				foreach ($gp['group_members'] as $members) {
					$audits[] = $members['user'];
				}
			}
		}

		foreach ($audits as $audit) {
			$data = array(
				'orderid'=>$p['orderid'],
				'level'=>$p['level'],
				'claimer'=>$order['claimer'],
				'status'=>22,
				'audit_info'=>'',
				'audit_user'=>$audit,
				'create_time'=>time(),
				'update_time'=>time(),
			);
			$this->_orderResult->insert($data);
		}
		return true;
	}

	public function auditOrderResult($p) {
		$where = $this->_flowOrder->getAdapter()->quoteInto('orderid = ?', $p['orderid']);
		$order = $this->_flowOrder->fetchRow($where)->toArray();

		$where = array();
		$where[] = $this->_flowLevel->getAdapter()->quoteInto('flow_id = ?', $order['flow_id']);
		$where[] = $this->_flowLevel->getAdapter()->quoteInto('level = ?', $p['level']);
		
		$flowLevel = $this->_flowLevel->fetchRow($where)->toArray();

		$audits = array();
		$approver = $flowLevel['approver'];//case 3
		if (strpos($approver, '@')) { // email
			$audits = explode(',', $approver);
		} else {
			$gp = $this->_groupMan->getGroup($approver);
			if ($gp && $gp['group_members']) {
				foreach ($gp['group_members'] as $members) {
					$audits[] = $members['user'];
				}
			}
		}

		$where = array();
		$where[] = $this->_orderResult->getAdapter()->quoteInto('orderid = ?', $p['orderid']);
		$where[] = $this->_orderResult->getAdapter()->quoteInto('level = ?', $p['level']);
		$where[] = $this->_orderResult->getAdapter()->quoteInto('audit_user = ?', $p['audit_user']);
		
		$p['update_time'] = time();
		$this->_orderResult->update($p, $where);

		$closed = false;
		if ($p['status'] == 24) {
			$closed = true;
		} else {
			$select = $this->_flowLevel->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
			$select->setIntegrityCheck(false)
				->where('flow_id = ?', $order['flow_id'])
				->order('level desc')
				->limit(1);
			$orderMaxLevel = $this->_flowLevel->fetchRow($select)->toArray()['level'];

			$select = $this->_orderResult->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
			$select->setIntegrityCheck(false)
				->where('orderid = ?', $p['orderid'])
				->order('level desc')
				->limit(1);
			$resultMaxLevel = $this->_orderResult->fetchRow($select);
			$resultMaxLevel = $resultMaxLevel ? $resultMaxLevel->toArray()['level'] : -1;
			if ($orderMaxLevel == $resultMaxLevel) {
				$closed = true;
			}
		}

		if ($closed) {
			$where = $this->_flowOrder->getAdapter()->quoteInto('orderid = ?', $p['orderid']);
			$data = array();
			$data['update_time'] = time();
			$data['status'] = 1;//0: open, 1: close, 2: delete
			$this->_flowOrder->update($data, $where);
		}
		return true;
	}

	public function getOrderResult($p) {
		$select = $this->_orderResult->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('orderid = ?', $p)
			->order('level')
			->order('update_time desc');
		 
		$rows = $this->_orderResult->fetchAll($select);
		$list = $rows ? $rows->toArray() : array();
		//过滤出同一层级的有效结果
		foreach ($rows as $row) {
			$ret[$row['level']] = $row->toArray();
			if($row->status != 22){
				$tmp[$row['level']]['audit_user'] = $row->audit_user;
				$tmp[$row['level']]['audit_info'] = $row->audit_info;
			}
		}
		if(is_array($tmp))
			foreach ($tmp as $level => $data) {
			$ret[$level]['audit_user'] = $data['audit_user'];
			$ret[$level]['audit_info'] = $data['audit_info'];
		}
		return $ret;
	}

	public function getOrderById($orid){
		$orderinfo = $this->_flowOrder->find($orid)->toArray()[0];
		return array(
			'flowinfo'=>$this->_flowInfo->find($orderinfo['flow_id'])->toArray()[0],
			'orderinfo'=>$orderinfo,
			'orderauditinfo' => $this->getOrderResult($orid),
		);
	}

	public function getAuditOrderByUser(array $p) {
		$select = $this->_orderResult->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('audit_user = ?', $p['audit_user'])
			->where('status in (?)', $p['status'])
			->order('update_time desc');
		$rows = $this->_orderResult->fetchAll($select);
		$rows = $rows ? $rows->toArray() : array();
		if (!$rows) {
			return array();
		}

		$ret = array();
		foreach ($rows as $row) {
			$audit = array();
			$audit['flow_order_result'] = $row;
			$audit['flow_order'] = $this->_flowOrder->find($row['orderid'])->toArray()[0];
			$flow_id = $audit['flow_order']['flow_id'];
			$audit['flow_info'] = $this->_flowInfo->find($flow_id)->toArray()[0];
			$ret[] = $audit;
		}

		return $ret;
	}

	
	public function getOrderByUser($p) {
		$select = $this->_flowOrder->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('claimer = ?', $p)
			->order('update_time desc');
		 
		$rows = $this->_flowOrder->fetchAll($select);
		return $rows ? $rows->toArray() : array();
	}

	public function counterWaitingOrderByUser($user){
		$select = $this->_flowOrder->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('claimer = ? AND status = 0', $user)
			->order('update_time desc');
		$rows = $this->_flowOrder->fetchAll($select);
		$list = $rows ? $rows->toArray() : array();
		return count($list);
	}

	public function counterWaitingAuditByUser($user){
		$list = $this->getAuditOrderByUser(array('audit_user'=>$user, 'status'=>array(22)));
		return count($list);
	}

	public function countFlows(){
		$select = $this->_flowInfo->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('status = ?', 0);
		$rows = $this->_flowInfo->fetchAll($select);
		$list = $rows ? $rows->toArray() : array();
		return count($list);
	}

	public function getCounter(){
		$user = new UserInfoModel();
		$userinfo = $user->showUserInfo();
		$ret = array(
              'counter_apply' => $this->counterWaitingOrderByUser($userinfo['username']),
              'counter_audit' => $this->counterWaitingAuditByUser($userinfo['username']),
              'counter_flows' => $this->countFlows(),
		 );
		Yaf_Registry::set('counter', $ret);
	}
}
