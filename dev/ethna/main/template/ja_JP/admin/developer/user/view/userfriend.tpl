<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ閲覧 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ユーザデータ　フレンド情報</h2>

			<table border="0" cellpadding="4">
				<tr>
					<td>ユーザID</td>
					<td>{$app.base.user_id}</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td>{$app.base.name}</td>
				</tr>
				<tr>
					<td>フレンド数</td>
					<td>{$app.friend_cnt.1+$app.friend_cnt.2}／{$app.base.friend_max+$app.base.friend_expand}</td>
				</tr>
				<tr>
					<td>申請受信数</td>
					<td>{$app.friend_cnt.3}</td>
				</tr>
				<tr>
					<td>ブロック数</td>
					<td>{$app.friend_cnt.4}</td>
				</tr>
			</table>
			
			<br />
			<table border="1" rules="none">
				<tr>
					<td colspan="4">フレンド承認済　一覧</td>
				</tr>
				<tr>
					<td align="center" width="50">　No.　</td>
					<td align="center" width="100">　フレンドID　</td>
					<td align="center" width="200">　フレンド名　</td>
					<td align="center" width="200">　連れて行けるようになる時間　</td>
				</tr>
				{foreach from=$app.friend_list.2 key=k item=v}
				<tr>
					<td align="center">{$k+1}</td>
					<td align="center">{$v.friend_id}</td>
					<td align="center">{$v.name}</td>
					<td align="center">{$v.date_bring}</td>
				</tr>
				{/foreach}
			</table>
			
			<br />
			<table border="1" rules="none">
				<tr>
					<td colspan="4">フレンド申請中　一覧</td>
				</tr>
				<tr>
					<td align="center" width="50">　No.　</td>
					<td align="center" width="100">　フレンドID　</td>
					<td align="center" width="200">　フレンド名　</td>
					<td align="center" width="200"></td>
				</tr>
				{foreach from=$app.friend_list.1 key=k item=v}
				<tr>
					<td align="center">{$k+1}</td>
					<td align="center">{$v.friend_id}</td>
					<td align="center">{$v.name}</td>
					<td align="center"></td>
				</tr>
				{/foreach}
			</table>
			
			<br />
			<table border="1" rules="none">
				<tr>
					<td colspan="4">フレンド申請受信　一覧</td>
				</tr>
				<tr>
					<td align="center" width="50">　No.　</td>
					<td align="center" width="100">　フレンドID　</td>
					<td align="center" width="200">　フレンド名　</td>
					<td align="center" width="200"></td>
				</tr>
				{foreach from=$app.friend_list.3 key=k item=v}
				<tr>
					<td align="center">{$k+1}</td>
					<td align="center">{$v.friend_id}</td>
					<td align="center">{$v.name}</td>
					<td align="center"></td>
				</tr>
				{/foreach}
			</table>
			
			<br />
			<table border="1" rules="none">
				<tr>
					<td colspan="4">ブロック　一覧</td>
				</tr>
				<tr>
					<td align="center" width="50">　No.　</td>
					<td align="center" width="100">　フレンドID　</td>
					<td align="center" width="200">　フレンド名　</td>
					<td align="center" width="200"></td>
				</tr>
				{foreach from=$app.friend_list.4 key=k item=v}
				<tr>
					<td align="center">{$k+1}</td>
					<td align="center">{$v.friend_id}</td>
					<td align="center">{$v.name}</td>
					<td align="center"></td>
				</tr>
				{/foreach}
			</table>
			
			<p>
				<br />
				{a href="list?by=id&id=`$form.id`"}戻る{/a}
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
