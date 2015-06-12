<?php
/**
 *  Admin/Announce/Help/Detail/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_announce_help_detail_* で共通のアクションフォーム定義 */
class Pp_Form_AdminAnnounceHelpDetail extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'help_id' => array(
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

			'category_id' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,    // Input type
				'form_type'   => FORM_TYPE_SELECT,// Form type
				'name'        => '大項目選択',      // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'option'      => 'getCategoryList',
			),

			'priority' => array(
				// フォームの定義
				'type'      => VAR_TYPE_INT,
				'form_type' => FORM_TYPE_SELECT,
				'name'      => '大項目内Priority',

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
				'name'        => 'ヘルプタイトル',// Display name

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
				'name'        => 'ヘルプ詳細',    // Display name

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

			'picture' => array(
				// Form definition
				'type'        => VAR_TYPE_FILE,   // Input type
				'form_type'   => FORM_TYPE_FILE,  // Form type
				'name'        => 'ヘルプ画像',    // Display name

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

			'del_flg' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_HIDDEN, // Form type
				'name'        => '削除フラグ',     // Display name

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

	function getCategoryList()
	{
		$array = array();
		$help_m =& $this->backend->getManager('AdminHelp');
		$category_list = $help_m->getHelpCategorylist();
		foreach ($category_list as $item) {
			$array[$item['id']] = $item['title'];
		}
		return $array;
	}

	/**
	 * テンポラリの画像ファイル名を取得する
	 *
	 * @param string $confirm_uniq 確認画面で生成したユニーク値
	 * @return string ファイル名（フルパス）
	 */
	function getAdminTmpFilename($confirm_uniq)
	{
		return BASE . '/tmp/admin_tmp_help_detail_' . $confirm_uniq;
	}
}

/**
 *  admin_announce_help_detail_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHelpDetailIndex extends Pp_Form_AdminAnnounceHelpDetail
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
 *  admin_announce_help_detail_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHelpDetailIndex extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_announce_help_detail_index Action.
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
	 *  admin_announce_help_detail_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$this->af->setApp('category_list',  $this->af->getCategoryList());
		return 'admin_announce_help_detail_index';
	}
}
