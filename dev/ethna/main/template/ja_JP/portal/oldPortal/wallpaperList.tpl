<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="css/reset.css" media="all">
<link rel="stylesheet" href="css/common.css" media="all">
<link rel="stylesheet" href="css/wallpaper.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="js/jquery.js"></script>
<script src="js/common.js"></script>
</head>
<body>

<div id="wrapper">

<div id="background">
<img id="area" src="img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<!--戻るボタン-->
        <a id="btn-back" onClick="Unity.call('{$app.url}/psychopass_portal/content.php');"><img src="./img/common/btn_back.png"></a>
<!--/戻るボタン-->

<style type="text/css">

#btn-back{
	position: fixed;
	top:0;
	z-index:99;	
	}
#btn-back img{
	width:80px;
	height:auto;
	}

</style>

<div id="wallpaperArea" style="padding-top:50px;">

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=kogami');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/kogami.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=tsunemori');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/tsunemori.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=ginoza');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/ginoza.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=masaoka');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/masaoka.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=kagari');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/kagari.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=kunizuka');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/kunizuka.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=karanomori');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/karanomori.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=togane');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/togane.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=hinakawa');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/hinakawa.png"></a>
</div>

<div class="characterSelect">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=shimotsuki');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/shimotsuki.png"></a>
</div>

<div class="characterSelect">
<div style="position:relative;">
<a onClick="Unity.call('{$app.url}/psychopass_portal/wallpaper.php?character=other');"><img class="theme" src="img/theme/{$app.user.theme_name}/wallpaper/other.png"><img src="./img/news/new-pink.png" style="max-width:30px;height:auto;position:absolute;top:0px;right:0px;"></a>
</div>
</div>

<!-- /wallpaperArea --></div>

<!-- /wrapper --></div>

</body>
</html>
