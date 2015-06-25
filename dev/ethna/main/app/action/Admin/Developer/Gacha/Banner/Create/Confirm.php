<?php
/**
 *  Admin/Developer/Gacha/Banner/Create/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *  admin_developer_gacha_banner_create_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperGachaBannerCreateConfirm extends Pp_Form_AdminDeveloperGacha
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
		'banner_image' => array('required' => false),
		'gacha_id'   => array('required' => false),
		'lang',
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
 *  admin_developer_gacha_banner_create_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperGachaBannerCreateConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_gacha_banner_create_confirm Action.
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
     *  admin_developer_gacha_banner_create_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$shop_m =& $this->backend->getManager('AdminShop');

		$banner_image = $this->af->get('banner_image');
		$gacha_id     = $this->af->get('gacha_id');
		
		if ($gacha_id) {
			$row = $shop_m->getGachaListId($gacha_id);
			if (!$row) {
				return 'admin_error_500';
			}
		}
		
		$confirm_uniq = uniqid();
		
		// 確認画面→完了画面引き渡し用のバナー画像ファイルのサーバ内パス
		$banner_filename = $this->af->getAdminTmpBannerFilename($confirm_uniq);

//		umask(0002);
		umask(0000);
		if ($banner_image && ($banner_image['error'] == UPLOAD_ERR_OK) && ($banner_image['size'] > 0)) {
			move_uploaded_file($banner_image['tmp_name'], $banner_filename);
		} else if (!$gacha_id) {
			$this->af->ae->add(null, "バナー画像を選択して下さい。");
			return 'admin_error_400';
		} else {
			// 複製の場合
			copy($shop_m->getGachaBannerPath($gacha_id), $banner_filename);
		}
		
		// バナー画像のData URLスキーム表記
		$banner_data = 'data:image/png;base64,' . base64_encode(file_get_contents($banner_filename));
		
		// テンプレート変数アサイン
		$this->af->setApp('confirm_uniq', $confirm_uniq);
		$this->af->setApp('banner_data',  $banner_data);
		if (isset($row)) $this->af->setApp('row', $row);

		return 'admin_developer_gacha_banner_create_confirm';
    }
}

?>