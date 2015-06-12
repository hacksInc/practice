<div class="container-fluid" style="witdh: 800px;">
	<div class="dialog-title">フォト取得情報詳細</div>
	{* 検索用入力エリア *}
	<div class="content-part-quest-info">
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				ニックネーム
			</div>
			<div class="content-part-quest-info-line-item">
				[{$app.photo_log_list.user_id}]&nbsp;{$app.photo_log_list.name}
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				処理ID
			</div>
			<div class="content-part-quest-info-line-item">
				{$app.photo_log_list.api_transaction_id}
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				処理日
			</div>
			<div class="content-part-quest-info-line-item">
				{$app.photo_log_list.date_log}&nbsp;[log_id:{$app.photo_log_list.id}]
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				フォト取得種類
			</div>
			<div class="content-part-quest-info-line-item">
				[{$app.photo_log_list.photo_id}]{$app.photo_log_list.photo_name}
			</div>
		</div>
		<div class="content-part-quest-info-line">
			<div class="content-part-quest-info-line-title">
				実行API
			</div>
			<div class="content-part-quest-info-line-item">
				{$app.quest_log_list.account_name}
			</div>
		</div>
	</div>
	<div style="margin-bottom:10px;">
		<div class="quest-list-box">
			<div class="quest-list-title content-list-part-680">アイテム情報</div>
		</div>
		<div class="quest-list-box">
			<div class="quest-list-content content-list-part-680">
				{foreach from=$app.item_log_list item="item_log_data" key="item_k"}
				[{$item_log_data.item_id}]&nbsp;{$item_log_data.item_name}{if $item_log_data.item_id=="9000"}{$item_log_data.service_name}{/if}
				(&nbsp;{$item_log_data.old_num}&nbsp;→&nbsp;{$item_log_data.num}&nbsp;)<br />
				{/foreach}
			</div>
		</div>
	</div>
	<hr>
	<div class="dialog-title">モンスター取得情報</div>
	{if $app.photo_prize_log_count!=0}
	<div>
		<div class="quest-list-box">
			<div class="quest-list-title content-list-part-100">ID</div>
			<div class="quest-list-title content-list-part-200">モンスター名</div>
			<div class="quest-list-title content-list-part-80">EXP</div>
			<div class="quest-list-title content-list-part-50">レベル</div>
			<div class="quest-list-title content-list-part-50">HP</div>
			<div class="quest-list-title content-list-part-50">攻撃力</div>
			<div class="quest-list-title content-list-part-50">スキルレベル</div>
		</div>
		{* foreach from=$app.photo_prize_log_list item="log_data" key="prize_k" *}
		{foreach from=$app.get_monster_log_list item="monster_data" key="monster_k"}
		{* if $monster_data.status=='-1' *}
		<div class="quest-list-box">
			<div class="quest-list-content content-list-part-100">
				{$monster_data.prize.id}<br />
				order_id&nbsp;:&nbsp;{$monster_data.prize.order_id}<br />
				list_id&nbsp;:&nbsp;{$monster_data.prize.list_id}<br />
			</div>
			<div class="quest-list-content content-list-part-200">
				{$monster_data.monster.user_monster_id}<br />
				[{$monster_data.monster.monster_id}]&nbsp;{$monster_data.monster.monster_name}<br />
				{$monster_data.monster.rare}
			</div>
			<div class="quest-list-content content-list-part-80">{$monster_data.monster.exp}</div>
			<div class="quest-list-content content-list-part-50">{$monster_data.monster.lv}</div>
			<div class="quest-list-content content-list-part-50">{$monster_data.monster.hp}</div>
			<div class="quest-list-content content-list-part-50">{$monster_data.monster.attack}</div>
			<div class="quest-list-content content-list-part-50">{$monster_data.monster.skill_lv}</div>
		</div>
		{/foreach}
	</div>
	{else}
	モンスターの取得はありません
	{/if}
	<hr>
	<div class="dialog-title">勲章付与情報</div>
	{if $app.achievement_log_count!=0}

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
	{else}
	勲章の付与はありません
	{/if}
</div><!--/span-->
