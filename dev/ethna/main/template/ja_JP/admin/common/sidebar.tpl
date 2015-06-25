<div class="span2">
    <div class="well sidebar-nav">
        <ul class="nav nav-list">
		{if script_match("/psychopass_game/admin/kpi/")}
			<li class="nav-header">KPIユーザ動向</li>
			<li {if script_match("/psychopass_game/admin/kpi/user/continuance/")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/user/continuance/index"}継続率{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/user/mission/index")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/user/mission/index"}ミッション攻略情報{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/user/mission/fail/")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/user/mission/fail/index"}FAIL情報{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/user/hazard/level")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/user/hazard/level"}サイコハザード情報(レベル毎){/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/user/hazard/stage")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/user/hazard/stage"}サイコハザード情報(STAGE毎){/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/user/stresscare/")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/user/stresscare/index"}ストレスケア情報{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/device/info/")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/device/info/index"}デバイス情報{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/user/base/daily")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/user/base/daily"}基本情報(日){/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/user/base/monthly")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/user/base/monthly"}基本情報(月){/a}
			</li>

			<li class="nav-header">KPIアイテム</li>
			<li {if script_match("/psychopass_game/admin/kpi/item/sales/daily")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/item/sales/daily"}アイテム販売情報(日){/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/item/sales/monthly")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/item/sales/monthly"}アイテム販売情報(月){/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/item/use/")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/item/use/index"}アイテム使用情報{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/gacha/rate/")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/gacha/rate/index"}ガチャ回転情報{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/gacha/user/")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/gacha/user/index"}ユーザ毎ガチャ情報{/a}
			</li>

			<li class="nav-header">LTV</li>
			<li {if script_match("/psychopass_game/admin/kpi/ltv/daily")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/ltv/daily"}LTV(日){/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/ltv/monthly")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/ltv/monthly"}LTV(月){/a}
			</li>
			<li {if script_match("/psychopass_game/admin/kpi/ltv/charge")} class="active"{/if}>
				{a href="/psychopass_game/admin/kpi/ltv/charge"}登録月別化金額＆課金UU{/a}
			</li>
		{elseif script_match("/psychopass_game/admin/program/")}
		    <li class="nav-header">プログラム</li>
			<li {if script_match("/psychopass_game/admin/program/deploy/backup")}class="active"{/if}>
				{a href="/psychopass_game/admin/program/deploy/backup"}バックアップ{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/program/deploy/svn") || script_match("/psychopass_game/admin/program/deploy/diff/svn")}class="active"{/if}>
				{a href="/psychopass_game/admin/program/deploy/svn"}SVN反映{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/program/deploy/rsync") || script_match("/psychopass_game/admin/program/deploy/diff/dest")}class="active"{/if}>
				{a href="/psychopass_game/admin/program/deploy/rsync"}商用反映{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/program/deploy/makuo") || script_match("/psychopass_game/admin/program/deploy/diff/makuo")}class="active"{/if}>
				{a href="/psychopass_game/admin/program/deploy/makuo"}デプロイ{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/program/deploy/ctrl/log/")}class="active"{/if}>
				{a href="/psychopass_game/admin/program/deploy/ctrl/log/view"}操作ログ{/a}
			</li>
			<li class="divider"></li>
				<li {if script_match("/psychopass_game/admin/program/entry/")}class="active"{/if}>
					{a href="/psychopass_game/admin/program/entry/ini/update/input"}エントリポイント{/a}
				</li>
		{elseif script_match("/psychopass_game/admin/announce/") || script_match("/psychopass_game/admin/present/")}
		    <li class="nav-header">アナウンスデータ</li>
		    <li {if script_match("/psychopass_game/admin/announce/news/content/")}class="active"{/if}>
				{a href="/psychopass_game/admin/announce/news/content/index"}お知らせ{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/announce/message/dialog/")}class="active"{/if}>
				{a href="/psychopass_game/admin/announce/message/dialog/index"}ダイアログメッセージ{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/announce/message/error/")}class="active"{/if}>
				{a href="/psychopass_game/admin/announce/message/error/index"}エラーメッセージ{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/announce/help/category/")}class="active"{/if}>
				{a href="/psychopass_game/admin/announce/help/category/index"}ヘルプ大項目設定{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/announce/help/detail/")}class="active"{/if}>
				{a href="/psychopass_game/admin/announce/help/detail/index"}ヘルプ詳細文設定{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/announce/home/banner/")}class="active"{/if}>
				{a href="/psychopass_game/admin/announce/home/banner/index"}メインバナー{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/present/distribution/content/")}class="active"{/if}>
				{a href="/psychopass_game/admin/present/distribution/content/index"}プレゼント配布{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/announce/gamectrl/")}class="active"{/if}>
				{a href="/psychopass_game/admin/announce/gamectrl/index"}ゲーム制御{/a}
			</li>
		{elseif strncmp($smarty.server.SCRIPT_NAME, "/admin/developer/raid/", strlen("/admin/developer/raid/")) === 0}
		    <li class="nav-header">レイド</li>
			<li {if script_match("/psychopass_game/admin/developer/raid/partylist")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/raid/partylist"}パーティ一覧{/a}
			</li>
		{elseif script_match("/psychopass_game/admin/developer/user/")}
			<li class="nav-header">ユーザデータ</li>
			<li {if script_match("/psychopass_game/admin/developer/user/view/")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/user/view/index"}閲覧{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/developer/user/ctrl/")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/user/ctrl/index"}制御{/a}
			</li>
		{elseif script_match("/psychopass_game/admin/developer/master/")
				|| script_match("/psychopass_game/admin/developer/assetbundle/")
				|| script_match("/psychopass_game/admin/developer/announce/")
				|| script_match("/psychopass_game/admin/developer/master/")}
			<li class="nav-header">マスタデータ</li>
			<li {if script_match("/psychopass_game/admin/developer/master/deploy/") && ($form.mode == "standby")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/master/deploy/index?mode=standby"}商用同期{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/developer/master/deploy/") && ($form.mode == "deploy")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/master/deploy/index?mode=deploy"}デプロイ{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/developer/master/consistency/")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/master/consistency/index"}整合性チェック{/a}
			</li>
			
			
			<li class="nav-header">アセットバンドル</li>
			<li {if script_match("/psychopass_game/admin/developer/assetbundle/list/")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/assetbundle/list/index"}最新アセットバンドル{/a}
			</li>
			<li {if script_match("/psychopass_game/admin/developer/assetbundle/deploy/")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/assetbundle/deploy/index"}デプロイ制御(アセットバンドル){/a}
			</li>
			<li class="nav-header">アナウンスデータ</li>
			<li {if script_match("/psychopass_game/admin/developer/announce/deploy/")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/announce/deploy/index"}デプロイ制御(アナウンス画像){/a}
			</li>
			<li class="nav-header">マスターデータ</li>
			<li {if script_match("/psychopass_game/admin/developer/master/output/")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/master/output/index"}マスターデータバックアップ{/a}
			</li>
		{elseif script_match("/psychopass_game/admin/account/")}
		    <li class="nav-header">アカウント管理</li>
		    <li {if script_match("/psychopass_game/admin/account/list") || script_match("/psychopass_game/admin/account/password/update/")}class="active"{/if}>
				{a href="/psychopass_game/admin/account/list"}アカウント一覧{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/account/create/")}	class="active"{/if}>
				{a href="/psychopass_game/admin/account/create/input"}アカウント追加{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/account/delete/")}	class="active"{/if}>
				{a href="/psychopass_game/admin/account/delete/input"}アカウント削除{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/account/log/")}class="active"{/if}>
				{a href="/psychopass_game/admin/account/log/view"}アカウント操作ログ{/a}
			</li>
		{elseif script_match("/psychopass_game/admin/log/")}
		    <li class="nav-header">CS用ログ管理</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/item/")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/item/index"}アイテム増減履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/monster/index")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/monster/index"}モンスター増減履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/monster/powerup")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/monster/powerup/index"}モンスター強化合成履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/monster/evolution")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/monster/evolution/index"}モンスター進化合成履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/monster/sell")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/monster/sell/index"}モンスター売却履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/present/")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/present/index"}プレゼント履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/quest/")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/quest/index"}クエスト履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/achievement/")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/achievement/index"}勲章履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/user/base/")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/user/base/index"}ユーザー情報履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/user/login/")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/user/login/index"}ユーザーログイン履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/user/tutorial/")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/user/tutorial/index"}ユーザーチュートリアル履歴{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/log/cs/gacha/")} class="active"{/if}>
				{a href="/psychopass_game/admin/log/cs/gacha/index"}ガチャ履歴{/a}
			</li>
		{else}
		    <li class="nav-header">その他{*・未分類*}</li>
		    <li {if script_match("/psychopass_game/admin/etc/log/")}class="active"{/if}>
				{a href="/psychopass_game/admin/etc/log/select"}ログのダウンロード{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/test/data/")}class="active"{/if}>
				{a href="/psychopass_game/admin/test/data/input"}テストデータ生成{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/test/api/")}class="active"{/if}>
				{a href="/psychopass_game/admin/test/api/input"}API検証{/a}
			</li>
		    <li {if script_match("/psychopass_game/admin/developer/ranking/result/")}class="active"{/if}>
				{a href="/psychopass_game/admin/developer/ranking/result/index"}ランキング集計結果{/a}
			</li>
		{/if}
        </ul>
    </div><!--/.well -->
</div><!--/span-->
