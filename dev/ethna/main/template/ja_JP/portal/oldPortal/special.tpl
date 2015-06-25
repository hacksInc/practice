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
<link rel="stylesheet" href="./css/modal.css" media="all">
<link rel="stylesheet" href="./css/owl.carousel/owl.carousel.css" media="all">
<link rel="stylesheet" href="./css/owl.carousel/owl.theme.psychopass.css" media="all">
<link rel="stylesheet" href="./css/special.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/owl.carousel.min.js"></script>
<script src="./js/common.js"></script>
<script src="./js/special.js"></script>
</head>
<body>

<input type="hidden" id="php_theme_json" value="{$app_ne.theme_info}">
<input type="hidden" id="php_user_point" value="{$app.user.point}">
<input type="hidden" id="php_server_domain" value="{$app.domain}">

<div id="wrapper">

<div id="background">
<img id="area" src="./img/common/area.png" alt="" width="80" height="80">
</div><!-- /background -->

<!--戻るボタン-->
        <a id="btn-back" onClick="Unity.call('{$app.url}/psychopass_portal/content.php');"><img src="./img/common/btn_back.png"></a>
<!--/戻るボタン-->

<style type="text/css">


div#pointArea {
	width:100%;
	text-align:right;
	}
div#pointArea table{
	width:70%;
	display:inline-block;
}

td#point {
	width:60%;
	}

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

<div id="pointArea">
<table>
<tr>
<th>所持<br>ポイント</th>
<td id="point">{$app.user.point}<span>pt</span></td>
</tr>
</table>
<!-- /pointArea --></div>


<div id="themeNote">
<a class="cap">アプリのテーマを設定する事ができます。<br>
キャラクターを選択してください。</a>
<!-- /note --></div>

<div id="prevButton" class="nav"><img class="theme" src="./img/theme/{$app.user.theme_name}/arrowL.png" alt="" width="21" height="55"></div>
<div id="nextButton" class="nav"><img class="theme" src="./img/theme/{$app.user.theme_name}/arrowR.png" alt="" width="21" height="55"></div>

<div id="owl-carousel">
<a class="link"><div id="kogami" class="oneTheme lock">kogami</div></a>
<a class="link"><div id="tsunemori" class="oneTheme lock">tsunemori</div></a>
<a class="link"><div id="kagari" class="oneTheme lock">kagari</div></a>
<a class="link"><div id="ginoza" class="oneTheme lock">ginoza</div></a>
<a class="link"><div id="kunizuka" class="oneTheme lock">kunizuka</div></a>
<a class="link"><div id="masaoka" class="oneTheme lock">masaoka</div></a>
<a class="link"><div id="karanomori" class="oneTheme lock">karanomori</div></a>

</div>

<!-- /wrapper --></div>

</body>
</html>
