<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
    <div class="row-fluid" style="margin-top: 32px;">
        <div class="span9">
			{foreach from=$errors item="error"}
				{$error|nl2br}<br>
			{/foreach}
        </div><!--/span-->
    </div><!--/row-->

    <hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
