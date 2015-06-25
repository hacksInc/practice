<div class="container-fluid" style="witdh: 800px;">
    <div class="dialog-title">クエスト情報</div>
    {* 検索用入力エリア *}
    <div class="content-part-quest-info">
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                ニックネーム
            </div>
            <div class="content-part-quest-info-line-item">
               [{$app.quest_start_log.user_id}]&nbsp;{$app.quest_start_log.user_name}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                マップID
            </div>
            <div class="content-part-quest-info-line-item">
               [{$app.quest_start_log.map_id}]&nbsp;{$app.quest_start_log.map_name}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                クエストID
            </div>
            <div class="content-part-quest-info-line-item">
               [{$app.quest_start_log.quest_id}]&nbsp;{$app.quest_start_log.quest_name}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                エリアID
            </div>
            <div class="content-part-quest-info-line-item">
               [{$app.quest_start_log.area_id}]&nbsp;{$app.quest_start_log.area_name}
            </div>
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-110">PLAY_ID</div>
            <div class="quest-list-title content-list-part-200">クエスト開始</div>
            <div class="quest-list-title content-list-part-200">クエスト終了</div>
            <div class="quest-list-title content-list-part-170">ステータス</div>
        </div>
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-110">{$app.quest_start_log.id}</div>
            <div class="quest-list-content content-list-part-200">{$app.quest_start_log.date_log}<br />({$app.quest_start_log.api_transaction_id})</div>
            <div class="quest-list-content content-list-part-200">{$app.quest_end_log.date_log}<br />({$app.quest_end_log.api_transaction_id})</div>
            <div class="quest-list-content content-list-part-170">{$app.quest_end_log.quest_st_name}<br />(コンティニュー：{$app.quest_end_log.continue_cnt}回)</div>
        </div>
    </div>
    <div style="margin-bottom:10px;">
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-680">取得アイテム</div>
        </div>
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-680">
                <ul>
                    <li>取得コイン&nbsp;：&nbsp;{$app.quest_end_log.drop_gold}</li>
                    <li>取得モンスター&nbsp;：&nbsp;</li>
                </ul>
            </div>
        </div>
    </div>
    <hr>
    <div class="dialog-title">チーム情報</div>
    <div>
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-100">ID</div>
            <div class="quest-list-title content-list-part-80">配置場所</div>
            <div class="quest-list-title content-list-part-200">モンスター名</div>
            <div class="quest-list-title content-list-part-80">EXP</div>
            <div class="quest-list-title content-list-part-50">レベル</div>
            <div class="quest-list-title content-list-part-50">HP</div>
            <div class="quest-list-title content-list-part-50">攻撃力</div>
            <div class="quest-list-title content-list-part-50">スキルレベル</div>
        </div>
        {foreach from=$app.quest_team_log_list item="team_data" key="team_k"}
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-100">{$team_data.id}</div>
            <div class="quest-list-content content-list-part-80">{$team_data.position}</div>
            <div class="quest-list-content content-list-part-200">
            {$team_data.user_monster_id}<br />
            [{$team_data.monster_id}]{$team_data.monster_name}<br />
            {$team_data.rare}
            </div>
            <div class="quest-list-content content-list-part-80">{$team_data.exp}</div>
            <div class="quest-list-content content-list-part-50">{$team_data.lv}</div>
            <div class="quest-list-content content-list-part-50">{$team_data.hp}</div>
            <div class="quest-list-content content-list-part-50">{$team_data.attack}</div>
            <div class="quest-list-content content-list-part-50">{$team_data.skill_lv}</div>
        </div>
        {/foreach}
    </div>
    <hr>
    <div class="dialog-title">
        <div class="quest-list-box">
            <div>出現モンスター情報</div>
            <button id="btnQuestEnemyMonsterView" style="width30px;margin-left:520px;background-color:#ffffff">＋</button>
        </div>
    </div>
    {* 検索結果出力 *}
    <div class="quest-enemy-monster-list" style="display: none;">
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-80">ID</div>
            <div class="quest-list-title content-list-part-50">バトル番号</div>
            <div class="quest-list-title content-list-part-200">モンスター名</div>
            {* <div class="quest-list-title content-list-part-50">attribute_id</div> *}
            <div class="quest-list-title content-list-part-120">モンスター詳細</div>
            <div class="quest-list-title content-list-part-250">ドロップ情報</div>
        </div>
        {foreach from=$app.quest_monster_log_list item="monster_data" key="monster_k"}
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-80">{$log_data.id}</div>
            <div class="quest-list-content content-list-part-50">{$monster_data.battle_num}</div>
            <div class="quest-list-content content-list-part-200">[{$monster_data.monster_id}]{$monster_data.monster_name}</div>
            {* <div class="quest-list-content content-list-part-50">{$monster_data.attribute_id}</div> *}
            <div class="quest-list-content content-list-part-120">
                HP&nbsp;:&nbsp;{$monster_data.hp}<br />
                Atc&nbsp;:&nbsp;{$monster_data.attack}<br />
                Def&nbsp;:&nbsp;{$monster_data.defense}<br />
                boss&nbsp;：&nbsp;{$monster_data.boss_flag}{if $monster_data.boss_flag=='1'}(Lv&nbsp;:&nbsp;{$monster_data.boss_lv}){/if}<br />
                idx&nbsp;:&nbsp;{$monster_data.enemy_id}
            </div>
            <div class="quest-list-content content-list-part-250">
                通常ドロップ
                {if $monster_data.normal_drop_type=='1'}
                <ul>
                    <li>タイプ&nbsp;：&nbsp;{$monster_data.normal_drop_type}</li>
                    <li>モンスター名&nbsp;：&nbsp;[{$monster_data.normal_drop_monster_id}]{$monster_data.normal_drop_monster_name}</li>
                    <li>レベル&nbsp;：&nbsp;{$monster_data.normal_drop_monster_lv}</li>
                </ul>
                {elseif $monster_data.normal_drop_type=='3'}
                <ul>
                    <li>タイプ&nbsp;：&nbsp;{$monster_data.normal_drop_type}</li>
                    <li>アイテム名&nbsp;：&nbsp;[{$monster_data.normal_drop_monster_id}]{$monster_data.normal_drop_monster_name}</li>
                    <li>数量&nbsp;：&nbsp;{$monster_data.normal_drop_monster_lv}</li>
                </ul>
                {else}
                    <br />　なし
                {/if}
                {if $monster_data.overkill_drop_type=='1'}
                <div class="quest-info-overkill-drop">
                オーバーキルドロップ
                <ul>
                    <li>タイプ&nbsp;：&nbsp;{$monster_data.overkill_drop_type}</li>
                    <li>モンスター名&nbsp;：&nbsp;[{$monster_data.overkill_drop_monster_id}]{$monster_data.overkill_drop_monster_name}</li>
                    <li>レベル&nbsp;：&nbsp;{$monster_data.overkill_drop_monster_lv}</li>
                </ul>
                </div>
                {elseif $monster_data.overkill_drop_type=='3'}
                <div class="quest-info-overkill-drop">
                オーバーキルドロップ
                <ul>
                    <li>タイプ&nbsp;：&nbsp;{$monster_data.overkill_drop_type}</li>
                    <li>アイテム名&nbsp;：&nbsp;[{$monster_data.overkill_drop_monster_id}]{$monster_data.overkill_drop_monster_name}</li>
                    <li>数量&nbsp;：&nbsp;{$monster_data.overkill_drop_monster_lv}</li>
                </ul>
                </div>
                {/if}
            </div>
        </div>
        {/foreach}
    </div>
</div><!--/span-->
{literal}
<script>
    $("#btnQuestEnemyMonsterView").click(function(){
        $(".quest-enemy-monster-list").toggle();
    });
</script>
{/literal}
