<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>管理画面 | 株式会社hacks</title>
<meta name="keywords" content="">
<meta name="description" content="">
<?php echo $this->fetch('meta'); ?>
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="format-detection" content="telephone=no, email=no">
<?php echo $this->fetch('css'); ?>
<script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<?php echo $this->Html->script('admin'); ?>
<?php echo $this->fetch('script'); ?>
</head>
<body>
<div id="header">
	<div class="title"><h1>hacks</h1></div>
	<ul class="navi">
		<li><?php echo $this->Html->link('Log out', array('controller' => 'users', 'action' => 'logout')); ?></li>
		<li><?php echo $this->Session->flash(); ?></li>
	</ul>
<!-- header --></div>
<div id="main">
<div id="sidebar">
	<ul class="menu">
		<li><a href="">ダッシュボード</a></li>
		<li>
			<a href="/projects">プロジェクト</a>
			<ul class="menuList">
				<li><a href="/projects/add">新規登録</a></li>
			</ul>
		</li>
		<li>
			<a href="">フリーランス</a>
			<ul class="menuList">
			</ul>
		</li>
		<li>
			<a href="">クライアント</a>
			<ul class="menuList">
			</ul>
		</li>
		<li>
			<a href="">パートナー</a>
			<ul class="menuList">
			</ul>
		</li>
	</ul>
<!-- sidebar --></div>
	<?php echo $this->fetch('content'); ?>
<!-- main --></div>
</body>
</html>