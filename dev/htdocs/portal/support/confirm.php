<?php
/**
*  confirm.php
* @author suzuki.a
* @copyright Copyright (c) 2013 cave All Rights Reserved.
*/

require('config.php');
// print_r($_COOKIE);

$err = array();
$err_count=0;
$obj = array();
//POST取得
foreach ($postArray as $key => $value) {
	$obj[$key] = (isset($_POST[$key])?$_POST[$key]:'');
}
//エージェントは別チェック
$agent = (isset($_POST['agent'])?$_POST['agent']:'');
$sid = (isset($_POST['sid'])?$_POST['sid']:'');

//postで送ったsessionIdと一致しなければindexに飛ばす
if(!checkSid($sid))
{
	$_SESSION = array();
	setcookie("PHPSESSID", '', time() - 1800, '/');
	header("HTTP/1.1 301 Moved Permanently");
	header('Location:'. $url .'index.php');
	exit();
}

//入力チェック
foreach ($postArray as $key => $value)
{
	$err[$key] = "";
	if(strlen($obj[$key]) == 0)	{
		$err[$key] = "<br/>$postArray[$key]を入力してください。<br/>";
		$err_count++;
	}
}
//メール
if(strlen($obj['mail'])>0)
{
	if(!is_mail($obj['mail'])){
		$err['mail'] = "<br/>メールアドレスが不正です。<br/>";
		$err_count++;
	}
	if(!check_max($obj['mail'],255)){
		$err['mail'] = "<br/>メールアドレスは255文字までです。<br/>";
		$err_count++;
	}
}
//ID
/*
if(strlen($obj['yourId'])>0)
{
	$msg = "<br/>あなたのユーザーIDの形式が正しくありません。<br/>";
	if(!check_range($obj['yourId'],10)){
		$err['yourId'] = $msg;
		$err_count++;
	}
	if(!is_num($obj['yourId'])){
		$err['yourId'] = $msg;
		$err_count++;
	}
}
*/
//ニックネーム
if(strlen($obj['nickName'])>0)
{
	if(!check_max($obj['nickName'],10)){
		$err['nickName'] = "<br/>ニックネームは全角10文字までです。<br/>";
		$err_count++;
	}
}

//日付のみ
/*
$err['birthday'] = "";
$birthda7 = '';
if((strlen($obj['birthday_Year'])== 0)||(strlen($obj['birthday_Month'])== 0)||(strlen($obj['birthday_Day']) == 0))
{
	$err['birthday'] = "生年月日を入力してください。<br/>";
	$birthday = '0-0-0';
	$err_count++;
}else{
	$birthday = date('Y-m-d', mktime(0,0,0,$obj['birthday_Month'], $obj['birthday_Day'], $obj['birthday_Year']));

	if(!is_date($obj['birthday_Year'],$obj['birthday_Month'],$obj['birthday_Day'])){
		$err['birthday'] = "<br/>生年月日が未来か、正しくない日付になっています。<br/>";
		$err_count++;
	}
}
*/
//利用端末
if(strlen($obj['useModel'])>0)
{
	if(!check_max($obj['useModel'],100)){
		$err['useModel'] = "<br/>ご利用機種は100文字までにしてください。<br/>";
		$err_count++;
	}
}

$err['date'] = "";
$date = '';
if((strlen($obj['date_Year'])== 0)||(strlen($obj['date_Month'])== 0)||(strlen($obj['date_Day']) == 0)||(strlen($obj['date_Hour']) == 0))
{
	$err['date'] = "<br/>発生日時を入力してください。<br/>";
	$date = '0-0-0 0:0:0';
	$err_count++;
}else{
	$date = date('Y-m-d H', mktime(0,0,0,$obj['date_Month'], $obj['date_Day'], $obj['date_Year']));

	if(!is_date($obj['date_Year'],$obj['date_Month'],$obj['date_Day'],$obj['date_Hour'])){
		$err['date'] = "<br/>発生日時が未来か、正しくない日付になっています。<br/>";
		$err_count++;
	}
}

//お問い合わせ内容
if(strlen($obj['content'])>0)
{
	if(!check_max($obj['content'],1000)){
		$err['content'] = "<br/>お問い合わせは1000文字までにしてください。<br/>";
		$err_count++;
	}
}

//デバッギングコンソールを表示
// $smarty->debugging = true;

//セット
foreach ($obj as $key => $value)
{
	if((!preg_match("/^birthday_/", $key))&&(!preg_match("/^date_/", $key)))
	{
		$smarty->assign($key,htmlspecialchars($value));
		$smarty->assign('err_' . $key ,$err[$key]);
	}
}
$smarty->assign('agent',$agent);
$smarty->assign('sid',$sid);
//エラーがあるかないかでテンプレートをわける
//エラーがあるとき
if($err_count > 0)
{
	$smarty->assign('err_summary','入力内容に不備があります。もう一度お確かめください。');
//	$smarty->assign('birthday', $birthday);
//	$smarty->assign('err_birthday',$err['birthday']);
	$smarty->assign('date',$date );
	$smarty->assign('date_Hour',$obj['date_Hour']);
	$smarty->assign('err_date',$err['date']);
	$smarty->assign('hourList',$hourList);
	$smarty->assign('categoryList',$categoryList);

	$smarty->display('index.tpl');

}else{
//	$smarty->assign('birthday',$obj['birthday_Year'] . "年" .$obj['birthday_Month'] . "月" . $obj['birthday_Day'] . "日");
	$smarty->assign('useOS_disp',$useOS_dispArray[$obj['useOS']]);
	$smarty->assign('date',$obj['date_Year'] . "年" .$obj['date_Month'] . "月" . $obj['date_Day'] . "日 " . $hourList[$obj['date_Hour']]);
	$smarty->assign('category_disp',$categoryList[$obj['category']]);
	$smarty->assign('content',str_replace('\n\r', '<br/>', $obj['content']));

	$smarty->display('confirm.tpl');
}

//=============================//
//メールチェック
function is_mail($str)
{
	if (preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $str))
	{
		return true;
	} else {
		return false;
	}
}
//文字数チェック(最大）
function check_max($str,$num)
{
	if (mb_strlen($str, "UTF-8") <= $num)
	{
		return true;
	} else {
		return false;
	}
}
//文字数チェック(範囲）
function check_range($str,$num)
{
	if(mb_strlen($str, "UTF-8") === $num)
	{
		return true;
	} else {
		return false;
	}
}
//数字チェック
function is_num($str)
{
	if (preg_match("/^[0-9]+$/", $str))
	{
		return true;
	} else {
		return false;
	}
}
//英字チェック
function is_english($str)
{
	if (preg_match("/^[a-zA-Z]+$/", $str))
	{
		return true;
	} else {
		return false;
	}
}
//日付チェック
function is_date($year,$month,$day,$hour=0)
{
	$year = intval($year);
	$month = intval($month);
	$day = intval($day);
	if(!checkdate($month, $day, $year))
	{
		return false;
	}
	//未来時
	if(time() < mktime($hour,0,0,$month, $day, $year))
	{
		return false;
	}
	return true;
}

?>

