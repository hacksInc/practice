<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="./css/portal2/reset.css" media="all">
<link rel="stylesheet" href="./css/portal2/modal.css" media="all">
<link rel="stylesheet" href="./css/owl.carousel/owl.carousel.css" media="all">
<link rel="stylesheet" href="./css/owl.carousel/owl.theme.psychopass.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/common.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/special.css" media="all">
<script src="./js/portal2/jquery.js"></script>
<script src="./js/portal2/owl.carousel.min.js"></script>
<script src="./js/portal2/common.js"></script>
<script src="./js/portal2/special.js"></script>
<script src="/psychopass_portal/js/portal2/chimichara.js"></script>
</head>
<body>

<input type="hidden" id="chimiFlg" value="1">
<input type="hidden" id="php_theme_name" value="{$app.user.theme_name}">
<input type="hidden" id="php_theme_json" value="{$app_ne.theme_info}">
<input type="hidden" id="php_user_point" value="{$app.user.point}">
<input type="hidden" id="php_server_domain" value="{$app.domain}">

<div id="portalBackground">

<header class="clearfix">
	<img id="headerTitleFrame" src="/psychopass_portal/img/portal/headerTitle.png" width="100%">
	<img id="mark" src="/psychopass_portal/img/portal/psychoMark.png" width="12%">
	<img id="votePtFrame" src="/psychopass_portal/img/portal/votePt.png" width="100%">
	<img id="portalPtFrame" src="/psychopass_portal/img/portal/portalPt.png" width="100%">
	<a onClick="Unity.call('jump_to_game');"><img id="toGame" src="/psychopass_portal/img/portal/toGame.png" width="100%"></a>
	<p id="headerTitle">キセカエ</p>
	<p id="userNumber">{$app.user.pp_id}</p>
	<p id="votePt">{$app.voting.point|intval}pt</p>
	<p id="portalPt">{$app.user.point}pt</p>
</header>

<div id="contentFrame">
	<img src="/psychopass_portal/img/portal/newsFrame.png" width="100%">
	<p id="contentTitle">THEME LIST</p>
	<img id="character" class="theme" src="./img/theme/{$app.user.theme_name}/chara.png" width="100%">
	<p id="themeNote">
		テーマを切り替えました。<br>
		<a onClick="Unity.call('{$app.url}/psychopass_portal/special.php');">テーマ選択へ</a>
	</p>
<!-- contentFrame --></div>

<a class="backLink" onClick="Unity.call('{$app.url}/psychopass_portal/content.php');">もどる</a><img class="backBtn" src="/psychopass_portal/img/portal/btn.png" width="100%">



<footer class="clearfix">
	<img id="btnFrame" src="/psychopass_portal/img/portal/btnFrame.png">
	<ul id="btn">
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/index.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn1.png" width="100%"></a></li>
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/news.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn2.png" width="100%"></a></li>
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn3.png" width="100%"></a></li>
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/info.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn4.png" width="100%"></a></li>
		<li><a onClick="Unity.call('{$app.url}/psychopass_portal/mypage.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn5.png" width="100%"></a></li>
	</ul>
</footer>


<!-- portalBackground --></div>


</body>
</html>
