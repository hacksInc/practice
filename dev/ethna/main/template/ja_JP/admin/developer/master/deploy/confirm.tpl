<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="マスターデータデプロイ確認 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<form action="exec" method="post" class="form-horizontal">
	<input type="hidden" name="mode" value="{$form.mode}" />
	<div class="row-fluid">

		<div class="span9">
			<h2>{if $form.mode == "standby"}商用同期{elseif $form.mode == "deploy"}デプロイ{/if} マスターデータ確認</h2>

			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th>テーブル名称</th>
						<th>DB名称</th>
						<th>データ更新日時</th>
						<th>前回同期日時</th>
						<th>概要</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$app.list key="table" item="row"}
					<tr>
						<td>{$row.table_label}{form_input name=$table id=$table}</td>
						<td>{$table}</td>
						<td>{$row.last_modified.date_modified}</td>
						<td>{$app.sync_last[$table].date_modified}</td>
						<td>{$row.summary}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div><!--/span-->
	</div><!--/row-->
	<br />
	{if 0 < count($app.list) }
	<div style="width:580px;">
		<div class="search-part-button">
			<input type="submit" name="btn" value="デプロイ" class="btn btn-primary" />
		</div>
	</div>
	{/if}

	</form>


	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
