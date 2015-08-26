<main class="main">
	<div class="container">
	<?php
		foreach($project as $key) :
			$value = $key['Project'];
	?>
		<ul class="breadcrumb">
			<li><a href="/" alt="トップ">トップ</a></li>
			<?php if(!empty($value['position_id'])) : ?>
			<li><a href="/projects?Position=<?php echo h($value['position_id']); ?>" alt="<?php echo h($key['Position']['name']); ?>"><?php echo h($key['Position']['name']); ?>の案件/求人</a></li>
			<?php endif; ?>
			<?php //for( $i=0; $i < count($key['Skill']) && $i < 3;$i++) : ?>
			<li><a href="/projects?Skill=<?php echo h($key['PrimarySkill']['id']); //h($key['Skill'][$i]['id']); ?>" alt="<?php echo h($key['PrimarySkill']['name']); //h($key['Skill'][$i]['name']); ?>"><?php echo h($key['PrimarySkill']['name']); //h($key['Skill'][$i]['name']); ?>の案件/求人</a></li>
			<?php //endfor; ?>
			<li><?php echo h($value['title']); ?></li>
		<!-- breadcrumb --></ul>
		<div class="main_content">
			<section class="project_detail">
				<h2 class="title"><?php echo h($value['title']); ?></h2>
				<div class="project_inner">
					<dl class="left">
						<dt>勤務地</dt>
						<dd><?php echo h($value['station']); ?></dd>
					</dl>
					<dl>
						<dt>面談回数</dt>
						<dd><?php echo h($value['meeting']); ?></dd>
					</dl>
					<dl class="left">
						<dt>金額</dt>
						<dd><?php echo "¥".number_format(h($key['MinPrice']['name']))." 〜 ¥".number_format(h($key['MaxPrice']['name'])); ?></dd>
					</dl>
					<dl>
						<dt>勤務時間</dt>
						<dd><?php echo h($value['duty_hours']); ?></dd>
					</dl>
					<dl class="left">
						<dt>清算</dt>
						<dd>有り</dd>
					</dl>
					<dl>
						<dt>服装</dt>
						<dd><?php echo h($value['clothes']); ?></dd>
					</dl>
				</div>
				<div class="project_inner big">
					<dl>
						<dt>ポジション</dt>
						<dd>
							<a class="tag" href="/projects?Position=<?php echo h($value['position_id']); ?>" alt="<?php echo h($key['Position']['name']); ?>"><?php echo h($key['Position']['name']); ?></a>
						</dd>
					</dl>
					<dl>
						<dt>開発環境</dt>
						<dd>
							<?php for( $i=0; $i < count($key['Skill']);$i++) : ?>
							<a href="/projects?Skill=<?php echo h($key['Skill'][$i]['id']); ?>" alt="<?php echo h($key['Skill'][$i]['name']); ?>" class="tag"><?php echo h($key['Skill'][$i]['name']); ?></a>
							<?php endfor; ?>
						</dd>
					</dl>
					<dl class="wide">
						<dt>必須スキル</dt>
						<dd><?php echo nl2br(h($value['must_skill'])); ?></dd>
					</dl>
					<?php if (!empty($value['more_skill'])) : ?>
					<dl class="wide">
						<dt>尚可スキル</dt>
						<dd><?php echo nl2br(h($value['more_skill'])); ?></dd>
					</dl>
					<?php endif; ?>
					<?php if (!empty($value['content'])) : ?>
					<dl class="wide">
						<dt>業務内容</dt>
						<dd><?php echo nl2br(h($value['content'])); ?></dd>
					</dl>
					<?php endif; ?>
					<?php if (!empty($value['work_envi'])) : ?>
					<dl class="wide">
						<dt>職場環境</dt>
						<dd><?php echo nl2br(h($value['work_envi'])); ?></dd>
					</dl>
					<?php endif; ?>
					<dl class="wide">
						<dt>備考</dt>
						<dd><?php echo nl2br(h($value['other'])); ?></dd>
					</dl>
				</div>
				<div class="keep">
					<?php if( !empty($keep_id) && in_array($value['id'], $keep_id) ) : ?>
						<a href="javascript:void(0)" class="keep_delete" value="<?php echo h($value['id']); ?>">★ 気になる！</a>
					<?php else : ?>
						<a href="javascript:void(0)" class="keep_data" value="<?php echo h($value['id']); ?>">★ 気になる！</a>
					<?php endif; ?>
				</div>
				<div class="entry">
					<?php
						echo $this->Form->create('Member', array('type' => 'post', 'controller' => 'Member', 'action' => 'index'));
						echo $this->Form->submit('いますぐエントリー！', array('div' => false, 'name' => 'keep', 'class' => 'submit'));
						echo $this->Form->hidden('entry_id][', array('value' => h($value['id']), 'id' => false ));
						echo $this->Form->end();
					?>
				</div>
			</section>
			<section class="same_project">
				<h2><?php echo h($value['title']); ?>に似た案件</h2>
				<p class="text"><?php echo h($value['title']); ?>を見た人はこんな案件も見ています</p>
				<div class="same_project_list">
	<?php endforeach; ?>
				<?php
					foreach($sub_project as $key) :
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
							<h3><?php echo h($value['title']); ?></h3>
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
									<th>開発環境</th>
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
			<!-- same_project --></section>
		<!-- main_content --></div>
	<!-- container --></div>
	<?php echo $this->element('sp_bottom_menu'); ?>
</main>