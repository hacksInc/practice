<div class="container-fluid" style="witdh: 800px;">
    <div class="dialog-title">フレンド履歴情報詳細</div>
    {* 検索用入力エリア *}
    <div class="content-part-quest-info">
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                ニックネーム
            </div>
            <div class="content-part-quest-info-line-item">
               [{$app.friend_log_list.0.user_id}]&nbsp;{$app.friend_log_list.0.name}
               (Rank&nbsp;:&nbsp;{$app.friend_log_list.0.rank})
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                処理ID
            </div>
            <div class="content-part-quest-info-line-item">
               {$app.friend_log_list.0.api_transaction_id}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                処理日
            </div>
            <div class="content-part-quest-info-line-item">
                {$app.friend_log_list.0.date_log}
            </div>
        </div>
        <div class="content-part-quest-info-line">
            <div class="content-part-quest-info-line-title">
                実行API
            </div>
            <div class="content-part-quest-info-line-item">
                {$app.friend_log_list.0.account_name}
            </div>
        </div>
    </div>
    <hr>
    <div class="dialog-title">フレンド申請履歴詳細情報</div>
    <div style="margin-bottom:10px;">
        <div class="quest-list-box">
            <div class="quest-list-title content-list-part-100">ID</div>
            <div class="quest-list-title content-list-part-130">処理</div>
            <div class="quest-list-title content-list-part-150">ユーザー</div>
            <div class="quest-list-title content-list-part-100">フレンド数</div>
            <div class="quest-list-title content-list-part-200">リーダーモンスター</div>
        </div>
        {foreach from=$app.friend_log_list item="friend" key="friend_key"}
        <div class="quest-list-box">
            <div class="quest-list-content content-list-part-100">
                {$friend.id}<br />
            </div>
            <div class="quest-list-content content-list-part-130">
                {$friend.processing_type_name}<br >
                ({$friend.date_log})
            </div>
            <div>
                <div class="content-monster-float-box">
                    <div class="quest-list-content content-list-part-150">
                        [{$friend.u_user_id}]&nbsp;{$friend.u_name}<br />
                        Rank&nbsp;:&nbsp;{$friend.u_rank}
                    </div>
                    <div class="quest-list-content content-list-part-100">
                        {$friend.f_friend_max_num}<br />
                        ({$friend.u_old_friend_rest}&nbsp;→&nbsp;<span style="font-weight:bold;">{$friend.u_friend_rest}</span>)<br />
                    </div>
                    <div class="search-list-content content-list-part-200">
                        [{$friend.u_reader_monster_id}]&nbsp;{$friend.u_reader_monster_name}<br />
                        Rare&nbsp;:&nbsp;{$friend.u_reader_monster_rare}<br />
                        Lv&nbsp;:&nbsp;{$friend.u_reader_monster_lv}<br />
                        Skill_Lv&nbsp;:&nbsp;{$friend.u_reader_monster_skill_lv}
                    </div>
                </div>
                <div class="content-monster-float-box">
                    <div class="quest-list-content content-list-part-150">
                        [{$friend.f_user_id}]&nbsp;{$friend.f_name}<br />
                        Rank&nbsp;:&nbsp;{$friend.u_rank}
                    </div>
                    <div class="quest-list-content content-list-part-100">
                        {$friend.f_friend_max_num}<br />
                        ({$friend.f_old_friend_rest}&nbsp;→&nbsp;<span style="font-weight:bold;">{$friend.f_friend_rest}</span>)<br />
                    </div>
                    <div class="search-list-content content-list-part-200">
                        [{$friend.f_reader_monster_id}]&nbsp;{$friend.f_reader_monster_name}<br />
                        Rare&nbsp;:&nbsp;{$friend.f_reader_monster_rare}<br />
                        Lv&nbsp;:&nbsp;{$friend.f_reader_monster_lv}<br />
                        Skill_Lv&nbsp;:&nbsp;{$friend.f_reader_monster_skill_lv}
                    </div>
                </div>
            </div>
        </div>
        {/foreach}
    </div>
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
