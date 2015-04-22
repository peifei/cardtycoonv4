<?php
/*require_once 'autoload.php';

$robot=new Robot();
$robot->login();
$robot->updateXlPic();*/
/*$handle=fopen('xl.txt','r');
$str=fread($handle,102400);
preg_match_all('/<p class=\\\"mt9 \\\">(.*?)<\/p>/',$str,$match);

$picarr=array();

foreach($match[1] as $str2){
    preg_match_all('/<img src=\\\"(.*?)\\\" alt=\\\"(.*?)\\\" width=\\\"78\\\" \/>/',$str2,$match2);
    foreach($match2[1] as $pic){
        $picarr[]=$pic;

    }
}

echo '<pre>';
var_dump($picarr);
echo '</pre>';*/

/*$crawler=new Crawler();
$crawler->login();
$crawler->crawlXlPage();

/**
 * get config info
 * @param $confName             match the config file name
 * @return mixed                config array
 */
function get_conf_info($confName){
    static $friend_list;
    static $login_info;
    static $xl_list;
    if(empty($friend_list)){
        $friend_list=require_once 'Config'.DIRECTORY_SEPARATOR.'FriendList.php';
    }
    if(empty($login_info)){
        $login_info=require_once 'Config'.DIRECTORY_SEPARATOR.'LoginInfo.php';
    }
    if(empty($xl_list)){
        $xl_list=require_once 'Config'.DIRECTORY_SEPARATOR.'XlList.php';
    }
    $confName=strtolower($confName);
    switch($confName){
        case 'friendlist':
            return $friend_list;
        case 'logininfo':
            return $login_info;
        case 'xllist':
            return $xl_list;
    }
}

$str='var result_20153917544512149 = { "value":{"result":"{\"Success\":true,\"Message\":\"\\u003Cdiv class=\\\"card_get\\\">\\r\\n\\u003Cdiv class=\\\"p_r\\\">\\r\\n\\u003Cp class=\\\"money_bg\\\">&nbsp;\\u003C/p>\\r\\n\\u003Cimg src=\\\"http://img31.mtime.cn/game/card/2009/11/M12428A01.png\\\" class=\\\"getcard\\\" title=\\\"\\u70B9\\u51FB\\u5373\\u53EF\\u62FE\\u53D6\\\" alt=\\\"\\u70B9\\u51FB\\u5373\\u53EF\\u62FE\\u53D6\\\">\\r\\n\\u003Ca class=\\\"bgclose\\\" title=\\\"\\u653E\\u5F03\\u62FE\\u53D6\\\" href=\\\"#1\\\">\\u003C/a>\\r\\n\\u003Cp class=\\\"card_none\\\">\\u003Cinput type=\\\"checkbox\\\">&nbsp;\\u4E0D\\u518D\\u5F39\\u51FA\\u5361\\u7247\\u003C/p>\\r\\n\\u003C/div>\\r\\n\\u003C/div>\",\"Value\":{\"ID\":\"c923fafa14f247f5a6b86e086b50db99\",\"InputChars\":null,\"ImageUrl\":null}}"},"error":null};var callbackAppResult=result_20153917544512149;';
//$str=file_get_contents('a.txt');
var_dump($str);
//$str=strval($str);
preg_match('/src=\\\\\\\\\\\"(.*?)\\\\\\\\\\\"/',$str,$match);
//preg_match('/\\\\"ID\\\\":\\\\"(.*?)\\\\"/',$str,$match);
var_dump($match);