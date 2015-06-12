<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="権限一覧 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>権限一覧</h2>
			<h3>ロール</h3>
			{strip}
			<dl class="dl-horizontal role1">
				{foreach from=$app.role_master item="role_value" key="role_name"}
					<dt style="margin:0px;">{$role_name}</dt><dd>{$role_value}</dd>
				{/foreach}
			</dl>
			{/strip}

			<h3>パーミッション</h3>
			{strip}
			<table class="table table-bordered table-condensed">
				<tr>
					<th rowspan="3">action</th>
					<th rowspan="3">query</th>
					<th colspan="{$app.role_master_cnt*3}">role</th>
				</tr>
				<tr>
					<th colspan="{$app.role_master_cnt}">dev</th>
					<th colspan="{$app.role_master_cnt}">stg</th>
					<th colspan="{$app.role_master_cnt}">pro</th>
				</tr>
				<tr>
					{section loop=3 name="dummy"}
					{foreach from=$app.role_master item="role_value" key="role_name"}
						<th>{$role_name}</th>
					{/foreach}
					{/section}
				</tr>
				{foreach from=$app.permission_master item="row"}
					<tr>
						<td>{$row.action}</td>
						<td>
							{foreach from=$row.query item="query_value" key="query_name" name="loop2"}
								{if not $smarty.foreach.loop2.first}<br>{/if}
								{$query_name}: {$query_value}
							{/foreach}
						</td>
						{foreach from=$row.role item="row2" key="env"}
							{foreach from=$app.role_master item="role_value" key="role_name"}
								<td>{if in_array($role_name, $row2)}○{/if}</td>
							{/foreach}
						{/foreach}
					</tr>
				{/foreach}
			</table>
			{/strip}
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>