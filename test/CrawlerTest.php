<?php
/**
 * Created by PhpStorm.
 * User: fxcm
 * Date: 2015/3/6
 * Time: 14:20
 */

class CrawlerTest extends PHPUnit_Framework_TestCase {
    public function testGeneratorT(){
        $time=microtime();
        list($microtime,$timestamp)=explode(' ',$time);

    }

    public function testPregMatchPic(){
        //$str='var result_20153917544512149 = { "value":{"result":"{\"Success\":true,\"Message\":\"\\u003Cdiv class=\\\"card_get\\\">\\r\\n\\u003Cdiv class=\\\"p_r\\\">\\r\\n\\u003Cp class=\\\"money_bg\\\">&nbsp;\\u003C/p>\\r\\n\\u003Cimg src=\\\"http://img31.mtime.cn/game/card/2009/11/M12428A01.png\\\" class=\\\"getcard\\\" title=\\\"\\u70B9\\u51FB\\u5373\\u53EF\\u62FE\\u53D6\\\" alt=\\\"\\u70B9\\u51FB\\u5373\\u53EF\\u62FE\\u53D6\\\">\\r\\n\\u003Ca class=\\\"bgclose\\\" title=\\\"\\u653E\\u5F03\\u62FE\\u53D6\\\" href=\\\"#1\\\">\\u003C/a>\\r\\n\\u003Cp class=\\\"card_none\\\">\\u003Cinput type=\\\"checkbox\\\">&nbsp;\\u4E0D\\u518D\\u5F39\\u51FA\\u5361\\u7247\\u003C/p>\\r\\n\\u003C/div>\\r\\n\\u003C/div>\",\"Value\":{\"ID\":\"c923fafa14f247f5a6b86e086b50db99\",\"InputChars\":null,\"ImageUrl\":null}}"},"error":null};var callbackAppResult=result_20153917544512149;';
        //$str='var result_201531110441447484 = { "value":{"result":"{\"Success\":true,\"Message\":\"\\u003Cdiv class=\\\"card_get\\\">\\r\\n\\u003Cdiv class=\\\"p_r\\\">\\r\\n\\u003Cp class=\\\"money_bg\\\">&nbsp;\\u003C/p>\\r\\n\\u003Cimg src=\\\"http://img31.mtime.cn/game/card/2009/13/1037bf69-8068-4091-bcdf-c2735be9bdd7.png\\\" class=\\\"getcard\\\" title=\\\"\\u70B9\\u51FB\\u5373\\u53EF\\u62FE\\u53D6\\\" alt=\\\"\\u70B9\\u51FB\\u5373\\u53EF\\u62FE\\u53D6\\\">\\r\\n\\u003Ca class=\\\"bgclose\\\" title=\\\"\\u653E\\u5F03\\u62FE\\u53D6\\\" href=\\\"#1\\\">\\u003C/a>\\r\\n\\u003Cp class=\\\"card_none\\\">\\u003Cinput type=\\\"checkbox\\\">&nbsp;\\u4E0D\\u518D\\u5F39\\u51FA\\u5361\\u7247\\u003C/p>\\r\\n\\u003C/div>\\r\\n\\u003C/div>\",\"Value\":{\"ID\":\"b3f33ca3a20f47bd908c1bd8506dad66\",\"InputChars\":[\"0\",\"4\",\"5\",\"6\",\"9\"],\"ImageUrl\":\"http://app.mtime.com/card/verifycodeimage_c.aspx?rid=b3f33ca3a20f47bd908c1bd8506dad66\"}}"},"error":null};var callbackAppResult=result_201531110441447484;';
        $str='var result_201531110524289695 = { "value":{"result":"{\"Success\":true,\"Message\":\"\\u003Cdiv class=\\\"card_get\\\">\\r\\n\\u003Cdiv class=\\\"p_r\\\">\\r\\n\\u003Cp class=\\\"money_bg\\\">&nbsp;\\u003C/p>\\r\\n\\u003Cimg src=\\\"http://img31.mtime.cn/game/card/2012/09/21/105517.45863023.png\\\" class=\\\"getcard\\\" title=\\\"\\u70B9\\u51FB\\u5373\\u53EF\\u62FE\\u53D6\\\" alt=\\\"\\u70B9\\u51FB\\u5373\\u53EF\\u62FE\\u53D6\\\">\\r\\n\\u003Ca class=\\\"bgclose\\\" title=\\\"\\u653E\\u5F03\\u62FE\\u53D6\\\" href=\\\"#1\\\">\\u003C/a>\\r\\n\\u003Cp class=\\\"card_none\\\">\\u003Cinput type=\\\"checkbox\\\">&nbsp;\\u4E0D\\u518D\\u5F39\\u51FA\\u5361\\u7247\\u003C/p>\\r\\n\\u003C/div>\\r\\n\\u003C/div>\",\"Value\":{\"ID\":\"572323f56f024ec4875d90cafe7dd23d\",\"InputChars\":[\"0\",\"2\",\"3\",\"4\",\"9\"],\"ImageUrl\":\"http://app.mtime.com/card/verifycodeimage_c.aspx?rid=572323f56f024ec4875d90cafe7dd23d\"}}"},"error":null};var callbackAppResult=result_201531110524289695;';
        preg_match('/src=\\\\\\\\\"(.*?)\\\\\\\\\"/',$str,$match);
        $this->assertEquals('http://img31.mtime.cn/game/card/2009/11/M12428A01.png',$match[1]);
    }


} 