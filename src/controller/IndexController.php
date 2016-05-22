<?php

class IndexController extends Controller{

    protected function process($request){
        $data = array();
        $data['post_count'] = AppManager::getInstance()->getPostCount();
        $data['view_count'] = AppManager::getInstance()->updateViewCount();
        $this->render('index.html',$data);
    }

    public function getHeaderController(){
        return new HeaderController();
    }

    public function getFooterController(){
        return new FooterController();
    }
}

Controller::addRoute(Controller::REQUEST_GET, "/", function() {
    $controller = new IndexController();
    $controller->handleRequest($_REQUEST);
});