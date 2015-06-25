<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ダイアログメッセージ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ダイアログメッセージデータ登録&nbsp;確認</h2>
			<form id="formDialogUpdate" action="exec" method="post" class="form-horizontal">
                <dl class="dl-horizontal">
    				<dt>{form_name name="dialog_id"}</dt>
    				<dd>
	    				<input type="hidden" name="dialog_id" value="{$form.dialog_id}">
		    			{form_input name="base_dialog_id" id="baseDialogId"}
		    			{$form.dialog_id}
			    	</dd>
			    </dl>
				
                <dl class="dl-horizontal">
                    <dt>{form_name name="dialog_type"}</dt>
                    <dd>
                        <input type="hidden" name="dialog_type" value="{$form.dialog_type}">
                        {$form.dialog_type}
                    </dd>
                </dl>

                <dl class="dl-horizontal">
                    <dt>{form_name name="use_name"}</dt>
                    <dd>
                        <input type="hidden" name="use_name" value="{$form.use_name}">
                        {$form.use_name}
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
        $("#formDialogUpdate").attr('action', 'input');
        $("#formDialogUpdate").submit();
    });
{/literal}
</script>
</body>
</html>
