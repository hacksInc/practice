<?php

require_once 'File/Util.php';

// アクセス権限があるか
function _smarty_block_a_has_permission($href)
{
	static $role = null;
	
	$backend =& $GLOBALS['_Ethna_controller']->getBackend();
	$admin_m =& $backend->getManager('Admin');
	
	$parts = parse_url($href);
	
	if (isset($parts['query'])) {
		parse_str($parts['query'], $queries);
	} else {
		$queries = null;
	}
	
	$path = $parts['path'];
	if (strncmp($path, '/', 1) !== 0) {
		$path = dirname($_SERVER['SCRIPT_NAME']) . '/' . $path;
	}
	
	$path = File_Util::realpath($path);
	
	$action = str_replace('/', '_', substr($path, 1));

	$env = Util::getEnv();
	$unit = $backend->config->get('unit_id');
	
	if (!$role) {
		$lid = $backend->session->get('lid');
		$user = $admin_m->getAdminUser($lid);
		$role = $user['role'];
	}
	
	#return $admin_m->hasAccessControlPermission($role, $action, $env, $queries, $unit);
	return true;
}

/**
 *	aタグの代替関数
 *
 *  管理画面用。
 *  リンク先アクセス権限チェックも行なう。
 *	@return string	出力データ
 */
function smarty_block_a( $_params, $_content, &$_smarty, &$_repeat )
{
	// パラメータチェック
	if ( is_null( $_content ) || !isset( $_params["href"] ) ) {
		return;
	}
	
	$params = $_params;
	
	// リンク先アクセス権限チェック
	if (!_smarty_block_a_has_permission($_params['href'])) {
		unset($params['href']);
		
		if (isset($_params['class']) && (strlen($_params['class']) > 0)) {
			$class = $_params['class'] . ' ';
		} else {
			$class = '';
		}
		
		$class .= 'muted';
		
		$params['class'] = $class;
	}

	// =========================================================
	// 出力作成
	// =========================================================
	$output = '<a';
	foreach ($params as $key => $value) {
		$output .= ' '.$key.'="'.$value.'"';
	}

	$output .= ">{$_content}</a>";
	
	return $output;
}
?>
