<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="./css/reset.css" media="all">
<link rel="stylesheet" href="./css/common.css" media="all">
<link rel="stylesheet" href="./css/voting.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
<script src="./js/jquery.nicescroll.min.js"></script>
<script src="./js/voting.js"></script>
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
			<img src="img/voting/warning.png" style="width:10%;float:left;margin-right:2%;"><span></span>
		</div>
		<div class="cancel">
			<a  alt="" onclick="cancel();">CLOSE</a>
		</div>
		<p id="test" style="font-size:12px;color:fff;"></p>
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
<a  id="overlay" onclick="cancel();"></a>
<!-- controlBox --></div>

<footer >

    <div id="sns" width="20%">
        
        <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img src="img/common/twitter.png" alt=""></a>
         <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fevents%2F349846928549125%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" alt=""></a>
        
    </div>
    <div id="timeLimit">
    	<p style="padding-left: 15%;border-bottom:1px solid #fff;">
      	<img src="img/voting/item.png" style="position:absolute;top:30%;left:5%;width:8%;">
       	終了まであと<span style="color:#f00;font-size:100%;">{$app.time_limit}</span></p>
   	</div>
</footer>

	<a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');" ><img src="img/voting/backBtn.png" style="display:block;position:absolute;bottom:1%;left:28%;width:21%;z-index:999;"></a>
</body>
</html>
