<?php
class UserController extends Yaf_Controller_Abstract {

    private $_layout;
    private $_user;
    private $_query;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
        $this->_user = new UserInfoModel();
        $this->_query =  $this->getRequest();
    }

    public function indexAction() {
        $username = $this->_user->showUserInfo();
        var_dump($username);die();
    }
    public function loginAction() {
        if(!$this->_query->isGet()){
            $username = $this->_query->getPost('username');
            $password = $this->_query->getPost('password');
            $autologin = $this->_query->getPost('autologin');
            $ret = $this->_user->login($username, $password, $autologin?86400*7:86400);
            $data = array(
                    'status' => false,
                    'msg' => '登录错误',
                );
            if($ret){
               $userinfo = $this->_user->showUserInfo();
               $forward = $this->_user->getForward();
               $data['status'] = true;
               $data['msg'] = '登录成功';
               $data['userinfo'] = $userinfo;
               $data['forward'] = $forward;
            }
            echo json_encode($data);die();
        }
    }
    public function logoutAction() {
      // $this->_user->showUserInfo();
       $this->_user->logout();
       $this->redirect("/user/login");
       die();
    }
    
}