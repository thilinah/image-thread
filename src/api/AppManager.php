<?php

class AppManager {

    private static $me = null;

    private function __construct(){

    }

    public static function getInstance(){
        if(empty(self::$me)){
            self::$me = new AppManager();
        }

        return self::$me;
    }
    
    public function updateViewCount(){
        $meta = new MetaData();
        $meta->Load("name = ?",array('total_page_views'));
        if($meta->name != 'total_page_views'){
            $meta->name = 'total_page_views';
            $meta->value = 1;
        }else{
            $meta->value = 1 + intval($meta->value);

        }
        $meta->updated = date("Y-m-d H:i:s");
        $meta->Save();
        return $meta->value;
    }

    public function getPostCount(){
        $post = new Post();
        $rs = $post->DB()->Execute("select id from Posts",array());
        return $rs->RecordCount();
    }

    public function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function createZip($files ,$destination) {
        $valid_files = array();
        if(is_array($files)) {
            foreach($files as $file=>$val) {
                if(file_exists($file)) {
                    $valid_files[$file] = $val;
                }
            }
        }
        LogManager::getInstance()->error("Valid Files:".print_r($valid_files,true));
        if(count(array_values($valid_files))) {
            $zip = new ZipArchive();
            $resp = $zip->open($destination,ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
            if($resp !== true) {
                return false;
            }
            foreach($valid_files as $file=>$newFileName) {
                $zip->addFile($file,$newFileName);
            }
            $zip->close();
            $fileExists = file_exists($destination);
            return $fileExists;
        }
        LogManager::getInstance()->error("No Valid Files:".print_r($valid_files,true));
        return false;
    }
}