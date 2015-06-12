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
            <h2>マスターデータの整合性チェック</h2>
            <form action="check" method="post" class="form-horizontal" id="form1">
                <input class="btn btn-success" type="submit" value="整合性チェック開始！">
            </form>
        </div><!--/span-->
    </div><!--/row-->
    <hr>
    {include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
