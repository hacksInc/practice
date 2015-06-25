<?php
/**
 *  Pp_RestserverManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_RestserverManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RestserverManager extends Ethna_AppManager
{
	// コンストラクタで取得されないDBのインスタンス
	protected $db_m = null;
	protected $db_m_r = null;

	/** コンストラクタ */
	function __construct(&$backend) {
		parent::__construct($backend);

		if( is_null( $this->db_m ))
		{	// インスタンスを取得していないなら取得
			$this->db_m =& $this->backend->getDB( 'm' );
		}

		if( is_null( $this->db_m_r ))
		{	// インスタンスを取得していないなら取得
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
	}

	function post($table, $columns)
	{
		return $this->create($table, $columns);
	}

	function create($table, $columns)
	{
		$ret = $this->db_m->autoExecute($table, $columns, 'INSERT');

		return $this->checkResultAndAffectedRows($ret);
	}

	function patch($table, $id, $columns)
	{
		$where = $this->getWhereClause($table, $id);
		$ret = $this->db_m->autoExecute($table, $columns, 'UPDATE', $where);

		return $this->checkResultAndAffectedRows($ret);
	}

	function delete($table, $id)
	{
		$where = $this->getWhereClause($table, $id);
		$ret = $this->db_m->execute("DELETE FROM $table WHERE $where");

		return $this->checkResultAndAffectedRows($ret);
	}

	/**
	 * patch,delete用のWHERE区を取得する
	 *
	 * @param string $table テーブル名
	 * @param string $id 主キーをカンマ区切りで並べた文字列（Backbone.jsで使用するidと同フォーマット）
	 * @return string WHERE句
	 */
	protected function getWhereClause($table, $id)
	{
		$developer_m = $this->backend->getManager('Developer');

		$pieces = array();
		$ids = explode(',', $id);
		$primary_keys = $developer_m->getPrimaryKeys($table);
		$cnt = count($primary_keys);
		for ($i = 0; $i < $cnt; $i++) {
			if ($developer_m->isDateColumnName($primary_keys[$i])) {
				$ids[$i] = date('Y-m-d H:i:s', $ids[$i]);
				if (!$ids[$i]) {
					error_log('ERROR:' . __FILE__ . ':' . __LINE__);
					return false;
				}
			} elseif ($developer_m->isStringColumnName($primary_keys[$i])) {
				if (strlen($ids[$i]) == 0) {
					error_log('ERROR:' . __FILE__ . ':' . __LINE__);
					return false;
				}
			} else if (($table === 'm_master') && ($primary_keys[$i] === 'table_name')) {
				if (!preg_match('/^m_[a-z_]+/', $ids[$i]) || strlen($ids[$i] > 128)) {
					error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $ids[$i]);
					return false;
				}
			} else {
				if (!is_numeric($ids[$i])) {
					error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $ids[$i]);
					return false;
				}
			}

			$pieces[] = $primary_keys[$i] . " = '" . $ids[$i] . "'";
		}

		$where = implode(' AND ', $pieces);

		return $where;
	}

	protected function checkResultAndAffectedRows($ret)
	{
		if (!$ret) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$affected_rows = $this->db_m->db->affected_rows();
		if ($affected_rows != 1) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		return true;
	}
}
