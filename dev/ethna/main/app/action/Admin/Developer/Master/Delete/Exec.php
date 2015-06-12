<?php
/**
 *  Admin/Developer/Master/Delete/Exec.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_master_delete_exec Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperMasterDeleteExec extends Pp_AdminActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'table' => array(
			'required'    => true,            // Required Option(true/false)
		),
		'agree' => array(
			'type'        => VAR_TYPE_INT,    // Input type
			'required'    => true,            // Required Option(true/false)
			'name'        => '確認チェック',  // Display name
		),
		'confpass' => array(
			'type'        => VAR_TYPE_STRING, // Input type
			'required'    => true,                // Required Option(true/false)
			'regexp'      => '/^jmjaabe$/',   // String by Regexp
			'name'        => '確認パスワード',    // Display name
		),
	);

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
 *  admin_developer_master_delete_exec action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperMasterDeleteExec extends Pp_AdminActionClass
{
	/**
	 *  preprocess of admin_developer_master_delete_Exec Action.
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
	 *  admin_developer_master_list action implementation.
	 *
	 *  @access public
	 *  @return string  forward name.
	 */
	function perform()
	{
		$unit_m =& $this->backend->getManager('Unit');
		$table = $this->af->get('table');
		$unit = $this->session->get('unit');
		
		$sql = "DELETE FROM $table";//実行ユーザ権限によりtruncateできないのでdeleteで削除
		$ret = $unit_m->executeForUnit($unit, $sql, false, false);
		
		return 'admin_developer_master_delete_exec';
	}
}

?>