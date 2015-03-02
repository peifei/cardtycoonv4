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

    public function setUp(){
        $this->cardList=new CardList();
        $data1=array(
            'cardId'=>'15',
            'suitId'=>'3',
            'cardUserId'=>'247859',
            'cardPos'=>'12345'
        );
        $data1=array(
            'cardId'=>'15',
            'suitId'=>'3',
            'cardUserId'=>'1247859',
            'cardPos'=>'654321'
        );
        $data1=array(
            'cardId'=>'15',
            'suitId'=>'3',
            'cardUserId'=>'247859',
            'cardPos'=>'12345'
        );
        $data1=array(
            'cardId'=>'15',
            'suitId'=>'3',
            'cardUserId'=>'247859',
            'cardPos'=>'12345'
        );
    }

    public function testInit(){
        $this->cardList->init();
        $this->assertEmpty($this->cardList->cardIdList);
        $this->assertEmpty($this->cardList->robotPocket);
        $this->assertEmpty($this->cardList->cardObjects);
        $this->assertEmpty($this->cardList->cardPosList);
    }

    public function testAddCard(){
        $this->cardList->addCard($this->card1);
    }
} 