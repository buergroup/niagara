<?php 
class Bootstrap extends Yaf_Bootstrap_Abstract {

    private $_config;

    /*get a copy of the config*/
    public function _initBootstrap(){
        $this->_config = Yaf_Application::app()->getConfig();
    }


    public function _initIncludePath(){
        set_include_path(get_include_path() . PATH_SEPARATOR . $this->_config->application->library);
    }

    public function _initErrors(){
        if($this->_config->application->showErrors){
            error_reporting (-1);
            ini_set('display_errors','On');
        }
    }

    public function _initNamespaces(){
        Yaf_Loader::getInstance()->registerLocalNameSpace(array("Zend"));
        Yaf_Loader::getInstance()->registerLocalNameSpace(array("CAS"));
    }

    public function _initRoutes(){
        
    }

    public function _initLayout(Yaf_Dispatcher $dispatcher){
        $layout = new LayoutPlugin('layout.phtml');
        Yaf_Registry::set('layout', $layout);
        $dispatcher->registerPlugin($layout);
    }

    public function _initDefaultDbAdapterMysql(){
        $dbAdapter = new Zend_Db_Adapter_Pdo_Mysql(
            $this->_config->database->params->toArray()
        );
        Zend_Db_Table::setDefaultAdapter($dbAdapter);
    }
    public function _initUser(Yaf_Dispatcher $dispatcher){
        $user = new UserPlugin('layout.phtml');
        $dispatcher->registerPlugin($user);
    }


}
