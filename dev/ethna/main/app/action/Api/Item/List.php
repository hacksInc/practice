<?php
/**
 *	Api/Item/List.php
 *	アイテム一覧取得
 *
 *	@author     {$author}
 *	@package    Pp
 *	@version    $Id$
 */

/**
 *  api_item_list Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiItemList extends Pp_ApiActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}

/**
 *  api_item_list action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiItemList extends Pp_ApiActionClass
{
    /**
     *  preprocess of api_item_list Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        if ($this->af->validate() > 0) {
            return 'error_400';
        }

        return null;
    }

    /**
     *  api_item_list action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		// クライアントからの引数を取得
		$pp_id = $this->getAuthenticatedBasicAuth( 'user' );
		
		$item_m =& $this->backend->getManager( "Item" );
		
		$item_list = $item_m->getUserItemList( $pp_id, "db" );
		
		// 整形
		$user_item = array();
		foreach ( $item_list as $row ) {
			$user_item[] = array(
				"item_id"	=> $row['item_id'],
				"item_num"	=> $row['num'],
			);
		}
		
		$this->af->setApp( "user_item", $user_item, true );
		
        return 'api_json_encrypt';
    }
}
?>