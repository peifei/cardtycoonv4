<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/2/26
 * Time: 17:04
 */
spl_autoload_register('auto_load');

function auto_load($name){
    if(file_exists('Class'.DIRECTORY_SEPARATOR.$name.'.php'));
    require_once 'Class'.DIRECTORY_SEPARATOR.$name.'.php';
}