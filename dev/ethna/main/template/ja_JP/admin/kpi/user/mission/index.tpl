<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="KPI ミッション攻略動向 - サイコパス管理ページ"}

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
</style>
{/literal}
<body>

{include file="admin/common/navbar.tpl"}

<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span10 main-contents">
			<div class="page-header"><h2>ミッション攻略動向</h2></div>

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
					検索日：{form_input name="search_date_from" id="searchDateFrom" class="jquery-ui-datetimepicker"}&nbsp;～&nbsp;{form_input name="search_date_to" id="searchDateTo" class="jquery-ui-datetimepicker"}
				</div>
				<div class="search-part">
					集計項目：{form_input name="search_ua" id="searchUa"}
				</div>
				<div class="search-part">
					エリアID：{form_input name="search_area_id" id="searchAreaId"}
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
						<th>ステージ名</th>
						<th>エリア名</th>
						<th>ミッション名</th>
						<th>スタートUU</th>
						<th>クリアUU</th>
						<th>クリア率</th>
						<th>ミッション<br />挑戦回数</th>
						<th>クリア回数</th>
						<th>総予備ドミネーター<br />使用回数</th>
						<th>予備ドミネータ<br />未使用クリア数</th>
						<th>予備ドミネータ<br />使用クリア数</th>
						<th>BESTクリア回数</th>
						<th>NORMALクリア回数</th>
						<th>FAIL回数</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$app.list key=k item=v}
					<tr>
						<td>{$v.stage_name}</td>
						<td>{$v.area_name}</td>
						<td>{$v.mission_name}</td>
						<td>{$v.uu_start}</td>
						<td>{$v.uu_clear}</td>
						<td>{$v._clear_rate}%</td>
						<td>{$v.count_challenge}</td>
						<td>{$v.count_clear}</td>
						<td>{$v.count_spare_domi_total}</td>
						<td>{$v.count_spare_domi_unused}</td>
						<td>{$v.count_spare_domi_used}</td>
						<td>{$v.count_best}</td>
						<td>{$v.count_normal}</td>
						<td>{$v.count_fail}</td>
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
