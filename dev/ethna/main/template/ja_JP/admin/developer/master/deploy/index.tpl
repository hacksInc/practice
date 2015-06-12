<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="テーブル選択 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}

<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">

		{include file="admin/common/sidebar.tpl"}

{*
		<li {if script_match("/psychopass_game/admin/developer/master/deploy/") && ($form.mode == "standby")}class="active"{/if}>
			{a href="/psychopass_game/admin/developer/master/deploy/index?mode=standby"}商用同期{/a}
		</li>
		<li {if script_match("/psychopass_game/admin/developer/master/deploy/") && ($form.mode == "deploy")}class="active"{/if}>
			{a href="/psychopass_game/admin/developer/master/deploy/index?mode=deploy"}デプロイ{/a}
		</li>
		<li {if script_match("/psychopass_game/admin/developer/master/deploy/") && ($form.mode == "deploy")}class="active"{/if}>
			{a href="/psychopass_game/admin/developer/assetbundle/deploy/index"}デプロイ制御(アセットバンドル){/a}
		</li>
*}

		{if $form.mode != ""}
		<form action="confirm" method="get" class="form-horizontal" id="formList">
		<input type="hidden" name="mode" value="{$form.mode}" />
		<div class="span9">
			<h2>{if $form.mode == "standby"}商用同期{elseif $form.mode == "deploy"}デプロイ{/if}テーブル選択</h2>

			<input type="submit" name="btn" value="デプロイ確認" class="btn btn-primary" /><br />
			<br />
			<label for="all_checked"><input type="checkbox" id="all_checked" />&nbsp;全選択</label>
			<table class="table table-bordered table-condensed">
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>テーブル名称</th>
						<th>DB名称</th>
						<th>データ更新日時</th>
						<th>更新者</th>
						<th>概要</th>
					</tr>
				</thead>
				<tbody>
				{foreach from=$app.list key="table" item="row"}
					<tr>
						<td>{form_input name=$table id=$table class="deploy_check"}</td>
						<td>{a href="confirm?`$table`=1"}{$row.table_label}{/a}</td>
						<td>{$table}</td>
						<td>{$row.last_modified.date_modified}</td>
						<td>{$row.last_modified.account_upd}</td>
						<td>{$row.summary}</td>
					</tr>
				{/foreach}
				</tbody>
			</table>
		</div><!--/span-->
		</form>
		{/if}
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}

{literal}
<script>
$(function() {
	$('#all_checked').change(function() {
		var items = $(this).closest('#formList').next().find('input');
		if ($(this).is(':checked')) {
			$('.deploy_check').prop('checked', true);
		} else {
		$('.deploy_check').prop('checked', false);
		}
	});
});
</script>
{/literal}

</body>
</html>
