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
			<h3>データ更新</h3>
{*
			※↓m_asset_bundle,モンスターマスタ(m_monster)は、実際に更新が行なわれます。左記以外は、開発中のため、実際には更新は行われず、画面遷移のみ確認可能です。
*}
			<form action="regist" method="POST">
				<input type="submit" value="更新する">

				<table class="table table-bordered table-striped table-condensed">
					<thead>
						<tr>
							<th>&nbsp;</th>
							{foreach from=$app.label item="label"}
								<th>{$label}</th>
							{/foreach}
						</tr>
					</thead>

					<tbody>
					{strip}
					{foreach from=$app.list key="key" item="row"}
					{if $app.row_crud[$key]}
						<tr>
							<td><b>
								{if $app.row_crud[$key] eq "c"}
									<font color="#0000ff">追加</font>
								{elseif $app.row_crud[$key] eq "d"}
									<font color="#ff0000">削除</font>
								{elseif $app.row_crud[$key] eq "u"}
									<font color="#008000">変更</font>
								{else}
									&nbsp;
								{/if}
							</b></td>
							{foreach from=$row item="cellvalue" name="loop2"}
								<td>
								{assign var="i" value=$smarty.foreach.loop2.index}
								{if $app.row_crud[$key] eq "c"}
									<font color="#0000ff">{$cellvalue|nl2br}</font>
								{elseif $app.row_crud[$key] eq "d"}
									<font color="#ff0000">{$cellvalue|nl2br}</font>
								{elseif ($app.row_crud[$key] eq "u") and $app.cell_update[$key][$i]}
									<font color="#008000">{$cellvalue|nl2br}</font>
								{else}
									{$cellvalue|nl2br}
								{/if}
								</td>
							{/foreach}
						</tr>
					{/if}
					{/foreach}
					{/strip}
					</tbody>
				</table>

				{foreach from=$app.row_crud key="key" item="value"}
					{if $value}
						<input type="hidden" name="crudlist[{$key}]" value="{$value}">
					{/if}
				{/foreach}
				<input type="hidden" name="file" value="{$app.fname}">
				<input type="hidden" name="table" value="{$form.table}">
			</form>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
