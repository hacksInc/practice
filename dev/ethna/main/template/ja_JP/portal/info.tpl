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
<link rel="stylesheet" href="/psychopass_portal/css/portal2/info.css" media="all">
<script src="/psychopass_portal/js/portal2/jquery.js"></script>
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
	<p id="headerTitle">INFO</p>
	<p id="userNumber">{$app.user.pp_id}</p>
	<p id="votePt">{$app.voting.point|intval}pt</p>
	<p id="portalPt">{$app.user.point}pt</p>
</header>

<div id="contentFrame" class="clearfix">
	<img src="/psychopass_portal/img/portal/info_frame.png" width="100%">
	<ul id="infoContent" class="scroll">
		<li>
			<iframe src="//www.youtube.com/embed/mzvqDOP8ALU" frameborder="0" allowfullscreen width="95%" ></iframe>
		</li>
		<li>
			<a onClick="Unity.call('http://gekijyo.toho-movie.com/psycho-pass/');"><img class="theme" width="95%"   src="./img/theme/{$app.user.theme_name}/movie.png" alt="劇場版PSYCHO-PASSオフィシャルサイト"></a>
		</li>
		<li>
			<a onClick="Unity.call('http://psycho-pass.com');"><img class="theme" src="./img/theme/{$app.user.theme_name}/officialPcSite.jpg" alt="オフィシャルPCサイト" width="95%" ></a>
		</li>
		<li>
			<iframe src="//www.youtube.com/embed/Fn9DEPD7Rko" frameborder="0" allowfullscreen width="95%" ></iframe>
		</li>
		<li>
			<a onClick="Unity.call('http://psycho-pass.com/bd-dvd/index2_02.html');"><img class="theme" width="95%"   src="./img/theme/{$app.user.theme_name}/blu-ray-vol2.png" alt="PSYCHO-PASS2 BD&amp;DVD VOL.2 1.21 on sale"></a>
		</li>
		<li>
			<a onClick="Unity.call('http://psycho-pass.com/bd-dvd/index2_01.html');"><img class="theme" width="95%"  src="./img/theme/{$app.user.theme_name}/blu-ray-vol1.png" alt="PSYCHO-PASS2 BD&amp;DVD VOL.1 12.17 on sale"></a>
		</li>
	</ul>
<!-- contentFrame --></div>

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
