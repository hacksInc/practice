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
<link rel="stylesheet" href="/psychopass_portal/css/portal2/home.css" media="all">
<script src="/psychopass_portal/js/portal2/jquery.js"></script>
<script src="/psychopass_portal/js/portal2/jquery.nicescroll.min.js"></script>
<script src="/psychopass_portal/js/portal2/home.js"></script>
<script src="/psychopass_portal/js/portal2/chimichara.js"></script>
<script src="./js/portal2/common.js"></script>
</head>
<body onload="scroll();">

<input type="hidden" id="chimiFlg" value="1">
<input type="hidden" id="php_theme_name" value="{$app.user.theme_name}">
<input type="hidden" id="php_twitter_txt" value="{$app.twitter_txt}" />
<input type="hidden" id="php_news_title" value="{$app.pickup.news_title}" />
<input type="hidden" id="php_news_text" value="{$app.pickup.news_text}" />
<input type="hidden" id="php_point_get" value="{if $app.login_now eq true}1{else}0{/if}">

<div id="portalBackground">

<header class="clearfix">
	<img id="headerTitleFrame" src="/psychopass_portal/img/portal/headerTitle.png" width="100%">
	<img id="mark" src="/psychopass_portal/img/portal/psychoMark.png" width="12%">
	<img id="votePtFrame" src="/psychopass_portal/img/portal/votePt.png" width="100%">
	<img id="portalPtFrame" src="/psychopass_portal/img/portal/portalPt.png" width="100%">
	<a onClick="Unity.call('jump_to_game');"><img id="toGame" src="/psychopass_portal/img/portal/toGame.png" width="100%"></a>
	<p id="headerTitle">HOME</p>
	<p id="userNumber">{$app.user.pp_id}</p>
	<p id="votePt">{$app.voting.point|intval}pt</p>
	<p id="portalPt">{$app.user.point}pt</p>
</header>

<div id="contentFrame">
	<img src="/psychopass_portal/img/portal/contentFrame.png" width="100%">
	<p id="contentTitle">PICKUP NEWS</p>

	<div id="contentText" class="scroll">
		<article id="topicsText">
		</article>

	</div>

</div>

<div id="other">
	<img src="/psychopass_portal/img/portal/other_frame.png" width="100%">

	<div id="sns" class="clearfix">
	    <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img class="shere" id="tweet" src="/psychopass_portal/img/portal/tweetbtn.png" width="100%"></a>
	    <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img class="shere" id="share" src="./img/portal/sharebtn.png" width="100%"></a>
	</div>

</div><!-- other -->

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

<div id="modal-content">
<p>ログインボーナス！</p>
<p>10pt獲得！</p>
<p>※ポップアップ以外の箇所をタッチすると元の画面に戻ります</p>
</div>

</div><!-- portalBackground -->

</body>
</html>
