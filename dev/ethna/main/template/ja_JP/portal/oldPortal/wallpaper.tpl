<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Cache-Control" content="no-store">
<meta http-equiv="Expires" content="-1">
<link rel="stylesheet" href="./css/reset.css" media="all">
<link rel="stylesheet" href="./css/common.css" media="all">
<link rel="stylesheet" href="./css/owl.carousel/owl.carousel.css" media="all">
<link rel="stylesheet" href="./css/owl.carousel/owl.theme.psychopass.css" media="all">
<link rel="stylesheet" href="./css/wallpaper.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/owl.carousel.min.js"></script>
<script src="./js/common.js"></script>
<script src="./js/wallpaper.js"></script>
</head>
<body>

<input type="hidden" id="php_server_domain" value="{$app.domain}">
<input type="hidden" id="php_ua" value="{if $app.user.user_agent eq 1}iphone{else}android{/if}">

<div id="wrapper">

<div id="background">
<img id="area" src="./img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="./img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<!--戻るボタン-->
        <a id="btn-back" onClick="Unity.call('{$app.url}/psychopass_portal/wallpaperList.php');"><img src="./img/common/btn_back.png" style="width:80px;height:auto;"></a>
<!--/戻るボタン-->

<div id="prevButton" class="nav"><img class="theme" src="./img/theme/{$app.user.theme_name}/arrowL.png" alt="" width="21" height="55"></div>
<div id="nextButton" class="nav"><img class="theme" src="./img/theme/{$app.user.theme_name}/arrowR.png" alt="" width="21" height="55"></div>

<div id="owl-carousel">
</div>

<!-- /wrapper --></div>

</body>
</html>
