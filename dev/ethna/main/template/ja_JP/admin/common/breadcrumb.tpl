<div class="row-fluid">
<div class="span9">
{if $smarty.server.SCRIPT_NAME != "/psychopass_game/admin/index"}
	{a href="/psychopass_game/admin/index"}Home{/a} <span class="divider">＞</span>
	{if script_match("/psychopass_game/admin/kpi/")}
		{if script_match("/psychopass_game/admin/kpi/index")}
			KPI
		{else}
			{a href="/psychopass_game/admin/kpi/index"}KPI{/a} <span class="divider">＞</span>

			{if script_match("/psychopass_game/admin/kpi/user/")}
				{if script_match("/psychopass_game/admin/kpi/user/index")}
					ユーザー動向
				{else}
					ユーザー動向 <span class="divider">＞</span>
					{if script_match("/psychopass_game/admin/kpi/user/continuance/")}
					   継続率
					{elseif script_match("/psychopass_game/admin/kpi/user/mission/index")}
					   ミッション攻略情報
					{elseif script_match("/psychopass_game/admin/kpi/user/mission/fail/index")}
					   FAIL情報
					{elseif script_match("/psychopass_game/admin/kpi/user/hazard/level")}
					   サイコハザード情報(レベル毎)
					{elseif script_match("/psychopass_game/admin/kpi/user/hazard/stage")}
					   サイコハザード情報(STAGE毎)
					{elseif script_match("/psychopass_game/admin/kpi/user/stresscare/")}
					   ストレスケア情報
					{elseif script_match("/psychopass_game/admin/kpi/device/info/")}
					   デバイス情報
					{elseif script_match("/psychopass_game/admin/kpi/user/base/daily")}
					   基本情報(日)
					{elseif script_match("/psychopass_game/admin/kpi/user/base/monthly")}
					   基本情報(月)
					{/if}
				{/if}

			{elseif script_match("/psychopass_game/admin/kpi/item/") || script_match("/psychopass_game/admin/kpi/gacha/")}
				{if script_match("/psychopass_game/admin/kpi/item/index")}
					アイテム
				{else}
					アイテム <span class="divider">＞</span>
					{if script_match("/psychopass_game/admin/kpi/item/sales/daily")}
					   アイテム販売情報（日）
					{elseif script_match("/psychopass_game/admin/kpi/item/sales/monthly")}
					   アイテム販売情報（月）
					{elseif script_match("/psychopass_game/admin/kpi/item/use/")}
					   アイテム使用情報
					{elseif script_match("/psychopass_game/admin/kpi/gacha/rate/")}
					   ガチャ回転情報
					{elseif script_match("/psychopass_game/admin/kpi/gacha/user/")}
					   ユーザ毎ガチャ情報
					{/if}
				{/if}

			{elseif script_match("/psychopass_game/admin/kpi/ltv/")}
				{if script_match("/psychopass_game/admin/kpi/ltv/index")}
					LTV
				{else}
					LTV <span class="divider">＞</span>
					{if script_match("/psychopass_game/admin/kpi/ltv/daily")}
					   LTV(日)
					{elseif script_match("/psychopass_game/admin/kpi/ltv/monthly")}
					   LTV(月)
					{elseif script_match("/psychopass_game/admin/kpi/ltv/charge")}
					   登録月別化金額＆課金UU
					{/if}
				{/if}
			{/if}

		{/if}

	{elseif script_match("/psychopass_game/admin/announce/")}
		{if script_match("/psychopass_game/admin/announce/index")}
			アナウンスデータ
		{else}
			{a href="/psychopass_game/admin/announce/index"}アナウンスデータ{/a} <span class="divider">＞</span>

			{if script_match("/psychopass_game/admin/announce/news/content/")}
				お知らせ
			{/if}
			{if script_match("/psychopass_game/admin/announce/message/dialog/")}
				ダイアログメッセージ
			{/if}
			{if script_match("/psychopass_game/admin/announce/message/error/")}
				エラーメッセージ
			{/if}
			{if script_match("/psychopass_game/admin/announce/help/category/")}
				ヘルプ大項目設定
			{/if}
			{if script_match("/psychopass_game/admin/announce/help/detail/")}
				ヘルプ詳細文設定
			{/if}
			{if script_match("/psychopass_game/admin/announce/home/banner/")}
				メインバナー
			{/if}
			{if strncmp($smarty.server.SCRIPT_NAME, "/psychopass_game/admin/announce/gamectrl/", strlen("/psychopass_game/admin/announce/gamectrl/")) === 0}
				ゲーム制御
			{/if}
			{if script_match("/psychopass_game/admin/announce/deploy/")}
				デプロイ制御
			{/if}
		{/if}

	{elseif script_match("/psychopass_game/admin/present/")}
		{if script_match("/psychopass_game/admin/present/index")}
				アナウンスデータ
		{else}
			{a href="/psychopass_game/admin/present/index"}アナウンスデータ{/a} <span class="divider">＞</span>

			{if script_match("/psychopass_game/admin/present/distribution/content/")}
				プレゼント配布
			{/if}
		{/if}

	{elseif script_match("/psychopass_game/admin/program/")}
		{if script_match("/psychopass_game/admin/program/index")}
			プログラム
		{else}
			{a href="/psychopass_game/admin/program/index"}プログラム{/a} <span class="divider">＞</span>

			{if script_match("/psychopass_game/admin/program/deploy/index")}
				デプロイ
{*
			{elseif script_match("/psychopass_game/admin/program/deploy/ctrl/")}
				デプロイ制御
			{elseif script_match("/psychopass_game/admin/program/deploy/diff/")}
				デプロイ検証
*}
			{elseif script_match("/psychopass_game/admin/program/deploy/backup")}
				バックアップ
			{elseif script_match("/psychopass_game/admin/program/deploy/svn") || script_match("/psychopass_game/admin/program/deploy/diff/svn")}
				SVN反映
			{elseif script_match("/psychopass_game/admin/program/deploy/rsync") || script_match("/psychopass_game/admin/program/deploy/diff/dest")}
				商用反映
			{elseif script_match("/psychopass_game/admin/program/deploy/makuo") || script_match("/psychopass_game/admin/program/deploy/diff/makuo")}
				デプロイ
			{elseif script_match("/psychopass_game/admin/program/deploy/ctrl/log/")}
				操作ログ
			{elseif script_match("/psychopass_game/admin/program/entry/")}
				エントリポイント
			{/if}
		{/if}

	{elseif script_match("/psychopass_game/admin/developer/user/")}
		{if script_match("/psychopass_game/admin/developer/user/index")}
			ユーザデータ
		{else}
			{a href="/psychopass_game/admin/developer/user/index"}ユーザデータ{/a} <span class="divider">＞</span>

			{if script_match("/psychopass_game/admin/developer/user/view/")}
				閲覧
			{elseif script_match("/psychopass_game/admin/developer/user/ctrl/")}
				制御
			{/if}
		{/if}

	{elseif strncmp($smarty.server.SCRIPT_NAME, "/psychopass_game/admin/developer/raid/", strlen("/psychopass_game/admin/developer/raid/")) === 0}
		{if script_match("/psychopass_game/admin/developer/raid/index")}
			レイド
		{else}
			{a href="/psychopass_game/admin/developer/raid/index"}レイド{/a} <span class="divider">＞</span>

			{if script_match("/psychopass_game/admin/developer/raid/partylist/")}
				パーティ一覧
			{/if}
		{/if}

	{elseif script_match("/psychopass_game/admin/developer/")}
		{if script_match("/psychopass_game/admin/developer/master/deploy/index")}
			デプロイ
		{else}
			{a href="/psychopass_game/admin/developer/master/deploy/index"}デプロイ{/a} <span class="divider">＞</span>
		{/if}

		{if script_match("/psychopass_game/admin/developer/master/deploy/")}
			{if $form.mode == "standby"}
				商用同期
			{elseif $form.mode == "deploy"}
				デプロイ
			{elseif $form.mode == "unitsync"}
				ユニット間同期
			{/if}
		{elseif script_match("/psychopass_game/admin/developer/master/consistency/")}
			整合性チェック
		{elseif script_match("/psychopass_game/admin/developer/assetbundle/list/")}
			最新アセットバンドル
		{elseif script_match("/psychopass_game/admin/developer/assetbundle/deploy/")}
			デプロイ制御（アセットバンドル）
		{elseif script_match("/psychopass_game/admin/developer/announce/deploy/")}
			デプロイ制御（アナウンス画像）
		{elseif script_match("/psychopass_game/admin/developer/master/output/")}
			マスターデータ  バックアップ
		{/if}

	{elseif script_match("/psychopass_game/admin/account/")}
		{if script_match("/psychopass_game/admin/account/index")}
			アカウント管理
		{else}
			{a href="/psychopass_game/admin/account/index"}アカウント管理{/a} <span class="divider">＞</span>

			{if (script_match("/psychopass_game/admin/account/list") ||
				script_match("/psychopass_game/admin/account/password/update/"))}
				アカウント一覧
			{elseif script_match("/psychopass_game/admin/account/create/")}
				アカウント追加
			{elseif script_match("/psychopass_game/admin/account/delete/")}
				アカウント削除
			{elseif script_match("/psychopass_game/admin/account/log/")}
				アカウント操作ログ
			{/if}
		{/if}

	{elseif script_match("/psychopass_game/admin/log/")}
		{if script_match("/psychopass_game/admin/log/index")}
			ログ管理
		{else}
			{a href="/psychopass_game/admin/log/index"}ログ管理{/a} <span class="divider">＞</span>
			{a href="/psychopass_game/admin/log/cs/index"}CS用ログ管理{/a} <span class="divider">＞</span>

			{if script_match("/psychopass_game/admin/log/cs/item/")}
			   アイテム履歴
			{elseif script_match("/psychopass_game/admin/log/cs/monster/index")}
				モンスター履歴
			{elseif script_match("/psychopass_game/admin/log/cs/monster/powerup/")}
				モンスター強化履歴
			{elseif script_match("/psychopass_game/admin/log/cs/monster/evolution/")}
				モンスター進化履歴
			{elseif script_match("/psychopass_game/admin/log/cs/monster/sell/")}
				モンスター売却履歴
			{elseif script_match("/psychopass_game/admin/log/cs/present/")}
				プレゼント履歴
			{elseif script_match("/psychopass_game/admin/log/cs/quest/")}
				クエスト履歴
			{elseif script_match("/psychopass_game/admin/log/cs/achievement/")}
				勲章履歴
			{elseif script_match("/psychopass_game/admin/log/cs/user/base/")}
				ユーザー情報履歴
			{elseif script_match("/psychopass_game/admin/log/cs/user/login/")}
				ユーザーログイン履歴
			{elseif script_match("/psychopass_game/admin/log/cs/user/tutorial/")}
				ユーザーチュートリアル履歴
			{elseif script_match("/psychopass_game/admin/log/cs/gacha/")}
				ガチャ履歴
			{elseif script_match("/psychopass_game/admin/log/cs/point/")}
				ポイント管理
			{/if}
		{/if}

	{elseif script_match("/psychopass_game/admin/etc/")}
		{if script_match("/psychopass_game/admin/etc/index")}
			その他{*・未分類*}
		{else}
			{a href="/psychopass_game/admin/etc/index"}その他{*・未分類*}{/a} <span class="divider">＞</span>

			{if script_match("/psychopass_game/admin/etc/log/")}
			   ログのダウンロード
			{/if}
		{/if}

	{elseif script_match("/psychopass_game/admin/test/api/")}
		その他
	{/if}
{/if}
</div>
<div class="span3">
	<div class="pull-right">
		{strip}<span class="label environment-dependent-inverse">
		{env_label}
		</span>
		{if Util::getAppverEnv() == "main"}
			&nbsp;
			<span class="label environment-dependent-inverse">
			main
			</span>
		{elseif Util::getAppverEnv() == "review"}
			&nbsp;
			<span class="label environment-dependent-inverse">
			review
			</span>
		{/if}
		{if $smarty.session.unit}
			&nbsp;
			<span class="label environment-dependent-inverse">
			unit{$smarty.session.unit}
			</span>
		{/if}{/strip}
	</div>
</div>
</div>
