<?php
class FlowManageModel {

	private $_flowInfo;
	private $_flowLevel;

	public function __construct() {
		$this->_flowInfo = new FlowInfoModel();
		$this->_flowLevel = new FlowLevelModel();
	}

	public function addFlow($p) {
		$info = $p['flow_info'];
		if (!array_key_exists('create_time', $info)) {
			$info['create_time'] = time();
		}
		if (!array_key_exists('update_time', $info)) {
			$info['update_time'] = time();
		}
		$flowId = $this->_flowInfo->insert($info);
		for ($i=0; $i<sizeof($p['flow_levels']); $i++) {
			$level = $p['flow_levels'][$i];
			$level['flow_id'] = $flowId;
			$level['level'] = $i;
			if (!array_key_exists('create_time', $level)) {
				$level['create_time'] = time();
			}
			if (!array_key_exists('update_time', $level)) {
				$level['update_time'] = time();
			}
			$this->_flowLevel->insert($level);
		}
		return $flowId;
	}

	public function listFlows() {
		$select = $this->_flowInfo->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('status = ?', 0);
		 
		$rows = $this->_flowInfo->fetchAll($select);
		if (!$rows) {
			return null;
		}
		$rows = $rows->toArray();
		$ret = array();
		foreach ($rows as $row) {
			$info = array();
			$info['flow_info'] = $row;
			$select = $this->_flowLevel->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
			$select->setIntegrityCheck(false)
				->where('flow_id = ?', $row['flow_id'])
				->order('level');
			$levels = $this->_flowLevel->fetchAll($select);
			$info['flow_levels'] = $levels ? $levels->toArray() : array();

			$ret[] = $info;
		}
		return $ret;
	}

	//todo
	public function updateFlow($p) {
	}

	public function getFlow($p) {
		$select = $this->_flowInfo->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('flow_id = ?', $p);
		
		$row = $this->_flowInfo->fetchRow($select);
		if (!$row) {
			return null;
		}
		$row = $row->toArray();
		$info = array();
		$info['flow_info'] = $row;
		$select = $this->_flowLevel->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('flow_id = ?', $row['flow_id'])
			->order('level');
		$levels = $this->_flowLevel->fetchAll($select);
		if (!$levels) {
			$info['flow_levels'] = array();
			return $info;
		}
		$levels = $levels->toArray();
		$info['flow_levels'] = $levels;

		return $info;
	}
}

