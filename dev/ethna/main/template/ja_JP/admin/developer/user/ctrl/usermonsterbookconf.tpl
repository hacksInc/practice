<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ制御 - サイコパス管理ページ"}

{literal}
<script type="text/javascript">
<!--
function C_Value(){
    if(document.getElementById){
        document.getElementById("BAN_LIM").value="2999-12-31 23:59:59"
    }
}
//-->
</script>
{/literal}

<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ユーザ制御　モンスター図鑑情報 登録確認</h2>

			<table border="0">
				<tr>
					<td>ユーザID</td>
					<td>{$app.base.user_id}</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td>{$app.base.name}</td>
				</tr>
			</table>
			
			<br />
			
			<form action="usermonsterbookreg" method="post">
				<table border="0">
					<tr>
						<td style="width:100px;">モンスターID</td>
						<td style="width:200px;">モンスター名</td>
						<td size=1 style="width:50px;">入手</td>
					</tr>
					<tr>
						<td>
							{$form.monster_id}
						</td>
						<td>
							{$app.monster_name}
						</td>
						<td>
							{if $form.status==2}○{else}　{/if}
						</td>
					</tr>
				</table>
				<input type="hidden" name="id" value="{$form.id}" />
				<input type="hidden" name="monster_id" value="{$form.monster_id}" />
				<input type="hidden" name="status" value="{$form.status}" />
				<input type="hidden" name="table" value="t_user_monster_book" />
				<input type="submit" value="登録" class="btn" />
			</form>
			
			<p>
				<br />
				{a href="usermonsterbook?id=`$form.id`&table=t_user_monster_book"}戻る{/a}
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
</body>
</html>
