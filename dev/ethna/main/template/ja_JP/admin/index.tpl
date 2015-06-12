<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}
	
    <div class="row-fluid">
        <div class="span9">
			<ol>
{*				
				<li>{a href="#" target="_blank">売上げ実績を確認する（Paymentサーバへ遷移します）{/a}</li>

				<li><span style="color:gray">売上げ実績を確認する</span></li>
*}
				<li>{a href="/psychopass_game/admin/kpi/index"}KPI{/a}</li>
{*
				<li>{a href="/psychopass_game/admin/operation/index"}運営ツール{/a}</li>
				<li><span style="color:gray">運営ツール</span></li>
*}
				<li>{a href="/psychopass_game/admin/announce/index"}アナウンスデータ{/a}</li>
				<li>{a href="/psychopass_game/admin/developer/index"}マスタデータ{/a}</li>
				<li>{a href="/psychopass_game/admin/developer/user/index"}ユーザデータ{/a}</li>
				<li>{a href="/psychopass_game/admin/developer/raid/index"}レイド{/a}</li>
				<li>{a href="/psychopass_game/admin/account/index"}アカウント管理{/a}</li>
				<li>{a href="/psychopass_game/admin/present/index"}プレゼント配布{/a}</li>
				<li>{a href="/psychopass_game/admin/etc/index"}その他{*・未分類*}{/a}</li>
			</ol>
        </div><!--/span-->
    </div><!--/row-->

    <hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
