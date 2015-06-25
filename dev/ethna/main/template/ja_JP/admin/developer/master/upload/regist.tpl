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

			{if count($errors) > 0}
				<p class="text-error">
				{foreach from=$errors item=error}
					{$error|nl2br}<br />
				{/foreach}
				</p>
			{else}
				<p{* class="text-success"*}>
				更新が完了しました。<br />
				</p>
			{/if}

			<p>
				{if $app.back_location}
					{a href=$app.back_location}戻る{/a}
				{else}
					{a href="../list?table=`$form.table`"}戻る{/a}
				{/if}
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
