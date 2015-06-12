<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="./css/reset.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/common.css" media="all">
<link rel="stylesheet" href="/psychopass_portal/css/portal2/changeName.css" media="all">
<script src="./js/portal2/jquery.js"></script>
<script src="./js/portal2/common.js"></script>
<script src="./js/portal2/changeName.js"></script>
<script src="./js/portal2/chimichara.js"></script>
</head>
<body>

<input type="hidden" id="chimiFlg" value="1">
<input type="hidden" id="php_theme_json" value="{$app_ne.theme_info}">
<input type="hidden" id="php_theme_name" value="{$app.user.theme_name}">
<input type="hidden" id="php_server_domain" value="{$app.domain}">


<div id="portalBackground">

<header class="clearfix">
    <img id="headerTitleFrame" src="/psychopass_portal/img/portal/headerTitle.png" width="100%">
    <img id="mark" src="/psychopass_portal/img/portal/psychoMark.png" width="12%">
    <img id="votePtFrame" src="/psychopass_portal/img/portal/votePt.png" width="100%">
    <img id="portalPtFrame" src="/psychopass_portal/img/portal/portalPt.png" width="100%">
    <a onClick="Unity.call('jump_to_game');"><img id="toGame" src="/psychopass_portal/img/portal/toGame.png" width="100%"></a>
    <p id="headerTitle">MYPAGE</p>
    <p id="userNumber">{$app.user.pp_id}</p>
    <p id="votePt">{$app.voting.point|intval}pt</p>
    <p id="portalPt">{$app.user.point}pt</p>
</header>

<div id="profileArea">
    <img src="/psychopass_portal/img/portal/profile_frame.png" width="100%">

    <img id="profileImg" src="./img/mypage/{if $app.user.sex eq 1}taro{else}hanako{/if}.png" width="100%">

    <img id="nameDecoration" src="/psychopass_portal/img/portal/item.png" width="100%">
    <p id="name">{$app.user.user_name}</p>
    <table>
    <tr>
        <th><p>NAME</p></th><td>: {$app.user.user_name_en}</td>
    </tr>
    <tr>
        <th><p>ID</p></th><td>: {$app.user.pp_id}</td>
    </tr>
    <tr>
        <th><p>ログイン数</p></th><td>: {$app.user.login_num}</td>
    </tr>
    </table>

</div><!-- profileArea -->

<div id="changeName">

	<p id="title">
		住人登録名を変更する
	</p>
	<p id="addName">住人登録名（ニックネーム）</p>
    <input type="text" id="nickname" name="nickname">
    <p class="limit">※9文字以内</p>

	<p id="nameEn">住人登録名（英数字）</p>
    <input type="text" id="ruby" name="ruby">
    <p class="limit">※英数字のみ、9文字以内</p>

</div>

<div id="errBox" style="display:none;">

    <p id="title">
        エラー
    </p>
    <p id="errMsg">住人登録名（ニックネーム）を正しく入力してください。</p>

    <a onClick="cancel();" class="backBtn" id="back" >閉じる</a>
</div>

<a onClick="accept();" class="changeText">変更する</a>
<img id="changeBtn" src="/psychopass_portal/img/portal/btn.png" width="100%">

<a class="backLink2" onClick="Unity.call('{$app.url}/psychopass_portal/mypage.php');">もどる</a><img class="backBtn2" src="/psychopass_portal/img/portal/btn.png" width="100%">

<footer class="clearfix">
    <img id="btnFrame" src="/psychopass_portal/img/portal/btnFrame.png">
    <ul id="btn">
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/index.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn1.png" width="100%"></a></li>
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/news.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn2.png" width="100%"></a></li>
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn3.png" width="100%"></a></li>
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/info.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn4.png" width="100%"></a></li>
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/mypage.php');"><img class="footerBtn" src="/psychopass_portal/img/portal/btn5.png" width="100%"></a></li>
    </ul>
</footer>


<a  onClick="cancel();" id="overlay"></a>

</div><!-- portalBackground -->

</body>
</html>