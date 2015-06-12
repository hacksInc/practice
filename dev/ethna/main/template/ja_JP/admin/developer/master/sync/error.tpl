<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
    <div class="row-fluid" style="margin-top: 32px;">
        <div class="span9">
			<ul>
			{foreach from=$app.app_errors item="error" name="loop1"}
					<li>{$error.message|nl2br}</li>
					{if isset($error.verify_info)}
						※下記diffコマンドで使用したテンポラリファイルはサーバーに残っています。詳細はサーバーにあるファイルを参照して下さい。
						<pre>$ {$error.verify_info.command}
{$error.verify_info.output}</pre>
					{/if}
			{/foreach}
			</ul>
        </div><!--/span-->
    </div><!--/row-->

    <hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>