<?php
class DebugPlugin extends Yaf_Plugin_Abstract {

    public function dispatchLoopShutdown ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
        Yaf_Registry::set('debug_now', microtime(true)*1000;); 
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
        
    }
}