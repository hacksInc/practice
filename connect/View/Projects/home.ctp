

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
		</div>
	</div>
	<div class="explain">
		<section class="message">
			<h2><span>＊</span>Connect（コネクト）とは？</h2>
			<section>
				<h3>高単価なフリーランス向け案件が豊富！</h3>
				<p>本来、フリーランスは単価が高くなければいけません。余計なマージンを撤廃しているため高単価な案件が豊富！</p>
			</section>
			<section>
				<h3>IT/web業界のフリーランスをトータルにサポート！</h3>
				<p>IT/web業界のフリーランスに特化した案件/仕事情報を公開しており、キャリア相談〜案件紹介、アフターフォローまでフリーランスをトータルにサポート！</p>
			</section>
			<section>
				<h3>現役コンサルタントがフリーランスの価値を教えます！</h3>
				<p>数百人ものフリーランスと会ってきた現役の人材コンサルタントがあなたの価値を市場動向からズバリ教えます！</p>
			</section>
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
				<?php echo $this->element('search'); ?>
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
						<div class="keep">
							<?php if( !empty($keep_id) && in_array($value['id'], $keep_id) ) : ?>
								<a href="javascript:void(0)" class="keep_delete" value="<?php echo h($value['id']); ?>">★ 気になる！</a>
							<?php else : ?>
								<a href="javascript:void(0)" class="keep_data" value="<?php echo h($value['id']); ?>">★ 気になる！</a>
							<?php endif; ?>
						</div>
						<a href="/projects/<?php echo h($value['id']); ?>" alt="<?php echo h($value['title']); ?>">
							<h3 class="title"><?php echo h($value['title']); ?></h3>
							<table>
								<tr>
									<th>金額</th>
									<td><?php echo "¥".number_format(h($key['MinPrice']['name']))." 〜 ¥".number_format(h($key['MaxPrice']['name'])); ?></td>
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
											if( ($i+1) == count($key['Skill'])) {
												echo h($key['Skill'][$i]['name']);
											} else {
												echo h($key['Skill'][$i]['name']).'/';
											}
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
		<div class="sub_content">
			<section class="sub_keyword">
				<h3>＊ 人気のキーワード</h3>
				<ul>
					<li><a href="/projects?Skill=1" alt="PHP">PHP</a></li>
					<li><a href="/projects?Skill=2" alt="Ruby">Ruby</a></li>
					<li><a href="/projects?Skill=3" alt="java">Java</a></li>
					<li><a href="/projects?Skill=6" alt="HTML5/CSS3">HTML5/CSS3</a></li>
					<li><a href="/projects?Skill=11" alt="Unity">Unity</a></li>
					<li><a href="/projects?Skill=7" alt="JavaScript">JavaScript</a></li>
					<li><a href="/projects?Skill=12" alt="Swift">Swift</a></li>
					<li><a href="/projects?Skill=11" alt="Android">Android</a></li>
					<li><a href="/projects?Skill=10" alt="Objective-C">Objective-C</a></li>
					<li><a href="/projects?Position=6" alt="webデザイナー">webデザイナー</a></li>
				</ul>
			</section>
			<section class="sub_project">
				<h3>＊ 人気の案件</h3>
				<ul>
					<?php
						foreach($sub_project as $key) :
							$value = $key['Project'];
					?>
					<li>
					<a href="/projects/<?php echo h($value['id']); ?>" alt="<?php echo h($value['title']); ?>">
						<h4><?php echo h($value['title']); ?></h4>
						<p><?php echo h($value['station'])." / ¥".number_format(h($key['MinPrice']['name']))." 〜 ¥".number_format(h($key['MaxPrice']['name'])); ?></p>
						<p>
						<?php
							for( $i=0; $i < count($key['Skill']);$i++) {
								if( ($i+1) == count($key['Skill'])) {
									echo h($key['Skill'][$i]['name']);
								} else {
									echo h($key['Skill'][$i]['name']).'/';
								}
							}
						?>
						</p>
					</a>
					</li>
					<?php endforeach; ?>
				</ul>
			</section>
			<div class="sub_partner">
				<a href="/contacts/company"><img src="img/partner.png" width="100%"></a>
			</div>
			<div class="sub_sns">
				<div class="fb-page" data-href="https://www.facebook.com/pages/Connect/1588548634743761" data-width="500" data-height="300" data-small-header="false" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"><div class="fb-xfbml-parse-ignore"><blockquote cite="https://www.facebook.com/pages/Connect/1588548634743761"><a href="https://www.facebook.com/pages/Connect/1588548634743761">Connect</a></blockquote></div></div>
			</div>
		<!-- sub_content --></div>
	<!-- container --></div>
</main>