
<div class="project_add">
<?php
	echo $this->Form->create('Project', array('action'=>'add'));
	$this->Form->inputDefaults(array(
        'div' => false,
        'label' => false,
        'id' => false
    ));
?>
	<div class="form_area">
	<table>
		<tr>
			<th>案件名</th>
			<td><?php echo $this->Form->input('title', array('class' => 'title','placeholder' => '例）ソーシャルゲームの開発')); ?></td>
		</tr>
		<tr>
			<th>契約形態</th>
			<td>
			<?php echo $this->Form->input('Contract', array('type' => 'select', 'multiple' => 'checkbox', 'class'=> 'check' )); ?>
			</td>
		</tr>
		<tr>
			<th>都道府県</th>
			<td>
				<?php echo $this->Form->select('prefecture_id', array($prefecture), array('empty' => false, 'id' => false,'default'=>'13', 'class' => 'prefecture')); ?>
				<?php echo $this->Form->select('city_id', array($city), array('empty' => '選択して下さい', 'id' => false, 'class' => 'city')); ?>
			</td>
		</tr>
		<tr>
			<th>勤務地</th>
			<td><?php echo $this->Form->input('place', array('class' => 'place', 'placeholder' => '猿楽町2-3-5 ブライズ神保町803号室')); ?></td>
		</tr>
		<tr>
			<th>最寄駅</th>
			<td><?php echo $this->Form->input('station', array('placeholder' => '渋谷駅')); ?></td>
		</tr>
		<tr>
			<th>期間</th>
			<td><?php echo $this->Form->input('term', array('placeholder' => '即日〜長期')); ?></td>
		</tr>
		<tr>
			<th>面段回数</th>
			<td><?php echo $this->Form->input('meeting', array('placeholder' => '1回')); ?></td>
		</tr>
		<tr>
			<th>清算</th>
			<td><?php echo $this->Form->select('liquidation_id', array($liquidation), array('empty' => '選択してください')); ?></td>
		</tr>
		<tr>
			<th>単価</th>
			<td>￥<?php echo $this->Form->select('min_price_id', array($minPrice), array('empty' => '選択してください')); ?> 〜 ￥<?php echo $this->Form->select('max_price_id', array($maxPrice), array('empty' => '選択してください')); ?></td>
		</tr>
		<tr>
			<th>服装</th>
			<td><?php echo $this->Form->input('clothes', array('placeholder' => '自由')); ?></td>
		</tr>
		<tr>
			<th>勤務時間</th>
			<td><?php echo $this->Form->input('duty_hours', array( 'class' => 'duty_hours', 'placeholder' => '10時〜20時（フレックス制）')); ?></td>
		</tr>
		<tr>
			<th>業種</th>
			<td><?php echo $this->Form->select('service_id', array($service), array('empty' => '選択してください', 'id' => false)); ?></td>
		</tr>
		<tr>
			<th>ポジション</th>
			<td><?php echo $this->Form->select('position_id', array($position), array('empty' => '選択してください', 'id' => false)); ?></td>
		</tr>
		<tr>
			<th>主要スキル</th>
			<td><?php echo $this->Form->select('primary_skill_id', array($primarySkill), array('empty' => '選択してください', 'id' => false)); ?></td>
		</tr>
	</table>
	<table>
		<tr>
			<th>必須スキル</th>
			<td><?php echo $this->Form->input('must_skill', array('placeholder' => '・LAMP開発経験3年以上')); ?></td>
		</tr>
		<tr>
			<th>尚可スキル</th>
			<td><?php echo $this->Form->input('more_skill'); ?></td>
		</tr>
		<tr>
			<th>職場環境</th>
			<td><?php echo $this->Form->input('work_envi', array('placeholder' => '若い人が中心の職場', 'rows' => '5')); ?></td>
		</tr>
		<tr>
			<th>業務内容</th>
			<td><?php echo $this->Form->input('content', array('placeholde' => 'ソーシャルゲームの開発をしていただきます。')); ?></td>
		</tr>
		<tr>
			<th>備考</th>
			<td><?php echo $this->Form->input('other', array()); ?></td>
		</tr>
	</table>
	</div>
	<div class="form_area2">
	<p>開発環境</p>
	<table>
		<tr>
			<th>スキル</th>
			<td>
			<?php echo $this->Form->input('Skill', array('type' => 'select', 'multiple' => 'checkbox', 'class'=> 'check')); ?>
			</td>
		</tr>
	</table>
	</div>
	<?php echo $this->Form->submit('保存する', array('div' => 'submitForm', 'class' => 'submit')); ?>
	<?php echo $this->Form->end(); ?>
</div>