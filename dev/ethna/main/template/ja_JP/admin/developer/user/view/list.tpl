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
			<div class="page-header"><h2>ユーザデータ閲覧　メニュー</h2></div>

			<table border="0" cellpadding="6">
				<tr>
					<td>ユーザID</td>
					<td>{$app.base.pp_id}</td>
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
					<td>{$app.base.name}</td>
				</tr>
				<tr>
					<td>ユーザ属性</td>
					<td>{if $app.base.attr==10}10:通常{elseif $app.base.attr==21}21:開発スタッフ{elseif $app.base.attr==26}26:外部協力会社{/if}</td>
				</tr>
				<tr>
					<td>ログイン禁止解除日時</td>
					<td>{$app.base.ban_limit}</td>
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
			</table>

		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>
