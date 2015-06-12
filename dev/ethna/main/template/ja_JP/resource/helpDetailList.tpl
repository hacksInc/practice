<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>helpDetailList</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="stylesheet" href="/www/css/help/reset.css">
<link rel="stylesheet" href="/www/css/help/help_detail_list.css">
<script src="/www//js/help/jquery.js"></script>
<script type="text/javascript" src="/www/js/help/common.js"></script>
<script type="text/javascript" src="/www/js/help/scrollbar.js"></script>
<script type="text/javascript" src="/www/js/help/jquery.nicescroll.min.js"></script>

</head>
<body>
<div id="wrapper">
	<ul class="content">
		{foreach from=$app.list item="row" key="i"}
			<li><a href="./helpDetail?help_id={$row.help_id}">{$app_ne.list[$i].title}</a></li>
		{/foreach}
	</ul>
	<a href="./helpList"><img src="/www/img/help/help_back_btn.png" class="back_button"></a>
<!-- wrappar --></div>
</body>
</html>

