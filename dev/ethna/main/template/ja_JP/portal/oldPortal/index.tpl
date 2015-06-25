<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="/psychopass_portal/css/reset.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/common.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/top.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/modal.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/theme/{$app.user.theme_name}.css" media="all">
<script src="/psychopass_portal/js/jquery.js"></script>
<script src="/psychopass_portal/js/common.js"></script>
<script src="/psychopass_portal/js/home.js"></script>
</head>
<body>

<input type="hidden" id="php_twitter_txt" value="{$app.twitter_txt}" />
<input type="hidden" id="php_news_title" value="{$app.pickup.news_title}" />
<input type="hidden" id="php_news_text" value="{$app.pickup.news_text}" />
<input type="hidden" id="php_point_get" value="{if $app.login_now eq true}1{else}0{/if}">

<div id="wrapper">

<div id="background">
<img id="area" src="img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="/psychopass_portal/img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<!--<a href="https://twitter.com/psychopass_tv">
<div id="twitter">
<div class="marquee">
<p></p>-->
<!-- /marquee --><!-- </div> -->
<!-- /twitter --><!-- </div> -->
<!-- </a> -->

<header id="windowHeader" class="pickupNews">
<h1><img class="theme" src="/psychopass_portal/img/theme/{$app.user.theme_name}/tabPickupNews.png" alt="PICKUP NEWS" width="158" height="33"></h1>
</header>

<div id="window2">
<article id="contents">

</article>
</div>

<!-- /wrapper --></div>

<footer>

    <div id="sns">
        
        <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img src="/psychopass_portal/img/common/twitter.png" alt=""></a>
        <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fpages%2FPsycho-Pass-%25E3%2582%25B5%25E3%2582%25A4%25E3%2582%25B3%25E3%2583%2591%25E3%2582%25B9-Official-App%2F430791707090830%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" alt=""></a>
        
    </div>
    
    <div id="Pt">
        所持ポイント：{$app.user.point}pt
    </div>

</footer>


<div id="modal-content">
<p>ログインボーナス！</p>
<p>10pt獲得！</p>
<p>※ポップアップ以外の箇所をタッチすると元の画面に戻ります</p>
</div>

</body>
</html>
