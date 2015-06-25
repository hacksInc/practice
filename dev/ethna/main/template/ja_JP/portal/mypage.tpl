<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/reset.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/common.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/mypage.css" media="all">
<script src="/psychopass_portal/js/portal2/jquery.js"></script>
<script src="/psychopass_portal/js/portal2/chimichara.js"></script>
<script src="./js/portal2/common.js"></script>
</head>
<body>

<input type="hidden" id="chimiFlg" value="3">
<input type="hidden" id="php_theme_json" value="{$app_ne.theme_info}">
<input type="hidden" id="php_theme_name" value="{$app.user.theme_name}">

<div id="portalBackground">

<header class="clearfix">
	<img id="headerTitleFrame" src="/psychopass_portal/img/portal/headerTitle.png" width="100%">
	<img id="mark" src="/psychopass_portal/img/portal/psychoMark.png" width="12%">
	<img id="votePtFrame" src="/psychopass_portal/img/portal/votePt.png" width="100%">
	<img id="portalPtFrame" src="/psychopass_portal/img/portal/portalPt.png" width="100%">
	<a onClick="Unity.call('jump_to_game');"><img id="toGame" src="/psychopass_portal/img/portal/toGame.png" width="100%"></a>
	<p id="headerTitle">MYPAGE</p>
	<p id="userNumber">{$app.user.pp_id}</p>
	<p id="votePt">{$app.voting.point|intval}pt</p>
	<p id="portalPt">{$app.user.point}pt</p>
</header>


<div id="profileArea">
	<img src="/psychopass_portal/img/portal/profile_frame.png" width="100%">

	<img id="profileImg" src="./img/mypage/{if $app.user.sex eq 1}taro{else}hanako{/if}.png" width="100%">

	<img id="nameDecoration" src="/psychopass_portal/img/portal/item.png" width="100%">
	<p id="name">{$app.user.user_name}</p>
	<table>
	<tr>
		<th><p>NAME</p></th><td>: {$app.user.user_name_en}</td>
	</tr>
	<tr>
		<th><p>ID</p></th><td>: {$app.user.pp_id}</td>
	</tr>
	<tr>
		<th><p>ログイン数</p></th><td>: {$app.user.login_num}</td>
	</tr>
	</table>

</div>

<div id="toPage" class="clearfix">

	<a class="leftLink" onClick="Unity.call('{$app.url}/psychopass_portal/history.php');">イベント履歴</a><img class="leftBtn" src="/psychopass_portal/img/portal/btn.png" width="100%">
	<a class="rightLink" onClick="Unity.call('{$app.url}/psychopass_portal/changeName.php');">名前を変更</a><img class="rightBtn" src="/psychopass_portal/img/portal/btn.png" width="100%">
	
</div>

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

</div><!-- portalBackground -->

</body>
</html>
