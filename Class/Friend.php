<?php
//require_once 'Card.php';
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/1/26
 * Time: 18:38
 */

class Friend {
    private $pocket;
    private $friendId;
    private $refreshFlag;

    public function __construct($friendId){
        $this->friendId=$friendId;
        $this->setRefreshFlag(true);
    }



    public function clearPocket(){
        $this->pocket=array();
    }



    /**
     * get the number of cards in pocket
     * @return int
     */
    public function getCardsNum(){
        return count($this->pocket);
    }

    public function getPocketCards(){
        return $this->pocket;
    }

    public function getFriendId(){
        return $this->friendId;
    }

    /**
     * @return boolean
     */
    public function isRefreshFlag()
    {
        return $this->refreshFlag;
    }

    /**
     * @param boolean $refreshFlag
     */
    public function setRefreshFlag($refreshFlag)
    {
        $this->refreshFlag = $refreshFlag;
    }

    public function getPocket(){
        return $this->pocket;
    }

    public function addCard($cardUserId,Card $card){
        $this->pocket[$cardUserId]=$card;
    }

    public function removeCard($cardUserId){
        unset($this->pocket[$cardUserId]);
    }

    public function getCard($cardUserId){
        if(isset($this->pocket[$cardUserId])){
            return $this->pocket[$cardUserId];
        }else{
            return null;
        }
    }

}
