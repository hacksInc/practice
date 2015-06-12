<?php
/**
 *  Admin/Log/Cs/Friend/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_friend_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsFriend extends Pp_Form_AdminLogCs
{
    /**
     * コンストラクタ
     */
    function __construct(&$backend) {
        $form_template = array(
            'search_friend_name' => array(
                // Form definition
                'type'        => VAR_TYPE_STRING,     // Input type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'フレンドニックネーム', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
            'search_friend_name_option' => array(
                // Form definition
                'type'        => VAR_TYPE_INT,     // Input type
                //'option'      => array(1 => '完全一致検索する'),     // Input type
                //'form_type'   => FORM_TYPE_CHECKBOX,   // Form type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'フレンドニックネーム検索オプション', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
            'search_friend_user_id' => array(
                // Form definition
                'type'        => VAR_TYPE_STRING,     // Input type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'フレンドユーザーID', // Display name

                //  Validator (executes Validator by written order.)
                'required'    => false,             // Required Option(true/false)
                //'min'         => 30000,            // Minimum value
                //'max'         => 40000,            // Maximum value
            ),
        );

        foreach ($form_template as $key => $value) {
            $this->form_template[$key] = $value;
        }
        parent::__construct($backend);
    }
}

/**
 *  admin_log_cs_friend_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsFriendIndex extends Pp_Form_AdminLogCsFriend
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from',
        'search_date_to',
        'search_name',
        'search_name_option',
        'search_user_id',
        'search_friend_name',
        'search_friend_name_option',
        'search_friend_user_id',
        'search_processing_type_name',
        'search_flg',
        'start',
    );
}

/**
 *  admin_log_cs_friend_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsFriendIndex extends Pp_Action_AdminLogCsIndex
{
    const MAX_PAGE_DATA_COUNT = '100';
    const MAX_DATA_COUNT = '10000';
    const MAX_TERM_DAY = 14;

    /**
     *  preprocess of admin_log_cs_friend_index Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {

        // アクセス制御
        if ($this->must_login && $this->must_permission) {
            $ret = $this->permit();
            if ($ret) {
                return $ret;
            }
        }

        if ($this->af->validate() > 0) {
            return 'admin_log_cs_friend_index';
        }

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('name_option', $this->af->get('search_name_option'));
        if ($search_flg == '1'){
            if ($this->checkLogSearchDate(self::MAX_TERM_DAY) === false){
                return 'admin_log_cs_friend_index';
            }
        }
        return null;

    }

    /**
     *  admin_log_cs_friend_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $limit = self::MAX_PAGE_DATA_COUNT;
        $offset = $this->af->get('start');
        $data_max_cnt = self::MAX_DATA_COUNT;

        $logdata_view_m = $this->backend->getManager('LogdataViewFriend');
        $search_params = array(
            'date_from' => $this->af->get('search_date_from'),
            'date_to' => $this->af->get('search_date_to'),
            'name' => $this->af->get('search_name'),
            'name_option' => $this->af->get('search_name_option'),
            'user_id' => $this->af->get('search_user_id'),
            'friend_name' => $this->af->get('search_friend_name'),
            'friend_name_option' => $this->af->get('search_friend_name_option'),
            'friend_user_id' => $this->af->get('search_friend_user_id'),
            'processing_type_name' => $this->af->get('search_processing_type_name'),
        );

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('search_flg', $search_flg);
        $this->af->setApp('name_option', $this->af->get('search_name_option'));

        if ($search_flg == '1'){
            $friend_log_count = $logdata_view_m->getFriendLogDataCount($search_params);
            if ($friend_log_count > $data_max_cnt) {
                $this->af->setApp('friend_log_count', -1);
                return 'admin_log_cs_friend_index';
            }
            $friend_log_data = $logdata_view_m->getFriendLogData($search_params, $limit, $offset);
            $pager = $logdata_view_m->getPager($friend_log_count, $offset, $limit);

            $this->af->setApp('friend_log_list', $friend_log_data['data']);
            $this->af->setApp('friend_log_count', $friend_log_count);
        }
        $this->af->setApp('create_file_path', '/admin/log/cs/friend');
        return 'admin_log_cs_friend_index';
    }
}
