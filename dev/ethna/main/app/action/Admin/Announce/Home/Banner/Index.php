<?php
/**
 *  Admin/Announce/Home/Banner/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once 'Pp_NewsManager.php';
require_once 'Pp_UserManager.php';

/** admin_announce_home_banner_* で共通のアクションフォーム定義 */
class Pp_Form_AdminAnnounceHomeBanner extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'hbanner_id' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,    // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => 'メインバナーID', // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 1,               // Minimum value
				'max'         => null,            // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp
				//  Filter
				'filter'      => null,            // Optional Input filter to convert input
				'custom'      => null,            // Optional method name which
				// is defined in this(parent) class.
			),

			'ua' => array(
				// フォームの定義
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_SELECT,
				'name'      => '対応OS',

				// バリデータ(記述順にバリデータが実行されます)
				'required'  => true,            // 必須オプション(true/false)
				'option'    => array(
					Pp_UserManager::OS_IPHONE         => 'iOS',
					Pp_UserManager::OS_ANDROID        => 'Android',
					Pp_UserManager::OS_IPHONE_ANDROID => 'iOS / Android',
				),
			),

			'img_id' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,    // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => 'バナーイメージID', // Display name
		
				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => -1,               // Minimum value
				'max'         => null,            // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp
				//  Filter
				'filter'      => null,            // Optional Input filter to convert input
				'custom'      => null,            // Optional method name which
				// is defined in this(parent) class.
			),
				
			'type' => array(
				// フォームの定義
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_SELECT,
				'name'      => 'Type',

				// バリデータ(記述順にバリデータが実行されます)
				'required'  => true,            // 必須オプション(true/false)
				'option'    => array(
					Pp_NewsManager::HOME_BANNER_TYPE_NONE        => Pp_NewsManager::HOME_BANNER_TYPE_NONE . ':なし',
					Pp_NewsManager::HOME_BANNER_TYPE_SHOP        => Pp_NewsManager::HOME_BANNER_TYPE_SHOP . ':ショップ画面へ',
					Pp_NewsManager::HOME_BANNER_TYPE_MISSION     => Pp_NewsManager::HOME_BANNER_TYPE_MISSION . ':ミッション画面へ',
					Pp_NewsManager::HOME_BANNER_TYPE_CHARACTER   => Pp_NewsManager::HOME_BANNER_TYPE_CHARACTER . ':キャラクター画面へ',
					Pp_NewsManager::HOME_BANNER_TYPE_PHOTO       => Pp_NewsManager::HOME_BANNER_TYPE_PHOTO . ':フォト画面へ',
					Pp_NewsManager::HOME_BANNER_TYPE_MEDAL       => Pp_NewsManager::HOME_BANNER_TYPE_MEDAL . ':メダル画面へ',
					Pp_NewsManager::HOME_BANNER_TYPE_PRESENT_BOX => Pp_NewsManager::HOME_BANNER_TYPE_PRESENT_BOX . ':プレゼントBox画面へ',
					Pp_NewsManager::HOME_BANNER_TYPE_URL         => Pp_NewsManager::HOME_BANNER_TYPE_URL . ':外部ブラウザ',
					Pp_NewsManager::HOME_BANNER_TYPE_WEBVIEW     => Pp_NewsManager::HOME_BANNER_TYPE_WEBVIEW . ':webview',
				),
			),

			'memo' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => '運営メモ',      // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,           // Required Option(true/false)
				'min'         => 0,               // Minimum value
				'max'         => 512,             // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp

				//  Filter
				'filter'      => null,            // Optional Input filter to convert input
				'custom'      => null,            // Optional method name which
				// is defined in this(parent) class.
			),

			'disp_sts' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,    // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => '表示ステータス', // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,
				'min'         => Pp_NewsManager::HOME_BANNER_DISP_STS_NORMAL,
				'max'         => Pp_NewsManager::HOME_BANNER_DISP_STS_PAUSE,
			),

			'pri' => array(
				// フォームの定義
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_SELECT,
				'name'      => 'Priority',

				// バリデータ(記述順にバリデータが実行されます)
				'required'  => true,            // 必須オプション(true/false)
				'option'    => array(
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
					6 => '6',
					7 => '7',
					8 => '8',
					9 => '9',
					99 => 'なし',
				),
			),

			'url_ja' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => 'URL',           // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,           // Required Option(true/false)
				'min'         => 0,               // Minimum value
				'max'         => 512,             // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp

				//  Filter
				'filter'      => null,            // Optional Input filter to convert input
				'custom'      => 'checkURL, checkHostEnv',      // Optional method name which
				// is defined in this(parent) class.
			),

			'banner_attribute' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,    // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => 'バナー属性',    // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,
				'min'         => 0,
				'max'         => 99,
			),

			'banner_uploaded' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_HIDDEN, // Form type

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 0,               // Minimum value
				'max'         => 1,               // Maximum value
			),

			'banner_image' => array(
				// Form definition
				'type'        => VAR_TYPE_FILE,   // Input type
				'form_type'   => FORM_TYPE_FILE,  // Form type
				'name'        => 'バナー画像',     // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,            // Required Option(true/false)
				'min'         => null,            // Minimum value
				'max'         => null,            // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp

				//  Filter
				'filter'      => null,            // Optional Input filter to convert input
				'custom'      => null,            // Optional method name which
				// is defined in this(parent) class.
			),

		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}

		$this->form_template['date_start']['name'] = '公開開始日時';
		$this->form_template['date_end']['name']   = '公開終了日時';

		parent::__construct($backend);
	}

	/**
	 *  ユーザ定義検証メソッド(フォーム値間の連携チェック等)
	 *
	 *  @access protected
	 */
	function _validatePlus()
	{
		$news_m =& $this->backend->getManager('AdminNews');
	}

	/**
	 * テンポラリのバナー画像ファイル名を取得する
	 *
	 * @param string $confirm_uniq 確認画面で生成したユニーク値
	 * @return string ファイル名（フルパス）
	 */
	function getAdminTmpBannerFilename($confirm_uniq)
	{
		return BASE . '/tmp/admin_tmp_home_banner_' . $confirm_uniq;
	}
}

/**
 *  admin_announce_home_banner_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHomeBannerIndex extends Pp_Form_AdminAnnounceHomeBanner
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
	);
}

/**
 *  admin_announce_home_banner_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHomeBannerIndex extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_home_banner_index Action.
	 *
	 *  @access public
	 *  @return string    forward name(null: success.
	 *                                false: in case you want to exit.)
	 */
/*
	function prepare()
	{
		// アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
	}
 */

	/**
	 *  admin_announce_home_banner_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_announce_home_banner_index';
	}
}
