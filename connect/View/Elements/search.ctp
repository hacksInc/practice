<div class="search_panel">
	<ul class="search_panel_tab_list">
		<li class="search_panel_tab on">ポジション</li>
		<li class="search_panel_tab">スキル</li>
		<li class="search_panel_tab">金額</li>
	</ul>
	<div class="search_panel_box">
		<?php
			echo $this->Form->create('Project', array('type' => 'get', 'url' => array('controller' => 'projects', 'action' => 'index')));
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
			<?php echo $this->Form->submit('検索', array('div' => false, 'class' => 'submit')); ?>
			<?php echo $this->Form->end(); ?>
		</div>
	<!-- search_panel_box --></div>
<!-- search_panel --></div>