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
<link rel="stylesheet" href="/psychopass_portal/css/portal2/news.css" media="all">
<link rel="stylesheet" href="./css/theme/nonMember.css" media="all">
<script src="./js/jquery.js"></script>
<script src="./js/common.js"></script>
</head>
<body>

<div id="portalBackground">
<img id="backgroundImg" src="/psychopass_portal/img/portal/portalBackground.png" width="100%">

<header class="clearfix">
    <img id="headerTitleFrame" src="/psychopass_portal/img/portal/headerTitle.png" width="100%">
    <img id="mark" src="/psychopass_portal/img/portal/psychoMark.png" width="12%">
    <img id="votePtFrame" src="/psychopass_portal/img/portal/votePt.png" width="100%">
    <img id="portalPtFrame" src="/psychopass_portal/img/portal/portalPt.png" width="100%">
    <a onClick="Unity.call('jump_to_game');"><img id="toGame" src="/psychopass_portal/img/portal/toGame.png" width="100%"></a>
    <p id="headerTitle">EVENT</p>
    <p id="userNumber">{$app.user.pp_id}</p>
    <p id="votePt">{$app.voting.point|intval}pt</p>
    <p id="portalPt">{$app.user.point}pt</p>
</header>

<div id="error">
    <img src="/psychopass_portal/img/portal/balloon.png" width="100%">
    <p id="errorText">
        {foreach from=$errors item=error}
            {$error|nl2br}
        {foreachelse}
            エラーが発生しました。
        {/foreach}
    </p>
</div>

<a id="errorBackText" onClick="Unity.call('{$app.url}/psychopass_portal/mypage.php');">もどる</a><img id="errorBack" src="/psychopass_portal/img/portal/btn.png" width="100%">

<img id="errorChara" src="/psychopass_portal/img/portal/chara1_stand.png" width="100%">
<img id="errorChara_shadow"  src="/psychopass_portal/img/portal/chara_shadow.png" width="100%">

<div id="overlay"></div>

<footer class="clearfix">
    <img id="btnFrame" src="/psychopass_portal/img/portal/btnFrame.png">
    <ul id="btn">
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/index.php');"><img src="/psychopass_portal/img/portal/btn1.png" width="100%"></a></li>
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/news.php');"><img src="/psychopass_portal/img/portal/btn2.png" width="100%"></a></li>
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/content.php');"><img src="/psychopass_portal/img/portal/btn3.png" width="100%"></a></li>
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/info.php');"><img src="/psychopass_portal/img/portal/btn4.png" width="100%"></a></li>
        <li><a onClick="Unity.call('{$app.url}/psychopass_portal/mypage.php');"><img src="/psychopass_portal/img/portal/btn5.png" width="100%"></a></li>
    </ul>
</footer>

</div><!-- portalBackground -->

</body>
</html>
