<?php
class GroupManageModel {

	private $_groupModel;
	private $_memberModel;

	public function __construct() {
		$this->_groupModel = new GroupInfoModel();
		$this->_memberModel = new GroupMemberModel();
	}

	public function addGroup($p) {
		return $this->_groupModel->insert($p);
	}

	public function listGroups() {
		$rows = $this->_groupModel->fetchAll();
		if (!$rows) {
			return null;
		}
		$rows = $rows->toArray();
		$ret = array();
		foreach ($rows as $row) {
			$info = array();
			$info['group_info'] = $row;
			$select = $this->_memberModel->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
			$select->setIntegrityCheck(false)
				->where('group_id = ?', $row['id']);
			$members = $this->_memberModel->fetchAll($select);
			$info['group_members'] = $members ? $members->toArray() : array();

			$ret[] = $info;
		}
		return $ret;
	}

	public function getGroup($p) {
		$select = $this->_groupModel->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('id = ?', $p);
		
		$row = $this->_groupModel->fetchRow($select);
		if (!$row) {
			return null;
		}
		$row = $row->toArray();
		$info = array();
		$info['group_info'] = $row;
		$select = $this->_memberModel->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
		$select->setIntegrityCheck(false)
			->where('group_id = ?', $row['id']);
		$members = $this->_memberModel->fetchAll($select);
		if (!$members) {
			$info['group_members'] = array();
			return $info;
		}
		$members = $members->toArray();
		$info['group_members'] = $members;

		return $info;
	}

	public function addUserToGroup(array $p) {
		foreach($p as $u) {
			$this->_memberModel->insert($u);
		}
		return true;
	}

	public function removeUserFromGroup(array $ps) {
		foreach($ps as $p) {
			$where = array();
			$where[] = $this->_memberModel->getAdapter()->quoteInto("group_id = ?", $p['group_id']);
			$where[] = $this->_memberModel->getAdapter()->quoteInto("user = ?", $p['user']);
		 
			$this->_memberModel->delete($where);
		}
		return true;
	}
}
