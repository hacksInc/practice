<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>アニメ「PSYCHO-PASS サイコパス」スマホ公式アプリ　お問い合わせ入力</title>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
<link rel="stylesheet" href="css/style.css" />
</head>

<body>
<h1>お問い合わせフォーム</h1>
<p>以下のフォームにご入力の上、「確認画面に進む」ボタンをクリックしてください。<br/><br/>
アニメ「PSYCHO-PASS サイコパス」スマホ公式アプリに関するご意見・お問い合わせについては全ての返信はおこなっておりませんが、内容は全て確認させていただき改善に活用させて頂いております。
</p>
{if $err_summary != ""}
  <span class="errorMessage">{$err_summary}</span>
{/if}

<!--
<div style="text-align:center;">
  <p class="button">
    <a href="http://www.jugmon.net/faq/" class="button button-blue" target="_blank"><span>よくあるお問い合わせ</span></a>
  </p>
</div>
<p class="alignCenter">お問い合わせの前に必ずご確認ください。</p>
-->

<p class="supplement">※窓口の営業時間は平日10：00～18：00となります。<br><br>
※返信にはお時間を頂く場合がございます。<br><br>
※問題発生から、7日以上が経過した内容については対応致しかねます。問題が発生した場合、お早めにご連絡ください。</p>
<p><font>*</font>は必須項目です。</p>
<form action="confirm.php" method="post">
<p class="bartitle">
  <font>*</font>返信用メールアドレス
{if $err_mail != ""}
  <span class="errorMessage">{$err_mail}</span>
{/if}
</p>
<input type="email" name="mail"  value="{$mail|escape}">
<span class="supplement">※</span>運営チームからの確認が必要な場合、ご入力頂いたメールアドレスに返信いたします。メールアドレスに誤りがないよう、問い合わせ前にご確認ください。<br/>
<span class="supplement">※</span>メールの指定受信を行っている場合、弊社からのメールが受信できるように「@cave.co.jp」の許可設定をお願い致します。
<br><br>
<p class="bartitle">
  <font>*</font>ニックネーム
{if $err_nickName != ""}
  <span class="errorMessage">{$err_nickName}</span>
{/if}
</p>
<input type="text"name="nickName" value="{$nickName|escape}" maxlength="10"><span class="supplement">※</span>アプリ内の「MYPAGE」よりご確認頂けます。
<br><br>
<p class="bartitle"><font>*</font>ご利用OS</p>
<div class="clearfix marginTopBottom10">
<div class="leftFloat">
<input type="radio" name="useOS" id="useOS" value="iOS"
{if $useOS == "iOS"}
  checked="checked"
{/if}
  class="leftFloat">
<span class="radio">iOS(App Store)</span>
</div>
<div class="leftFloat marginTopBottom10">
<input type="radio" name="useOS" id="useOS" value="Android"
{if $useOS == "Android"}
  checked="checked"
{/if}
class="leftFloat">
<span class="radio">Android(Google Play)</span>
</div>
</div>
<p class="bartitle"><font>*</font>ご利用機種
{if $err_useModel != ""}
  <span class="errorMessage">{$err_useModel}</span>
{/if}
</p>
<input type="text" name="useModel" id="useModel" size="50" value="{$useModel|escape}"><br>
<span class="supplement">※</span>ご利用機種名を入力してください。例):iPhone5、SO-02E、SH-04E
<br>
<p class="bartitle"><font>*</font>発生日時
{if $err_date != ""}
  <span class="errorMessage">{$err_date}</span></p>
{/if}
</p>
{php}
  if(strftime('%Y') > 2014){
    $this->assign('start_year',strftime('%Y') -1);
  }else{
    $this->assign('start_year',2014);
  }
{/php}
<div class="maxWidth">
  {html_select_date
      prefix          = "date_"
      start_year      = $start_year
      reverse_years   = "true"
      field_order     = "Y--"
      time            = $date
      year_empty      = "-"
      all_extra       = 'class="year"'
    }<span class="fontmargin1">年</span>
  {html_select_date
      prefix          = "date_"
      month_format    = "%m"
      field_order     = "M--"
      time            = $date
      month_empty     = "-"
      all_extra       = 'class="month"'
    }<span class="fontmargin1">月</span>
    {html_select_date
      prefix          = "date_"
      field_order     = "D--"
      time            = $date
      day_empty       = "-"
      all_extra       = 'class="day"'
    }<span class="fontmargin1">日</span>
    {html_options name=date_Hour options=$hourList selected=$date_Hour class="hour"}
    <span class="fontmargin1">時ごろ</span>
</div>
<br>
<p class="bartitle"><font>*</font>お問い合わせカテゴリ</p>
{html_options name=category options=$categoryList selected=$category}

<br><br>
<p class="bartitle"><font>*</font>お問い合わせ内容
{if $err_content != ''}
  <span class="errorMessage">{$err_content}</span>
{/if}
</p>
<textarea name="content" id="message" rows="5" cols="40">{$content|escape}</textarea><br/><br/>
<span class="supplement">※</span>お問い合わせ内容を入力してください。1000文字までとなります。
<p>
<input type="hidden" name="sid" value="{$sid|escape}">
<input type="hidden" name="agent" value="{$agent|escape}">
<input type="submit" value="確認画面に進む">
</p>
</form>
<p class="bartitle">窓口からの返信に関するご案内</p>
<p>
窓口からのお返事は平日10～18時の間にメールにてご案内を行っております。<br>
会員様のデータの調査が必要な内容については、お返事まで2～3営業日程度お時間をいただく場合がございます。
<br>
</p>
</body>
</html>