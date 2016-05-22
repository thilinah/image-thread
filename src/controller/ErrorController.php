<?php

use \NoahBuscher\Macaw\Macaw;

class ErrorController extends Controller{

    protected function process($request){
        $data = array();
        $this->render('error.html',$data);
    }

    public function getHeaderController(){
        return null;
    }

    public function getFooterController(){
        return null;
    }
}

Macaw::error(function() {
    $controller = new ErrorController();
    $controller->handleRequest($_REQUEST);
});