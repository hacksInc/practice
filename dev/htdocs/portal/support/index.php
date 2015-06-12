<?php
/**
*  index.php
* @author suzuki.a
* @copyright Copyright (c) 2013 cave All Rights Reserved.
*/

require('config.php');
session_start();
$user_agent = $_SERVER['HTTP_USER_AGENT'];

foreach ($postArray as $key => $value)
{
	$err_name = 'err_' . $key;
	$smarty->assign($err_name,'');
}
//エラー表示用
$smarty->assign('err_summary','');

//デフォルト設定
$smarty->assign('mail','');
//$smarty->assign('yourId','');
//$smarty->assign('transId','');
$smarty->assign('nickName','');
//$smarty->assign('birthday','0-0-0');
$smarty->assign('useOS','iOS');
$smarty->assign('useModel','');
$smarty->assign('date','0-0-0');
$smarty->assign('date_Hour','0');
$smarty->assign('hourList',$hourList);
$smarty->assign('category',0);
$smarty->assign('categoryList',$categoryList);
$smarty->assign('content','');
$smarty->assign('agent',$user_agent);
$smarty->assign('sid',session_id());

//デバッギングコンソールを表示
// $smarty->debugging = true;
$smarty->display('index.tpl')

?>

