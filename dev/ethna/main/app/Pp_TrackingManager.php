<?php
/**
 *  Pp_TrackingManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  ユーザトラッキングマネージャ
 * 
 *  運営から要望があったKPIやカスタマーサポート関連の情報を記録する。
 *  記録先が別DBサーバになったり、MySQLではなくファイルやsyslogに変更になる可能性もあるので、
 *  MySQLのトリガー等は使用せずに実装する。
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_TrackingManager extends Ethna_AppManager
{
	/**
	 * ログバッファ
	 * @var array $log_buffer = array(array(user_id, columns),   array(user_id, columns), ...)
	 */
	protected $log_buffer = array();
	
	/**
	 * DB接続(pp-ini.phpの'dsn_log'で定義したDB)
	 */
	protected $db_log = null;
	
	/**
	 * 記録する
	 * 
	 * この関数ではまだ最終的な記録先へは出力せず、バッファリングするのみ
	 * @param int $user_id ジャグモン内ユーザID
	 * @param array $columns 記録内容（$columns[カラム名] = 内容）　※カラム名はlog_trackingテーブルにあるカラムと同名で
	 */
	function log($user_id, $columns)
	{
/*
		// ユーザー基本情報が指定されていなかったら補う
		if (!isset($columns['rank'])) {
			$user_m = $this->backend->getManager('User');
			$base = $user_m->getUserBase($user_id);
			if ($base && isset($base['rank'])) {
				$columns['rank'] = $base['rank'];
			}
		}

		$monster_m = $this->backend->getManager('Monster');
		foreach (array('_after', '_before') as $suffix) {
			if (isset($columns['monster_id' . $suffix])) {
				// モンスター名が指定されていなかったら補う
				if (!isset($columns['monster_name' . $suffix])) {
					$master_monster = $monster_m->getMasterMonster($columns['monster_id' . $suffix]);
					if ($master_monster && isset($master_monster['name_ja'])) {
						$columns['monster_name' . $suffix] = $master_monster['name_ja'];
					}
				}
			}
		}
		
		if (isset($columns['item_id'])) {
			// アイテム名が指定されていなかったら補う
			if (!isset($columns['item_name'])) {
				$item_m = $this->backend->getManager('Item');
				$master_item = $item_m->getMasterItem($columns['item_id']);
				if ($master_item && isset($master_item['name_ja'])) {
					$columns['item_name'] = $master_item['name_ja'];
				}
			}
		}
		
		// 所持数(差分)が指定されていなくて計算で求められる場合は補う
		if (isset($columns['num_after']) && isset($columns['num_before']) && 
			!isset($columns['num_delta'])
		) {
			$columns['num_delta'] = $columns['num_after'] - $columns['num_before'];
		}
		
		// 'how'が指定されていなかったらaction_nameで代用
		if (!isset($columns['how'])) {
			$columns['how'] = $this->backend->ctl->getCurrentActionName();
		}

		// バッファに保存
		$this->log_buffer[] = array($user_id, $columns);
*/
	}

	/**
	 * フラッシュ
	 * 
	 * バッファリングされたログをまとめて出力する。
	 * Pp_Plugin_Filter_TrackingのpostFilterから呼ぶようにしてあるので、
	 * 各アクションやマネージャから個別に呼ぶ必要は無い。
	 * MySQLのトランザクション外なのでエラー起きてもロールバックできないが、
	 * エラーの場合はEthnaのログへ出力するので、担当者が最低限の事後対応する事は可能なはず。
	 */
	function flush()
	{
/*
		while ($log = array_shift($this->log_buffer)) {
			$this->_flush($log[0], $log[1]);
		}
*/
	}
	
/*
	protected function _flush($user_id, $columns)
	{
		if (!$this->db_log) {
			$this->db_log =& $this->backend->getDB('log');
		}
		
		$param = array_values($columns);
		$param[] = $user_id;
		$sql = "INSERT INTO log_tracking(" . implode(",", array_keys($columns))
			 . ", user_id, date_created)"
			 . " VALUES(" . str_repeat("?,", count($columns)) . "?,NOW())";
		if (!$this->db_log->execute($sql, $param)) {
			$this->backend->logger->log(LOG_ERR, 
				'Tracking flush failed. user_id=[' . $user_id . '] '.
				'columns=[' . var_export($columns, true) . ']'
			);
		}

		$this->backend->logger->log(LOG_DEBUG, 
			'Tracking flushed. user_id=[' . $user_id . '] '.
			'columns=[' . var_export($columns, true) . ']'
		);
	}
*/
}
?>
