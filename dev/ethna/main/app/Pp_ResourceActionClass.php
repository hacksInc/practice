<?php
// vim: foldmethod=marker
/**
 *  Pp_ResourceActionClass.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

// {{{ Pp_ResourceActionClass
/**
 *  リソース用 action execution class
 *
 *  @author     {$author}
 *  @package    Pp
 *  @access     public
 */
class Pp_ResourceActionClass extends Ethna_ActionClass
{
	/**
	 *  authenticate before executing action.
	 *
	 *  @access public
	 *  @return string  Forward name.
	 *                  (null if no errors. false if we have something wrong.)
	 */
	function authenticate()
	{
		$ret = parent::authenticate();
		if ($ret) {
			return $ret;
		}

		$ret = $this->authenticateUnit();
		if ($ret) {
			return $ret;
		}
	}
	
	private function authenticateUnit()
	{
		$unit_m = $this->backend->getManager('Unit');
		
		$unit_default = $this->config->get('unit_default');
		if (is_array($unit_default) && isset($unit_default['resource'])) {
			$unit = $unit_default['resource'];
			$unit_m->resetUnit($unit);
			$this->backend->logger->log(LOG_DEBUG, 'Unit found. unit=[' . $unit . ']');
		} else {
			$this->backend->logger->log(LOG_WARNING, 'Unit not found.');
		}
		
		return;
	}
}
// }}}

?>