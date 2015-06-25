<?php
/**
 *  Admin/Developer/Assetbundle/Bgmodel/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pager.php';
require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_bgmodel_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleBgmodelIndex extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');

		$page = $this->af->getPageFromPageID();

		$limit = 50;
		$offset = $limit * $page;
		
		$list = $assetbundle_m->getMasterAssetBundleListEx('bgmodel', $offset, $limit);
		$num = $assetbundle_m->countMasterAssetBundle('bgmodel');

		$options = array(
			'mode'        => 'Sliding',
			'delta'       => 4,
			'importQuery' => false,
			'totalItems'  => $num,
			'perPage'     => $limit,
		);

		$pager =& Pager::factory($options);
		$links = $pager->getLinks();

		$this->af->setApp('list', $list);
		$this->af->setAppNe('pager', $links);
    }
}

?>