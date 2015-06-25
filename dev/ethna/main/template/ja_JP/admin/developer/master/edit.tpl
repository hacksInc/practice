<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="`$app.table_label` - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">

		<div class="span9">
			<h2>{$app.table_label}</h2>
			<h3>編集</h3>
			※変更ではセルを編集する都度、サーバーに反映されます。（通信が発生します）<br />

			<!-- Feedback message zone -->
			<div id="message"></div>

			<h4>追加</h4>
			<div id="addcontent"></div>

			<h4>変更</h4>
			<div id="tablecontent"></div>
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
	<p>通信エラーが発生しました。サーバにデータが反映されなかった可能性があります。</p>
	</div>
	<div class="modal-footer">
	<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
	{*<button class="btn btn-primary">Save changes</button>*}
	</div>
</div>

{include file="admin/common/script.tpl"}
{literal}
<script>
	window.onload = function() {
		var metadata = {/literal}{$app_ne.metadata}{literal};
		var data = {/literal}{$app_ne.data}{literal};
		var metadataAdd = {/literal}{$app_ne.metadata_add}{literal};
		var dataAdd = {/literal}{$app_ne.data_add}{literal};
		var primaryKeyPositions = {/literal}{$app_ne.primary_key_positions}{literal};
		var tableName = "{/literal}{$app.table}{literal}";

		Backbone.emulateHTTP = true;
		Backbone.emulateJSON = true;

		var MyModel = Backbone.Model.extend({
			urlRoot: '/psychopass_game/admin/api/rest/' + tableName
		});

		editableGrid = new EditableGrid("DemoGridJsData", {
			// called when some value has been modified: we display a message
			modelChanged: function(rowIdx, colIdx, oldValue, newValue, row) {
				console.log(rowIdx);
				console.log(colIdx);
				console.log(oldValue);
				console.log(newValue);
				console.log($(row).find("td:eq(0)").html());
				console.log(metadata[colIdx].name);

				var id = row.id.substr(this.name.length + 1);
				var attrs = {
					id: id
				};

				var myModel = new MyModel(attrs);
				myModel.save(metadata[colIdx].name, newValue, {
					patch: true,
					success: function(model, resp) {
						console.trace();
						console.log('success');
						$("#message").text("updated. id:" + id + " name:" + metadata[colIdx].name + " newValue:" + newValue);
					},
					error: function(model, resp) {
						console.trace();
						console.log('error');
						$('#errModal').modal();
					}
				});
			},

			enableSort: false
		});

		editableGrid.myRemove = function(rowIndex) {
			var row = this.getRow(rowIndex);
			console.log('deleting ' + row.id);

			var id = row.id.substr(this.name.length + 1);
			var attrs = {
				id: id
			};

			var myModel = new MyModel(attrs);
			myModel.destroy({
				wait: true,
				success: function(model, resp) {
					console.trace();
					console.log('deleted (success)');
					$("#message").text("deleted. id:" + id);
				},
				error: function(model, resp) {
					console.trace();
					console.log('deleted (error)');
					$('#errModal').modal();
				}
			});

			this.remove(rowIndex);
		};

		editableGrid.load({"metadata": metadata, "data": data});
		editableGrid.setCellRenderer("action", new CellRenderer({render: function(cell, value) {
			cell.innerHTML = "<button class='btn btn-mini' onclick=\"if (confirm('削除しますか？')) editableGrid.myRemove(" + cell.rowIndex + ");\" style=\"cursor:pointer\">" +
							 "削除</button>";
		}}));
		editableGrid.renderGrid("tablecontent", "grid1");

		// 新規追加用
		editableGridAdd = new EditableGrid("DemoGridJsDataAdd", {
			enableSort: false
		});

		editableGridAdd.myPost = function(rowIndex) {
			var row = this.getRow(rowIndex);
			console.log('inserting ' + row.id);

			var getPrimaryKey = function(row) {
				var key = '';
				var len = primaryKeyPositions.length;
				for (var i = 0; i < len; i++) {
					if (key.length > 0) {
						key += ',';
					}

					key += $(row).find('td:eq(' + i + ')').html();
				}

				return key;
			};

			var id = getPrimaryKey(row);
			var attrs = this.getRowValues(rowIndex);

			var myModel = new MyModel(attrs);
			myModel.save(null, {
				success: function(model, resp) {
					console.trace();
					console.log('success');
					console.log('rowIndex:' + rowIndex);
//					location.reload();
					editableGrid.insert(0, id, attrs);
				},
				error: function(model, resp) {
					console.trace();
					console.log('error');
					$('#errModal').modal();
				}
			});

		};

		editableGridAdd.load({"metadata": metadataAdd, "data": dataAdd});
		editableGridAdd.setCellRenderer("action", new CellRenderer({render: function(cell, value) {
			cell.innerHTML = "<button class='btn btn-mini' onclick=\"if (confirm('追加しますか？')) editableGridAdd.myPost(" + cell.rowIndex + ");\" style=\"cursor:pointer\">" +
							 "追加</button>";
		}}));
		editableGridAdd.renderGrid("addcontent", "grid1");
	}
</script>
{/literal}
</body>
</html>
