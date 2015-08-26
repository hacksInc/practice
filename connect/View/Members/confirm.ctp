
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
					echo $this->Form->create('Member', array('action' => 'complete'));
				?>
				<div class="contact_inner">
						<dl class="must">
							<dt>氏名</dt>
							<dd><?php echo h($member['sei']).' '.h($member['mei']); ?></dd>
						</dl>
						<dl class="must">
							<dt>氏名(カナ)</dt>
							<dd><?php echo h($member['sei_kana']).' '.h($member['mei_kana']); ?></dd>
						</dl>
						<dl class="must">
							<dt>性別</dt>
							<dd><?php echo h($member['sex']) == 1 ? '男性' : '女性' ; ?></dd>
						</dl>
						<dl class="must">
							<dt>生年月日</dt>
							<dd><?php echo h($member['year']).'年 '.h($member['month']).'月 '.h($member['day']).'日'; ?></dd>
						</dl>
						<dl class="must">
							<dt>メールアドレス</dt>
							<dd><?php echo h($member['email']); ?></dd>
						</dl>
						<dl class="must">
							<dt>電話番号</dt>
							<dd><?php echo h($member['tel']); ?></dd>
						</dl>
						<dl class="must">
							<dt>最寄駅</dt>
							<dd><?php echo h($member['station']); ?></dd>
						</dl>
						<dl class="skillsheet">
							<dt>スキルシート<br>
								(職務経歴書)
							</dt>
							<dd><?php echo h($member['File']['name']); ?></dd>
						</dl>
						<dl>
							<dt>
								得意分野/スキル<br>
								ポートフォリオ(URL)等
							</dt>
							<dd><?php echo h($member['have_skill']); ?></dd>
						</dl>
						<dl>
							<dt>ご希望の案件/条件など</dt>
							<dd><?php echo h($member['hope']); ?></dd>
						</dl>
						<dl>
							<dt>その他</dt>
							<dd><?php echo h($member['other']); ?></dd>
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