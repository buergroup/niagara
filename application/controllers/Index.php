<?php
class IndexController extends Yaf_Controller_Abstract {

    private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
    }

    public function indexAction() {
        $user = new UserInfoModel();
        $user->showUserInfo();
        $this->_view->counter = Yaf_Registry::get('counter');
    }
    
}
