<?php
/**
 *  Admin/Announce/Help/Category/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_announce_help_category_* で共通のアクションフォーム定義 */
class Pp_Form_AdminAnnounceHelpCategory extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'category_id' => array(
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

			'use_name' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => 'UseName',       // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 1,               // Minimum value
				'max'         => 512,             // Maximum value
				'regexp'      => null,            // String by Regexp
				'mbregexp'    => null,            // Multibype string by Regexp
				'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp

				//  Filter
				'filter'      => null,                     // Optional Input filter to convert input
				'custom'      => 'checkTagsBoldForecolor', // Optional method name which
				// is defined in this(parent) class.
			),

			'title' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING, // Input type
				'form_type'   => FORM_TYPE_TEXT,  // Form type
				'name'        => 'タイトル',      // Display name

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
		);

		foreach ($form_template as $key => $value) {
			$this->form_template[$key] = $value;
		}

		parent::__construct($backend);
	}
}

/**
 *  admin_announce_help_category_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpCategoryIndex extends Pp_Form_AdminAnnounceHelpCategory
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
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
 *  admin_announce_help_category_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpCategoryIndex extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_help_category_index Action.
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
	 *  admin_announce_help_category_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_announce_help_category_index';
	}
}
