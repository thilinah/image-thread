<?php
ini_set('error_log', '/tmp/image-thread.log');
define('LOG_LEVEL', 'INFO');

define('APP_NAME', 'image-thread');
define('APP_PATH', dirname(__FILE__)."/");
define('APP_URL', 'http://localhost/image-thread/');
define('APP_THEME', 'insided');
define('CACHE_THEME', FALSE);

define('APP_DB', 'image-thread');
define('APP_USERNAME', 'root');
define('APP_PASSWORD', '');
define('APP_HOST', 'localhost');
define('APP_CON_STR', 'mysqli://'.APP_USERNAME.':'.APP_PASSWORD.'@'.APP_HOST.'/'.APP_DB);

//file upload
define('FILE_TYPES', 'jpg,png,jpeg,gif,png');
define('MAX_FILE_SIZE_KB', 20 * 1024);
define('MAX_FILE_WIDTH', 1920);
define('MAX_FILE_HEIGHT', 1080);