<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ制御 - サイコパス管理ページ"}
{literal}
<style>
.table.table-striped td {
	vertical-align: middle;
}
.table.table-striped td input {
	margin-bottom: 0;
}
.table.table-striped td input.success {
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

		<div class="span6">
			<div class="page-header"><h2>ユーザ所持アイテム確認</h2></div>

			<table cellpadding="6">
				<tr><th align="left">ユーザID</th><td>{$app.base.pp_id}</td></tr>
				<tr><th align="left">ニックネーム</th><td>{$app.base.name}</td></tr>
			</table>

			<h3>所持アイテム一覧</h3>
			<p class="text-warning">※入力すると実際に更新が行なわれます。</p>

			<!-- Feedback message zone -->
			<div id="message"></div>

			<table border="0" class="table table-striped" id="edit1">
			<thead>
				<tr>
					<th><strong>No</strong></th>
					<th><strong>アイテムID</strong></th>
					<th><strong>アイテム名</strong></th>
					<th><strong>所持数</strong></th>
					<th><strong>データ作成日時</strong></th>
					<th><strong>データ更新日時</strong></th>
				</tr>
			</thead>
			<tbody>
				{foreach from=$app.item key=k item=v}
				<tr>
					<td>{$k+1}</td>
					<td>{$v.item_id}</td>
					<td>{$v.name}</td>
					<td><input type="text" data-key="{$k}" data-no="{$k+1}" id="edit1-num-{$k+1}" name="num" value="{$v.num}"></td>
					<td>{$v.date_created}</td>
					<td class="date_modified">{$v.date_modified}</td>
				</tr>
				{/foreach}
			</tbody>
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
	</div>
</div>

{include file="admin/common/script.tpl"}
{literal}
<script>
	$(document).ready(function(){
		var userAssoc = {/literal}{$app_ne.item_json}{literal};

		Backbone.emulateHTTP = true;
		Backbone.emulateJSON = true;

		var User = Backbone.Model.extend({
			urlRoot: ''
		});

		var UserList = Backbone.Collection.extend({
			model: User
		});

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

				var table = $(e.target).data('table');
				if (!table) {
					table = 'ut_user_item';
				}

				var key = $(e.target).data('key');

				var attrs = userAssoc[key];
				attrs.id = attrs.pp_id + ',' + attrs.item_id;

				var message = '<div role="alert" class="alert alert-success"><strong>Success！</strong>No.' + $(e.target).data('no') + 'の所持数を変更しました。</div>';

				var user = new User(attrs);

				user.urlRoot = '/psychopass_game/admin/api/rest/' + table;

				user.save(name, value, {
					patch: true,
					success: function(model, resp) {

						var option = {};
						option.cache = false;
						option.ifModified = false;
						option.url = '/psychopass_game/admin/developer/user/api';
						option.data = {
							table: table,
							id: attrs.id
						};

						$.ajax(option).done(function(data) {

							var _data = $.parseJSON(data);
							target.parent().parent().find('.date_modified').html(_data.date_modified);

							target.addClass('success');

							$('#message').append(message);

						}).fail(function(data) {
							$('#message').append(message);
						});
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
