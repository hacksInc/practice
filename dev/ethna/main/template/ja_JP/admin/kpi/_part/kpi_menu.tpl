<div class="dropdown">
    <button class="btn dropdown-toggle sr-only" type="button" id="dropdownMenu1" data-toggle="dropdown">
      KPI選択
      <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu2">
        <li class="dropdowm-header">マジカルメダル</li>
        <li role="presentation" class="divider"></li>
        <li class="dropdowm-header">モンスター</li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/monster/use/index")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/monster/use/index"}モンスター使用率{/a}
        </li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/monster/distribution/index")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/monster/distribution/index"}モンスター流通量{/a}
        </li>
        <li role="presentation" class="divider"></li>
        <li class="dropdowm-header">ユーザー動向</li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/user/continuance/")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/user/continuance/index"}継続率{/a}
        </li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/user/battle/progress/")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/user/battle/progress/index"}バトル進捗{/a}
        </li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/user/area/progress/")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/user/area/progress/index"}エリア進捗{/a}
        </li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/user/area/progress2/")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/user/area/progress/index"}エリア進捗(新){/a}
        </li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/device/info/")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/device/info/index"}デバイス情報{/a}
        </li>
        <li role="presentation" class="divider"></li>
        <li class="dropdowm-header">アイテム</li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/item/charge/index")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/item/charge/index"}アイテム毎課金率{/a}
        </li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/gacha/charge/index")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/gacha/charge/index"}ガチャ毎課金率{/a}
        </li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/gacha/uu/index")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/gacha/uu/index"}ガチャ毎課金UU{/a}
        </li>
        <li role="presentation"{if script_match("/psychopass_game/admin/kpi/gacha/aggregate/")} class="disabled"{/if}>
            {a href="/psychopass_game/admin/kpi/gacha/aggregate/index"}ユーザー毎ガチャ回数{/a}
        </li>
    </ul>
</div><!--/.well -->
