<?php

include_once('CAS.php');

class CAS_User
{
	private $_identity;
	public $casVersion;
	public $serverHost;
	public $serverPort;
	public $serverName;
	public $isProxy;
	public $clientPort;
	public $rootUrl;

	public function login() {
		try{
			$this->CASAuthentication();
			return ture;
		}
		catch (Exception $e) {
			throw new CHttpException(400, 'Invalid request or server has some problems. Please contract with administrator');
			return false;
		}
	}

	public function CASAuthentication() {
		try{
			phpCAS::client(CAS_VERSION_2_0, $this->serverHost, $this->serverPort, $this->serverName);
			phpCAS::setNoCasServerValidation();
			phpCAS::forceAuthentication();
		} catch(Exception $e) {
			throw $e;                        
		}
	}

	public function setUser() {
		echo "set user";
	}

	public function logout($destroySession = true) {
		phpCAS::client(CAS_VERSION_2_0, $this->serverHost, $this->serverPort, $this->serverName);
		phpCAS::logout(array('service'=>'not null'));
	}
}
