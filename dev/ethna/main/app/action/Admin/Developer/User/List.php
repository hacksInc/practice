<?php
/**
 *  Admin/Developer/User/List.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_user_list Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperUserList extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'by' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING, // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'by',            // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 1,               // Minimum value
            'max'         => 4,               // Maximum value
            'regexp'      => '/^id$|^name$/', // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        ),
		
		'id' => array(
            // Form definition
			// VAR_TYPE_INTにすると空文字列を渡されたときにエラーになるので文字列として扱う
            'type'        => VAR_TYPE_STRING,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'id',            // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => false,           // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => '/[0-9]*/',      // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        ),
		
		'nickname',
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
 *  admin_developer_user_list action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperUserList extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_user_list Action.
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
     *  admin_developer_user_list action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$by       = $this->af->get('by');
		$id       = $this->af->get('id');
		$nickname = $this->af->get('nickname');

		$user_m =& $this->backend->getManager('AdminUser');
		
		$base = null;
		switch ($by) {
			case 'id':
				$base = $user_m->getUserBase($id);
				if (!$base) {
			        return 'admin_developer_user_error';
				}
				break;
			
			case 'name':
				$bases = $user_m->getUserBaseFromNameLike($nickname);
				$bases_cnt = count($bases);
				if ($bases_cnt == 1) {
					$base = $bases[0];
				} else if ($bases_cnt > 1) {
					$this->af->setApp('bases', $bases);
			        return 'admin_developer_user_name';
				} else {
			        return 'admin_developer_user_error';
				}
				break;
		}
		
		$this->af->setApp('base', $base);

		return 'admin_developer_user_list';
    }
}

?>