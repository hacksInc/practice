<?php
/**
 * RaidSearchTest.php
 *
 * @author    {$author}
 * @package   Pp.Test
 * @version   $Id$
 */

/**
 * RaidSearch TestCase
 *
 * @author    {$author}
 * @package   Pp.Test
 */
class RaidSearch_TestCase extends Ethna_UnitTestCase
{
    /**
     * initialize test.
     *
     * @access public
     */
    function setUp()
    {
    }

    /**
     *  clean up testcase.
     *
     *  @access public
     */
    function tearDown()
    {
    }
	
	function test_isValidSearchCodeParamDigits()
	{
		$raid_search_m =& $this->backend->getManager('RaidSearch');

        $this->assertFalse(
				Pp_RaidSearchManager::isValidSearchCodeParamDigits(
						-1, Pp_RaidSearchManager::DUNGEON_ID_DIGITS_MAX));
		
        $this->assertTrue(
				Pp_RaidSearchManager::isValidSearchCodeParamDigits(
						0, Pp_RaidSearchManager::DUNGEON_ID_DIGITS_MAX));
		
        $this->assertTrue(
				Pp_RaidSearchManager::isValidSearchCodeParamDigits(
						9999, Pp_RaidSearchManager::DUNGEON_ID_DIGITS_MAX));
		
        $this->assertFalse(
				Pp_RaidSearchManager::isValidSearchCodeParamDigits(
						10000, Pp_RaidSearchManager::DUNGEON_ID_DIGITS_MAX));
		
        $this->assertFalse(
				Pp_RaidSearchManager::isValidSearchCodeParamDigits(
						9999, Pp_RaidSearchManager::DIFFICULTY_DIGITS_MAX));
    }
	
	function test_getSearchCode()
	{
		$raid_search_m =& $this->backend->getManager('RaidSearch');

		$search_code = $raid_search_m->getSearchCode("", 0, 0, 0, 0, 0);
		
        $this->assertTrue(
				preg_match('/^[0-9]{1,10}$/', $search_code));
		
        $this->assertTrue(
				($search_code < pow(2, 32)));
	}
	
	function test_convertDateToClock()
	{
		$raid_search_m =& $this->backend->getManager('RaidSearch');
		
		$time = 1400000061;
		$date = date('Y-m-d H:i:s', $time);
		$clock = $raid_search_m->convertDateToClock($date);
		$rclock = $raid_search_m->roundTimeByFriendMinutes($time);
		
        $this->assertTrue($clock == 23333334);
        $this->assertTrue($rclock == 23333330);
	}
}
