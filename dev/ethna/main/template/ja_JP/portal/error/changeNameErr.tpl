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
<link rel="stylesheet" href="./css/top.css" media="all">
<link rel="stylesheet" href="./css/theme/nonMember.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
</head>
<body>

<div id="wrapper">

<div id="background">
<img id="area" src="img/common/area.png" alt="" width="80" height="80">
</div><!-- /background -->

<div id="controlBox">

<div id="changeName" style="position:fixed;top:20%;left:10%;width:60%;height:55%; background:rgba(0,0,0,0.6); border: 1px solid #f00;padding:0 10%">

	<p id="finish" style="position:absolute;top:10%;left17%;width:72%;color:#f00;text-align:center;font-size:120%;border-bottom:1px solid #f00;">
		※エラー
	</p>
<article id="contents" style="position:absolute;top:30%;left:10%;width:80%;text-align:center;color:#f00;">
{foreach from=$errors item=error}
	{$error|nl2br}<br />
{foreachelse}
	エラーが発生しました。<br />
{/foreach}
</article>

<a class="transparentButton" href="javascript:void(0);" onclick="Unity.call('{$app.url}/psychopass_portal/changeName.php');" style="position:absolute;bottom:5%;left:30%;width:40%; margin:0 auto; text-align:center;color:#f00;border:1px solid #f00;">戻る</a>
</div>


<!-- /controlBox --></div>

<!-- /wrapper --></div>

</body>
</html>
