<?php
class ApprovalController extends Yaf_Controller_Abstract {

    private $_layout;
    public $userinfo;
    private $_query;
    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
        $_user = new UserInfoModel();
        $this->userinfo = $_user->showUserInfo();
        $this->_query =  $this->getRequest();
    }

    public function indexAction() {
    }
    
    public function listAction() {
    	//获取待审核列表
	   $order = new OrderManageModel();
	   $params = array(
	   		'audit_user' => $this->userinfo['username'],
	   		'status' => array(AUDIT_AUDITING),
	   	);
	   $this->_view->audit_auditing = $order->getAuditOrderByUser($params);
	   $params['status'] = array(AUDIT_REJECTED, AUDIT_ACCEPTED);
	   $this->_view->audit_audited = $order->getAuditOrderByUser($params);
	   //var_dump($this->_view->audit_auditing);die();
    }
    public function auditAction(){
        if(!$this->_query->isGet()){
        	$level = $this->_query->getPost('level');
	        $status = $this->_query->getPost('status');
	        $audit_info = $this->_query->getPost('content');
	        $audit_user = $this->userinfo['username'];
	        $orderid = $this->_query->getPost('orderid');

	        $data = array(
	        	'orderid'=>$orderid, 
				'level'=>$level,
			 	'status'=>$status, 
			 	'audit_info'=> $audit_info, 
			 	'audit_user'=>$audit_user
			 );
	         $order = new OrderManageModel();
    	 	$ret = $order->auditOrderResult($data);
    	 	if($ret){
    	 		if ($status == AUDIT_ACCEPTED) {
	    	 		$order->addNextOrderResult(array('orderid'=>$orderid));
	    	 	}
    	 		G::RenderJson(true, '审批成功');
    	 	}
    	 	G::RenderJson(false, '审批失败');
    	}
    	G::RenderJson(false, '参数错误');
    }
}
