<?php
/**
 *  Pp_AdminManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp/Ltsv.php';
require_once 'array_column.php';

/**
 *  Pp_AdminManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_AdminManager extends Ethna_AppManager
{
	/** KPI集計種別 価格合計 */
	const KPI_TYPE_SUM_PRICE = 1;

	/** KPI集計種別 ユニークユーザー数 */
	const KPI_TYPE_UU = 2;

	/** KPI集計種別 個数合計 */
	const KPI_TYPE_SUM_NUM = 3;

	/** 期間種別：1時間単位 */
	const DURATION_TYPE_HOURLY  = 1;

	/** 期間種別：1日単位 */
	const DURATION_TYPE_DAILY   = 2;

	/** 期間種別：1ヶ月単位 */
	const DURATION_TYPE_MONTHLY = 3;

	/** HTTPダイジェスト認証のrealm */
	const HTTP_DIGEST_REALM = 'jugmon';

	/** ポータルのドキュメントルートに付加されるディレクトリ */
	const PORTAL_DOC_ROOT_SUFIX_DIR = 'psychopass_game';

	/** ゲームのドキュメントルートに付加されるディレクトリ */
	const GAME_DOC_ROOT_SUFIX_DIR = 'psychopass_portal';

	/**
	 * アクセス制御ロール情報
	 *
	 * @var array $ACCESS_CONTROL_ROLE['ロール識別子'] = ロール名
	 */
	var $ACCESS_CONTROL_ROLE = array(
//		'am' => '責任者',        // admin manager
		'am' => '管理者',        // admin manager
		'ds' => '開発スタッフ',  // development staff
		'dm' => '開発責任者',    // development manager
		'ss' => 'STGスタッフ',   // staging staff
		'sm' => 'STG責任者',     // staging manager
		'ps' => '商用スタッフ',  // production staff
		'pm' => '商用責任者',    // production manager
	);

	/**
	 * アクセス制御パーミッション情報
	 *
	 * @var array $ACCESS_CONTROL_PERMISSION = array(array(
	 *                                            'action' => Ethnaアクション名の正規表現,
	 *                                            'query' => array($_REQUESTのクエリ名 => 正規表現, ...),
	 *                                            'role' => array(環境 => array(許可するロール, ...), ...),
	 *                                            'unit' => 許可するユニット,
	 *                                          ), ...)
	 */
	var $ACCESS_CONTROL_PERMISSION = array(
		array(
			'action' => '/^admin_index$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_program_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_program_deploy_.+$/',
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_api_rsync$/',
			'query' => array('target' => '/^program$/'),
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_api_makuo$/',
			'query' => array('target' => '/^program$/'),
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_api_svn_.+$/',
			'query' => array('target' => '/^program$/'),
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_api_tar_exit$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_api_tar_download$/',
			'query' => array('target' => '/^program$/'),
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_announce_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_api_rsync$/',
			'query' => array('target' => '/^announce$/'),
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_api_makuo$/',
			'query' => array('target' => '/^announce$/'),
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_api_tar_download$/',
			'query' => array('target' => '/^announce$/'),
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_present_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                         'pm'),
			),
		),
		array(
			'action' => '/^admin_account_self_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_account_.+$/',
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',                             ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_developer_index$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_master_index$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_master_list$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_master_download$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_developer_master_upload_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_developer_master_edit$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',                             ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_developer_master_editlog_view$/',
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',                             ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_developer_master_log_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_developer_master_output_.+$/',
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',                             ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_master_sync_.+$/',
			'query' => array('mode' => '/^deploy$/'),
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',                             ),
				'pro' => array('am',                    'ps','pm'),
			),
//			'unit' => 1,
		),
		array(
			'action' => '/^admin_developer_master_sync_.+$/',
			'query' => array('mode' => '/^refresh$/'),
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',                             ),
				'pro' => array('am',                    'ps','pm'),
			),
//			'unit' => 1,
		),
		array(
			'action' => '/^admin_developer_master_sync_.+$/',
			'query' => array('mode' => '/^standby$/'),
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
//			'unit' => 1,
		),
		array(
			'action' => '/^admin_developer_master_sync_.+$/',
			'query' => array('mode' => '/^unitsync$/'),
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
//			'unit' => 1,
		),
		array(
			'action' => '/^admin_developer_master_consistency_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_developer_master_delete_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array(                                  ),
			),
		),
		array(
			'action' => '/^admin_developer_assetbundle_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_assetbundle_deploy_.+$/',
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_api_rsync$/',
			'query' => array('target' => '/^assetbundle$/'),
			'role' => array(
				'dev' => array('am',                             ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_api_makuo$/',
			'query' => array('target' => '/^assetbundle$/'),
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_api_tar_download$/',
			'query' => array('target' => '/^assetbundle$/'),
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_gacha_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_ranking_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                         'pm'),
			),
		),
		array(
			'action' => '/^admin_developer_raid_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_user_index$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_user_view_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_developer_user_ctrl_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',               'sm',         ),
				'pro' => array('am',                         'pm'),
			),
		),
		array(
			'action' => '/^admin_etc_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_kpi_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_operation_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
		array(
			'action' => '/^admin_api_rest$/',
			'query' => array('_table' => '/^m_.+$/'),
			'role' => array(
				'dev' => array('am','ds','dm'                    ),
				'stg' => array('am',                             ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_api_rest$/',
			'query' => array('_table' => '/^t_.+$/'),
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',               'sm',         ),
				'pro' => array('am',                         'pm'),
			),
		),
		array(
			'action' => '/^admin_log_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
        ),
		array(
			'action' => '/^admin_test_data_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                             ),
			),
		),
		array(
			'action' => '/^admin_test_api_.+$/',
			'role' => array(
				'dev' => array('am','ds','dm',                   ),
				'stg' => array('am',          'ss','sm',         ),
				'pro' => array('am',                    'ps','pm'),
			),
		),
	);

	/**
	 * アクセス制御関連のログファイル名
	 *
	 * フルパス
	 * コンストラクタでセットされる
	 */
	var $ACCESS_CONTROL_FILENAME = null;

	/**
	 * 管理画面操作ログに対応するテーブル名
	 *
	 * このクラスの function addAdminOperationLog も参照のこと。
	 * @var array  キーがディレクトリ（BASE/log下の部分）とファイル名のbasenameをつなげたパス、値がテーブル名の連想配列
	 */
	var $ADMIN_OPERATION_LOG_TABLE = array(
		'/announce/event_news/content_log'   => 'm_event_news_content',
		'/announce/home/banner_log'          => 'm_home_banner',
		'/announce/message/dialog_log'       => 'm_dialog_message',
		'/announce/dialog/message_log'       => 'm_dialog_message',
		'/announce/message/error_log'        => 'm_error_message',
		'/announce/message/help_log'         => 'm_help_message',
		'/announce/message/helpbar_log'      => 'm_helpbar_message',
		'/announce/message/tips_log'         => 'm_tip_message',
		'/announce/news/content_log'         => 'm_news_content',
		'/developer/assetbundle/bgmodel_log' => 'm_asset_bundle',
		'/developer/assetbundle/effect_log'  => 'm_asset_bundle',
		'/developer/assetbundle/map_log'     => 'm_asset_bundle',
		'/developer/assetbundle/monster_log' => 'm_asset_bundle',
		'/developer/assetbundle/sound_log'   => 'm_asset_bundle',
		'/developer/assetbundle/version_log' => 'm_res_ver',
		'/developer/gacha/banner_log'        => 'm_gacha_list',
		'/developer/gacha/category_log'      => 'm_gacha_category',
		'/developer/gacha/item_log'          => 'm_gacha_itemlist',
	);

	/**
	 * ディレクトリ情報
	 *
	 * 管理画面でrsyncやmakuoを実行する際の処理対象
	 */
	var $DIRECTORIES = array(
		'program'     => array('app', 'lib', 'schema', 'template', 'www'),
		'assetbundle' => array('data/resource/assetbundle/'),
		'announce'    => array('data/resource/image/'),
	);
	var $BASE_DIRECTORIES = array(
		'announce'    => 'ethna/main',
	);


	/**
	 * DB接続(pp-ini.phpの'dsn_cmn'で定義したDB)
	 *
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn = null;

	/**
	 * DB接続(pp-ini.phpの'dsn_cmn_r'で定義したDB)
	 *
	 * コンストラクタでは生成されないので、明示的にgetDBしてから使用すること
	 */
	protected $db_cmn_r = null;

	/**
	 * DB接続(pp-ini.phpの'dsn_log_r'で定義したDB)
	 */
	protected $db_log_r = null;

	/**
	 * 管理画面があるドキュメントルート
	 *
	 * 管理画面からCLIを呼び出す際などの受け渡し用
	 */
	protected $admin_document_root = null;

	function __construct(&$backend) {
		parent::__construct($backend);

		$this->ACCESS_CONTROL_FILENAME = BASE . '/log/account/pp-account.log';
	}

	/**
	 * セッション内でクエリキャッシュを無効化する
	 *
	 * @see http://dev.mysql.com/doc/refman/5.1/ja/query-cache-configuration.html
	 */
	function offSessionQueryCache()
	{
		foreach (array('db', 'db_r') as $db) {
//$all = $this->$db->getAll( "SHOW VARIABLES LIKE '%query_cache%'" );
//echo 'DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($all, true) ."\n";
			$this->$db->query("SET SESSION query_cache_type = OFF");
//$all = $this->$db->getAll( "SHOW VARIABLES LIKE '%query_cache%'" );
//echo 'DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($all, true) ."\n";
		}
	}

	/**
	 * セッション内でSQL_BIG_SELECTSを有効化する
	 */
	function setSessionSqlBigSelectsOn($db_keys = null)
	{
		if ($db_keys === null) {
			$db_keys = array('logex', 'logex_r');
		}

		foreach ($db_keys as $db_key) {
			$db = $this->backend->getDB($db_key);
//$all = $db->getAll("SHOW VARIABLES LIKE 'sql_big_selects'");
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($all, true));
			$db->query("SET SESSION sql_big_selects = ON");
//$all = $db->getAll("SHOW VARIABLES LIKE 'sql_big_selects'");
//error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($all, true));
		}
	}

	/**
	 * ADODB_COUNTRECSをセットする
	 *
	 * @see http://phplens.com/adodb/reference.varibles.adodb_countrecs.html
	 * @global type $ADODB_COUNTRECS
	 * @param bool 新しいADODB_COUNTRECS値
	 * @return bool 元のADODB_COUNTRECS値
	 */
	function setAdodbCountrecs($value)
	{
		global $ADODB_COUNTRECS;

		$old = $ADODB_COUNTRECS;
		$ADODB_COUNTRECS = $value;

		return $old;
	}

	/**
	 * 管理画面ユーザーを生成する
	 *
	 * @param string $user ユーザー名
	 * @param string $password パスワード
	 * @param string $role アクセス制御ロール
	 * @return boolean|Ethna_Error 成否
	 */
	function createAdminUser($user, $password, $role)
	{
		if (!$this->db_cmn) {
			$this->db_cmn =& $this->backend->getDB('cmn');
		}

		$hash = $this->hashAdminPassword($user, $password);
		$digest = $this->hashAdminDigestA1($user, $password);

		$param = array($user, $hash, $digest, $role);
		$sql = "INSERT INTO ct_adm_user(user, password_hash, digest_a1, role, date_created, date_modified)"
		     . " VALUES(?, ?, ?, ?, NOW(), NOW())";
		if (!$this->db_cmn->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}

		return true;
	}

	/**
	 * 管理画面ユーザーパスワードを更新する
	 *
	 * @param string $user ユーザー名
	 * @param string $password パスワード
	 * @return boolean|Ethna_Error 成否
	 */
	function updateAdminPassword($user, $password)
	{
		if (!$this->db_cmn) {
			$this->db_cmn =& $this->backend->getDB('cmn');
		}

		$hash = $this->hashAdminPassword($user, $password);
		$digest = $this->hashAdminDigestA1($user, $password);

		$param = array($hash, $digest, $user);
		$sql = "UPDATE ct_adm_user SET password_hash = ?, digest_a1 = ? WHERE user = ?";
		if (!$this->db_cmn->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$affected_rows = $this->db_cmn->db->affected_rows();
		if ($affected_rows > 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		return true;
	}

	/**
	 * 管理画面ユーザへの操作をログに残す
	 *
	 * @param string $operator_user 操作を行ったアカウント名
	 * @param string $target_user 追加、削除、変更されたアカウント名
	 * @param string $type 操作の種別("create" or "update" or "delete")
	 * @param string $note 備考
	 * @return void
	 */
	function logAdminUserOperation($operator_user, $target_user, $type, $note = null)
	{
		$types = array(
			'create' => '追加',
			'update' => '変更',
			'delete' => '削除',
		);

		assert(isset($types[$type]));

		$header = 'yyyy年mm月dd日 hh:mm:ss [操作を行ったアカウント名] [追加、削除、変更されたアカウント名] [された内容(追加/削除/変更)] [備考]';

		$message = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'])
		         . " [{$operator_user}] [{$target_user}] [" . $types[$type] . "]"
		         . ((strlen($note) > 0) ? " [$note]" : "")  . "\n";

		$dir = dirname($this->ACCESS_CONTROL_FILENAME);
		is_dir($dir) || mkdir($dir);

		$file = $this->ACCESS_CONTROL_FILENAME;
		if (!is_file($file)) {
			$message = $header . "\n" . $message;
		}

		if (file_put_contents($file, $message, FILE_APPEND | LOCK_EX) === false) {
			$this->backend->logger->log(LOG_ERR,
					'Logging failed. file=[%s] message=[%s]', $file, $message);
		}
	}

	/**
	 * 管理画面ユーザーパスワードが正しいか判定する
	 *
	 * @param string $user ユーザー名
	 * @param string $password パスワード
	 * @return boolean 正誤
	 */
	function isValidAdminPassword($user, $password)
	{
		if (!$this->db_cmn_r) {
			$this->db_cmn_r =& $this->backend->getDB('cmn_r');
		}

		$hash = $this->db_cmn_r->GetOne(
			"SELECT password_hash FROM ct_adm_user WHERE user = ?",
			array($user)
		);

		if (!$hash || Ethna::isError($hash)) {
			return false;
		}

		if ($hash != $this->hashAdminPassword($user, $password)) {
			return false;
		}

		return true;
	}

	/**
	 * 有効な管理画面HTTPダイジェスト認証A1ハッシュ値を取得する
	 *
	 * @param string $user ユーザー名
	 * @return string HTTPダイジェスト認証A1ハッシュ値
	 */
/*
	function getValidAdminDigestA1($user)
	{
		if (!$this->db_cmn_r) {
			$this->db_cmn_r =& $this->backend->getDB('cmn_r');
		}

		return $this->db_cmn_r->GetOne(
			"SELECT digest_a1 FROM adm_user WHERE user = ?",
			array($user)
		);
	}
*/

	/**
	 * HTTP Digest認証で用いるA1ハッシュ値を計算する
	 *
	 * @see http://jp2.php.net/manual/ja/features.http-auth.php
	 * @param string $username
	 * @param string $password
	 * @param string $realm
	 * @return string
	 */
	function hashAdminDigestA1($username, $password, $realm = null)
	{
		if ($realm === null) {
			$realm = self::HTTP_DIGEST_REALM;
		}

		return md5($username . ':' . $realm . ':' . $password);
	}

	/**
	 * 認証失敗カウンタへのアクセサ
	 *
	 * @param string $user ユーザー名
	 * @param int $add カウンタ増分値（省略可）
	 * @return int カウンタ値
	 */
	function accessAuthFailCnt($user, $add = 0)
	{
		$cache =& Ethna_CacheManager::getInstance('memcache');
		$cache_key = $user . '_admin_auth_fail_cnt';
		$fail_cnt = $cache->get($cache_key, 30); // 30はlifetime
		if (Ethna::isError($fail_cnt)) {
			$fail_cnt = 0; // OK
		}

		if ($add) {
			$fail_cnt += $add;
			$cache->set($cache_key, $fail_cnt);
		}

		return $fail_cnt;
	}

	/**
	 * 管理画面パスワードをハッシュする
	 *
	 * @param string $user ユーザー名（Saltの一部として使用する）
	 * @param string $password パスワード
	 * @return string ハッシュ文字列
	 */
	protected function hashAdminPassword($user, $password)
	{
		// Saltの内、アプリケーションサーバ内にだけ保持する部分
		$salt_app = 'dQRfx8dc';

		$salt = $salt_app . $user;
		$hash = sha1($password . $salt);

		return $hash;
	}

	/**
	 * 管理画面ユーザー一覧を取得する
	 *
	 * @return array 管理画面ユーザー一覧
	 */
	function getAdminUserList()
	{
		if (!$this->db_cmn_r) {
			$this->db_cmn_r =& $this->backend->getDB('cmn_r');
		}

		return $this->db_cmn_r->GetAll("SELECT user, role FROM ct_adm_user");
	}

	/**
	 * 管理画面ユーザーを取得する
	 *
	 * @param string $user ユーザー名
	 * @return array 管理画面ユーザー
	 */
	function getAdminUser($user)
	{
		# TODO ログイン機能を停止、暫定処理
		return array('user' => $user, 'role' => 'am');
		/*
		if (!$this->db_cmn_r) {
			$this->db_cmn_r =& $this->backend->getDB('cmn_r');
		}

		$param = array($user);
		$sql = "SELECT user, role FROM adm_user WHERE user = ?";

		return $this->db_cmn_r->GetRow($sql, $param);
		*/
	}

	/**
	 * 管理画面ユーザー情報をセットする
	 *
	 * @param string $user ユーザー名
	 * @param array $columns セットする情報の連想配列
	 * @return bool 成否
	 */
	function setAdminUser($user, $columns)
	{
		if (!$this->db_cmn) {
			$this->db_cmn =& $this->backend->getDB('cmn');
		}

		$where = "user = '$user'";
		$ret = $this->db_cmn->db->AutoExecute('ct_adm_user', $columns, 'UPDATE', $where);
		if (!$ret) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		if ($this->db_cmn->db->affected_rows() > 1) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			return false;
		}

		return true;
	}

	/**
	 * 管理画面ユーザーを削除する
	 *
	 * @param string $user ユーザー名
	 * @return boolean|Ethna_Error 成否
	 */
	function deleteAdminUser($user)
	{
		if (!$this->db_cmn) {
			$this->db_cmn =& $this->backend->getDB('cmn');
		}

		$param = array($user);
		$sql = "DELETE FROM ct_adm_user WHERE user = ?";
		if (!$this->db_cmn->execute($sql, $param)) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$affected_rows = $this->db_cmn->db->affected_rows();
		if ($affected_rows != 1) {
			return Ethna::raiseError("rows[%d]", E_USER_ERROR, $affected_rows);
		}

		return true;
	}

	/**
	 * アクセス制御パーミッションを所持しているか
	 *
	 * @param string $role アクセス制御ロール
	 * @param string $action_name Ethnaアクション名
	 * @param string $env 環境("dev" or "stg" or "pro")
	 * @param array  $queries 判定に使用する$_REQUESTクエリ
	 * @param int    $unit ユニット番号
	 * @return boolean 有無
	 */
	function hasAccessControlPermission($role, $action_name, $env, $queries = null, $unit = null)
	{
		foreach ($this->ACCESS_CONTROL_PERMISSION as $permission) {
			if (!preg_match($permission['action'], $action_name)) {
				continue;
			}

			if(!in_array($role, $permission['role'][$env])) {
				continue;
			}

			if (isset($permission['query'])) {
				$ok = true;
				foreach ($permission['query'] as $name => $pattern) {
					if (!$queries || !isset($queries[$name]) || !preg_match($pattern, $queries[$name])) {
						$ok = false;
						break;
					}
				}

				if (!$ok) {
					continue;
				}
			}

			if (isset($permission['unit'])) {
				if ($permission['unit'] != $unit) {
					continue;
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * アクセス制御に使用する可能性があるアクションフォームの全クエリ名を取得する
	 *
	 * @return array クエリ名の配列
	 */
	function getAccessControlQueryNameAll()
	{
		$names = array();

		foreach ($this->ACCESS_CONTROL_PERMISSION as $permission) {
			if (isset($permission['query'])) {
				$names = array_merge($names, array_keys($permission['query']));
			}
		}

		return $names;
	}

	/**
	 * 管理画面操作ログが書き込み可能かどうかを調べる
	 *
	 * @param string $dirname ディレクトリ（BASE/log下の部分。先頭の/は付ける。末尾の/は付けない）
	 * @param string $basename ファイル名のbasename
	 * @return bool 書き込み可能か
	 */
	function isAdminOperationLogWritable($dirname, $basename)
	{
		$base_len = strlen(BASE);
		$path = $this->getLtsvLogFilename($dirname, $basename);

		$cnt = 0;
		while (1) {
			if ($cnt > 999) {
				// ここに来る事はないはずだが、念のため無限ループ回避対応
				error_log('ERROR:' . __FILE__ . ':' . __LINE__);
				return false;
			}

			if (strlen($path) <= $base_len) {
				return false;
			}

			if (file_exists($path)) {
				return is_writable($path);
			}

			$path = dirname($path);
			$cnt++;
		}
	}

	/**
	 * 管理画面操作ログに追記する
	 *
	 * このクラスの var $OPERATION_LOG_TABLE も参照のこと。
	 * @param string $dirname ディレクトリ（BASE/log下の部分。先頭の/は付ける。末尾の/は付けない）
	 * @param string $basename ファイル名のbasename
	 * @param array $columns ログ内容の連想配列（LTSVラベル名 => 内容）
	 * @param bool $db_flg DBにテーブルごとの更新者情報を記録するか
	 * @return int|false file_put_contents結果（ファイルに書き込まれたバイト数、あるいは失敗した場合にはFALSE）
	 */
	function addAdminOperationLog($dirname, $basename, $columns, $db_flg = true)
	{
		// 引数チェック
		if (!preg_match('/^[a-zA-Z0-9_-]+$/', $basename)) {
			error_log('ERROR:' . __FILE__ . ':' . __LINE__);
			$this->backend->logger->log(LOG_ERR, 'Logging failed.');
			return false;
		}

		// 出力内容を生成する
		// 現在日時は必ず先頭に
		$map = array(
			'time' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']),
		);

		// 'user'は現在日時の次に
		$labels_order = array('user');
		foreach ($labels_order as $label) {
			if (isset($columns[$label])) {
				$map[$label] = $columns[$label];
			}
		}

		// その他ラベルは、引数で渡された順に
		foreach ($columns as $label => $value) {
			if (!in_array($label, $labels_order)) {
				$map[$label] = $value;
			}
		}

		// LTSV生成
		$ltsv = Pp_Ltsv::encode($map);
		$contents = $ltsv . "\n";

		// ファイル出力
		$filename = $this->getLtsvLogFilename($dirname, $basename);
		$pathname = dirname($filename);
		is_dir($pathname) || mkdir($pathname, 0755, true);
		$ret = file_put_contents($filename, $contents, FILE_APPEND | LOCK_EX);

		// DBにテーブルごとの更新者情報を記録する
		if ($db_flg) {
			$table = $this->getAdminOperationLogTable($dirname, $basename);

			if ($table) {
				$db_columns = array(
					'table_name'  => $table,
				);

				if (isset($columns['user'])) {
					$db_columns['account_reg'] = $columns['user'];
				}

				if (isset($columns['action'])) {
					$db_columns['action'] = $columns['action'];
				}

				$this->backend->getManager('Developer')->logMasterModify($db_columns);
			}
		}

		return $ret;
	}

	/**
	 * 管理画面操作ログのパスからDBのテーブル名を取得する
	 *
	 * @param string $dirname ディレクトリ（BASE/log下の部分。先頭の/は付ける。末尾の/は付けない）
	 * @param string $basename ファイル名のbasename
	 * @return string テーブル名
	 */
	protected function getAdminOperationLogTable($dirname, $basename)
	{
		$key = $dirname . '/' . $basename;
		if (isset($this->ADMIN_OPERATION_LOG_TABLE[$key])) {
			return $this->ADMIN_OPERATION_LOG_TABLE[$key];
		}
	}

	/**
	 * 管理画面操作ログを逆順で取得する
	 *
	 * @param string $dirname ディレクトリ（BASE/log下の部分。先頭の/は付ける。末尾の/は付けない）
	 * @param string $basename ファイル名のbasename
	 * @param int $number_of_lines 行数
	 * @param bool $decode LTSVデコードするか
	 * @return array|false ログ（行の配列）　エラー時はfalse
	 */
	function getAdminOperationLogReverse($dirname, $basename, $number_of_lines, $decode = true)
	{
		$filename = $this->getLtsvLogFilename($dirname, $basename);

		$command = "tail -n {$number_of_lines} $filename";
		exec($command, $output, $return_var);
		if ($return_var) {
			return false;
		}

		$lines = array_reverse($output);
		if ($decode) {
			return Pp_Ltsv::decode($lines);
		} else {
			return $lines;
		}
	}

	/**
	 * LTSVログファイル名（フルパス）を取得する
	 *
	 * @param string $dirname ディレクトリ（BASE/log下の部分。先頭の/は付ける。末尾の/は付けない）
	 * @param string $basename ファイル名のbasename
	 * @return string|boolean ファイル名（フルパス）　エラーの場合はfalse
	 */
	function getLtsvLogFilename($dirname, $basename)
	{
		if (!preg_match('/^\/[a-zA-Z0-9-_\/]*[^\/]$/', $dirname)) {
			return false;
		}

		if (!preg_match('/^[a-zA-Z0-9-_]+$/', $basename)) {
			return false;
		}

//		$filename = BASE . '/log' . $dirname . '/' . $basename . '.ltsv';
		$filename = $this->backend->ctl->getDirectory('log')
		          . $dirname . '/' . $basename . '.ltsv';

		return $filename;
	}

	/**
	 * 集計対象期間の開始時点を求める
	 *
	 * @param int $type 期間種別(const DURATION_TYPE_～)
	 * @param int $time 期間内のUNIXタイプスタンプ
	 * @return string 日時(Y-m-d H:i:s形式)
	 * @throws Exception
	 */
	function getCurrentDurationDate($type, $time)
	{
		switch ($type) {
			case self::DURATION_TYPE_HOURLY:
				$date = date('Y-m-d H', $time) . ':00:00';
				break;

			case self::DURATION_TYPE_DAILY:
				$date = date('Y-m-d', $time) . ' 00:00:00';
				break;

			case self::DURATION_TYPE_MONTHLY:
				$date = date('Y-m', $time) . '-01 00:00:00';
				break;

			default:
				throw new Exception('Invalid type.');
		}

		return $date;
	}

	/**
	 * 1つ前の集計対象期間の開始時点タイムスタンプを求める
	 *
	 * @param int $type 期間種別(const DURATION_TYPE_～)
	 * @param int $time 期間内のUNIXタイプスタンプ
	 * @return string 日時(Y-m-d H:i:s形式)
	 * @throws Exception
	 */
	function getPreviousDurationDate($type, $time)
	{
		switch ($type) {
			case self::DURATION_TYPE_HOURLY:
				$date = date('Y-m-d H', $time - 3600) . ':00:00';
				break;

			case self::DURATION_TYPE_DAILY:
				$date = date('Y-m-d', $time - 86400) . ' 00:00:00';
				break;

			case self::DURATION_TYPE_MONTHLY:
				$time_tmp = strtotime(date('Y-m', $time) . '-01 00:00:00');
				$date = date('Y-m', $time_tmp - 1) . '-01 00:00:00';
				break;

			default:
				throw new Exception('Invalid type.');
		}

		return $date;
	}

	/**
	 * 1つ後の集計対象期間の開始時点タイムスタンプを求める
	 *
	 * @param int $type 期間種別(const DURATION_TYPE_～)
	 * @param int $time 期間内のUNIXタイプスタンプ
	 * @return string 日時(Y-m-d H:i:s形式)
	 * @throws Exception
	 */
	function getNextDurationDate($type, $time)
	{
		switch ($type) {
			case self::DURATION_TYPE_HOURLY:
				$date = date('Y-m-d H', $time + 3600) . ':00:00';
				break;

			case self::DURATION_TYPE_DAILY:
				$date = date('Y-m-d', $time + 86400) . ' 00:00:00';
				break;

			case self::DURATION_TYPE_MONTHLY:
				$time_tmp = strtotime(date('Y-m', $time) . '-01 00:00:00');
				$date = date('Y-m', $time_tmp + 86400 * 31) . '-01 00:00:00';
				break;

			default:
				throw new Exception('Invalid type.');
		}

		return $date;
	}

	/**
	 * ショップ関連のKPI集計を行う
	 *
	 * ※$duration_typeが
	 * @param string $date_use_start 集計対象とする消費日時の期間の始点(Y-m-d H:i:s形式)
	 * @param int $duration_type 集計対象とする消費日時の期間の期間種別(const DURATION_TYPE_～の値)
	 */
/*
	function makeKpiUserShop($duration_type, $date_use_start)
	{
		$date_use_end = $this->getNextDurationDate($duration_type, strtotime($date_use_start));

		$param = array($date_use_start, $date_use_end);
		$from_where_clause = " FROM log_user_shop l, t_user_base b"
						   . " WHERE l.user_id = b.user_id"
						   . " AND ? <= l.date_use AND l.date_use < ?";

		foreach (array(
			self::KPI_TYPE_SUM_PRICE => "SUM(l.price)",
			self::KPI_TYPE_UU => "COUNT(DISTINCT(l.user_id))",
			self::KPI_TYPE_SUM_NUM => "SUM(l.num)",
		) AS $kpi_type => $select_tmp) {
			$select_func_clause = $select_tmp . " AS kpi_value";

			$sql_list = array();
			$sql_list[] = " SELECT {$select_func_clause}, b.app_id, l.shop_id, l.item_id, l.num, l.price"
						. $from_where_clause
						. " GROUP BY b.app_id, l.shop_id, l.item_id, l.num, l.price";
			$sql_list[] = " SELECT {$select_func_clause}, b.app_id, l.shop_id, l.item_id, 0 AS num, 0 AS price"
						. $from_where_clause
						. " GROUP BY b.app_id, l.shop_id, l.item_id";

			// item_id問わずの値も求める
			// ただし個数はitem_id問わずの合計値を求めても意味ないので、個数以外について処理する
			if ($kpi_type != self::KPI_TYPE_SUM_NUM) {
				$sql_list[] = " SELECT {$select_func_clause}, b.app_id, 0 AS shop_id, 0 AS item_id, 0 AS num, 0 AS price"
							. $from_where_clause
							. " GROUP BY b.app_id";
			}

			foreach ($sql_list as $sql) {
				$result =& $this->db_r->query($sql, $param);
				if (Ethna::isError($result)) {
					throw new Exception($this->db->db->ErrorMsg());
				}

				while ($row = $result->FetchRow()) {
					$param2 = array_values($row);
					$param2[] = $kpi_type;
					$param2[] = $date_use_start;
					$param2[] = $duration_type;
					$sql2 = "INSERT INTO kpi_user_shop(" . implode(",", array_keys($row))
					      . ", kpi_type, date_use_start, duration_type, date_created)"
						  . " VALUES(" . str_repeat("?,", count($row)) . "?,?,?,NOW())";
					if (!$this->db->execute($sql2, $param2)) {
						throw new Exception($this->db->db->ErrorMsg());
					}
				}
			}
		}
	}
*/

	/**
	 * KPI素材一時データ（ユーザごとのノーマルクエストのクリア数）を作成する
	 *
	 * @param string $date 集計対象日（Y-m-d形式。昨日または昨日以前にすること）
	 * @throws Exception
	 */
	function makeTmpKpiMaterialNormalQuestPerUser($date)
	{
		$quest_m = $this->backend->getManager('Quest');

		$date_end = date('Y-m-d H:i:s', strtotime($date. ' 00:00:00') + 86400);

		$area_assoc = $quest_m->getMasterAreaAssoc(Pp_QuestManager::QUEST_TYPE_NORMAL);
		$area_id_joined = implode(',', array_keys($area_assoc));
		$param = array($date_end, Pp_QuestManager::QUEST_STATUS_CLEAR);
		$sql = <<<EOD
SELECT user_id, COUNT(*) AS cnt
FROM t_user_area
WHERE date_modified < ?
AND status = ?
AND area_id IN ({$area_id_joined})
EOD;
		$result =& $this->db_r->query($sql, $param);
		if (Ethna::isError($result)) {
			throw new Exception($this->db->db->ErrorMsg());
		}

		while ($row = $result->FetchRow()) {
			$this->addTmpKpiMaterialData('normal_quest_per_user', $date, $row['cnt'], $row['user_id']);
		}
	}

	/**
	 * KPI素材一時データ（ユーザごとの売上）を作成する
	 *
	 * @param string $date 集計対象日（Y-m-d形式。昨日または昨日以前にすること）
	 * @throws Exception
	 */
/*
	function makeTmpKpiMaterialShopUsePerUser($date)
	{
		$date_start = $date . ' 00:00:00';
		$date_end = date('Y-m-d H:i:s', strtotime($date_start) + 86400);

		$param = array($date_start, $date_end);
		$sql = <<<EOD
SELECT user_id, SUM(price) AS price
FROM log_user_shop
WHERE date_use >= ?
AND date_use < ?
GROUP BY user_id
EOD;
		$result =& $this->db_r->query($sql, $param);
		if (Ethna::isError($result)) {
			throw new Exception($this->db->db->ErrorMsg());
		}

		while ($row = $result->FetchRow()) {
			$this->addTmpKpiMaterialData('shop_use_per_user', $date, $row['price'], $row['user_id']);
		}
	}
*/

	/**
	 * クエスト分布集計を作成する
	 *
	 * 達成ノーマルクエスト数ごとのKPI値
	 * t_user_baseの情報は引数で指定された日付と関係なく現在の値を使用するので注意
	 * @param string $date 集計対象日（Y-m-d形式。昨日または昨日以前にすること）
	 * @param int $arppu_min 集計対象ARPPU最小値（端点含む）
	 * @param int $arppu_max 集計対象ARPPU最大値（端点含まない）
	 * @param int $ua Uger-Agent種別（const Pp_UserManager::OS_～）
	 */
	function makeKpiNormalQuest($date, $arppu_min, $arppu_max, $ua = null)
	{
		$kpi_assoc = array(); // $kpi_assoc[達成ノーマルクエスト数][カラム名] = 値

		// 人数, プレイヤーランク（平均）を求める
		// ※達成ノーマルクエスト数は午前0時時点、人数とプレイヤーランクは午前2時付近の時点の値
		$param = array($date, $date, $arppu_min, $arppu_max);
		$sql = " SELECT m1.statistic, COUNT(*) AS cnt, FLOOR(AVG(b.rank)) AS rank"
		     . " FROM t_user_base b"
		     . "   LEFT OUTER JOIN tmp_kpi_material m1"
		     . "     ON b.user_id = m1.target"
		     . "     AND m1.type = 'normal_quest_per_user'"
		     . "     AND m1.date_action = ?"
		     . "   LEFT OUTER JOIN tmp_kpi_material m2"
		     . "     ON b.user_id = m2.target"
		     . "     AND m2.type = 'shop_use_per_user'"
		     . "     AND m2.date_action = ?";

		if ($arppu_min == 0) {
			$sql .= " WHERE (m2.statistic IS NULL OR (m2.statistic >= ? AND m2.statistic < ?))";
		} else {
			$sql .= " WHERE m2.statistic >= ? AND m2.statistic < ?";
		}

		if ($ua) {
			$param[] = $ua;
			$sql .= " AND b.ua = ?";
		}

		$sql .= " GROUP BY m1.statistic";

		foreach ($this->db_r->getAll($sql, $param) as $row) {
			$kpi_assoc[$row['statistic']]['user_num'] = $row['cnt'];
			$kpi_assoc[$row['statistic']]['rank_avg'] = $row['rank'];
		}

		// 脱落数を求める
		// ※脱落数：離脱プレイヤー数の表示。そのクエスト帯で過去１週間以上ログインしていないＩＤ数を抽出。
		$threshold = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - 86400 * 7);
		$param = array($date, $date, $arppu_min, $arppu_max);
		$sql = " SELECT m1.statistic, COUNT(*) AS cnt"
		     . " FROM t_user_base b"
		     . "   LEFT OUTER JOIN tmp_kpi_material m1"
		     . "     ON b.user_id = m1.target"
		     . "     AND m1.type = 'normal_quest_per_user'"
		     . "     AND m1.date_action = ?"
		     . "   LEFT OUTER JOIN tmp_kpi_material m2"
		     . "     ON b.user_id = m2.target"
		     . "     AND m2.type = 'shop_use_per_user'"
		     . "     AND m2.date_action = ?";

		if ($arppu_min == 0) {
			$sql .= " WHERE (m2.statistic IS NULL OR (m2.statistic >= ? AND m2.statistic < ?))";
		} else {
			$sql .= " WHERE m2.statistic >= ? AND m2.statistic < ?";
		}

		if ($ua) {
			$param[] = $ua;
			$sql .= " AND b.ua = ?";
		}

		$param[] = $threshold;
		$sql .= " AND b.login_date < ?"
             . " GROUP BY m1.statistic";

		foreach ($this->db_r->getAll($sql, $param) as $row) {
			$kpi_assoc[$row['statistic']]['escape_num'] = $row['cnt'];
		}

		// 使用合成素材数（合計とユーザー数）を求める
		$param = array($date, $date, $arppu_min, $arppu_max);
		$sql = " SELECT b.user_id, m1.statistic"
		     . " FROM t_user_base b"
		     . "   LEFT OUTER JOIN tmp_kpi_material m1"
		     . "     ON b.user_id = m1.target"
		     . "     AND m1.type = 'normal_quest_per_user'"
		     . "     AND m1.date_action = ?"
		     . "   LEFT OUTER JOIN tmp_kpi_material m2"
		     . "     ON b.user_id = m2.target"
		     . "     AND m2.type = 'shop_use_per_user'"
		     . "     AND m2.date_action = ?";

		if ($arppu_min == 0) {
			$sql .= " WHERE (m2.statistic IS NULL OR (m2.statistic >= ? AND m2.statistic < ?))";
		} else {
			$sql .= " WHERE m2.statistic >= ? AND m2.statistic < ?";
		}

		if ($ua) {
			$param[] = $ua;
			$sql .= " AND b.ua = ?";
		}

		$material_total = 0;
		$material_user_num = 0;
		foreach ($this->db_r->getAll($sql, $param) as $row) {
			$num = $this->getDailySynthesisMaterialNumPerUser($row['user_id'], $date);

			foreach (array('material_total', 'material_user_num') as $key) {
				if (!isset($kpi_assoc[$row['statistic']][$key])) {
					$kpi_assoc[$row['statistic']][$key] = 0;
				}
			}

			$kpi_assoc[$row['statistic']]['material_total'] += $num;
			$kpi_assoc[$row['statistic']]['material_user_num'] += 1;
		}

		foreach ($kpi_assoc as $clear_num => $columns) {
			// 使用合成素材数（平均）を求める
			if ($columns['material_user_num'] != 0) {
				$material_avg = floor($columns['material_total'] / $columns['material_user_num']);
			} else {
				$material_avg = null;
			}

			// 脱落数は存在しない場合もある
			if (!isset($columns['escape_num'])) {
				$escape_num = 0;
			} else {
				$escape_num = $columns['escape_num'];
			}

			// 記録する
			$this->addKpiNormalQuest($date, $arppu_min, $arppu_max, $ua, $clear_num, $columns['user_num'], $columns['rank_avg'], $escape_num, $material_avg);
		}
	}

	/**
	 * クエスト分布KPI集計データをDBに記録する
	 */
	protected function addKpiNormalQuest($date_kpi, $arppu_min, $arppu_max, $ua, $clear_num, $user_num, $rank_avg, $escape_num, $synthesis_material_avg)
	{
		$param = array($date_kpi, $arppu_min, $arppu_max, $ua, $clear_num, $user_num, $rank_avg, $escape_num, $synthesis_material_avg);
		$sql = "INSERT INTO kpi_normal_quest(date_kpi, arppu_min, arppu_max, ua, clear_num, user_num, rank_avg, escape_num, synthesis_material_avg)"
		     . " VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)";
		if (!$this->db->execute($sql, $param)) {
			throw new Exception($this->db->db->ErrorMsg());
		}

		return true;
	}

	/**
	 * ランク帯分布集計を作成する
	 *
	 * プレイヤーランクごとのKPI値
	 * t_user_baseの情報は引数で指定された日付と関係なく現在の値を使用するので注意
	 * @param string $date 集計対象日（Y-m-d形式。昨日または昨日以前にすること）
	 * @param int $arppu_min 集計対象ARPPU最小値（端点含む）
	 * @param int $arppu_max 集計対象ARPPU最大値（端点含まない）
	 * @param int $ua Uger-Agent種別（const Pp_UserManager::OS_～）
	 */
	function makeKpiRank($date, $arppu_min, $arppu_max, $ua = null)
	{
		$kpi_assoc = array(); // $kpi_assoc[ランク][カラム名] = 値

		// プライヤーランク, 人数を求める
		$param = array($date, $arppu_min, $arppu_max);
		$sql = " SELECT b.rank, COUNT(*) AS cnt"
		     . " FROM t_user_base b"
		     . "   LEFT OUTER JOIN tmp_kpi_material m2"
		     . "     ON b.user_id = m2.target"
		     . "     AND m2.type = 'shop_use_per_user'"
		     . "     AND m2.date_action = ?";

		if ($arppu_min == 0) {
			$sql .= " WHERE (m2.statistic IS NULL OR (m2.statistic >= ? AND m2.statistic < ?))";
		} else {
			$sql .= " WHERE m2.statistic >= ? AND m2.statistic < ?";
		}

		if ($ua) {
			$param[] = $ua;
			$sql .= " AND b.ua = ?";
		}

		$sql .= " GROUP BY b.rank";

		foreach ($this->db_r->getAll($sql, $param) as $row) {
			$kpi_assoc[$row['rank']]['user_num'] = $row['cnt'];
		}

		// プライヤーランク, レベル（平均）, 攻撃力補正値（平均）, ヒットポイント補正値（平均）を求める
		$param = array($date, $arppu_min, $arppu_max);
		$sql = " SELECT b.rank, FLOOR(AVG(m.lv)) AS lv, FLOOR(AVG(m.attack_plus)) AS attack_plus, FLOOR(AVG(m.hp_plus)) AS hp_plus"
		     . " FROM t_user_base b"
		     . "   INNER JOIN t_user_monster m"
		     . "     ON b.user_id = m.user_id"
		     . "   LEFT OUTER JOIN tmp_kpi_material m2"
		     . "     ON b.user_id = m2.target"
		     . "     AND m2.type = 'shop_use_per_user'"
		     . "     AND m2.date_action = ?";

		if ($arppu_min == 0) {
			$sql .= " WHERE (m2.statistic IS NULL OR (m2.statistic >= ? AND m2.statistic < ?))";
		} else {
			$sql .= " WHERE m2.statistic >= ? AND m2.statistic < ?";
		}

		if ($ua) {
			$param[] = $ua;
			$sql .= " AND b.ua = ?";
		}

		$sql .= " GROUP BY b.rank";

		foreach ($this->db_r->getAll($sql, $param) as $row) {
			$kpi_assoc[$row['rank']]['lv_avg']          = $row['lv'];
			$kpi_assoc[$row['rank']]['attack_plus_avg'] = $row['attack_plus'];
			$kpi_assoc[$row['rank']]['hp_plus_avg']     = $row['hp_plus'];
		}

		// 脱落数を求める
		// ※脱落数：離脱プレイヤー数の表示。そのクエスト帯で過去１週間以上ログインしていないＩＤ数を抽出。
		$threshold = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] - 86400 * 7);
		$param = array($date, $arppu_min, $arppu_max);
		$sql = " SELECT b.rank, COUNT(*) AS cnt"
		     . " FROM t_user_base b"
		     . "   LEFT OUTER JOIN tmp_kpi_material m2"
		     . "     ON b.user_id = m2.target"
		     . "     AND m2.type = 'shop_use_per_user'"
		     . "     AND m2.date_action = ?";

		if ($arppu_min == 0) {
			$sql .= " WHERE (m2.statistic IS NULL OR (m2.statistic >= ? AND m2.statistic < ?))";
		} else {
			$sql .= " WHERE m2.statistic >= ? AND m2.statistic < ?";
		}

		if ($ua) {
			$param[] = $ua;
			$sql .= " AND b.ua = ?";
		}

		$param[] = $threshold;
		$sql .= " AND b.login_date < ?"
             . " GROUP BY b.rank";

		foreach ($this->db_r->getAll($sql, $param) as $row) {
			$kpi_assoc[$row['rank']]['escape_num'] = $row['cnt'];
		}

		// 使用合成素材数（合計とユーザー数）を求める
		$param = array($date, $arppu_min, $arppu_max);
		$sql = " SELECT b.user_id, b.rank"
		     . " FROM t_user_base b"
		     . "   LEFT OUTER JOIN tmp_kpi_material m2"
		     . "     ON b.user_id = m2.target"
		     . "     AND m2.type = 'shop_use_per_user'"
		     . "     AND m2.date_action = ?";

		if ($arppu_min == 0) {
			$sql .= " WHERE (m2.statistic IS NULL OR (m2.statistic >= ? AND m2.statistic < ?))";
		} else {
			$sql .= " WHERE m2.statistic >= ? AND m2.statistic < ?";
		}

		if ($ua) {
			$param[] = $ua;
			$sql .= " AND b.ua = ?";
		}

		$material_total = 0;
		$material_user_num = 0;
		foreach ($this->db_r->getAll($sql, $param) as $row) {
			$num = $this->getDailySynthesisMaterialNumPerUser($row['user_id'], $date);

			foreach (array('material_total', 'material_user_num') as $key) {
				if (!isset($kpi_assoc[$row['rank']][$key])) {
					$kpi_assoc[$row['rank']][$key] = 0;
				}
			}

			$kpi_assoc[$row['rank']]['material_total'] += $num;
			$kpi_assoc[$row['rank']]['material_user_num'] += 1;
		}

		foreach ($kpi_assoc as $rank => $columns) {
			// 使用合成素材数（平均）を求める
			if ($columns['material_user_num'] != 0) {
				$material_avg = floor($columns['material_total'] / $columns['material_user_num']);
			} else {
				$material_avg = null;
			}

			// 存在しない場合もある値の処理
			foreach (array('escape_num', 'lv_avg', 'attack_plus_avg', 'hp_plus_avg') as $colname) {
				if (!isset($columns[$colname])) {
					$columns[$colname] = 0;
				}
			}

			// 記録する
			$this->addKpiRank($date, $arppu_min, $arppu_max, $ua, $rank, $columns['user_num'], $columns['lv_avg'], $columns['attack_plus_avg'], $columns['hp_plus_avg'], $columns['escape_num'], $material_avg);
		}
	}

	/**
	 * ランク帯分布KPI集計データをDBに記録する
	 */
	protected function addKpiRank($date_kpi, $arppu_min, $arppu_max, $ua, $rank, $user_num, $lv_avg, $attack_plus_avg, $hp_plus_avg, $escape_num, $synthesis_material_avg)
	{
		$param = array($date_kpi, $arppu_min, $arppu_max, $ua, $rank, $user_num, $lv_avg, $attack_plus_avg, $hp_plus_avg, $escape_num, $synthesis_material_avg);
		$sql = "INSERT INTO kpi_rank(date_kpi, arppu_min, arppu_max, ua, rank, user_num, lv_avg, attack_plus_avg, hp_plus_avg, escape_num, synthesis_material_avg)"
		     . " VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		if (!$this->db->execute($sql, $param)) {
			throw new Exception($this->db->db->ErrorMsg());
		}

		return true;
	}

	/**
	 * 使用合成素材数（ユーザーごと、日次）を求める
	 *
	 * @param int $user_id ユーザーID
	 * @param string $date 日付(Y-m-d)
	 * @return int 使用合成素材数
	 */
	protected function getDailySynthesisMaterialNumPerUser($user_id, $date)
	{
		if (!$this->db_log_r) {
			$this->db_log_r =& $this->backend->getDB('log_r');
		}

		$periodlog_m = $this->backend->getManager('Periodlog');

		$param = array($date . ' 00:00:00',
			Pp_PeriodlogManager::PERIOD_TYPE_DAILY,
			Pp_PeriodlogManager::ACTION_TYPE_SYNTHESIS_MATERIAL_NUM,
			$user_id,
		);
		$sql = <<<EOD
SELECT num
FROM log_period_user_accumu
WHERE date_start = ?
AND period_type = ?
AND action_type = ?
AND user_id = ?
EOD;
		$num = $this->db_log_r->getOne($sql, $param);
		if (!is_numeric($num)) {
			$num = 0;
		}

		return $num;
	}

	/**
	 * 所持アイテムを集計する
	 *
	 * 集計できるのは現時点の情報のみ
	 */
	function makeKpiUserItem()
	{
		$sql = <<<EOD
SELECT i.item_id, b.ua, SUM(num) AS num
FROM t_user_item i, t_user_base b
WHERE i.user_id = b.user_id
GROUP BY i.item_id, b.ua
EOD;
		$result =& $this->db_r->query($sql);
		if (Ethna::isError($result)) {
			throw new Exception($this->db->db->ErrorMsg());
		}

		$date_kpi = date('Y-m-d', $_SERVER['REQUEST_TIME']);
		$sql2 = <<<EOD
INSERT INTO kpi_user_item(date_kpi, item_id, ua, sum_num, date_created)
VALUES(?, ?, ?, ?, NOW())
EOD;
		while ($row = $result->FetchRow()) {
			$param2 = array($date_kpi, $row['item_id'], $row['ua'], $row['num']);
			if (!$this->db->execute($sql2, $param2)) {
				throw new Exception($this->db->db->ErrorMsg());
			}
		}
	}

	/**
	 * 所持モンスターを集計する
	 *
	 * 集計できるのは現時点の情報のみ
	 */
	function makeKpiUserMonster()
	{
		$sql = <<<EOD
SELECT m.monster_id, b.ua, COUNT(*) AS num
FROM t_user_monster m, t_user_base b
WHERE m.user_id = b.user_id
GROUP BY m.monster_id, b.ua
EOD;
		$result =& $this->db_r->query($sql);
		if (Ethna::isError($result)) {
			throw new Exception($this->db->db->ErrorMsg());
		}

		$date_kpi = date('Y-m-d', $_SERVER['REQUEST_TIME']);
		$sql2 = <<<EOD
INSERT INTO kpi_user_monster(date_kpi, monster_id, ua, sum_num, date_created)
VALUES(?, ?, ?, ?, NOW())
EOD;
		while ($row = $result->FetchRow()) {
			$param2 = array($date_kpi, $row['monster_id'], $row['ua'], $row['num']);
			if (!$this->db->execute($sql2, $param2)) {
				throw new Exception($this->db->db->ErrorMsg());
			}
		}
	}

	/**
	 * 所持アイテムKPI集計の一覧を取得する
	 *
	 * @param string $date_kpi
	 * @return array
	 */
	function getKpiUserItemList($date_kpi)
	{
		$param = array($date_kpi);
		$sql = "SELECT *"
		     . " FROM kpi_user_item"
		     . " WHERE date_kpi = ?";
		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * 所持モンスターKPI集計の一覧を取得する
	 *
	 * @param string $date_kpi
	 * @return array
	 */
	function getKpiUserMonsterList($date_kpi)
	{
		$param = array($date_kpi);
		$sql = "SELECT *"
		     . " FROM kpi_user_monster"
		     . " WHERE date_kpi = ?";
		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * クエスト分布KPI集計の一覧を取得する
	 *
	 * @param string $date_kpi
	 * @param int $arppu_min
	 * @param int $arppu_max
	 * @param int $ua
	 * @return array
	 */
	function getKpiNormalQuestList($date_kpi, $arppu_min, $arppu_max, $ua)
	{
		$param = array($date_kpi, $arppu_min, $arppu_max, $ua);
		$sql = "SELECT *"
		     . " FROM kpi_normal_quest"
		     . " WHERE date_kpi = ?"
		     . " AND arppu_min = ?"
		     . " AND arppu_max = ?"
		     . " AND ua = ?"
		     . " ORDER BY clear_num";
		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * ランク帯分布KPI集計の一覧を取得する
	 *
	 * @param string $date_kpi
	 * @param int $arppu_min
	 * @param int $arppu_max
	 * @param int $ua
	 * @return array
	 */
	function getKpiRankList($date_kpi, $arppu_min, $arppu_max, $ua)
	{
		$param = array($date_kpi, $arppu_min, $arppu_max, $ua);
		$sql = "SELECT *"
		     . " FROM kpi_rank"
		     . " WHERE date_kpi = ?"
		     . " AND arppu_min = ?"
		     . " AND arppu_max = ?"
		     . " AND ua = ?"
		     . " ORDER BY rank";
		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 *
	 * @param type $date_use_start　（端点含む）
	 * @param type $date_use_max   （端点含む）
	 * @param type $duration
	 * @param bool $group_sum_flg app_id問わずの集約値（GROUP BY app_idでSUM(kpi_value)）を取得するか、集約しない値を取得するか
	 */
	function getKpiUserShopList($date_use_start, $date_use_max, $duration_type, $kpi_type, $item_id = null, $group_sum_flg = false)
	{
		$param = array($date_use_start, $date_use_max, $duration_type, $kpi_type);
		$from = " FROM kpi_user_shop";
		$where = " WHERE date_use_start >= ?"
		       . " AND date_use_start <= ?"
		       . " AND duration_type = ?"
		       . " AND kpi_type = ?";

		if ($item_id === null) {
			$where .= " AND num > 0";
		} else {
			$param[] = $item_id;
			$where .= " AND item_id = ?";
		}

		if ($group_sum_flg) {
			$sql = " SELECT date_use_start, duration_type, 0 AS app_id, shop_id, item_id, num, price, kpi_type, SUM(kpi_value) AS kpi_value"
			     . $from
			     . $where
			     . " GROUP BY date_use_start, duration_type, shop_id, item_id, num, price, kpi_type";
		} else {
			$sql = " SELECT date_use_start, duration_type, app_id, shop_id, item_id, num, price, kpi_type, kpi_value"
			     . $from
			     . $where;
		}

		$sql .= " ORDER BY date_use_start, shop_id, item_id, num, price";

		return $this->db_r->GetAll($sql, $param);
	}

	function getLogUserShopList($user_id, $offset = 0, $limit = 100)
	{
		$param = array($user_id);
		$sql = <<<EOD
SELECT *
FROM log_user_shop
WHERE user_id = ?
ORDER BY date_use DESC
EOD;

		if ($limit !== null) {
			$param[] = $offset;
			$param[] = $limit;
			$sql .= " LIMIT ?,?";
		}

		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 *
	 *
	 * データ量が多くなるので行ごとに標準出力へ出力する
	 * （変数に全ての行を保持しようとしない）
	 * TODO: 一旦テンポラリファイルに書き出す処理と、出力する処理の2段階に分けた方が汎用性高かったかも
	 * @param string $date_start　（端点含む）
	 * @param string $date_max   （端点含む）
	 * @param int $user_id
	 */
	function exportTrackingLogList($date_start, $date_max, $user_id = null)
	{
		$param = array($date_start, $date_max);
		$sql = <<<EOD
SELECT *
FROM log_tracking
WHERE date_created >= ?
AND date_created <= ?
EOD;

		if ($user_id) {
			$param[] = $user_id;
			$sql .= <<<EOD
AND user_id = ?
EOD;
		}

		$result = $this->db_r->execute($sql, $param);
		if (!$result) {
			return Ethna::raiseError("CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_r->db->ErrorNo(), $this->db_r->db->ErrorMsg(), __FILE__, __LINE__);
		}

		$header_flg = false;
		while ($row = $result->FetchRow()) {
			if (!$header_flg) {
				echo '"' . implode('","', array_keys($row)) . '"' . "\r\n";
				$header_flg = true;
			}

			$line = '"' . implode('","', array_values($row)) . '"';
			echo mb_convert_encoding($line, 'SJIS') . "\r\n";
		}
	}

	/**
	 * ユーザ基本情報を集計する
	 *
	 * @param string $date 集計対象日(Y-m-d) 必ず昨日以前
	 */
	function statUserBase($date)
	{
		$today = date('Y-m-d');
		if ($date >= $today) {
			throw new Exception('Invalid date.');
		}

		$date_from = $date . ' 00:00:00';
		$date_to = date('Y-m-d H:i:s', strtotime($date_from) + 86400);

		$daily_list = $this->countCreatedUserBase($date_from, $date_to);
		foreach ($daily_list as $row) {
			$this->addStatData('user_create_daily_num', $date, $row['cnt'], $row['ua']);
		}

		$total_list = $this->countCreatedUserBase('2001-01-01 00:00:00', $date_to);
		foreach ($total_list as $row) {
			$this->addStatData('user_create_total_num', $date, $row['cnt'], $row['ua']);
		}

		return true;
	}

	/**
	 * ユーザ登録件数を数える
	 *
	 * @param string $date_from 集計対象期間とする登録日時の開始時点（端点含む）
	 * @param type $date_to 集計対象期間とする登録日時の開始時点（端点含まない）
	 * @return array 件数情報 … array(array('ua' => 0, 'cnt' => 'ua問わない件数), array('ua' => 0以外, 'cnt' => 該当uaについての件数), array(...)))
	 */
	protected function countCreatedUserBase($date_from, $date_to)
	{
		$param = array($date_from, $date_to);
		$sql = <<<EOD
SELECT ua, COUNT(*) AS cnt
FROM t_user_base
WHERE date_created >= ?
AND date_created < ?
GROUP BY ua
EOD;
		$list = $this->db_r->GetAll($sql, $param);

		$total = 0;
		foreach ($list as $row) {
			$total += $row['cnt'];
		}

		$list[] = array('ua' => 0, 'cnt' => $total);

		return $list;
	}

	/**
	 * ユーザ登録件数をユニット共通DBで数える
	 *
	 * @param string $date_from 集計対象期間とする登録日時の開始時点（端点含む）
	 * @param string $date_to 集計対象期間とする登録日時の開始時点（端点含まない）
	 * @return int 件数
	 */
	function countCreatedDbCmnUser($date_from, $date_to)
	{
		if (!$this->db_cmn_r) {
			$this->db_cmn_r =& $this->backend->getDB('cmn_r');
		}

		$param = array($date_from, $date_to);
		$sql = <<<EOD
SELECT COUNT(*) AS cnt
FROM t_user_unit
WHERE date_created >= ?
AND date_created < ?
EOD;

		return $this->db_cmn_r->GetOne($sql, $param);
	}

	/**
	 * ユーザ登録件数の集計値一覧を取得する
	 *
	 * @param string $date_from 集計対象期間とする登録日時の開始時点（Y-m-d, 端点含む）
	 * @param type $date_to 集計対象期間とする登録日時の開始時点（Y-m-d, 端点含まない）
	 * @return array
	 */
	function getCreatedUserBaseList($date_from, $date_to)
	{
		$param = array($date_from, $date_to);
		$sql = <<<EOD
SELECT target, type, date_action, statistic
FROM kpi_common
WHERE type IN('user_create_daily_num', 'user_create_total_num')
AND date_action >= ?
AND date_action < ?
ORDER BY target, type, date_action
EOD;

		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * ユーザ登録件数の集計値一覧を取得する（月次）
	 *
	 * @param string $month_from 集計対象期間とする登録日時の開始時点（Y-m, 端点含む）
	 * @param type $month_to 集計対象期間とする登録日時の開始時点（Y-m, 端点含まない）
	 * @return array
	 */
	function getCreatedUserBaseMonthlyList($month_from, $month_to)
	{
		$date_from = $month_from . '-01';
		$date_to   = $month_to . '-01';

		$param = array($date_from, $date_to, $date_from, $date_to);
		$sql = <<<EOD
SELECT *
FROM ((
  SELECT target, type, DATE_FORMAT(date_action, '%Y-%m') AS date_action, SUM(statistic) AS statistic
  FROM kpi_common
  WHERE type = 'user_create_daily_num'
  AND date_action >= ?
  AND date_action < ?
  GROUP BY target, type, DATE_FORMAT(date_action, '%Y-%m')
) UNION (
  SELECT target, type, DATE_FORMAT(date_action, '%Y-%m') AS date_action, statistic
  FROM kpi_common
  WHERE type = 'user_create_total_num'
  AND date_action >= ?
  AND date_action < ?
  AND DATE_FORMAT(DATE_ADD(date_action, INTERVAL 1 DAY), '%d') = 1
)) t1
ORDER BY target, type, date_action
EOD;

		return $this->db_r->GetAll($sql, $param);
	}

	/**
	 * 統計値をDBに記録する
	 */
	protected function addStatData($type, $date_action, $statistic, $target = 0)
	{
		$param = array($type, $date_action, $statistic, $target);
		$sql = "INSERT INTO kpi_common (type, date_action, statistic, target)"
		     . " VALUES(?, ?, ?, ?)";
		if (!$this->db->execute($sql, $param)) {
			throw new Exception($this->db->db->ErrorMsg());
		}

		return true;
	}

	/**
	 * KPI素材一時データをDBに記録する
	 */
	protected function addTmpKpiMaterialData($type, $date_action, $statistic, $target = 0)
	{
		$param = array($type, $date_action, $statistic, $target);
		$sql = "INSERT INTO tmp_kpi_material(type, date_action, statistic, target)"
		     . " VALUES(?, ?, ?, ?)";
		if (!$this->db->execute($sql, $param)) {
			throw new Exception($this->db->db->ErrorMsg());
		}

		return true;
	}

	/**
	 * 管理画面があるドキュメントルートをセットする
	 *
	 * 管理画面からCLIを呼び出す際などの受け渡し用
	 * @param string $document_root ドキュメントルート
	 */
	function setAdminDocumentRoot($document_root)
	{
		$this->admin_document_root = $document_root;
	}

	/**
	 * Ethnaエントリポイント設定ファイルのフルパスを取得する
	 *
	 * @return array|null ファイル名（フルパス）の配列
	 */
	protected function getEntryIniPhpPaths()
	{
		$filename = '/entry-ini.php';

		if ($this->admin_document_root) {
			$document_root = $this->admin_document_root;
		} else {
			if (!isset($_SERVER['DOCUMENT_ROOT'])) {
				return null;
			}

			$document_root = $_SERVER['DOCUMENT_ROOT'];
		}

		// ドキュメントルートを求める（複数ある）
		$document_roots = array($document_root);

		$extra_basedir = dirname($document_root);

		$extra_subdir = array(
				'htdocs/'.Pp_AdminManager::PORTAL_DOC_ROOT_SUFIX_DIR,
				'htdocs/'.Pp_AdminManager::GAME_DOC_ROOT_SUFIX_DIR,
				'htdocs_dl'
		);

		/*
		foreach (array('htdocs', 'htdocs_dl', 'htdocs_inapi') as $extra_subdir) {
			$extra_dir = $extra_basedir . '/' . $extra_subdir;
			if (!in_array($extra_dir, $document_roots) && is_dir($extra_dir)) {
				$document_roots[] = $extra_dir;
			}
		}
		*/

		foreach ($extra_subdir as $subdir) {
			$extra_dir = $extra_basedir . '/' . $subdir;
			if (!in_array($extra_dir, $document_roots) && is_dir($extra_dir)) {
				$document_roots[] = $extra_dir;
			}
		}

		// entry-ini.phpが必要なドキュメントルートを判別する
		$paths = array();
		foreach ($document_roots as $document_root_tmp) {
			//foreach (array('api.php', 'resource.php', 'inapi.php') as $entrypoint_filename) {
			foreach (array('_api.php', '_portal.php', 'resource.php') as $entrypoint_filename) {
				$entrypoint_path = $document_root_tmp . '/' . $entrypoint_filename;
				if (file_exists($entrypoint_path)) {
					$paths[] = $document_root_tmp . '/entry-ini.php';
					break;
				}
			}
		}

		return $paths;
	}

	/**
	 * Node.jsディレクトリを取得する
	 *
	 * @return string ディレクトリ名（フルパス）
	 */
	protected function getNodeJsDir()
	{
		return dirname(BASE) . '/nodejs';
	}

	/**
	 * エントリポイント設定ファイル(JSON)のパスを取得する
	 *
	 * @return string ディレクトリ名・ファイル名（フルパス）
	 */
	protected function getEntryIniJsonPath()
	{
		return $this->getNodeJsDir() . '/config/entry-ini.json';
	}

	/**
	 * Ethnaエントリポイント設定ファイルをバックアップする
	 *
	 * @return bool 成否
	 */
	function backupEntryIni()
	{
		$uniqid = uniqid();
		$paths = $this->getEntryIniPhpPaths();
		//$paths[] = $this->getEntryIniJsonPath();
		$is_ok = true;

		foreach ($paths as $path) {
			$dest_file = basename($path) . '.' . basename(dirname($path)) . '.' . $uniqid . '.bak';
			$dest_dir  = BASE . '/backup';
			$dest = $dest_dir . '/' . $dest_file;

			if (!file_exists($path)) {
				// OK
				continue;
			}

			if (!copy($path, $dest)) {
				$this->backend->logger->log(LOG_ERR, 'backupEntryIni failed.');
				$is_ok = false;
			}
		}

		return $is_ok;
	}

	/**
	 * Ethnaエントリポイント設定ファイルを書き込む
	 *
	 * @param int $current_ver 現行バージョン
	 * @param int $review_ver レビューバージョン
	 * @return bool 成否
	 */
	function writeEntryIni($current_ver, $review_ver)
	{
		$ret1 = $this->writeEntryIniPhp($current_ver, $review_ver);
		//$ret2 = $this->writeEntryIniJson($current_ver, $review_ver);

		//return ($ret1 && $ret2);
		return $ret1;
	}

	/**
	 * Ethnaエントリポイント設定ファイルを書き込む(PHPのdefine形式)
	 *
	 * @param int $current_ver 現行バージョン
	 * @param int $review_ver レビューバージョン
	 * @return bool 成否
	 */
	protected function writeEntryIniPhp($current_ver, $review_ver)
	{
		// 引数チェック
		if (!preg_match("/^[0-9]{1,10}$/", $current_ver)) { // 整数か
			$this->backend->logger->log(LOG_ERR, 'Invalid current_ver.');
			return false;
		}

		if (!$review_ver) {
			$review_ver_expr = 'null';
		} else if (preg_match("/^[0-9]{1,10}$/", $review_ver)) {
			$review_ver_expr = $review_ver;
		} else {
			$this->backend->logger->log(LOG_ERR, 'Invalid review_ver.');
			return false;
		}

		// 出力内容を準備
$contents = <<<EOD
<?php
//
// DO NOT EDIT THIS FILE
//
// It is automatically generated by admin_program_deploy_entry_ini_update_exec.
//

define('PP_CURRENT_VER', {$current_ver});
define('PP_REVIEW_VER', {$review_ver_expr});

EOD;

		$paths = $this->getEntryIniPhpPaths();
		foreach ($paths as $path) {
			// ファイル出力する
			$ret = file_put_contents($path, $contents, LOCK_EX);
			if ($ret === false) {
				$this->backend->logger->log(LOG_ERR, 'writeEntryIni failed.');
				return false;
			}
		}

		return true;
	}

	/**
	 * エントリポイント設定ファイルを書き込む(JSON形式)
	 *
	 * @param int $current_ver 現行バージョン
	 * @param int $review_ver レビューバージョン
	 * @return bool 成否
	 */
	protected function writeEntryIniJson($current_ver, $review_ver)
	{
		// 引数チェック
		if (!preg_match("/^[0-9]{1,10}$/", $current_ver)) { // 整数か
			$this->backend->logger->log(LOG_ERR, 'Invalid current_ver.');
			return false;
		}

		$current_ver_cast = intval($current_ver);

		if (!$review_ver) {
			$review_ver_cast = null;
		} else if (preg_match("/^[0-9]{1,10}$/", $review_ver)) {
			$review_ver_cast = intval($review_ver);
		} else {
			$this->backend->logger->log(LOG_ERR, 'Invalid review_ver.');
			return false;
		}


		// 出力内容を準備
		$contents = json_encode(array(
			'PP_CURRENT_VER' => $current_ver_cast,
			'PP_REVIEW_VER'  => $review_ver_cast,
		));

		// ファイル出力する
		$path = $this->getEntryIniJsonPath();
		$ret = file_put_contents($path, $contents, LOCK_EX);
		if ($ret === false) {
			$this->backend->logger->log(LOG_ERR, 'writeEntryIniJson failed.');
			return false;
		}

		return true;
	}

	/**
	 * Ethnaエントリポイント設定ファイルをデプロイする
	 *
	 * makuoする
	 * @return bool 成否
	 */
	function deployEntryIni()
	{
		$config_makuo = $this->config->get('makuo');
		if (!is_array($config_makuo) ||
			!isset($config_makuo['command_name'])  || (strlen($config_makuo['command_name']) == 0)
		) {
			$this->backend->logger->log(LOG_WARNING, 'Invalid config_makuo.');
			return false;
		}

		$makuo = $config_makuo['command_name'];

		$paths = $this->getEntryIniPhpPaths();

		$short_paths = array();
		foreach ($paths as $path) {
			if (strpos($path, Pp_AdminManager::PORTAL_DOC_ROOT_SUFIX_DIR) !== false
					|| strpos($path, Pp_AdminManager::GAME_DOC_ROOT_SUFIX_DIR) !== false) {
				$home = dirname(dirname(dirname($path)));
			} else {
				$home = dirname(dirname($path));
			}
			$short_paths[] = substr($path, strlen($home) + 1);
		}

		//$short_paths[] = substr($this->getEntryIniJsonPath(), strlen(dirname($this->getNodeJsDir())) + 1);

//		$option = '-n';
		$option = '-g';

		$result_assoc = array();
		foreach ($short_paths as $short_path) {
			$command = $makuo . ' ' . $option . ' ' . $short_path;

			$output = null;
			$return_var = null;

			exec($command, $output, $return_var);
			$this->backend->logger->log(LOG_WARNING, 'command:' . $command);
			$this->backend->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->backend->logger->log(LOG_WARNING, 'return_var:' . $return_var);

			$result_assoc[$short_path] = $return_var;
		}

		// 同期結果を判定
		$is_error = false;
		foreach ($result_assoc as $short_path => $result) {
			if ($result == 0) {
				// OK
				continue;
			}

			$is_error = true;
			break;
		}

		return ($is_error == false);
	}

	/**
	 * Ethnaエントリポイント設定ファイルをインクルードする
	 *
	 * 管理画面用
	 * @return void
	 */
	function includeEntryIni()
	{
		$paths = $this->getEntryIniPhpPaths();

		require_once $paths[0];
	}

    /**
     * 環境に対応するクライアントバージョンを取得する
     *
     * @param string $appver_env アプリバージョン切り替え用の環境名("main" or "review") 省略可
     * @return int|null クライアントバージョン
     */
    function getAppverFromAppverEnv($appver_env = null)
    {
        if ($appver_env === null) {
            $appver_env = Util::getAppverEnv();
        }

        // 環境に応じたdefine名を求める
        if ($appver_env == 'review') {
            $constant_name = 'PP_REVIEW_VER';
        } else if ($appver_env == 'main') {
            $constant_name = 'PP_CURRENT_VER';
        } else {
            return null;
        }

        // 設定ファイルを読み込む
        $entry_ini_paths = $this->getEntryIniPhpPaths();
        if (!is_array($entry_ini_paths) || empty($entry_ini_paths)) {
            return null;
        }

        include_once $entry_ini_paths[0];
        if (!defined($constant_name)) {
            return null;
        }

        $appver = constant($constant_name);

        return $appver;
    }

    /**
     * プログラム関連ファイルのdiffを実行する
     *
     * @param string $wcroot 作業コピーrootディレクトリ（"/var/stgjugmon/main/tmp/svn～～"等）
     * @param string $base 作業コピーbaseディレクトリ（"web"等）
     * @param array $subdirs 比較対象サブディレクトリ（array("app", "lib")等）
     * @param string $wcside 作業ディレクトリが左右どちら側か（"left" or "right"）
     * @return array diff結果リスト（キーがサブディレクトリ名、値がdiffコマンドの出力）
     */
    function execProgramDiff($wcroot, $base, $subdirs, $wcside)
    {
        if ($wcside === 'left') {
            $left_base  = $base;
            $right_base = BASE;
        } else if ($wcside === 'right') {
            $left_base  = BASE;
            $right_base = $base;
        } else {
			$this->backend->logger->log(LOG_ERR,
					'Invalid parameter. wcside[%s] FILE[%s] LINE[%d]', $wcside, __FILE__, __LINE__);
            return false;
        }

		$lists = array(); // diff結果リスト（キーがサブディレクトリ名、値がdiffコマンドの出力）
		foreach ($subdirs as $subdir) {
			// コマンド組み立て(diff)
            $command = "cd $wcroot && LANG=en_US.UTF-8 diff -r -q --exclude='*.bak' {$left_base}/$subdir {$right_base}/$subdir";

			// コマンド実行(diff)
			$output = null;
			$return_var = null;
			exec($command, $output, $return_var);
			$this->backend->logger->log(LOG_WARNING, 'command:' . $command);
			$this->backend->logger->log(LOG_WARNING, 'output:' . implode("\n", $output));
			$this->backend->logger->log(LOG_WARNING, 'return_var:' . $return_var);

			$lists[$subdir] = $output;
		}

        return $lists;
    }

	/**
	 * テーブル定義からフィールド名を取得する
	 *
	 * @param string $table テーブル名
	 * @param object $dbh データベースハンドラ（Pp_DB_ADOdbオブジェクトへの参照） 省略可
	 * @return array フィールド名の配列
	 *
	 */
	function getFieldsFromTableDefinition($table, $dbh = null)
	{
		if ($dbh === null) {
			$dbh = $this->db_r;
		}

		$mysql_columns_list = $dbh->GetAll("SHOW FULL COLUMNS FROM $table");
		$fields = array_column($mysql_columns_list, 'Field');

		return $fields;
	}

	/**
	 * トランザクションデータ消去バックアップディレクトリを初期化する
	 *
	 * DELETEクエリを実行する前にファイルへバックアップする為に使用する。
	 * @param int $time UNIXタイムスタンプ（省略可）
	 * @return string ディレクトリ名（フルパス）
	 */
	function initTransactionCleanupBackupDir($time = null)
	{
		$dir = $this->getTransactionCleanupBackupDir($time);

		if (!is_dir($dir)) {
			mkdir($dir, 0775);
		}

		return $dir;
	}

	/**
	 * トランザクションデータ消去バックアップディレクトリを圧縮する
	 *
	 * ディレクトリ内の各ファイルをbzip2圧縮する
	 * @param int $time UNIXタイムスタンプ（省略可）
	 */
	function compressTransactionCleanupBackupDir($time = null)
	{
		$dir = $this->getTransactionCleanupBackupDir($time);
		if (!is_dir($dir)) {
			return;
		}

		`bzip2 $dir/*.csv`;
	}

	/**
	 * トランザクションデータ消去バックアップディレクトリ名を取得する
	 *
	 * @see Pp_AdminUserManager::initTransactionCleanupBackupDir
	 * @param int $time UNIXタイムスタンプ（省略可）省略するとリクエストの開始時のタイムスタンプになる
	 * @return string ディレクトリ名（フルパス）
	 */
	protected function getTransactionCleanupBackupDir($time = null)
	{
		if ($time === null) {
			$time = $_SERVER['REQUEST_TIME'];
		}

		$date = date('Ymd', $time);
		$unit = $this->config->get('unit_id');

		$dir = BASE . "/backup/transaction_cleanup_{$date}_unit{$unit}";

		return $dir;
	}

	/**
	 * 現在サーバメンテナンス中か
	 *
	 * iniファイルとDBのt_game_ctrlテーブルを参照して、現在サーバメンテナンス中か判別する。
	 * 別途、ApiActionClassで、所定の時間になったらt_game_ctrlテーブルのstatusを更新する処理が
	 * 動作している事が前提なので注意。
	 */
	function isServerMaintenance()
	{
		$user_m =& $this->backend->getManager('User');

		// サーバメンテナンスチェック
		if ($this->config->get('maintenance') > 0) {
			return true;
		}

		// サーバメンテナンスチェック(DB)
		$gm_ctrl = $user_m->getGameCtrl();
        if ($gm_ctrl['status'] != Pp_UserManager::GAME_CTRL_STATUS_RUNNING) {
			return true;
		}

		return false;
	}
}
