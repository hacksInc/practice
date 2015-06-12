<?php
/**
 *  Pp_AdminNewsManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_NewsManager.php';

/**
 *  Pp_AdminNewsManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminNewsManager extends Pp_NewsManager
{
	/** 最後に新規作成された内容ID */
	protected $last_insert_content_id = null;

	/** 最後に新規作成されたメインバナーデータの各種ID */
	protected $last_insert_home_banner_id = array(
		'hbanner_id' => null,
		'img_id'     => null,
	);

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
	 * ニュース内容データを取得する
	 *
	 * @param int $content_id 内容ID
	 * @return array m_news_contentデータ（m_news_contentのカラム名がキー）
	 */
	function getNewsContent($content_id)
	{
		return $this->db_m_r->GetRow(
			"SELECT * FROM m_news_content WHERE content_id = ?",
			array($content_id)
		);
	}

	/**
	 * メインバナーデータを取得する
	 *
	 * @param int $hbanner_id メインバナーID
	 * @return array m_home_bannertデータ（m_home_bannerのカラム名がキー）
	 */
	function getHomeBanner($hbanner_id)
	{
		return $this->db_m_r->GetRow(
			"SELECT * FROM m_home_banner WHERE hbanner_id = ?",
			array($hbanner_id)
		);
	}

	/**
	 * ニュース内容データの一覧を取得
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @param string $lang 言語
	 * @param int $ua User-Agent種別
	 * @return array
	 */
	function getNewsContentList($offset = 0, $limit = 100000, $end = false, $lang = null, $ua = null)
	{
		$param = array();
		$sql = "SELECT *"
		     . " FROM m_news_content"
		     . " WHERE date_end " . ($end ? "<=" : ">") . " NOW()";

		if ($ua !== null) {
			$param[] = $ua;
			$sql .= " AND ua = ?";
		}

		if ($lang != null) {
			$param[] = $lang;
			$sql .= " AND lang = ?";
		}

		if ($end) {
			$sql .= " ORDER BY date_disp DESC, content_id DESC";
		} else {
			$sql .= " ORDER BY priority ASC, date_disp DESC, content_id DESC";
		}

		$param[] = $offset;
		$param[] = $limit;
		$sql .= " LIMIT ?, ?";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * メインバナーデータの一覧を取得
	 *
	 * @param int $offset
	 * @param int $limit
	 * @param bool $end 取得対象を表示終了したデータに限定するか
	 * @return array
	 */
	function getHomeBannerList($offset = 0, $limit = 100000, $end = false)
	{
		$param = array();
		$sql = "SELECT *"
		     . " FROM m_home_banner";

		if ($end) {
			$sql .= " WHERE date_end <= NOW()"
			     .  " ORDER BY date_end DESC, hbanner_id DESC"; // 最新の表示終了データを先頭に
		} else {
			$sql .= " WHERE date_end > NOW()"
			     .  " ORDER BY pri ASC, date_start DESC, hbanner_id DESC";
		}

		$param[] = $offset;
		$param[] = $limit;
		$sql .= " LIMIT ?, ?";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * ニュース内容データを更新する
	 *
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateNewsContent($columns)
	{
		if (!is_numeric($columns['content_id'])) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$where = "content_id = " . $columns['content_id'];
		unset($columns['content_id']);

		return $this->db_m->db->AutoExecute('m_news_content', $columns, 'UPDATE', $where);
	}

	/**
	 * メインバナーデータを更新する
	 *
	 * @param array $columns 更新する内容（カラム名 => 値 の連想配列。主キーも含める事）
	 * @return bool 成否
	 */
	function updateHomeBanner($columns)
	{
		if (!is_numeric($columns['hbanner_id'])) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		$where = "hbanner_id = " . $columns['hbanner_id'];
		unset($columns['hbanner_id']);

		return $this->db_m->db->AutoExecute('m_home_banner', $columns, 'UPDATE', $where);
	}

	/**
	 * ニュース内容データを新規作成する
	 *
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーは含めなくてよい）
	 * @return bool 成否
	 */
	function insertNewsContent($columns)
	{
		if (!isset($columns['content_id'])) {
			$max = $this->db_m_r->GetOne("SELECT MAX(content_id) FROM m_news_content");
			if (!$max) $max = 0;

			$this->last_insert_content_id = $columns['content_id'] = $max + 1;
		}

		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_m->db->AutoExecute('m_news_content', $columns, 'INSERT');
	}

	/**
	 * メインバナーデータを新規作成する
	 *
	 * @param array $columns 新規作成する内容（カラム名 => 値 の連想配列。主キーは含めなくてよい）
	 * @return bool 成否
	 */
	function insertHomeBanner($columns)
	{
		foreach (array_keys($this->last_insert_home_banner_id) as $colname) {
			if (!isset($columns[$colname])) {
				$max = $this->db_m_r->GetOne("SELECT MAX($colname) FROM m_home_banner");
				if (!$max) $max = 0;

				$columns[$colname] = $max + 1;
			}

			$this->last_insert_home_banner_id[$colname] = $columns[$colname];
		}

		if (!isset($columns['date_created'])) {
			$columns['date_created'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		return $this->db_m->db->AutoExecute('m_home_banner', $columns, 'INSERT');
	}

	/** 最後に新規作成された内容IDを取得する */
	function getLastInsertContentId()
	{
		return $this->last_insert_content_id;
	}

	/** 最後に新規作成されたメインバナーデータの各種ID値を取得する */
	function getLastInsertHomeBannerId($colname)
	{
		return $this->last_insert_home_banner_id[$colname];
	}

	/** メインバナー画像ディレクトリのfilemtimeを取得する */
	function getHomeBannerDirMtime()
	{
		$path = $this->getHomeBannerPath(1);
		$dir = dirname($path);

		if (file_exists($dir))
		{
			return filemtime($dir);
		} else {
			return false;
		}
	}
}
?>
