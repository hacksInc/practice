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
			<h2>オーダーリスト</h2>
			<form action="refresh/exec" method="post" class="text-right">
				リフレッシュデッキ数&nbsp;&nbsp;{$app.gacha_list.create_deck}&nbsp;&nbsp;
				<input type="submit" value="リフレッシュ" class="btn refresh-btn">
				<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
			</form>
			
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
					{form_name name="order_id"}
				</div>
				<div class="span2">
					<form action="index" method="post" class="one-button-only" id="form-order-id">
						<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
						{html_options name="order_id" values=$app.order_id_list output=$app.order_id_list selected=$app.order_id class="span11" id="select-order-id"}
					</form>
				</div>
				<div class="span2">
					デッキ数
				</div>
				<div class="span2">
					{$app.gacha_order_info.deck}	
				</div>
				<div class="span2">
					登録枚数
				</div>
				<div class="span2">
					{$app.total_number_of_monsters}
				</div>
			</div>

			<div class="row-fluid">
				<div class="span4">
				</div>
				<div class="span2">
					次ドロー位置
				</div>
				<div class="span2">
					{$app.gacha_order_info.list_idx}	
				</div>
			</div>
				
			<table class="table table-bordered table-condensed">
				<tr>
					<th>No.</th>
					<th>{$app.form_template.rarity.name}</th>
					<th>{$app.form_template.monster_id.name}</th>
					<th>モンスター名</th>
					<th>出現枚数/総枚数</th>
					<th>出現％</th>
					<th>取得ユーザーID</th>
					<th>取得日付</th>
				</tr>
				
				{foreach from=$app.gacha_draw_list item="row" key="i"}
				<tr>
					<td><div class="text-right">{$row.list_id}</div></td>
					<td><div class="text-right">{$row.rarity}</div></td>
					<td>{$row.monster_id}</td>
					<td>{$row.monster.name_ja}</td>
					<td>{$row.monster_idx}/{$row.number_of_monsters}</td>
					<td><div class="text-right">{$row.percentage_of_monsters|string_format:"%.2f"}%</div></td>
					<td>{$row.user_id}</td>
					<td>{$row.date_draw}</td>
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
{include file="admin/common/script.tpl"}
{literal}
<script>
	$(function(){
		$('#select-order-id').change(function(){
			$('#form-order-id').submit();
		});
		
		$('input.refresh-btn').click(function() {
			return window.confirm('リフレッシュしますがよろしいですか？');
		});
	});
</script>
{/literal}
</body>
</html>