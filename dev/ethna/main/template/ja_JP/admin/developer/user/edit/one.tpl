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
			<h2>{$app.table_label}</h2>
			<p class="text-warning">※入力すると実際に更新が行なわれます。</p>

			<h3>編集</h3>

			<!-- Feedback message zone -->
			<div id="message"></div>
			
		    <form class="form-horizontal" id="edit1">
				{foreach from=$app.label key="key" item="label"}
					{if $key == "user_id"}
					    <div class="control-group">
						    <label class="control-label">{$label}</label>
						    <div class="controls">
								{$app.user.$key}
						    </div>
					    </div>
					{else}
					    <div class="control-group">
						    <label class="control-label" for="edit1-{$key}">{$label}</label>
						    <div class="controls">
							    <input type="text" id="edit1-{$key}" name="{$key}" value="{$app.user.$key}">
						    </div>
					    </div>
					{/if}
				{/foreach}
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
		var tableName = "{/literal}{$app.table}{literal}";

		Backbone.emulateHTTP = true;
		Backbone.emulateJSON = true;

		var User = Backbone.Model.extend({
			urlRoot: '/admin/api/rest/' + tableName
		});
			
		var UserList = Backbone.Collection.extend({
			model: User
		});
		
		var users = new UserList();
		(function() {
			var attrs = userAssoc;
			attrs.id = userAssoc.user_id;
		
			var user = new User(attrs);
			users.add(user);
		})();
			
		var UserView = Backbone.View.extend({
			el: '#edit1',

			events: {
				'change input': 'updateOnChange'
			},

//			initialize: function() {
//				console.log('initialize');
////				this.listenTo(users, 'add', this.render);
////				this.listenTo(this.model, 'add', this.render);
//				
//				this.model = users.at(0);
//				this.listenTo(this.model, 'change', this.render);
//			},
//
//			render: function() {
//				//↓なぜかこのログが出ない。listenToしてるはずだが。
//				console.log('render');
//				return this;
//			},
				
			updateOnChange: function(e) {
				console.log('updateOnChange');
					
				var name = $(e.target).attr('name');
				var value = $(e.target).val();
				console.log(name + ':' + value);

				// ↓モデルとビューの紐付けは、このやり方ではだめな気がする。
				var user = users.at(0);

				user.save(name, value, {
					patch: true,
					success: function(model, resp) {
						console.log('success.');
					},
					error: function(model, resp) {
						console.log('error.');
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