<?php
/**
 *  Admin/Developer/Ranking/Prize/Abort/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once 'Pp_ItemManager.php';
require_once dirname(__FILE__) . '/../../Pp_Form_AdminDeveloperRanking.php';

/**
 *  admin_developer_ranking_prize_abort_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingPrizeAbortExec extends Pp_Form_AdminDeveloperRanking
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
	var $form = array(
		'id',
		'ranking_id',
	);
}

/**
 *  admin_developer_ranking_prize_abort_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingPrizeAbortExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_ranking_prize_abort_exec Action.
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
			return 'admin_developer_ranking_prize_abort_input';
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }
*/
    /**
     *  admin_developer_ranking_prize_abort_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$ranking_prize_m =& $this->backend->getManager( 'AdminRankingPrize' );
		$present_m =& $this->backend->getManager( 'AdminPresent' );

		$db =& $this->backend->getDB();
		$db_cmn =& $this->backend->getDB('cmn');

		$id = $this->af->get( 'id' );
		$ranking_id = $this->af->get( 'ranking_id' );

		// 配布情報を取得
		$infos = $ranking_prize_m->getRankingPrizeDistributionForPrizeId( $id );

		// トランザクション開始
		$db->begin();
		$db_cmn->begin();

		$distribute_ids = array();

		// 指定賞品の全配布レコード(t_user_present)に対して配布中止の設定を行う
		foreach( $infos as $info )
		{
			$ret = $present_m->changePresentStatus(
				$info['present_id'], Pp_PresentManager::STATUS_DELETE, $info['unit']
			);
			if( !$ret || Ethna::isError($ret))
			{	// 更新エラー
				$db->rollback();
				$db_cmn->rollback();
				return 'admin_error_500';
			}
			array_push( $distribute_ids, $info['prize_distribute_id'] );
		}

		// 対象の配布管理情報(t_ranking_prize_distribution)を論理削除する
		$ret = $ranking_prize_m->abortRankingPrizeDistributionMulti( $distribute_ids );
		if( !$ret || Ethna::isError( $ret ))
		{	// 更新エラー
			$db->rollback();
			$db_cmn->rollback();
			return 'admin_error_500';
		}

		// 配布を中止する賞品を配布中止に設定
		$prize = $ranking_prize_m->getRankingPrizeForId( $id );
		$prize['status'] = Pp_AdminRankingPrizeManager::PRIZE_STATUS_STOP;
		$ret = $ranking_prize_m->updateRankingPrize( $prize );
		if( !$ret || Ethna::isError( $ret ))
		{	// 更新エラー
			$db->rollback();
			$db_cmn->rollback();
			return 'admin_error_500';
		}

		// 全て問題がなければコミット
		$db->commit();
		$db_cmn->commit();

		$this->af->setApp( 'ranking_id', $ranking_id );

		return 'admin_developer_ranking_prize_abort_exec';
    }

}

?>