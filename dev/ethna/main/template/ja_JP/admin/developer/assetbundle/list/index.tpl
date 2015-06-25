<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="最新アセットバンドル - サイコパス管理ページ"}

{include file="admin/log/cs/_part/log_cs_css.tpl"}

{literal}
<style>
.button-wrap {
	text-align: center;
	margin-top: 20px;
}
.button-wrap .btn {
	margin-left: 25px;
}
.search-box {
	width: 620px;
	border: 1px solid #aaa;
	padding: 10px;
	margin-bottom: 20px;
}
.search-error {
	margin: 0;
	list-style: none;
	padding-left: 10px;
}
	.search-error li {
		color: #ff0000;
	}

.table-striped tbody > tr.total:nth-child(2n+1) > td, tr.total {
	background: #F9F0C5;
}
</style>
{/literal}
<body>

{include file="admin/common/navbar.tpl"}

<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span10 main-contents">
			<div class="page-header"><h2>最新アセットバンドル</h2></div>

			<div class="search-box">
			{if count($errors)}
			<ul class="search-error">
			{foreach from=$errors item=error}
			<li>{$error}</li>
			{/foreach}
			</ul>
			{/if}

			<form action="index" method="post" class="form-horizontal" id="formLogSearch">
				<div class="search-part">
					ファイル名：{form_input name="search_file_name" id="searchFileName"}
				</div>
				<div class="button-wrap">
					<input type="button" value="表示する" class="btn btn-primary" id="btnSearchData">
					<input type="hidden" value="" id="searchFlg" name="search_flg">
					<input type="hidden" value="" id="start" name="start">

					<input type="button" value="CSVダウンロード" class="btn btn-primary" id="btnCsvDownloadDummy">
				</div>
			</form>

			<form action="/psychopass_game/admin/developer/download" method="post" class="form-horizontal" id="formCsvDownload" style="display: none;">
				<input type="button" value="CSVダウンロード" class="btn btn-primary" id="btnCsvDownload">
				<input type="hidden" value="" id="downloadFileName" name="file_name">
			</form>

			</div>

		{if $app.search_flg == 1}

			{if count($app.list) == 0}
			<div class="alert alert-warning">
				<span>データが存在しません</span>
			</div>
			{else}

			<div class="serach-result">
				<table border="0" class="table table-striped table-hover">
				<thead>
					<tr>
						<th>管理ID</th>
						<th>ファイルタイプ</th>
						<th>ディレクトリ</th>
						<th>ファイル名</th>
						<th>拡張子</th>
						<th>バージョン</th>
						<th>開始日時</th>
						<th>終了日時</th>
						<th>活性フラグ</th>
						<th>作成日時</th>
						<th>更新日時</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$app.list key=k item=v}
					<tr {if $v.is_total == 1}class="total"{/if}>
						<td>{$v.id}</td>
						<td>{$v.file_type}</td>
						<td>{$v.dir}</td>
						<td>{$v.file_name}</td>
						<td>{$v.extension}</td>
						<td>{$v.version}</td>
						<td>{$v.date_start}</td>
						<td>{$v.date_end}</td>
						<td>{$v.active_flg}</td>
						<td>{$v.date_created}</td>
						<td>{$v.date_modified}</td>
					</tr>
					{/foreach}
				</tbody>
				</table>
			</div>

			{/if}

		{/if}
		</div><!--/span-->

	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}

</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
{include file="admin/log/cs/_part/log_cs_js.tpl"}
{include file="admin/_part/csvfile_download_js.tpl"}

{literal}
<script type="text/javascript">
$(function() {
	$('#btnCsvDownloadDummy').click(function() {
		$('#btnCsvDownload').click();
	});
});

</script>
{/literal}

</body>
</html>
