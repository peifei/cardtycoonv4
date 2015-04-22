<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/1/26
 * Time: 17:04
 */

class Card {
    private $cardId;
    private $suitId;
    private $cardUserId;           //the unique id for an alive card
    private $cardPos;              //stands for where is card from,('robot' or friends id)

    public function __construct(Array $data){
        $this->setCardId($data['cardId']);
        $this->setSuitId($data['suitId']);
        $this->setCardUserId($data['cardUserId']);
        if(isset($data['cardPos'])){
            $this->setCardPos($data['cardPos']);
        }else{
            $this->setCardPos();
        }
    }

    /**
     * @return mixed
     */
    public function getCardId()
    {
        return $this->cardId;
    }

    /**
     * @param mixed $cardId
     */
    public function setCardId($cardId)
    {
        $this->cardId = $cardId;
    }

    /**
     * @return mixed
     */
    public function getCardUserId()
    {
        return $this->cardUserId;
    }

    /**
     * @param mixed $cardUserId
     */
    public function setCardUserId($cardUserId)
    {
        $this->cardUserId = $cardUserId;
    }

    /**
     * @return mixed
     */
    public function getSuitId()
    {
        return $this->suitId;
    }

    /**
     * @param mixed $suitId
     */
    public function setSuitId($suitId)
    {
        $this->suitId = $suitId;
    }

    /**
     * @return mixed
     */
    public function getCardPos()
    {
        return $this->cardPos;
    }

    /**
     * @param mixed $cardPos
     * $cardPos='robot' means this card is in robot pocket.
     */
    public function setCardPos($cardPos='robot')
    {
        $this->cardPos = $cardPos;
    }



} 