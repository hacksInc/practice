<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="./css/portal2/reset.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/common.css" media="all">
<link rel="stylesheet" href="./css/portal2/reward.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/portal2/jquery.js"></script>
<script src="./js/portal2/common.js"></script>
<script src="./js/portal2/reward.js"></script>
<script src="./js/portal2/jquery.nicescroll.min.js"></script>
</head>
<body onload="scroll();">

<input type="hidden" id="php_server_domain" value="{$app.domain}">

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

<div id="reward_background">

	<img src="img/reward/background.png" width="100%">
	<div id="content" class="scroll">
		<img id="voting_banner" src="img/voting/reportBanner.png" width="100%">
		
		<ul id="rewardList">
			<li>
				<img src="img/reward/list.png" width="100%">
				<p class="weeks">1段階目</p>
				<img class="now" src="img/reward/now.png" width="100%">
				<div class="number"><span class="nowPt">{$app.total}</span>回 / <span class="targetPt">315840</span>回</div>
				<div class="gauge"><span class="fill"></span></div>
				<img class="photo" src="img/reward/photo1.png" width="100%">
				<img class="clear" src="img/reward/clear.png" width="100%">
			</li>
			<li>
				<img src="img/reward/list.png" width="100%">
				<p class="weeks">2段階目</p>
				<img class="now" src="img/reward/now.png" width="100%">
				<div class="number"><span class="nowPt">{$app.total}</span>回 / <span class="targetPt">800000</span>回</div>
				<div class="gauge"><span class="fill"></span></div>
				<img class="photo" src="img/reward/photo2.png" width="100%">
				<img class="clear" src="img/reward/clear.png" width="100%">
			</li>
			<li>
				<img src="img/reward/list.png" width="100%">
				<p class="weeks">3段階目</p>
				<img class="now" src="img/reward/now.png" width="100%">
				<div class="number"><span class="nowPt">{$app.total}</span>回 / <span class="targetPt">1200000</span>回</div>
				<div class="gauge"><span class="fill"　width="30%"></span></div>
				<img class="photo" src="img/reward/photo3.png" width="100%">
				<img class="clear" src="img/reward/clear.png" width="100%">
			</li>
			<li>
				<img src="img/reward/list.png" width="100%">
				<p class="weeks">4段階目</p>
				<img class="now" src="img/reward/now.png" width="100%">
				<div class="number"><span class="nowPt">{$app.total}</span>回 / <span class="targetPt">1600000</span>回</div>
				<div class="gauge"><span class="fill"></span></div>
				<img class="photo" src="img/reward/photo2.png" width="100%">
				<img class="clear" src="img/reward/clear.png" width="100%">
			</li>
			<li>
				<img src="img/reward/list.png" width="100%">
				<p class="weeks">5段階目</p>
				<img class="now" src="img/reward/now.png" width="100%">
				<div class="number"><span class="nowPt">{$app.total}</span>回 / <span class="targetPt">2000000</span>回</div>
				<div class="gauge"><span class="fill"></span></div>
				<img class="photo" src="img/reward/photo3.png" width="100%">
				<img class="clear" src="img/reward/clear.png" width="100%">
			</li>
		</ul>

		<div id="announce">
			※全プレイヤーの執行数が集計されていきます<br>
			※エリミネーター、パラライザーは問いません<br>
			※達成時には順次[BOX]へ報酬が贈られます
		</div>
	</div>
</div>

<div id="sns">
        
    <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img class="shere" src="img/common/twitter.png" width="100%"></a>
    <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img class="shere" src="./img/common/facebook.png" width="100%"></a>
        
</div>
<div id="timeLimit">
    <p style="padding-left: 15%;border-bottom:1px solid #fff;">
    <img src="img/voting/item.png" style="position:absolute;top:30%;left:5%;width:8%;">
    <span style="color:#f00;font-size:100%;">{$app.time_limit}</span></p>
</div>

<a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');" ><img id="backBtn" src="img/voting/backBtn.png"></a>


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


<a  id="overlay" onclick="cancel();"></a>
<!-- portalBackground --></div>
</body>
</html>
