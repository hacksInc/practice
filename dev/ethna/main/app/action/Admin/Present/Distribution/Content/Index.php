<?php
/**
 *  Admin/Present/Distribution/Content/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/** admin_present_distribution_content_* で共通のアクションフォーム定義 */
class Pp_Form_AdminPresentDistributionContent extends Pp_AdminActionForm
{
	var $form_template = array(
	);

	/**
	 * 不正タグチェック（TinyMCEのbold,forecolorボタンが生成するタグのみ許可）
	 *
	 * 許可するタグは以下の通り。
	 * <p></p><strong></strong><span style="color: #xxxxxx;"></span>
	 */
/*
	function checkTagsBoldForecolor($name)
	{
		$source = $this->form_vars[$name];

		$check1 = strip_tags($source);
		$tmp = str_replace(array('<p>', '</p>', '<br />', '<strong>', '</strong>', '</span>'), '', $source);
		$check2 = preg_replace('/<span style="color: #[a-f0-9]+;">/', '', $tmp);
		if (strcmp($check1, $check2) !== 0) {
			$this->ae->add($name, "不正なHTMLタグがあります。", E_ERROR_DEFAULT);
		}
	}
 */
}

/**
 *  admin_present_distribution_content_index Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminPresentDistributionContentIndex extends Pp_Form_AdminPresentDistributionContent
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
/*
   var $form = array(
	);
 */

	/**
	 *  Form input value convert filter : sample
	 *
	 *  @access protected
	 *  @param  mixed   $value  Form Input Value
	 *  @return mixed           Converted result.
	 */
	/*
	function _filter_sample($value)
	{
		//  convert to upper case.
		return strtoupper($value);
	}
	 */
}

/**
 *  admin_present_distribution_content_index action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminPresentDistributionContentIndex extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_present_distribution_content_index Action.
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
	 *  admin_present_distribution_content_index action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		return 'admin_present_distribution_content_index';
	}
}

