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
			<h2>ランキングマスター&nbsp;登録</h2>
			<form action="confirm" method="post" class="form-horizontal">

				<!-- エラー表示 -->
				{if count($errors)}
				<div class="alert alert-error">
					{foreach from=$errors item=error}
					<div style="color: #ff0000;">{$error}</div>
					{/foreach}
				</div>
				{/if}
				
				<!-- ランキングID-->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="ranking_id"}
					</div>
					<div class="span4">
						<input class="span8" type="text" name="ranking_id" value="{$app.ranking_id}" />
					</div>
				</div>
				<br>
				<!-- ランキングタイトル -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="title"}
					</div>
					<div class="span8">
						<input class="span8" type="text" name="title" value="{$app.title}" />
					</div>
				</div>
				<br>
				<!-- ランキングサブタイトル -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="subtitle"}
					</div>
					<div class="span8">
						<input class="span8" type="text" name="subtitle" value="{$app.subtitle}" />
					</div>
				</div>
				<br>
				<!-- ターゲットタイプ -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="target_type"}
					</div>
					<div class="span4">
						<select name="target_type" class="input-block-level">
							{html_options options=$app.target_type_options selected=$app.target_type}
						</select>
					</div>
				</div>
				<br>

				<!-- ターゲット -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="targets"}<i class="icon-question-sign" data-original-title="モンスターIDなどをカンマ区切りで入力"></i>
				    </div>
					<div class="span10">
						<input class="span10" type="text" name="targets" value="{$app.targets}" />
				    </div>
			    </div>
				<br>

				<!-- 獲得方法 -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="processing_type"}<i class="icon-question-sign" data-original-title="処理タイプをカンマ区切りで入力"></i>
					</div>
					<div class="span10">
						<input class="span10" type="text" name="processing_type" value="{$app.processing_type}" />
					</div>
				</div>
				<br>

				<!-- クリアチェック対象ダンジョンID（超級／上級） -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="clear_target_dungeon_rank3"}<i class="icon-question-sign" data-original-title="ダンジョンIDをカンマ区切りで入力"></i>
					</div>
					<div class="span10">
						<input class="span10" type="text" name="clear_target_dungeon_rank3" value="{$app.clear_target_dungeon_rank3}" />
					</div>
				</div>
				<br>
				<div class="row-fluid">
					<div class="span2">
						{form_name name="clear_target_dungeon_rank4"}<i class="icon-question-sign" data-original-title="ダンジョンIDをカンマ区切りで入力"></i>
					</div>
					<div class="span10">
						<input class="span10" type="text" name="clear_target_dungeon_rank4" value="{$app.clear_target_dungeon_rank4}" />
					</div>
				</div>
				<br>

				<!-- ランキング順位閾値／ランキングTOP表示数 -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="threshold"}<i class="icon-question-sign" data-original-title=""></i>
					</div>
					<div class="span4">
						<input class="span4" type="text" name="threshold" value="{$app.threshold}" />
					</div>

					<div class="span2">
						{form_name name="view_ranking_top"}<i class="icon-question-sign" data-original-title="表示しない場合は「0」を入力"></i>
					</div>
					<div class="span4">
						<input class="span4" type="text" name="view_ranking_top" value="{$app.view_ranking_top}" />
					</div>
				</div>
				<br>

				<!-- 上位／下位表示数 -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="view_higher"}
					</div>
					<div class="span4">
						<input class="span4" type="text" name="view_higher" value="{$app.view_higher}" />
					</div>
					<div class="span2">
						{form_name name="view_lower"}
					</div>
					<div class="span4">
						<input class="span4" type="text" name="view_lower" value="{$app.view_lower}" />
					</div>
				</div>
				<br>
				<!-- 開始／終了期間 -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="date_start"}
					</div>
					<div class="span4">
						<input type="text" name="date_start" value="{$app.date_start}" class="jquery-ui-datetimepicker">
					</div>
					<div class="span2">
						{form_name name="date_end"}
					</div>
					<div class="span4">
						<input type="text" name="date_end" value="{$app.date_end}" class="jquery-ui-datetimepicker">
					</div>
				</div>
				<br>
				<!-- バナーURL  -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="banner_url"}
				    </div>
					<div class="span10">
						<input class="span10" type="text" name="banner_url" value="{$app.banner_url}" />
				    </div>
			    </div>
				<br>
				<!-- URL  -->
				<div class="row-fluid">
					<div class="span2">
						{form_name name="url"}
				    </div>
					<div class="span10">
						<input class="span10" type="text" name="url" value="{$app.url}" />
				    </div>
			    </div>
				<br>

				<div class="text-center">
				   <input type="submit" value="登録確認" class="btn" />
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