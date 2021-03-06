<?php
/**
 *  Admin/Developer/Gacha/Draw/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Pp_Form_AdminDeveloperGacha.php';

/**
 *  admin_developer_gacha_draw_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaDrawIndex extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'gacha_id',
		'date_draw_start',
		'date_draw_end',
		'pageID',
    );
}

/**
 *  admin_developer_gacha_draw_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaDrawIndex extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_draw_index Action.
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

		$date_draw_end = $this->af->get('date_draw_end');
		if (!$date_draw_end) {
			$date_draw_end = date('Y-m-d H:i', $_SERVER['REQUEST_TIME'] - 60) . ':00';
			$this->af->set('date_draw_end', $date_draw_end);
		}
		
		$date_draw_start = $this->af->get('date_draw_start');
		if (!$date_draw_start) {
			$date_draw_start = date('Y-m-d', strtotime($date_draw_end) - 86400 * 14) . ' 00:00:00';
			$this->af->set('date_draw_start', $date_draw_start);
		}
    }

    /**
     *  admin_developer_gacha_draw_index action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'admin_developer_gacha_draw_index';
    }
}
