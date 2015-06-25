<?php
/**
 *  Resource/News.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  resource_news view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_ResourceNews extends Pp_ViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$news_m =& $this->backend->getManager('News');
		$lang = $this->af->get('lang');
		$ua   = $this->af->get('ua');

		$list = $news_m->getCurrentNewsContentList($lang, $ua);
		if (is_array($list)) foreach ($list as $i => $row) {
			$list[$i]['date_disp_short'] = $news_m->getDateDispShort($row['date_disp']);
		}

		$this->af->setApp('lang', $lang);
		$this->af->setApp('list', $list);
		$this->af->setAppNe('list', $list);
    }
}

?>
