<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>helpDetail</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="stylesheet" href="/www/css/help/reset.css">
<link rel="stylesheet" href="/www/css/help/help_detail.css">
<script src="/www//js/help/jquery.js"></script>
<script type="text/javascript" src="/www/js/help/common.js"></script>
<script type="text/javascript" src="/www/js/help/scrollbar.js"></script>
<script type="text/javascript" src="/www/js/help/jquery.nicescroll.min.js"></script>
</head>
<body>
<div id="wrapper">
	<div class="content_box">
		<h2>{$app_ne.title}</h2>
		<div class="content">
			<p>{$app_ne.body}</p>
			{if $app.row.picture != ""}
				<div class="banner">
					<img src="./helpdetailimage?img_id={$app.row.help_id}&dummy={$app.mtime}" width="100%">
				</div>
			{/if}
		</div>
	</div>
	<img src="/www/img/help/help_back_btn.png" class="back_button" onclick="back();">
<!-- wrappar --></div>
</body>
</html>

