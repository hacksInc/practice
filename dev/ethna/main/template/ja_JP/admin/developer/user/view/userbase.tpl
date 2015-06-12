<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ閲覧 - サイコパス管理ページ"}
{literal}
<style>
table.data {
	max-width: 600px;
}
table.data th {
	width: 50%;
	text-align: right;
	padding: 7px 30px;
	max-width: 300px;
}
table.data th span {
	font-size: 80%;
	color: #666;
}
table.data td {
	width: 50%;
	padding: 0 15px;
	text-align: left;
	max-width: 300px;
}
</style>
{/literal}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<div class="page-header"><h2>ユーザ基本情報</h2></div>

			<!-- Feedback message zone -->
			<div id="message"></div>

		    <form class="form-horizontal" id="edit1">
		    	<table border="0" cellpadding="6" class="data">
					<tr><th>ユーザID</th><td>{$app.user.pp_id}</td></tr>
					<tr><th>ニックネーム</th><td>{$app.user.name}</td></tr>
					<tr><th>ログイン日時</th><td>{$app.user.last_login}</td></tr>
					<tr><th>トータルログイン回数</th><td>{$app.user.login}</td></tr>
					<tr><th>連続ログイン回数</th><td>{$app.user.cont_login}</td></tr>
					<tr><th>当日ログイン回数</th><td>{$app.user.today_login}</td></tr>
					<tr><th>チュートリアル進捗値</th><td>{$app.user.flag0}</td></tr>
					<tr><th>年齢認証<br /><span>(-1:未チェック, 0～3)</span></th><td>{$app.user.age_verification}</td></tr>
					<tr><th>進行ID</th><td>{$app.user.mission_id}</td></tr>
					<tr><th>User-Agent種別</th><td>{if $app.user.device_type==1}iOS{elseif $app.user.device_type==2}Android{/if}</td></tr>
					<tr><th>ゲームトランザクションID</th><td>{$app.user.api_transaction_id}</td></tr>
					<tr><th>データ移行アカウント</th><td>{$app.user.migrate_id}</td></tr>
					<tr><th>アクセス制限時間</th><td>{$app.user.ban_limit}</td></tr>
					<tr><th>OSバージョン</th><td>{$app.user.os_type}</td></tr>
					<tr><th>機種名</th><td>{$app.user.device_name}</td></tr>
					<tr><th>月間購入金額</th><td>{$app.user.ma_purchase}</td></tr>
					<tr><th>月間購入金額上限</th><td>{$app.user.ma_purchase_max}</td></tr>
					<tr><th>ポータルポイント</th><td>{$app.user.point}</td></tr>
					<tr><th>犯罪係数</th><td>{$app.user.crime_coef}</td></tr>
					<tr><th>身体係数</th><td>{$app.user.body_coef}</td></tr>
					<tr><th>知能係数</th><td>{$app.user.intelli_coef}</td></tr>
					<tr><th>心的係数</th><td>{$app.user.mental_coef}</td></tr>
					<tr><th>臨時ストレスケア回数</th><td>{$app.user.ex_stress_care}</td></tr>
				</table>
		    </form>

			<p>
				<br />
				{a href="list?by=id&id=`$form.id`"}戻る{/a}
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->

</body>
</html>