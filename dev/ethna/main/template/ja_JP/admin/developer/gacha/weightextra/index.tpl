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
			<h2>おまけガチャウェイト&nbsp;設定</h2>
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
					{$app.form_template.comment.name}
				</div>
				<div class="span2">
					{$app.gacha_list.comment}
				</div>
			</div>
			
			<h3>おまけガチャカテゴリマスタ</h3>
			<div class="row-fluid">
				<div class="span8">
					<table class="table table-bordered table-condensed">
						<tr>
							<td>
								{a href="category/create/input?gacha_id=`$form.gacha_id`" class="btn btn-block"}追加{/a}
							</td>
							<th>{$app.form_template.rarity.name}</th>
							<th>{$app.form_template.weight_float.name}</th>
							<th>枚数<i class="icon-question-sign" data-original-title="レアリティごとの生成される枚数"></i></th>
							<th>割合<i class="icon-question-sign" data-original-title="生成枚数に対する割合"></i></th>
							<td>
							</td>
						</tr>

						{foreach from=$app.gacha_extra_category_list item="row" key="i" name="loop1"}
						<tr>
							{if $smarty.foreach.loop1.first}
								<td rowspan="{$smarty.foreach.loop1.total}">
								<form action="category/update/multi/input" method="post" class="one-button-only">
									<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
									<input type="submit" value="修正" class="btn btn-block">
								</form>
								</td>
							{/if}
							<td><div class="text-right">{$row.rarity}</div></td>
							<td><div class="text-right">{$row.weight_float|string_format:"%.2f"}</div></td>
							<td><div class="text-right">{$row.number_of_monsters}{*<i class="icon-question-sign" data-original-title="レアリティごとの生成される枚数"></i>*}</div></td>
							<td><div class="text-right">{$row.percentage_of_monsters|string_format:"%.2f"}%</div></td>
							<td>
								<form action="category/delete/exec" method="post" class="one-button-only">
									<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
									<input type="hidden" name="rarity" value="{$row.rarity}">
									<input type="submit" value="削除" class="btn btn-block delete-btn">
								</form>
							</td>
						</tr>
						{/foreach}
						<tr>
							<td colspan="3">計</td>
							<td><div class="text-right">{$app.total_number_of_monsters}{*<i class="icon-question-sign" data-original-title="全レアリティの生成される枚数"></i>*}</div></td>
							<td colspan="2"></td>
						</tr>
					</table>
				</div>
				<div class="span4">
					<div>
						<div class="text-center"><strong>CSVアップロード</strong><i class="icon-question-sign" data-original-title="CSVではウェイト値は100倍で設定してください。"></i></div>
						<form action="category/upload/confirm" method="POST" enctype="multipart/form-data">
							<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
							<input type="file" name="xml" class="file-drop"><br />
							<input type="submit" value="次へ" class="btn">
						</form>
					</div>
					<div>
						<div class="text-center"><strong>データ取得</strong><i class="icon-question-sign" data-original-title="CSVではウェイト値は100倍されています。"></i></div>
						{a href="category/download?gacha_id=`$form.gacha_id`" class="btn btn-block"}CSV取得{/a}
					</div>
				</div>
			</div>
			<div class="text-right">{a href="category/log/view?gacha_id=`$form.gacha_id`"}＞ 操作ログ閲覧{/a}&nbsp;&nbsp;{a href="category/log/list?gacha_id=`$form.gacha_id`"}＞ ログ閲覧{/a}</div>
			
			<hr>
			<h3>おまけガチャアイテムリストマスタ</h3>
			<div class="row-fluid">
				<div class="span4">
					<div class="text-center"><strong>CSVアップロード</strong><i class="icon-question-sign" data-original-title="CSVではウェイト値は100倍で設定してください。"></i></div>
					<form action="item/upload/confirm" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
						<input type="file" name="xml" class="file-drop"><br />
						<input type="submit" value="次へ" class="btn">
					</form>
				</div>
				<div class="span4">
					<div class="text-center"><strong>データ取得</strong><i class="icon-question-sign" data-original-title="CSVではウェイト値は100倍されています。"></i></div>
					{a href="item/download?gacha_id=`$form.gacha_id`" class="btn btn-block"}CSV取得{/a}
				</div>
			</div>
			<div class="text-right" style="margin-top: 10px;">{a href="item/log/view?gacha_id=`$form.gacha_id`"}＞ 操作ログ閲覧{/a}&nbsp;&nbsp;{a href="item/log/list?gacha_id=`$form.gacha_id`"}＞ ログ閲覧{/a}</div>
			
			<table class="table table-bordered table-condensed">
				<tr>
					<th>Action</th>
					<th>{$app.form_template.rarity.name}</th>
					<th>{$app.form_template.monster_id.name}</th>
					<th>画像</th>
					<th>モンスター名</th>
					<th>モンスターLV</th>
					<th>{$app.form_template.weight_float.name}</th>
					<th>枚数<i class="icon-question-sign" data-original-title="レアリティのウェイト×モンスターのウェイト"></i></th>
					<th>割合<i class="icon-question-sign" data-original-title="全枚数に対する割合"></i></th>
					<th>Action</th>
				</tr>
				
				<tr>
					<td>
						{a href="item/create/input?gacha_id=`$form.gacha_id`" class="btn btn-block"}追加{/a}
					</td>
					<td>
						<div class="text-center">-</div>
					</td>
					<td>
						<div class="text-center">-</div>
					</td>
					<td>
						<div class="text-center">-</div>
					</td>
					<td>
						<div class="text-center">-</div>
					</td>
					<td>
					</td>
					<td>
					</td>
					<td>
					</td>
					<td>
					</td>
				</tr>
				{foreach from=$app.gacha_extra_item_list item="row" key="i"}
				<tr>
					<td>
						<form action="item/update/input" method="post" class="one-button-only">
							<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
							<input type="hidden" name="rarity" value="{$row.rarity}">
							<input type="hidden" name="monster_id" value="{$row.monster_id}">
							<input type="hidden" name="monster_lv" value="{$row.monster_lv}">
							<input type="submit" value="修正" class="btn btn-block">
						</form>
					</td>
					<td><div class="text-right">{$row.rarity}</div></td>
					<td>{$row.monster_id}</td>
					<td>
						{if $row.monster.mtime}
							<a href="/admin/developer/assetbundle/monster/image?monster_id={$row.monster_id}&dummy={$row.monster.mtime}" target="_blank" class="hoverImg">
							<img width="64" height="64" src="/admin/developer/assetbundle/monster/image?monster_id={$row.monster_id}&dummy={$row.monster.mtime}">
							</a>
						{/if}
					</td>
					
					<td>{$row.monster.name_ja}</td>
					<td>{$row.monster_lv}</td>
					<td><div class="text-right">{$row.weight_float|string_format:"%.2f"}</div></td>
					<td><div class="text-right">{$row.number_of_monsters}</div></td>
					<td><div class="text-right">{$row.percentage_of_monsters|string_format:"%.4f"}%</div></td>
					<td>
						<form action="item/delete/exec" method="post" class="one-button-only">
							<input type="hidden" name="gacha_id" value="{$form.gacha_id}">
							<input type="hidden" name="rarity" value="{$row.rarity}">
							<input type="hidden" name="monster_id" value="{$row.monster_id}">
							<input type="hidden" name="monster_lv" value="{$row.monster_lv}">
							<input type="submit" value="削除" class="btn btn-block delete-btn">
						</form>
					</td>
				</tr>
				{/foreach}
			</table>
			
			<p>
				{a href="../banner/index"}一覧へ戻る{/a}
			</p>
			
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
<script src="/js/admin/common/hover-img.js"></script>
{literal}
<script>
	$(function(){
		var deck_min = {/literal}{$app.form_template.deck.min}{literal};
			
		$('input.delete-btn').click(function() {
			return window.confirm('削除します。よろしいですか？');
		});
	});
</script>
{/literal}
</body>
</html>