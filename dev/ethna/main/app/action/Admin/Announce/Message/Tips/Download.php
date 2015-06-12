<?php
/**
 *  Admin/Announce/Message/Tips/Download.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';
require_once dirname(__FILE__) . '/Index.php';

/**
 *  admin_announce_message_tips_download Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminAnnounceMessageTipsDownload extends Pp_Form_AdminAnnounceMessageTips
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'table' => array(
           // 'required'    => true,                // Required Option(true/false)
            'required'    => false,                // Required Option(true/false)
        ),
		'format' => array(
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => false,            // Required Option(true/false)
            'min'         => null,             // Minimum value
            'max'         => 4,                // Maximum value
            'regexp'      => '/^csv$|^json$/', // String by Regexp
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
 *  admin_announce_message_tips_download action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminAnnounceMessageTipsDownload extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_announce_message_tips_download Action.
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
     *  admin_announce_message_tips_download action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$format = $this->af->get('format');
		
/*		if ($format == 'json') {
	        return 'admin_announce_message_tips_download_json';
		} else {
	        return 'admin_announce_message_tips_download';
        }*/
	        return 'admin_announce_message_tips_download_json';
    }
}
