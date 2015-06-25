<?php
/**
 *  Admin/Log/Cs/Quest/Info.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_quest_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsQuest extends Pp_Form_AdminLogCs
{
}

/**
 *  admin_log_cs_quest_info Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsQuestInfo extends Pp_Form_AdminLogCs
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'api_transaction_id' => array('require' => true),
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
 *  admin_log_cs_quest_info action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsQuestInfo extends Pp_AdminActionClass
{
    const MAX_PAGE_DATA_COUNT = '100';
    const MAX_DATA_COUNT = '2000';

    /**
     *  preprocess of admin_log_cs_quest_info Action.
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
            return 'admin_log_cs_quest_info';
        }
        return null;

    }

    /**
     *  admin_log_cs_quest_info action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {

        return 'admin_log_cs_quest_info';
    }
}
