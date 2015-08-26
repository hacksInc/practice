<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>ログイン | 株式会社hacks</title>
<meta name="keywords" content="">
<meta name="description" content="">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<?php echo $this->Html->css('login'); ?>
</head>
<body>
<div id="header">
	<div class="title"><h1>hacks</h1></div>
</div>
<div id="main">
	<?php
	if ($this->Session->check('Message.auth'));
	echo $this->Session->flash('auth');
	echo $this->Form->create('User');
	?>
	<div class="login">	
		<h2>ログイン画面</h2>
		<ul>
			<li>
				<p>ユーザー名</p>
				<?php echo $this->Form->input('username', array('label'=>false, 'div' => false, 'id'=>false, 'class' => 'username')); ?>
			</li>
			<li>
				<p>パスワード</p>
				<?php echo $this->Form->input('password', array('label'=>false, 'div' => false, 'id'=>false, 'class' => 'password')); ?>
			</li>
		</ul>
				<?php echo $this->Form->submit('ログイン', array('div' => 'submitForm', 'class' => 'submit')); ?>
		<?php echo $this->Form->end(); ?>
	</div>
</div>
</body>
</html>