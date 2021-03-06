<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>アニメ「PSYCHO-PASS サイコパス」</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<link rel="stylesheet" href="css/portal2/reset.css" media="all">
<link rel="stylesheet" href="css/portal2/common.css" media="all">
<link rel="stylesheet" href="css/portal2/content.css" media="all">
<link rel="stylesheet" href="./css/theme/{$app.user.theme_name}.css" media="all">
<script src="js/portal2/jquery.js"></script>
<script src="js/portal2/common.js"></script>
<script src="js/portal2/input.js"></script>
</head>
<body>

<input type="hidden" id="php_server_domain" value="{$app.domain}">

<div id="wrapper">

<div id="background">
<img id="area" src="img/common/area.png" alt="" width="80" height="80">
<img id="character" class="theme" src="img/theme/{$app.user.theme_name}/chara.png" alt="" width="651" height="804">
</div><!-- /background -->

<!--戻るボタン-->
        <a id="btn-back" onClick="Unity.call('{$app.url}/psychopass_portal/eventDetail.php?serial_id={$app.m_serial.serial_id}');"><img src="./img/common/btn_back.png" style="width:80px;height:auto;"></a>
<!--/戻るボタン-->

<div id="controlBox">

<!--キャプション-->

<a id="cap-back">「サイコパスる大捜査線」<br />
捜査報告</a>

<!--キャプション-->




<form id="form-send" action="" method="post">
	<input id="input-text" type="password" pattern="^[0-9A-Za-z]+$" maxlength="12" placeholder="公安局極秘暗号を入力して下さい" style="display:inline-block;width:100%;height:42px;padding:0 6px;box-sizing:border-box;">
    <input id="input-submit" type="button" value="送信">
</form>

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
