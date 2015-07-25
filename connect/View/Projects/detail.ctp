<?php
	$this->set('title', 'Connect(コネクト) IT/webフリーランスの案件/求人情報');
	$this->set('keywords', 'フリーランス,エンジニア,デザイナー,web,IT,案件,求人,仕事');
	$this->set('description', 'Connect(コネクト)はITエンジニア/webデザイナなどのフリーランスと企業を繋ぐ、案件/求人情報サイト。キャリア相談〜案件紹介、アフターフォローまでIT/webフリーランスをトータルサポート！');
	$this->set('css', 'project');
	$this->set('js', 'project');
?>
<main class="main">
	<div class="container">
	<?php
		foreach($project as $key) :
			$value = $key['Project'];
	?>
		<ul class="breadcrumb">
			<li><a href="/" alt="トップ">トップ</a></li>
			<li><a href="/projects?Position=<?php echo h($value['position_id']); ?>" alt="<?php echo h($key['Position']['name']); ?>"><?php echo h($key['Position']['name']); ?>の案件/求人</a></li>
			<li><a href="/projects?Skill=<?php echo h($value['primary_skill_id']); ?>" alt="<?php echo h($key['PrimarySkill']['name']); ?>"><?php echo h($key['PrimarySkill']['name']); ?>の案件/求人</a></li>
			<li><?php echo h($value['title']); ?></li>
		<!-- breadcrumb --></ul>
		<div class="main_content">
			<section class="project_detail">
				<h2 class="title"><?php echo h($value['title']); ?></h2>
				<div class="project_inner">
					<table>
						<tr>
							<th>勤務地</th>
							<td><?php echo h($value['station']); ?></td>
							<th>面談回数</th>
							<td><?php echo h($value['meeting']); ?></td>
						</tr>
						<tr>
							<th>金額</th>
							<td>¥<?php echo h($value['min_price']); ?>〜¥<?php echo h($value['max_price']); ?></td>
							<th>勤務時間</th>
							<td><?php echo h($value['duty_hours']); ?></td>
						</tr>
						<tr>
							<th>清算</th>
							<td><?php echo h($key['Liquidation']['name']); ?></td>
							<th>備考</th>
							<td><?php echo h($value['other']); ?></td>
						</tr>
					</table>
				</div>
				<div class="project_inner">
					<table>
						<tr>
							<th>ポジション</th>
							<td>
								<a class="tag" href="/projects?Position=<?php echo h($value['position_id']); ?>" alt="<?php echo h($key['Position']['name']); ?>"><?php echo h($key['Position']['name']); ?></a>
							</td>
						</tr>
						<tr>
							<th>開発環境</th>
							<td>
								<?php for( $i=0; $i < count($key['Skill']);$i++) : ?>
								<a class="tag" href="/projects?Skill=<?php echo h($key['Skill'][$i]['id']); ?>" alt="<?php echo h($key['Skill'][$i]['name']); ?>"><?php echo h($key['Skill'][$i]['name']); ?></a>
								<?php endfor; ?>
							</td>
						</tr>
						<tr class="wide">
							<th>必須スキル</th>
							<td><?php echo nl2br(h($value['must_skill'])); ?></td>
						</tr>
						<?php if (!empty($value['more_skill'])) : ?>
						<tr class="wide">
							<th>尚可スキル</th>
							<td><?php echo nl2br(h($value['more_skill'])); ?></td>
						</tr>
						<?php endif; ?>
						<?php if (!empty($value['content'])) : ?>
						<tr class="wide">
							<th>業務内容</th>
							<td><?php echo nl2br(h($value['content'])); ?></td>
						</tr>
						<?php endif; ?>
						<?php if (!empty($value['work_envi'])) : ?>
						<tr class="wide">
							<th>職場環境</th>
							<td><?php echo nl2br(h($value['work_envi'])); ?></td>
						</tr>
						<?php endif; ?>
					</table>
				</div>
				<div class="keep">
					<?php if( $keep_id !== false ) : ?>
						<a href="javascript:void(0)" class="keep_delete" value="<?php echo h($value['id']); ?>">★ 気になる！</a>
					<?php else : ?>
						<a href="javascript:void(0)" class="keep_data" value="<?php echo h($value['id']); ?>">★ 気になる！</a>
					<?php endif; ?>
				</div>
				<div class="entry">
					<?php
						echo $this->Form->create('Member', array('type' => 'post', 'controller' => 'Member', 'action' => 'index'));
						echo $this->Form->submit('いますぐエントリー！', array('div' => false, 'name' => 'keep', 'class' => 'submit'));
						echo $this->Form->hidden('id][', array('value' => h($value['id']), 'id' => false ));
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
						<a href="/projects/<?php echo h($value['id']); ?>" alt="<?php echo h($value['title']); ?>">
							<h3><?php echo h($value['title']); ?></h3>
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
									<th>開発環境</th>
									<td>
									<?php
										for( $i=0; $i < count($key['Skill']);$i++) {
											echo h($key['Skill'][$i]['name']).' / ';
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