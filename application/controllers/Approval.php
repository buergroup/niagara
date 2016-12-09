<?php
class ApprovalController extends Yaf_Controller_Abstract {

    private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
    }

    public function indexAction() {
    }
    
    public function listAction() {
    	//获取待审核列表
	   $order = new OrderManageModel();
	   //$this->_view->auditlist = $order->getAuditBy($this->userinfo['username'], $status);

    }
}
