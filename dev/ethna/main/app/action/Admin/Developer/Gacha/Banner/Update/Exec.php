<?php
/**
 *  Admin/Developer/Gacha/Banner/Update/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_gacha_banner_update_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaBannerUpdateExec extends Pp_Form_AdminDeveloperGacha
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'type',
		'price',
		'comment',
		'sort_list',
		'date_start',
		'date_end',
		'confirm_uniq',
		'banner_image' => array('required' => false),
		'gacha_id',
		'lang',
		'banner_uploaded',
		'ua',
		'banner_type',
		'banner_url',
		'width',
		'height',
		'position_x',
		'position_y',
    );
}

/**
 *  admin_developer_gacha_banner_update_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaBannerUpdateExec extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_banner_update_exec Action.
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
		
		$banner_uploaded = $this->af->get('banner_uploaded');
		if ($banner_uploaded) {
			$confirm_uniq = $this->af->get('confirm_uniq');
			$banner_filename = $this->af->getAdminTmpBannerFilename($confirm_uniq);
			
			if (!(file_exists($banner_filename) && (filesize($banner_filename) > 0))) {
				return 'admin_error_500';
			}
			
			$this->af->setApp('banner_filename', $banner_filename);
		}
    }

    /**
     *  admin_developer_gacha_banner_update_exec action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$shop_m =& $this->backend->getManager('AdminShop');
		$admin_m =& $this->backend->getManager('Admin');

		$gacha_id = $this->af->get('gacha_id');

		$banner_filename = $this->af->getApp('banner_filename');
		
		$row = $shop_m->getGachaListId($gacha_id);
		if (!$row) {
			return 'admin_error_500';
		}

		$banner_data = null;
		if ($banner_filename) {
			$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));
		}

		$columns = array(
			'gacha_id'    => $gacha_id,
			'type'        => $this->af->get('type'),
			'price'       => $this->af->get('price'),
			'comment'     => $this->af->get('comment'),
			'sort_list'   => $this->af->get('sort_list'),
			'ua'          => $this->af->get('ua'),
			'banner_type' => $this->af->get('banner_type'),
			'banner_url'  => $this->af->get('banner_url'),
			'width'       => $this->af->get('width'),
			'height'      => $this->af->get('height'),
			'position_x'  => $this->af->get('position_x'),
			'position_y'  => $this->af->get('position_y'),
			'date_start'  => $this->af->get('date_start'),
			'date_end'    => $this->af->get('date_end'),
			'account_upd' => $this->session->get('lid'),
		);

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// DBへ登録
		$ret = $shop_m->updateGachaList($columns);
		if (!$ret || Ethna::isError($ret)) {
			$db->rollback();
			return 'admin_error_500';
		}

		// ファイルを登録
		if ($banner_filename) {
			$banner_dest = $shop_m->getGachaBannerPath($gacha_id);
//			umask(0002);
			if (!rename($banner_filename, $banner_dest)) {
				$db->rollback();
				return 'admin_error_500';
			}
		}
			
		// トランザクション完了
		$db->commit();
		
		// ログ
		$log_columns = $columns;
		if ($banner_data) {
			$log_columns['banner_data'] = $banner_data;
		}
		
		$admin_m->addAdminOperationLog('/developer/gacha', 'banner_log', 
			array_merge(array(
				'user'   => $this->session->get('lid'),
				'action' => $this->backend->ctl->getCurrentActionName(),
			), $log_columns)
		);
		
        return 'admin_developer_gacha_banner_update_exec';
    }
}

?>