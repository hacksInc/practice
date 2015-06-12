<div class="navbar {if Util::getAppverEnv() != "main"}navbar-inverse{/if} navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			{a class="brand" href="/psychopass_game/admin/index"}サイコパス管理{/a}
			<div class="nav-collapse collapse">
				<div class="btn-group pull-right">
					<a class="btn btn-small {if Util::getAppverEnv() != "main"}btn-inverse{/if} dropdown-toggle" data-toggle="dropdown" href="/psychopass_game#">
						{$smarty.session.lid}
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li>{a href="/psychopass_game/admin/account/self/password/update/input"}パスワード変更{/a}</li>
						<li class="divider"></li>
						<li><a href="/psychopass_game/admin/logout">ログアウト</a></li>
					</ul>
				</div>

				<p class="navbar-text pull-right">
					<i class="icon-info-sign" data-placement="bottom" data-original-title="{strip}
					{env_label}
					&nbsp;
					{if Util::getAppverEnv() == "main"}
						main
					{elseif Util::getAppverEnv() == "review"}
						review
					{/if}
					&nbsp;
					{if $smarty.session.unit}
						unit{$smarty.session.unit}
					{/if}
					{/strip}"></i>
					&nbsp;
				</p>
				<ul class="nav">
				<li {if script_match("/psychopass_game/admin/kpi/")}class="active"{/if}>
					{a href="/psychopass_game/admin/kpi/index"}KPI{/a}
				</li>

				<li {if script_match("/psychopass_game/admin/program/")}class="active"{/if}>
					{a href="/psychopass_game/admin/program/index"}プログラム{/a}
				</li>

				<li {if script_match("/psychopass_game/admin/announce/")
						|| script_match("/psychopass_game/admin/present/")}class="active"{/if}>
					{a href="/psychopass_game/admin/announce/index"}アナウンス{/a}
				</li>

{*
				<li {if script_match("/psychopass_game/admin/developer/master/") ||
						script_match("/psychopass_game/admin/developer/assetbundle/") ||
						script_match("/psychopass_game/admin/developer/gacha/") ||
						script_match("/psychopass_game/admin/developer/index")}class="active"{/if}>
					{a href="/psychopass_game/admin/developer/master/index"}マスタ{/a}
				</li>
*}
                                <li {if script_match("/psychopass_game/admin/developer/master/") 
						&& !script_match("/psychopass_game/admin/developer/master/deploy/")}class="active"{/if}>
                                        {a href="/psychopass_game/admin/developer/master/index"}マスタ{/a}
                                </li>

				<li {if script_match("/psychopass_game/admin/developer/user/")}	class="active"{/if}>
					{a href="/psychopass_game/admin/developer/user/index"}ユーザ{/a}
				</li>

				<li {if script_match("/psychopass_game/admin/account/")}class="active"{/if}>
					{a href="/psychopass_game/admin/account/index"}アカウント{/a}
				</li>

				<li {if script_match("/psychopass_game/admin/log/")}class="active"{/if}>
					{a href="/psychopass_game/admin/log/index"}行動ログ{/a}
				</li>

				<li {if script_match("/psychopass_game/admin/etc/")}class="active"{/if}>
					{a href="/psychopass_game/admin/etc/index"}その他{*・未分類*}{/a}
				</li>

				<li {if script_match("/psychopass_game/admin/developer/master/deploy/")
						|| script_match("/psychopass_game/admin/developer/assetbundle/deploy/")
						|| script_match("/psychopass_game/admin/developer/announce/deploy/")}class="active"{/if}>
					{a href="/psychopass_game/admin/developer/master/deploy/index"}デプロイ{/a}
				</li>

				</ul>
			</div><!--/.nav-collapse -->
		</div>
	</div>
</div>
