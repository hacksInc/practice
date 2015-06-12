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
			<h2>賞品配布設定&nbsp;登録確認</h2>
			<h4>&nbsp;『{$app.title}{if empty($app.subtitle) === false}&nbsp;【{$app.subtitle}】{/if}』</h4>
			<br>

			<form action="exec" method="post" class="form-horizontal">
				<!-- 先祖代々伝わる秘伝の教え -->
				<input type="hidden" name="ranking_id" value="{$app.ranking_id}">
				<input type="hidden" name="distribute_start" value="{$app.distribute_start}">
				<input type="hidden" name="distribute_end" value="{$app.distribute_end}">
				<input type="hidden" name="prize_type" value="{$app.prize_type}">
				<input type="hidden" name="prize_id" value="{$app.prize_id}">
				<input type="hidden" name="lv" value="{$app.lv}">
				<input type="hidden" name="number" value="{$app.number}">

				<!-- 前画面で入力した内容の表示 -->
				<div class="control-group">
					<label class="control-label">{form_name name="ranking_id"}</label>
					<div class="controls">
						{$app.ranking_id}
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">{form_name name="distribute_start"}</label>
					<div class="controls">
						{$app.distribute_start}
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">{form_name name="distribute_end"}</label>
					<div class="controls">
						{$app.distribute_end}
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">{form_name name="prize_type"}</label>
					<div class="controls">
						{$app.prize_type_str}
					</div>
				</div>
				{if ( $app.prize_type === 3 )||( $app.prize_type === 4 )}
				<div class="control-group">
					<label class="control-label">{form_name name="prize_id"}</label>
					<div class="controls">
						{$app.prize_id}
					</div>
				</div>
				{/if}
				{if empty( $app.prize_name ) === false}
				<div class="control-group">
					<label class="control-label">賞品名</label>
					<div class="controls">
						{$app.prize_name}
					</div>
				</div>
				{/if}
				{if $app.prize_type === '2'}
				<div class="control-group">
					<label class="control-label">{form_name name="lv"}</label>
					<div class="controls">
						{$app.lv}
					</div>
				</div>
				{/if}
			    <div class="control-group">
					<label class="control-label">{form_name name="number"}</label>
					<div class="controls">
						{$app.number}
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