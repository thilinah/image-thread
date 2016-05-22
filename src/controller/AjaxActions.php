<?php

class EchoAction extends AjaxController{

    public function handleRequest($request){
        return new Response(Response::SUCCESS,'Echo');
    }

    public function getType(){
        return 'get';
    }

    public function getAction(){
        return 'echo';
    }

}

AjaxControllerHandler::getInstance()->addAction('echo','EchoAction');

class UploadAction extends AjaxController{

    public function handleRequest($request){

        $file = array_shift(array_values($_FILES));
        if(empty($file)){
            return new Response(Response::ERROR,'No file uploaded');
        }

        $allowedFileTypes = explode(",",strtolower(FILE_TYPES));
        $nameArray =  explode(".",strtolower($file['name']));
        if(count($nameArray) < 2 || !in_array($nameArray[count($nameArray)-1], $allowedFileTypes)){
            unlink($file['tmp_name']);
            return new Response(Response::ERROR,'This file type is not supported');
        }
        
        //check file size
        if(filesize($file['tmp_name']) > MAX_FILE_SIZE_KB * 1024){
            unlink($file['tmp_name']);
            return new Response(Response::ERROR,'This file is  too large');
        }

        $data = getimagesize($file['tmp_name']);

        //check file resolution
        if($data[0] > MAX_FILE_WIDTH){
            unlink($file['tmp_name']);
            return new Response(Response::ERROR,"Image is too large ($data[0] x $data[1]). Image size should be less than ".MAX_FILE_WIDTH." x ".MAX_FILE_HEIGHT."");
        }
        if($data[1] > MAX_FILE_HEIGHT){
            unlink($file['tmp_name']);
            return new Response(Response::ERROR,"Image is too large ($data[0] x $data[1]). Image size should be less than ".MAX_FILE_WIDTH." x ".MAX_FILE_HEIGHT."");
        }
        
        $newFileName = $this->getFileName($nameArray[count($nameArray)-1]);
        $newFileNameAbs = APP_PATH."data/".$newFileName;
        copy($file['tmp_name'], $newFileNameAbs);

        return new Response(Response::SUCCESS,$newFileName);
    }

    public function getFileName($ext){
        $name = AppManager::getInstance()->generateRandomString(10).microtime().".".$ext;
        if(file_exists($name)){
            return $this->getFileName($ext);
        }
        return $name;
    }



    public function getType(){
        return 'post';
    }

    public function getAction(){
        return 'upload';
    }

}

AjaxControllerHandler::getInstance()->addAction('upload','UploadAction');


class PostImageAction extends AjaxController{

    public function handleRequest($request){
        $title = $request['title'];
        $file = $request['file'];

        if(empty($file)){
            return new Response(Response::ERROR,'Please upload a file first');
        }

        $post = new Post();
        $post->title = $title;
        $post->image = $file;
        $post->created = date("Y-m-d H:i:s");
        $post->updated = date("Y-m-d H:i:s");
        $post->source = $_SERVER['REMOTE_ADDR'];

        $ok = $post->Save();
        if(!$ok){
            LogManager::getInstance()->error("Image posting error:".$post->ErrorMsg());
            return new Response(Response::ERROR,'Error occurred while posting image');
        }

        return new Response(Response::SUCCESS,$post);

    }

    public function getType(){
        return 'post';
    }

    public function getAction(){
        return 'post_image';
    }

}

AjaxControllerHandler::getInstance()->addAction('post_image','PostImageAction');


class GetPostsAction extends AjaxController{

    public function handleRequest($request){
        $post = new Post();
        $posts = $post->Find("id > ?",array($request['lastId']));
        return new Response(Response::SUCCESS,$posts);

    }

    public function getType(){
        return 'post';
    }

    public function getAction(){
        return 'get_posts';
    }

}

AjaxControllerHandler::getInstance()->addAction('get_posts','GetPostsAction');

class DownloadPostsAction extends AjaxController{

    public function handleRequest($request){
        $data = array();
        $post = new Post();
        $posts = $post->Find("1=1");

        $data[] = array('Title', 'File name');
        $files = array();
        foreach($posts as $post){
            $data[] = array($post->title, $post->image);
            $files[APP_PATH."data/".$post->image] = 'images/'.$post->image;
        }

        $csvBaseName = AppManager::getInstance()->generateRandomString(12).microtime().".csv";
        $csvName = "/tmp/".$csvBaseName;
        $output = fopen($csvName, "w");
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);

        $zipName = "/tmp/".AppManager::getInstance()->generateRandomString(12).microtime().".zip";
        $files[$csvName] = $csvBaseName;
        $result = AppManager::getInstance()->createZip($files,$zipName);
        if($result){
            header("Content-Type: application/zip");
            header("Content-Disposition: application/octet-stream; filename=export.zip");

            header("Cache-Control: no-cache, no-store, must-revalidate");
            header("Pragma: no-cache");
            header("Expires: 0");
            header('Content-Length: ' . filesize($zipName));
            readfile($zipName);

            exit();
        }else{
            echo "Error occurred while downloading report";
            exit();
        }


    }

    public function getType(){
        return 'get';
    }

    public function getAction(){
        return 'download_posts';
    }

}

AjaxControllerHandler::getInstance()->addAction('download_posts','DownloadPostsAction');

