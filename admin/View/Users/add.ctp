
<div class="users_form">
<?php echo $this->Form->create('User'); ?>
	<span><?php echo __('ユーザー新規登録'); ?></span>
	<table>
		<tr>
			<th>ユーザー名</th>
			<td>
	        <?php echo $this->Form->input('username', array('label'=>'')); ?>
	        </td>
	    </tr>
	    <tr>
	    	<th>パスワード</th>
			<td>
				<?php echo $this->Form->input('password', array('label'=>'')); ?>
			</td>
		</tr>
		<tr>
			<th>権限</th>
       	<td>
        		<?php echo $this->Form->input('role', array('label'=>'', 'options' => array('admin' => '管理者', 'author' => 'ユーザー'))); ?>
        	</td>
        </tr>

<!-- 		<tr>
			<th></th>
       	<td>
        		<?php echo $this->Form->input('group_id', array('label'=>'', 'options' => array(1 => 1, 2=>2, 3=>3))); ?>
        	</td>
        </tr>
-->
       </table>
<?php echo $this->Form->end(__('登録する')); ?>
</div>