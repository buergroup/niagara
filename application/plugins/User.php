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
        $uri = '/index';
        $user = new UserInfoModel();
        if(in_array(strtolower($request->controller), array('flow','index','group','apply','approval')) && 
            in_array(strtolower($request->action), array('list','show','create','index')) 
            ){
            $uri = "/".$request->controller."/".$request->action;
            $params = http_build_query($request->getQuery());
            if($params){
                $uri = $uri."?".$params;
            }
            $user->setForward($uri);
        }
        $userinfo = $user->showUserInfo();
        if(!$userinfo){
             $user->requiredLogin();
        }
        //判断Auth
        $auth = new AuthManageModel();
        $ret = $auth->canAccess($request->controller,$request->action);
        if($ret){
            throw new AuthException("Permission denied for the action", 1);
        }

    }

    public function routerStartup ( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
 
    }
}