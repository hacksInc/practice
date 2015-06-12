<?php
/**
 *  Pp_Plugin_Filter_Tracking.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  filter plugin implementation for tracking user data.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Plugin_Filter_Tracking extends Ethna_Plugin_Filter
{
	/**#@+
	 *  @access private
	 */

	/**
	 *  @var    int     Start time.
	 */
	var $stime;

	/**#@-*/


	/**
	 *  filter before first processing.
	 *
	 *  @access public
	 */
	function preFilter()
	{
		$stime = explode(' ', microtime());
		$stime = $stime[1] + $stime[0];
		$this->stime = $stime;
	}

	/**
	 *  filter BEFORE executing action.
	 *
	 *  @access public
	 *  @param  string  $action_name  Action name.
	 *  @return string  null: normal.
	 *                string: if you return string, it will be interpreted
	 *                        as Action name which will be executed immediately.
	 */
/*
	function preActionFilter($action_name)
	{
		return null;
	}
*/

	/**
	 *  filter AFTER executing action.
	 *
	 *  @access public
	 *  @param  string  $action_name    executed Action name.
	 *  @param  string  $forward_name   return value from executed Action.
	 *  @return string  null: normal.
	 *                string: if you return string, it will be interpreted
	 *                        as Forward name.
	 */
/*
	function postActionFilter($action_name, $forward_name)
	{
		return null;
	}
*/
	
	/**
	 *  filter which will be executed at the end.
	 *
	 *  @access public
	 */
	function postFilter()
	{
		$backend = $this->ctl->getBackend();

/*
		$tracking_m = $this->ctl->class_factory->getManager('Tracking');
		$tracking_m->flush();
*/
		
		$periodlog_m = $this->ctl->class_factory->getManager('Periodlog');
		$periodlog_m->flush();
		
		$backend->closeDB();
		$backend->closeDB('r');
		$backend->closeDB('log');
		
		$kpi_m = $this->ctl->class_factory->getManager('Kpi');
		$kpi_m->flush();
	}
}
?>
