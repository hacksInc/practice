<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="エラーメッセージ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>エラーメッセージデータ登録&nbsp;確認</h2>
			<form id="formErrorUpdate" action="exec" method="post" class="form-horizontal">
                <dl class="dl-horizontal">
    				<dt>{form_name name="error_id"}</dt>
    				<dd>
	    				<input type="hidden" name="error_id" value="{$form.error_id}">
                        {form_input name="base_error_id" id="baseErrorId"}
		    			{$form.error_id}
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
        $("#formErrorUpdate").attr('action', 'input');
        $("#formErrorUpdate").submit();
    });
{/literal}
</script>
</body>
</html>
