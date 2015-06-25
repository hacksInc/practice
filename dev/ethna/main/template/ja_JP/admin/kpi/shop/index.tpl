<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="売上情報 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>売上情報</h2>
			メニューを選択して下さい。<br />
			<ol>
				<li>{a href="sum/index"}アイテムの販売個数{/a}</li>
				<li>{a href="uu/index"}アイテムの購入者数{/a}</li>
				<li>{a href="user/index"}会員数{/a}</li>
				<li>{a href="userlog/select"}購入履歴{/a}</li>
			</ol>

{*
			※DL数（アプリダウンロード数）は取得できません。<br />
			※課金者数、ARPU、ARPPU、購入履歴はPaymentサーバを参照して下さい。<br />
*}
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>