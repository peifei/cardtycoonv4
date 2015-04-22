<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/3/3
 * Time: 17:14
 */
//require_once '..\Class\Card.php';
class CardTest extends PHPUnit_Framework_TestCase {
    public function testCanBeConstruct(){
        $card=new Card(array(
            'cardId'=>'1',
            'suitId'=>'2',
            'cardUserId'=>'12346798',
            'cardPos'=>'123456'
        ));
        $this->assertInstanceOf('Card',$card);
        return $card;
    }

    /**
     * @depends testCanBeConstruct
     */
    public function testCanGetParam(Card $card){
        $this->assertEquals('1',$card->getCardId());
        $this->assertEquals('2',$card->getSuitId());
        $this->assertEquals('12346798',$card->getCardUserId());
        $this->assertEquals('123456',$card->getCardPos());
    }
} 