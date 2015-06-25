<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="./css/reset.css" media="all">
<link rel="stylesheet" href="./css/common.css" media="all">
<link rel="stylesheet" href="./css/voting.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
<script src="./js/voting.js"></script>
<script src="./js/jquery.nicescroll.min.js"></script>
</head>
<body onload="start();">

<input type="hidden" id="php_server_domain" value="{$app.domain}">

<div id="wrapper">

<div id="background">
<img id="area" src="img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<div id="controlBox">

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
<a  onclick="cancel();" id="overlay"></a>
<!-- controlBox --></div>

<footer>

    <div id="sns">
        
        <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img src="img/common/twitter.png" alt=""></a>
        <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" alt=""></a>
        
    </div>
    
</footer>
	<a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');" ><img src="img/voting/backBtn.png" style="display:block;position:absolute;bottom:1%;left:28%;width:21%;z-index:999;"></a>
</body>
</html>
