<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="css/portal2/reset.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/common.css" media="all">
<link rel="stylesheet" href="css/portal2/wallpaper.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="js/portal2/jquery.js"></script>
<script src="./js/portal2/jquery.nicescroll.min.js"></script>
<script src="./js/portal2/common.js"></script>
</head>
<body onload="scroll();">

<div id="portalBackground">

<header class="clearfix">
	<img id="headerTitleFrame" src="/psychopass_portal/img/portal/headerTitle.png" width="100%">
	<img id="mark" src="/psychopass_portal/img/portal/psychoMark.png" width="12%">
	<img id="votePtFrame" src="/psychopass_portal/img/portal/votePt.png" width="100%">
	<img id="portalPtFrame" src="/psychopass_portal/img/portal/portalPt.png" width="100%">
	<a onClick="Unity.call('jump_to_game');"><img id="toGame" src="/psychopass_portal/img/portal/toGame.png" width="100%"></a>
	<p id="headerTitle">壁紙</p>
	<p id="userNumber">{$app.user.pp_id}</p>
	<p id="votePt">{$app.voting.point|intval}pt</p>
	<p id="portalPt">{$app.user.point}pt</p>
</header>

<div id="contentFrame" class="clearfix">
	<img src="/psychopass_portal/img/portal/info_frame.png" width="100%">
	<div id="infoContent" class="scroll">
<div id="wallpaperArea">

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=kogami');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/kogami.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=tsunemori');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/tsunemori.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=ginoza');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/ginoza.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=masaoka');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/masaoka.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=kagari');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/kagari.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=kunizuka');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/kunizuka.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=karanomori');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/karanomori.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=togane');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/togane.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=hinakawa');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/hinakawa.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=shimotsuki');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/shimotsuki.png"></a>
</div>

<div class="characterSelect">
<div style="position:relative;">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=other');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/other.png"><img src="./img/news/new-pink.png" style="max-width:30px;height:auto;position:absolute;top:0px;right:0px;"></a>
</div>
</div>
</div>
</div>
<!-- /wallpaperArea --></div>


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
