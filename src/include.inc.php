<?php
ini_set('display_errors', false);

include(dirname(__FILE__)."/config.inc.php");
include(dirname(__FILE__)."/composer/vendor/autoload.php");

//Include all classes in api
foreach (glob(dirname(__FILE__).'/api/*.php') as $filename)
{
    include $filename;
}

//Include all Controllers
foreach (glob(dirname(__FILE__).'/controller/*.php') as $filename)
{
    include $filename;
}
if(!defined('TAGS_TO_PRESERVE')){define('TAGS_TO_PRESERVE','');}
$_REQUEST = InputCleaner::cleanParameters($_REQUEST);
$_GET = InputCleaner::cleanParameters($_GET);
$_POST = InputCleaner::cleanParameters($_POST);

include(dirname(__FILE__).'/composer/vendor/adodb/adodb-php/adodb.inc.php');
include(dirname(__FILE__).'/composer/vendor/adodb/adodb-php/adodb-active-record.inc.php');
include (dirname(__FILE__)."/model/models.inc.php");

$ADODB_ASSOC_CASE = 2;
$dbLocal = NewADOConnection(APP_CON_STR);
Post::SetDatabaseAdapter($dbLocal);