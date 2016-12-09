<?php
/**
 * Replace audit status to text
 */
defined('AUDIT_AUDITING') or define('AUDIT_AUDITING', 22);
defined('AUDIT_ACCEPTED') or define('AUDIT_ACCEPTED', 23);
defined('AUDIT_REJECTED') or define('AUDIT_REJECTED', 24);
class G{
	static function  GetAuditStatus($status){
		$map = array(
			AUDIT_AUDITING => '待审核',
			AUDIT_AUDITING => '已通过',
			AUDIT_REJECTED => '已拒绝',
		);
		if(isset($map[$status])){
			return $map[$status];
		}
		return '未知';
	}
	static function GetRealName($username, $only_user_name = true){
		$user = new UserInfoModel();
		$userlist = $user->getDomobUserList();
		$t = array_filter($userlist , function($u) use($username){
			return $u['username'] == $username;
		});	
		foreach ($t as $key => $tuser) {}
		if($only_user_name){
			return $tuser['realname']?$tuser['realname']:'未知用户';
		}
		return $tuser;
	}
	static function RenderActive($c, $a = null){
		if(self::GetIsActive($c, $a)){
			echo "active";
		}
	}
	static function GetIsActive($c, $a = null){	
		$request = Yaf_Registry::get('request');
		$controller = strtolower($request->getControllerName());
		$action = strtolower($request->getActionName());
		if($c == $controller){
			if($a){
				if($action == $a) return true;
					return false;
			}
			return true;
		}
	}
}
