<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>information</title>
<meta name="keywords" content="">
<meta name="description" content="">
<link rel="stylesheet" href="/www/css/help/reset.css">
<link rel="stylesheet" href="/www/css/help/info_list.css">
<script src="/www//js/help/jquery.js"></script>
<script type="text/javascript" src="/www/js/help/common.js"></script>
</head>
<body>
<div id="wrapper">
	<ul class="content">
		{foreach from=$app.list item="row" key="i"}
		<li class="list">
		<img class="bar" src="/www/img/help/bar.png" width="100%">
		<div class="frame1">
			<div class="title">
				<div class="title_logo"><img src="/www/img/help/title_logo.png" width="100%"></div>
				<h2>{$app_ne.list[$i].title}</h2>
				{if $row.is_new == 1}<div class="new"><img src="/www/img/help/new.png" width="100%"></div>{/if}
			</div>
		</div>

		<div class="frame2">
			<div class="info">
				<p class="text">{$app_ne.list[$i].abridge}</p>
				{if $row.banner != ""}
				<div class="banner">
					<img src="./newscontentimage?img_id={$row.content_id}&type=banner&dummy={$app.mtime}" width="100%">
				</div>
				{/if}
			</div>
		</div>

		<div class="frame3">
			<div class="date">DATE:{$row.date_disp_short}</div>
			<div class="detail_btn">
				<img onClick="location.href='./infoDetail?content_id={$row.content_id}'" src="/www/img/help/detail_button.png" width="100%" >
			</div>
		<div class="tri"> </div>
		<div class="tri2"> </div>
		<div class="tri3"> </div>
		</div>
		</li>
		{/foreach}
	</ul>
</div>
</body>
</html>

