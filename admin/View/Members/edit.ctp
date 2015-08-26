<div class="project_add">
<?php
	echo $this->Form->create('Member', array('action'=>'edit','enctype' => 'multipart/form-data'));
	$this->Form->inputDefaults(array(
        'div' => false,
        'label' => false,
        'id' => false,
        'legend' => false
    ));
?>
	<div class="form_area">
		<dl>
			<dt>氏名</dt>
			<dd>
				<?php echo $this->Form->input('sei', array()); ?>
				<?php echo $this->Form->input('mei', array()); ?>
			</dd>
		</dl>
		<dl>
			<dt>氏名(カナ)</dt>
			<dd>
				<?php echo $this->Form->input('sei_kana', array()); ?>
				<?php echo $this->Form->input('mei_kana', array()); ?>
			</dd>
		</dl>
		<dl>
			<dt>性別</dt>
			<dd>
				<?php echo $this->Form->input('sex', array('type'=>'radio', 'options' => array(1 => '男性','女性'), 'default' => 1, 'label' => true)); ?>
			</dd>
		</dl>
		<dl>
			<dt>生年月日</dt>
			<dd>
			<?php echo $this->Form->input('birth', array('dateFormat' => 'YMD')); ?>
			</dd>
		</dl>
		<dl>
			<dt>メールアドレス</dt>
			<dd>
				<?php echo $this->Form->input('email', array()); ?>
			</dd>
		</dl>
		<dl>
			<dt>電話番号</dt>
			<dd>
				<?php echo $this->Form->input('tel', array()); ?>
			</dd>
		</dl>
		<dl>
			<dt>最寄駅</dt>
			<dd>
				<?php echo $this->Form->input('station', array()); ?>
			</dd>
		</dl>
		<dl class="skillsheet">
			<dt>スキルシート<br>
				(職務経歴書)
			</dt>
			<dd>
				<?php echo $this->Form->input('File', array('type' => 'file')); ?>
				<?php
					if(isset($empty)) {

						echo h($empty);

					} else {

						echo $this->Html->link($data['Member']['sei'].$data['Member']['mei'].'のスキルシートをダウンロード'  , '/members/download/' .$data['Member']['id'], array('class' => 'skillsheet'));
					}
				?>
			</dd>
		</dl>
		<dl>
			<dt>
				得意分野/スキル<br>
				ポートフォリオ(URL)等
			</dt>
			<dd>
				<?php echo $this->Form->input('have_skill', array('type' => 'textarea', 'class' => 'mySkill', 'rows' => 5)); ?>			
			</dd>
		</dl>
		<dl>
			<dt>ご希望の案件/条件など</dt>
			<dd>
				<?php echo $this->Form->input('hope', array('type' => 'textarea', 'class' => 'necessary', 'rows' => 5)); ?>
			</dd>
		</dl>
		<dl>
			<dt>その他</dt>
			<dd>
				<?php echo $this->Form->input('other', array('type' => 'textarea', 'class' => 'necessary','rows' => 3)); ?>
			</dd>
		</dl>
	</div>
	<?php echo $this->Form->submit('保存する', array('div' => 'submitForm', 'class' => 'submit')); ?>
	<?php echo $this->Form->end(); ?>
</div>