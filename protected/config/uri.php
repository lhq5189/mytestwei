<?php
//图片存放物理路径
/*define('PATH_ROOT',dirname(__FILE__).'/../..');
define('PATH_UPLOAD_PIC', PATH_ROOT.'/upload');
define('PATH_ITEM_PIC', PATH_ROOT.'/img/item');

define('MAIN', '.pai.com');
define('DOMAIN', 'http://www'.MAIN);
define('URL_WWW_UPLOAD', DOMAIN . '/upload');
define('URL_WWW_PIC', DOMAIN . '/img/item');
define('URL_WWW_DEFAULT', DOMAIN . '/images/default.jpg');*/
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');


//微信配置
define('TOKEN','weixin');
define('APPID','wxd09bd5e183402373');
define('APPSECRET','89374e3907a2a5a9486cb4928f013c83');