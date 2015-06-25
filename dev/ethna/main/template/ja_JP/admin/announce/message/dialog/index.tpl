<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ダイアログメッセージ - サイコパス管理ページ"}
<body>
{literal}
<style type="text/css">
	.news-content-status-waiting {
		font-weight: bold;
	}
	.news-content-status-flag {
		font-weight: bold;
		color: blue;
	}
	.news-content-status-active {
		font-weight: bold;
		color: red;
	}
    .dialog-content-box {
        width: 100%;
        display: -webkit-box;　/* Safari,Google Chrome用 */
        display: -moz-box;　/* Firefox用 */
    }
</style>
{/literal}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ダイアログメッセージ一覧</h2>
            <div class="dialog-content-box" style="margin-bottom:5px;">
                <div>
                <form action="create/input" method="post">
                    <input type="submit" value="追加" class="btn btn-primary"> 新規ダイアログメッセージを追加します。
                </form>
                </div>
                <div style="margin-left:360px;">
                <form action="download" method="post">
                    <input type="submit" value="JSONデータ取得" class="btn btn-primary"> 
                </form>
                </div>
			</div>
			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px;">
					<div class="dialog-content-box row-fluid">
						<div style="width: 650px;">
                            <div class="row-fluid" style="margin: 10px;">
                                <dl class="dl-horizontal" style="margin: 3px;">
                                    <dt style="width:60px;">{form_name name="dialog_id"}</dt>
                                    <dd style="margin-left:80px;">{$row.dialog_id}</dd>
                                </dl>
                                <dl class="dl-horizontal" style="margin: 3px;">
                                    <dt style="width:60px;">{form_name name="dialog_type"}</dt>
                                    <dd style="margin-left:80px;">{$row.dialog_type}</dd>
                                </dl>
                                <dl class="dl-horizontal" style="margin: 3px;">
                                    <dt style="width:60px;">{form_name name="use_name"}</dt>
                                    <dd style="margin-left:80px;">{$row.use_name}</dd>
                                </dl>
                            </div>
							<div class="row-fluid" style="margin:10px;">
									{$row.message}
							</div>
						</div>
                        <div class="row-fluid">
                            <div class="dialog-content-box" style="width: 200px;">
                                <div style="margin: 5px;">
                                    <form action="update/input" method="post">
                                        <input type="hidden" name="dialog_id" value="{$row.dialog_id}">
                                        <input type="hidden" name="btn_action" value="update">
                                        <input type="submit" value="修正" class="btn btn-info">
                                    </form>
                                </div>
                                <div style="margin: 5px;">
                                    <input type="button" value="削除" class="btn btn-danger" id="btnDeleteMessage{$row.dialog_id}" name="btn_delete_message{$row.dialog_id}">
                                </div>
                                <div style="margin: 5px;">
                                    <form action="create/input" method="post">
                                        <input type="hidden" name="dialog_id" value="{$row.dialog_id}">
                                        <input type="hidden" name="btn_action" value="copy">
                                        <input type="submit" value="複製" class="btn">
                                    </form>
                                </div>
                            </div>
                            <div style="width: 200px;">
                                <div style="margin:10px;">
                                    {form_name name="date_created"}:{$row.date_created}
                                </div>
                                <div style="margin:10px;">
                                    {form_name name="date_modified"}:{$row.date_modified}
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			{/foreach}
			<div class="row-fluid" style="border-top: solid 1px;">
				&nbsp;
			</div>

			<div class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}&nbsp;</div>
			
        </div><!--/span-->
	</div><!--/row-->

<div id="deleteConfirmDialog">
    削除します。よろしいでしょうか？</ br>
	<form action="delete/exec" method="post" id="formDeleteMessageDialog">
        <input type="hidden" name="dialog_id" value="" id="deleteDialogId">
	</form>
</div>

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{literal}
<script>

    $("input[name^='btn_delete_message']").click(function(){
      var name = $(this).attr('name'),
          dialog_id = "";
      dialog_id = name.replace('btn_delete_message', '');
      $("#deleteDialogId").val(dialog_id);
      $('#deleteConfirmDialog').dialog('open');
    });

    $('#deleteConfirmDialog').dialog({
      autoOpen: false,
      title: 'ダイアログメッセージ削除確認',
      width: "300px",
      closeOnEscape: false,
      modal: true,
/*      position: {
          my: "center",
          at: "center",
          of: ".main-contents"
      },*/
      buttons: {
        "削除する": function(){
          $(this).dialog('close');
          $("#formDeleteMessageDialog").submit();
        },
        "キャンセル": function(){
          $(this).dialog('close');
        }
      },
    });

</script>
{/literal}
</body>
</html>
