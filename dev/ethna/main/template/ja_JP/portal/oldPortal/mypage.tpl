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
<link rel="stylesheet" href="./css/mypage.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
</head>
<body>
<input type="hidden" id="php_server_domain" value="{$app.domain}">
<div id="wrapper">

<div id="background">
<img id="area" src="./img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<div id="controlBox">

<a class="transparentButton" onClick="Unity.call('{$app.url}/psychopass_portal/history.php');">イベントクリア履歴を見る</a>

<header id="windowHeader" class="profile">
<h1><img class="theme" src="./img/theme/{$app.user.theme_name}/tabProfile.png" alt="PROFILE" width="121" height="33"></h1>
</header>

<div id="profileWindow">
<article id="contents">

<div id="imgArea"><img id="profileImg" src="./img/mypage/{if $app.user.sex eq 1}taro{else}hanako{/if}.png" alt="" width="114" height="139"></div>

<div id="profileArea">
<div id="profile">

<p id="name">{$app.user.user_name}</p>

<table>
<tr>
<th id="nameEn">{$app.user.user_name_en}</th>
</tr>
<tr>
<td id="id">ID : {$app.user.pp_id}</td>
</tr>
<tr>
<td id="codeName">CODE NAME : COMMISA</td>
</tr>
<tr>
<td id="loginCount">NUMBER OF LOGINS : {$app.user.login_num}</td>
</tr>
</table>

<!-- /profile --></div>
<!-- /profileArea --></div>

</article>
<!-- /profileWindow --></div>
<a class="transparentButton" onClick="Unity.call('{$app.url}/psychopass_portal/changeName.php');" style="margin-top:15px;">名前を変更する</a>


<!-- /controlBox --></div>

<!-- /wrapper --></div>


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
