<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ制御 - サイコパス管理ページ"}
{literal}
<style>
#edit1 {
	max-width: 500px;
}
.control-group {
	clear: both;
}
.control-group label {
	float: left;
	text-align: right;
	font-weight: bold;
	width: 200px;
}
.control-group .controls {
	float: left;
}
.control-group .controls input {
	width: 200px;
}
.form-horizontal .controls {
	margin-left: 30px;
}
.control-group .controls input.success {
	background: #dff0d8;
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
			<p class="text-warning">※入力すると実際に更新が行なわれます。</p>

			<!-- Feedback message zone -->
			<div id="message"></div>

			<form class="form-horizontal" id="edit1">

				<div class="control-group">
					<label>ユーザID</label><div class="controls">{$app.user.pp_id}</div>
				</div>

				<div class="control-group">
					<label>ニックネーム</label><div class="controls"><input type="text" id="edit1-name" name="name" value="{$app.user.name}" {if !isset($app.user.name)}disabled=""{/if}></div>
				</div>
				<div class="control-group">
					<label>ログイン日時</label><div class="controls"><input data-table="ut_user_game" type="text" id="edit1-last_login" name="last_login" value="{$app.user.last_login}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>トータルログイン回数</label><div class="controls"><input data-table="ut_user_achievement_count" type="text" id="edit1-login" name="login" value="{$app.user.login}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>連続ログイン回数</label><div class="controls"><input data-table="ut_user_game" type="text" id="edit1-cont_login" name="cont_login" value="{$app.user.cont_login}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>当日ログイン回数</label><div class="controls"><input data-table="ut_user_game" type="text" id="edit1-today_login" name="today_login" value="{$app.user.today_login}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>チュートリアル進捗値</label><div class="controls"><input data-table="ut_user_tutorial" type="text" id="edit1-flag0" name="flag0" value="{$app.user.flag0}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>年齢認証<br />(-1:未チェック, 0～3)</label><div class="controls"><input type="text" id="edit1-age_verification" name="age_verification" value="{$app.user.age_verification}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>進行ID</label><div class="controls"><input data-table="ut_user_mission" type="text" id="edit1-mission_id" name="mission_id" value="{$app.user.mission_id}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>User-Agent種別<br />(1:iphone,2:android) </label><div class="controls"><input type="text" id="edit1-device_type" name="device_type" value="{$app.user.device_type}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>ゲームトランザクションID</label><div class="controls"><input data-table="ut_transaction" type="text" id="edit1-api_transaction_id" name="api_transaction_id" value="{$app.user.api_transaction_id}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>アクセス制限時間</label><div class="controls"><input type="text" id="edit1-ban_limit" name="ban_limit" value="{$app.user.ban_limit}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>OSバージョン</label><div class="controls"><input data-table="ut_user_device_info" type="text" id="edit1-content" name="content" value="{$app.user.os_type}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>機種名</label><div class="controls"><input data-table="ut_user_device_info" type="text" id="edit1-content" name="content" value="{$app.user.device_name}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>月間購入金額</label><div class="controls"><input type="text" id="edit1-ma_purchase" name="ma_purchase" value="{$app.user.ma_purchase}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>月間購入金額上限</label><div class="controls"><input type="text" id="edit1-ma_purchase_max" name="ma_purchase_max" value="{$app.user.ma_purchase_max}" disabled=""></div>
				</div>
				<div class="control-group">
					<label>ポータルポイント</label><div class="controls"><input data-table="ut_portal_user_base" type="text" id="edit1-point" name="point" value="{$app.user.point}" {if !isset($app.user.point)}disabled=""{/if}></div>
				</div>
				<div class="control-group">
					<label>犯罪係数</label><div class="controls"><input data-table="ut_user_game" type="text" id="edit1-crime_coef" name="crime_coef" value="{$app.user.crime_coef}" {if !isset($app.user.crime_coef)}disabled=""{/if}></div>
				</div>
				<div class="control-group">
					<label>身体係数</label><div class="controls"><input data-table="ut_user_game" type="text" id="edit1-body_coef" name="body_coef" value="{$app.user.body_coef}" {if !isset($app.user.body_coef)}disabled=""{/if}></div>
				</div>
				<div class="control-group">
					<label>知能係数</label><div class="controls"><input data-table="ut_user_game" type="text" id="edit1-intelli_coef" name="intelli_coef" value="{$app.user.intelli_coef}" {if !isset($app.user.intelli_coef)}disabled=""{/if}></div>
				</div>
				<div class="control-group">
					<label>心的係数</label><div class="controls"><input data-table="ut_user_game" type="text" id="edit1-mental_coef" name="mental_coef" value="{$app.user.mental_coef}" {if !isset($app.user.mental_coef)}disabled=""{/if}></div>
				</div>
				<div class="control-group">
					<label>臨時ストレスケア回数</label><div class="controls"><input data-table="ut_user_game" type="text" id="edit1-ex_stress_care" name="ex_stress_care" value="{$app.user.ex_stress_care}" {if !isset($app.user.ex_stress_care)}disabled=""{/if}></div>
				</div>
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

<!-- Modal -->
<div id="errModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
	<h3 id="myModalLabel">Error</h3>
	</div>
	<div class="modal-body">
	<p>エラーが発生しました。サーバにデータが反映されなかった可能性があります。</p>
	</div>
	<div class="modal-footer">
	<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	{*<button class="btn btn-primary">Save changes</button>*}
	</div>
</div>

{include file="admin/common/script.tpl"}
{literal}
<script>
	$(document).ready(function(){
		var userAssoc = {/literal}{$app_ne.user_json}{literal};

		Backbone.emulateHTTP = true;
		Backbone.emulateJSON = true;

		var User = Backbone.Model.extend({
			urlRoot: ''
		});

		var UserList = Backbone.Collection.extend({
			model: User
		});

		var users = new UserList();
		(function() {
			var attrs = userAssoc;
			attrs.id = userAssoc.pp_id;

			var user = new User(attrs);
			users.add(user);
		})();

		var UserView = Backbone.View.extend({
			el: '#edit1',

			events: {
				'change input': 'updateOnChange'
			},

			updateOnChange: function(e) {

				if (0 < $('#message .alert').length) {
					$('#message .alert').remove();
				}

				if (!window.confirm('データを更新しますか？')) {
					return;
				}

				var target = $(e.target);
				var name = $(e.target).attr('name');
				var value = $(e.target).val();

				var label = $.trim($(e.target).parent().parent().text());

				var table = $(e.target).data('table');
				if (!table) {
					table = 'ut_user_base';
				}

				var user = users.at(0);

				user.urlRoot = '/psychopass_game/admin/api/rest/' + table;

				user.save(name, value, {
					patch: true,
					success: function(model, resp) {
						$('#message').append('<div role="alert" class="alert alert-success"><strong>Success！</strong>' + label + 'を変更しました。</div>');
						target.addClass('success');
					},
					error: function(model, resp) {
						$('#errModal').modal();
					}
				});
			}
		});

		new UserView();
	});
</script>
{/literal}

</body>
</html>