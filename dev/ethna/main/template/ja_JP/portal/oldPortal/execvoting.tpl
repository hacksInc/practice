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

<div id="voting_background">

	<img src="img/voting/background.png" width="100%">
	<p id="voting_title">投票開催中</p>

	<div id="content" class="scroll">
		<img id="voting_banner" src="img/voting/openBanner.png" width="100%">

		<p id="announce">
			あなたを含めた<span style="color:#eab602;">スリーマンセル</span>として、<br>
			あと<span style="color:#eab602;">2名</span>を指定してください。
		</p>

		<div id="voting_list1" class="clearfix">
		<img id="listItem" src="img/voting/listItem.png" width="50%">
		<p class="member">MEMBER1</p>
		<div class="myname">あああああああああ監視官</div>
		<img src="img/voting/cross.png" class="cross">
		</div>

		<div class="clearfix" style="width:98%;position:relative;">
			<div id="voting_list2">
			<img id="listItem2" src="img/voting/listItem.png">
			<p class="member2">MEMBER2</p>
				<select name="name1" class="name_list">
					<option value="宜野座">宜野座</option>
				</select>
			</div>

			<img src="img/voting/cross.png" class="cross2">

			<div id="voting_list3">
			<img id="listItem2" src="img/voting/listItem.png" width="50%">
			<p class="member2">MEMBER3</p>
				<select name="name2" class="name_list">
					<option value="常守">常守</option>
				</select>
			</div>
		</div>

		<div class="clearfix" style="margin:3%;width:94%;position:relative">
			<div class="clearfix" style="float:left;width:45%;">
				<div id="voting_list4" class="clearfix">
					<p id="ppp">所持票数</p>
					<p id="myPt">5660</p>
				</div>
				<div id="voting_list5">
					<select name="usePoint" class="point_list">
						<option value="111">111票</option>
					</select>
				</div>
			</div>
			<a href=""><img src="img/voting/vote_btn.png" width="100%" id="vote_btn"></a>
		</div>

		<p id="announce2">
			※投票点数はMISSIONにて潜在藩を執行すると獲得できます
		</p>



		<div id="rank_list">
		<a href=""><img id="ranking_btn" src="img/voting/ranking_btn.png" width="100%"></a>
			<img src="img/voting/top3.png" width="100%">
			<ul>	
			{foreach from=$app.r_voting key="key" item="row"}
				{if $key < 3}
				<li>
					<p class="rank">{$row.rank_str}</p>
				<img class="image" src="img/voting/{$app.assoc[$row.item_id].item_file}.png" width="100%">
				<img class="image2" src="img/voting/{$app.assoc[$row.item_id2].item_file}.png" width="100%">
					<div class="border">
					<p class="rank_name">{$app.assoc[$row.item_id].item_name}×{$app.assoc[$row.item_id2].item_name}</p>
					<p class="rank_point">{$row.point|number_format}票</p>
					</div>
				</li>
				{/if}
			{/foreach}
			</ul>
		</div>
	</div>
</div>
<!-- finish -->
	<div id="popup_finish" style="display:block;">
		<div id="name" class="clearfix">
			<p id="name1">{$app.m_voting[$form.item_id].item_name}</p>
			<span style="position:absolute;top:-15%;left:44%;font-size:130%;">×</span>
			<p id="name2">{$app.m_voting[$form.item_id2].item_name}</p>
		</div>
		<div class="point">
			<span style="font-size:120%;">{$form.point}</span>票 投票しました
		</div>
	    <p id="result_shere">みんなに共有しよう！</p>
	    <div id="result_sns">
	        <a onClick="Unity.call('https://twitter.com/share?text=サイコパス公式アプリ　{$app.m_voting[$form.item_id].item_name}　×　{$app.m_voting[$form.item_id2].item_name}に{$form.point}票投じました！http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス　#pp4sp_vote');"><img src="./img/common/twitter.png" alt="" style="width:42%; float:left;"></a>
	        <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" alt="" style="width:42%; float:right;"></a>
	    </div>
		<div class="close">
			<a href="" alt="" onClick="Unity.call('{$app.url}/psychopass_portal/voting.php');">CLOSE</a>
		</div>
	</div>
<!-- /finish -->
	
<a href="" id="overlay" style="display:block;" onClick="Unity.call('{$app.url}/psychopass_portal/voting.php');"></a>
<!-- controlBox --></div>

<footer >

    <div id="sns" width="20%">
        
        <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img src="img/common/twitter.png" alt=""></a>
	         <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" alt=""></a>
        
    </div>
</footer>
</body>
</html>
