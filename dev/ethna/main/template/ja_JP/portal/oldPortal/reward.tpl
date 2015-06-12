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
<link rel="stylesheet" href="./css/reward.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
<script src="./js/reward.js"></script>
<script src="./js/jquery.nicescroll.min.js"></script>
<script src="./js/scrollbar.js"></script>
</head>
<body>

<input type="hidden" id="php_server_domain" value="{$app.domain}">

<div id="wrapper">

<div id="background">
<img id="area" src="img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<div id="controlBox">

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
<!-- controlBox --></div>

<footer >

    <div id="sns" width="20%">
        
        <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img src="img/common/twitter.png" alt=""></a>
         <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" alt=""></a>
        
    </div>

    <div id="timeLimit">
    	<p style="padding-left: 15%;border-bottom:1px solid #fff;">
      	<img src="img/reward/item.png" style="position:absolute;top:30%;left:5%;width:10%;">
{**       	終了まであと<span style="color:#f00;font-size:110%;">{$app.time_limit}</span></p> **}
			<span style="color:#f00;font-size:110%;">終了しました</span></p>
   	</div>
</footer>

	<a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');" ><img src="img/voting/backBtn.png" style="display:block;position:absolute;bottom:1%;left:28%;width:21%;z-index:999;"></a>
</body>
</html>
