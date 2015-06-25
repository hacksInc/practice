<div class="container-fluid" style="witdh: 800px;">
	<div class="dialog-title">勲章付与情報詳細</div>
	{* 検索用入力エリア *}
	<div class="content-part-quest-info">
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				ニックネーム
			</div>
			<div class="content-part-quest-info-line-item">
				[{$app.achievement_log_list.0.user_id}]&nbsp;{$app.achievement_log_list.0.name}
				(Rank&nbsp;:&nbsp;{$app.achievement_log_list.0.rank})
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				処理ID
			</div>
			<div class="content-part-quest-info-line-item">
				{$app.achievement_log_list.0.api_transaction_id}
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				処理日
			</div>
			<div class="content-part-quest-info-line-item">
				{$app.achievement_log_list.0.date_log}
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				実行API
			</div>
			<div class="content-part-quest-info-line-item">
				{$app.achievement_log_list.0.account_name}
			</div>
		</div>
	</div>
	<hr>
	<div class="dialog-title">勲章付与詳細情報</div>
	<div style="margin-bottom:10px;">
		<div class="quest-list-box">
			<div class="quest-list-title content-list-part-100">ID</div>
			<div class="quest-list-title content-list-part-300">勲章名</div>
			<div class="quest-list-title content-list-part-300">取得アイテム/モンスター</div>
		</div>
		{foreach from=$app.achievement_log_list item="achievement" key="achievement_key"}
		<div class="quest-list-box">
			<div class="quest-list-content content-list-part-100">
				{$achievement.id}<br />
			</div>
			<div class="quest-list-content content-list-part-300">
				[{$achievement.achievement_id}]&nbsp;{$achievement.achievement_name}<br />
				Rank&nbsp;:&nbsp;{$achievement.achievement_rank}<br />
				<span style="color:#999999;font-size:10px;">付与条件&nbsp;:&nbsp;{$achievement.achievement_description}</span><br />
			</div>
			{if $achievement.present_type==2}
			<div class="search-list-content content-list-part-300">
				[{$achievement.item_id}]&nbsp;{$monster_list.$transaction_id.monster_name}<br />
				ID：{$monster_list.$transaction_id.user_monster_id}<br />
				Lv：{$achievement.lv}<br />
			</div>
			{else}
			<div class="search-list-content content-list-part-300">[{$achievement.item_id}]&nbsp;{$item_data.item_name}</div>
			{/if}
		</div>
		{/foreach}
	</div>
</div><!--/span-->
