<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.table_label` - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">

		<div class="span9">
			<h2>{$app.table_label}</h2>
			<h3>データ取得</h3>
			{a href="download?table=`$form.table`&format=csv"}CSV取得{/a}<br />
			{a href="download?table=`$form.table`&format=json"}JSON取得{/a}

			<h3>データ更新</h3>
			<h4>CSVアップロード</h4>
			※現在のテーブルの内容が破棄され、CSVの内容に差し替えられます。
			<form action="upload/confirm" method="POST" enctype="multipart/form-data">
				<input type="hidden" name="table" value="{$form.table}">
				<input type="file" name="xml" size="30" class="file-drop"><br />
				<input type="submit" value="次へ" class="btn">
			</form>

			<h3>直接データ編集</h3>
			{a href="edit?table=`$form.table`"}編集{/a}<br>
			{a href="editlog/view?table=`$form.table`"}操作ログ閲覧{/a}

			<h3>CSVログデータ</h3>
			{a href="log/list?table=`$form.table`"}ログ選択へ{/a}

			<h3>一覧</h3>
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						{foreach from=$app.label item="label"}
							<th>{$label}</th>
						{/foreach}
					</tr>
				</thead>
				<tbody>
				{strip}
				{foreach from=$app.list item="row"}
					<tr>
						{foreach from=$app.label item="label" key="key"}
							<td>{$row.$key|nl2br}</td>
						{/foreach}
					</tr>
				{/foreach}
				{/strip}
				</tbody>
			</table>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
