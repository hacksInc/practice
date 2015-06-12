<?php
/**
 *  Pp_Controller.php
 *
 *  コントローラークラス
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/** Application base directory */
define('BASE', dirname(dirname(__FILE__)));

/** include_path setting (adding "/app" and "/lib" directory to include_path) */
$app = BASE . "/app";
$lib = BASE . "/lib";
set_include_path(implode(PATH_SEPARATOR, array($app, $lib)) . PATH_SEPARATOR . get_include_path());

/** including application library. */
require_once 'Ethna/Ethna.php';
require_once 'Pp_Define.php';
require_once 'Pp_Error.php';
require_once 'Pp_ActionClass.php';
require_once 'Pp_ApiActionClass.php';
require_once 'Pp_ActionForm.php';
require_once 'Pp_ApiActionForm.php';
require_once 'Pp_ViewClass.php';
require_once 'Pp_UnitManager.php';
require_once 'Pp_Backend.php';
require_once 'Pp_Util.php';
require_once 'const/Const_Pp.php';

/**
 *  Pp application Controller definition.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Controller extends Ethna_Controller
{
	/**#@+
	 *  @access private
	 */

	/**
	 *  @var    string  Application ID(appid)
	 */
	var $appid = 'PP';

	/**
	 *  @var    array   forward definition.
	 */
	var $forward = array(
		/*
		 *  TODO: write forward definition here.
		 *
		 *  Example:
		 *
		 *  'index'         => array(
		 *      'view_name' => 'Pp_View_Index',
		 *  ),
		 */
	);

	/**
	 *  @var    array   action definition.
	 */
	var $action = array(
		/*
		 *  TODO: write action definition here.
		 *
		 *  Example:
		 *
		 *  'index'     => array(
		 *      'form_name' => 'Sample_Form_SomeAction',
		 *      'form_path' => 'Some/Action.php',
		 *      'class_name' => 'Sample_Action_SomeAction',
		 *      'class_path' => 'Some/Action.php',
		 *  ),
		 */
	);

	/**
	 *  @var    array   SOAP action definition.
	 */
	var $soap_action = array(
		/*
		 *  TODO: write action definition for SOAP application here.
		 *  Example:
		 *
		 *  'sample'            => array(),
		 */
	);

	/**
	 *  @var    array       application directory.
	 */
	var $directory = array(
		'action'        => 'app/action',
		'action_cli'    => 'app/action_cli',
		'action_xmlrpc' => 'app/action_xmlrpc',
		'app'           => 'app',
		'plugin'        => 'app/plugin',
		'bin'           => 'bin',
		'etc'           => 'etc',
		'filter'        => 'app/filter',
		'locale'        => 'locale',
		'log'           => 'log',
		'plugins'       => array('app/plugin/Smarty',),
		'template'      => 'template',
		'template_c'    => 'tmp',
		'tmp'           => 'tmp',
		'view'          => 'app/view',
		'www'           => 'www',
		'test'          => 'app/test',
	);

	/**
	 *  @var    array       database access definition.
	 */
	var $db = array(
		''              => DB_TYPE_RW,
		'r'             => DB_TYPE_RO,
        'p'             => DB_TYPE_RW,
		'p_r'           => DB_TYPE_RO,
		'm'             => DB_TYPE_RW,
        'm_r'           => DB_TYPE_RO,
		'log'           => DB_TYPE_RW,
		'log_r'         => DB_TYPE_RO,
		'cmn'           => DB_TYPE_RW,
		'cmn_r'         => DB_TYPE_RO,
		'logex'         => DB_TYPE_RW,
		'logex_r'       => DB_TYPE_RO,
		'unit1'         => DB_TYPE_RW,
		'unit1_r'       => DB_TYPE_RO,
	);

	/**
	 *  @var    array       extention(.php, etc) configuration.
	 */
	var $ext = array(
		'php'           => 'php',
		'tpl'           => 'tpl',
	);

	/**
	 *  @var    array   class definition.
	 */
	var $class = array(
		/*
		 *  TODO: When you override Configuration class, Logger class,
		 *        SQL class, don't forget to change definition as follows!
		 */
		'class'         => 'Ethna_ClassFactory',
//		'backend'       => 'Ethna_Backend',
		'backend'       => 'Pp_Backend',
		'config'        => 'Ethna_Config',
//      'db'            => 'Ethna_DB_PEAR',
//		'db'            => 'Ethna_DB_ADOdb',
		'db'            => 'Pp_DB_ADOdb',
		'error'         => 'Ethna_ActionError',
		'form'          => 'Pp_ActionForm',
		'i18n'          => 'Ethna_I18N',
		'logger'        => 'Pp_Logger',
		'plugin'        => 'Ethna_Plugin',
		'session'       => 'Ethna_Session',
		'sql'           => 'Ethna_AppSQL',
		'view'          => 'Pp_ViewClass',
		'renderer'      => 'Ethna_Renderer_Smarty',
		'url_handler'   => 'Pp_UrlHandler',
	);

	/**
	 *  @var    array       list of application id where Ethna searches plugin.
	 */
	var $plugin_search_appids = array(
		/*
		 *  write list of application id where Ethna searches plugin.
		 *
		 *  Example:
		 *  When there are plugins whose name are like "Common_Plugin_Foo_Bar" in
		 *  application plugin directory, Ethna searches them in the following order.
		 *
		 *  1. Common_Plugin_Foo_Bar,
		 *  2. Pp_Plugin_Foo_Bar
		 *  3. Ethna_Plugin_Foo_Bar
		 *
		 *  'Common', 'Pp', 'Ethna',
		 */
		'Pp', 'Ethna',
	);

	/**
	 *  @var    array       filter definition.
	 */
	var $filter = array(
		/*
		 *  TODO: when you use filter, write filter plugin name here.
		 *  (If you specify class name, Ethna reads filter class in 
		 *   filter directory)
		 *
		 *  Example:
		 *
		 *  'ExecutionTime',
		 */
		'Tracking',
	);

	/**#@-*/

    /**
     *  DB設定を返す
     *  引数がない場合、初期接続するもの（db、db_r）のみを返す
     *  これはEthna_AppManager.phpのコンストラクタでDBとの接続を行っているので、そこで無駄な接続を生まないようにするため
     *  要するにユニット以外の各DBへはgetDBしてください
     *
	 *  "log"と"log_r"は返さない
     *  @access public
     *  @param  string  $db_key DBキー("", "r", "rw", "default", "blog_r"...)
     *  @return string  $db_keyに対応するDB種別定義(設定が無い場合はnull)
     */
    function getDBType($db_key = null)
    {
        if (is_null($db_key)) {
            // 指定がなければdbおよびdb_rの情報のみを返す
			return array("" => $this->db[""], "r" => $this->db["r"]);
        }

		return parent::getDBType($db_key);
    }

	/**
	 *  Get Default language and locale setting.
	 *  If you want to change Ethna's output encoding, override this method.
	 *
	 *  @access protected
	 *  @return array   locale name(e.x ja_JP, en_US .etc),
	 *                  system encoding name,
	 *                  client encoding name(= template encoding)
	 *                  (locale name is "ll_cc" format. ll = language code. cc = country code.)
	 */
	function _getDefaultLanguage()
	{
		return array('ja_JP', 'UTF-8', 'UTF-8');
	}

	/**
	 *  テンプレートエンジンのデフォルト状態を設定する
	 *
	 *  @access protected
	 *  @param  object  Ethna_Renderer  レンダラオブジェクト
	 *  @obsolete
	 */
	function _setDefaultTemplateEngine(&$renderer)
	{
	}

	/**
	 *  フォームにより要求されたアクション名を返す
	 *
	 *  @access protected
	 *  @return string  フォームにより要求されたアクション名
	 */
	function _getActionName_Form ()
	{
		// 親メソッドを呼ぶと、若干無駄な処理が実行される罠
//		parent::_getActionName_Form();
		
		// mod_rewrite経由で渡されたアクション名を取得する
		// ※別にクエリとして渡してもいいですけど…
		$action_name = null;
		
		if ( isset( $_REQUEST['_action'] ) && $_REQUEST['_action'] != "" ) {
			if ( preg_match("/\/p\//", $_REQUEST['_action'], $matches) ) {
				// パラメータをURLとして付与するパターン
				$p_action = explode('/p/', $_REQUEST['_action']);
				
				// "/p/"より前をaction_nameとする
				$action_name = str_replace( '/', '_', $p_action[0] );
			} else {
				$action_name = str_replace( '/', '_', $_REQUEST['_action'] );
			}
			$action_name = rtrim( $action_name, '_' );
		}
		
		// mod_rewriteのRewriteRuleに"mo_"が付いたり付かなかったりする事への対応
		foreach (array('mo_api_' => 'api_', 'mo_admin_' => 'admin_', 'mo_resource_' => 'resource_') as $from => $to) {
			$len = strlen($from);
			if (strncmp($action_name, $from, $len) === 0) {
				$action_name = $to . substr($action_name, $len);
				break;
			}
		}
        
        // 20141217黒澤
        // ポータルアプリのURL指定に拡張子がついているため、対応
        if ( substr( $action_name, 0, 6 ) == 'portal' && substr( $action_name, strlen( $action_name ) - 4, 4 ) == '.php' ) {
            $action_name = substr( $action_name, 0, strlen( $action_name ) - 4 );
        }

//TODO RESTのAPIの処理はここでいいのか？ EthaのUrlHandlerとかの方がいいのか？
		if (strncmp($action_name, 'admin_api_', 10) === 0) { // 10 == strlen('admin_api_')
			if (strncmp($action_name, 'admin_api_rest_', strlen('admin_api_rest_')) === 0) {
				$action_name = 'admin_api_rest';
			}
		}
		
		$this->logger->log(LOG_INFO, 'action_name=[' . $action_name . ']');
		
		return $action_name;
	}
	
	// 無理矢理だが... DSNを再セットする関数
	// この関数は基底クラスには無い。Pp_Controllerの独自の関数。
	function resetDSN()
	{
		$this->dsn = $this->_prepareDSN();
	}
}

// 負荷テスト対応
// 負荷テスト終了後はコメントアウトすること
/*
function is_stress_test()
{
	if ((strcmp($_SERVER['SERVER_NAME'], 'jmja.jugmon.net') === 0) ||  // 商用環境
//	if ((strncmp($_SERVER['SERVER_NAME'], 'dev.', 4) === 0) ||  // 開発環境
	    (strcmp($_SERVER['SERVER_NAME'], 'st.jmja.jugmon.net') === 0) ||  // 負荷テスト環境
	    (strncmp($_SERVER['SERVER_NAME'], 'stg.', 4) === 0) ||  // ステージング環境
	    (strncmp($_SERVER['SERVER_NAME'], '192.168.56.', 11) === 0)    // VirtualBoxローカル開発環境
	) {
//		if (in_array($_SERVER['REMOTE_ADDR'], array(
//			'192.168.131.248', // CAVE Nakameguro office to Nihonbashi IDC via VPN
//			'124.39.138.2',    // CAVE Nakameguro office's global IP address
//			'192.168.56.1',    // VirtualBox (Local PC only)
//		))) {
			return true;
//		}
	}

	return false;
}
*/
