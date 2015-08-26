<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $title; ?></title>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<meta name="description" content="<?php echo $description; ?>" />
<!-- OGP -->
<html class="no-js" prefix="og: http://ogp.me/ns#" />
<meta property="og:title" content="<?php echo $title; ?>" />
<meta property="og:type" content="<?php echo $ogtype; ?>" />
<meta property="og:image" content="https://connect-job.com/img/facebook_ogp.png" />
<meta property="og:url" content="<?php echo $ogurl; ?>" />
<meta property="og:locale" content="ja_JP" />
<meta property="og:description" content="<?php echo $description; ?>" />
<meta property="og:site_name" content="Connect" />
<meta property="fb:app_id" content="868440269892811" />
<!-- end OGP -->

<link rel="shortcut icon" href="http://faviconist.com/icons/648e5902da8b05381f40d4323a226fa5/favicon.ico" />
<?php
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if ((strpos($ua, 'iPhone') !== false) || (strpos($ua, 'iPod') !== false) || (strpos($ua, 'iPad') !== false) || (strpos($ua, 'Android') !== false) || (strpos($ua, 'Windows Phone') !== false) || (strpos($ua, 'BlackBerry') !== false)) : ?>
<link rel="apple-touch-icon" href="/img/webclip.png" />
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
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.4&appId=868440269892811";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
<?php echo $this->element('header'); ?>
<?php echo $this->fetch('content'); ?>	
<?php echo $this->element('footer'); ?>
</body>
</html>