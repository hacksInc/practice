<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="アカウント一覧 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>アカウント一覧</h2>
			<table class="table">
			{foreach from=$app.list item="row"}
				<tr>
					<td>{$row.user}</td>
					<td>{$row.role_name}</td>
					{strip}<td>
						{if $smarty.session.lid == $row.user}
							<span class="muted">［権限変更］</span>
						{else}
							［{a href="role/update/input?lid=`$row.user`"}権限変更{/a}］
						{/if}
						［{a href="password/update/input?lid=`$row.user`"}パスワード変更{/a}］
						<span class="muted">［アクセスログ］</span>
					</td>{/strip}
				</tr>
			{/foreach}
			</table>
			
			<p>
				{a href="permission"}権限一覧{/a}
			</p>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>