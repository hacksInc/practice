<?php
/**
 *	Admin/Developer/Gacha/Banner/Renew/Exec.php
 *
 *	@author 	{$author}
 *	@package	Pp
 *	@version	$Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/../Index.php';

/**
 *	admin_developer_gacha_banner_renew_exec Form implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Form_AdminDeveloperGachaBannerRenewExec extends Pp_Form_AdminDeveloperGacha
{
	/**
	 *	@access private
	 *	@var	array	form definition.
	 */
	var $form = array(
		'gacha_id',
	);
}

/**
 *	admin_developer_gacha_banner_renew_exec action implementation.
 *
 *	@author 	{$author}
 *	@access 	public
 *	@package	Pp
 */
class Pp_Action_AdminDeveloperGachaBannerRenewExec extends Pp_AdminActionClass
{
	/**
	 *	preprocess of admin_developer_gacha_banner_renew_exec Action.
	 *
	 *	@access public
	 *	@return string	  forward name(null: success.
	 *								  false: in case you want to exit.)
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
	 *	admin_developer_gacha_banner_renew_exec action implementation.
	 *
	 *	@access public
	 *	@return string	forward name.
	 */
	function perform()
	{
		$shop_m =& $this->backend->getManager( 'AdminShop' );
		$admin_m =& $this->backend->getManager( 'Admin' );

		$gacha_id = $this->af->get( 'gacha_id' );

		// おまけガチャの有無（true:あり false:なし）
		$extra_gacha = (( $this->af->get( 'extra_gacha' ) === 'false' )||
						( $this->af->get( 'extra_gacha' ) === false )) ? false : true;

		// 念の為、トランザクションのテーブルにゴミデータが残っていないかチェック
		$ret = $shop_m->isGachaListInfoExists( $gacha_id );
		if( $ret === true )
		{	// ゴミデータが残ってますよ
			return 'admin_error_500';
		}
		$ret = $shop_m->isGachaCategoryInfoExists( $gacha_id );
		if( $ret === true )
		{	// ゴミデータが残ってますよ
			return 'admin_error_500';
		}
		$ret = $shop_m->isGachaItemInfoExists( $gacha_id );
		if( $ret === true )
		{	// ゴミデータが残ってますよ
			return 'admin_error_500';
		}
		if( $extra_gacha === true )
		{
			$ret = $shop_m->isGachaExtraListInfoExists( $gacha_id );
			if( $ret === true )
			{	// ゴミデータが残ってますよ
				return 'admin_error_500';
			}
			$ret = $shop_m->isGachaExtraItemInfoExists( $gacha_id );
			if( $ret === true )
			{	// ゴミデータが残ってますよ
				return 'admin_error_500';
			}
			$ret = $shop_m->isGachaExtraCategoryInfoExists( $gacha_id );
			if( $ret === true )
			{	// ゴミデータが残ってますよ
				return 'admin_error_500';
			}
		}

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		// 一次抽選テーブルの作成
		$ret = $shop_m->createGachaCategoryInfo( $gacha_id );
		if( $ret !== true )
		{	// 作成に失敗
			$db->rollback();
			return 'admin_error_500';
		}

		// 二次抽選テーブルの作成
		$ret = $shop_m->createGachaItemInfo( $gacha_id );
		if( $ret !== true )
		{	// 作成に失敗
			$db->rollback();
			return 'admin_error_500';
		}

		// ガチャ管理情報の作成
		$ret = $shop_m->createGachaListInfo( $gacha_id );
		if( $ret !== true )
		{	// 作成に失敗
			$db->rollback();
			return 'admin_error_500';
		}

		if( $extra_gacha === true )
		{	// おまけガチャあり
			// おまけガチャ一次抽選テーブルの作成
			$ret = $shop_m->createGachaExtraCategoryInfo( $gacha_id );
			if( $ret !== true )
			{	// 作成に失敗
				$db->rollback();
				return 'admin_error_500';
			}

			// おまけガチャ二次抽選テーブルの作成
			$ret = $shop_m->createGachaExtraItemInfo( $gacha_id );
			if( $ret !== true )
			{	// 作成に失敗
				$db->rollback();
				return 'admin_error_500';
			}

			// おまけガチャ管理情報の作成
			$ret = $shop_m->createGachaExtraListInfo( $gacha_id );
			if( $ret !== true )
			{	// 作成に失敗
				$db->rollback();
				return 'admin_error_500';
			}
		}

		// トランザクション終了
		$db->commit();

		$this->af->setApp('gacha_id', $gacha_id);
		$this->af->setApp('extra_gacha', $extra_gacha);

		// ログ
		$admin_m->addAdminOperationLog( '/developer/gacha', 'banner_log', array(
			'gacha_id' => $gacha_id,
			'user'	   => $this->session->get('lid'),
			'action'   => $this->backend->ctl->getCurrentActionName(),
		));
		
		return 'admin_developer_gacha_banner_renew_exec';
	}
}

?>