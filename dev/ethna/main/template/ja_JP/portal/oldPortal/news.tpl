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
<link rel="stylesheet" href="./css/news.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
</head>
<body>

<div id="wrapper">

<div id="background">
<img id="area" src="./img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="./img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<header id="windowHeader" class="newsList">
<h1><img class="theme" src="./img/theme/{$app.user.theme_name}/tabNewsList.png" alt="NEWS LIST" width="139" height="33"></h1>
</header>

<div id="window2">
<article id="contents">
{foreach from=$app.news item="row"}
	<section class="oneNews list">
	{if $row.read eq true}
		<h2 class="newsTitle2"><a class="read" onClick="Unity.call('{$app.url}/psychopass_portal/newsDetail.php?news_id={$row.ID}');">{$row.news_title}</a>
	{else}
		<h2 class="newsTitle"><a onClick="Unity.call('{$app.url}/psychopass_portal/newsDetail.php?news_id={$row.ID}');">{$row.news_title}</a>
	{/if}
	{if $row.new eq true}
		<br/><img class="newImage" src="img/news/new.png" width="30" height="13" style="margin-left:2px;">
	{/if}
	</h2><p class="date">{$row.date}</p></section><hr>
{/foreach}
</article>
</div>

<!-- /wrapper --></div>


<footer>

    <div id="sns">
        
        <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img src="./img/common/twitter.png" alt=""></a>
        <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fpages%2FPsycho-Pass-%25E3%2582%25B5%25E3%2582%25A4%25E3%2582%25B3%25E3%2583%2591%25E3%2582%25B9-Official-App%2F430791707090830%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" alt=""></a>
        
    </div>
    
    <div id="Pt">
        所持ポイント：{$app.user.point}pt
    </div>

</footer>


</body>
</html>
