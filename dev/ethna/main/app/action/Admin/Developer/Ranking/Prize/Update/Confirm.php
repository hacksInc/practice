<?php
/**
 *  Admin/Developer/Ranking/Prize/Update/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../../Pp_Form_AdminDeveloperRanking.php';

/**
 *  admin_developer_ranking_prize_update_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingPrizeUpdateConfirm extends Pp_Form_AdminDeveloperRanking
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
	var $form = array(
		'id' => array(
			'required' => true,
		),
		'ranking_id' => array(
			'required' => true,
		),
		'distribute_start' => array(
			'required' => true,
		),
		'distribute_end' => array(
			'custom' => 'checkDistributeEnd',
		),
		'prize_type' => array(
			'required' => true,
			'min' => 1,
		),
		'prize_id' => array(
			'custom' => 'checkRequiredPrizeId',
		),
		'lv' => array(
			'custom' => 'checkRequiredLv',
		),
		'number' => array(
			'required' => true,
			'min' => 1,
		),
		'status' => array(
			'required' => true,
			'min' => 0,
		),
	);
}

/**
 *  admin_developer_ranking_prize_update_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingPrizeUpdateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_ranking_prize_update_confirm Action.
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
			if ($ret == 'admin_error_400') {
				$this->af->setApp( 'id', $this->af->get( 'id' ));
				$this->af->setApp( 'ranking_id', $this->af->get( 'ranking_id' ));
				$this->af->setApp( 'distribute_start', $this->af->get( 'distribute_start' ));
				$this->af->setApp( 'distribute_end', $this->af->get( 'distribute_end' ));
				$this->af->setApp( 'prize_type', $this->af->get( 'prize_type' ));
				$this->af->setApp( 'prize_id', $this->af->get( 'prize_id' ));
				$this->af->setApp( 'lv', $this->af->get( 'lv' ));
				$this->af->setApp( 'number', $this->af->get( 'number' ));
				$this->af->setApp( 'status', $this->af->get( 'status' ));
				return 'admin_developer_ranking_prize_update_input';
			} else {
				return $ret;
			}
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }

    /**
     *  admin_developer_ranking_prize_update_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_ranking_prize_update_confirm';
    }
}

?>