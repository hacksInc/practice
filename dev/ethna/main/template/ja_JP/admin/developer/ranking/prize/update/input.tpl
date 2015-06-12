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
			<h2>賞品配布設定&nbsp;更新</h2>
			<h4>&nbsp;『{$app.title}{if empty($app.subtitle) === false}&nbsp;【{$app.subtitle}】{/if}』</h4>
			<br>

			<form action="confirm" method="post" class="form-horizontal">

				<!-- 先祖代々伝わる秘伝の教え -->
				<input type="hidden" name="id" value="{$app.prize.id}">
				<input type="hidden" name="ranking_id" value="{$app.prize.ranking_id}">
				<input type="hidden" name="status" value="{$app.prize.status}">

				<!-- エラー表示 -->
				{if count($errors)}
				<div class="alert alert-error">
					{foreach from=$errors item=error}
					<div style="color: #ff0000;">{$error}</div>
					{/foreach}
				</div>
				{/if}

				<!-- 配布先頭順位／配布末尾順位 -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="distribute_start"}
					</div>
					<div class="span4">
						<input class="span4" type="text" name="distribute_start" value="{$app.prize.distribute_start}" />
					</div>
					<div class="span2">
						{form_name name="distribute_end"}
						<i class="icon-question-sign" data-original-title="配布順位が単独の場合は入力不要"></i>
					</div>
					<div class="span4">
						<input class="span4" type="text" name="distribute_end" value="{$app.prize.distribute_end}" />
					</div>
				</div>
				<br>

				<div class="row-fluid">
					<!-- 賞品タイプ -->
					<div class="span2">
						{form_name name="prize_type"}
					</div>
					<div class="span4">
						<select name="prize_type" class="input-block-level">
							{html_options options=$app.prize_type_options selected=$app.prize.prize_type}
						</select>
					</div>
					<!-- 賞品ID-->
					<div class="span2">
						{form_name name="prize_id"}
						<i class="icon-question-sign" data-original-title="賞品タイプが「通常アイテム」「モンスター」の場合のみ"></i>
					</div>
					<div class="span4">
						<input class="span6" type="text" name="prize_id" value="{$app.prize.prize_id}" />
					</div>
				</div>
				<br>

				<!-- LV -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="lv"}
						<i class="icon-question-sign" data-original-title="賞品タイプが「モンスター」の場合のみ"></i>
					</div>
					<div class="span4">
						<input class="span4" type="text" name="lv" value="{$app.prize.lv}" />
					</div>
				</div>
				<br>

				<!-- 配布数 -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="number"}
					</div>
					<div class="span6">
						<input class="span4" type="text" name="number" value="{$app.prize.number}" />
					</div>
				</div>
				<br>

				<div class="text-center">
				   <input type="submit" value="更新確認" class="btn" />
			   </div>
			</form>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
</body>
</html>