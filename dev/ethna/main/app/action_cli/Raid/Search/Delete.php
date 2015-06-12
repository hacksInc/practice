<?php
/**
 *  レイドパーティ検索一時データの削除
 *
 *  cron の bin/raid_search_renew_delete.sh から呼ばれる。
 *  tmp_raid_search_party_counter, tmp_raid_search_quest_counter から不要な古い行を削除する。
 *  tmp_raid_search_party, tmp_raid_search_quest は削除しないので注意。
 *  別途、raid_search_truncateのCLIアクションをメンテナンス中に手動実行すること。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  raid_search_delete Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_RaidSearchDelete extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  raid_search_delete action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_RaidSearchDelete extends Pp_CliActionClass
{
    /**
     *  preprocess of raid_search_delete Action.
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
			echo "メンテナンス中 [" . date('Y-m-d H:i:s') . "]\n\n";
			exit(0);
		}
		
        return null;
    }

    /**
     *  raid_search_delete action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$admin_m       = $this->backend->getManager('Admin');
        $raid_search_m = $this->backend->getManager('AdminRaidSearch');
		
		// 二重起動防止ロック
		$lock_result = $this->dirLock();
		if (!$lock_result) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ': Locked. [' . date('Y-m-d H:i:s') . ']');
			$this->backend->logger->log(LOG_CRIT, 'Locked. [' . date('Y-m-d H:i:s') . ']');
			exit(1);
		}
		
		$admin_m->offSessionQueryCache();
		$admin_m->setSessionSqlBigSelectsOn(array('r'));
		
		// 処理開始
		echo "●raid_search_delete開始 [" . date('Y-m-d H:i:s') . "]\n";

		$clock_type_list = array(
			Pp_RaidSearchManager::CLOCK_TYPE_PARTY,
			Pp_RaidSearchManager::CLOCK_TYPE_SALLY,
		);
		
		$limit = 1000;

		$is_ok = true;
		foreach ($clock_type_list as $clock_type) {
			echo "clock_type[" . $clock_type . "]\n";
			$ret = $raid_search_m->deleteExpiredTmpRaidSearchCounter($clock_type, $limit);
			if ($ret === false) {
				$is_ok = false;
				echo "NG\n";
			} else if ($ret >= $limit) {
				echo "WARNING: limit[$limit] ret[$ret]\n";
				$this->backend->logger->log(LOG_CRIT, 
					"不要なレイド検索一時データが残っている可能性があります。 limit[$limit] ret[$ret]"
				);
			}
			
			sleep(1);
		}
		
		echo ($is_ok ? '成功' : '失敗') . "\n";
		
		// ロック解除
		$this->dirUnlock();
		
		echo "終了 [" . date('Y-m-d H:i:s') . "]\n\n";
		
        return null;
    }
}

?>