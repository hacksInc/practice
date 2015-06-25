{*
<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="デプロイ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
        <div class="span12">

			<iframe src="{$app.url}" width="960" height="640" />
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
*}
