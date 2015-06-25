<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="整合性チェック - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
    {include file="admin/common/breadcrumb.tpl"}

    <div class="row-fluid">
        {include file="admin/common/sidebar.tpl"}

        <div class="span9">
            <h2>マスターデータ 整合性チェック結果</h2>
			{if count($errors)}
				<ul>
					{foreach from=$errors item=error}
						<div style="color: #ff0000;">{$error}</div>
					{/foreach}
				</ul>
			{else}
                {foreach from=$app.master_list key="table" item="item"}
                    <span style="font-weight: bold;">{$item.name}マスタ</span>
                    {if $item.cnt==0}
                        <ul>
                            <li>正常です。</li>
                        </ul>
                    {else}
                        <ul>
                            {foreach from=$item.err_msg key="key" item="msg"}
                            <li><span class="label label-important">Warning!!</span>&nbsp;{$msg}</li>
                            {/foreach}
                        </ul>
                    {/if}
                {/foreach}
                {if $app.all_cnt==0}
                    <span style="color: #000000;font-weight: bold;">マスタ整合性に問題点はありません。</span>
                {else}
                    <span style="color: #ff0000;font-weight: bold;">{$app.all_cnt}件の問題点が確認されました。</span>
                {/if}
			{/if}
        </div><!--/span-->
    </div><!--/row-->
    <hr>
    {include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
