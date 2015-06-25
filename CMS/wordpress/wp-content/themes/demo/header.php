<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<!-- タイトルの表示。wp_titleで現在のページのタイトル取得。引数で文字列を追加。 bloginfoでブログ自体のタイトルを追加 -->
<title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<!-- functions.phpに記述したアクションの呼び出し -->
<?php wp_head(); ?>
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
	<?php if (is_home()) : //トップページの場合、画像を出す ?>
		<div class="topimage"><img src="<?php echo get_stylesheet_directory_uri().'/images/topimage.jpg';?>" alt="" width="100%"></div>

	<?php else : // トップページじゃない場合、パンくずリストを出す ?>
		<div class="breadcrumb">
			<ol>
				<li><a href="/">トップ</a></li>
			<?php if (is_page() && !$post->post_parent) : //固定ページかつ子ページじゃない時 ?>
				<li><?php the_title(); ?></li>
			<?php elseif(is_single()) : //上記以外 ?>
				<?php $cat = get_the_category(); $cat = $cat[0]; ?>
				<li><a href="<?php echo $cat->category_nicename; ?>"><?php echo $cat->cat_name; ?></a></li>
				<li><?php the_title(); ?></li>
			<?php else : ?>
				<li><?php the_title(); ?></li>
			<?php endif; ?>
			</ol>
		</div>
	<?php endif; ?>
</div>
<div id="main">