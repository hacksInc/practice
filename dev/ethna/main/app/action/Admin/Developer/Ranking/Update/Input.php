<?php
/**
 *  Admin/Developer/Ranking/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Pp_Form_AdminDeveloperRanking.php';

/**
 *  admin_developer_ranking_update_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingUpdateInput extends Pp_Form_AdminDeveloperRanking
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'ranking_id' => array(
			'require' => true,
		),
		'title',
		'subtitle',
		'target_type',
		'targets',
		'processing_type',
		'clear_target_dungeon_rank3',
		'clear_target_dungeon_rank4',
		'threshold',
		'view_higher',
		'view_lower',
		'view_ranking_top',
		'date_start',
		'date_end',
		'banner_url',
		'url',
    );
}

/**
 *  admin_developer_ranking_update_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingUpdateInput extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_ranking_update_input Action.
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
     *  admin_developer_ranking_update_input action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
	function perform()
	{
		
		return 'admin_developer_ranking_update_input';
	}
}

?>