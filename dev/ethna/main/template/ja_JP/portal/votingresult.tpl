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
<link rel="stylesheet" href="./css/portal2/voting.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/portal2/jquery.js"></script>
<script src="./js/portal2/common.js"></script>
<script src="./js/portal2/voting.js"></script>
<script src="./js/portal2/jquery.nicescroll.min.js"></script>
<script src="./js/portal2/common.js"></script>
</head>
<body onload="start();">

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

<div id="voting_background">

	<img src="img/voting/background.png" width="100%">
	<p id="voting_title">あなたが組みたい3人チーム結果発表</p>
	<img id="voting_banner" src="img/voting/resultBanner.png" style="width:96%;position:absolute;top:6%;left:2%;">

	<a  onclick="rank();"><img id="result_rank_btn" src="img/voting/ranking_btn.png" width="50%"></a>

	<div id="result_rank">
		<img src="img/voting/top3.png" width="100%">
		<ul>
			{foreach from=$app.r_voting key="key" item="row"}
				{if $key < 3}
				<li>
					<p class="rank">{$row.rank_str}</p>
				<img class="image" src="img/voting/{$app.m_voting[$row.item_id_1].item_file}.png" width="100%">
				<img class="image2" src="img/voting/{$app.m_voting[$row.item_id_2].item_file}.png" width="100%">
					<div class="border">
					<p class="rank_name">{$app.m_voting[$row.item_id_1].item_name}×{$app.m_voting[$row.item_id_2].item_name}</p>
					<p class="rank_point">{$row.point|number_format}票</p>
					</div>
				</li>
				{/if}
			{/foreach}
		</ul>
	</div>

	<div id="result_announce">
		<span>投票の結果は以上になります。</span><br><br>
		以上をもちまして、<br>
		あなたが組みたい3人チームを終了させていただきます。<br><br>
		たくさんの投票、<br>
		ありがとうございました。
	</div>


</div>
<!-- ポップアップランキング -->
	<div id="popup_ranking">
		<img src="img/voting/popup_ranking.png" width="100%">
		<ul class="scroll">
		
		{foreach from=$app.r_voting item="row" name=loop2}
			{if $smarty.foreach.loop2.index < 25}
			<li>
			<p class="rank">{$row.rank_str}</p>
			<img class="image" src="img/voting/{$app.m_voting[$row.item_id_1].item_file}.png" width="100%">
			<img class="image2" src="img/voting/{$app.m_voting[$row.item_id_2].item_file}.png" width="100%">
			<div class="border">
			<p class="rank_name"><span id="aaa">{$app.m_voting[$row.item_id_1].item_name}</span>×<span id="bbb">{$app.m_voting[$row.item_id_2].item_name}</span></p>
			<p class="rank_point">{$row.point|number_format}票</p>
			</div>
			</li>
			{/if}
		{/foreach}
		</ul>
	</div>
<!-- /ポップアップランキング -->

<div id="sns">
        
    <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img src="img/common/twitter.png" width="100%"></a>
    <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" width="100%"></a>
        
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
