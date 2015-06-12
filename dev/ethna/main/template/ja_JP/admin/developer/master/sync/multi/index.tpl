<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.sync_label[$form.mode].mode` - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>マスターデータ&nbsp;	{$app.sync_label[$form.mode].mode}</h2>

			<form action="confirm" method="post">
				<input type="hidden" name="mode" value="{$form.mode}">
				
				{if $form.mode != "unitsync"}
				{strip}
				<p>
					{$app.form_template.algorithms.name}&nbsp;&nbsp;
					<label style="display:inline-block;">
						<input type="radio" name="algorithms" value="default" checked>{$app.form_template.algorithms.option.default}
						<i class="icon-question-sign" data-original-title="通常はこちらを選んで下さい。"></i>
					</label>&nbsp;&nbsp;
					<label style="display:inline-block;">
						<small>
							<input type="radio" name="algorithms" value="Stream">{$app.form_template.algorithms.option.Stream}
						</small>
						<i class="icon-question-sign" data-original-title="標準モードで同期に失敗した場合、こちらを選んで下さい。ただし、こちらのモードは、同期先を一旦削除してから再登録する為、実行する場合は必ずメンテナンス中に行なって下さい。"></i>
					</label>
				</p>
				{/strip}
				{/if}
				
				<table class="table table-bordered table-condensed">
					<thead>
						<tr>
							<th width="1">CHK</th>
							<th>テーブル名称</th>
							{if $form.mode == "standby" || $form.mode == "unitsync"}
								<th>データ更新日時</th>
							{/if}
							{if $form.mode != "unitsync"}
								<th>{$app.sync_label.standby.last_synced}</th>
							{/if}
							{if $form.mode == "deploy"}
								<th>{$app.sync_label.deploy.last_synced}</th>
							{/if}
							<th>概要</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="5">
								{if $form.mode == "unitsync"}
									<label><input type="checkbox" name="all_sync" value="1" id="all_sync"> 全件同期　※対象ユニットのマスタテーブルを一時的に空にしますのでメンテ中以外は実行しないで下さい。以下のチェックは無効になります。</label>
								{else}
									<label><input type="checkbox" id="check-all"> 全件チェック</label>
								{/if}
							</td>
						</tr>
					{foreach from=$app.list key="table" item="row"}
						<tr>
							<td><input type="checkbox" name="tables[]" value="{$table}" id="input-{$table}" class="check-table"></td>
							<td><label for="input-{$table}">{$row.table_label}</label></td>
							{if $form.mode == "standby" || $form.mode == "unitsync"}
								<td>{$row.last_modified.date_modified}</td>
							{/if}
							{if $form.mode != "unitsync"}
								<td>{$row.last_synced.standby}</td>
							{/if}
							{if $form.mode == "deploy"}
								<td>{$row.last_synced.deploy}</td>
							{/if}
							<td>{$row.summary}</td>
						</tr>
					{/foreach}
					</tbody>
				</table>
					
				<div class="text-center">
				   <input type="submit" value="{$app.sync_label[$form.mode].mode}確認" class="btn" />
			   </div>
			</form>
			   
			<div class="text-right">{a href="log/view?mode=`$form.mode`"}＞ 操作ログ閲覧{/a}</div>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{literal}
<script>
	$('#check-all').on('change', function() {
		$('input.check-table:checkbox').prop('checked', this.checked);
	});
</script>
{/literal}
</body>
</html>