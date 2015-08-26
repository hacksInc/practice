
<main class="main">
	<div class="container">
		<div class="main_content">
			<div class="contact">
			<?php
				echo $this->Form->create('Member', array('action'=>'index','enctype' => 'multipart/form-data',));
				$this->Form->inputDefaults(array('div' => false, 'label' => false, 'id' => false, 'legend' => false));
			?>
				<?php if (!empty($project)) : ?>
				<section class="keep_project">
					<h2>＊ エントリーする案件</h2>
					<?php
						foreach ($project as $key) :
							$value = $key['Project'];
					?>
						<section>
						<a href="/projects/<?php echo h($value['id']); ?>" alt="<?php echo h($value['title']); ?>">
							<h3><?php echo h($value['title']); ?></h3>
							<p><?php echo h($value['station'])." / ¥".number_format(h($key['MinPrice']['name']))." 〜 ¥".number_format(h($key['MaxPrice']['name']))." / ".h($key['Position']['name']); ?></p>
						</a>
						<div class="keep_delete_button"><a href="javascript:void(0)" class="keep_delete" value="<?php echo h($value['id']); ?>">削除<span>する</span></a></div>
						<?php echo $this->Form->hidden('Project][', array('value' => h($value['id']), 'id' => false )); ?>
						</section>
					<?php endforeach; ?>
				<!-- keep_project --></section>
				<?php endif; ?>
				<ul class="process">
					<li class="now">内容の入力</li>
					<li>内容の確認</li>
					<li>エントリー完了</li>
				</ul>
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
							<dt>性別</dt>
							<dd>
								<?php echo $this->Form->input('sex', array('type'=>'radio', 'options' => array(1 => '男性','女性'), 'default' => 1, 'label' => true)); ?>
							</dd>
						</dl>
						<dl class="must">
							<dt>生年月日</dt>
							<dd>
								<?php echo $this->Form->input('year', array('type' => 'select', 'class' => 'year', 'options' => array($year), 'empty' => '年')); ?>
								<?php echo $this->Form->input('month', array('type' => 'select', 'class' => 'month', 'options' => array($month), 'empty' => '月')); ?>
								<?php echo $this->Form->input('day', array('type' => 'select', 'class' => 'day', 'options' => array($day), 'empty' => '日')); ?>
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
							<dt>最寄駅</dt>
							<dd>
								<?php echo $this->Form->input('station', array('class' => 'tel', 'placeholder' => '渋谷駅')); ?>
							</dd>
						</dl>
						<dl class="skillsheet">
							<dt>スキルシート<br>
								(職務経歴書)
							</dt>
							<dd>
								<?php echo $this->Form->input('File', array('type' => 'file')); ?>
								<span class="skillsheet">スキルシートをお持ちでない方は<a href="/download/スキルシート.xlsx" download="スキルシート.xlsx">コチラ</a>をご利用下さい。</span>
							</dd>
						</dl>
						<dl>
							<dt>
								得意分野/スキル<br>
								ポートフォリオ(URL)等
							</dt>
							<dd>
								<?php echo $this->Form->input('have_skill', array('type' => 'textarea', 'class' => 'mySkill', 'placeholder' => '例) LAMP経験3年/Cake/CodeIgniter。詳細設計〜可能。ECサイトが得意。リーダー経験あり等', 'rows' => 5)); ?>							
							</dd>
						</dl>
						<dl>
							<dt>ご希望の案件/条件など</dt>
							<dd>
								<?php echo $this->Form->input('hope', array('type' => 'textarea', 'class' => 'necessary', 'placeholder' => '例) 希望する勤務地 / 単価 / 稼働開始日 / 案件内容 / お顔合わせ希望日など', 'rows' => 5)); ?>
							</dd>
						</dl>
						<dl>
							<dt>その他</dt>
							<dd>
								<?php echo $this->Form->input('other', array('type' => 'textarea', 'class' => 'necessary','rows' => 3)); ?>
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