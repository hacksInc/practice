<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ガチャ - サイコパス管理ページ"}
{literal}
<style>
	.cell-margin {
		margin-right: 10px;
		margin-left: 10px;
	}
</style>
{/literal}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ガチャ&nbsp;レアリティ&nbsp;修正</h2>

{*
			{if count($errors) > 0}
				<p class="text-error">
				{foreach from=$errors item=error}
					{$error|nl2br}<br />
				{/foreach}
				</p>
			{/if}
*}

			<div class="row-fluid">
				<div class="span2">
					{form_name name="gacha_id"}
				</div>
				<div class="span2">
					{$form.gacha_id}
				</div>
			</div>

			<form action="confirm" method="post" style="margin-top: 10px;">
				<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
				<table>
					<tr>
						<th>
							<div class="cell-margin">
								{form_name name="rarities"}
							</div>
						</th>
						<th>
							<div class="cell-margin">
								{form_name name="weights_float"}<i class="icon-question-sign" data-original-title="少数2桁まで"></i>
							</div>
						</th>
						<th>
							<div class="cell-margin">
								枚数
							</div>
						</th>
						<th>
							<div class="cell-margin">
								%
							</div>
						</th>
					</tr>

					{foreach from=$app.gacha_category_list item="row" key="i" name="loop1"}
					<tr>
						<td>
							<div class="text-right cell-margin">
								<input type="hidden" name="rarities[{$i}]" value="{$row.rarity}">
								{$row.rarity}
							</div>
						</td>
						<td>
							<div class="cell-margin">
								<input class="span12" type="text" name="weights_float[{$i}]" value="{$row.weight_float|string_format:"%.2f"}">
							</div>
						</td>
						<td>
							<div class="text-right cell-margin">
								{$row.number_of_monsters}
							</div>
						</td>
						<td>
							<div class="text-right cell-margin">
								{$row.percentage_of_monsters|string_format:"%.2f"}%
							</div>
						</td>
					</tr>
					{/foreach}

					<tr>
						<td colspan="4">
							<div class="text-center">
								<input type="submit" value="修正確認" class="btn" />
							</div>
						</td>
					</tr>
				</table>
			</form>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
</body>
</html>