<?php
/**
 *  Raid/Search/Truncate.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  raid_search_truncate Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_RaidSearchTruncate extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  raid_search_truncate action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_RaidSearchTruncate extends Pp_CliActionClass
{
    /**
     *  preprocess of raid_search_truncate Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        $admin_m = $this->backend->getManager('Admin');
		
		echo "●raid_search_truncate開始 [" . date('Y-m-d H:i:s') . "]\n";
		echo "unit:" . $this->config->get('unit_id') . "\n";
		
		// サーバメンテナンスチェック
		if (!$admin_m->isServerMaintenance()) {
			echo "ERROR:通常運用中の為、処理を行いません。メンテナンス中に実行して下さい。 [" . date('Y-m-d H:i:s') . "]\n\n";
			exit(1);
		}
		
        return null;
    }

    /**
     *  raid_search_truncate action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $raid_search_m = $this->backend->getManager('AdminRaidSearch');
		
		$ret = $raid_search_m->truncateTmpRaidSearchTables();
		echo (($ret === true) ? '成功' : '失敗') . "\n";
		
		echo "終了 [" . date('Y-m-d H:i:s') . "]\n\n";
		
        return null;
    }
}
