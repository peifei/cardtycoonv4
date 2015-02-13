<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/2/10
 * Time: 14:52
 */
require_once 'CardListSingleton.php';
require_once 'Friend.php';
class Crawler {
    private $ch;
    public $forBack;
    public $sellOccupiedNum;
    public function __construct(){

        $cookieJar=tempnam('cookie','cookie');
        $this->ch=curl_init();
        curl_setopt($this->ch,CURLOPT_COOKIEJAR,$cookieJar);
        curl_setopt($this->ch,CURLOPT_COOKIEFILE,$cookieJar);
        curl_setopt($this->ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:33.0) Gecko/20100101 Firefox/33.0');
        curl_setopt($this->ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($this->ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($this->ch,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($this->ch,CURLOPT_AUTOREFERER, true);
    }

    public function login(){
        $login_info=get_conf_info('logininfo');
        $url='http://passport.mtime.com/member/signin/';
        curl_setopt($this->ch,CURLOPT_URL,$url);
        $res=curl_exec($this->ch);              //get login page
        $postAction=$this->parseLoginAction($res);
        $postStr='email='.$login_info['user'].'&password='.$login_info['pwd'];
        curl_setopt($this->ch,CURLOPT_POSTFIELDS,$postStr);
        curl_setopt($this->ch,CURLOPT_POST,true);
        curl_setopt($this->ch,CURLOPT_URL,$postAction);
        $res2=curl_exec($this->ch);             //post login info
        curl_setopt($this->ch,CURLOPT_REFERER,'http://passport.mtime.com/member/signin/');
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array("Host:my.mtime.com"));
        curl_setopt($this->ch,CURLOPT_URL,'http://my.mtime.com/');
        $res3=curl_exec($this->ch);
        if(stristr($res3,$login_info['testStr'])){
            return true;
        }else{
            return false;
        }
    }

    /**
     * crawl and refresh robot pocket cards
     */
    public function crawlRobotPocket(){
        $t=$this->generatorT();
        $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteLoad&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2F&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=';
        curl_setopt($this->ch,CURLOPT_URL,$url);
        curl_setopt($this->ch,CURLOPT_REFERER,'http://my.mtime.com/app/card/');
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array("Host:sandbox.my.mtime.com"));
        $res=curl_exec($this->ch);
        //file_put_contents('c:\\ls.txt',$res);
        $this->parsePocketAction($res);
        echo "refresh robot pocket success \r\n";
    }

    /**
     * crawl and refresh friend pocket cards
     * @param Friend $friend
     */
    public function crawlFriendPocket(Friend $friend){
        $friend_id=$friend->getFriendId();
        if($friend->isRefreshFlag()){
            $t=$this->generatorT();
            $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteLoad&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2Ffriend%2F'.$friend_id.'%2Findex-1.html%23f&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=friend%2F'.$friend_id.'%2Findex-1.html';
            curl_setopt($this->ch,CURLOPT_URL,$url);
            curl_setopt($this->ch,CURLOPT_REFERER,'http://my.mtime.com/app/card/friend/'.$friend_id.'/index-1.html');
            curl_setopt($this->ch,CURLOPT_HTTPHEADER,array("Host:sandbox.my.mtime.com"));
            $response_text=curl_exec($this->ch);
            //file_put_contents('c:\\fpocket.txt',$response_text);
            $this->parseFriendPocketAction($response_text,$friend);
            echo "refresh friend: ".$friend_id." pocket success,there is ".$friend->getCardsNum()." cards in pocket\r\n";
            $friend->setRefreshFlag(false);
            sleep(rand(1,2));
        }else{
            echo "skip refresh friend:".$friend_id."\r\n";
        }
    }

    public function addCardToFriend(Card $card,Friend $friend){
        if($friend->getCardsNum()==5){
            return false;
        }
        $friend_id=$friend->getFriendId();
        $card_user_id=$card->getCardUserId();
        $t=$this->generatorT();
        $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2Ffriend%2F'.$friend_id.'%2Findex-1.html%23f&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Ffriend%2Ffriendpocket.aspx%3Ffriendid%3D'.$friend_id.'%26friendpageindex%3D1%26ajax%3D1%26m%3DSaveCardAction&Ajax_CallBackArgument2=actionType%3D5%26userID%3D1384093%26friendUserID%3D'.$friend_id.'%26robertID%3D-1%26cardUserCardIDs%3D'.$card_user_id.'%26cardID%3D-1';

        curl_setopt($this->ch,CURLOPT_URL,$url);
        curl_exec($this->ch);
        //TODO test if add success
        if($friend_id!=$card->getCardPos()){
            $card->setCardPos($friend_id);
        }
        $this->pocket[$card->getCardUserId()]=$card;
        if(false==$friend->isRefreshFlag()){
            $friend->setRefreshFlag(true);
        }
    }

    public function pickCardFromFriend($cardUserId, Friend $friend){
        if(null!=$friend->getCard($cardUserId)){
            $friend_id=$friend->getFriendId();
            $t=$this->generatorT();
            $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2Ffriend%2F'.$friend_id.'%2Findex-1.html%23f&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Ffriend%2Ffriendpocket.aspx%3Ffriendid%3D'.$friend_id.'%26friendpageindex%3D1%26ajax%3D1%26m%3DSaveCardAction&Ajax_CallBackArgument2=actionType%3D3%26userID%3D1384093%26friendUserID%3D'.$friend_id.'%26robertID%3D-1%26cardUserCardIDs%3D'.$cardUserId.'%26cardID%3D-1';
            curl_setopt($this->ch,CURLOPT_URL,$url);
            curl_exec($this->ch);
            //TODO test if pick success
            $friend->removeCard($cardUserId);
            if(false==$friend->isRefreshFlag()){
                $friend->setRefreshFlag(true);
            }
            return true;
        }else{
            return false;
        }
    }

    public function pickBackSellCard(){
        $num=count($this->forBack);
        if($num>0){
            for($i=0;$i<$num;$i++){
                $t=$this->generatorT();
                $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2Fauction%2F&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Fauction%2Fauction.aspx%3Fpi%3D1%26ajax%3D1%26m%3DAuctionOperate&Ajax_CallBackArgument2=cardAuctionID%3D'.array_shift($this->forBack).'%26operateType%3D0';
                curl_setopt($this->ch,CURLOPT_URL,$url);
                curl_exec($this->ch);
                if(true){//TODO test
                    sleep(1);
                    $this->sellOccupiedNum-=1;
                }
            }
        }
    }

    public function sellCard(){
        $cardList=CardListSingleton::getInstance();
        $robotPocketCards=$cardList->getRobotPocketCards();

        foreach($robotPocketCards as $card){
            $t=$this->generatorT();
            $cardId=$card->getCardId();
            if($cardList->getCardNum($cardId)>1){
                $cardUserId=$card->getCardUserId();
                $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2Fauction%2F&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Fauction%2Fauction.aspx%3Fpi%3D1%26ajax%3D1%26m%3DAddAuction&Ajax_CallBackArgument2=cardID%3D'.$cardId.'%26cardToolID%3D-1%26timeLimited%3D8%26startPrice%3D90%26fixedPrice%3D199';
                curl_setopt($this->ch,CURLOPT_URL,$url);
                curl_exec($this->ch);
                //TODO success test
                CardListSingleton::getInstance()->removeCard($cardUserId);
                echo "sell card :$cardId\r\n";
            }
        }
    }

    public function crawlSellPage(){
        $t=$this->generatorT();
        $this->forBack=array();
        $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteLoad&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2Fauction%2F&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=auction%2F';
        curl_setopt($this->ch,CURLOPT_URL,$url);
        curl_setopt($this->ch,CURLOPT_REFERER,'http://my.mtime.com/app/card/auction/');
        curl_setopt($this->ch,CURLOPT_HTTPHEADER,array("Host:sandbox.my.mtime.com"));
        $res=curl_exec($this->ch);
        //file_put_contents('c:\\ls2.txt',$res);
        $this->parseSellAction($res);
        echo "get selling page \r\n";
    }

    public function refreshCard(){
        for($i=0;$i<20;$i++){
            $t=$this->generatorT();
            $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2F&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Fmy%2Fdiscard.aspx%3Fajax%3D1%26m%3DGenerateCardGold&Ajax_CallBackArgument2=';
            curl_setopt($this->ch,CURLOPT_URL,$url);
            $res=curl_exec($this->ch);
            $id=$this->parseRandomId($res);
/*            $res=$res."\r\n\r\n";
            var_dump($res);*/
            //file_put_contents('c:\\godcard.txt',$res,FILE_APPEND);
            if(!empty($id)){
                echo "do click card....\r\n";
                $code=$this->parseRandomCodeImg($res);
                $t2=$this->generatorT();
                $url2='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2F&t='.$t2.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Fmy%2Fdiscard.aspx%3Fcardusercardid%3D0%26ajax%3D1%26m%3DSaveOneCard&Ajax_CallBackArgument2=randomID%3D'.$id.'%26verifyCode%3D';
                if(!empty($code)){
                    $url2=$url2.$code;
/*                    echo "\r\n";
                    file_put_contents('c:\\url2.txt',$url2);
                    echo $url2;
                    echo "\r\n";*/
                }
                curl_setopt($this->ch,CURLOPT_URL,$url2);
                $res2=curl_exec($this->ch);
                //file_put_contents('c:\\getcards.txt',$res2,FILE_APPEND);
                var_dump($res2);
                echo "click card end....\r\n";
            }
            sleep(rand(1,2));
        }
    }

    public function mixsuit($userCardIdArr,$suitId){
        $t=$this->generatorT();
        $cardUserIdStr=implode('%252C',$userCardIdArr);
        $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2F&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Fmy%2Fmypocket.aspx%3Fismore%3D0%26ajax%3D1%26m%3DCardMixSuit&Ajax_CallBackArgument2=userID%3D1384093%26suitID%3D'.$suitId.'%26cardUserCardIDs%3D'.$cardUserIdStr;
        curl_setopt($this->ch,CURLOPT_URL,$url);
        curl_exec($this->ch);
    }

    public function hijack($dogId,$cardId){
        $t=$this->generatorT();
        $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2Ftools%2Fuse%2F5%2F%3Ftarget%3D'.$dogId.'&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Ftools%2Fusetoolcard.aspx%3Fcardtoolid%3D5%26target%3D'.$dogId.'%26ajax%3D1%26m%3DUse&Ajax_CallBackArgument2=receiver%3D'.$dogId.'%26relatedCardID%3D'.$cardId.'%26msg%3D%26withDemonCard%3Dfalse';

    }

    public function panda(){

    }

    private function parseRandomId($str){
        preg_match('/\\\"ID\\\":\\\"(.*?)\\\"/',$str,$match);
        if(isset($match[1])){
            return $match[1];
        }
    }

    private function parseRandomCodeImg($str){
        preg_match('/\\\"ImageUrl\\\":\\\"(.*?)\\\"/',$str,$match);
        if(isset($match[1])){
            $url=$match[1];
            $img=fopen($url,'rb');

            $limg=fopen('c:\\lcode.gif','wb');
            if($img){
                while(!feof($img)){
                    fwrite($limg,fread($img,1024*8),1024*8);
                }
            }else{
                echo 'picurl is :'.$url."\r\n";
                return null;
            }
            system('winver');
            echo "see pic and please input code!\r\n";

            $handle = fopen ("php://stdin","r");
            $line = fgets($handle);
            echo $line;
            return trim($line);
        }


    }

    private function parseSellAction($str){
        //get back card
        preg_match_all('/<input method=\\\"backCard\\\" auctionID=\\\"(.*?)\\\" type=\\\"button\\\" class=\\\"btn_blue\\\" value=\\\"取回卡片\\\" \/>/',$str,$match);
        for($i=0;$i<count($match[1]);$i++){
            $this->forBack[]=$match[1][$i];
        }
        //get selling card
        preg_match_all('/<input method=\\\"cancel\\\" auctionID=\\\"(.*?)\\\" storagefee=\\\"10\\\\" type=\\\\"button\\\\" class=\\\"btn_gray\\\" onmouseover=\\\"this.className=\'btn_blue\'\\\" onmouseout=\\\"this.className=\'btn_gray\'\\\" value=\\\"取消拍卖\\\" \/>/',$str,$match2);
        //get pai card
        preg_match_all('/<input type=\\\"button\\\" class=\\\"btn_gray false\\\" disabled=\\\"disabled\\\" value=\\\"取消拍卖\\\" \/>/',$str,$match3);
        $this->sellOccupiedNum=count($match[1])+count($match2[1])+count($match3[0]);
    }

    private function parseFriendPocketAction($responseText,Friend $friend){
        preg_match_all('/<b name=\\\"selectedCard\\\" method=\\\"cardImg\\\" suitID=\\\"(.*?)\\\" cardUserCardID=\\\"(.*?)\\\" needCardCount=\\\".*?\\\" pocketPosition=\\\"1\\\" cardID=\\\"(.*?)\\\" style=\\\"display: none;\\\"><\/b>/',$responseText,$match);
        $friend->clearPocket();
        $pocket=&$friend->getPocket();
        for($i=0;$i<5;$i++){
            if(!empty($match[1][$i])){
                $data=array();
                $data['suitId']=$match[1][$i];
                $data['cardUserId']=$match[2][$i];
                $data['cardId']=$match[3][$i];
                $data['cardPos']=$friend->getFriendId();
                $friend->addCard($data['cardUserId'],new Card($data));
            }
        }
    }

    private function parsePocketAction($str){
        $cardList=&CardListSingleton::getInstance();//use cardList as a parameter
        //claer robot pocket cards records first
        $cardList->clearRobotPocket();
        preg_match_all('/<b name=\\\"selectedCard\\\" method=\\\"cardImg\\\" suitID=\\\"(.*?)\\\" cardUserCardID=\\\"(.*?)\\\" needCardCount=\\\".*?\\\" pocketPosition=\\\"0\\\" cardID=\\\"(.*?)\\\" style=\\\"display: none;\\\"><\/b>/',$str,$match);
        $xlList= get_conf_info('xllist');
        for($i=0;$i<10;$i++){
            if(!empty($match[1][$i])){
                //pick xl here for quick pick up card.
                if(in_array($match[1][$i],$xlList)){
                    $this->pickXl($match[2][$i]);
                }else{
                    $data=array();
                    $data['suitId']=$match[1][$i];
                    $data['cardUserId']=$match[2][$i];
                    $data['cardId']=$match[3][$i];
                    $card=new Card($data);
                    $cardList->addCard($card);
                }
            }
        }
    }

    private function pickXl($cardUserId){
        $t=$this->generatorT();
        $url='http://sandbox.my.mtime.com/Service/callback.mc?Ajax_CallBack=true&Ajax_CallBackType=Mtime.MemberCenter.Pages.CallbackService&Ajax_CallBackMethod=RemoteCallback&Ajax_CrossDomain=1&Ajax_RequestUrl=http%3A%2F%2Fmy.mtime.com%2Fapp%2Fcard%2F&t='.$t.'&Ajax_CallBackArgument0=card&Ajax_CallBackArgument1=%2Fmy%2Fmypocket.aspx%3Fismore%3D0%26ajax%3D1%26m%3DMoveCardToBag&Ajax_CallBackArgument2=cardUserCardIDList%3D'.$cardUserId;
        curl_setopt($this->ch,CURLOPT_URL,$url);
        curl_exec($this->ch);
        //TODO pick card test
        echo "pick xl success\r\n";
    }

    private function parseLoginAction($str){
        preg_match('/<form.*action="(.*?)".*>/',$str,$match);
        return $match[1];
    }

    private function generatorT(){
        list($mtime,$time)=explode(' ',microtime());
        $str1=date('YnjG',$time);
        $str2=intval(date('i',$time));
        $str3=intval(date('s',$time));
        return $str1.$str2.$str3.intval($mtime*1000000);
    }
} 