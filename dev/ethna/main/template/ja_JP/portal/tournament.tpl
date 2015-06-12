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
<link rel="stylesheet" href="./css/tournament.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
<script src="./js/tournament.js"></script>
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

<div id="reward_background" style="position:relative;">

	<img src="img/reward/background.png" width="100%">

	<div id="tournament">
		<div id="tournamentTable">
			<img src="img/tournament/tournamentTable.png" width="100%">
			<p id="tournamentTitle"></p>
			<div id="table">
			<!-- 1回戦 -->
				<span class="height"id="height1"></span>
				<span class="width"id="width1"></span>
				<span class="height"id="height2"></span>
				<span class="width"id="width2"></span>
				<span class="height"id="height3"></span>
				<span class="width"id="width3"></span>
				<span class="height"id="height4"></span>
				<span class="width"id="width4"></span>
			<!-- 1回戦 -->
			<!-- 2回戦 -->
				<span class="round2_height_long"id="height5"></span>
				<span class="round2_width"id="width5"></span>
				<span class="round2_height"id="height6"></span>
				<span class="round2_width"id="width6"></span>
				<span class="round2_height"id="height7"></span>
				<span class="round2_width"id="width7"></span>
				<span class="round2_height_long"id="height8"></span>
				<span class="round2_width"id="width8"></span>
			<!-- 2回戦 -->
			<!-- 3回戦 -->
				<span class="round3_height" id="height9"></span>
				<span class="round3_width" id="width9"></span>
				<span class="round3_height" id="height10"></span>
				<span class="round3_width" id="width10"></span>
				<span id="height11"></span>
			<!-- 3回戦 -->
			</div>
			<ul id="participant" class="clearfix">
				<li><img src="img/voting/ginoza.png" width="100%"></li>
				<li><img src="img/voting/ginoza.png" width="100%"></li>
				<li><img src="img/voting/ginoza.png" width="100%"></li>
				<li><img src="img/voting/ginoza.png" width="100%"></li>
				<li><img src="img/voting/ginoza.png" width="100%"></li>
				<li><img src="img/voting/ginoza.png" width="100%"></li>
			</ul>
		</div>
		<div id="battleZone">
			<img src="img/tournament/battleBackground.png" width="100%">
			<img id="l_chara" src="img/tournament/l_chara_3.png" width="100%">
			<img id="vs" src="img/tournament/vs.png" width="100%">
			<img id="r_chara" src="img/tournament/r_chara_5.png" width="100%">
			<p id="l_count">2000</p>
			<p id="r_count">5000</p>
			<img id="gauge" src="img/tournament/gauge.png" width="100%">
			<div id="mater">
				<img id="blueGauge" src="img/tournament/blueGauge.png">
				<img id="redGauge" src="img/tournament/redGauge.png">
			</div>
			<p id="tournamentTitle"></p>

		</div>
	</div>
</div>
<!-- controlBox --></div>

<footer >

    <div id="sns" width="20%">
        
        <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img src="img/common/twitter.png" alt=""></a>
         <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" alt=""></a>
        
    </div>
<!--
    <div id="timeLimit">
    	<p style="padding-left: 15%;border-bottom:1px solid #fff;">
      	<img src="img/reward/item.png" style="position:absolute;top:30%;left:5%;width:10%;">
       	終了まであと<span style="color:#f00;font-size:110%;">{$app.time_limit}</span></p>
   	</div>
--></footer>

	<a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');" ><img src="img/voting/backBtn.png" style="display:block;position:absolute;bottom:1%;left:28%;width:21%;z-index:999;"></a>
</body>
</html>
