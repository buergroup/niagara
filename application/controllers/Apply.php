<?php
class ApplyController extends Yaf_Controller_Abstract {

    private $_layout;
    private $_query;
    public $userinfo;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
        $this->_query =  $this->getRequest();
        $_user = new UserInfoModel();
        $this->userinfo = $_user->showUserInfo();
    }

    public function indexAction() {
        
    }
    public function createAction() {
        $flow_id = $this->_query->getQuery('flow') ?: 0;
	    $flow = new FlowManageModel();
        $ret = $flow->getFlow($flow_id);
        if($ret){
            $this->_view->flow = $ret;
        }else{
            var_dump("error param");die();
        }
        
        if(!$this->_query->isGet()){
            $data = $this->_query->getPost();
  
            $order = new OrderManageModel();
            $addOrder = array(
                'claimer' => $this->userinfo['username'],
                'flow_id' => $flow_id,
                'summary' => $data['summary'],
                'content' => $data['content'],
            );
            $orderid = $order->addOrder($addOrder);
            if($orderid){
                $this->redirect('/apply/list');
            }else{
                $this->view->params = $data;
            }
        }
    }
    
    public function listAction() {
        $order = new OrderManageModel();
        $orderlist = $order->getOrderByUser($this->userinfo['username']);
        if($orderlist){
            $this->_view->orderlist = $orderlist;
        }
    }
}
