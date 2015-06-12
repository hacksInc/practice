<?php
/**
 *  Admin/Developer/Ranking/Prize/Distribute/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once 'Pp_ItemManager.php';
require_once dirname(__FILE__) . '/../../Pp_Form_AdminDeveloperRanking.php';

/**
 *  admin_developer_ranking_prize_distribute_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingPrizeDistributeExec extends Pp_Form_AdminDeveloperRanking
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
	var $form = array(
		'ranking_id',
	);
}

/**
 *  admin_developer_ranking_prize_distribute_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingPrizeDistributeExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_ranking_prize_distribute_exec Action.
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
			return 'admin_developer_ranking_prize_distribute_input';
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }
*/
    /**
     *  admin_developer_ranking_prize_distribute_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$ranking_m =& $this->backend->getManager('AdminRanking');
		$ranking_prize_m =& $this->backend->getManager('AdminRankingPrize');
		$present_m = $this->backend->getManager('Present');
		$unit_m = $this->backend->getManager('Unit');
		$ranking_id = $this->af->get( 'ranking_id' );

		// 配布していない賞品のリストを取得
		$prize = $ranking_prize_m->getRankingPrizeNondistribution( $ranking_id );

		$db =& $this->backend->getDB();
		$db_cmn =& $this->backend->getDB('cmn');

		foreach( $prize as $v )
		{
			// トランザクション開始
			$db->begin();

			// 配布対象ユーザーの取得
			$user_ids = $ranking_m->getRankingUserIdForRank( $ranking_id, $v['distribute_start'], $v['distribute_end'] );

			// 対象ユーザーに配布
			foreach( $user_ids as $v2 )
			{
				//プレゼントのデータをセット
				$columns = array(
							'user_id_to'   => $v2['user_id'],
							'comment_id'   => Pp_PresentManager::COMMENT_RANKING,
							'comment'      => '',
							'item_id'      => $v['prize_id'],
							'lv'           => (( empty( $v['lv'] ) === true ) ? 0 : $v['lv'] ),
							'badge_expand' => (( empty( $v['badge_expand'] ) === true ) ? 0 : $v['badge_expand'] ),
							'badges'       => (( empty( $v['badges'] ) === true ) ? '' : $v['badges'] ),
							'number'       => $v['number'],
						);

				// 賞品タイプ別に処理を分岐
				switch( intval( $v['prize_type'] ))
				{
					case Pp_AdminRankingPrizeManager::PRIZE_TYPE_ITEM:
						$columns['type'] = Pp_PresentManager::TYPE_ITEM;
						break;
					case Pp_AdminRankingPrizeManager::PRIZE_TYPE_MONSTER:
						$columns['type'] = Pp_PresentManager::TYPE_MONSTER;
						break;
					case Pp_AdminRankingPrizeManager::PRIZE_TYPE_COIN:
						$columns['type'] = Pp_PresentManager::TYPE_MEDAL;
						break;
					case Pp_AdminRankingPrizeManager::PRIZE_TYPE_MEDAL:
						$columns['type'] = Pp_PresentManager::TYPE_MAGICAL_MEDAL;
						break;
					case Pp_AdminRankingPrizeManager::PRIZE_TYPE_BADGE:
						$columns['type'] = Pp_PresentManager::TYPE_MAGICAL_BADGE;
						break;
					case Pp_AdminRankingPrizeManager::PRIZE_TYPE_MATERIAL:
						$columns['type'] = Pp_PresentManager::TYPE_MAGICAL_MATERIAL;
						break;
					default:
				}

				// プレゼントを贈る
				$unit = $unit_m->cacheGetUnitFromUserId( $v2['user_id'] );	// ユーザーのユニットを取得

				$ret = $present_m->setUserPresent(Pp_PresentManager::PPID_FROM_ADMIN, Pp_PresentManager::ID_NEW_PRESENT, $columns, $unit);
				if (!$ret || Ethna::isError($ret)) {
					$db->rollback();
					$db_cmn->rollback();
					return 'admin_error_500';
				}

				// 賞品配布管理テーブルに登録
				$ret = $ranking_prize_m->insertRankingPrizeDistribution( $v['id'], $ret, $unit );
				if( !$ret || Ethna::isError( $ret ))
				{
					$db->rollback();
					$db_cmn->rollback();
					return 'admin_error_500';
				}
			}

			// 配布データのステータスを更新
			$v['status'] = Pp_AdminRankingPrizeManager::PRIZE_STATUS_BUSY;
			unset( $v['date_created'] );
			unset( $v['date_modified'] );
			$ret = $ranking_prize_m->updateRankingPrize( $v );
			if( !$ret || Ethna::isError( $ret ))
			{
				$db->rollback();
				$db_cmn->rollback();
				return 'admin_error_500';
			}

			// 賞品が対象のユーザーに渡す準備ができたらコミット
			$db->commit();
			$db_cmn->commit();
		}

		$this->af->setApp( 'ranking_id', $ranking_id );

		return 'admin_developer_ranking_prize_distribute_exec';
    }
}

?>