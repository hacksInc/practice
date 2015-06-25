<?php
/**
 *  Pp_HelpManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_HelpManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_HelpManager extends Ethna_AppManager
{
	protected $db_m_r = null;	//	マスタデータDB

	const HELP_TYPE_HELP = 1;
	const HELP_TYPE_LOG  = 2;

	protected function set_db()
	{
		if( is_null( $this->db_m_r ))
		{
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
	}

	/**
	 * 表示日時の短縮表記を取得する
	 *
	 * @param string $date_disp 表示日時(Y-m-d H:i:s)
	 * @return string 短縮表記(Y/ n/ j)
	 */
	function getDateDispShort($date_disp)
	{
		$date = date('Y/m/d', strtotime($date_disp));
		$short = str_replace('/0', '/ ', $date);

		return $short;
	}

	/**
	 * ヘルプデータの一覧を取得
	 *
	 * @param int $test_flag 表示テスト中フラグ(0 or 1) 省略可
	 * @param string $now 現在日時(Y-m-d H:i:s) 省略可
	 * @return array
	 */
	function getHelpCategoryList($test_flag = 0, $now = null)
	{
		$this->set_db();

		if (!$now) {
			$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		$param = array($now, $now);
		$sql = "SELECT *"
		     . " FROM m_help_category"
		     . " WHERE date_start <= ?"
		     . " AND ? < date_end";

		if (!$test_flag) {
			$sql .= " AND test_flag = 0";
		}

		$sql .= " ORDER BY priority ASC, category_id ASC";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * ヘルプ詳細データの一覧を取得
	 *
	 * @param int $category_id ヘルプ大項目選択
	 * @return array
	 */
	function getHelpDetailList($category_id)
	{
		$this->set_db();

		$param = array($category_id);
		$sql = "SELECT *"
		     . " FROM m_help"
		     . " WHERE category_id = ?"
		     . " AND del_flg = 0";

		$sql .= " ORDER BY priority ASC, help_id ASC";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * ヘルプ詳細データを取得
	 *
	 * @param int $help_id 詳細ID
	 * @return array
	 */
	function getHelpDetail($help_id)
	{
		$this->set_db();

		return $this->db_m_r->GetRow(
			"SELECT * FROM m_help WHERE help_id = ?",
			array($help_id)
		);
	}

	/**
	 * ヘルプの画像のパスを取得
	 *
	 * サーバのファイルシステム上のパスを取得する。
	 * @param int $detail_id 内容ID
	 * @return string パス
	 */
	function getHelpDetailPicturePath($detail_id)
	{
		$path = BASE . '/data/resource/image/help/detail_picture_' . $detail_id . '.png';

		return $path;
	}

	/** 画像ディレクトリのfilemtimeを取得する */
	function getImageDirMtime()
	{
		$path = $this->getHelpDetailPicturePath(1);
		$dir = dirname($path);

		if (file_exists($dir))
		{
			return filemtime($dir);
		} else {
			return false;
		}
	}
}
