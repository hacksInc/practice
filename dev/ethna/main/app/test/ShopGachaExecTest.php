<?php
/**
 * ShopGachaTest.php
 *
 * @author    {$author}
 * @package   Pp.Test
 * @version   $Id$
 */

/**
 * Util TestCase
 *
 * @author    {$author}
 * @package   Pp.Test
 */
class ShopGachaExec_TestCase extends Ethna_UnitTestCase
{

    public $action_name = 'api_shop_gacha_exec';

    /**
     * initialize test.
     *
     * @access public
     */
    function setUp()
    {
        $this->createActionForm();
        $this->createActionClass();
    }

    /**
     *  clean up testcase.
     *
     *  @access public
     */
    function tearDown()
    {
    }

    function test_ScriptMatch()
    {
//        $this->ac->authenticated_basic_auth = array('user' => 1, 'password' => 1111);

        $this->ac->authenticate();

        $this->af->set('gacha_cnt', 1); // 同時にガチャを引く数
        $this->af->set('gacha_id', 10); // 1:ブロンズガチャ, 2: ゴールドガチャ, 3:レアガチャ
        $this->af->set('game_transaction_id', 1); // マジカルメダルの時に使用

//        $forward_name = $this->ac->authenticate();
//        var_dump($forward_name);
//        $this->dump("gacha exec test forward_name: ". $forward_name);


        for ($i = 0; $i < 1; $i++) {
            $forward_name = $this->ac->prepare();
            $this->dump("gacha exec test forward_name: ". $forward_name);

            $time_start = microtime(true);
            $forward_name = $this->ac->perform();
            $this->dump("gacha exec test forward_name: ". $forward_name);
            $time_end = microtime(true);
            $time = $time_end - $time_start;

            echo ("$time 秒掛かりました。<br/>");

        }
        $output = array();
        foreach ($this->af->getOutputNames() as $name) {
            $output[$name] = $this->af->getApp($name);
        }

        echo "<br/><br/>-----------<br/>";
        var_dump($output);
        echo "<br/>-----------<br/><br/>";
//        $this->assertTrue(true);
    }
}

?>
