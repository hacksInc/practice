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
<link rel="stylesheet" href="./css/content.css" media="all">
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

<!--戻るボタン-->
        <a id="btn-back" onClick="Unity.call('{$app.url}/psychopass_portal/history.php');"><img src="./img/common/btn_back.png" style="width:80px;height:auto;"></a>
<!--/戻るボタン-->

<header id="windowHeader" class="newsList">
<style>
header#windowHeader.newsList:before{
	clip:auto;
}
</style>
<img class="theme" src="./img/theme/{$app.user.theme_name}/tabRecord.png" alt="NEWS LIST" width="139" height="33">
</header>

<div id="window">
<article id="contents" class="historyDetail">
<div style="text-align:center;">{$app.m_event.title}</div>
<p class="date">2014.12.26 - 2015.02.15</p><br />
{$app.m_event.description}<br />
</article>
</div>
<div class="historyDetail"><a class="transparentButtonEvent" onClick="Unity.call('{$app.url}/psychopass_portal/img/wallpaper/{if $app.guser.device_type eq 1}iphone{else}android{/if}/event/wallpaper1.jpg');">特典を入手</a></div>
<!-- /wrapper --></div>

</body>
</html>
