<?php

class HeaderController extends SubController{
    protected function process($request){
        $data = array();
        
        $this->render('header.html',$data);
    }

}