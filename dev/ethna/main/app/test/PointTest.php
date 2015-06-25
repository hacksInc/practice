<?php
/**
 * PointTest.php
 * 
 * @author    {$author}
 * @package   Pp.Test
 * @version   $Id$
 */

/**
 * Point TestCase 
 * 
 * @author    {$author}
 * @package   Pp.Test
 */
class Point_TestCase extends Ethna_UnitTestCase
{
	protected $user_id = 999999999;
	
	protected $payment = null;
	protected $service = null;
	
    /**
     * initialize test.
     * 
     * @access public
     */
    function setUp()
    {
		$db =& $this->backend->getDB();
		
		$sql = "INSERT INTO t_user_base(user_id, ua, account, uipw_hash, dmpw_hash, date_created)"
		     . "VALUES(" . $this->user_id . ", 1, 'dummy', 'dummy', 'dummy', NOW())";
		$db->execute($sql);
    }
    
    /**
     *  clean up testcase.
     * 
     *  @access public
     */
    function tearDown()
    {
        // TODO: write testcase cleanup code.
        // Example: restore database data for development.
    }
    
    /**
     * sample testcase.
     * 
     * @access public
     */
//    function test_Point()
//    {
//        /**
//         *  TODO: write test case! :)
//         *  @see http://simpletest.org/en/first_test_tutorial.html
//         *  @see http://simpletest.org/en/unit_test_documentation.html
//         */
//        $this->fail('No Test! write Test!');
//    }
	
	function test_Exclusive()
	{
		$point_m =& $this->backend->getManager('Point');
		
		$user_id = $this->user_id;
		
		$point_m->initExclusive($user_id);
		
        $this->assertIdentical($point_m->checkExclusive($user_id), true, 'checkExclusive succeed');
        $this->assertIdentical($point_m->checkExclusive('anotheruserid'), false, 'checkExclusive failed for another user');
		
		$another_point_m =& $this->backend->getManager('Point', true);
		$another_point_m->initExclusive($user_id);
		
        $this->assertIdentical($point_m->checkExclusive($user_id), false, 'checkExclusive failed after re-initialized by another instance');
	}
	
	function test_convertPointOutputToPaymentService_Valid()
	{
		$point_m =& $this->backend->getManager('Point');
		
		$point_output = json_decode('{"sts":"OK","msg":["",{"app_id":"JGM_AP_JPY","game_transaction_id":"api9223954825226d3a80154b","regist_date":"2013-09-05 10:52:20","time_stamp":"2013-09-05 10:10:16"}],"pay":{"item_id":"medal","payment":"0"},"service":{"item_id":"medal","service":"950"}}', true);
		list($payment, $service) = $point_m->convertPointOutputToPaymentService($point_output);
		
		$this->assertTrue(is_numeric($payment), 'payment is numeric');
		$this->assertTrue(is_numeric($service), 'service is numeric');
		$this->assertTrue(($payment == 0),      'payment value is correct');
		$this->assertTrue(($service == 950),    'service value is correct');
	}
	
	function test_convertPointOutputToPaymentService_Empty()
	{
		$point_m =& $this->backend->getManager('Point');
		
		$point_output = json_decode('', true);
		list($payment, $service) = $point_m->convertPointOutputToPaymentService($point_output);

		$this->assertIdentical($payment, null, 'payment is null if point_output is empty');
		$this->assertIdentical($service, null, 'service is null if point_output is empty');
	}
	
	function test_convertPointOutputToPaymentService_Collapse()
	{
		$point_m =& $this->backend->getManager('Point');
		
		$point_output = array('foo' => 'var');
		list($payment, $service) = $point_m->convertPointOutputToPaymentService($point_output);

		$this->assertIdentical($payment, null, 'payment is null if point_output is collapsed');
		$this->assertIdentical($service, null, 'service is null if point_output is collapsed');
	}
	
	function test_convertPointOutputToPaymentService_Error()
	{
		$point_m =& $this->backend->getManager('Point');
		
		$point_output = Ethna::raiseError("Test error.", E_USER_ERROR);

		list($payment, $service) = $point_m->convertPointOutputToPaymentService($point_output);

		$this->assertIdentical($payment, null, 'payment is null if point_output is Ethna error');
		$this->assertIdentical($service, null, 'service is null if point_output is Ethna error');
	}
	
	function test_createTransaction()
	{
		$point_m =& $this->backend->getManager('Point');
		
		$user_id = $this->user_id;
		
		$game_transaction_id = $point_m->createTransaction($user_id, 'admin');
		
        $this->assertTrue(is_string($game_transaction_id), 'game_transaction_id is string');
        $this->assertTrue((strlen($game_transaction_id) > 0), 'game_transaction_id is not empty string');
		
        $this->assertIdentical(
			$point_m->isTransactionExists($game_transaction_id, $user_id), 
			true, 
			'Transaction exists'
		);
		
        $this->assertIdentical(
			$point_m->isTransactionAvailable($game_transaction_id, $user_id), 
			true, 
			'Transaction is available'
		);
	}
	
	function test_getDefaultAppId()
	{
		$point_m =& $this->backend->getManager('Point');
		$user_m  =& $this->backend->getManager('User');
		
		foreach (array(
			Pp_UserManager::OS_IPHONE,
			Pp_UserManager::OS_ANDROID
		) as $ua) {
			$app_id = $point_m->getDefaultAppId($ua);

			$this->assertTrue(is_string($app_id), 'app_id is string');
		    $this->assertTrue((strlen($app_id) > 0), 'app_id is not empty string');
		}
	}
	
    function test_inquiry()
    {
		$point_m =& $this->backend->getManager('Point');
		
		$user_id = $this->user_id;
		
		$point_output = $point_m->inquiry($user_id);
		list($payment, $service) = $point_m->convertPointOutputToPaymentService($point_output);
		
		$this->assertTrue(is_numeric($payment), 'payment is numeric');
		$this->assertTrue(is_numeric($service), 'service is numeric');
		
		$this->payment = $payment;
		$this->service = $service;
    }
	
	function test_adjustUserGameTransactionId()
	{
		$point_m =& $this->backend->getManager('Point');
		
		$ret = $point_m->adjustUserGameTransactionId($this->user_id);
		
		$this->assertIdentical($ret, true, 'adjustUserGameTransactionId completed');
	}
	
	function test_gamebonus()
	{
		$point_m =& $this->backend->getManager('Point', true);
		$user_m  =& $this->backend->getManager('User');

		$user_id = $this->user_id;
		$service_count = 100;
		$remote_addr = 'dummy';
		$action = 'dummy';
		
		$base = $user_m->getUserBase($user_id);
		$game_transaction_id = $base['game_transaction_id'];
		
		list($payment, $service) = $point_m->requestGamebonusAndConvertOutput($game_transaction_id, $user_id, $service_count, $remote_addr, $action);
		$this->assertTrue(is_numeric($payment), 'payment is numeric');
		$this->assertTrue(is_numeric($service), 'service is numeric');

		$old_total = $this->payment + $this->service;
		$new_total = $payment + $service;
        $this->assertEqual($old_total + $service_count, $new_total, 'gamebonus is added');

		// ポイント管理サーバへのリクエストに同じゲームトランザクションIDを再利用しても影響ない事を確認
		$another_point_m =& $this->backend->getManager('Point', true);
		list($payment2, $service2) = $another_point_m->requestGamebonusAndConvertOutput($game_transaction_id, $user_id, $service_count, $remote_addr, $action);
        $this->assertEqual($payment2, $payment);
        $this->assertEqual($service2, $service);

		// ポイント管理サーバからの結果をジャグモンDBに記録できることを確認
		$ret = $another_point_m->updatePreparedTransaction();
		$this->assertIdentical($ret, true, 'updatePreparedTransaction completed');

		$last_transaction = $point_m->getTransactionResult($game_transaction_id, $user_id);
		$this->assertTrue(is_array($last_transaction) && (strlen($last_transaction['point_output_sts']) > 0));
		
		// 既にDBに結果が記録されているゲームトランザクションIDを使用してポイント管理サーバへ通信を試みるとエラーになる事を確認
		$retry_ret = $another_point_m->gamebonus($game_transaction_id, $user_id, $service_count, $remote_addr, $action);
        $this->assertTrue(Ethna::isError($retry_ret));
 		
		$new_game_transaction_id = $point_m->createTransaction($user_id);
		$user_m->setUserBase($user_id, array(
			'game_transaction_id' => $new_game_transaction_id,
			'medal' => $payment,
			'service_point' => $service,
		));

		$this->payment = $payment;
		$this->service = $service;
	}
	
	function test_consume()
	{
		$point_m =& $this->backend->getManager('Point', true);
		$user_m  =& $this->backend->getManager('User');

		$user_id = $this->user_id;
		$item_count = 100;
		$remote_addr = 'dummy';
		$action = 'dummy';
		$game_arg = array(
			'consume_id' => '',
			'price'      => $item_count,
		);
		
		$base = $user_m->getUserBase($user_id);
		$game_transaction_id = $base['game_transaction_id'];

        $this->assertEqual($this->payment, $base['medal']);
        $this->assertEqual($this->service, $base['service_point']);
		
		list($payment, $service) = $point_m->requestConsumeAndConvertOutput($game_transaction_id, $user_id, $item_count, $remote_addr, $action);
		$this->assertTrue(is_numeric($payment), 'payment is numeric');
		$this->assertTrue(is_numeric($service), 'service is numeric');

		$old_total = $this->payment + $this->service;
		$new_total = $payment + $service;
        $this->assertEqual($old_total - $item_count, $new_total, 'consumed');
		
		// ポイント管理サーバへのリクエストに同じゲームトランザクションIDを再利用しても影響ない事を確認
		$another_point_m =& $this->backend->getManager('Point', true);
		list($payment2, $service2) = $another_point_m->requestConsumeAndConvertOutput($game_transaction_id, $user_id, $item_count, $remote_addr, $action, $game_arg);
        $this->assertEqual($payment2, $payment);
        $this->assertEqual($service2, $service);

		// ポイント管理サーバからの結果をジャグモンDBに記録できることを確認
		$ret = $another_point_m->updatePreparedTransaction();
		$this->assertIdentical($ret, true, 'updatePreparedTransaction completed');

		$last_transaction = $point_m->getTransactionResult($game_transaction_id, $user_id);
		$this->assertTrue(is_array($last_transaction) && (strlen($last_transaction['point_output_sts']) > 0));
		$this->assertTrue($point_m->isSameArg($last_transaction, $action, $game_arg));
		
		// 既にDBに結果が記録されているゲームトランザクションIDを使用してポイント管理サーバへ通信を試みるとエラーになる事を確認
		$retry_ret = $another_point_m->consume($game_transaction_id, $user_id, $item_count, $remote_addr, $action);
        $this->assertTrue(Ethna::isError($retry_ret));

		$this->payment = $payment;
		$this->service = $service;
	}
}
?>