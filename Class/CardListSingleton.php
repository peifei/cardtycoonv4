<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/2/10
 * Time: 15:55
 */

//require_once 'CardList.php';
class CardListSingleton {
    private static $cardList;
    private function  __construct(){}

    public static function getInstance(){
        if(!self::$cardList instanceof CardList){
            self::$cardList=new CardList();
        }
        return self::$cardList;
    }
} 