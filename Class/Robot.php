<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/1/26
 * Time: 20:28
 */
//require_once 'CardListSingleton.php';
//require_once 'Crawler.php';
class Robot {
    private $crawler;               //the crawler do all curl action
    private $friendsObj;            //the friend object array

    public function __construct(){
        $this->crawler=new Crawler();
    }

    public function run(){
        try {
            $this->login();
            $this->initFriends();
            $round=1;
            while(true){
                echo "***********round:$round*************\r\n";
                $this->refreshAllCards();
                $this->crawler->crawlSellPage();
                if (count($this->crawler->forBack)>0) {
                    $this->crawler->pickBackSellCard();
                }
                $this->refreshPocket();
                echo "try sell card....\r\n";
                $this->crawler->sellCard();


                $robotPocketCards=CardListSingleton::getInstance()->getRobotPocketCards();

                if(count($robotPocketCards)<7){
                    echo "group cards\r\n";
                    $this->groupCards($robotPocketCards);
                }else{
                    echo "add cards to friend begin\r\n";
                    $this->addCardsToFriend($robotPocketCards);
                }

                $this->refreshAllCards();

                $robotPocketCards=CardListSingleton::getInstance()->getRobotPocketCards();
                echo "current pocket cards num is:".count($robotPocketCards)."\r\n";
                if(count($robotPocketCards)<7) {
                    $this->crawler->refreshCard();
                }else{
                    echo "sleeping........\r\n";
                    sleep(rand(200,400));
                }
                $round++;
            }
        }catch (Exception $e){
            echo $e."\r\n";
        }
    }

    /**
     *init friend to friend object
     */
    public function initFriends(){
        $friend_list=get_conf_info('friendlist');
        if(count($friend_list)>0){
            foreach($friend_list as $friend_id){
                $this->friendsObj[$friend_id]=new Friend($friend_id);
            }
        }
        var_dump($this->friendsObj);
        sleep(20);
    }
    /**
     * login action
     * @return bool
     */
    public function login(){
        $login=$this->crawler->login();
        if($login){
            echo "login success.\r\n";
        }else{
            throw new Exception('login failed.');
        }
    }

    public function doPanda(){
        $dogIdArr=array('12345','678910');
        foreach($dogIdArr as $dogId){
            $this->crawler->panda('2128449');
            sleep(30);
        }
    }

    public function refreshAllCards(){
        CardListSingleton::getInstance()->init();
        $this->refreshPocket();
        $this->refreshFriendsPocket();
        sort(CardListSingleton::getInstance()->cardIdList);
        $ls=array();
        $cardList=CardListSingleton::getInstance();
        foreach($cardList->cardIdList as $cardId){
            $card=$cardList->cardObjects[$cardList->cardPosList[$cardId]];
            $ls[$cardId]=$card->getCardPos();
        }
        var_dump($ls);
    }

    public function refreshPocket(){
        $this->crawler->crawlRobotPocket();
    }

    public function refreshXL(){
        $this->crawler->refreshXl();
    }

    public function refreshFriendsPocket(){
        foreach($this->friendsObj as $friend) {
            $this->crawler->crawlFriendPocket($friend);
            if ($friend->getCardsNum() > 0) {
                $friendsPocketCards = $friend->getPocketCards();
                foreach ($friendsPocketCards as $card) {
                    CardListSingleton::getInstance()->addCard($card);
                }
            }
        }
    }




    public function groupCards($robotPocketCards){

        foreach($robotPocketCards as $key=>$card){
            echo "test hk \r\n";
            //$cardId=$card->getCardId();
            $suitArr=$this->testSuit($card);
            if(is_array($suitArr)){
                echo "hk..... \r\n";
                var_dump($suitArr);
                $userCardArr=$this->pickCardForSuit($suitArr);

                $this->crawler->mixSuit($userCardArr,$card->getSuitId());
                foreach($suitArr as $card){
                    CardListSingleton::getInstance()->removeCard($card->getCardUserId());
                }

            }
        }

    }

    public function addCardsToFriend($robotPocketCards){
        foreach($robotPocketCards as $key=>$card){
            $cardId=$card->getCardId();

            $suitArr=$this->testSuit($card);
            //if this card is ready for suit, then do not add to friend pocket.
            if(is_array($suitArr)){
                echo "card $cardId is for suit\r\n";
                continue;
            }

            if(CardListSingleton::getInstance()->getCardNum($cardId)<=2){
                //$key=$this->getEmptyFriendsPos();
                $friend=$this->getEmptyFriendsPos();
                if(!empty($friend)){
                    echo "do add card to friend \r\n";
                    $this->crawler->addCardToFriend($card,$friend);
                    /*                $this->cardList[]=$cardId;
                                    $this->cardPosList[$cardId]=$friend->getFriendId();*/
                }
            }else{
                echo "card $cardId num is ".CardListSingleton::getInstance()->getCardNum($cardId)."\r\n";
            }

        }
    }

    public function updateXlPic(){
        $picArr=$this->crawler->crawlXlPage();
        var_dump($picArr);
        echo '<hr/>';
        sleep(5);
        $xlimg=parse_ini_file('xl.ini');
        //var_dump($xlimg);
        $m=count($xlimg);
        $file=fopen('xl.ini','a');

        foreach($picArr as $pic){
            if(!in_array($pic,$xlimg)){
                $m++;
                fwrite($file,'p_'.$m.'='.$pic."\r\n");
            }
        }
        fclose($file);

    }




    private function testSuit($card){
        $cardId=$card->getCardId();
        echo "cardId is $cardId\r\n";
        $cardList=CardListSingleton::getInstance();
        $m=$cardId%5;
        if($m==0){
            $c1=$cardId-4;
            $c2=$cardId-3;
            $c3=$cardId-2;
            $c4=$cardId-1;
            if(in_array($c1,$cardList->cardIdList)&&
               in_array($c2,$cardList->cardIdList)&&
               in_array($c3,$cardList->cardIdList)&&
               in_array($c4,$cardList->cardIdList)){
                $card1=$cardList->cardObjects[$cardList->cardPosList[$c1]];
                $card2=$cardList->cardObjects[$cardList->cardPosList[$c2]];
                $card3=$cardList->cardObjects[$cardList->cardPosList[$c3]];
                $card4=$cardList->cardObjects[$cardList->cardPosList[$c4]];
                return array($card1,$card2,$card3,$card4,$card);
            }else{
                return false;
            }
        }
        if($m==1){
            $c2=$cardId+1;
            $c3=$cardId+2;
            $c4=$cardId+3;
            $c5=$cardId+4;
            if(in_array($c5,$cardList->cardIdList)&&
                in_array($c2,$cardList->cardIdList)&&
                in_array($c3,$cardList->cardIdList)&&
                in_array($c4,$cardList->cardIdList)){
                $card2=$cardList->cardObjects[$cardList->cardPosList[$c2]];
                $card3=$cardList->cardObjects[$cardList->cardPosList[$c3]];
                $card4=$cardList->cardObjects[$cardList->cardPosList[$c4]];
                $card5=$cardList->cardObjects[$cardList->cardPosList[$c5]];
                return array($card,$card2,$card3,$card4,$card5);
            }else{
                return false;
            }
        }
        if($m==2){
            $c1=$cardId-1;
            $c3=$cardId+1;
            $c4=$cardId+2;
            $c5=$cardId+3;
            if(in_array($c1,$cardList->cardIdList)&&
                in_array($c5,$cardList->cardIdList)&&
                in_array($c3,$cardList->cardIdList)&&
                in_array($c4,$cardList->cardIdList)){
                $card1=$cardList->cardObjects[$cardList->cardPosList[$c1]];
                $card3=$cardList->cardObjects[$cardList->cardPosList[$c3]];
                $card4=$cardList->cardObjects[$cardList->cardPosList[$c4]];
                $card5=$cardList->cardObjects[$cardList->cardPosList[$c5]];
                return array($card1,$card,$card3,$card4,$card5);
            }else{
                return false;
            }
        }
        if($m==3){
            $c1=$cardId-2;
            $c2=$cardId-1;
            $c4=$cardId+1;
            $c5=$cardId+2;
            if(in_array($c1,$cardList->cardIdList)&&
                in_array($c5,$cardList->cardIdList)&&
                in_array($c2,$cardList->cardIdList)&&
                in_array($c4,$cardList->cardIdList)){
                $card1=$cardList->cardObjects[$cardList->cardPosList[$c1]];
                $card2=$cardList->cardObjects[$cardList->cardPosList[$c2]];
                $card4=$cardList->cardObjects[$cardList->cardPosList[$c4]];
                $card5=$cardList->cardObjects[$cardList->cardPosList[$c5]];
                return array($card1,$card2,$card,$card4,$card5);
            }else{
                return false;
            }
        }
        if($m==4){
            $c1=$cardId-3;
            $c2=$cardId-2;
            $c3=$cardId-1;
            $c5=$cardId+1;
            if(in_array($c1,$cardList->cardIdList)&&
                in_array($c5,$cardList->cardIdList)&&
                in_array($c2,$cardList->cardIdList)&&
                in_array($c3,$cardList->cardIdList)){
                $card1=$cardList->cardObjects[$cardList->cardPosList[$c1]];
                $card2=$cardList->cardObjects[$cardList->cardPosList[$c2]];
                $card3=$cardList->cardObjects[$cardList->cardPosList[$c3]];
                $card5=$cardList->cardObjects[$cardList->cardPosList[$c5]];
                return array($card1,$card2,$card3,$card,$card5);
            }else{
                return false;
            }
        }
    }

    private function pickCardForSuit($suitArr){
        $cardUserIdArr=array();
        foreach($suitArr as $card){
            $friendId=$card->getCardPos();
            $cardUserId=$card->getCardUserId();
            if('robot'!=$friendId){
                $friend=$this->friendsObj[$friendId];
                $this->crawler->pickCardFromFriend($cardUserId,$friend);
            }
            $cardUserIdArr[]=$cardUserId;
        }
        return $cardUserIdArr;
    }



    private function mixSuit($userCardIdArr,$suitId,$ch){
        $t=
        $cardUserIdStr=implode('%252C',$userCardIdArr);
        $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2F&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Fmy%2Fmypocket.aspx%3Fismore%3D0%26ajax%3D1%26m%3DCardMixSuit&Ajax_CallBackArgument2=userID%3D1384093%26suitID%3D'.$suitId.'%26cardUserCardIDs%3D'.$cardUserIdStr;
        curl_setopt($this->ch,CURLOPT_URL,$url);
        curl_exec($this->ch);
    }



    private function getEmptyFriendsPos(){
        foreach($this->friendsObj as $key=>$friend){
            if($friend->getCardsNum()<5){
                return $friend;
            }
        }
    }
    

    
    
    
    
    
    
    
    
    
    
}