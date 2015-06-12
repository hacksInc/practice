<div class="dropdown">
	<button class="btn dropdown-toggle sr-only" type="button" id="dropdownMenu1" data-toggle="dropdown">
		ログ選択
		<span class="caret"></span>
	</button>
	<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu2">
		<li class="dropdowm-header">アイテム管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/accounting/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/accounting/index"}課金アイテム購入履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/item/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/item/index"}アイテム増減履歴{/a}
		</li>
		<li class="divider"></li>
		<li class="dropdowm-header">キャラクター管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/character/index")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/character/index"}キャラクター履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/stress/index")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/stress/index"}ストレスケア履歴{/a}
		</li>
		<li class="divider"></li>
		<li class="dropdowm-header">ミッション管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/mission/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/mission/index"}ミッション履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/area/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/area/index"}エリアストレス履歴{/a}
		</li>
		<li class="divider"></li>
		<li class="dropdowm-header">ユーザー管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/user/login/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/user/login/index"}ユーザーログイン履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/present/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/present/index"}プレゼント履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/achievement/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/achievement/index"}アチーブメント履歴{/a}
		</li>
		<li class="divider"></li>
		<li class="dropdowm-header">フォト管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/gacha/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/gacha/index"}ガチャ履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/photo/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/photo/index"}フォト取得履歴{/a}
		</li>




		<!--
		<li class="divider"></li>
		<li class="dropdowm-header">モンスター管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/monster/index")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/monster/index"}モンスター増減履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/monster/powerup")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/monster/powerup/index"}モンスター強化合成履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/monster/evolution")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/monster/evolution/index"}モンスター進化合成履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/monster/sell")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/monster/sell/index"}モンスター売却履歴{/a}
		</li>
		<li class="divider"></li>
		<li class="dropdowm-header">クエスト管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/quest/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/quest/index"}クエスト履歴{/a}
		</li>
		<li class="divider"></li>
		<li class="dropdowm-header">ユーザー管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/user/base/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/user/base/index"}ユーザー情報履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/user/login/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/user/login/index"}ユーザーログイン履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/user/tutorial/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/user/tutorial/index"}ユーザーチュートリアル履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/present/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/present/index"}プレゼント履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/achievement/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/achievement/index"}勲章履歴{/a}
		</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/friend/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/friend/index"}フレンド履歴{/a}
		</li>
		<li class="divider"></li>
		<li class="dropdowm-header">ガチャ管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/gacha/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/gacha/index"}ガチャ履歴{/a}
		</li>
		<li class="divider"></li>
		<li class="dropdowm-header">ポイント管理</li>
		<li role="presentation"{if script_match("/psychopass_game/admin/log/cs/point/request/")} class="disabled"{/if}>
		{a href="/psychopass_game/admin/log/cs/point/request/index"}ポイント管理アクセスログ{/a}
		</li>
		-->
		<li role="presentation" class="divider"></li>
	</ul>
</div><!--/.well -->
