<?php
/**
 *  Pp_NewsManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  Pp_NewsManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_NewsManager extends Ethna_AppManager
{
	protected $db_m_r = null;	//	マスタデータDB

	const NEWS_TYPE_NEWS = 1;
	const NEWS_TYPE_LOG  = 2;

	/** ホームバナー バナータイプ：なし */
	const HOME_BANNER_TYPE_NONE = 0;

	/** ホームバナー バナータイプ：ショップ */
	const HOME_BANNER_TYPE_SHOP = 1;

	/** ホームバナー バナータイプ：ミッション */
	const HOME_BANNER_TYPE_MISSION = 2;
	
	/** ホームバナー バナータイプ：キャラクター */
	const HOME_BANNER_TYPE_CHARACTER = 3;

	/** ホームバナー バナータイプ：フォト */
	const HOME_BANNER_TYPE_PHOTO = 4;

	/** ホームバナー バナータイプ：メダル */
	const HOME_BANNER_TYPE_MEDAL = 5;

	/** ホームバナー バナータイプ：プレゼントボックス */
	const HOME_BANNER_TYPE_PRESENT_BOX = 6;

	/** ホームバナー バナータイプ：URL（外部ブラウザ） */
	const HOME_BANNER_TYPE_URL = 7;

	/** ホームバナー バナータイプ：WEBVIEW */
	const HOME_BANNER_TYPE_WEBVIEW = 8;

	/** ホームバナー表示ステータス：通常 */
	const HOME_BANNER_DISP_STS_NORMAL = 0;

	/** ホームバナー表示ステータス：表示テスト */
	const HOME_BANNER_DISP_STS_TEST   = 1;

	/** ホームバナー表示ステータス：表示一時停止 */
	const HOME_BANNER_DISP_STS_PAUSE  = 2;

	/** チュートリアルクリア後にアプリから取得するメインバナーをのimg_id */
	const TUTORIAL_CLEAR_HOME_BANNER_IMG_ID = 2;


	protected function set_db()
	{
		if( is_null( $this->db_m_r ))
		{
			$this->db_m_r =& $this->backend->getDB( 'm_r' );
		}
	}

	/**
	 * 最新のお知らせを取得する
	 */
	function getLatestNews($date, $type = self::NEWS_TYPE_NEWS)
	{
		// $cache_m =& Ethna_CacheManager::getInstance('memcache');
		// $cacheData = $cache_m->get("latest_news_". $type);

		// if (($cacheData && !Ethna::isError($cacheData)) ||
		//     ($cacheData && Ethna::isError($cacheData) && $cacheData->getCode() != E_CACHE_NO_VALUE)) {
		//     return $cacheData;
		// }

		$param = array($type, $date, $date);
		$sql = "SELECT * FROM m_news"
			. " WHERE type = ? AND date_start <= ? AND date_end > ?"
			. " ORDER BY date_start DESC LIMIT 1";

		$data = $this->db_m_r->GetRow($sql, $param);

		// if ($data) {
		//     $cache_m->set("latest_news_". $type, $data, 30);
		// }
		return $data;
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
	 * ニュース内容データを取得する
	 *
	 * @param int $content_id 内容ID
	 * @return array m_news_contentデータ（m_news_contentのカラム名がキー）
	 */
	function getNewsContent($content_id)
	{
		$this->set_db();

		return $this->db_m_r->GetRow(
			"SELECT * FROM m_news_content WHERE content_id = ?",
			array($content_id)
		);
	}

	/**
	 * 現在のニュース内容データの一覧を取得
	 *
	 * @param string $lang 言語
	 * @param int $ua User-Agent種別
	 * @param int $test_flag 表示テスト中フラグ(0 or 1) 省略可
	 * @param string $now 現在日時(Y-m-d H:i:s) 省略可
	 * @return array
	 */
	function getCurrentNewsContentList($test_flag = 0, $now = null)
	{
		$this->set_db();

		if (!$now) {
			$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		//$param = array($now, $now, $lang, $ua);
		$param = array($now, $now);
		$sql = "SELECT *"
			. " FROM m_news_content"
			. " WHERE date_start <= ?"
			. " AND ? < date_end";

		if (!$test_flag) {
			$sql .= " AND test_flag = 0";
		}

		$sql .= " ORDER BY priority ASC, date_disp DESC";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * API応答用の現在のメインバナーデータの一覧を取得
	 *
	 * @param int $max_disp_status 表示ステータス最大値
	 * @param string $now 現在日時(Y-m-d H:i:s) 省略可
	 * @param int $ua User-Agent種別 省略可
	 * @return array  一覧 array(array('img_id' => バナーイメージID, 'type' => バナータイプ, 'url_ja' => URL), …以下同様の配列)
	 */
	function getCurrentHomeBannerListForApiResponse($max_disp_status = 0, $now = null, $ua = null)
	{
		$this->set_db();

		$user_m = $this->backend->getManager('User');

		if (!$now) {
			$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		// $cache_m =& Ethna_CacheManager::getInstance('memcache');
		// $cacheData = $cache_m->get("HomeBannerList_disp_status_". $max_disp_status);

		// if (($cacheData && !Ethna::isError($cacheData)) ||
		//     ($cacheData && Ethna::isError($cacheData) && $cacheData->getCode() != E_CACHE_NO_VALUE)) {
		//     return $cacheData;
		// }

		$param = array($now, $now, $max_disp_status);
		$sql = "SELECT img_id, type, url_ja, banner_attribute, banner_attribute_value, ex_disp_sts, screen_name, add_user_id, add_appver"
			. " FROM m_home_banner"
			. " WHERE date_start <= ?"
			. " AND ? < date_end"
			. " AND disp_sts <= ?";

		if ($ua !== null) {
			$param[] = Pp_UserManager::OS_IPHONE_ANDROID;
			$param[] = $ua;
			$sql .= " AND (ua = ? OR ua = ?)";
		}

		$sql .= " ORDER BY pri ASC, date_start DESC, hbanner_id DESC"
			. " LIMIT 7";

		$data = $this->db_m_r->GetAll($sql, $param);

		// if ($data) {
		//     $cache_m->set("HomeBannerList_disp_status_". $max_disp_status, $data, 30);
		// }
		return $data;
	}

	/**
	 * 使用可能なメインバナーのバナーイメージIDか
	 *
	 * @param int $max_disp_status 表示ステータス最大値
	 * @param string $now 現在日時(Y-m-d H:i:s) 省略可
	 * @return bool 真否
	 */
	function isHomebannerImgIdAvailable($img_id, $max_disp_status = 0, $now = null)
	{
		$this->set_db();

		// チュートリアルクリア後の為の番号は許可
		if ($img_id == self::TUTORIAL_CLEAR_HOME_BANNER_IMG_ID) {
			return true;
		}

		if (!$now) {
			$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}

		// /user/boot等で渡すリストに含まれる番号は許可
		// ただしUser-Agent種別やLIMITの件数は問わない
		$param = array($now, $now, $max_disp_status, $img_id);
		$sql = "SELECT img_id"
			. " FROM m_home_banner"
			. " WHERE date_start <= ?"
			. " AND ? < date_end"
			. " AND disp_sts <= ?"
			. " AND img_id = ?"
			. " LIMIT 1";

		$ret = $this->db_m_r->GetOne($sql, $param);
		if (is_numeric($ret) && ($ret > 0)) {
			return true;
		}

		// 上記のいずれにも該当しない場合、拒否
		return false;
	}

	/**
	 * url_idに対応したURLを返す
	 *
	 * @param int $url_id URLのID
	 * @param string $now 現在日時(Y-m-d H:i:s) 省略可
	 */
	function getUrlData($url_id = 0, $now = null)
	{
		$this->set_db();

		if (!$now) {
			$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		$param = array($url_id, $now, $now);
		$sql = "SELECT url"
			. " FROM m_url_data"
			. " WHERE url_id = ?"
			. " AND date_start <= ?"
			. " AND ? < date_end"
			. " ORDER BY id DESC"
			. " LIMIT 1";
		//万一同じ期間に同じurl_idのものが重複したらurl_idの大きい方を優先

		return $this->db_m_r->GetRow($sql, $param);
	}

	/**
	 * idに対応したデータを返す
	 *
	 * @param int $id 自動で振られるユニークなID
	 */
	function getUrlDataById($id)
	{
		$this->set_db();

		$param = array($id);
		$sql = "SELECT *"
			. " FROM m_url_data"
			. " WHERE id = ?";

		return $this->db_m_r->GetRow($sql, $param);
	}

	/**
	 * データを全取得
	 */
	function getUrlDataAll()
	{
		$this->set_db();

		$sql = "SELECT *"
			. " FROM m_url_data"
			. " ORDER BY id";

		return $this->db_m_r->GetAll($sql);
	}
	function getUrlDataShowing($now = null)
	{
		$this->set_db();

		if (!$now) {
			$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		$param = array($now);
		$sql = "SELECT *"
			. " FROM m_url_data"
			. " WHERE ? < date_end"
			. " ORDER BY url_id";

		return $this->db_m_r->GetAll($sql, $param);
	}
	function getUrlDataShowed($now = null)
	{
		$this->set_db();

		if (!$now) {
			$now = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
		}
		$param = array($now);
		$sql = "SELECT *"
			. " FROM m_url_data"
			. " WHERE ? > date_end"
			. " ORDER BY url_id";

		return $this->db_m_r->GetAll($sql, $param);
	}

	/**
	 * URLデータをセットする
	 *
	 * @param int $id
	 * @param array $columns セットする情報の連想配列
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function setUrlData($id, $columns)
	{
		$this->set_db();

		$affected_rows = 0;
		// UPDATE実行
		$param = array_values($columns);
		$param[] = $id;
		$sql = "UPDATE m_url_data SET "
			. implode("=?,", array_keys($columns)) . "=? "
			. " WHERE id = ?";
		if (!$this->db_m->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_m->db->ErrorNo(), $this->db_m->db->ErrorMsg(), __FILE__, __LINE__);
		}
		// 影響した行数を確認
		$affected_rows = $this->db_m->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		if ($affected_rows == 0) {
			// INSERT実行
			$param = array($columns['url_id'], $columns['memo'], $columns['url'], $columns['date_start'], $columns['date_end'], $columns['account_reg'], $columns['account_upd'], date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) );
			$sql = "INSERT INTO m_url_data(url_id, memo, url, date_start, date_end, account_reg, account_upd, date_created)"
				. " VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
			if (!$this->db_m->execute($sql, $param)) {
				return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
					$this->db_m->db->ErrorNo(), $this->db_m->db->ErrorMsg(), __FILE__, __LINE__);
			}
		}
		return true;
	}

	/**
	 * URLデータを削除する
	 *
	 * @param int $id
	 * @return bool|object 成功時:true, 失敗時:Ethna_Errorオブジェクト
	 */
	function deleteUrlData($id)
	{
		$this->set_db();

		$param = array($id);
		$sql = "DELETE FROM m_url_data"
			. " WHERE id = ?";

		if (!$this->db_m->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_m->db->ErrorNo(), $this->db_m->db->ErrorMsg(), __FILE__, __LINE__);
		}
		$affected_rows = $this->db_m->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}
		return true;
	}

	/**
	 * お知らせのバナー画像のパスを取得
	 *
	 * サーバのファイルシステム上のパスを取得する。
	 * @param int $content_id 内容ID
	 * @return string パス
	 */
	function getNewsContentBannerPath($content_id)
	{
		$path = BASE . '/data/resource/image/news_content/news_content_banner_' . $content_id . '.png';

		return $path;
	}

	/**
	 * お知らせのアナウンス全文用画像のパスを取得
	 *
	 * サーバのファイルシステム上のパスを取得する。
	 * @param int $content_id 内容ID
	 * @return string パス
	 */
	function getNewsContentPicturePath($content_id)
	{
		$path = BASE . '/data/resource/image/news_content/news_content_picture_' . $content_id . '.png';

		return $path;
	}

	/**
	 * メインバナーのバナー画像のパスを取得
	 *
	 * サーバのファイルシステム上のパスを取得する。
	 * @param int $img_id バナーイメージID
	 * @return string パス
	 */
	function getHomeBannerPath($img_id)
	{
		$path = BASE . '/data/resource/image/hbanner/home_banner_' . $img_id . '.png';

		return $path;
	}

	/** 画像ディレクトリのfilemtimeを取得する */
	function getImageDirMtime()
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
