<?php

class Response{

    const SUCCESS = "SUCCESS";
    const ERROR = "ERROR";

    var $status;
    var $data;

    public function __construct($status,$data = null){
        $this->status = $status;
        $this->data = $data;
    }

    public function getStatus(){
        return $this->status;
    }

    public function getData(){
        return $this->data;
    }

    public function getObject(){
        return $this->data;
    }

    public function getJsonArray(){
        return array("status"=>$this->status,"data"=>$this->data);
    }
}

abstract class Controller {

    const REQUEST_GET = "get";
    const REQUEST_POST = "post";
    const REQUEST_PUT = "put";
    const REQUEST_DELETE = "delete";
    const REQUEST_HEAD = "head";

    var $twig;
    var $theme;
    public static $routes = array();

    function __construct(){
        $this->theme = APP_THEME;
        if(empty($this->theme)){
            echo "No theme defined";
            exit();
        }
        LogManager::getInstance()->debug("Twig Template Path:".APP_PATH.'themes/'.$this->theme);
        $loader = new Twig_Loader_Filesystem(APP_PATH.'themes/'.$this->theme);
        $twigOptions = array();
        if(defined('CACHE_THEME') && CACHE_THEME){
            $twigOptions = array(
                'cache' => '/tmp/cache_'.str_replace('/','_',ADMIN_BASE_PATH),
            );
        }
        $this->twig = new Twig_Environment($loader, $twigOptions);
    }

    public function render($template, $data){
        $data['app_url'] = APP_URL;
        $data['theme_url'] = APP_URL.'themes/'.$this->theme."/";
        $template = $this->twig->loadTemplate($template);
        echo $template->render($data);
    }

    public function handleRequest($request){
        $headerController = $this->getHeaderController();
        if(!empty($headerController)){
            echo $headerController->handleRequest($request);
        }

        $this->process($request);

        $footerController = $this->getFooterController();
        if(!empty($footerController)){
            echo $footerController->handleRequest($request);
        }

    }

    protected abstract function process($request);
    public abstract function getHeaderController();
    public abstract function getFooterController();
    

    public static function addRoute($type, $path, $function){

        self::$routes[$path] = array($type, $function);
    }

}


abstract class SubController extends Controller{
    public function getHeaderController(){
        return null;
    }

    public function getFooterController(){
        return null;
    }
}

class AjaxControllerHandler{
    private static $me = null;

    private function __construct(){

    }

    public static function getInstance(){
        if(self::$me == null){
            self::$me = new AjaxControllerHandler();
        }
        return self::$me;
    }

    private $ajaxActions = array();

    public function addAction($action, $class){
        $this->ajaxActions[$action] = $class;
    }

    public function getActionClass($action, $type){
        if(isset(AjaxControllerHandler::getInstance()->ajaxActions[$action]) && !empty(AjaxControllerHandler::getInstance()->ajaxActions[$action])){
            $class = AjaxControllerHandler::getInstance()->ajaxActions[$action];
            $obj = new $class();
            if($obj->getType() == $type){
                return $obj;
            }

        }
        return new ErrorActionController();
    }
}


abstract class AjaxController{

    public abstract function handleRequest($request);

    public function process($request){
        $response = $this->handleRequest($request);
        echo json_encode($response->getJsonArray());
    }

    public abstract function getAction();
}

class ErrorActionController extends AjaxController{

    public function handleRequest($request){
        return new Response(Response::ERROR,'Action not defined');
    }

    public function getAction(){
        return 'error';
    }
}

