<?php
/**
 * Replace audit status to text
 */
defined('AUDIT_AUDITING') or define('AUDIT_AUDITING', 22);
defined('AUDIT_ACCEPTED') or define('AUDIT_ACCEPTED', 23);
defined('AUDIT_REJECTED') or define('AUDIT_REJECTED', 24);
class G{
	static function GetOrderStats($status, $render_label = false){
		$map = array(
			0 => '审批中',
			1 => '已结束',
			2 => '已删除',
		);
		$label_map = array(
			0 =>'<span class="label label-warning">审批中</span>',
			1 =>'<span class="label label-info">已结束</span>',
			2 =>'<span class="label label-danger">已删除</span>',
		);
		if(isset($map[$status])){
			if($render_label){
				return $label_map[$status];
			}
			return $map[$status];
		}
		return '未知';
	}
	static function  GetAuditStatus($status, $render_label = false){
		$map = array(
			AUDIT_AUDITING => '待审核',
			AUDIT_ACCEPTED => '已通过',
			AUDIT_REJECTED => '已拒绝',
		);
		$label_map = array(
			AUDIT_AUDITING =>'<span class="label label-info">待审核</span>',
			AUDIT_ACCEPTED =>'<span class="label label-success">已通过</span>',
			AUDIT_REJECTED =>'<span class="label label-danger">已拒绝</span>',
		);
		if(isset($map[$status])){
			if($render_label){
				return $label_map[$status];
			}
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
	static function load(){
		return true;
	}
	static function RenderJson($status, $msg){
		$ret = array(
			'status' => $status,
			'msg'=>$msg,
			);
		echo json_encode($ret);die();
	}
}

/**
	 * 判断请求是否为手机浏览器
	 *
	 */
 function isMobile(){ 
		// 如果有HTTP_X_WAP_PROFILE则一定是移动设备
		if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
			return true;
		} 
		// 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
		if (isset ($_SERVER['HTTP_VIA'])){ 
			// 找不到为flase,否则为true
			return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
		} 
		// 脑残法，判断手机发送的客户端标志,兼容性有待提高
		if (isset ($_SERVER['HTTP_USER_AGENT'])){
			$clientkeywords = array ('nokia',
					'sony',
					'ericsson',
					'mot',
					'samsung',
					'htc',
					'sgh',
					'lg',
					'sharp',
					'sie-',
					'philips',
					'panasonic',
					'alcatel',
					'lenovo',
					'iphone',
					'ipod',
					'blackberry',
					'meizu',
					'android',
					'netfront',
					'symbian',
					'ucweb',
					'windowsce',
					'palm',
					'operamini',
					'operamobi',
					'openwave',
					'nexusone',
					'cldc',
					'midp',
					'wap',
					'mobile'
						); 
			// 从HTTP_USER_AGENT中查找手机浏览器的关键字
			if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
				return true;
			} 
		} 
		// 协议法，因为有可能不准确，放到最后判断
		if (isset ($_SERVER['HTTP_ACCEPT'])){ 
			// 如果只支持wml并且不支持html那一定是移动设备
			// 如果支持wml和html但是wml在html之前则是移动设备
			if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
			{
				return true;
			} 
		} 
		return false;
	}
