<?php
	$this->set('title', 'Connect(コネクト) IT/webフリーランスの案件/求人情報');
	$this->set('keywords', 'フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
	$this->set('description', 'Connect(コネクト)はITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイト。キャリア相談〜案件紹介、アフターフォローまでIT/webフリーランスをトータルサポート！');
	$this->set('css', 'home');
	$this->set('js', 'home');
?>

<main class="main">
	<div class="topimage">
		<div class="topimage_inner">
			<p class="top_message">Please Search Freeword or Scroll Down.<span class="flash">.</span></p>
			<?php
				echo $this->Form->create('Project', array('type' => 'get', 'controller' => 'projects', 'action' => 'index', 'class' => "freeword_search_form"));
				$this->Form->inputDefaults(array('div' => false,'label' => false,'id' => false, 'legend' => false, 'hiddenField' => false));
				echo $this->Form->input('freeword', array('type' => 'search', 'class' => 'freeword'));
				echo $this->Form->submit('検索', array('div' => false, 'class' => 'submit'));
				echo $this->Form->end();
			?>
			</form>
		</div>
	</div>
	<div class="explain">
		<section class="message">
			<h2>ConnectはIT業界の個人事業主様をトータルにサポート！</h2>
			<p>IT/WEB業界のフリーランスに特化した案件/求人情報を公開しております。<br>
			キャリア相談〜案件紹介、アフターフォローまであなたをトータルにサポート！<br>
			WEB系/業務系エンジニア、インフラエンジニアに限らず、デザイナー/ディレクターなどのクリエイティブ案件、プロジェクトマネジメント/ヘルプデスクなどの管理系/事務系案件など幅広く取り扱っております。<br>専属コンサルタントがあなたにピッタリの案件をご紹介致します！
			</p>
		</section>
		<div class="flow">
			<img src="img/flow.png" alt="支援サービスの流れ「step1ご登録、step2カウンセリング、step3案件ご紹介、step4面談/参画決定、step5サポート」" width="100%">
		</div>
	<!-- explain --></div>
	<div class="container">
		<div class="main_content">
			<section class="search">
				<h2>
					＊ 案件検索<br>
					<span>条件を絞り込んで、あなたに合った案件を見つけよう！</span>
				</h2>
				<div class="search_panel">
					<ul class="search_panel_tab_list">
						<li class="search_panel_tab on">スキル</li>
						<li class="search_panel_tab">ポジション</li>
						<li class="search_panel_tab">金額</li>
					</ul>
					<div class="search_panel_box">
						<?php
							echo $this->Form->create('Project', array('type' => 'get', 'url' => array('controller' => 'Projects', 'action' => 'index')));
							$this->Form->inputDefaults(array('div' => false,'label' => false,'id' => false, 'legend' => false, 'hiddenField' => false));
						?>
						<div class="search_panel_select">
							<?php echo $this->Form->input('Skill', array('type' => 'select', 'multiple' => 'checkbox', 'id' => 'skill_')); ?>
						</div>
						<div class="search_panel_select">							
							<?php echo $this->Form->input('Position', array('type' => 'select', 'multiple' => 'checkbox', 'id' => 'position_')); ?>
						</div>
						<div class="search_panel_select">
							<div>
								<?php echo $this->Form->radio('price', $price, array('div' => false, 'legend' => false, 'separator' => '</div><div>', 'hiddenField' => false, 'id' => 'price_')); ?>
							</div>
						</div>
						<div class="search_panel_selected">
							<dl class="selected_item_box">
								<dt>選択した項目</dt>
								<dd class="selected_item">
									<p class="selected_item_default">まだ選択されていません</p>
								</dd>
								<p class="selected_item_all_delete">
									<a href="javascript:void(0)">条件をクリア</a>
								</p>
							</dl>
						<!-- search_panel_selected --></div>
						<div class="submit_area">
						<?php echo $this->Form->submit('検索', array('div' => false, 'class' => 'submit')); ?>
						<?php echo $this->Form->end(); ?>
						</div>
					<!-- search_panel_box --></div>
				<!-- search_panel --></div>
			<!-- search --></section>
			<section class="pickup">
				<h2>
					＊ ピックアップ案件<br>
					<span>新着/急募など今イチオシのピックアップ案件！</span>
				</h2>
				<div class="pickup_project">
					<?php
						foreach($pickup_project as $key) :
							$value = $key['Project'];
					?>
					<section>
						<a href="/projects/<?php echo h($value['id']); ?>" alt="<?php echo h($value['title']); ?>">
							<h3 class="title"><?php echo h($value['title']); ?></h3>
							<table>
								<tr>
									<th>金額</th>
									<td>¥<?php echo h($value['min_price']); ?>〜¥<?php echo h($value['max_price']); ?></td>
								</tr>
								<tr>
									<th>最寄駅</th>
									<td><?php echo h($value['station']); ?></td>
								</tr>
								<tr>
									<th>ポジション</th>
									<td>
										<?php echo h($key['Position']['name']); ?>
									</td>
								</tr>
								<tr>
									<th>スキル</th>
									<td>
									<?php
										for( $i=0; $i < count($key['Skill']);$i++) {
											echo h($key['Skill'][$i]['name']).'/';
										}
									?>
									</td>
								</tr>
							</table>
							<p class="more">詳細を見る</p>
						</a>
					</section>
					<?php endforeach; ?>
				</div>
			</section>
		<!-- main_content --></div>
		<?php echo $this->element('sidebar'); ?>
	<!-- container --></div>
</main>