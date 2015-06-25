<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ランキング - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>ランキングマスター&nbsp;登録確認</h2>

			<form action="exec" method="post" class="form-horizontal">
				<!-- 先祖代々伝わる秘伝の教え -->
				<input type="hidden" name="ranking_id" value="{$app.ranking_id}">
				<input type="hidden" name="title" value="{$app.title}">
				<input type="hidden" name="subtitle" value="{$app.subtitle}">
				<input type="hidden" name="target_type" value="{$app.target_type}">
				<input type="hidden" name="targets" value="{$app.targets}">
				<input type="hidden" name="processing_type" value="{$app.processing_type}">
				<input type="hidden" name="clear_target_dungeon_rank3" value="{$app.clear_target_dungeon_rank3}">
				<input type="hidden" name="clear_target_dungeon_rank4" value="{$app.clear_target_dungeon_rank4}">
				<input type="hidden" name="threshold" value="{$app.threshold}">
				<input type="hidden" name="view_higher" value="{$app.view_higher}">
				<input type="hidden" name="view_lower" value="{$app.view_lower}">
				<input type="hidden" name="view_ranking_top" value="{$app.view_ranking_top}">
				<input type="hidden" name="date_start" value="{$app.date_start}">
				<input type="hidden" name="date_end" value="{$app.date_end}">
				<input type="hidden" name="banner_url" value="{$app.banner_url}">
				<input type="hidden" name="url" value="{$app.url}">

				<!-- 前画面で入力した内容の表示 -->
			    <div class="control-group">
				    <label class="control-label">{form_name name="ranking_id"}</label>
				    <div class="controls">
						{$app.ranking_id}
				    </div>
			    </div>
				<div class="control-group">
				    <label class="control-label">{form_name name="title"}</label>
				    <div class="controls">
						{$app.title}
				    </div>
			    </div>
				<div class="control-group">
				    <label class="control-label">{form_name name="subtitle"}</label>
				    <div class="controls">
						{$app.subtitle}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="target_type"}</label>
				    <div class="controls">
						{$app.target_type_str}
				    </div>
			    </div>
				{if $app.target_type === '1'}
			    <div class="control-group">
				    <label class="control-label">{form_name name="targets"}</label>
				    <div class="controls">
						{$app.targets}
				    </div>
			    </div>
				{/if}

				{if $app.target_type === '1'}
			    <div class="control-group">
				    <label class="control-label">{form_name name="processing_type"}</label>
				    <div class="controls">
						{$app.processing_type}
				    </div>
			    </div>
				{/if}

				{if $app.target_type === '3'}
			    <div class="control-group">
				    <label class="control-label">{form_name name="clear_target_dungeon_rank3"}</label>
				    <div class="controls">
						{$app.clear_target_dungeon_rank3}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="clear_target_dungeon_rank4"}</label>
				    <div class="controls">
						{$app.clear_target_dungeon_rank4}
				    </div>
			    </div>
				{/if}

			    <div class="control-group">
				    <label class="control-label">{form_name name="threshold"}</label>
				    <div class="controls">
						{$app.threshold}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="view_ranking_top"}</label>
				    <div class="controls">
						{$app.view_ranking_top}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="view_higher"}</label>
				    <div class="controls">
						{$app.view_higher}
					</div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="view_lower"}</label>
				    <div class="controls">
						{$app.view_lower}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="date_start"}</label>
				    <div class="controls">
						{$app.date_start}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="date_end"}</label>
				    <div class="controls">
						{$app.date_end}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="banner_url"}</label>
				    <div class="controls">
						{$app.banner_url}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label">{form_name name="url"}</label>
				    <div class="controls">
						{$app.url}
				    </div>
			    </div>

				<!-- 登録するよポチっとな -->
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="登録" class="btn" />
				    </div>
			    </div>
			</form>
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
</body>
</html>