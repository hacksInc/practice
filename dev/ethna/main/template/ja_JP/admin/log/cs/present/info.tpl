<div class="container-fluid" style="witdh: 800px;">
    <div class="dialog-title">プレゼント情報詳細</div>
    {* 検索用入力エリア *}
    <div class="content-part-quest-info">
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                ニックネーム
            </div>
            <div class="content-part-quest-info-line-item">
               [{$app.present_log_list.0.user_id}]&nbsp;{$app.present_log_list.0.name}
               (Rank&nbsp;:&nbsp;{$app.present_log_list.0.rank})
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                処理ID
            </div>
            <div class="content-part-quest-info-line-item">
               {$app.present_log_list.0.api_transaction_id}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                処理日
            </div>
            <div class="content-part-quest-info-line-item">
                {$app.present_log_list.0.date_log}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                実行API
            </div>
            <div class="content-part-quest-info-line-item">
                {$app.present_log_list.0.account_name}
            </div>
        </div>
    </div>
    <div style="margin-bottom:10px;margin-top:10px;">
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-100">ID</div>
            <div class="quest-list-title content-list-part-100">ステータス</div>
            <div class="quest-list-title content-list-part-150">プレゼント種類</div>
            <div class="quest-list-title content-list-part-200">プレゼント概要</div>
            <div class="quest-list-title content-list-part-50">数量</div>
        </div>
        {foreach from=$app.present_log_list item="present" key="present_key"}
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-100">
                {$present.present_id}
            </div>
            <div class="quest-list-content content-list-part-100">
                {$present.status_name}
            </div>
            <div class="quest-list-content content-list-part-150">
                {$present.present_type_name}
            </div>
            <div class="quest-list-content content-list-part-200">
                [{$present.item_id}]&nbsp;{$present.item_name}<br />
                {if $present.present_type=="2"}Lv&nbsp;:&nbsp;{$present.lv}{/if}
            </div>
            <div class="quest-list-content content-list-part-50">
                {$present.number}
            </div>
        </div>
        {/foreach}
    </div>
    <hr>
    <div class="dialog-title">アイテム取得詳細情報</div>
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
    <hr>
    <div class="dialog-title">モンスター取得詳細情報</div>
    <div style="margin-bottom:10px;">
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-100">ID</div>
            <div class="quest-list-title content-list-part-250">モンスター名</div>
            <div class="quest-list-title content-list-part-50">Lv</div>
            <div class="quest-list-title content-list-part-50">HP</div>
            <div class="quest-list-title content-list-part-50">Act</div>
            <div class="quest-list-title content-list-part-50">Skill_Lv</div>
        </div>
        {foreach from=$app.monster_log_list item="monster" key="monster_key"}
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-100">
                {$monster.id}<br />
            </div>
            <div class="quest-list-content content-list-part-250">
                {$monster.user_monster_id}<br />
                [{$monster.monster_id}]&nbsp;{$monster.monster_name}<br />
                {$monster.rare}<br />
            </div>
            <div class="quest-list-content content-list-part-50">
                {$monster.Lv}
            </div>
            <div class="quest-list-content content-list-part-50">
                {$monster.hp}
            </div>
            <div class="quest-list-content content-list-part-50">
                {$monster.attack}
            </div>
            <div class="quest-list-content content-list-part-50">
                {$monster.skill_lv}
            </div>
        </div>
        {/foreach}
    </div>
</div><!--/span-->
