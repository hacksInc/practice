<?php
/**
 *  Admin/Developer/Ranking/Delete/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Pp_Form_AdminDeveloperRanking.php';

/**
 *  admin_developer_ranking_delete_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingDeleteExec extends Pp_Form_AdminDeveloperRanking
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
 *  admin_developer_ranking_delete_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingDeleteExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_ranking_delete_exec Action.
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
     *  admin_developer_ranking_delete_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
	function perform()
	{
		$ranking_m  =& $this->backend->getManager( 'AdminRanking' );
		$ranking_id   = $this->af->get( 'ranking_id' );

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db_cmn =& $this->backend->getDB('cmn');
		$db->begin();
		$db_cmn->begin();

		// DBへ登録
		$ret = $ranking_m->deleteMasterRanking( $ranking_id );
		if( $ret === true )
		{
			$ret = $ranking_m->deleteRankingInfo( $ranking_id );
			if( $ret === true )
			{
				$ret = $ranking_m->deleteRankingData( $ranking_id );
			}
		}
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			$db_cmn->rollback();
			return 'admin_error_500';
		}

		// トランザクション完了
		$db->commit();
		$db_cmn->commit();

		return 'admin_developer_ranking_delete_exec';
    }
}

?>