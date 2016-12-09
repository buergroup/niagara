<?php
class UserInfoModel {
	private $_userrecord;
	public function __construct(){
		$this->_userrecord = new UserRecordModel();
	}
	/**
	 * 获取用户信息
	 */
	public function showUserInfo(){
		if($this->_userrecord->autoLoginFromCookie()){
			$userinfo = $this->_userrecord->getUserInfoFromRegistry();
			if($userinfo){
				return $userinfo;
			}
		}
		return array();
	}

	public function requiredLogin(){
		header("Location:/user/login");
	}
	public function setForward($url){
		setcookie("forward_uri",$url);
	}
	public function getForward(){
		return $_COOKIE['forward_uri']?$_COOKIE['forward_uri']:'/index';
	}
	public function login($username, $password, $duration){
		$user = $this->_userrecord;
		$user->username = $username;
		$user->password = $password;
		$user->duration = $duration;
		$ret = $user->authenticate();
		if($ret){
			return true;
		}
		return false;
	}

	public function logout(){
		$this->_userrecord->logout();
	}
	/**
	 * CAS Login
	 *
	 */
	public static function getCAS(){
		$config = Yaf_Application::app()->getConfig();
		$_cas = new CAS_User();
		$_cas->serverHost = $config->cas->params->serverHost;
		$_cas->serverPort = intval($config->cas->params->serverPort);
		$_cas->serverName = $config->cas->params->serverName;
		return $_cas;
	}
	public function CASlogin(){
		try{
			self::getCAS()->login();
			return phpCAS::getUser();
		}catch (CHttpException $e) {
			return false;
		}
		return true;
	}
	public function CASlogOut(){
		self::getCAS()->logout();
	}
	public function getDomobUserList(){
		return $this->_userrecord->getUserList();
	}
}