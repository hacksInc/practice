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

<div id="wrapper">

<div id="background">
<img id="area" src="./img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="./img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<div id="pointArea">
<table>
<tr>
<th>所持<br>ポイント</th>
<td id="point">{$app.user.point}<span>pt</span></td>
</tr>
</table>
<!-- /pointArea --></div>

<div id="themeNote">
テーマを切り替えました。<br>
<div style="text-align:center;"><a onClick="Unity.call('{$app.url}/psychopass_portal/special.php');">テーマ選択へ</a></div>
<!-- /note --></div>

<!-- /wrapper --></div>

</body>
</html>
