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

<!--キャプション-->

<a id="cap-back" style="margin-bottom:20px;">「サイコパスる大捜査線」<br />
コンプリート</a>

<!--キャプション-->


<div id="controlBox" style="text-align:center;position:relative;">
<span style="margin:0 0 10px;color:#0f0;font-size:36px;font-weight:700;text-align:center;display:block;">限定壁紙入手!!</span><br />
<span style="color:#fff;text-align:center;display:block;margin-bottom:3px;">▼画像をタップしてダウンロード</span>
<a onClick="Unity.call('{$app.url}/psychopass_portal/img/wallpaper/{if $app.user.user_agent eq 1}iphone{else}android{/if}/event/wallpaper1.jpg');"><img class="theme" src="img/wallpaper/{if $app.user.user_agent eq 1}iphone{else}android{/if}/event/thumbnail/wallpaper1.jpg" width="50%"></a><br />

<div style="position:relative;margin:30px 0 0;">
<a onClick="Unity.call('https://twitter.com/share?text=公安局極秘暗号を全て入手！褒章の限定壁紙を獲得！&hashtags=サイコパスる大捜査線&url=http://psycho-pass.cave.co.jp/');" style="display:inline-block;width:50%;"><img src="./img/common/PsychoPass_LP_SPi_23.png" alt="" style="width:90%;height:auto;"></a><a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=309437425817038&u=http%3A%2F%2Fpsycho-pass.cave.co.jp%2F&display=popup&ref=plugin');" style="display:inline-block;width:50%;"><img src="./img/common/PsychoPass_LP_SPi_21.png" alt="" style="width:90%;height:auto;"></a>
</div>

<!-- /controlBox --></div>

<!-- /wrapper --></div>

</body>
</html>
