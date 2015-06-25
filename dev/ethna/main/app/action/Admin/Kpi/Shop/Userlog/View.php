<?php
/**
 *  Admin/Kpi/Shop/Userlog/View.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_kpi_shop_userlog_view Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminKpiShopUserlogView extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
       /*
        *  TODO: Write form definition which this action uses.
        *  @see http://ethna.jp/ethna-document-dev_guide-form.html
        *
        *  Example(You can omit all elements except for "type" one) :
        *
        *  'sample' => array(
        *      // Form definition
        *      'type'        => VAR_TYPE_INT,    // Input type
        *      'form_type'   => FORM_TYPE_TEXT,  // Form type
        *      'name'        => 'Sample',        // Display name
        *  
        *      //  Validator (executes Validator by written order.)
        *      'required'    => true,            // Required Option(true/false)
        *      'min'         => null,            // Minimum value
        *      'max'         => null,            // Maximum value
        *      'regexp'      => null,            // String by Regexp
        *      'mbregexp'    => null,            // Multibype string by Regexp
        *      'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        *
        *      //  Filter
        *      'filter'      => 'sample',        // Optional Input filter to convert input
        *      'custom'      => null,            // Optional method name which
        *                                        // is defined in this(parent) class.
        *  ),
        */

		'id' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
            'form_type'   => FORM_TYPE_TEXT,  // Form type
            'name'        => 'id',            // Display name
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
            'mbregexp'    => null,            // Multibype string by Regexp
            'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        ),

		'format',
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
 *  admin_kpi_shop_userlog_view action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminKpiShopUserlogView extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_kpi_shop_userlog_view Action.
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
     *  admin_kpi_shop_userlog_view action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$user_id = $this->af->get('id');

		$user_m =& $this->backend->getManager('AdminUser');
		
		$base = $user_m->getUserBase($user_id);
		if (!$base) {
			//TODO エラーページへ
		}

		$this->af->setApp('base', $base);
		
		return 'admin_kpi_shop_userlog_view';
    }
}

?>