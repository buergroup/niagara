<?php
class DebugPlugin extends Yaf_Plugin_Abstract {

    public function dispatchLoopShutdown ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
        //所有的业务逻辑都已经运行完成, 响应发送之前
        $start = Yaf_Registry::get('debug_now');
        $time =  microtime(true)*1000 - $start;
        
        Yaf_Registry::set('debug_exec', $time);
        $time = Yaf_Registry::get('debug_exec');
        var_dump($time);die();
    }

    public function dispatchLoopStartup ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
    }

    public function postDispatch ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
    }

    public function preDispatch ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
 
    }

    public function preResponse ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
        
    }

    public function routerShutdown ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){

    }

    public function routerStartup ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
        //在路由之前触发
        Yaf_Registry::set('debug_now', microtime(true)*1000); 
    }
}