<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/1/28
 * Time: 11:01
 */
//require_once 'Card.php';
class CardList {
    public $robotPocket;        //save cards info of robot
    public $cardObjects;        //save all cards info
    public $cardIdList;         //save all unique cardId
    public $cardPosList;        //save all unique cardPos

    public function __construct(){
        $this->init();
    }

    public function init(){
        $this->robotPocket=array();
        $this->cardObjects=array();
        $this->cardIdList=array();
        $this->cardPosList=array();
    }

    public function addCard(Card $card){
        $cardId=$card->getCardId();
        $cardUserId=$card->getCardUserId();
        //$cardPos=$card->getCardPos();
        $this->cardObjects[$cardUserId]=$card;
        $this->refreshCardIdAndPosList($cardId,$cardUserId);
    }

    public function removeCard($cardUserId){
        $card=$this->cardObjects[$cardUserId];
        if($card instanceof Card){
            $cardId=$card->getCardId();
            unset($this->cardObjects[$cardUserId]);
            foreach($this->cardIdList as $key=>$value){
                if($cardId==$value){
                    unset($this->cardIdList[$key]);
                }
            }
            unset($this->cardPosList[$cardId]);

            foreach($this->cardObjects as $kCardUserId=>$vCard){
                $this->refreshCardIdAndPosList($vCard->getCardId(),$kCardUserId);
            }


        }
    }


    private function refreshCardIdAndPosList($cardId,$cardUserId){
        if(!in_array($cardId,$this->cardIdList)){
            $this->cardIdList[]=$cardId;
            $this->cardPosList[$cardId]=$cardUserId;
        }
    }


    public function getRobotPocketCards(){
        $robotPocketCard=array();
        foreach($this->cardObjects as $card){
            if($card->getCardPos()=='robot'){
                $robotPocketCard[]=$card;
            }
        }
        return $robotPocketCard;
    }

    public function getCardNum($cardId){
        $num=0;
        foreach($this->cardObjects as $card){
            if($cardId==$card->getCardId()){
                $num+=1;
            }
        }
        return $num;
    }
    public function clearRobotPocket(){
        foreach($this->cardObjects as $cardUserId=>$card){
            if('robot'==$card->getCardPos()){
                unset($this->cardObjects[$cardUserId]);
            }
        }
        $this->robotPocket=array();
    }
} 