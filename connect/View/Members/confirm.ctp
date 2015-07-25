<?php
	$this->set('title', 'Connect(コネクト) IT/webフリーランスの案件/求人情報');
	$this->set('keywords', 'フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
	$this->set('description', 'Connect(コネクト)はITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイト。キャリア相談〜案件紹介、アフターフォローまでIT/webフリーランスをトータルサポート！');
	$this->set('css', 'member');
	$this->set('js', 'member');
?>
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
							<dd><?php echo $member['sei'].' '.$member['mei']; ?></dd>
						</dl>
						<dl class="must">
							<dt>氏名(カナ)</dt>
							<dd><?php echo $member['sei_kana'].' '.$member['mei_kana']; ?></dd>
						</dl>
						<dl class="must">
							<dt>性別</dt>
							<dd><?php echo $member['sex'] == 1 ? '男性' : '女性' ; ?></dd>
						</dl>
						<dl class="must">
							<dt>生年月日</dt>
							<dd><?php echo $member['year'].'年 '.$member['month'].'月 '.$member['day'].'日'; ?></dd>
						</dl>
						<dl class="must">
							<dt>メールアドレス</dt>
							<dd><?php echo $member['email']; ?></dd>
						</dl>
						<dl class="must">
							<dt>電話番号</dt>
							<dd><?php echo $member['tel']; ?></dd>
						</dl>
						<dl class="must">
							<dt>最寄駅</dt>
							<dd><?php echo $member['station']; ?></dd>
						</dl>
						<dl class="skillsheet">
							<dt>スキルシート<br>
								(職務経歴書)
							</dt>
							<dd><?php echo $member['File']['name']; ?></dd>
						</dl>
						<dl>
							<dt>
								得意分野/スキル<br>
								ポートフォリオ(URL)等
							</dt>
							<dd><?php echo $member['have_skill']; ?></dd>
						</dl>
						<dl>
							<dt>ご希望の案件/条件など</dt>
							<dd><?php echo $member['hope']; ?></dd>
						</dl>
						<dl>
							<dt>その他</dt>
							<dd><?php echo $member['other']; ?></dd>
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