<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ヘルプバーメッセージ - サイコパス管理ページ"}
<body>
{include file="admin/announce/message/_part/message_css.tpl"}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ヘルプバーメッセージ&nbsp;修正</h2>
            {if count($errors)}
            <div class="alert alert-error">
              {foreach from=$errors item=error}
               <div style="color: #ff0000;">{$error}</div>
              {/foreach}
            </div>
            {/if}
			<form action="confirm" method="post" enctype="multipart/form-data" class="form-horizontal">
				<input type="hidden" name="helpbar_id" value="{$app.row.helpbar_id}">

			    <div class="row-fluid">
                    <div class="control-group">
                      <label class="control-label" for="helpbarId">{form_name name="helpbar_id"}</label>
                      <div class="controls">
                        {form_input name="helpbar_id" id="helpbarId"}
                        {form_input name="base_helpbar_id" id="baseHelpbarId"}
                      </div>
                    </div>
                    <div class="control-group">
                      <label class="control-label" for="dialogId">{form_name name="message"}</label>
                      <div class="controls">
                        {form_input name="message" id="dialogMessage" style="width:500px;height:100px;"}
                      </div>
                    </div>
                </div>

				<br>
				<br>
				<div class="text-center">
				   <input type="submit" value="修正確認" class="btn btn-primary" />
			   </div>
			</form>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
{include file="admin/announce/message/_part/message_js.tpl"}
</body>
</html>
