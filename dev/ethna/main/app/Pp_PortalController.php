<?php
/**
 *  Pp_Controller.php
 *
 *  コントローラークラス
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/** including application library. */
require_once "Pp_Controller.php";
require_once "Pp_PortalActionClass.php";
require_once "Pp_PortalActionForm.php";
require_once "Pp_PortalWebViewActionClass.php";
require_once "Pp_PortalWebViewActionForm.php";

// キャッシュやDB接続はBaseManagerの関数で一元化
require_once "Pp_PortalBaseManager.php";

/**
 *  Pp application Controller definition.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_PortalController extends Pp_Controller
{
	// 20141225黒澤
	// 実体は今のところなし。
	// 後々の作業でWebView周りだけ独自の処理を行う可能性があるので、いったんクラスだけ定義しておく
}