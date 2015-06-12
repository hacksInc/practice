<?php
// vim: foldmethod=marker
/**
 *  Pp_AdminActionForm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

// {{{ Pp_AdminActionForm
/**
 *  AdminActionForm class.
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_AdminActionForm extends Ethna_ActionForm
{
	/**#@+
	 *  @access private
	 */

	/** @var    array   form definition (default) */
	var $form_template = array(
		'duration_type' => array(
			// Form definition
			'type'        => VAR_TYPE_INT,    // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => '期間種別',       // Display name

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => 1,               // Minimum value
			'max'         => 3,               // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
			//  Filter
			'filter'      => 'sample',        // Optional Input filter to convert input
			'custom'      => null,            // Optional method name which
											  // is defined in this(parent) class.
		),

		'start' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING, // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'start',         // Display name

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

		'end' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => 'end',            // Display name

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

		'date' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => 'date',           // Display name

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

		'date_disp' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => '表示日時',       // Display name

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
			//  Filter
			'filter'      => 'space_zentohan,numeric_zentohan,ltrim,rtrim', // Optional Input filter to convert input
			'custom'      => 'checkDatetime9999', // Optional method name which
											      // is defined in this(parent) class.
		),

		'date_start' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => '表示開始日時',   // Display name

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
			//  Filter
			'filter'      => 'space_zentohan,numeric_zentohan,ltrim,rtrim', // Optional Input filter to convert input
			'custom'      => 'checkDatetime9999', // Optional method name which
											      // is defined in this(parent) class.
		),
		
		'date_end' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => '表示終了日時',   // Display name

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
			//  Filter
			'filter'      => 'space_zentohan,numeric_zentohan,ltrim,rtrim', // Optional Input filter to convert input
			'custom'      => 'checkDatetime9999', // Optional method name which
											      // is defined in this(parent) class.
		),
		
		'date_created_from' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => '生成日時',       // Display name

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
			//  Filter
			'filter'      => 'space_zentohan,numeric_zentohan,ltrim,rtrim', // Optional Input filter to convert input
			'custom'      => 'checkDatetime9999', // Optional method name which
											      // is defined in this(parent) class.
		),
		
		'date_created_to' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => '生成日時',       // Display name

			//  Validator (executes Validator by written order.)
			'required'    => true,            // Required Option(true/false)
			'min'         => null,            // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
			//  Filter
			'filter'      => 'space_zentohan,numeric_zentohan,ltrim,rtrim', // Optional Input filter to convert input
			'custom'      => 'checkDatetime9999', // Optional method name which
											      // is defined in this(parent) class.
		),
		
		'format' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => 'format',         // Display name

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
		),

		'platform_query' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_TEXT,   // Form type
			'name'        => 'platform_query', // Display name

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
		),
		
		'arppu_range' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_SELECT, // Form type
			'name'        => 'ARPPU範囲', // Display name

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

			'option'      => array(
				'0_1000'             => '0～1000', 
				'1000_10000'         => '1000～10000',
				'10000_100000'       => '10000～100000',
				'100000_1000000'     => '100000～1000000',
				'1000000_1000000000' => '1000000～1000000000',
			),
		),
		
		'platform' => array(
			// Form definition
			'type'        => VAR_TYPE_STRING,  // Input type
			'form_type'   => FORM_TYPE_SELECT, // Form type
			'name'        => 'プラットフォーム', // Display name

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

			'option'      => array(
				'apple'  => 'Apple',
				'google' => 'Google', 
				'any'    => '問わず',
			),
		),
		
        'table' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING, // Input type
 			'name'        => 'テーブル名',    // Display name
       
            //  Validator (executes Validator by written order.)
            'required'    => false,               // Required Option(true/false)
            'min'         => 0,                   // Minimum value
            'max'         => 64,                  // Maximum value
            'regexp'      => '/^[a-zA-Z0-9_]+$/', // String by Regexp
        ),

        'tables' => array(
            // Form definition
            'type'        => array(VAR_TYPE_STRING), // Input type
 			'name'        => 'テーブル名',           // Display name
       
            //  Validator (executes Validator by written order.)
            'required'    => false,               // Required Option(true/false)
            'min'         => 0,                   // Minimum value
            'max'         => 64,                  // Maximum value
            'regexp'      => '/^[a-zA-Z0-9_]+$/', // String by Regexp
        ),

		'nickname' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'nickname',      // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => false,           // Required Option(true/false)
            'min'         => 1,               // Minimum value
            'max'         => 256,             // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        ),

		// 管理画面ユーザ
		'lid' => array(
            'type'      => VAR_TYPE_STRING,    // Input type
            'form_type' => FORM_TYPE_TEXT,     // Form type
            'name'      => 'ID',               // Display name
        
            //  Validator (executes Validator by written order.)
            'required' => true,                // Required Option(true/false)
            'min'      => null,                // Minimum value
            'max'      => 16,                  // Maximum value
            'regexp'   => '/^[a-zA-Z0-9.]+$/', // String by Regexp
			
            //  Filter
            'filter'   => null,                // Optional Input filter to convert input
            'custom'   => null,                // Optional method name which
        ),

		// 管理画面パスワード
		'lpw' => array(
            'type'      => VAR_TYPE_STRING,      // Input type
            'form_type' => FORM_TYPE_TEXT,       // Form type
            'name'      => 'Password',           // Display name
        
            //  Validator (executes Validator by written order.)
            'required' => true,                  // Required Option(true/false)
            'min'      => null,                  // Minimum value
            'max'      => 16,                    // Maximum value
            'regexp'   => '/^[a-zA-Z0-9._-]+$/', // String by Regexp
        ),

		// アクセス制御ロール
		'role' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => 2,               // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 

            //  Filter
            'filter'      => null,            // Optional Input filter to convert input
            'custom'      => 'checkRole',     // Optional method name which
		),
		
		// ページング指定（0始まり）
		'page' => array(
			// Form definition
			'type'        => VAR_TYPE_INT,    // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'ページ',        // Display name

			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
			'min'         => 0,               // Minimum value
			'max'         => null,            // Maximum value
			'regexp'      => null,            // String by Regexp
			'mbregexp'    => null,            // Multibype string by Regexp
			'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
			//  Filter
			'filter'      => null,            // Optional Input filter to convert input
			'custom'      => null,            // Optional method name which
											  // is defined in this(parent) class.
		),
		
		// ページング指定（PEAR::Pager用。1始まり）
		'pageID' => array(
			// Form definition
			'type'        => VAR_TYPE_INT,    // Input type
			'form_type'   => FORM_TYPE_TEXT,  // Form type
			'name'        => 'ページ',        // Display name

			//  Validator (executes Validator by written order.)
			'required'    => false,           // Required Option(true/false)
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
		
		// 確認画面ユニーク値
        'confirm_uniq' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING,  // Input type
            'form_type'   => FORM_TYPE_HIDDEN, // Form type
			'name'        => 'confirm_uniq',   // Display name
        
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
		
		'lang' => array(
			// フォームの定義
			'type'      => VAR_TYPE_STRING, 
			'form_type' => FORM_TYPE_SELECT,
			'name'      => '言語',

			// バリデータ(記述順にバリデータが実行されます)
			'required'  => true,         // 必須オプション(true/false)

			'option'    => array(
				'ja' => '日本語',
			),
		),
	);

    /**
     *  setter method for form definition.
     *
     *  @access protected
     */
    function _setFormDef()
    {
        return parent::_setFormDef();
    }
	
    /**
     *  フォーム値変換フィルタ: 全角スペース->半角スペース
     *
     *  @access protected
     *  @param  mixed   $value  フォーム値
     *  @return mixed   変換結果
     */
    function _filter_space_zentohan($value)
    {
        return mb_convert_kana($value, "s");
    }
	
	/**
	 * 管理画面ユーザが存在することをチェックする
	 */
	function checkLidExists($name)
	{
		$lid = $this->form_vars[$name];
		$admin_m =& $this->backend->getManager('Admin');
		
		$user = $admin_m->getAdminUser($lid);
		if (!$user) {
			$this->ae->add($name, "アカウントがありません。", E_ERROR_DEFAULT);
		}
	}
	
	/**
	 * 現在ログイン中の管理画面ユーザでないことをチェックする
	 */
	function checkLidNotCurrent($name)
	{
		$lid = $this->form_vars[$name];
		
		if ($lid == $this->backend->session->get('lid')) {
			$this->ae->add($name, "そのIDは指定できません。", E_ERROR_DEFAULT);
		}
	}
	
	/**
	 * アクセス制御ロールをチェックする
	 */
	function checkRole($name)
	{
		$role = $this->form_vars[$name];

		$admin_m =& $this->backend->getManager('Admin');
		
		if (!isset($admin_m->ACCESS_CONTROL_ROLE[$role])) {
			$this->ae->add($name, "ロールがありません。", E_ERROR_DEFAULT);
		}
	}

	/**
	 * 西暦9999年まで対応したdatetimeのチェック処理
	 * 
	 * 基本的にはstrtotimeでチェックするが、
	 * UNIXタイムスタンプの範囲外でも西暦1年～9999年までならOKとする。
	 */
	function checkDatetime9999($name)
	{
		$datetime = $this->form_vars[$name];
		
		if (strlen($datetime) === 0) {
			if (isset($this->form_template[$name])) {
				if (!isset($this->form_template[$name]['required']) || 
					!$this->form_template[$name]['required']
				) {
					//OK
					return;
				}
			}
		}
		
		if (strtotime($datetime)) {
			//OK
			return;
		}
		
		$year = substr($datetime, 0, 4);
		if (preg_match('/^[0-9]{4}$/', $year) &&
		    ($year >= 1) &&
		    ($year <= 9999) &&
		    strtotime('2001' . substr($datetime, 4))
		) {
			//OK
			return;
		}

		$this->ae->add($name, "{form}が不正です。", E_ERROR_DEFAULT);
	}
	
	/**
	 * 日時が過去であることをチェックする
	 */
	function checkDatetimePast($name)
	{
		$date = $this->form_vars[$name];
		
		$time = strtotime($date);
		$now = $_SERVER['REQUEST_TIME'];
		
		if (!$time) {
			$this->ae->add($name, "{form}が不正です。");
		} else if ($now <= $time) {
			$this->ae->add($name, "{form}は過去の日時を指定して下さい。");
		}
	}
	
	/**
	 * 不正タグチェック（TinyMCEのbold,forecolorボタンが生成するタグのみ許可）
	 * 
	 * 許可するタグは以下の通り。
	 * <p></p><strong></strong><span style="color: #xxxxxx;"></span>
	 */
	function checkTagsBoldForecolor($name)
	{
		$source = $this->form_vars[$name];
//DEBUG		
//$source .= "<br>";
//$source .= '<span style="color:red;">TEST</span>';
//$source .= '<span style="color: #zzzzzz;">TEST</span>';

		$check1 = strip_tags($source);
/*
		$tmp = str_replace(array('<p>', '</p>', '<br />', '<strong>', '</strong>', '</span>'), '', $source);
		$check2 = preg_replace('/<span style="color: #[a-f0-9]+;">/', '', $tmp);
*/

                $tmp = str_replace(array('<p>', '</p>', '<br />', '<strong>', '</strong>', '</span>', '</a>'), '', $source);
                $tmp = preg_replace('/<span style="color: #[a-f0-9]+;">/', '', $tmp);
                $check2 = preg_replace('/<a .+>/', '', $tmp);


		if (strcmp($check1, $check2) !== 0) {
			$this->ae->add($name, "不正なHTMLタグがあります。", E_ERROR_DEFAULT);
		}
	}
	
	/**
	 * 不正タグチェック（TinyMCEのbold,forecolorボタンが生成するタグと、aタグを許可）
	 * 
	 * 許可するタグは以下の通り。
	 * <p></p><strong></strong><span style="color: #xxxxxx;"></span><a ～></a>
	 * ※aタグ内にjavascriptがあったり、リンク先がwww.jugmon.net以外だったりしてもエラーにならないので注意。
	 */
	function checkTagsBoldForecolorAnchor($name)
	{
		$source = $this->form_vars[$name];

		$check1 = strip_tags($source);
		
		$tmp = str_replace(array('<p>', '</p>', '<br />', '<strong>', '</strong>', '</span>', '</a>'), '', $source);
		$tmp = preg_replace('/<span style="color: #[a-f0-9]+;">/', '', $tmp);
		$check2 = preg_replace('/<a .+>/', '', $tmp);
		
		if (strcmp($check1, $check2) !== 0) {
			$this->ae->add($name, "不正なHTMLタグがあります。", E_ERROR_DEFAULT);
		}
	}
	
	/**
	 * フォーム定義に選択肢が存在するかチェックする
	 * 
	 * @see http://www.ethna.jp/ethna-document-dev_guide-view-form_helper.html#p19c658d
	 */
	function checkFormDefinitionOptionExists($name)
	{
		$values = $this->get($name);

		// option には、input タグの value 値をキーにして、表示するラベルを値にした 配列を指定します。
		// http://www.ethna.jp/ethna-document-dev_guide-view-form_helper.html#p19c658d
		$option = $this->form[$name]['option'];

		foreach ($values as $value) {
			if (isset($option[$value])) {
				continue;
			}
			
			$this->ae->add($name, "No such option.", E_FORM_INVALIDCHAR);
			return;
		}
	}

	/**
	 * pageID(PEAR::Parger用の1始まりの値)からpage(0始まり)を求める
	 */
	function getPageFromPageID()
	{
		$pageID = $this->get('pageID');
		if (!$pageID) $pageID = 1;
		
		$page = $pageID - 1;

		return $page;
	}

	/**
	 * 日付の書式を正規化する
	 * 
	 * ExcelでCSV編集・保存すると書式が変わってしまう現象への対策に使用する。
	 * @param string $value 日付（"2001-01-01 00:00:00"形式。"-"の代わりに"/"でも可。月・日・時・分・秒が1桁の場合は先頭のゼロを省略可。秒は省略可）
	 * @return string $value 日付（"2001-01-01 00:00:00"形式）
	 */
	static function normalizeDateString($value)
	{
		$year   = strtok($value, '-/');
		$month  = strtok('-/');
		$day    = strtok(' ');
		$hour   = strtok(':');
		$minute = strtok(':');
		$second = strtok(' ');
		
		if (strlen($hour) > 0) {
			return sprintf('%04d-%02d-%02d %02d:%02d:%02d', 
				$year, $month, $day, $hour, $minute, $second
			);
		} else {
			return sprintf('%04d-%02d-%02d', $year, $month, $day);
		}
	}
	
	/**
	 * CSV行の要素数を調整する
	 * 
	 * ExcelでCSVファイル編集すると、右端のカラムが空文字列だった場合に手前の区切りカンマまで消えてしまう為、不足分を補う。
	 * また、fgetcsvで最後に空行を取得した際に無視する処理も行なう。
	 * @param $row fgetcsvで取得した行
	 * @param $num 調整後のカラム数 
	 * @return array 調整された行
	 */
	static function adjustCsvRow($row, $num)
	{
		// 空行は無視する
		if ((count($row) == 1) && ($row[0] == '')) {
			return false;
		}
		
		// 空行は無視する（カラムが複数存在するがどのカラムも空の場合）
		if (count($row) > 1) {
			$empty = true;
			foreach ($row as $col) {
				if (strlen($col) > 0) {
					$empty = false;
					break;
				}
			}
			
			if ($empty) {
				return false;
			}
		}
		
		// カラム数が足りなかったら補う
		for ($i = 0; $i < $num; $i++) {
			if (!isset($row[$i])) {
				$row[$i] = '';
			}
		}
		
		return $row;
	}

	/**
	 *	URLのホスト指定が管理ページの環境と同じかをチェック
	 *
	 *	@access protected
	 */
	function checkHostEnv( $name )
	{
		$input_url = trim( $this->form_vars[$name] );
		if( strlen( $input_url ) === 0 )
		{	// 入力がなければチェックしない
			return;
		}

		// parse_urlが相対URLに対して使用できないが、checkURLがある程度対応してくれると思うので
		// ここでは相対URLかどうかのチェックはしない。なのでこの関数を使用する際はcheckURLとの
		// 併用が前提となる。
		$param = parse_url( $input_url );
		if( mb_strpos( $_SERVER["HTTP_HOST"], '.dev.' ) !== false )
		{	// 開発環境
			if(( strcmp( $param['host'], 'stg.jmja.jugmon.net' ) === 0 )||( strcmp( $param['host'], 'jmja.jugmon.net' ) === 0 ))
			{
				$this->ae->add( $name, $this->form_template[$name]['name'].'に開発環境以外の環境へのURLが設定されています', E_FORM_INVALIDVALUE);
			}
		}
		else if( mb_strpos( $_SERVER["HTTP_HOST"], '.stg.' ) !== false )
		{	// ステージング環境
			if(( strcmp( $param['host'], 'dev.jmja.jugmon.net' ) === 0 )||( strcmp( $param['host'], 'jmja.jugmon.net' ) === 0 ))
			{
				$this->ae->add( $name, $this->form_template[$name]['name'].'にステージング環境以外の環境へのURLが設定されています', E_FORM_INVALIDVALUE);
			}
		}
		else
		{	// 商用環境
			if(( strcmp( $param['host'], 'dev.jmja.jugmon.net' ) === 0 )||( strcmp( $param['host'], 'stg.jmja.jugmon.net' ) === 0 ))
			{
				$this->ae->add( $name, $this->form_template[$name]['name'].'に商用環境以外の環境へのURLが設定されています', E_FORM_INVALIDVALUE);
			}
		}
	}
}
// }}}

?>
