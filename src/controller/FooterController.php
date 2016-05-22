<?php
class FooterController extends SubController{
    protected function process($request){
        $data = array();
        $templates['post'] = preg_replace('~[\r\n]+~', '', file_get_contents(APP_PATH.'themes/'.APP_THEME.'/templates/post.html'));
        $data['templates'] = json_encode($templates);
        $this->render('footer.html',$data);
    }
}