<div class="container-fluid" style="witdh: 800px;">
    <div class="dialog-title">モンスター情報詳細</div>
    {* 検索用入力エリア *}
    <div class="content-part-quest-info">
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                ニックネーム
            </div>
            <div class="content-part-quest-info-line-item">
               [{$app.monster_log_list.0.user_id}]&nbsp;{$app.monster_log_list.0.name}
               (Rank&nbsp;:&nbsp;{$app.monster_log_list.0.rank})
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                処理ID
            </div>
            <div class="content-part-quest-info-line-item">
               {$app.monster_log_list.0.api_transaction_id}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                処理日
            </div>
            <div class="content-part-quest-info-line-item">
                {$app.monster_log_list.0.date_log}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                実行API
            </div>
            <div class="content-part-quest-info-line-item">
                {$app.monster_log_list.0.account_name}
            </div>
        </div>
    </div>
    <hr>
    <div class="dialog-title">モンスター増減詳細情報</div>
    <div style="margin-bottom:10px;">
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-100">ID</div>
            <div class="quest-list-title content-list-part-250">モンスター名</div>
            <div class="quest-list-title content-list-part-50">増/減</div>
        </div>
        {foreach from=$app.monster_log_list item="monster" key="monster_key"}
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-100">
                {$monster.id}<br />
            </div>
            <div class="quest-list-content content-list-part-250">
                [{$monster.user_monster_id}]<br />
                [{$monster.monster_id}]&nbsp;{$monster.monster_name}<br />
                [{$monster.rare}]<br />
            </div>
            <div class="quest-list-content content-list-part-50">
                {$monster.status_name}
            </div>
        </div>
        {/foreach}
    </div>
</div><!--/span-->
