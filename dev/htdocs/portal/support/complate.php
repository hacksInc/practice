<?php
/**
*  complete.php
* @author suzuki.a
* @copyright Copyright (c) 2013 cave All Rights Reserved.
*/

 require('config.php');

  //内部のエンコード
  mb_language("japanese");
  mb_internal_encoding("UTF-8");

//POST取得
$err = array();
$err_count=0;
$obj = array();

$obj['mail'] = (isset($_POST['mail'])?$_POST['mail']:'');
$obj['yourId'] = (isset($_POST['yourId'])?$_POST['yourId']:'');
//$obj['transId'] = (isset($_POST['transId'])?$_POST['transId']:'');
$obj['nickName'] = (isset($_POST['nickName'])?$_POST['nickName']:'');
//$obj['birthday'] = (isset($_POST['birthday'])?$_POST['birthday']:'');
$obj['useOS'] = (isset($_POST['useOS'])?$_POST['useOS']:'');
$obj['useModel'] = (isset($_POST['useModel'])?$_POST['useModel']:'');
$obj['date'] = (isset($_POST['date'])?$_POST['date']:'');
$obj['category'] = (isset($_POST['category'])?$_POST['category']:'');
$obj['content'] = (isset($_POST['content'])?$_POST['content']:'');
$obj['agent'] = (isset($_POST['agent'])?$_POST['agent']:'');

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

  $fromAddress = $obj['mail'];

  //ヘッダ情報
  $subject  = "【PP】お問い合わせ(" . $categoryList[$obj['category']] . ")" ;
  $message = "お問い合わせがありました。 \n";
  $message .= get_message($obj,$categoryList);

  //メール用にサブジェクトと本文をエンコード
  // $subject = mb_encode_mimeheader($subject);
  $message = mb_convert_encoding($message, "JIS");

  foreach ($addresses as $key => $toAddress)
  {
   send($toAddress,$fromAddress,$subject, $message);
  }

  //session削除
  $_SESSION = array();
  setcookie("PHPSESSID", '', time() - 1800, '/');

  $smarty->display('complate.tpl');

/**
* メール送信
*/
function send($toAddress,$fromAddress,$subject, $message)
{
	// header作成
	$header = "Return-Path: " . $fromAddress . "\r\n";
	$header.= "Reply-To: "    . $fromAddress . "\r\n";

	// From
	$header .= 'FROM: =?ISO-2022-JP?B?' . base64_encode(mb_convert_encoding('サイコパスポータル', 'iso-2022-jp')) . '?= <' . $fromAddress . '>' . "\r\n";

	// Content-Type / Charset
	$header .= "Content-Type: text/plain; charset=\"ISO-2022-JP\" \r\n";

	// X-Mailer
	//$header .= "X-Mailer: Noir_Mail";
	return mb_send_mail($toAddress, $subject, $message, $header, "-f " . $fromAddress);

}
/**
* メール本文作成
*/
function get_message($obj,$categoryList)
{
  $message = "-------------------------------------------------------" . "\n";
  $message .= "返信用メールアドレス: " . $obj['mail'] . "\n";
//  $message .= "ユーザID        : " . $obj['yourId'] . "\n";
//  $message .= "引継ぎID       : " . $obj['transId'] . "\n";
  $message .= "ニックネーム      : " . $obj['nickName'] . "\n";
//  $message .= "生年月日       : " . $obj['birthday']  . "\n";
  $message .= "ご利用OS       :" . $obj['useOS'] . "\n";
  $message .= "ご利用機種      :" . $obj['useModel'] . "\n";
  $message .= "発生日時       :" . $obj['date'] . "\n";
  $message .= "お問い合わせカテゴリ:" . $categoryList[$obj['category']] . "\n";
  $message .= "お問い合わせ内容 :" . str_replace('<br/>', '\n', $obj['content']) . "\n";
  $message .= "ユーザエージェント  :" . $obj['agent'] . "\n";
  $message .= "-------------------------------------------------------" . "\n";

  return $message;

}


?>

