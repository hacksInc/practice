<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title><?php echo $title; ?></title>
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="description" content="<?php echo $description; ?>">
<?php
/*
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if(strpos($ua, '/(iPhone|iPad|iPod|Android|Windows)/')) :
?>
<link rel="stylesheet" href="css/sp_<?php echo $css; ?>.css">
<?php else : ?>
<link rel="stylesheet" href="css/<?php echo $css; ?>.css">
<?php endif; ?>
*/
?>
<script>
	var css = <?php echo json_encode($css, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
	if(!navigator.userAgent.match(/(iPhone|iPad|Android)/)){
		document.write('<link rel="stylesheet" href="/css/'+css+'.css">');
	}else{
		document.write('<link rel="stylesheet" href="/css/sp/sp_'+css+'.css">');
	}
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
<script src="/js/<?php echo $js; ?>.js"></script>
<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->
</head>
<body>
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
