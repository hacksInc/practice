<?php
/**
 *  Unit/Id/List.php
 *
 *  設定ファイル上に存在するユニット番号を空白区切りで一覧出力する
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  unit_id_list Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_UnitIdList extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  unit_id_list action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_UnitIdList extends Pp_CliActionClass
{
    /**
     *  preprocess of unit_id_list Action.
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
     *  unit_id_list action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$unit_all = $this->config->get('unit_all');
		
		$unit_id_list = array_keys($unit_all);
		
		echo implode(" ", $unit_id_list);
    }
}

?>
