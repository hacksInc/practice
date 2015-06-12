<?php
/**
 *  Admin/Developer/Assetbundle/Monster/Image.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_assetbundle_monster_image Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperAssetbundleMonsterImage extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'monster_id' => array(
            // Form definition
            'type'        => VAR_TYPE_INT,    // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => true,            // Required Option(true/false)
            'min'         => 1,               // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => null,            // String by Regexp
        ),
        'type' => array(
            // Form definition
            'type'        => VAR_TYPE_STRING, // Input type
        
            //  Validator (executes Validator by written order.)
            'required'    => false,           // Required Option(true/false)
            'min'         => null,            // Minimum value
            'max'         => null,            // Maximum value
            'regexp'      => '/^image$|^icon$/', // String by Regexp
        ),
    );
}

/**
 *  admin_developer_assetbundle_monster_image action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperAssetbundleMonsterImage extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_assetbundle_monster_image Action.
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
     *  admin_developer_assetbundle_monster_image action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');

		$monster_id = $this->af->get('monster_id');
		$type = $this->af->get('type');
		if (!$type) $type = 'image';
		
		$path = $assetbundle_m->getMonsterImagePath($type, $monster_id);
		
		if (!is_file($path)) {
			header('HTTP/1.0 404 Not Found');
			exit;
		}
		
		$this->af->setApp('path', $path);

		return 'admin_developer_assetbundle_monster_image';
    }
}

?>