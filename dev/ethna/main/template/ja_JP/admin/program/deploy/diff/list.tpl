<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="デプロイ制御 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
            <h2>{$app.title}</h2>
            <p>
                <small>
                    バックアップファイル(*.bak)は除く
                </small>
            </p>
			
			{foreach from=$app.lists item="list" key="dir"}
				{$dir}<br>
				{foreach from=$list item="line"}
					<form method="post" action="file" target="_blank" class="one-button-only">
						<input type="hidden" name="checkout_uniq" value="{$app.checkout_uniq}">
						<input type="hidden" name="msg" value="{$line}">
                        <input type="hidden" name="wcside" value="{$app.wcside}">
						<input type="submit" class="btn btn-link" value="{$line}">
					</form>
				{foreachelse}
					{$dir} is identical
				{/foreach}
				<hr>
			{/foreach}
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
