<?php
class LayoutPlugin extends Yaf_Plugin_Abstract {

    private $_layoutDir;
    private $_layoutFile;
    private $_layoutVars =array();

    public function __construct($layoutFile, $layoutDir=null){
        $this->_layoutFile = $layoutFile;
        $this->_layoutDir = ($layoutDir) ? $layoutDir : APP_PATH.'views/';
    }

    public function  __set($name, $value) {
        $this->_layoutVars[$name] = $value;
    }

    public function dispatchLoopShutdown ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){

    }

    public function dispatchLoopStartup ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
        $action = $request->getActionName();
        if($action == 'login' ||  $action == 'logout'){
            return;
        }
        $order = new OrderManageModel();
        $order->getCounter();
    }

    public function postDispatch ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
        $action = $request->getActionName();
        if($action == 'login' ||  $action == 'logout'){
            return;
        }
        Yaf_Registry::set('request', $request);
        $body = $response->getBody();
        $response->clearBody();
        $layout = new Yaf_View_Simple($this->_layoutDir);
        $layout->content = $body;
        $user = new UserInfoModel();
        $counter = Yaf_Registry::get('counter');
        $layout->counter = $counter;
        $layout->user = $user->showUserInfo();
        $layout->assign('layout', $this->_layoutVars);
        /* set the response to use the wrapped version of the content */
        $response->setBody($layout->render($this->_layoutFile));
    }

    public function preDispatch ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
 
    }

    public function preResponse ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
        $order = new OrderManageModel();
        $counter = $order->getCounter();
    }

    public function routerShutdown ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){

    }

    public function routerStartup ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){

    }
}