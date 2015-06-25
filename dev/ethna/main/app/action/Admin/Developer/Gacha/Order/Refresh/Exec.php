<?php
/**
 *  Admin/Developer/Gacha/Order/Refresh/Exec.php
 *
 *  ※Api/Shop/Gacha/Execにも、このアクションで行っているのと同様にオーダーリストを生成する処理があるので、もし改修する際は要注意。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_order_refresh_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaOrderRefreshExec extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
//		'gacha_id',
    );
}

/**
 *  admin_developer_gacha_order_refresh_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaOrderRefreshExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_order_refresh_exec Action.
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
     *  admin_developer_gacha_order_refresh_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
return 'admin_error_500';
/*
		$shop_m =& $this->backend->getManager('AdminShop');
		
		//指定されたガチャID
		$gacha_id = $this->af->get('gacha_id');

		$gacha_data = $shop_m->getGachaListId($gacha_id);
		
		//MySQLトランザクション開始	
		$db =& $this->backend->getDB();
		$transaction = ($db->db->transCnt == 0); // トランザクション開始するか
		if ($transaction) $db->begin();

		//管理画面でもロック（APIの方が動作しているかもしれないので）
		$gacha_info = $shop_m->getGachaOrderInfo4Update($gacha_id);
		
		//オーダー情報が無い場合は
		if ($gacha_info == NULL) {
			//オーダーの初期情報を作る
			$gacha_info = array(
					'gacha_id'			=> $gacha_id,
					'active_order_id'	=> 0,
					'created_order_id'	=> 0,
					'list_idx'			=> 0,
					'list_max'			=> 0,
					'gacha_cnt'			=> 0,
				//	'deck'				=> 2,
				);
			$ret = $shop_m->setGachaOrderInfo($gacha_info);
			$gacha_info = $shop_m->getGachaOrderInfo4Update($gacha_id);//再度ロック
			$list_idx = $gacha_info['list_idx'];
		}

		//オーダーを進める
		$gacha_info['active_order_id']++;
		$order_id = $gacha_info['active_order_id'];
		$list_idx = $gacha_info['list_idx'] = 0;
		
		//リスト生成
		$gacha_list = $shop_m->makeGachaList($gacha_id, $gacha_data['create_deck']);
		//１行ずつ保存していく
		foreach($gacha_list as $key => $val) {
			$columns = array(
							'gacha_id'		=> $gacha_id,
							'order_id'		=> $order_id,
							'list_id'		=> $key,
							'rarity'		=> $val['rarity'],
							'monster_id'	=> $val['monster_id'],
							'user_id'		=> null,
							'date_draw'		=> null,
						);
			$ret = $shop_m->setGachaDraw($columns);
		}
		$gacha_info['created_order_id']++;
		$gacha_info['list_idx'] = 0;
		$gacha_info['list_max'] = count($gacha_list);
			
		$ret = $shop_m->setGachaOrderInfo($gacha_info);
		if (!$ret || Ethna::isError($ret)) {
			if ($transaction) $db->rollback();
			return 'admin_error_500';
		}

		//MySQLトランザクション完了
		if ($transaction) $db->commit();
		
        return 'admin_developer_gacha_order_refresh_exec';
*/
    }
}

?>