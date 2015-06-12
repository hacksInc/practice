<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>アニメ「PSYCHO-PASS サイコパス」スマホ公式アプリ　お問い合わせ入力</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<link rel="stylesheet" href="css/style.css" />
</head>

<body>
<h1>お問い合わせフォーム確認画面</h1>
<form action="complate.php" method="post">
<p class="bartitle"><font>*</font>返信用メールアドレス</p>
<p>{$mail|escape}</p>
<input type="hidden" name="mail" value="{$mail|escape}">
<br><br>
<!--<p class="bartitle"><font>*</font>あなたのユーザーID</p>
<p>{$yourId|escape}</p>
<input type="hidden" name="yourId" value="{$yourId|escape}">
-->
<br><br>
<p class="bartitle"><font>*</font>ニックネーム</p>
<p>{$nickName|escape}</p>
<input type="hidden" name="nickName" value="{$nickName|escape}">
<br><br>
<!--
<p class="bartitle"><font>*</font>生年月日</p>
<p>{$birthday|escape}</p>
<input type="hidden" name="birthday" value="{$birthday|escape}">
<br>
-->
<p class="bartitle"><font>*</font>ご利用OS</p>
<p>{$useOS_disp|escape}</p>
<input type="hidden" name="useOS" value="{$useOS|escape}">
<p class="bartitle"><font>*</font>ご利用機種</p>
<p>{$useModel|escape}</p>
<input type="hidden" name="useModel" value="{$useModel|escape}">
<br>
<p class="bartitle"><font>*</font>発生日時</p>
<p>{$date|escape} {$date_Hour|escape}</p>
<input type="hidden" name="date" value="{$date|escape}">
<input type="hidden" name="date_Hour" value="{$date_Hour|escape}">
<br>
<p class="bartitle"><font>*</font>お問い合わせカテゴリ</p>
<p>{$category_disp|escape}</p>
<input type="hidden" name="category" value="{$category|escape}">
<br><br>
<p class="bartitle"><font>*</font>お問い合わせ内容</p>
<p>{$content|escape}</p>
<input type="hidden" name="content" value="{$content|escape}">
<p>
<input type="hidden" name="sid" value="{$sid|escape}">
<input type="hidden" name="agent" value="{$agent|escape}">
<input type="submit" value="送信する">
</p>
</form>
</body>
</html>