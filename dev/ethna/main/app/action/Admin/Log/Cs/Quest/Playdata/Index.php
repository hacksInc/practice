<?php
/**
 *  Admin/Log/Cs/Quest/Playdata/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../Index.php';

/** admin_log_cs_quest_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsQuest extends Pp_Form_AdminLogCs
{
    /**
     * コンストラクタ
     */
    function __construct(&$backend) {
        $form_template = array(
            'play_id' => array(
                // Form definition
                'type'        => VAR_TYPE_INT,     // Input type
                'form_type'   => FORM_TYPE_TEXT,   // Form type
                'name'        => 'PLAY_ID', // Display name

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
 *  admin_log_cs_quest_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsQuestPlaydataIndex extends Pp_Form_AdminLogCsQuest
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'play_id',
        'user_id',
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
 *  admin_log_cs_quest_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsQuestPlaydataIndex extends Pp_AdminActionClass
{

    /**
     *  preprocess of admin_log_cs_quest_playdata_index Action.
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
            return 'admin_log_cs_quest_playdata_index';
        }

        return null;

    }

    /**
     *  admin_log_cs_quest_playdata_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {

        $logdata_view_m = $this->backend->getManager('LogdataViewQuest');
        $search_params = array(
            'play_id' => $this->af->get('play_id'),
//            'user_id' => $this->af->get('user_id'),
        );

        $quest_log_data = $logdata_view_m->getQuestPlayLogData($search_params);
        $this->af->setApp('quest_log_list', $quest_log_data['data']);
        $this->af->setApp('quest_log_count', $quest_log_data['count']);

        // api_transaction_idでひも付くモンスターログ情報を取得する(モンスター単位での売却価格を取得)
        foreach ($quest_log_data['data'] as $k => $v) {
            if ($v['quest_st'] == '0') {
                $api_transaction_id = $v['api_transaction_id'];
                $this->af->setApp('quest_start_log', $v);
            }
            if ($v['quest_st'] == '1' || $v['quest_st'] == '2') {
                $this->af->setApp('quest_end_log', $v);
            }
        }

        $quest_team_log_data = $logdata_view_m->getQuestTeamDataByApiTransactionId($api_transaction_id);
        $this->af->setApp('quest_team_log_list', $quest_team_log_data['data']);
        $this->af->setApp('quest_team_log_count', $quest_team_log_data['count']);

        $quest_monster_log_data = $logdata_view_m->getQuestMonsterDataByApiTransactionId($api_transaction_id);
        $this->af->setApp('quest_monster_log_list', $quest_monster_log_data['data']);
        $this->af->setApp('quest_monster_log_count', $quest_monster_log_data['count']);

        return 'admin_log_cs_quest_playdata_index';
    }
}
