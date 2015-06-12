<?php
/**
 *  Admin/Developer/Master/Consistency/Check.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_master_consistency_check Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterConsistencyCheck extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
       /*
        *  TODO: Write form definition which this action uses.
        *  @see http://ethna.jp/ethna-document-dev_guide-form.html
        *
        *  Example(You can omit all elements except for "type" one) :
        *
        *  'sample' => array(
        *      // Form definition
        *      'type'        => VAR_TYPE_INT,    // Input type
        *      'form_type'   => FORM_TYPE_TEXT,  // Form type
        *      'name'        => 'Sample',        // Display name
        *  
        *      //  Validator (executes Validator by written order.)
        *      'required'    => true,            // Required Option(true/false)
        *      'min'         => null,            // Minimum value
        *      'max'         => null,            // Maximum value
        *      'regexp'      => null,            // String by Regexp
        *      'mbregexp'    => null,            // Multibype string by Regexp
        *      'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        *
        *      //  Filter
        *      'filter'      => 'sample',        // Optional Input filter to convert input
        *      'custom'      => null,            // Optional method name which
        *                                        // is defined in this(parent) class.
        *  ),
        */
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
 *  admin_developer_master_consistency_check action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterConsistencyCheck extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_master_consistency_check Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */

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


    /**
     *  admin_developer_master_consistency_check action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {

        $quest_m = $this->backend->getManager('quest');
        $all_cnt = 0;

        // m_questマスタ
        $res = $quest_m->checkConsistencyQuestMasterForMapMaster();
        if (Ethna::isError($res)) {
            $this->ae->addObject(null, $res);
            return 'admin_developer_master_consistency_check';
        }
        $tmp['name'] = 'm_quest';
        $tmp['err_msg'] = $res['msg'];
        $tmp['cnt'] = $res['cnt'];
        $all_cnt = $all_cnt + $res['cnt'];
        $master_list[] = $tmp;

        // m_quest_enemyマスタ
        $res1 = $quest_m->checkConsistencyQuestEnemyMasterForQuestMaster();
        if (Ethna::isError($res1)) {
            $this->ae->addObject(null, $res1);
            return 'admin_developer_master_consistency_check';
        }
        $res2 = $quest_m->checkConsistencyQuestEnemyMasterForMonsterMaster();
        if (Ethna::isError($res2)) {
            $this->ae->addObject(null, $res2);
            return 'admin_developer_master_consistency_check';
        }
        $tmp['name'] = 'm_quest_enemy';
        $tmp['err_msg'] = array_merge($res1['msg'], $res2['msg']);
        $tmp['cnt'] = $res1['cnt'] + $res2['cnt'];
        $all_cnt = $all_cnt + $res1['cnt'] + $res2['cnt'];
        $master_list[] = $tmp;

        // m_areaマスタ
        $res = $quest_m->checkConsistencyAreaMasterForQuestMaster();
        if (Ethna::isError($res)) {
            $this->ae->addObject(null, $res);
            return 'admin_developer_master_consistency_check';
        }
        $tmp['name'] = 'm_area';
        $tmp['err_msg'] = $res['msg'];
        $tmp['cnt'] = $res['cnt'];
        $all_cnt = $all_cnt + $res['cnt'];
        $master_list[] = $tmp;

        // m_area_enemyマスタ
        $res1 = $quest_m->checkConsistencyAreaEnemyMasterForAreaMaster();
        if (Ethna::isError($res1)) {
            $this->ae->addObject(null, $res1);
            return 'admin_developer_master_consistency_check';
        }
        $res2 = $quest_m->checkConsistencyAreaEnemyMasterForQuestEnemyMaster();
        if (Ethna::isError($res2)) {
            $this->ae->addObject(null, $res2);
            return 'admin_developer_master_consistency_check';
        }
        $tmp['name'] = 'm_area_enemy';
        $tmp['err_msg'] = array_merge($res1['msg'], $res2['msg']);
        $tmp['cnt'] = $res1['cnt'] + $res2['cnt'];
        $all_cnt = $all_cnt + $res1['cnt'] + $res2['cnt'];
        $master_list[] = $tmp;
/*
        // m_area_boss_coefficientマスタ
        $res = $quest_m->checkConsistencyAreaBossCoefficientMasterForAreaMaster();
        if (Ethna::isError($res1)) {
            $this->ae->addObject(null, $res1);
            return 'admin_developer_master_consistency_check';
        }
        $tmp['name'] = 'm_area_boss_coefficient';
        $tmp['err_msg'] = $res['msg'];
        $tmp['cnt'] = $res['cnt'];
        $all_cnt = $all_cnt + $res['cnt'];
        $master_list[] = $tmp;
*/
        // m_battleマスタ
        $res = $quest_m->checkConsistencyBattleMasterForAreaMaster();
        if (Ethna::isError($res1)) {
            $this->ae->addObject(null, $res1);
            return 'admin_developer_master_consistency_check';
        }
        $tmp['name'] = 'm_battle';
        $tmp['err_msg'] = $res['msg'];
        $tmp['cnt'] = $res['cnt'];
        $all_cnt = $all_cnt + $res['cnt'];
        $master_list[] = $tmp;

        $this->af->setApp('master_list', $master_list);
        $this->af->setApp('all_cnt', $all_cnt);

        return 'admin_developer_master_consistency_check';
    }
}
