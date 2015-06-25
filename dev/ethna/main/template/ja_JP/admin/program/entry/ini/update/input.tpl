<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="エントリポイント設定 - サイコパス管理ページ"}
<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<h2>エントリポイント設定</h2>

			<p>
				バージョン振り分け設定を変更できます。<br>
				端末が現行バージョン以下の場合はmain環境へ、現行バージョンより上でレビューバージョン以下の場合はreview環境へ振り分けられます。
			</p>

			<form action="confirm" method="post" class="form-horizontal">
			    <div class="control-group">
				    <label class="control-label" for="inputCurrentVer">
						{form_name name="current_ver"}
						<i class="icon-question-sign" data-original-title="HTTPリクエストヘッダのX-Jugmon-Appverに相当する整数値"></i>
					</label>
				    <div class="controls">
						{form_input name="current_ver" id="inputCurrentVer" default=$smarty.const.PP_CURRENT_VER}
				    </div>
			    </div>
			    <div class="control-group">
				    <label class="control-label" for="inputReviewVer">
						{form_name name="review_ver"}
						<i class="icon-question-sign" data-original-title="Apple審査用。review環境を有効にしたい場合は、HTTPリクエストヘッダのX-Jugmon-Appverに相当する整数値。無効にしたい場合は空。"></i>
					</label>
				    <div class="controls">
						{form_input name="review_ver" id="inputReviewVer" default=$smarty.const.PP_REVIEW_VER}
				    </div>
			    </div>
			    <div class="control-group">
				    <div class="controls">
					   <input type="submit" value="次へ" class="btn" />
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
