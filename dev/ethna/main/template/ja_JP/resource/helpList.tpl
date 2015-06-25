<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>helpList</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="stylesheet" href="/www/css/help/reset.css">
<link rel="stylesheet" href="/www/css/help/help_list.css">
<script src="/www//js/help/jquery.js"></script>
<script type="text/javascript" src="/www/js/help/common.js"></script>
<script type="text/javascript" src="/www/js/help/scrollbar.js"></script>
<script type="text/javascript" src="/www/js/help/jquery.nicescroll.min.js"></script>
</head>
<body>
<div id="wrapper">
	<ul class="content">
		{foreach from=$app.list item="row" key="i"}
			<li><a href="./helpDetailList?category_id={$row.category_id}">{$app_ne.list[$i].title}</a></li>
		{/foreach}
	</ul>
</div>
</body>
</html>
