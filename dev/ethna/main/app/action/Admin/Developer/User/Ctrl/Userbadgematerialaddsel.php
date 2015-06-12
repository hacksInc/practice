<?php
/**
 *  Admin/Developer/User/Ctrl/Userbadgematerialaddsel.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_ctrl_userbadgematerialaddsel Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserCtrlUserbadgematerialaddsel extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'id' => array(
            // Form definition
			// VAR_TYPE_INTにすると空文字列を渡されたときにエラーになるので文字列として扱う
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'id',            // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => '/[0-9]*/',      // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        ),

		'table' => array(
            'required'    => true,                // Required Option(true/false)
        ),
		'material_list',
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
 *  admin_developer_user_ctrl_userbadgematerialaddsel action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserCtrlUserbadgematerialaddsel extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_user_ctrl_userbadgematerialaddsel Action.
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
     *  admin_developer_user_ctrl_userbadgematerialaddsel action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$developer_m =& $this->backend->getManager('Developer');

		$table = $this->af->get('table');

		$user_m =& $this->backend->getManager('User');
		$badge_m =& $this->backend->getManager('Badge');
		
		$user_id = $this->af->get('id');
		$user_base = $user_m->getUserBaseForApiResponse($user_id);

		$material_list = $this->af->get('material_list');
		$materials = explode("\r\n", $material_list);
		$material_alls = $badge_m->getMasterBadgeMaterial();
		$material_add = array();
		$material_err = array();
		$material_all = array();
		foreach($material_alls as $k => $v) {
			$material_all[$v['material_id']] = $v;
		}
		foreach($materials as $key => $val) {
			if (array_key_exists($val, $material_all) ) {
				$ret = $badge_m->addUserBadgeMaterialUpperLimit($user_id, $val, 1);
				$material_add[] = array('id' => $val, 'name' => $material_all[$val]['name_ja']);
			} else {
				$material_err[] = $val;
			}
		}
		$this->af->setApp('base',            $user_base);
		$this->af->setApp('material_add',    $material_add);
		$this->af->setApp('material_add_cnt',count($material_add));
		$this->af->setApp('material_err',    $material_err);
		$this->af->setApp('material_err_cnt',count($material_err));

        return 'admin_developer_user_ctrl_userbadgematerialaddsel';
    }
}

?>