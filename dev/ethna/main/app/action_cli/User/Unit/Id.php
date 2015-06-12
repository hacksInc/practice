<?php
/**
 *  ユーザーの所属ユニットを出力する
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  user_unit_id Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_UserUnitId extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  user_unit_id action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_UserUnitId extends Pp_CliActionClass
{
    /**
     *  preprocess of user_unit_id Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
		// 引数取得
		if ($GLOBALS['argc'] < 3) {
			// パラメータ不足
			error_log('Too few parameter.');
			exit(1);
		} else {
			// 第2引数以降を格納する
			$user_id = $GLOBALS['argv'][2];
		}
        
        $this->af->setApp('user_id', $user_id);
        
        return null;
    }

    /**
     *  user_unit_id action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
        $user_id = $this->af->getApp('user_id');
        
		$unit_m = $this->backend->getManager('Unit');
        $unit = $unit_m->getUnitFromUserId($user_id);
        
        $unit_all = $this->config->get('unit_all');
        if (is_scalar($unit) && isset($unit_all[$unit])) {
            echo $unit;
        } else {
			error_log('Unknown unit.');
        }
        
		exit(0);
    }
}

?>