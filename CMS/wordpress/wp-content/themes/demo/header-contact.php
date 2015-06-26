<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<!-- タイトルの表示。wp_titleで現在のページのタイトル取得。引数で文字列を追加。 bloginfoでブログ自体のタイトルを追加 -->
<title>お問い合わせ | <?php bloginfo('name'); ?></title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<!-- functions.phpに記述したアクションの呼び出し -->
<?php wp_head(); ?>
<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/css/contact.css">
</head>
<body>
<div id="header">
	<div class="top">
		<h1>hoge</h1>
		<!-- functions.phpで登録したメニューの呼び出し -->
		<!-- contentは自動で付与されるdiv。theme_locationはmenuｂの指定。items_wrapはどのように表示するか -->
		<?php wp_nav_menu( array('container' => 'false', 'theme_location' => 'menu', 'items_wrap' => '<ul class="menu">%3$s</ul>')); ?>

		<!-- スマホ用メニュー -->
		<a href="#" id="toggle"><span class="icon"></span></a>
	<!-- top --></div>
<!-- header --></div>
<div id="navi">
	<div class="breadcrumb">
		<ol>
			<li><a href="/">トップ</a></li>
			<li>お問い合わせ</li>
		</ol>
	</div>
</div>
<div id="main">