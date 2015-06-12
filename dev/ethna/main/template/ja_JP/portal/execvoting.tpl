<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
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
		<div class="myname">{$app.user.user_name}監視官</div>
		<img src="img/voting/cross.png" class="cross">
		</div>

		<div id="voting_list2">
		<img id="listItem2" src="img/voting/listItem.png">
		<p class="member2">MEMBER2×3</p>

			<select name="name1" class="name_list">
				{foreach from=$app.r_voting item="row" name=loop}
				{if $smarty.foreach.loop.index < 25}
					<option value="'{$app.m_voting[$row.item_id_1].item_name}','{$app.m_voting[$row.item_id_2].item_name}'">{$app.m_voting[$row.item_id_1].item_name}×{$app.m_voting[$row.item_id_2].item_name}</option>
				{/if}
				{/foreach}
			</select>
		</div>

		<div class="clearfix" style="margin:3%;width:94%;position:relative">
			<div class="clearfix" style="float:left;width:45%;">
				<div id="voting_list4" class="clearfix">
					<p id="ppp">所持票数</p>
					<p id="myPt">{$app.voting.point|intval}</p>
				</div>
				<div id="votin_list5">
					<select name="usePoint" class="point_list">
						{if $app.voting.point|intval > 0}
							{section name=i start=0 loop=$app.voting.point|intval}
								<option value="{$smarty.section.i.index+1}">{$smarty.section.i.index+1}票</option>
							{/section}
						{else}
							<option value="---">---</option>
						{/if}
					</select>
				</div>
			</div>
			<a  onclick="vote();"><img src="img/voting/vote_btn.png" width="100%" id="vote_btn"></a>
		</div>

		<p id="announce2">
			※投票点数はMISSIONにて潜在犯を執行すると獲得できます
		</p>



		<div id="rank_list">
		<a  onclick="rank();"><img id="ranking_btn" src="img/voting/ranking_btn.png" width="100%"></a>

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
	</div>
</div>
	
<!-- confirm -->
	<div id="popup_confirm">
		<div id="name" class="clearfix">
			<p id="name1"></p>
			<span style="position:absolute;top:-15%;left:44%;font-size:130%;">×</span>
			<p id="name2"></p>
		</div>
		<div class="point">
			<span style="font-size:120%;"></span>票 投票します
		</div>
		<div id="votingPoint" class="clearfix">
			<p id="ppt">所持票数</p>
			<p id="beforePt"></p>
			<img src="img/voting/yajirusi.png" style="width:8%;position:absolute;top:5%;left:62%;">
			<p id="afterPt" style="color:#f00;">0</p>
		</div>
		<p style="text-align:center;color:#eab602;width:80%;position:absolute;top:50%;left:10%;">※キャラクターの左右の配置に関係なく集計されます。</p>
		<div class="accept">
			<a  alt="" onclick="accept();">ACCEPT</a>
		</div>
		<div class="cancel">
			<a  alt="" onclick="cancel();">CANCEL</a>
		</div>
	</div>
<!-- /confirm -->

<!-- alert -->
	<div id="popup_alert">
		<div class="alert">
			<img src="img/voting/warning.png" style="width:10%;float:left;margin-right:2%;"><span>同じキャラクター同士での投票はできません</span>
		</div>
		<div class="cancel">
			<a  alt="" onclick="cancel();">CLOSE</a>
		</div>
	</div>
<!-- /alert -->

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

<!-- finish -->
	<div id="popup_finish" style="display:block;" onClick="Unity.call('{$app.url}/psychopass_portal/voting.php');">
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
	        <a onClick="Unity.call('https://twitter.com/share?text=サイコパス公式アプリ　{$app.m_voting[$form.item_id].item_name}　×　{$app.m_voting[$form.item_id2].item_name}に{$form.point}票投じました！http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス　#pp4sp_vote');"><img  class="shere" src="./img/common/twitter.png" alt="" style="width:42%; float:left;"></a>
	        <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img class="shere" src="./img/common/facebook.png" alt="" style="width:42%; float:right;"></a>
	    </div>
		<div class="close">
			<a href="" alt="" onClick="Unity.call('{$app.url}/psychopass_portal/voting.php');">CLOSE</a>
		</div>
	</div>
<!-- /finish -->



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

<a  id="overlay" onclick="cancel();" style="display:block;"></a>
<!-- portalBackground --></div>
</body>
</html>
