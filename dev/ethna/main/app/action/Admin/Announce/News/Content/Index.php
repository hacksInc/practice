<?php
/**
 *  Admin/Announce/News/Content/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_announce_news_content_* で共通のアクションフォーム定義 */
class Pp_Form_AdminAnnounceNewsContent extends Pp_AdminActionForm
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

			'lu' => array(
				// フォームの定義
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_SELECT,
				'name'      => '言語設定',

				// バリデータ(記述順にバリデータが実行されます)
				'required'  => true,            // 必須オプション(true/false)

				// langとuaの選択肢
				// langは"ja","en","es"の3種類
				// uaはPp_UserManager::OS_IPHONE, OS_ANDROIDの定数にあわせて1,2を使用する。
				'option'    => array(
					'ja1' => 'iOS(日本語)',
//					'en1' => 'iOS(英語)',
//					'es1' => 'iOS(スペイン語)',
					'ja2' => 'Android(日本語)',
//					'en2' => 'Android(英語)',
//					'es2' => 'Android(スペイン語)',
				),
			),

			'lu0' => array(
				// フォームの定義
				'type'      => VAR_TYPE_STRING,
				'form_type' => FORM_TYPE_SELECT,
				'name'      => '言語設定',

				// バリデータ(記述順にバリデータが実行されます)
				'required'  => true,            // 必須オプション(true/false)

				// langとuaの選択肢
				// langは"ja","en","es"の3種類
				// uaはPp_UserManager::OS_IPHONE, OS_ANDROIDの定数にあわせて1,2を使用する。ALL用に0を使用する。
				'option'    => array(
					'ja1' => 'iOS(日本語)',
//					'en1' => 'iOS(英語)',
//					'es1' => 'iOS(スペイン語)',
					'ja2' => 'Android(日本語)',
//					'en2' => 'Android(英語)',
//					'es2' => 'Android(スペイン語)',
					'ja0' => 'ALL(日本語)',
//					'en0' => 'ALL(英語)',
//					'es0' => 'ALL(スペイン語)',
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

			'title' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => 'アナウンスタイトル',      // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 1,               // Minimum value
				'max'         => 256,             // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp

				//  Filter
				'filter'      => null,                     // Optional Input filter to convert input
				'custom'      => 'checkTagsBoldForecolor', // Optional method name which
														   // is defined in this(parent) class.
			),

			'abridge' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => 'アナウンス略文',      // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 1,               // Minimum value
				'max'         => 256,             // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp

				//  Filter
				'filter'      => null,                     // Optional Input filter to convert input
				'custom'      => 'checkTagsBoldForecolor', // Optional method name which
														   // is defined in this(parent) class.
			),

			'body' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXTAREA,  // Form type
				'name'        => 'アナウンス全文',    // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 1,               // Minimum value
				'max'         => 2048,            // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp

				//  Filter
				'filter'      => null,                     // Optional Input filter to convert input
				'custom'      => 'checkTagsBoldForecolor', // Optional method name which
														   // is defined in this(parent) class.
			),

			'banner' => array(
				// Form definition
				'type'        => VAR_TYPE_FILE,   // Input type
				'form_type'   => FORM_TYPE_FILE,  // Form type
				'name'        => 'アナウンスバナー画像',     // Display name

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

			'picture' => array(
				// Form definition
				'type'        => VAR_TYPE_FILE,   // Input type
				'form_type'   => FORM_TYPE_FILE,  // Form type
				'name'        => 'アナウンス全文用画像',     // Display name

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

			'test_flag' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_HIDDEN, // Form type
				'name'        => '表示テスト中フラグ', // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 0,               // Minimum value
				'max'         => 1,               // Maximum value
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

			'picture_uploaded' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_HIDDEN, // Form type

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 0,               // Minimum value
				'max'         => 1,               // Maximum value
			),
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}

		parent::__construct($backend);
	}

	/**
	 * luクエリ値をセッションと同期するフィルタ
	 *
	 * セッションにもクエリにも無かった場合はデフォルト値になる。
	 */
	function _filter_sync_lu($value)
	{
		$session_key = 'admin_announce_news_content_lu';
		$session_value = $this->backend->session->get($session_key);

		$form_value = $this->get('lu');

		if ($form_value) {
			if ($form_value != $session_value) {
				$this->backend->session->set($session_key, $form_value);
			}

			return $form_value;
		} else if ($session_value) {
			return $session_value;
		} else {
			return 'ja1';
		}
	}

	/** lang部分を取り出す */
	function getLang()
	{
//		return substr($this->get('lu'), 0, 2);
		return "ja";
	}

	/** ua部分を取り出す */
	function getUa()
	{
//		return substr($this->get('lu'), 2);
		return "1";
	}

	/** lu0クエリからlang部分を取り出す */
	function getLang0()
	{
//		return substr($this->get('lu0'), 0, 2);
		return "ja";
	}

	/** lu0クエリからua部分を取り出す */
	function getUa0()
	{
//		return substr($this->get('lu0'), 2);
		return "1";
	}

	/**
	 * テンポラリの画像ファイル名を取得する
	 *
	 * @param string $confirm_uniq 確認画面で生成したユニーク値
	 * @return string ファイル名（フルパス）
	 */
	function getAdminTmpFilename($confirm_uniq)
	{
		return BASE . '/tmp/admin_tmp_news_content_' . $confirm_uniq;
	}
}

/**
 *  admin_announce_news_content_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceNewsContentIndex extends Pp_Form_AdminAnnounceNewsContent
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
//		'lu' => array('filter' => 'sync_lu'),
    );

    /**
     *  Form input value convert filter : sample
     *
     *  @access protected
     *  @param  mixed   $value  Form Input Value
     *  @return mixed           Converted result.
     */
    /*
    function _filter_sample($value)
    {
        //  convert to upper case.
        return strtoupper($value);
    }
    */
}

/**
 *  admin_announce_news_content_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceNewsContentIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_news_content_index Action.
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
     *  admin_announce_news_content_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_announce_news_content_index';
    }
}

?>
