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
<link rel="stylesheet" href="/psychopass_portal/css/portal2/content.css" media="all">
<script src="/psychopass_portal/js/portal2/jquery.js"></script>
<script src="./js/portal2/jquery.nicescroll.min.js"></script>
<script src="/psychopass_portal/js/portal2/chimichara.js"></script>
<script src="./js/portal2/common.js"></script>
</head>
<body onload="scroll();">

<input type="hidden" id="chimiFlg" value="2">
<input type="hidden" id="php_theme_json" value="{$app_ne.theme_info}">
<input type="hidden" id="php_theme_name" value="{$app.user.theme_name}">

<div id="portalBackground">

<header class="clearfix">
	<img id="headerTitleFrame" src="/psychopass_portal/img/portal/headerTitle.png" width="100%">
	<img id="mark" src="/psychopass_portal/img/portal/psychoMark.png" width="12%">
	<img id="votePtFrame" src="/psychopass_portal/img/portal/votePt.png" width="100%">
	<img id="portalPtFrame" src="/psychopass_portal/img/portal/portalPt.png" width="100%">
	<a onClick="Unity.call('jump_to_game');"><img id="toGame" src="/psychopass_portal/img/portal/toGame.png" width="100%"></a>
	<p id="headerTitle">SPECIAL</p>
	<p id="userNumber">{$app.user.pp_id}</p>
	<p id="votePt">{$app.voting.point|intval}pt</p>
	<p id="portalPt">{$app.user.point}pt</p>
</header>

<div id="contentFrame" class="clearfix">
	<img src="/psychopass_portal/img/portal/event_frame.png" width="100%">
	<ul id="event_content" class="scroll">
		<li>
			<img id="voting_banner" src="img/voting/openBanner" onClick="Unity.call('{$app.url}/psychopass_portal/voting.php');" width="100%" >
		</li>
		<li>
			<img id="voting_banner" src="img/voting/reportBanner" onClick="Unity.call('{$app.url}/psychopass_portal/reward.php');" width="100%" >
		</li>
		<li>
			<img id="voting_banner" src="img/voting/resultBanner" onClick="Unity.call('{$app.url}/psychopass_portal/votingresult.php');" width="100%" >
		</li>
	</ul>
</div><!-- contentFrame -->

<div class="specialTitle">
	<img src="/psychopass_portal/img/portal/titleFrame.png" width="100%">
	<p id="titleFrameText">Contents</p>
</div>

<div id="toPage" class="clearfix">

	<img class="leftBtn" src="/psychopass_portal/img/portal/btn.png" width="100%">
	<a class="leftLink" onClick="Unity.call('{$app.url}/psychopass_portal/special.php');">キセカエ</a>

	<img class="rightBtn" src="/psychopass_portal/img/portal/btn.png" width="100%">
	<a class="rightLink" onClick="Unity.call('{$app.url}/psychopass_portal/wallpaperList.php');">壁紙</a>

</div>
<div id="toPage2" class="clearfix">
	<img class="leftBtn" src="/psychopass_portal/img/portal/btn.png" width="100%">
	<a class="leftLink" onClick="Unity.call('{$app.url}/psychopass_portal/exchange.php');">投票券交換所</a>
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
