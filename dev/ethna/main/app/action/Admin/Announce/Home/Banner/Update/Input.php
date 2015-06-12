<?php
/**
 *  Admin/Announce/Home/Banner/Update/Input.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_announce_home_banner_update_input Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceHomeBannerUpdateInput extends Pp_Form_AdminAnnounceHomeBanner
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'hbanner_id',
    );
}

/**
 *  admin_announce_home_banner_update_input action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceHomeBannerUpdateInput extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_home_banner_update_input Action.
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
     *  admin_announce_home_banner_update_input action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$news_m =& $this->backend->getManager('AdminNews');
		$hbanner_id = $this->af->get('hbanner_id');

		$row = $news_m->getHomeBanner($hbanner_id);
		if (!$row) {
			return 'admin_error_500';
		}
error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . $hbanner_id);
error_log('DEBUG:' . __FILE__ . ':' . __LINE__ . ':' . var_export($row, true));

		$this->af->setApp('row', $row);
		
        return 'admin_announce_home_banner_update_input';
    }
}

?>