<?php
//servise
// define('SMARTY_DIR', '/usr/etc/service_pear_Smarty/');
set_include_path('/usr/lib/pear');

define('SMARTY_DIR', '/var/devptpyco/htdocs/smarty_libs/');
define('SUPPORT_DIR', '/var/devptpyco/htdocs/support/');
//define('SUPPORT_DIR', '/mnt/filenode/htdocs_wdp/support/');
//define('SUPPORT_DIR', '/mnt/filenode_fliajmja-020/htdocs_wdp/support/');
require_once(SMARTY_DIR . 'Smarty.class.php');
//require_once(SMARTY_DIR . 'SmartyBC.class.php');
$smarty = new Smarty();
//$smarty->php_handling = Smarty::PHP_ALLOW;

$smarty->template_dir = SUPPORT_DIR . 'templates/';
$smarty->compile_dir  = SUPPORT_DIR . 'templates_c/';
$smarty->config_dir   = SUPPORT_DIR . 'configs/';
$smarty->cache_dir    = SUPPORT_DIR . 'cache/';

//url
// $url = "http://online_redmine_test.cave.net/jagmon/";
$url = "http://dev.psycho-pass.cave.co.jp/support/index.php";

//送信アドレス
$addresses = array(
'ppp-support@cave.co.jp'
);

//エラーの日本語表示用
$postArray = array(
				'mail'           => "メールアドレス",
//				'yourId'         => "あなたのユーザーID",
//				'transId'        => "引継ぎID",
				'nickName'       => "ニックネーム",
//				'birthday_Year'  => "生年月日(年)",
//				'birthday_Month' => "生年月日(月)",
//				'birthday_Day'   => "生年月日(日)",
				'useOS'          => "ご利用のOS",
				'useModel'       => "ご利用機種",
				'date_Year'      => "発生日時(年)",
				'date_Month'     => "発生日時(月)",
				'date_Day'       => "発生日時(日)",
				'date_Hour'      => "発生日時(時)",
				'category'       => "お問い合わせカテゴリ",
				'content'        => "お問い合わせ内容"
				);

//カテゴリリスト
$categoryList = array(
					0 => "不具合について",
					1 => "ご意見・ご要望",
					2 => "その他お問い合わせ"
);

//機種名表示用
$useOS_dispArray = array(
				'Android'        => "Android(Google Play)",
				'iOS'            => "iOS(App Store)"
				);

//カテゴリリスト
$hourList = array(
				0 => "00:00～01:00",
				1 => "01:00～02:00",
				2 => "02:00～03:00",
				3 => "03:00～04:00",
				4 => "04:00～05:00",
				5 => "05:00～06:00",
				6 => "06:00～07:00",
				7 => "07:00～08:00",
				8 => "08:00～09:00",
				9 => "09:00～10:00",
				10 => "10:00～11:00",
				11 => "11:00～12:00",
				12 => "12:00～13:00",
				13 => "13:00～14:00",
				14 => "14:00～15:00",
				15 => "15:00～16:00",
				16 => "16:00～17:00",
				17 => "17:00～18:00",
				18 => "18:00～19:00",
				19 => "19:00～20:00",
				20 => "20:00～21:00",
				21 => "21:00～22:00",
				22 => "22:00～23:00",
				23 => "23:00～00:00");

//session_idのチェック
function checkSid($sid)
{
	if(!isset($_COOKIE['PHPSESSID'])||($sid ==''))
	{
		return false;

	}else{
		if(($sid !=  $_COOKIE['PHPSESSID']))
		{
			return false;
		}
	}
	return true;
}

?>
