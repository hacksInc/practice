
<div class="users_form">
<?php echo $this->Form->create('Group'); ?>
	<span><?php echo __('グループ新規登録'); ?></span>
	<table>
		<tr>
			<th>グループ名</th>
			<td>
	        <?php echo $this->Form->input('name', array('label'=>'')); ?>
	        </td>
	    </tr>
       </table>
<?php echo $this->Form->end(__('登録する')); ?>
</div>