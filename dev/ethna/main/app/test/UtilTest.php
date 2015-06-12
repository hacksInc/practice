<?php
/**
 * UtilTest.php
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
class Util_TestCase extends Ethna_UnitTestCase
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
 //   function test_Util()
 //   {
 //       /**
 //        *  TODO: write test case! :)
 //        *  @see http://simpletest.org/en/first_test_tutorial.html
 //        *  @see http://simpletest.org/en/unit_test_documentation.html
 //        */
 //       $this->fail('No Test! write Test!');
 //   }
	
	function test_ScriptMatch()
	{
        $this->assertTrue(script_match('/foo/', null, '/foo/'));
        $this->assertTrue(script_match('/foo/', null, '/foo/bar'));
        $this->assertTrue(script_match('/foo/bar', null, '/foo/bar'));
        $this->assertFalse(script_match('/foo/bar', null, '/foo/bar/baz'));
        $this->assertFalse(script_match('/foo/bar', null, '/foo/barr'));
        $this->assertFalse(script_match('/foo/barr', null, '/foo/bar'));
	}
}

?>
