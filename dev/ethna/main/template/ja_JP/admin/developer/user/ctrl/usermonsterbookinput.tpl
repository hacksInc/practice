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
			<h2>ユーザ制御　モンスター図鑑情報 登録</h2>

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
			<font color="#ff0000">{$app.err_msg}<br /></font>
			
			<form action="usermonsterbookconf" method="post">
				<table border="0">
					<tr>
						<td>モンスターID</td>
						<td size=1 style="width:50px;">入手</td>
					</tr>
					<tr>
						<td>
							<input type="text" name="monster_id">
						</td>
						<td>
							<select name="status" size=1 style="width:50px;">
								<option value="2">○</option>
								<option value="1">　</option>
							</select>
						</td>
					</tr>
				{*
					<tr>
						<td>遭遇日時</td>
						<td>
							<input type="text" name="date_met" value="" id="DATE_MET" class="jquery-ui-datetimepicker">
						</td>
					</tr>
					<tr>
						<td>入手日時</td>
						<td>
							<input type="text" name="date_got" value="" id="DATE_GOT" class="jquery-ui-datetimepicker">
						</td>
					</tr>
				*}
				</table>
				<input type="hidden" name="id" value="{$app.base.user_id}" />
				<input type="submit" value="登録確認" class="btn" />
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
