<div class="container-fluid" style="witdh: 800px;">
    <div class="dialog-title">モンスター強化合成情報</div>
    {* 検索用入力エリア *}
    <div class="content-part-quest-info">
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                ニックネーム
            </div>
            <div class="content-part-quest-info-line-item">
               [{$app.monster_powerup_log.user_id}]&nbsp;{$app.monster_powerup_log.name}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                処理ID
            </div>
            <div class="content-part-quest-info-line-item">
               {$app.monster_powerup_log.api_transaction_id}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                処理日
            </div>
            <div class="content-part-quest-info-line-item">
                {$app.monster_powerup_log.date_log}&nbsp;[log_id:{$app.monster_powerup_log.id}]
            </div>
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-200">モンスターID</div>
            <div class="quest-list-title content-list-part-300">強化詳細</div>
            <div class="quest-list-title content-list-part-150">コスト</div>
        </div>
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-200">
                {$app.monster_powerup_log.user_monster_id}<br />
                [{$app.monster_powerup_log.monster_id}]&nbsp;{$app.monster_powerup_log.monster_name}<br />
                {$app.monster_powerup_log.rare}<br />
            </div>
            <div class="quest-list-content content-list-part-300">
                Lv&nbsp;:&nbsp;{$app.monster_powerup_log.old_lv}&nbsp;→&nbsp;{$app.monster_powerup_log.lv}<br />
                EXP&nbsp;:&nbsp;{$app.monster_powerup_log.old_exp}&nbsp;→&nbsp;{$app.monster_powerup_log.exp}<br />
                HP&nbsp;:&nbsp;{$app.monster_powerup_log.old_hp}&nbsp;→&nbsp;{$app.monster_powerup_log.hp}<br />
                AT&nbsp;:&nbsp;{$app.monster_powerup_log.old_attack}&nbsp;→&nbsp;{$app.monster_powerup_log.attack}<br />
                SkillLv&nbsp;:&nbsp;{$app.monster_powerup_log.old_skill_lv}&nbsp;→&nbsp;{$app.monster_powerup_log.skill_lv}<br />
            </div>
            <div class="quest-list-content content-list-part-150">
                {$app.monster_powerup_log.cost}<br />
                ({$app.item_log_list.old_num}&nbsp;→&nbsp;{$app.item_log_list.num})
            </div>
        </div>
    </div>
    <hr>
    <div class="dialog-title">消費モンスター情報</div>
    <div>
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-100">ID</div>
            <div class="quest-list-title content-list-part-200">モンスター名</div>
            <div class="quest-list-title content-list-part-80">EXP</div>
            <div class="quest-list-title content-list-part-50">レベル</div>
            <div class="quest-list-title content-list-part-50">HP</div>
            <div class="quest-list-title content-list-part-50">攻撃力</div>
            <div class="quest-list-title content-list-part-50">スキルレベル</div>
            <div class="quest-list-title content-list-part-50">素材EXP</div>
            <div class="quest-list-title content-list-part-50">素材Skill_Lv</div>
        </div>
        {foreach from=$app.monster_log_list item="monster_data" key="monster_k"}
        {* if $monster_data.status=='-1' *}
        {if $monster_data.user_monster_id!=$app.monster_powerup_log.user_monster_id}
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-100">{$monster_data.id}</div>
            <div class="quest-list-content content-list-part-200">
            {$monster_data.user_monster_id}<br />
            [{$monster_data.monster_id}]&nbsp;{$monster_data.monster_name}<br />
            {$monster_data.rare}
            </div>
            <div class="quest-list-content content-list-part-80">{$monster_data.exp}</div>
            <div class="quest-list-content content-list-part-50">{$monster_data.lv}</div>
            <div class="quest-list-content content-list-part-50">{$monster_data.hp}</div>
            <div class="quest-list-content content-list-part-50">{$monster_data.attack}</div>
            <div class="quest-list-content content-list-part-50">{$monster_data.skill_lv}</div>
            <div class="quest-list-content content-list-part-50">{$monster_data.add_exp}</div>
            <div class="quest-list-content content-list-part-50">{$monster_data.add_skill_lv}</div>
        </div>
        {/if}
        {/foreach}
    </div>
    <hr>
    <div class="dialog-title">勲章付与情報</div>
    {if $app.achievement_log_list_count!=0}
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
                <span style="color: #999999;font-size:10px;">{$achievement.achievement_description}</span><br />
            </div>
            <div class="quest-list-content content-list-part-300">
            </div>
        </div>
        {/foreach}
    </div>
    {else}
        勲章の付与はありません
    {/if}
</div><!--/span-->
