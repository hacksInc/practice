<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ制御 - サイコパス管理ページ"}
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
            
            <br />
			<h3>再発行</h3>
            <p>
                <small>
                    データ移行パスワード（引き継ぎパスワード）を再発行します。<br />
                    <span class="text-warning">
                        <i class="icon-warning-sign"></i>
                        現在のデータ移行パスワード（引き継ぎパスワード）は使用不可になります。
                    </span>
                </small>
            </p>
            
            <br />
			<form action="exec" method="POST">
				本当によろしいですか？<br />
				<br />
				<input id="agree" type="checkbox" name="agree" value="1">はい、私は決して後悔いたしません<br />
				<br />
				確認パスワード<br />
				<input type="password" name="confpass" value="" autocomplete="off" /><br />
				<input type="submit" value="実行" class="btn conf-btn">
				<input type="hidden" name="id" value="{$form.id}">
				<input type="hidden" name="account" value="{$app.base.account}">
			</form>
			
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
{literal}
<script>
	$(function(){
		$('input.conf-btn').click(function() {
			return window.confirm('再発行してしまいますが本当によろしいですか？');
		});
	});
</script>
{/literal}
</body>
</html>