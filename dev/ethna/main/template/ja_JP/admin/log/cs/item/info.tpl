<div class="container-fluid" style="witdh: 800px;">
	<div class="dialog-title">アイテム情報詳細</div>
	{* 検索用入力エリア *}
	<div class="content-part-quest-info">
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				ニックネーム
			</div>
			<div class="content-part-quest-info-line-item">
				[{$app.item_log_list.0.user_id}]&nbsp;{$app.item_log_list.0.name}
				(Rank&nbsp;:&nbsp;{$app.item_log_list.0.rank})
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				処理ID
			</div>
			<div class="content-part-quest-info-line-item">
				{$app.item_log_list.0.api_transaction_id}
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				処理日
			</div>
			<div class="content-part-quest-info-line-item">
				{$app.item_log_list.0.date_log}
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				実行API
			</div>
			<div class="content-part-quest-info-line-item">
				{$app.item_log_list.0.account_name}
			</div>
		</div>
	</div>
	<hr>
	<div class="dialog-title">アイテム増減詳細情報</div>
	<div style="margin-bottom:10px;">
		<div class="quest-list-box">
			<div class="quest-list-title content-list-part-100">ID</div>
			<div class="quest-list-title content-list-part-250">アイテム名</div>
			<div class="quest-list-title content-list-part-50">増/減</div>
			<div class="quest-list-title content-list-part-200">数量</div>
		</div>
		{foreach from=$app.item_log_list item="item" key="item_key"}
		<div class="quest-list-box">
			<div class="quest-list-content content-list-part-100">
				{$item.id}<br />
			</div>
			<div class="quest-list-content content-list-part-250">
				[{$item.item_id}]&nbsp;{$item.item_name}
				{if $item.item_id=="9000"}{$item.service_name}{/if}<br />
			</div>
			<div class="quest-list-content content-list-part-50">
				{if $item.count > 0}増
				{elseif $item.count < 0}減
				{else}－
				{/if}
			</div>
			<div class="search-list-content content-list-part-200">
				{$item.count}<br />
				({$item.old_num}&nbsp;→&nbsp;{$item.num})<br />
			</div>
		</div>
		{/foreach}
	</div>
</div><!--/span-->
