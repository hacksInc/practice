<?php
/**
 *  Admin/Announce/Invite/Distribution/Content/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_announce_invite_distribution_content_* で共通のアクションフォーム定義 */
class Pp_Form_AdminAnnounceInviteDistributionContent extends Pp_AdminActionForm
{
	var $form_template = array(
/*
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
				'en1' => 'iOS(英語)',
				'es1' => 'iOS(スペイン語)',
				'ja2' => 'Android(日本語)',
				'en2' => 'Android(英語)',
				'es2' => 'Android(スペイン語)',
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
			'custom'      => 'checkTagsBoldForecolor', // Optional method name which
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
		
*/
	);
	
	/**
	 * 不正タグチェック（TinyMCEのbold,forecolorボタンが生成するタグのみ許可）
	 * 
	 * 許可するタグは以下の通り。
	 * <p></p><strong></strong><span style="color: #xxxxxx;"></span>
	 */
/*
	function checkTagsBoldForecolor($name)
	{
		$source = $this->form_vars[$name];

		$check1 = strip_tags($source);
		$tmp = str_replace(array('<p>', '</p>', '<br />', '<strong>', '</strong>', '</span>'), '', $source);
		$check2 = preg_replace('/<span style="color: #[a-f0-9]+;">/', '', $tmp);
		if (strcmp($check1, $check2) !== 0) {
			$this->ae->add($name, "不正なHTMLタグがあります。", E_ERROR_DEFAULT);
		}
	}
*/
}

/**
 *  admin_announce_invite_distribution_content_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceInviteDistributionContentIndex extends Pp_Form_AdminAnnounceInviteDistributionContent
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
/*
   var $form = array(
		'lu' => array('filter' => 'sync_lu'),
    );
*/

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
 *  admin_announce_invite_distribution_content_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceInviteDistributionContentIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_invite_distribution_content_index Action.
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
     *  admin_announce_invite_distribution_content_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_announce_invite_distribution_content_index';
    }
}

?>