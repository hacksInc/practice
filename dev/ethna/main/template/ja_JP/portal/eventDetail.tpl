<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="./css/portal2/reset.css" media="all">
<link rel="stylesheet" href="./css/portal2/common.css" media="all">
<link rel="stylesheet" href="./css/portal2/content.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/portal2/jquery.js"></script>
<script src="./js/portal2/common.js"></script>
</head>
<body>

<div id="wrapper">

<div id="background">
<img id="area" src="./img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="./img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<!--戻るボタン-->
        <a id="btn-back" onClick="Unity.call('{$app.url}/psychopass_portal/event.php');"><img src="./img/common/btn_back.png" style="width:80px;height:auto;"></a>
<!--/戻るボタン-->

<header id="windowHeader" class="newsList">
<style>
header#windowHeader.newsList:before{
	clip:auto;
}
</style>
<h1 style="visibility:hidden;"><img class="theme" src="./img/theme/{$app.user.theme_name}/tabRecord.png" alt="NEWS LIST" width="139" height="33"></h1>
</header>

<div id="window2">
<article id="contents" class="eventDetail">
{$app_ne.description}<br />
</article>
</div>
{if $app.m_serial.need_serial eq 1 && $app.m_serial.date_open <= $app.date && $app.date <= $app.m_serial.date_close}
	<div class="eventDetail" style="margin-top:15px;"><a class="transparentButton" onClick="Unity.call('{$app.url}/psychopass_portal/serialInput.php?serial_id={$app.m_serial.serial_id}');">公安局極秘暗号を入力</a></div>
{/if}
<!-- /wrapper --></div>

</body>
</html>
