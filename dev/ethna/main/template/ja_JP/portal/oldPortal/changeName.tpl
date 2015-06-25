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
<link rel="stylesheet" href="./css/changeName.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
<script src="./js/changeName.js"></script>
</head>
<body>

<input type="hidden" id="php_server_domain" value="{$app.domain}">


<div id="wrapper">
<div id="background">
<img id="area" src="./img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div>


<div id="controlBox">
<div id="changeName">

	<p id="title" style="">
		住人登録名を変更する
	</p>
	<p id="name" style="">住人登録名（ニックネーム）</p>
    <input type="text" id="nickname" name="nickname">
    <p class="limit">※9文字以内</p>

	<p id="nameEn" style="">住人登録名（英数字）</p>
    <input type="text" id="ruby" name="ruby">
    <p class="limit">※英数字のみ、9文字以内</p>

	<a class="changeNamebtn" id="accept" href="javascript:void(0);">変更する</a>

</div>


<div id="errBox" style="display:none;">

    <p id="title">
        エラー
    </p>
    <p id="errMsg">住人登録名（ニックネーム）を正しく入力してください。</p>

    <a class="backBtn" id="back" href="javascript:void(0);">戻る</a>
</div>

<a href="" id="overlay"></a>
</div>



<footer>

    <div id="sns">
        <a onClick="Unity.call('https://twitter.com/share?text=「PSYCHO-PASS サイコパス 公式アプリ」ゲームモードを追加して好評配信中♪公安局刑事課一係のメンバーとともに潜在犯を追え！ http://psycho-pass.cave.co.jp/lp/ #pp4sp #サイコパス');"><img src="./img/common/twitter.png" alt=""></a>
        <a onClick="Unity.call('https://www.facebook.com/sharer/sharer.php?app_id=1620269151530399&sdk=joey&u=https%3A%2F%2Fwww.facebook.com%2Fpages%2FPsycho-Pass-%25E3%2582%25B5%25E3%2582%25A4%25E3%2582%25B3%25E3%2583%2591%25E3%2582%25B9-Official-App%2F430791707090830%3Fref%3Dhl&display=popup&ref=plugin&src=share_button');"><img src="./img/common/facebook.png" alt=""></a>
        
    </div>
    
    <div id="Pt">
        所持ポイント：{$app.user.point}pt
    </div>

</footer>
</body>
</html>
