<?php
/**
 *  Admin/Announce/Message/Help/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_announce_message_help_* で共通のアクションフォーム定義 */
class Pp_Form_AdminAnnounceMessageHelp extends Pp_AdminActionForm
{
	/**
	 * コンストラクタ
	 */
	function __construct(&$backend) {
		$form_template = array(
			'help_id' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_TEXT,   // Form type
				'name'        => 'ID', // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,             // Required Option(true/false)
				'min'         => 20000,            // Minimum value
				'max'         => 30000,            // Maximum value
			),

            'base_help_id' => array(
				// Form definition
				'type'        => VAR_TYPE_INT,     // Input type
				'form_type'   => FORM_TYPE_HIDDEN,   // Form type
				'name'        => 'ID', // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,             // Required Option(true/false)
				'min'         => 20000,            // Minimum value
				'max'         => 30000,            // Maximum value
			),

			'use_name' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING,  // Input type
				'form_type'   => FORM_TYPE_TEXT,   // Form type
				'name'        => '用途',           // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,             // Required Option(true/false)
				'min'         => 0,                // Minimum value
				'max'         => 256,              // Maximum value
			),

			'message' => array(
				// Form definition
				'type'        => VAR_TYPE_STRING,     // Input type
				'form_type'   => FORM_TYPE_TEXTAREA, // Form type
				'name'        => 'メッセージ', // Display name

				//  Validator (executes Validator by written order.)
				'required'    => true,            // Required Option(true/false)
				'min'         => 0,               // Minimum value
				'max'         => 512,               // Maximum value
			),

			'date_create' => array(
				// Form definition
				'type'        => VAR_TYPE_DATETIME, // Input type
				'form_type'   => FORM_TYPE_TEXT,    // Form type
				'name'        => '登録日時',        // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,             // Required Option(true/false)
				//'min'         => 10000,           // Minimum value
				//'max'         => 20000,           // Maximum value
			),

			'date_modified' => array(
				// Form definition
				'type'        => VAR_TYPE_DATETIME, // Input type
				'form_type'   => FORM_TYPE_TEXT,    // Form type
				'name'        => '更新日時',        // Display name

				//  Validator (executes Validator by written order.)
				'required'    => false,             // Required Option(true/false)
				//'min'         => 10000,           // Minimum value
				//'max'         => 20000,           // Maximum value
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
		$session_key = 'admin_announce_message_help_lu';
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
		return substr($this->get('lu'), 0, 2);
	}

	/** ua部分を取り出す */
	function getUa()
	{
		return substr($this->get('lu'), 2);
	}
	
	/** lu0クエリからlang部分を取り出す */
	function getLang0()
	{
		return substr($this->get('lu0'), 0, 2);
	}

	/** lu0クエリからua部分を取り出す */
	function getUa0()
	{
		return substr($this->get('lu0'), 2);
	}
}

/**
 *  admin_announce_message_help_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageHelpIndex extends Pp_Form_AdminAnnounceMessageHelp
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
 *  admin_announce_message_help_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageHelpIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_help_index Action.
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
     *  admin_announce_message_help_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_announce_message_help_index';
    }
}
