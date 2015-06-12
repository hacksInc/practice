<?php
/**
 * MonsterbookTest.php
 * 
 * @author    {$author}
 * @package   Pp.Test
 * @version   $Id$
 */

/**
 * Monsterbook TestCase 
 * 
 * @author    {$author}
 * @package   Pp.Test
 */
class Monsterbook_TestCase extends Ethna_UnitTestCase
{
	protected $user_id = 999999999;
	
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
//    function test_Monsterbook()
//    {
//        /**
//         *  TODO: write test case! :)
//         *  @see http://simpletest.org/en/first_test_tutorial.html
//         *  @see http://simpletest.org/en/unit_test_documentation.html
//         */
//        $this->fail('No Test! write Test!');
//    }
	
	function test_divideBookIdx()
	{
		$monster_m =& $this->backend->getManager('Monster');
		
		foreach (array(
			// 先頭ビット
			// 直値の74は、Pp_MonsterManager::BOOK_COL_LEN - 1 の意
			0   => array('colname_suffix' => 0, 'hex_position' => 0, 'char_position' => 74, 'bit_position' => 0),
			
			// 16進で1桁目の最終ビット
			3   => array('colname_suffix' => 0, 'hex_position' => 0, 'char_position' => 74, 'bit_position' => 3),
			
			// 16進で2桁目の最終ビット
			// 直値の73は、Pp_MonsterManager::BOOK_COL_LEN - 1 - 1の意
			7   => array('colname_suffix' => 0, 'hex_position' => 1, 'char_position' => 73, 'bit_position' => 3),
			
			// 2番目のカラムの先頭ビット
			// 直値の300は、Pp_MonsterManager::BOOK_COL_LEN * 4 の意
			300 => array('colname_suffix' => 1, 'hex_position' => 0, 'char_position' => 74, 'bit_position' => 0),
		) as $book_idx => $correct_answer) {
			$divided = $monster_m->divideBookIdx($book_idx);
			$this->assertTrue(($divided == $correct_answer), "divided value is correct. book_idx=[$book_idx]");
		}
	}
}

?>
