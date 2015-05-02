<?php
require_once 'autoload.php';

$robot=new Robot();
//$robot->run();
$robot->login();
//$robot->refreshPocket();
//$robot->updateXlPic();
//$robot->refreshXL();

$robot->doPanda();

/**
 * get config info
 * @param $confName             match the config file name
 * @return mixed                config array
 */
function get_conf_info($confName){
    static $friend_list;
    static $login_info;
    static $xl_list;
    static $xl_pic;
    if(empty($friend_list)){
        $friend_list=require_once 'Config'.DIRECTORY_SEPARATOR.'FriendList.php';
    }
    if(empty($login_info)){
        $login_info=require_once 'Config'.DIRECTORY_SEPARATOR.'LoginInfo.php';
    }
    if(empty($xl_list)){
        $xl_list=require_once 'Config'.DIRECTORY_SEPARATOR.'XlList.php';
    }

    if(empty($xl_pic)){
        $xl_pic=parse_ini_file('xl.ini');
    }

    $confName=strtolower($confName);
    switch($confName){
        case 'friendlist':
            return $friend_list;
        case 'logininfo':
            return $login_info;
        case 'xllist':
            return $xl_list;
        case 'xlpic':
            return $xl_pic;
    }
}


