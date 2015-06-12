<?php /* Smarty version 2.6.28, created on 2014-12-04 18:36:57
         compiled from index.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'index.tpl', 38, false),array('function', 'html_select_date', 'index.tpl', 90, false),array('function', 'html_options', 'index.tpl', 111, false),)), $this); ?>
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
<?php if ($this->_tpl_vars['err_summary'] != ""): ?>
  <span class="errorMessage"><?php echo $this->_tpl_vars['err_summary']; ?>
</span>
<?php endif; ?>

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
<?php if ($this->_tpl_vars['err_mail'] != ""): ?>
  <span class="errorMessage"><?php echo $this->_tpl_vars['err_mail']; ?>
</span>
<?php endif; ?>
</p>
<input type="email" name="mail"  value="<?php echo ((is_array($_tmp=$this->_tpl_vars['mail'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<span class="supplement">※</span>運営チームからの確認が必要な場合、ご入力頂いたメールアドレスに返信いたします。メールアドレスに誤りがないよう、問い合わせ前にご確認ください。<br/>
<span class="supplement">※</span>メールの指定受信を行っている場合、弊社からのメールが受信できるように「@cave.co.jp」の許可設定をお願い致します。
<br><br>
<p class="bartitle">
  <font>*</font>ニックネーム
<?php if ($this->_tpl_vars['err_nickName'] != ""): ?>
  <span class="errorMessage"><?php echo $this->_tpl_vars['err_nickName']; ?>
</span>
<?php endif; ?>
</p>
<input type="text"name="nickName" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['nickName'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
" maxlength="10"><span class="supplement">※</span>アプリ内の「MYPAGE」よりご確認頂けます。
<br><br>
<p class="bartitle"><font>*</font>ご利用OS</p>
<div class="clearfix marginTopBottom10">
<div class="leftFloat">
<input type="radio" name="useOS" id="useOS" value="iOS"
<?php if ($this->_tpl_vars['useOS'] == 'iOS'): ?>
  checked="checked"
<?php endif; ?>
  class="leftFloat">
<span class="radio">iOS(App Store)</span>
</div>
<div class="leftFloat marginTopBottom10">
<input type="radio" name="useOS" id="useOS" value="Android"
<?php if ($this->_tpl_vars['useOS'] == 'Android'): ?>
  checked="checked"
<?php endif; ?>
class="leftFloat">
<span class="radio">Android(Google Play)</span>
</div>
</div>
<p class="bartitle"><font>*</font>ご利用機種
<?php if ($this->_tpl_vars['err_useModel'] != ""): ?>
  <span class="errorMessage"><?php echo $this->_tpl_vars['err_useModel']; ?>
</span>
<?php endif; ?>
</p>
<input type="text" name="useModel" id="useModel" size="50" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['useModel'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><br>
<span class="supplement">※</span>ご利用機種名を入力してください。例):iPhone5、SO-02E、SH-04E
<br>
<p class="bartitle"><font>*</font>発生日時
<?php if ($this->_tpl_vars['err_date'] != ""): ?>
  <span class="errorMessage"><?php echo $this->_tpl_vars['err_date']; ?>
</span></p>
<?php endif; ?>
</p>
<?php 
  if(strftime('%Y') > 2014){
    $this->assign('start_year',strftime('%Y') -1);
  }else{
    $this->assign('start_year',2014);
  }
 ?>
<div class="maxWidth">
  <?php echo smarty_function_html_select_date(array('prefix' => 'date_','start_year' => $this->_tpl_vars['start_year'],'reverse_years' => 'true','field_order' => "Y--",'time' => $this->_tpl_vars['date'],'year_empty' => "-",'all_extra' => 'class="year"'), $this);?>
<span class="fontmargin1">年</span>
  <?php echo smarty_function_html_select_date(array('prefix' => 'date_','month_format' => "%m",'field_order' => "M--",'time' => $this->_tpl_vars['date'],'month_empty' => "-",'all_extra' => 'class="month"'), $this);?>
<span class="fontmargin1">月</span>
    <?php echo smarty_function_html_select_date(array('prefix' => 'date_','field_order' => "D--",'time' => $this->_tpl_vars['date'],'day_empty' => "-",'all_extra' => 'class="day"'), $this);?>
<span class="fontmargin1">日</span>
    <?php echo smarty_function_html_options(array('name' => 'date_Hour','options' => $this->_tpl_vars['hourList'],'selected' => $this->_tpl_vars['date_Hour'],'class' => 'hour'), $this);?>

    <span class="fontmargin1">時ごろ</span>
</div>
<br>
<p class="bartitle"><font>*</font>お問い合わせカテゴリ</p>
<?php echo smarty_function_html_options(array('name' => 'category','options' => $this->_tpl_vars['categoryList'],'selected' => $this->_tpl_vars['category']), $this);?>


<br><br>
<p class="bartitle"><font>*</font>お問い合わせ内容
<?php if ($this->_tpl_vars['err_content'] != ''): ?>
  <span class="errorMessage"><?php echo $this->_tpl_vars['err_content']; ?>
</span>
<?php endif; ?>
</p>
<textarea name="content" id="message" rows="5" cols="40"><?php echo ((is_array($_tmp=$this->_tpl_vars['content'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</textarea><br/><br/>
<span class="supplement">※</span>お問い合わせ内容を入力してください。1000文字までとなります。
<p>
<input type="hidden" name="sid" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['sid'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
<input type="hidden" name="agent" value="<?php echo ((is_array($_tmp=$this->_tpl_vars['agent'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
">
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