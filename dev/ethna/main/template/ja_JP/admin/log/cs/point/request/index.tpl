<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ポイント管理アクセスログ - サイコパス管理ページ"}
<body>
{include file="admin/log/cs/_part/log_cs_css.tpl"}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
    {include file="admin/common/breadcrumb.tpl"}

    <div class="row-fluid">
        {include file="admin/log/cs/_part/log_menu.tpl"}
        {* include file="admin/common/sidebar.tpl" *}

        <div class="span9 main-contents">
            <h2>ポイント管理アクセスログ</h2>
            {if count($errors)}
            <ul>
              {foreach from=$errors item=error}
               <div style="color: #ff0000;">{$error}</div>
              {/foreach}
            </ul>
            {/if}
			
            {* 検索用入力エリア *}
			<h3>ユーザーIDによる検索</h3>
			<div style="border: 1px solid #aaaaaa; padding:10px;margin:10px;width:600px;">
			<form action="/admin/log/cs/point/request/list" method="post" class="form-horizontal">
				<div class="search-part">
					検索日：<input class="jquery-ui-datetimepicker" type="text" name="search_date_from" value="{$app.search_date_from_1}" /> ～ 
					<input class="jquery-ui-datetimepicker" type="text" name="search_date_to" value="{$app.search_date_to_1}" />
				</div>
				<div class="search-part">
					ユーザーID：{form_input name="search_user_id"}<br />
				</div>
				<div style="width:580px;">
					<div class="search-part-button">
						<input type="submit" value="検索する" class="btn btn-primary">
					</div>
				</div>
				<input type="hidden" name="search_type" value="{"Jm_LogdataViewPointManager::SEARCH_TYPE_USER_ID"|constant}">
			</form>
			</div>
			
			<h3>指定期間アクセスエラー検索</h3>
			<div style="border: 1px solid #aaaaaa; padding:10px;margin:10px;width:600px;">
			<form action="/admin/log/cs/point/request/list" method="post" class="form-horizontal">
				<div class="search-part">
					検索日：<input class="jquery-ui-datetimepicker" type="text" name="search_date_from" value="{$app.search_date_from_2}" /> ～ 
					<input class="jquery-ui-datetimepicker" type="text" name="search_date_to" value="{$app.search_date_to_2}" />
				</div>
				<div style="width:580px;">
					<div class="search-part-button">
						<input type="submit" value="検索する" class="btn btn-primary">
					</div>
				</div>
				<input type="hidden" name="search_type" value="{"Jm_LogdataViewPointManager::SEARCH_TYPE_STS_NG"|constant}">
			</form>
			</div>
        </div><!--/span-->
    </div><!--/row-->

    <hr>
    {include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
<div class="" id="dialogTransactionInfo">
</div>
{include file="admin/common/script.tpl" datepicker="jquery"}
</body>
</html>
