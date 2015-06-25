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
<link rel="stylesheet" href="./css/modal.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
<script src="./js/newsDetail.js"></script>
</head>
<body>

<input type="hidden" id="php_point_get" value="{if $app.read_now eq true}1{else}0{/if}">

<div id="wrapper">

<div id="background">
<img id="area" src="./img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="./img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<!--戻るボタン-->
        <a id="btn-back" onClick="Unity.call('{$app.url}/psychopass_portal/news.php');"><img src="./img/common/btn_back.png" style="width:80px;height:auto;"></a>
<!--/戻るボタン-->
<div style="display:block;height:18px;"></div>
<header id="windowHeader" class="newsDetail">
<h1><img class="theme" src="./img/theme/{$app.user.theme_name}/tabNewsDetail.png" alt="NEWS DETAIL" width="155" height="33"></h1>
</header>

<div id="window2">
<article id="contents">
<section class="oneNews list">
<h2 class="newsTitle">{$app.news.news_title}
{if $app.news.new eq true}
	<img class="newImage" src="img/news/new.png" width="30" height="13">
{/if}
</h2>
<p class="date">{$app.news.date}</p>
{$app_ne.news_text}
</section>
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

<div id="modal-content">
<p>5pt獲得！</p>
<p>※ポップアップ以外の箇所をタッチすると元の画面に戻ります</p>
</div>

</body>
</html>
