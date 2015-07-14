<?php $this->Html->css('project', array('inline' => false)); ?>
<div class="content">
	<div class="project">
		<p class="count"><?php echo $this->Paginator->counter('検索結果：<span>{:count}件</span>{:start} 〜 {:end} 件目を表示中') ?></p>
		<table>
			<tr>
				<th>ID</th>
				<th>案件名</th>
				<th>単価</th>
				<th>最寄駅</th>
				<th>更新日</th>
				<th>削除</th>
			</tr>
		<?php foreach($projects as $key) : ?>
		<?php $value = $key['Project']; ?>
			<tr>
				<td><?php echo h($value['id']); ?></td>
				<td><?php echo $this->Html->link($value['title'], '/projects/edit/' .$value['id']); ?></td>
				<td><?php echo h($value['min_price']); ?> 〜 <?php echo h($value['max_price']); ?> 万円</td>
				<td><?php echo h($value['station']); ?></td>
				<td><?php echo date('Y/m/d', strtotime($value['modified'])); ?></td>
				<td><?php echo $this->Form->postlink('削除', array('action'=>'delete', $value['id']), array('confirm'=>'本当に削除してよろしいですか？')); ?></td>
			</tr>
		<?php endforeach; ?>
		</table>
	<!-- project --></div>
<!-- content --></div>

<div class="pagenate">
<?php
    if($this->Paginator->hasPrev()) print $this->Paginator->prev('<' , array());
    print $this->Paginator->numbers(array(
    'modulus' => 4,
    'first'=>2,
    'last'=>2,
    'currentClass'=>'now',
    'separator'=>null,
    'ellipsis' => '...'
    ));
    if($this->Paginator->hasNext()) print $this->Paginator->next('>' , array());
  ?>
</div>