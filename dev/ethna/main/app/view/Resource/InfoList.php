<?php
/**
 *  Resource/InfoList.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

//////////////////////////
// APIのアクションの場合 //
//////////////////////////

/**
 *  resource_infoList view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceInfoList extends Pp_ViewClass
{
	/**
	 *  preprocess before forwarding.
	 *
	 *  @access public
	 */
	function preforward()
	{
		$news_m =& $this->backend->getManager('News');

		$target = new DateTime('-48 hours');

		$list = $news_m->getCurrentNewsContentList();
		if (is_array($list)) foreach ($list as $i => $row) {

			$list[$i]['date_disp_short'] = $news_m->getDateDispShort($row['date_disp']);

			$dateDisp = new DateTime($row['date_disp']);
			$list[$i]['is_new'] = ($target <= $dateDisp) ? 1 : 0;
		}

		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);
		$this->af->setApp('mtime', $news_m->getImageDirMtime());
	}

}
