<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ワールドマップデータ - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ワールドマップデータ</h2>
			{a href="create/input" class="btn"}追加{/a} ワールドマップデータの追加を行います。
			
			{foreach from=$app.list item="row" key="id" name="loop1"}
				<div class="row-fluid" style="{if $smarty.foreach.loop1.first}margin-top: 5px; {/if}padding: 5px; border-top: solid 1px; border-left: solid 1px; border-right: solid 1px; {if $smarty.foreach.loop1.last}border-bottom: solid 1px; margin-bottom: 5px;{/if}">
			        <div class="span1">
						{a href="update/input?id=`$id`" class="btn"}修正{/a}
					</div>
					
					<div class="span11">
						<div class="row-fluid">
					        <div class="span3">ディレクトリ</div>
					        <div class="span3">{$row.dir}</div>
					        <div class="span3">開始日</div>
					        <div class="span3">{$row.start_date}</div>
						</div>
						<div class="row-fluid">
					        <div class="span3">ファイルバージョン</div>
					        <div class="span3">{$row.version}</div>
					        <div class="span3">終了日</div>
					        <div class="span3">{$row.end_date}</div>
						</div>
						<div class="row-fluid">
					        <div class="span3">ファイル名</div>
					        <div class="span3">{$row.file_name}</div>
					        <div class="span3">活性フラグ</div>
					        <div class="span3">{$row.active_flg}</div>
						</div>
						<div class="row-fluid">
							<div class="text-right">
								{a href="delete/exec?id=`$id`" class="btn delete-btn"}削除{/a}
							</div>
						</div>
					</div>
				</div>
			{/foreach}

			<div class="text-center">{$app_ne.pager.all}</div>
			<div class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}</div>
			
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{literal}
<script>
	$(function(){
		$('a.delete-btn').click(function() {
			return window.confirm('削除します。よろしいですか？');
		});
	});
</script>
{/literal}
</body>
</html>
