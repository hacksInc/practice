<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ヘルプバーメッセージ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ヘルプバーメッセージデータ登録&nbsp;確認</h2>
			<form id="formHelpbarUpdate" action="exec" method="post" class="form-horizontal">
                <dl class="dl-horizontal">
    				<dt>{form_name name="helpbar_id"}</dt>
    				<dd>
	    				<input type="hidden" name="helpbar_id" value="{$form.helpbar_id}">
                        {form_input name="base_helpbar_id" id="baseHelpbarId"}
		    			{$form.helpbar_id}
			    	</dd>
			    </dl>

                <dl class="dl-horizontal">
                    <dt>{form_name name="message"}</dt>
                    <dd>
                        <input type="hidden" name="message" value="{$form.message}">
                        {$form.message}
                    </dd>
                </dl>
				<br>
				<div class="text-center">
                   <input type="button" id="btnBack" value="戻る" class="btn btn-info span2" />
				   <input type="submit" value="修正" class="btn btn-primary span2" />
			   </div>
			</form>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
<script>
{literal}
    $("#btnBack").click (function() {
        $("#formHelpbarUpdate").attr('action', 'input');
        $("#formHelpbarUpdate").submit();
    });
{/literal}
</script>
</body>
</html>
