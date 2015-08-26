<table class="list">
			<tbody>
				<?php foreach($users as $user) : ?>
				<tr>
					<td>
						<?php echo h($user['User']['id']); ?>
					</td>
					<td>
						<?php echo h($user['User']['username']); ?>
					</td>
					<td>
						<?php echo h($user['User']['role']); ?>
					</td>
					<td>
						<?php echo $this->Form->postLink('削除', array('action'=>'delete', $user['User']['id']), array('confirm'=>'本当に削除してよろしいですか？')); ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>