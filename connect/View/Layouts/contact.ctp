<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $title; ?></title>
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="description" content="<?php echo $description; ?>">
<link rel="shortcut icon" href="http://faviconist.com/icons/648e5902da8b05381f40d4323a226fa5/favicon.ico" />
<?php
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if ((strpos($ua, 'iPhone') !== false) || (strpos($ua, 'iPod') !== false) || (strpos($ua, 'iPad') !== false) || (strpos($ua, 'Android') !== false) || (strpos($ua, 'Windows Phone') !== false) || (strpos($ua, 'BlackBerry') !== false)) : ?>
<link rel="stylesheet" href="/css/sp/sp_<?php echo $css; ?>.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="/js/sp/sp_<?php echo $js; ?>.js"></script>
<?php else : ?>
<link rel="stylesheet" href="/css/<?php echo $css; ?>.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="/js/<?php echo $js; ?>.js"></script>
<?php endif; ?>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
</head>
<body>
<noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-PDG9VL"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PDG9VL');</script>
<header class="header">
	<div class="header_inner">
		<div class="header_title">
			<h1>IT/webフリーランスと企業をつなぐ案件/求人情報サイト</h1>
			<p class="header_title_logo">
				<a href="/" alt="Connect">Connect</a>
			</p>
		</div>
	</div>
</header>
<?php echo $this->fetch('content'); ?>
<footer class="footer">
	<div class="copyright"><p>Copyright © IT/webフリーランスと企業をつなぐ案件/求人情報 Connect All Rights Reserved.</p></div>
</footer>
</body>
</html>
