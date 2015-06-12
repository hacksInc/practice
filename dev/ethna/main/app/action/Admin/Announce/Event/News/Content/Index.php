<?php
/**
 *  Admin/Announce/Event/News/Content/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once 'Pp_UserManager.php';

/** admin_announce_event_news_content_* で共通のアクションフォーム定義 */
class Pp_Form_AdminAnnounceEventNewsContent extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'content_id' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,    // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => '内容ID',        // Display name

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

			'priority' => array(
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
					99 => '（なし）',
				),
			),

			'body' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXTAREA,  // Form type
				'name'        => '本文',      // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 1,               // Minimum value
				'max'         => 1024,            // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 

				//  Filter
				'filter'      => null,                     // Optional Input filter to convert input
				'custom'      => 'checkTagsBoldForecolorAnchor', // Optional method name which
														   // is defined in this(parent) class.
			),

			'disp_sts' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,    // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => '表示ステータス', // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,
				'min'         => 0, // 0 is Pp_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_NORMAL
				'max'         => 2, // 2 is Pp_NewsManager::EVENT_NEWS_CONTENT_DISP_STS_PAUSE
			),
			
			'banner_disabled' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,       // Input type
				'form_type'   => FORM_TYPE_CHECKBOX, // Form type
				'name'        => 'バナー解除',        // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,           // Required Option(true/false)
				'min'         => 0,               // Minimum value
				'max'         => 1,               // Maximum value
				'option'      => array(1 => 'バナー解除'),
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
				'required'    => false,           // Required Option(true/false)
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
		
		parent::__construct($backend);
	}

	/**
	 * テンポラリのバナー画像ファイル名を取得する
	 * 
	 * @param string $confirm_uniq 確認画面で生成したユニーク値
	 * @return string ファイル名（フルパス）
	 */
	function getAdminTmpBannerFilename($confirm_uniq)
	{
		return BASE . '/tmp/admin_tmp_banner_' . $confirm_uniq;
	}
}

/**
 *  admin_announce_event_news_content_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceEventNewsContentIndex extends Pp_Form_AdminAnnounceEventNewsContent
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  admin_announce_event_news_content_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceEventNewsContentIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_event_news_content_index Action.
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
     *  admin_announce_event_news_content_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_announce_event_news_content_index';
    }
}

?>