
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