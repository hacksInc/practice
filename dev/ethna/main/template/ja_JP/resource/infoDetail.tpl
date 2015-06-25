<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>information</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="stylesheet" href="/www/css/help/reset.css">
<link rel="stylesheet" href="/www/css/help/info_detail.css">
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
		{if $app.row.banner != ""}
			<div class="banner">
				<img src="./newscontentimage?img_id={$app.row.content_id}&type=banner&dummy={$app.mtime}" width="100%">
			</div>
			{/if}
			<p class="text">{$app_ne.body}</p>
			{if $app.row.picture != ""}
			<div class="banner">
				<img src="./newscontentimage?img_id={$app.row.content_id}&type=picture&dummy={$app.mtime}" width="100%">
			</div>
			{/if}
		<!-- content --></div>
	<!-- content_box --></div>
	<img src="/www/img/help/help_back_btn.png" class="back_button" onclick="back();">
<!-- wrappar --></div>
</body>
</html>
