<?php
/**
 *  Pp_AdminHelpManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_HelpManager.php';

/**
 *  Pp_AdminHelpManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminHelpManager extends Pp_HelpManager
{
	/** 最後に新規作成された内容ID */
	protected $last_insert_category_id = null;

	// コンストラクタで取得されないDBのインスタンス
	protected $db_m = null;
	protected $db_m_r = null;

	/**
	 *  コンストラクタ
	 */
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

	/**
	 * ヘルプ大項目内容データを取得する
	 *
	 * @param int $category_id 内容ID
	 * @return array m_help_categoryデータ（m_help_categoryのカラム名がキー）
	 */
	function getHelpCategory($category_id)
	{
		return $this->db_m_r->GetRow(
			"SELECT * FROM m_help_category WHERE category_id = ?",
			array($category_id)
		);
	}

	/**
	 * ヘルプ詳細文内容データを取得する
	 *
	 * @param int $help_id 内容ID
	 * @return array m_helpデータ（m_helpのカラム名がキー）
	 */
	function getHelpDetail($help_id)
	{
		return $this->db_m_r->GetRow(
			"SELECT * FROM m_help WHERE help_id = ?",
			array($help_id)
		);
	}

	/**
	 * ヘルプ大項目内容データの一覧を取得
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getHelpCategoryList($offset = 0, $limit = 100000, $end = false)
	{
		$param = array();
		$sql = "SELECT *"
			. " FROM m_help_category"
			. " WHERE date_end " . ($end ? "<=" : ">") . " NOW()";

		if ($end) {
			$sql .= " ORDER BY date_disp DESC, category_id DESC";
		} else {
			$sql .= " ORDER BY priority ASC, date_disp DESC, category_id DESC";
		}

		$param[] = $offset;
		$param[] = $limit;
		$sql .= " LIMIT ?, ?";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * ヘルプ詳細文内容データの一覧を取得
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getHelpDetailList($offset = 0, $limit = 100000, $del_flg = false)
	{
		$where = "";
		if (!$del_flg)
		{
			$where = " WHERE del_flg = 0";
		}

		$param = array();
		$sql = "SELECT *"
			. " FROM m_help"
			. $where
			. " ORDER BY priority ASC, help_id DESC";

		$param[] = $offset;
		$param[] = $limit;
		$sql .= " LIMIT ?, ?";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * ヘルプ大項目内容データを更新する
	 *
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateHelpCategory($columns)
	{
		if (!is_numeric($columns['category_id'])) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$where = "category_id = " . $columns['category_id'];
		unset($columns['category_id']);

		return $this->db_m->db->AutoExecute('m_help_category', $columns, 'UPDATE', $where);
	}

	/**
	 * ヘルプ詳細文内容データを更新する
	 *
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateHelpDetail($columns)
	{
		if (!is_numeric($columns['help_id'])) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$where = "help_id = " . $columns['help_id'];
		unset($columns['help_id']);

		return $this->db_m->db->AutoExecute('m_help', $columns, 'UPDATE', $where);
	}

	/**
	 * ヘルプ大項目内容データを新規作成する
	 *
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーは含めなくてよい）
	 * @return bool 成否
	 */
	function insertHelpCategory($columns)
	{
		if (!isset($columns['category_id'])) {
			$max = $this->db_m_r->GetOne("SELECT MAX(category_id) FROM m_help_category");
			if (!$max) $max = 0;

			$this->last_insert_category_id = $columns['category_id'] = $max + 1;
		}

		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_m->db->AutoExecute('m_help_category', $columns, 'INSERT');
	}

	/**
	 * ヘルプ詳細文内容データを新規作成する
	 *
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーは含めなくてよい）
	 * @return bool 成否
	 */
	function insertHelpDetail($columns)
	{
		if (!isset($columns['help_id'])) {
			$max = $this->db_m_r->GetOne("SELECT MAX(help_id) FROM m_help");
			if (!$max) $max = 0;

			$this->last_insert_help_id = $columns['help_id'] = $max + 1;
		}

		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_m->db->AutoExecute('m_help', $columns, 'INSERT');
	}

	/** 最後に新規作成された内容IDを取得する */
	function getLastInsertCategoryId()
	{
		return $this->last_insert_category_id;
	}

	/** 最後に新規作成された内容IDを取得する */
	function getLastInsertHelpId()
	{
		return $this->last_insert_help_id;
	}

	/** 画像ディレクトリのfilemtimeを取得する */
	function getImageDirMtime()
	{
		$path = $this->getHelpDetailPicturePath(1);
		$dir = dirname($path);

		return filemtime($dir);
	}
}
