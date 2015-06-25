<?php
/**
 *  Admin/Developer/Assetbundle/Monster/Index.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pager.php';
require_once 'Pp_AdminViewClass.php';

/**
 *  admin_developer_assetbundle_monster_index view implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_View_AdminDeveloperAssetbundleMonsterIndex extends Pp_AdminViewClass
{
    /**
     *  preprocess before forwarding.
     *
     *  @access public
     */
    function preforward()
    {
		$assetbundle_m =& $this->backend->getManager('AdminAssetbundle');

//		$pageID = $this->af->get('pageID');
//		if (!$pageID) $pageID = 1;
//		
//		$page = $pageID - 1;
		$page = $this->af->getPageFromPageID();

		$limit = 50;
		$offset = $limit * $page;
		
		$list = $assetbundle_m->getMasterAssetBundleListEx('monster', $offset, $limit);
		$num = $assetbundle_m->countMasterAssetBundle('monster');

		$options = array(
			'mode'        => 'Sliding',
			'delta'       => 4,
			'importQuery' => false,
			'totalItems'  => $num,
			'perPage'     => $limit,
		);

		$pager =& Pager::factory($options);
		$links = $pager->getLinks();
		
		// 画像ファイルの最終更新時刻を取得（imgタグのクエリストリングに付加する為）
		foreach ($list as $id => $row) {
			$mtime = array();
			foreach (array('icon', 'image') as $type) {
				$path = $list[$id][$type];
				if (is_file($path)) {
					$mtime[$type] = filemtime($path);
				}
			}
			
			$list[$id]['mtime'] = $mtime;
		}
		
		$this->af->setApp('list', $list);
		$this->af->setAppNe('pager', $links);
    }
}

?>