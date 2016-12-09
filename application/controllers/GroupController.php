<?php
class ApplyController extends Yaf_Controller_Abstract {

    private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
    }

    public function indexAction() {
    }
    public function createAction() {
	    $this->_layout->meta_title = '填写申请单';
    }
    
    public function listAction() {
	    $this->_layout->meta_title = '组管理列表';
    }
}
