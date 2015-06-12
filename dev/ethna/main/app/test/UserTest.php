<?php
/**
 * UserTest.php
 * 
 * @author    {$author}
 * @package   Pp.Test
 * @version   $Id$
 */

/**
 * User TestCase 
 * 
 * @author    {$author}
 * @package   Pp.Test
 */
class User_TestCase extends Ethna_UnitTestCase
{
    /**
     * initialize test.
     * 
     * @access public
     */
    function setUp()
    {
        // TODO: write test initialization code.
        // Example: read test data from database.
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
//    function test_User()
//    {
//        /**
//         *  TODO: write test case! :)
//         *  @see http://simpletest.org/en/first_test_tutorial.html
//         *  @see http://simpletest.org/en/unit_test_documentation.html
//         */
//        $this->fail('No Test! write Test!');
//    }
	
	function test_getRandomAccount()
	{
		$user_m =& $this->backend->getManager('User');
		
		$account = $user_m->getRandomAccount();
		
        $this->assertTrue(is_string($account), 'account is string');
        $this->assertEqual(strlen($account), 20, 'account length is 20');
        $this->assertPattern('/^[a-zA-Z0-9]+$/', $account, 'account contains only alphabet or number');
        $this->assertNoPattern('/[1Il0O]/', $account, 'account does not contain mistakable letter');
	}
}

?>
