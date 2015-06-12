<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ガチャ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ドローリスト</h2>
			<form method="post">
				<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
				{form_name name="date_draw_start"}
				<input type="text" name="date_draw_start" value="{$form.date_draw_start}" class="jquery-ui-datetimepicker">
				～
				<input type="text" name="date_draw_end" value="{$form.date_draw_end}" class="jquery-ui-datetimepicker">
				<button class="btn btn-switch-action" data-action="index">表示する</button>
				<button class="btn btn-switch-action" data-action="download">CSVダウンロード</button>
			</form>

			<hr>
			<div class="text-center">
				{$app_ne.pager.all}
			</div>

			<div class="row-fluid">
				<div class="span2">
					{form_name name="gacha_id"}
				</div>
				<div class="span2">
					{$form.gacha_id}
				</div>
			</div>
				
			<div class="row-fluid">
				<div class="span2">
					{form_name name="date_draw_start"}
				</div>
				<div class="span10">
					{$form.date_draw_start}
					～
					{$form.date_draw_end}
				</div>
			</div>

			<table class="table table-bordered table-condensed">
				<tr>
					{foreach from=$app.gacha_draw_list_header item="label" key="i"}
						<th>{$label}</th>
					{/foreach}
				</tr>
				
				{foreach from=$app.gacha_draw_list item="row" key="i"}
				<tr>
					{foreach from=$row item="col" key="i"}
						<td>{$col}</td>
					{/foreach}
				</tr>
				{/foreach}
			</table>

			<div class="text-center">
				{$app_ne.pager.all}
			</div>
			
			<p>
				{a href="../banner/index"}一覧へ戻る{/a}
			</p>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
{literal}
<script>
	$(function(){
		$('.btn-switch-action').click(function(){
			$(this.form).attr('action', $(this).data('action'));
			$(this.form).submit();
		});
	});
</script>
{/literal}
</body>
</html>