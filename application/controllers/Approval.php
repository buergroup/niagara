<?php
class ApprovalController extends Yaf_Controller_Abstract {

    private $_layout;

    public function init(){
        $this->_layout = Yaf_Registry::get('layout');
    }

    public function indexAction() {
    }
    
    public function listAction() {
	    $this->_layout->meta_title = '审批管理';
    }
}
