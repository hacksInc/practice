<?php
// REAL
//define("KPI_URL" , "kpi-web.cave.co.jp");
// DEV
//define("KPI_URL" , "dev-kpi-web.cave.co.jp");
//※↑デプロイ時のミスの元なので、環境によって異なるdefineはここでは行わない。代わりにpp-ini.phpでdefineする。

function kpi_set($tag = null ,$type = null , $count = null ,$date = null , $user = null , $money =null , $level = null , $stage = null ){
	//function kpi_set($tag = null,$rate = null,$user = null){
	if( empty($tag) ){
		error_log("[KPI ERROR!] tag does not set");
		return false ;
	}
	if( empty($type) ){
		error_log("[KPI ERROR!] type does not set");
		return false ;
	}

	if( empty($count) || !is_numeric($count) ){
		$count = 1;
	}

	if( empty($date) ){
		$date = '';
	}
	if( is_null($user) ){
		$user = '';
	}

	if( empty($money) || !is_numeric($money) ){
		$money = '';
	}

	if( is_null($stage) ){
		$stage = '';
	}

	$options = array('http' => array('timeout' => 2));
	//$kpiUrl = 'http://'.KPI_URL.'/analytics/?TAG='.$tag.'&T='.$type.
	$kpiUrl = 'http://'.KPI_URL.'/?TAG='.$tag.'&T='.$type.
		'&C='.$count .'&D='.$date.'&U='.$user .'&M='.$money .'&L='.$level .'&S='.$stage ;
	//$kpiUrl = 'http://'.KPI_URL.'/analytics/?tag='.$tag.'&r='.$rate .'&u='.$user;
	@file_get_contents($kpiUrl,false,stream_context_create($options));
	return true ;
}

?>