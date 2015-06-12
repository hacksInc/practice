<?php
/**
 *  Pp_Backend.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

// {{{ Pp_Backend
/**
 *  バックエンド処理クラス
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Backend extends Ethna_Backend
{
	/**
     *  DBコネクションを切断する
     *
	 *  この関数はPp_BackEnd独自の関数。基底クラスには同名の関数は無い。
	 *  不要になったDBコネクションを早く切断したい場合に使用する。
     *  @access public
	 *  @see Ethna_Backend::getDB , Ethna_Backend::shutdownDB
     *  @param  string  $db_key DBキー
     */
    function closeDB($db_key = "")
    {
        $db_varname =& $this->_getDBVarname($db_key);
		
        if (isset($this->db_list[$db_varname]) && 
			($this->db_list[$db_varname] != null) && 
			$this->db_list[$db_varname]->isValid()
		) {
            $this->db_list[$db_varname]->db->Close();
            unset($this->db_list[$db_varname]);
		}
    }
}
// }}}
?>