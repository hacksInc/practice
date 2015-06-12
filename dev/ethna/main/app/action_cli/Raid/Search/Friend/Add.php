<?php
/**
 *  Raid/Search/Friend/Add.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  raid_search_friend_add Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_RaidSearchFriendAdd extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  raid_search_friend_add action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_RaidSearchFriendAdd extends Pp_CliActionClass
{
    /**
     *  preprocess of raid_search_friend_add Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        $admin_m = $this->backend->getManager('Admin');
		
		// サーバメンテナンスチェック
		if ($admin_m->isServerMaintenance()) {
//			echo "メンテナンス中 [" . date('Y-m-d H:i:s') . "]\n\n";
//			exit(0);
		}
		
        return null;
    }

    /**
     *  raid_search_friend_add action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $raid_search_m = $this->backend->getManager('AdminRaidSearch');

		$time_from = $_SERVER['REQUEST_TIME'] - ($_SERVER['REQUEST_TIME'] % 600);
		$time_to   = $time_from + 600;

		// 処理開始
		echo "●raid_search_friend_add開始 [" . date('Y-m-d H:i:s') . "]\n";
		echo "time_from: " . $time_from . "\n";
		echo "time_to: " . $time_to . "\n";
		
		$raid_search_m->addPartyFriendTmpDataOnBatch($time_from, $time_to);

		echo "終了 [" . date('Y-m-d H:i:s') . "]\n\n";
		
        return null;
    }
}

?>
