<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="モンスター画像 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>モンスター画像</h2>
{*
			{a href="upload/input"}追加/更新{/a} モンスター画像の追加、更新を行う際は[追加/更新]を使用して下さい。<br>
*}
			{a href="create/input" class="btn"}追加{/a} モンスター画像の追加を行います。
			
			<table>
				{foreach from=$app.list item="row" key="id"}
				<tr style="border-top: 1px solid; border-left: 1px solid; border-right: 1px solid;">
					<td rowspan="2">{a href="update/input?id=`$id`"}修正{/a}</td>
					<td rowspan="5">
						{if $row.mtime.image}
							<a href="/admin/developer/assetbundle/monster/image?monster_id={$row.monster.monster_id}&dummy={$row.mtime.image}" target="_blank" class="hoverImg">
							<img width="64" height="64" src="/admin/developer/assetbundle/monster/image?monster_id={$row.monster.monster_id}&dummy={$row.mtime.image}">
							</a>
						{else}
							画像なし
						{/if}
					</td>
					<td>ID</td>
					<td>{$row.monster.monster_id}</th>
					<td>開始日</td>
					<td>{$row.start_date}</td>
				</tr>
				<tr style="border-left: 1px solid; border-right: 1px solid;">
					<td>モンスター名</td>
					<td>{$row.monster.name_ja}</td>
					<td>終了日</td>
					<td>{$row.end_date}</td>
				</tr>
				<tr style="border-left: 1px solid; border-right: 1px solid;">
					<td rowspan="3">
						{if $row.mtime.icon}
							<a href="/admin/developer/assetbundle/monster/image?type=icon&monster_id={$row.monster.monster_id}&dummy={$row.mtime.icon}" target="_blank" class="hoverImg">
							<img width="32" height="32" src="/admin/developer/assetbundle/monster/image?type=icon&monster_id={$row.monster.monster_id}&dummy={$row.mtime.icon}">
							</a>
						{/if}
					</td>
					<td>ディレクトリ</td>
					<td>{$row.dir}</td>
					<td>活性フラグ</td>
					<td>{$row.active_flg}</td>
				</tr>
				<tr style="border-left: 1px solid; border-right: 1px solid;">
					<td>ファイルバージョン</td>
					<td>{$row.version}</td>
					<td colspan="2">&nbsp;</td>
				</tr>
				<tr style="border-left: 1px solid; border-right: 1px solid; border-bottom: 1px solid;">
					<td>ファイル名</td>
					<td>{$row.file_name}</td>
					<td colspan="2">&nbsp;</td>
				</tr>
				{/foreach}

				<tr>
					<td colspan="6" class="text-center">{$app_ne.pager.all}</td>
				</tr>
				<tr>
					<td colspan="6" class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}</td>
				</tr>
			</table>
			
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{*
{literal}
<script>
	$(function(){
		$('a.hoverImg').popover({
			html: true,
			trigger: 'hover',
			content: function(){
				return '<img src="' + $(this).attr('href') + '" />';
			}
		});
    });
</script>
{/literal}
*}
<script src="/js/admin/common/hover-img.js"></script>
</body>
</html>