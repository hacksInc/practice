<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="KPI ガチャ回転情報 - サイコパス管理ページ"}

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
			<div class="page-header"><h2>ガチャ回転情報</h2></div>

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
					検索日：{form_input name="search_date_from" id="searchDateFrom" class="jquery-ui-datepicker"}&nbsp;～&nbsp;{form_input name="search_date_to" id="searchDateTo" class="jquery-ui-datepicker"}
				</div>
				<div class="search-part">
					集計項目：{form_input name="search_ua" id="searchUa"}<br />
				</div>
				<div class="button-wrap">
					<input type="button" value="表示する" class="btn btn-primary" id="btnSearchData">
					<input type="hidden" value="" id="searchFlg" name="search_flg">
					<input type="hidden" value="" id="start" name="start">

					<input type="button" value="CSVダウンロード" class="btn btn-primary" id="btnCsvDownloadDummy">
				</div>
			</form>

			<form action="/psychopass_game/admin/kpi/download" method="post" class="form-horizontal" id="formCsvDownload" style="display: none;">
				<input type="button" value="CSVダウンロード" class="btn btn-primary" id="btnCsvDownload">
				<input type="hidden" value="" id="downloadFileName" name="file_name">
			</form>

			</div>

		{if $app.search_flg == 1}

			{if count($app.list) == 0}
			<div class="alert alert-warning">
				<span>対象となる集計結果は存在しません</span>
			</div>
			{else}

			<div class="serach-result">
				<table border="0" class="table table-striped">
				<thead>
					<tr>
						<th>日付</th>
						<th>ガチャID</th>
						<th>ガチャ名</th>
						<th>回転数</th>
						<th>利用UU</th>
						<th>回転率</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$app.list key=k item=v}
					<tr {if $v.is_total == 1}class="total"{/if}>
						<td>{$v.date_tally|date_format:"%Y年%m月%d日"}</td>
						<td>{$v.gacha_id}</td>
						<td>{$v.name}</td>
						<td>{$v.count_drop}</td>
						<td>{$v.uu_play}</td>
						<td>{$v.rate|string_format:"%.3f"}%</td>
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
