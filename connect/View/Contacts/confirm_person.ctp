<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<title>Connect(コネクト) IT/webフリーランスの案件/求人情報</title>
<meta name="keywords" content="フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事">
<meta name="description" content="Connect(コネクト)はITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイト。キャリア相談〜案件紹介、アフターフォローまでIT/webフリーランスをトータルサポート！">
<script>
	if(!navigator.userAgent.match(/(iPhone|iPad|iPod|Android)/)){
		document.write('<link rel="stylesheet" href="../css/contact.css">');
	}else{
		document.write('<link rel="stylesheet" href="../css/sp_contact.css">');
	}
</script>
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
<main class="main">
	<div class="container">
		<div class="main_content">
			<div class="contact">
				<ul class="process">
					<li>内容の入力</li>
					<li class="now">内容の確認</li>
					<li>エントリー完了</li>
				</ul>
				<?php
					echo $this->Form->create('Contact', array('action' => 'complete', 'method' => 'post'));
				?>
				<div class="contact_inner">
					<dl class="must">
						<dt>氏名</dt>
						<dd><?php echo $contact['sei'].' '.$contact['mei']; ?></dd>
					</dl>
					<dl class="must">
						<dt>氏名</dt>
						<dd><?php echo $contact['sei'].' '.$contact['mei']; ?></dd>
					</dl>
					<dl class="must">
						<dt>氏名(カナ)</dt>
						<dd><?php echo $contact['sei_kana'].' '.$contact['mei_kana']; ?></dd>
					</dl>
					<dl class="must">
						<dt>メールアドレス</dt>
						<dd><?php echo $contact['email']; ?></dd>
					</dl>
					<dl class="must">
						<dt>電話番号</dt>
						<dd><?php echo $contact['tel']; ?></dd>
					</dl>
					<dl class="must">
						<dt>お問い合わせ内容</dt>
						<dd><?php echo $contact['content']; ?></dd>
					</dl>
				<!-- contact_inner --></div>
				<div class="entry">
					<?php echo $this->Form->submit('この内容で応募する', array('class' => 'submit', 'div' => false, 'name' => 'complete')); ?>
					<?php echo $this->Form->end(); ?>
				</div>
				<a class="back" href="javascript:void(0);" onClick="history.back();">＜ 戻る</a>
			<!-- contact --></div>
		<!-- main_content --></div>
	<!-- container --></div>
</main>
<footer class="footer">
	<div class="copyright"><p>Copyright © IT/webフリーランスと企業をつなぐ案件/求人情報 Connect All Rights Reserved.</p></div>
</footer>
</body>
</html>