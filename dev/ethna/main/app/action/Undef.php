<?php
/**
 *  Undef.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  undef Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_Undef extends Pp_ApiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'c'
    );
}

/**
 *  undef action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_Undef extends Pp_ApiActionClass
{
	/**
	 *  アクション実行前の認証処理を行う
	 *
	 * このアクションは404 Not Foundを返すだけなので、認証不要。
	 * 認証しようとすると失敗して戻り値JSON生成してしまうが、
	 * 余計な情報は出力したくないので、このアクションでは認証処理しない。
	 *  @access public
	 *  @return string  遷移名(nullなら正常終了, falseなら処理終了)
	 */
	function authenticate()
	{
		// 何もしない
	}
	
    /**
     *  undef action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        return 'error_404';
    }
}

?>