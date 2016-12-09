<?php
class FlowController extends Yaf_Controller_Abstract {

    private $_layout;
    private $_query;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
        $this->_query =  $this->getRequest();
    }

    public function indexAction() {
    }
    public function createAction() {
        $user = new UserInfoModel();
        $userinfo = $user->showUserInfo();
        $this->_view->userlist = $user->getDomobUserList();
        if(!$this->_query->isGet()){
            $data = $this->_query->getPost();
            $flowdata = array(
                'flow_info'=>array(
                    'name'=> $data['name'],
                    'desc'=>$data['desc'],
                    'creator'=>$userinfo['username'],
                    )
            );
            foreach ($data['level'] as $level) {
               $flowdata['flow_levels'][] = array(
                    'name'=>$level['desc'],
                    'approver'=>$level['approver'],
                    'watcher'=>$level['watcher']
                );
            }
            $flow = new FlowManageModel();
            $ret = $flow->addFlow($flowdata);
            if($ret){
                $this->redirect('/flow/list');
            }else{
                $this->view->params = $data;
            }
        }

    }
    
    public function listAction() {
         $flow = new FlowManageModel();
         $flowList = $flow->listFlows();
         $this->_view->flowlist = $flowList;
         //var_dump($flowList);die();
    }

}
