<?php
/**
 *  レイドパーティ検索一時データの更新
 *
 *  cron の bin/raid_search_renew_delete.sh から呼ばれる。
 *  このバッチで更新するのは出撃情報のみ。
 *  パーティ作成・更新情報はAPI内で都度更新する必要があるので注意。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  raid_search_renew Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_RaidSearchRenew extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  raid_search_renew action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_RaidSearchRenew extends Pp_CliActionClass
{
    /**
     *  preprocess of raid_search_renew Action.
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
     *  raid_search_renew action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $raid_search_m = $this->backend->getManager('AdminRaidSearch');
        $raid_quest_m  = $this->backend->getManager('AdminRaidQuest');
		
		// 二重起動防止ロック
		$lock_result = $this->dirLock();
		if (!$lock_result) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ': Locked. [' . date('Y-m-d H:i:s') . ']');
			$this->backend->logger->log(LOG_CRIT, 'Locked. [' . date('Y-m-d H:i:s') . ']');
			exit(1);
		}
		
		// 前回バッチでどの管理IDまで処理済みかを取得する
		$id_file = $raid_search_m->getLastRaidQuestIdFile();
		$id_file_exists = file_exists($id_file);
		
		$id_last = null;
		if ($id_file_exists) {
			$id_last = file_get_contents($id_file);
		}
		
		// 前回バッチの情報がなかったらDBから既存の最大値を取得
		if (!$id_last) {
			$id_last = $raid_quest_m->getMaxLogRaidQuestId();
			if (!$id_last) {
				// OK, no data.
				echo "No data. [" . date('Y-m-d H:i:s') . "]\n";
				$this->dirUnlock(); // ロック解除
				return null;
			}
			
			file_put_contents($id_file, $id_last, LOCK_EX);
			sleep(1);
		}

		// 処理範囲を設定する
		$now = time();
		$date_from = date('Y-m-d H:i:s', $now - Pp_RaidSearchManager::MAX_BACK_MINUTES * 60);
		$date_to   = date('Y-m-d H:i:s', $now);
		$limit     = 100; // ループ1回あたりの処理件数の限界値
		
		// 処理開始
		echo "●raid_search_renew開始 [" . date('Y-m-d H:i:s') . "]\n";
		echo "date_from: " . $date_from . "\n";
		echo "date_to: " . $date_to . "\n";
		echo "id_last: " . $id_last . "\n";

		$cnt = 0;
		$id = $id_last;
		while ($id) {
			$id_min = $id + 1;
			$id_max = $id_min + $limit;
			$id = $raid_search_m->renewTmpDataAfterQuestInsertOnBatch($id_min, $id_max, $date_from, $date_to);
			
			if ($id) {
				file_put_contents($id_file, $id, LOCK_EX);
			}
	
			$cnt++;
			if ($cnt > 9999) {
				error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ': Possible infinite loop.');
				$this->backend->logger->log(LOG_CRIT, 'Possible infinite loop.');
				break;
			}
			
			if ($cnt % 10 == 0) {
				sleep(1);
			}
		}
		
		// ロック解除
		$this->dirUnlock();
		
		echo "終了 [" . date('Y-m-d H:i:s') . "]\n\n";
		
        return null;
    }
}

?>