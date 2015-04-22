<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/2/27
 * Time: 17:05
 */

class CardListTest extends PHPUnit_Framework_TestCase {


    private $cardList;
    private $card1;
    private $card2;
    private $card3;
    private $card4;

    public function setUp(){
        $this->cardList=new CardList();
        $this->card1=array(
            'cardId'=>'15',
            'suitId'=>'3',
            'cardUserId'=>'247859',
            'cardPos'=>'12345'
        );
        $this->card2=array(
            'cardId'=>'15',
            'suitId'=>'3',
            'cardUserId'=>'1247859',
            'cardPos'=>'654321'
        );
        $this->card3=array(
            'cardId'=>'16',
            'suitId'=>'3',
            'cardUserId'=>'25247859',
            'cardPos'=>'12345'
        );
        $this->card4=array(
            'cardId'=>'17',
            'suitId'=>'3',
            'cardUserId'=>'2476859',
            'cardPos'=>'robot'
        );
    }

    public function testInit(){
        $this->cardList->init();
        $this->assertInternalType('array',$this->cardList->cardIdList);
        $this->assertInternalType('array',$this->cardList->robotPocket);
        $this->assertInternalType('array',$this->cardList->cardObjects);
        $this->assertInternalType('array',$this->cardList->cardPosList);
        $this->assertEmpty($this->cardList->cardIdList);
        $this->assertEmpty($this->cardList->robotPocket);
        $this->assertEmpty($this->cardList->cardObjects);
        $this->assertEmpty($this->cardList->cardPosList);
    }

    public function testCanAddCard(){
        $this->cardList->addCard(new card($this->card1));
        $this->assertEquals(1,count($this->cardList->cardObjects));
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
    }

    public function testAddTwoSameCard(){
        $this->cardList->addCard(new card($this->card1));
        $this->cardList->addCard(new card($this->card2));
        $this->assertEquals(2,count($this->cardList->cardObjects));
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
    }

    public function testAddTwoDifferentCard(){
        $this->cardList->addCard(new card($this->card1));
        $this->cardList->addCard(new card($this->card3));
        $this->assertEquals(2,count($this->cardList->cardObjects));
        $this->assertEquals(2,count($this->cardList->cardIdList));
        $this->assertEquals(2,count($this->cardList->cardPosList));
    }

    public function testRemoveCard(){
        $this->cardList->addCard(new card($this->card1));
        $this->assertEquals(1,count($this->cardList->cardObjects));
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
        $this->cardList->removeCard('247859');
        $this->assertEquals(0,count($this->cardList->cardObjects));
        $this->assertEquals(0,count($this->cardList->cardIdList));
        $this->assertEquals(0,count($this->cardList->cardPosList));
    }

    public function testAddTwoSameCardAndRemoveOneCard(){
        $this->cardList->addCard(new card($this->card1));
        $this->cardList->addCard(new card($this->card2));
        $this->assertEquals(2,count($this->cardList->cardObjects));
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
        $this->cardList->removeCard('247859');
        $this->assertEquals(1,count($this->cardList->cardObjects));
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
    }

    public function testAddTwoDifferentCardAndRemoveOneCard(){
        $this->cardList->addCard(new card($this->card1));
        $this->cardList->addCard(new card($this->card3));
        $this->assertEquals(2,count($this->cardList->cardObjects));
        $this->assertEquals(2,count($this->cardList->cardIdList));
        $this->assertEquals(2,count($this->cardList->cardPosList));
        $this->cardList->removeCard('247859');
        $this->assertEquals(1,count($this->cardList->cardObjects));
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
    }

    public function testRefreshCardIdAndPosList(){
        $this->assertEquals(0,count($this->cardList->cardIdList));
        $this->cardList->addCard(new Card($this->card1));
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
        $this->assertEquals(15,$this->cardList->cardIdList[0]);
        $this->assertEquals($this->cardList->cardPosList['15'],'247859');
        $this->cardList->addCard(new Card($this->card2));
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
        $this->assertEquals(15,$this->cardList->cardIdList[0]);
        $this->assertEquals($this->cardList->cardPosList['15'],'247859');
        $this->cardList->removeCard('247859');
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
        $this->assertEquals(15,$this->cardList->cardIdList[1]);
        $this->assertEquals($this->cardList->cardPosList['15'],'1247859');
        $this->cardList->addCard(new Card($this->card3));
        $this->assertEquals(2,count($this->cardList->cardIdList));
        $this->assertEquals(2,count($this->cardList->cardPosList));
        $this->assertEquals(15,$this->cardList->cardIdList[1]);
        $this->assertEquals($this->cardList->cardPosList['15'],'1247859');
        $this->assertEquals(16,$this->cardList->cardIdList[2]);
        $this->assertEquals($this->cardList->cardPosList['16'],'25247859');
        $this->cardList->removeCard('1247859');
        $this->assertEquals(1,count($this->cardList->cardIdList));
        $this->assertEquals(1,count($this->cardList->cardPosList));
        $this->assertEquals(16,$this->cardList->cardIdList[2]);
        $this->assertEquals($this->cardList->cardPosList['16'],'25247859');

    }

    public function testGetRobotPocketCard(){
        $this->cardList->addCard(new Card($this->card1));
        $this->assertEquals(0,count($this->cardList->getRobotPocketCards()));
        $this->cardList->addCard(new Card($this->card4));
        $this->assertEquals(1,count($this->cardList->getRobotPocketCards()));
        $robtPocket=$this->cardList->getRobotPocketCards();
        $this->assertInstanceOf('Card',$robtPocket[0]);
        $this->assertEquals('2476859',$robtPocket[0]->getCardUserId());
    }

    public function testGetCardNum(){
        $this->cardList->addCard(new Card($this->card1));
        $this->cardList->addCard(new Card($this->card2));
        $this->cardList->addCard(new Card($this->card3));
        $this->cardList->addCard(new Card($this->card4));
        $this->assertEquals(2,$this->cardList->getCardNum('15'));
        $this->assertEquals(1,$this->cardList->getCardNum('16'));
        $this->assertEquals(1,$this->cardList->getCardNum('17'));
    }

    public function testClearPocket(){
        $this->cardList->addCard(new Card($this->card1));
        $this->cardList->addCard(new Card($this->card2));
        $this->cardList->addCard(new Card($this->card3));
        $this->cardList->addCard(new Card($this->card4));
        $this->assertEquals(1,count($this->cardList->getRobotPocketCards()));
        $this->cardList->clearRobotPocket();
        $this->assertEquals(0,count($this->cardList->getRobotPocketCards()));
    }

    public function tearDown(){
        $this->cardList=null;
        $this->card1=null;
        $this->card2=null;
        $this->card3=null;
    }
} 