<?php
/**
 *  User/Dmpw/Hash.php
 *
 *  データ移行パスワードハッシュ値を計算する。
 *  カスタマーサポート等でデータ移行パスワードを変更したい場合に使用する。
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  user_dmpw_hash Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_UserDmpwHash extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  user_dmpw_hash action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_UserDmpwHash extends Pp_CliActionClass
{
    /**
     *  preprocess of user_dmpw_hash Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  user_dmpw_hash action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// 引数取得
		if ( $GLOBALS['argc'] < 3 ) {
			// パラメータ不足
			Ethna::raiseError( 'Too few parameter.', E_GENERAL );
			error_log('Too few parameter.');
			return;
		} else {
			// 第2引数以降を格納する
			$user_id = $GLOBALS['argv'][2];
		}
		
		if (isset($GLOBALS['argv'][3])) {
			$dmpw = $GLOBALS['argv'][3];
		}

		$user_m = $this->backend->getManager('AdminUser');
		
		echo "dmpwハッシュ値を計算します。\n";
		
		if (!isset($dmpw)) {
			echo "dmpwが指定されていません。ランダムに生成します。\n";
			$dmpw = $user_m->getRandomDmpw();
		}
		
		$dmpw_hash = $user_m->hashDmpw($user_id, $dmpw);
		
		echo "ハッシュ値は以下の通りです。\n";
		echo "----------------------------------------------------------------\n";
		echo "user_id  : " . $user_id   . "\n";
		echo "dmpw     : " . $dmpw      . "\n";
		echo "dmpw_hash: " . $dmpw_hash . "\n";
		echo "※DBは変更されていません。\n";
		echo "※端末で入力するのはuser_idではなくaccountです。\n";
		echo "----------------------------------------------------------------\n";
		
        return null;
    }
}

?>