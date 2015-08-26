
<main class="main">
	<div class="container">
		<ul class="breadcrumb">
			<li><a href="/" alt="トップ">トップ</a></li>
			<li><?php echo $this->Paginator->counter('{:count}'); ?>件の<?php echo h($key); ?>案件/求人情報</li>
		<!-- breadcrumb --></ul>
		<div class="main_content">
			<div class="search_header">
				<p class="search_result">
					<?php echo $this->Paginator->counter('該当案件：<span>{:count}</span> 件 <span>{:start}〜{:end} 件目を表示中</span>'); ?>
				<p>
				<p class="search_panel_open">検索条件を変更する</p>
			</div>
			<div class="search">
				<section class="freeword_search">
					<h2>フリーワードで1発検索！</h2>
					<?php
						echo $this->Form->create('Project', array('type' => 'get', 'controller' => 'projects', 'action' => 'index', 'class' => "freeword_search_form"));
						$this->Form->inputDefaults(array('div' => false,'label' => false,'id' => false));
						echo $this->Form->input('freeword', array('type' => 'search', 'class' => 'freeword'));
						echo $this->Form->submit('検索', array('div' => false, 'class' => 'submit'));
						echo $this->Form->end();
					?>
				</section>
				<section class="search_panel">
					<h2>条件を絞り込んで検索する</h2>
					<ul class="search_panel_tab_list">
						<li class="search_panel_tab on">ポジション</li>
						<li class="search_panel_tab">スキル</li>
						<li class="search_panel_tab">金額</li>
					</ul>
					<div class="search_panel_box">
						<?php
							echo $this->Form->create('Project', array('type' => 'get', 'url' => array('controller' => 'Projects', 'action' => 'index')));
							$this->Form->inputDefaults(array('div' => false,'label' => false,'id' => false, 'legend' => false, 'hiddenField' => false));
						?>
						<div class="search_panel_select">							
							<?php echo $this->Form->input('Position', array('type' => 'select', 'multiple' => 'checkbox', 'id' => 'position_')); ?>
						</div>
						<div class="search_panel_select">
							<?php echo $this->Form->input('Skill', array('type' => 'select', 'multiple' => 'checkbox', 'id' => 'skill_')); ?>
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
							<?php
								echo $this->Form->submit('検索', array('div' => false, 'class' => 'submit'));
								echo $this->Form->end();
							?>
						</div>
					<!-- search_panel_box --></div>
				<!-- search_panel --></section>
			<!-- search --></div>
			<div class="project_list">
				<?php if( empty($project) ) : ?>
					案件が見つかりませんでした。
				<?php
					else :
					foreach($project as $key) :
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
					<a class="project_inner" href="/projects/<?php echo h($value['id']); ?>" alt="<?php echo h($value['title']); ?>">
						<h3 class="title"><?php echo h($value['title']); ?></h3>
						<p class="body"><?php echo h($value['content']); ?></p>
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
						<div class="project_overlay"></div>
					</a>
				</section>
				<?php endforeach; endif; ?>
			<!-- project_list --></div>
			<div class="pagenate">
			<?php
			    if($this->Paginator->hasPrev()) print $this->Paginator->prev('<' , array());
			    print $this->Paginator->numbers(array(
			    'modulus' => 2,
			    'first'=>1,
			    'last'=>1,
			    'currentClass'=>'now',
			    'separator'=>null,
			    'ellipsis' => '...'
			    ));
			    if($this->Paginator->hasNext()) print $this->Paginator->next('>' , array());
			  ?>
			</div>
		<!-- main_content --></div>
		<?php echo $this->element('sidebar'); ?>
	<!-- container --></div>
	<?php echo $this->element('sp_bottom_menu'); ?>
</main>