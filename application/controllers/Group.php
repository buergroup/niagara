<?php
class GroupController extends Yaf_Controller_Abstract {

    private $_layout;
    private $_query;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
        $this->_query =  $this->getRequest();
    }

    public function indexAction() {
    }
    public function createAction() {
        $name = $this->_query->getPost('name');
        $ret = array(
                'status' => false,
                'msg' => '创建失败',
            );
        if($name){
            $group = new GroupManageModel();
            $id = $group->addGroup(array('name'=>$name));
            if($id){
                $ret['status'] = true;
                $ret['msg'] = '创建成功';
            }
        }
        echo json_encode($ret);die();
    }
    
    public function listAction() {
        $group = new GroupManageModel();
        $this->_view->grouplist = $group->listGroups();
    }
    public function showAction() {
        $user = new UserInfoModel();
        $this->_view->userlist = $user->getDomobUserList();
        $group = new GroupManageModel();
        $gid = $this->_query->getQuery('id') ?: 0;
        if(intval($gid)){
            $this->_view->group = $group->getGroup(intval($gid));
        }
    }
    public function addUserAction() {
        $group = new GroupManageModel();
        $users = $this->_query->getPost('user');
        $group_id = $this->_query->getPost('group_id');
        $ret = array(
            'status' => false,
            'msg' => '创建失败',
        );
        if($users){
            $user_arr = explode(',', $users);
            foreach ($user_arr as $user) {
                $data[] = array(
                    'user'=>$user,
                    'group_id'=>$group_id,
                );
            }
            $count = $group->addUserToGroup($data);
            if($count){
                $ret['status'] = true;
                $ret['msg'] = '创建成功';
            }
        }
        echo json_encode($ret);die();
    }
}
