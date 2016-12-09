<?php
class AuthManageModel{
	private $_config;
	private $_user;
	public $userinfo;
	public function __construct(){
		$this->_config = Yaf_Application::app()->getConfig();
		$_user = new UserInfoModel();
		$this->userinfo = $_user->showUserInfo();
	}
	public function getAdminGroup(){
		//暂且从配置中获取管理员组ID
		return $this->_config->application->admingroup;

	}
	public function getAdminAction(){
		return array(
			'group/list',
			'group/list',
			'group/show',
			'group/adduser',
			'flow/create',
		);
	}
	public function Visiable(){
		$g = new GroupManageModel();
		return $g->isInGroup($this->userinfo['username'], $this->getAdminGroup());
	}
	public function canAccess($c, $a){
		return !$this->Visiable() && in_array(strtolower($c.'/'.$a),$this->getAdminAction());
	}
}

class AuthException extends Exception {

}