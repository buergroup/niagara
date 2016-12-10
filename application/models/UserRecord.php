<?php
class UserRecordModel extends Zend_Db_Table_Abstract{
	protected $_name;

	public $username;
	public $password;
	public $duration;
	private $_status;

	public function __construct(){
		$this->_config = Yaf_Application::app()->getConfig();
		$this->_name = $this->_config->application->admintable;
		parent::__construct();
	}


	public function authenticate(){
		try{
			$select = $this->select();
			$select->where("username=?", $this->username);
			$select->where("password=?", md5($this->password.$this->password));
			$select->where("status=0");
			$rows =  $this->fetchAll($select);
			foreach ($rows as $user) {}
			if($user){
				$userinfo = array(
					'username' => $user->username,
					'userid' => $user->userid,
					'realname' => $user->realname,
				);
				$this->refreshSession($userinfo);
				return true;
			}
			return false;
		}catch(Exception $e) {
			return false;
		}

		return true;
	}
	public function _buidCookieVar($data){
		return base64_encode(serialize($data));
	}
	public function _getCookieVar($data){
		return unserialize(base64_decode($data));
	}
	public function refreshSession($userinfo){
		Yaf_Registry::set('user', $userinfo);
		setcookie('niagara_id', $this->_buidCookieVar($userinfo), time()+ $this->duration , '/');
		setcookie('expired', time() + $this->duration,time()+ $this->duration , '/');
	}
	public function autoLoginFromCookie(){
		if(isset($_COOKIE['niagara_id']) &&  $_COOKIE['expired'] > time()){
			$userinfo = $this->_getCookieVar($_COOKIE['niagara_id']);
			Yaf_Registry::set('user', $userinfo);
			return true;
		}
		return false;
	}
	public function getUserInfoFromRegistry(){
		$userinfo = Yaf_Registry::get('user');
		return $userinfo;
	}
	public function logout(){
		Yaf_Registry::del('user');
		setcookie('niagara_id',"",time()-3600, '/');
		return true;
	}
	public function getUserList(){
		$select = $this->select();
		$select->where("status=0");
		$rows =  $this->fetchAll($select);
		$list = array();
		foreach ($rows as $user) {
			$list[] = array(
					'userid' =>$user->userid,
					'username' =>$user->username,
					'realname' =>$user->realname,
				);
		}
		return $list;
	}
}