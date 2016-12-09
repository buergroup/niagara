<?php
class UserPlugin extends Yaf_Plugin_Abstract {

    public function dispatchLoopShutdown ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){

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
        if($request->action == 'login'){
            return;
        }
        $uri = "/".$request->controller."/".$request->action;
        $params = http_build_query($request->getParams());
        if($params){
            $uri = $uri."?".$params;
        }
        $user = new UserInfoModel();
        $userinfo = $user->showUserInfo();
        if(!$userinfo){
             $user->setForward($uri);
             $user->requiredLogin();
        }
    }

    public function routerStartup ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
 
    }
}