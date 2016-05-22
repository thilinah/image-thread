<?php
include(dirname(__FILE__).'/include.inc.php');
use \NoahBuscher\Macaw\Macaw;

Macaw::get('/a/(:any)', function($action) {
    $actionClass = AjaxControllerHandler::getInstance()->getActionClass($action, 'get');
    $actionClass->process($_REQUEST);
});

Macaw::post('/a/(:any)', function($action) {
    $actionClass = AjaxControllerHandler::getInstance()->getActionClass($action, 'post');
    $actionClass->process($_REQUEST);
});

foreach(Controller::$routes as $path=>$data){
    $method = $data[0];
    $function = $data[1];
    Macaw::$method($path, $function);
}

Macaw::dispatch();