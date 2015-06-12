<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.table_label` - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ユーザ制御　データ移行パスワード</h2>
			<table border="0" cellpadding="4">
				<tr>
					<td>{$app.label.user_id}</td>
					<td>{$app.base.user_id}</td>
				</tr>
				<tr>
					<td>{$app.label.name}</td>
					<td>{$app.base.name}</td>
				</tr>
                <tr>
                    <td>{$app.label.account}</td>
                    <td>{$app.base.account}</td>
                </tr>
			</table>
                
			<h3>再発行</h3>
            再発行しました。<br>
            <br>
            新しいデータ移行パスワード（引き継ぎパスワード）&nbsp;&nbsp;{$app.new_dmpw}<br>
            <br>
            <small>
                <i class="icon-warning-sign"></i>
                <span class="text-warning">
                    新しいデータ移行パスワード（引き継ぎパスワード）は、再表示できません。<br>
                    &nbsp;&nbsp;&nbsp;このページから遷移する前に、カスタマーサポート等の業務を終了させて下さい。
                </span>
            </small>
            
			<p>
				<br />
				{a href="../../list?by=id&id=`$form.id`"}戻る{/a}
			</p>
            
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
