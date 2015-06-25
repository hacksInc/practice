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
<link rel="stylesheet" href="./css/portal2/reset.css" media="all">
<link rel="stylesheet" href="./css/portal2/common.css" media="all">
<link rel="stylesheet" href="./css/portal2/content.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<link rel="stylesheet" href="./css/portal2/event.css" media="all">
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
        <a id="btn-back" onClick="Unity.call('{$app.url}/psychopass_portal/content.php');"><img src="./img/common/btn_back.png" style="width:80px;height:auto;"></a>
<!--/戻るボタン-->

<div id="controlBox">
<header id="windowHeader" class="newsList">
<h1><img class="theme" src="./img/theme/{$app.user.theme_name}/tabList.png" alt="NEWS LIST" width="139" height="33"></h1>
</header>
{foreach from=$app.m_serial key="serial_id" item="row"}
	{if isset($app.u_serial[$serial_id]) eq true}
		<div style="position:relative"><a class="transparentButtonEvent" onClick="Unity.call('{$app.url}/psychopass_portal/eventDetail.php?serial_id={$serial_id}');">{$row.name}</a><img src="./img/common/clear_icon.png" alt="CLEAR!!" style="max-width:74px;height:auto;position:absolute;top:6px;right:0px;"></div>
	{else}
		<div style="position:relative"><a class="transparentButtonEvent" onClick="Unity.call('{$app.url}/psychopass_portal/eventDetail.php?serial_id={$serial_id}');">{$row.name}</a><img src="./img/common/EventIcon.png" alt="アプリ連動キャンペーン" style="max-width:115px;height:42px;position:absolute;top:0px;right:0px;"></div>
	{/if}
{/foreach}
<br />

{if count($app.u_serial) >= 5}
	<a class="transparentButton" onClick="Unity.call('{$app.url}/psychopass_portal/eventPrize.php');" style="color:#0f0;">任務完了褒章</a><br />
{else}
	<br /><div style="position:relative;background-color:rgba(0,0,0,0.56);border:solid 1px #666666;"><span style="color:#fff;background-color:rgba(0,0,0,0);border:none;">全事件の任務を完了させると、超限定スマホ壁紙をプレゼント!!<br />1～5の各事件を解決すると、<span style="color:#ff0000;font-weight:bold;">公安局極秘暗号</span>を入手できる！公式アプリで各暗号を入力し、5つ全ての暗号を入力すると超限定壁紙が…!!<br /></span></div><br /><span id="lockButton">任務完了褒章</span>
{/if}
<!-- /controlBox --></div>

<!-- /wrapper --></div>

</body>
</html>
