<?php
/*
 * pp-ini.php
 *
 * update:
 */

define("KPI_URL" , "dev-kpi-web.cave.co.jp");

$config = array(
    // site
    'url'		=> 'https://dev.psycho-pass.cave.co.jp',
	'domain'	=> 'dev.psycho-pass.cave.co.jp',

    // debug
    // (to enable ethna_info and ethna_unittest, turn this true)
    'debug' => false,

	// テストサイトかどうかのフラグ
	'is_test_site' => 1,
	
    // db
	// ユニット共通DBのDSN
	'dsn_cmn'   => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-master-new:3306/psychopass_game_main_common_dev', // read-write
	'dsn_cmn_r' => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-slave-new:3306/psychopass_game_main_common_dev', // read-only
	
    // マスターDBのDSN
	'dsn_m'   => 'mysqli://admin:nd06NhpixXem@dbptpyco-master-new:3306/psychopass_game_main_master_dev', // read-write
    'dsn_m_r' => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-slave-new:3306/psychopass_game_main_master_dev', // read-only
    
	// DBデプロイ用設定。スタンバイDB→本番DBに展開
	// Percona Toolkitのコマンドラインツールに渡すオプションを、この設定をパースして生成する
    'dsn_src'   => 'mysqli://admin:nd06NhpixXem@dbptpyco-master-new:3306/psychopass_game_main_standby_dev',
    'dsn_dest'  => 'mysqli://admin:nd06NhpixXem@dbptpyco-master-new:3306/psychopass_game_main_master_dev',

	// 旧ポータル環境参照用（事前のバッチ処理に使うので、ゲーム稼動後は不要になる気が）
	'dsn_p' => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-master-new:3306/psycho_pass2014', // read-write

	// ローカルホスト接続用SSHコマンド
	//'ssh_localhost' => 'ssh -o StrictHostKeyChecking=no -l devjugmon -i /var/www/.ssh/id_rsa localhost',
	'ssh_localhost' => 'ssh -o StrictHostKeyChecking=no -l devptpyco -i /var/www/.ssh/id_rsa localhost',
	
	// Subversion
	'svn' => array(
		'root' => 'svn+ssh://deviajmja@shaver-server/var/svn.repo/jugglerquest-repos',
	),

        // デプロイ用の情報
        'rsync_dest' => array(
                'user'  => 'stgptpyco',
                'host'  => 'localhost',
                'rsh'   => 'ssh',
                'home'  => '/var/stgptpyco/ethna',
        ),
	
 	// makuo実行用の情報
	'makuo' => array(
                'command_name' => 'dev_makuo',
                'ssh_localhost' => 'ssh -o StrictHostKeyChecking=no -l devptpyco -i /var/www/.ssh/id_rsa localhost', // ローカルホスト接続用SSHコマンド
	),

	// log出力用DBのDSN
	'dsn_logex'   => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-master-new:3306/psychopass_game_main_log_extra_dev', // read-write
	'dsn_logex_r' => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-slave-new:3306/psychopass_game_main_log_extra_dev', // read-only
	
    // log
    'log' => array(
        'syslog' => array(
            'level'    => 'debug',
			'facility' => LOG_LOCAL5,
        ),
    ),
    'log_option'            => 'pid,function,pos',
    'log_filter_do'         => '',
    'log_filter_ignore'     => 'Undefined index.*%%.*tpl',

    // memcache
    'memcache_valid' => 0,		// memcacheの有効・無効の切り替え（0:無効, !0:有効）
    'memcache_host' => 'mcptpyco-session-new',
    'memcache_port' => 11311,
    'memcache_use_pconnect' => false,
    'memcache_retry' => 3,

    // i18n
    //'use_gettext' => false,
    
    // mail 
    //'mail_func_workaround' => false,

    // Smarty
    //'renderer' => array(
    //    'smarty' => array(
    //        'left_delimiter' => '{',
    //        'right_delimiter' => '}',
    //    ),
    //),

    // csrf
    // 'csrf' => 'Session',

    // ケイブ決済サーバ
	'platform_manager' => 'UserCave', // ケイブフライトとの互換性確保用の設定
    'api_url' => 'http://stg.payment.cave.co.jp',
//	'api_url' => 'http://dummy.jugmon.net',
//	'app_id_iphone_yen' => 5,
//	'app_id_android_yen' => 4,
	'app_id' => array(
		// 1: Pp_UserManager::OS_IPHONE
		1 => 'JGM_AP_JPY', // JGM_AP_JPY: ジャグラー×モンスター-Apple-円用 ID　(キタック、ケイブ共通)
		
		// 2: Pp_UserManager::OS_ANDROID
//		2 => 'JGM_GG_TST', // JGM_GG_TST: ジャグラー×モンスター-Google-円用 テストID　（ケイブ用）
		2 => 'JGM_GG_JPY', // JGM_GG_JPY: ジャグラー×モンスター-Google-円用 ID　（キタック用）	
	),

	// 21007対応の為のAPP_ID情報
	// 通常APP_ID => アップル審査用APP_ID の連想配列
	'app_id_21007' => array(
		'JGM_AP_JPY' => 'JGM_AP_TST',
	),
	
    // アプリパスワード
    'appw' => '6A78413037676B6F6F5571616D664663',
    
    // 言語
    'lang' => 'ja',
    
//↓uid_rangeはユニットに応じて動的に決まるので、ここではまだ定義しない
//	// user_idの範囲
//	'uid_range' => array(
//		'min' => 910000001,
//		'max' => 919999999,
//	),

	// 全ユニット情報
	'unit_all' => array(
		'1' => array(
			'dsn'   => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-master-new:3306/psychopass_game_main_unit_dev_01', // read-write
			'dsn_r' => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-slave-new:3306/psychopass_game_main_unit_dev_01', // read-only
			'dsn_log'   => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-master-new:3306/psychopass_game_main_log_dev_01', // read-write
			'dsn_log_r' => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-slave-new:3306/psychopass_game_main_log_dev_01', // read-only
			'uid_range' => array(
				'min' => 910000001,
				'max' => 919999999,
			),
			'ppid_range' => array(
				'min' => 910000001,
				'max' => 919999999,
			),
			'max_unit_user' => 1000000, //ユニット内の最大ユーザー数
			'unit_allocatable' => true, //ユニットにユーザーを新規割り当て可能か
		),
		'2' => array(
			'dsn'   => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-master-new:3306/psychopass_game_main_unit_dev_02', // read-write
			'dsn_r' => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-slave-new:3306/psychopass_game_main_unit_dev_02', // read-only
			'dsn_log'   => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-master-new:3306/psychopass_game_main_log_dev_02', // read-write
			'dsn_log_r' => 'mysqli://httpd:qkn84CorIqfl@dbptpyco-slave-new:3306/psychopass_game_main_log_dev_02', // read-only
			'uid_range' => array(
				'min' => 900100000,
				'max' => 900109999,
			),
			'ppid_range' => array(
				'min' => 900100000,
				'max' => 900109999,
			),
			'max_unit_user' => 1000, //ユニット内の最大ユーザー数
			'unit_allocatable' => true, //ユニットにユーザーを新規割り当て可能か
		),
	),
	
	// unit_idは動的に割り当てるのでここではnull
	'unit_id' => null,

	// デフォルトのユニット番号（API以外のアクションで使用）
	'unit_default' => array(
		'admin'    => '1',
		'resource' => '1',
		'cli'      => '1',
		'inapi'    => '1',
	),

    // 以下は、全てのAPIで共通で戻り値として付加される
    'appver' => 1,
    'rscver' => 1,
    'maintenance' => 0,

    'helper_config' => array(
        'last_login_date' => strtotime("-12 month"), // 最終ログインでの絞り込み条件：Unixタイムスタンプ
        'helper_rank_range_max' => 5, // ユーザーのランクの絞り込み条件：ユーザーランク＋この値までを取得：int
        'helper_rank_range_min' => 5, // ユーザーのランクの絞り込み条件：ユーザーランクーこの値までを取得：int
    ),

	// Raid用パラメータ
	'raid_config' => array(
		'party_member_limit' => 5,		// パーティ同時入室メンバー数上限
	),
	
	// GooglePlay公開鍵
	'google_play_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqlRK699nw7dvlHqxa+2SGYQmRwfK60aD2XfwpPJkjkvxrDFaSASQelUjdHEgtT4GGw6R9oYPSqdTN4AW32/a/htECNEASeJSw76fxjnEliAqd/SvQf/GKWUowoPBU+j+oFpmAHP/Zf4/GsTKqsPoNn9JdFahXiudliEoildlIq78aQOSGPj6cxZcB2PNOdsPdF+IKlffcM8UcF8josCg3uB9PppIGgX9YIBFv7pFA4k4VKKH0UW7Rm4tM4W3e1eeDErHIDuS32tVn9wdFcrNKqzsuk4EpVFfr/RvhmV4UrsosVUwFJBiQ9A7afKyx2L0ye8b2c7KnfARbvNls1OXxwIDAQAB',
);

require_once 'pp-ini_common.php';
?>
