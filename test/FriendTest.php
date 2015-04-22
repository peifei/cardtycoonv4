<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/3/6
 * Time: 14:32
 */

class FriendTest extends PHPUnit_Framework_TestCase {
    public function testCanBeConstructed(){
        $friend=new Friend('12345678');
        $this->assertInstanceOf('Friend',$friend);
        $this->assertEquals('12345678',$friend->getFriendId());
        $this->assertTrue($friend->isRefreshFlag());
    }

    public function testSetRefreshFlag(){
        $friend=new Friend('12345678');
        $this->assertInstanceOf('Friend',$friend);
        $this->assertTrue($friend->isRefreshFlag());
        $friend->setRefreshFlag(false);
        $this->assertFalse($friend->isRefreshFlag());
    }

    public function testAddCard(){
        $card1=array(
            'cardId'=>'15',
            'suitId'=>'3',
            'cardUserId'=>'247859',
            'cardPos'=>'12345'
        );
        $card2=array(
            'cardId'=>'15',
            'suitId'=>'3',
            'cardUserId'=>'1247859',
            'cardPos'=>'654321'
        );
        $card3=array(
            'cardId'=>'16',
            'suitId'=>'3',
            'cardUserId'=>'25247859',
            'cardPos'=>'12345'
        );
        $card4=array(
            'cardId'=>'17',
            'suitId'=>'3',
            'cardUserId'=>'2476859',
            'cardPos'=>'robot'
        );
        $friend=new Friend('12345678');
        $friend->addCard('247859',new Card($card1));
        $friend->addCard('1247859',new Card($card2));
        $friend->addCard('25247859',new Card($card3));
        $friend->addCard('2476859',new Card($card4));
        $this->assertEquals(4,$friend->getCardsNum());
        return $friend;
    }

    /**
     * @depends testAddCard
     */
    public function testGetCard(Friend $friend){
        $this->assertInstanceOf('Card',$friend->getCard('247859'));
        $this->assertEquals('15',$friend->getCard('247859')->getCardId());
    }

    /**
     * @depends testAddCard
     */
    public function testRemoveCard(Friend $friend){
        $friend->removeCard('247859');
        $this->assertEquals(3,$friend->getCardsNum());
    }
} 