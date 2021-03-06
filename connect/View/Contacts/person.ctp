
<main class="main">
	<div class="container">
		<div class="main_content">
			<div class="contact">
				<div class="contact_title">
					<h2>お問い合わせ(個人のお客様)</h2>
					<p>法人のお客様のお問い合わせは<a href="/contacts/company">コチラ</a>にてお願い致します。</p>
				</div>
				<ul class="process">
					<li class="now">内容の入力</li>
					<li>内容の確認</li>
					<li>エントリー完了</li>
				</ul>
				<?php
					echo $this->Form->create('Contact', array('action'=>'person', 'method' => 'post'));
					$this->Form->inputDefaults(array('div' => false, 'label' => false, 'id' => false, 'legend' => false));
				?>
				<div class="contact_inner">
						<dl class="must">
							<dt>氏名</dt>
							<dd>
								<?php echo $this->Form->input('sei', array('class' => 'sei', 'placeholder' => '山田')); ?>
								<?php echo $this->Form->input('mei', array('class' => 'mei', 'placeholder' => '太郎')); ?>
							</dd>
						</dl>
						<dl class="must">
							<dt>氏名(カナ)</dt>
							<dd>
								<?php echo $this->Form->input('sei_kana', array('class' => 'sei', 'placeholder' => 'ヤマダ')); ?>
								<?php echo $this->Form->input('mei_kana', array('class' => 'mei', 'placeholder' => 'タロウ')); ?>
							</dd>
						</dl>
						<dl class="must">
							<dt>メールアドレス</dt>
							<dd>
								<?php echo $this->Form->input('email', array('class' => "email", 'placeholder' => 'hogehoge@example.com')); ?>
							</dd>
						</dl>
						<dl class="must">
							<dt>電話番号</dt>
							<dd>
								<?php echo $this->Form->input('tel', array('class' => 'tel', 'placeholder' => '08011112222')); ?>
							</dd>
						</dl>
						<dl class="must">
							<dt>お問い合わせ内容</dt>
							<dd>
								<?php echo $this->Form->input('content', array('type' => 'textarea', 'rows' => 8)); ?>
							</dd>
						</dl>
				<!-- contact_inner --></div>
				<div class="entry">
					<?php echo $this->Form->submit('同意して確認画面へ進む', array('class' => 'submit', 'name' => 'submit', 'div' => false)); ?>
					<?php echo $this->Form->end(); ?>
				</div>
				<?php echo $this->element('policy'); ?>
			<!-- contact --></div>
		<!-- main_content --></div>
	<!-- container --></div>
</main>