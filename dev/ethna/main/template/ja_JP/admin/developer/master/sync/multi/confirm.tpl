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
			<h2>マスターデータ&nbsp;	{$app.sync_label[$form.mode].mode}&nbsp;確認</h2>
			
			<form action="exec" method="post">
				<input type="hidden" name="mode" value="{$form.mode}">
				
				{if $form.mode != "unitsync"}
				{strip}
				<p>
					<input type="hidden" name="algorithms" value="{$form.algorithms}">
					{$app.form_template.algorithms.name}：&nbsp;{$app.form_template.algorithms.option[$form.algorithms]}<br>
					{if $form.algorithms == "Stream"}
						<i class="icon-warning-sign"></i>&nbsp;
						<span class="text-warning">
							詳細モードは同期先を一旦削除してから再登録する為、実行する場合は必ずメンテナンス中に行なって下さい。
						</span>
					{/if}
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
				{if $form.mode != "unitsync" || $form.all_sync != 1}
					{foreach from=$app.list key="table" item="row"}
						<tr>
							<td><input type="hidden" name="tables[]" value="{$table}"><i class="icon-ok"></i></td>
							<td>{$row.table_label}</td>
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
				{else}
					<tr>
						<td colspan=5>
							全件同期　※対象ユニットのマスタテーブルを一時的に空にしますのでメンテ中以外は実行しないで下さい。
							<input type="hidden" name="all_sync" value="{$form.all_sync}">
						</td>
					</tr>
				{/if}
					</tbody>
				</table>
					
				<div class="text-center">
				   <input type="submit" value="{$app.sync_label[$form.mode].mode}" class="btn" />
			   </div>
			</form>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>