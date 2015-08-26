<div class="content">
	<div class="project">
		<div class="info">
			<p class="count"><?php echo $this->Paginator->counter('検索結果：<span>{:count}件</span>{:start} 〜 {:end} 件目を表示中') ?></p>
			<?php
				echo $this->Form->create('Partner', array('type' => 'post', 'controller' => 'partners', 'action' => 'index', 'class'=>'search_box'));
				$this->Form->inputDefaults(array('div' => false,'label' => false,'id' => false, 'legend' => false, 'hiddenField' => false));
				echo $this->Form->input('search', array('type' => 'search', 'class' => 'search'));
				echo $this->Form->submit('検索', array('div' => false, 'class' => 'submit'));
				echo $this->Form->end();
			?>
			<?php echo $this->Html->link('アドレス一覧を取得'  , '/partners/download/', array('class' => 'email_list', 'target' => 'blank')); ?>
		</div>
		<table>
			<tr>
				<th>ID</th>
				<th>会社名</th>
				<th>更新日</th>
				<th>削除</th>
			</tr>
		<?php foreach($partner as $key) : ?>
		<?php $value = $key['Partner']; ?>
			<tr>
				<td><?php echo h($value['id']); ?></td>
				<td><?php echo $this->Html->link($value['company'], '/partners/edit/' .$value['id']); ?></td>
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