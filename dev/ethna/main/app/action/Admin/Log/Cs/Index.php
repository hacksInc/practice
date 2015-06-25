<?php
/**
 *  Admin/Log/Cs/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_announce_message_helpbar_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCs extends Pp_AdminActionForm
{
    /**
     * コンストラクタ
     */
    function __construct(&$backend) {
        $form_template = array(
            'api_transaction_id' => array(
                // Form definition
                'type'        => VAR_TYPE_STRING,     // Input type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'API_TRANSACTION_ID', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
            'search_date_from' => array(
                // Form definition
                'type'        => VAR_TYPE_DATETIME,     // Input type
                'form_type'   => FORM_TYPE_TEXT, // Form type
                'name'        => '検索日(開始日)', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
                //'min'         => 10000,               // Minimum value
                //'max'         => 20000,               // Maximum value
            ),

            'search_date_to' => array(
                // Form definition
                'type'        => VAR_TYPE_DATETIME,     // Input type
                'form_type'   => FORM_TYPE_TEXT, // Form type
                'name'        => '検索日(終了日)', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,            // Required Option(true/false)
                //'min'         => 10000,               // Minimum value
                //'max'         => 20000,               // Maximum value
            ),
            'search_name' => array(
                // Form definition
                'type'        => VAR_TYPE_STRING,     // Input type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'ニックネーム', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
            'search_name_option' => array(
                // Form definition
                'type'        => VAR_TYPE_INT,     // Input type
                //'option'      => array(1 => '完全一致検索する'),     // Input type
                //'form_type'   => FORM_TYPE_CHECKBOX,   // Form type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'ニックネーム検索オプション', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
            'search_user_id' => array(
                // Form definition
                'type'        => VAR_TYPE_STRING,     // Input type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'ユーザーID', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
            'search_processing_type_name' => array(
                // Form definition
                'type'        => VAR_TYPE_STRING,     // Input type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => '処理理由', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
            'search_flg' => array(
                // Form definition
                'type'        => VAR_TYPE_INT,     // Input type
                'form_type'   => FORM_TYPE_HIDDEN,   // Form type
                'name'        => '検索フラグ', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
            'start' => array(
                'type'          => VAR_TYPE_STRING,
                'form_type'     => FORM_TYPE_HIDDEN,
            ),
        );

        foreach ($form_template as $key => $value) {
            $this->form_template[$key] = $value;
        }

        parent::__construct($backend);
    }

}

/**
 *  admin_log_cs_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsIndex extends Pp_Form_AdminLogCs
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
 *  admin_log_cs_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_log_cs_index Action.
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
     *  admin_log_cs_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_log_cs_index';
    }

    /**
     *
     *
     */
    protected function checkLogSearchDate($max_term_day = 14)
    {

        $date_from = $this->af->get('search_date_from');
        $date_to = $this->af->get('search_date_to');
        if (empty($date_from) && empty($date_to)){
            $this->af->setApp('search_flg', '');
            $msg = "検索日が入力されていません";
            $this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
            return false;
        }

        $max_term_second = 60 * 60 * 24 * intval($max_term_day);

        if (!empty($date_from) && empty($date_to)){
            // 足りていないほうに日付を入力する
            $date_to = date('Y/m/d H:i:s', strtotime($date_from)+$max_term_second);
            $this->af->set('search_date_to', $date_to);
            return true;
        }

        if (empty($date_from) && !empty($date_to)){
            // 足りていないほうに日付を入力する
            $date_from = date('Y/m/d H:i:s', strtotime($date_to)-$max_term_second);
            $this->af->set('search_date_from', $date_from);
            return true;
        }

        if (Pp_Util::checkDateRange($date_from, $date_to, $max_term_day) === false) {
            $this->af->setApp('search_flg', '');
            $msg = "期間指定は{$max_term_day}日以内で指定をしてください";
            $this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
            return false;
        }

        if (Pp_Util::checkDateReversal($date_from, $date_to) === false) {
            $this->af->setApp('search_flg', '');
            $msg = "開始日と終了日が逆転しています";
            $this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
            return false;
        }

        return true;
    }
}
