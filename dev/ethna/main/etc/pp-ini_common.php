<?php
/**
 * 開発・商用共通設定ファイル
 */

//// ユニット判別
//$unit_tmp = Pp_UnitManager::getRequestUnit();
//if ($unit_tmp && isset($config['unit_all'][$unit_tmp])) {
//	foreach ($config['unit_all'][$unit_tmp] as $name_tmp => $value_tmp) {
//		$config[$name_tmp] = $value_tmp;
//	}
//	
//	$config['unit_id'] = $unit_tmp;
//}

// ユニット1のDSN設定
$unit_tmp = '1';
if (isset($config['unit_all']) && isset($config['unit_all'][$unit_tmp])) {
	foreach (array(
		'dsn'   => 'dsn_unit1',
		'dsn_r' => 'dsn_unit1_r',
	) as $key_tmp => $key_unit_tmp) {
		if (isset($config['unit_all'][$unit_tmp][$key_tmp])) {
			$config[$key_unit_tmp] = $config['unit_all'][$unit_tmp][$key_tmp];
		}
	}
}
