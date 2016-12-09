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
		//                getOrderByUser($user) @return (array of orders , order by update_time desc )
		//                        getOrderResult($orderid) @return (array of flow order result , order by update_time desc )
		//                                //getWatchOrderByUser($user) @return (array of flow order , order by update_time desc )))))))))))))))))
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
		return $this->_orderResult->update($p, $where);
	}

	public function getOrderResult($p) {
		$select = $this->_orderResult->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('orderid = ?', $p)
			->order('level')
			->order('update_time desc');
		 
		$rows = $this->_orderResult->fetchAll($select);
		return $rows ? $rows->toArray() : array();
	}

	public function getOrderByUser($p) {
		$select = $this->_flowOrder->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('claimer = ?', $p)
			->order('update_time desc');
		 
		$rows = $this->_flowOrder->fetchAll($select);
		return $rows ? $rows->toArray() : array();
	}
}
