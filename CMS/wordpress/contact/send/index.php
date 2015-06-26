<?php

date_default_timezone_set('Asia/Tokyo');

function checkInput($var) {
	if (is_array($var)) {
		return array_map('checkInput', $var);
	} else {
		if (get_magic_quotes_gpc()) {
			$var = stripslashes($var);
		}
		if (preg_match('/\0/', $var)) {
			die('不正な入力です。');
		}
	return $var;
	}
}

session_start();

$_SESSION = checkInput($_SESSION);

if (isset($_POST['ticket']) && isset($_SESSION['ticket'])) {
	$ticket = $_POST['ticket'];
	if ($ticket != $_SESSION['ticket']) {
		die('不正なアクセスの疑いがあります。');
	}
} else {
	die('不正なアクセスの疑いがあります。');
}

//$mailTo  = $mailAddress;
$mailTo  = 'guest.toyosaku@cave.co.jp';

//$mailFrom  = $mailAddress;
$mailFrom  = 'guest.toyosaku@cave.co.jp';

$subject = 'お問い合わせありがとうございます。';

$today = date("Y.m.d H:i:s");

foreach($_SESSION as $key => $value){
	$$key = $value;
}

$subject02 = $contactType.'がありました。';
$contents = <<<EOL
===============お問い合わせ内容===============

種別 : $contactType
フリガナ　　　　　：　$companyName
お名前　　　　　　：　$name
メールアドレス　　：　$email
電話番号　　　　　：　$tel

お問い合わせ内容
$content

==============================================

EOL;

$contents01 = <<<EOL

$subject02

$contents

EOL;


$contents02 = <<<EOL


お問い合わせいただきまして、ありがとうございます。

担当者より追ってご連絡を差し上げますので、お待ちいただけますよう
お願い申し上げます。

なお、お問い合わせの内容によっては返信を行なわない場合も
ございますので予めご了承下さい。
______________________________________________

株式会社ケイブシステムズ
※このメールは自動で返信されております。


$contents

EOL;


mb_language('ja');
mb_internal_encoding('utf-8');

$header = 'From: ' . $mailFrom;

$result = mb_send_mail($mailTo, $subject02, $contents01, $header);

if ($result) {
	if($email){
		$resultUser = mb_send_mail($email, $subject, $contents02, $header);
	}
	$uri = 'http://'.$_SERVER["HTTP_HOST"].str_replace( 'send' , 'complete' ,$_SERVER['PHP_SELF']);
	header('HTTP/1.1 303 See Other');
	header('Location: ' . $uri);
	$_SESSION = array();
	session_destroy();
} else {
	$message = '送信に失敗しました。';
	echo $message;
}
