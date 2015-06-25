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
			<div class="page-header"><h2>ユーザデータ制御　メニュー</h2></div>

			<form action="listconf" method="post" class="form-horizontal">
			<table border="0" cellpadding="6">
				<tr>
					<td>ユーザID</td>
					<td>{$app.base.pp_id}</td>
					<td rowspan="3"></td>
					<td rowspan="9" valign="top">
						＞<a href="userbase?id={$app.base.pp_id}">ユーザ基本情報</a><br />
						＞<a href="useritem?id={$app.base.pp_id}">ユーザ所持アイテム確認</a><br />
						＞<a href="userphoto?id={$app.base.pp_id}">ユーザ所持フォト確認</a><br />
						＞<a href="userpresent?id={$app.base.pp_id}">ユーザプレゼントBOX確認</a><br />
						＞<a href="userachievement?id={$app.base.pp_id}">ユーザアチーブメント状況確認</a><br />
						＞<a href="usercharacter?id={$app.base.pp_id}">ユーザ所持キャラクター確認</a>
					</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td><input type="text" name="name" value="{$app.base.name}"></td>
				</tr>
				<tr>
					<td>ユーザ属性</td>
					<td>
					<select name="attr">
						<option value="10" {if $app.base.attr==10}selected{/if}>10:通常</option>
						<option value="21" {if $app.base.attr==21}selected{/if}>21:開発スタッフ</option>
						<option value="26" {if $app.base.attr==26}selected{/if}>26:外部協力会社</option>
					</select>
					</td>
				</tr>
				<tr>
					<td>ログイン禁止解除日時</td>
					<td><input type="text" name="ban_limit" value="{$app.base.ban_limit}" id="BAN_LIM" class="jquery-ui-datetimepicker"></td>
					<td rowspan="6" valign="top"><input type="button" value="永続禁止" onClick="C_Value()"></td>
				</tr>
				<tr>
					<td>最終アクセス日時</td>
					<td>{$app.base.last_login}</td>
				</tr>
				<tr>
					<td>登録日時</td>
					<td>{$app.base.date_created}</td>
				</tr>
				<tr>
					<td>OS</td>
					<td>{if $app.base.device_type==1}iOS{elseif $app.base.device_type==2}Android{/if}</td>
				</tr>
				<tr>
					<td>年齢認証</td>
					<td>{if $app.base.age_verification==0}20歳以上{elseif $app.base.age_verification==1}14歳未満{elseif $app.base.age_verification==2}18歳未満{elseif $app.base.age_verification==3}20歳未満{else}未チェック{/if}</td>
				</tr>
				<tr>
					<td>当月購入金額</td>
					<td align="right">{$app.base.ma_purchase}</td>
				</tr>
				<tr>
					<td align="right">
						<input type="hidden" name="id" value="{$app.base.pp_id}">
						<input type="submit" value="更新" class="btn" />
					</td>
					<td colspan=3>ユーザ属性、アクセス制御の変更を行う場合は<br />変更後、更新ボタンを押して反映を行って下さい。</td>
				</tr>
			</table>
				<div class="text-center">
				</div>
			</form>

		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
</body>
</html>
