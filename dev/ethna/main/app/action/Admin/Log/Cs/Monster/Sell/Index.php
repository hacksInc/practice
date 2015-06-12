<?php
/**
 *  Admin/Log/Cs/Monster/Sell/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/** admin_log_cs_monster_sell_* で共通のアクションフォーム定義 */
class Pp_Form_AdminLogCsMonsterSell extends Pp_Form_AdminLogCsMonster
{
}

/**
 *  admin_log_cs_monster_sell_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminLogCsMonsterSellIndex extends Pp_Form_AdminLogCsMonster
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'search_date_from',
        'search_date_to',
        'search_monster_id',
        'search_name',
        'search_name_option',
        'search_user_id',
        'search_flg',
        'start',
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
 *  admin_log_cs_monster_sell_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminLogCsMonsterSellIndex extends Pp_AdminActionClass
{
    const MAX_PAGE_DATA_COUNT = '100';
    const MAX_DATA_COUNT = '10000';

    /**
     *  preprocess of admin_log_cs_monster_sell_index Action.
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
            return 'admin_log_cs_monster_index';
        }

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('name_option', $this->af->get('search_name_option'));
        if ($search_flg == '1'){

            $date_from = $this->af->get('search_date_from');
            $date_to = $this->af->get('search_date_to');
            if (empty($date_from) && empty($date_to)){
                $this->af->setApp('search_flg', '');
                $msg = "検索日が入力されていません";
                $this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
                return 'admin_log_cs_monster_index';
            }

            if (!empty($date_from) && empty($date_to)){
                // 足りていないほうに日付を入力する
                $date_to = date('Y/m/d H:i:s', strtotime($date_from)+(60*60*24*14));
                $this->af->set('search_date_to', $date_to);
                return null;
            }

            if (empty($date_from) && !empty($date_to)){
                // 足りていないほうに日付を入力する
                $date_from = date('Y/m/d H:i:s', strtotime($date_to)-(60*60*24*14));
                $this->af->set('search_date_from', $date_from);
                return null;
            }

            if (Pp_Util::checkDateRange($date_from, $date_to, 14) === false) {
                $this->af->setApp('search_flg', '');
                $msg = "期間指定は14日以内で指定をしてください";
                $this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
                return 'admin_log_cs_monster_index';
            }

            if (Pp_Util::checkDateReversal($date_from, $date_to) === false) {
                $this->af->setApp('search_flg', '');
                $msg = "開始日と終了日が逆転しています";
                $this->ae->addObject('search_date_from', Ethna::raiseNotice($msg, E_FORM_INVALIDCHAR));
                return 'admin_log_cs_monster_index';
            }

        }
        return null;

    }

    /**
     *  admin_log_cs_monster_sell_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $limit = self::MAX_PAGE_DATA_COUNT;
        $offset = $this->af->get('start');
        $data_max_cnt = self::MAX_DATA_COUNT;

        $logdata_view_monster_m = $this->backend->getManager('LogdataViewMonster');
        $logdata_view_item_m = $this->backend->getManager('LogdataViewItem');
        $search_params = array(
            'date_from' => $this->af->get('search_date_from'),
            'date_to' => $this->af->get('search_date_to'),
            'monster_id' => $this->af->get('search_monster_id'),
            'name' => $this->af->get('search_name'),
            'name_option' => $this->af->get('search_name_option'),
            'user_id' => $this->af->get('search_user_id'),
        );

        $search_flg = $this->af->get('search_flg');
        $this->af->setApp('search_flg', $search_flg);
        $this->af->setApp('name_option', $this->af->get('search_name_option'));
        $this->af->setApp('create_file_path', '/admin/log/cs/monster/sell');

        if ($search_flg != '1'){
            return 'admin_log_cs_monster_sell_index';
        }

        $monster_sell_log_count = $logdata_view_monster_m->getMonsterSellLogDataCount($search_params);

        if ($monster_sell_log_count > $data_max_cnt) {
            $this->af->setApp('monster_sell_log_count', -1);
            return 'admin_log_cs_monster_sell_index';
        }
        if ($monster_sell_log_count == 0) {
            $this->af->setApp('monster_sell_log_count', 0);
            return 'admin_log_cs_monster_sell_index';
        }

        // モンスター売却ログ情報
        $monster_sell_log_data = $logdata_view_monster_m->getMonsterSellLogData($search_params, $limit, $offset);

        // api_transaction_idでひも付くモンスターログ情報を取得する(モンスター単位での売却価格を取得)
        foreach ($monster_sell_log_data['data'] as $k => $v) {
            $transaction_id_list[] = $v['api_transaction_id'];
        }

        // 売却時喪失モンスター情報
        $sell_monster_list = $logdata_view_monster_m->getMonsterDataByApiTransactionId($transaction_id_list);
        $monster_data_list = '';
        foreach($sell_monster_list['data'] as $k => $v){
            $monster_data_list[$v['api_transaction_id']][] = $v;
        }

        // 売却時取得アイテム情報
        $sell_item_list = $logdata_view_item_m->getItemDataByApiTransactionId($transaction_id_list);
        $item_data_list = '';
        foreach($sell_item_list['data'] as $k => $v){
            $item_data_list[$v['api_transaction_id']] = $v;
        }

        $pager = $logdata_view_monster_m->getPager($monster_sell_log_count, $offset, $limit);

        $this->af->setApp('monster_sell_log_list', $monster_sell_log_data['data']);
        $this->af->setApp('monster_sell_log_count', $monster_sell_log_count);
        $this->af->setApp('monster_data_list', $monster_data_list);
        $this->af->setApp('item_data_list', $item_data_list);
        
        return 'admin_log_cs_monster_sell_index';
    }
}
