
<div class="project_add">
<?php
	echo $this->Form->create('Partner', array('action'=>'edit'));
	$this->Form->inputDefaults(array(
        'div' => false,
        'label' => false,
        'id' => false
    ));
?>
	<div class="form_area">
		<dl>
			<dt>会社名</dt>
			<dd><?php echo $this->Form->input('company', array()); ?></dd>
		</dl>
		<dl>
			<dt>会社名（カナ）</dt>
			<dd><?php echo $this->Form->input('company_kana', array()); ?></dd>
		</dl>
		<dl>
			<dt>担当者名</dt>
			<dd><?php echo $this->Form->input('sei', array()); ?><?php echo $this->Form->input('mei', array()); ?></dd>
		</dl>
		<dl>
			<dt>一斉配信用アドレス</dt>
			<dd><?php echo $this->Form->input('ses_email', array()); ?></dd>
		</dl>
		<dl>
			<dt>担当者アドレス</dt>
			<dd><?php echo $this->Form->input('email', array()); ?></dd>
		</dl>
		<dl>
			<dt>電話番号</dt>
			<dd><?php echo $this->Form->input('tel', array()); ?></dd>
		</dl>
		<dl>
			<dt>営業用メモ</dt>
			<dd><?php echo $this->Form->input('comment', array()); ?></dd>
		</dl>
		<dl>
			<dt>備考</dt>
			<dd><?php echo $this->Form->input('other', array()); ?></dd>
		</dl>
	</div>
	<?php echo $this->Form->submit('保存する', array('div' => 'submitForm', 'class' => 'submit')); ?>
	<?php echo $this->Form->end(); ?>
</div>