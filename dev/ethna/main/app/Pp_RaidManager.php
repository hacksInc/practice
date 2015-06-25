<?php
/**
 *  Pp_RaidManager.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id:$
 */

/**
 *  Pp_RaidManager
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_RaidManager extends Ethna_AppManager
{
	protected $db_cmn = null;
	protected $db_cmn_r = null;

	private function set_db()
	{
		if( is_null( $this->db_cmn ))
		{
			$this->db_cmn =& $this->backend->getDB( 'cmn' );
		}
	}
	private function set_db_r()
	{
		if( is_null( $this->db_cmn_r ))
		{
			$this->db_cmn_r =& $this->backend->getDB( 'cmn_r' );
		}
	}

	function addRaidTotal( $raid_id, $num )
	{
		$this->set_db();

		$param = array( $num, $raid_id );
		$sql = "UPDATE ct_raid SET total = total + ? WHERE raid_id = ?";
		if( !$this->db_cmn->execute( $sql, $param ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn->db->ErrorNo(), $this->db_cmn->db->ErrorMsg(), __FILE__, __LINE__ );
		}
		return true;
	}

	function getRaidTotal( $raid_id )
	{
		$this->set_db_r();

		$param = array( $raid_id );
		$sql = "SELECT total FROM ct_raid WHERE raid_id = ?";
		$total = $this->db_cmn_r->GetOne( $sql, $param );
		if( is_null( $total ))
		{	// エラー
			return Ethna::raiseError( "CODE[%d] MESSAGE[%s] FILE[%s] LINE[%d]", E_USER_ERROR,
				$this->db_cmn_r->db->ErrorNo(), $this->db_cmn_r->db->ErrorMsg(), __FILE__, __LINE__ );
		}

		return $total;
	}
}
?>
