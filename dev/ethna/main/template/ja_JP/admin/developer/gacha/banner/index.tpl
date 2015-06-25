<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ガチャ - サイコパス管理ページ"}
<body>
{literal}
<style type="text/css">
	.content-status-waiting {
		font-weight: bold;
	}
	.content-status-flag {
		font-weight: bold;
		color: blue;
	}
	.content-status-active {
		font-weight: bold;
		color: red;
	}
</style>
{/literal}
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

        <div class="span9">
			<form action="index" method="post" class="text-right" id="form-lang">
				{form_input name="lang" id="select-lang"}
			</form>
			
			<h2>ガチャ一覧<i class="icon-question-sign" data-original-title="“公開中”と“公開予定”のメッセージが、Priorityが高い順に表示されます。“公開終了”したものについては「ログ閲覧」画面へ移行します。"></i></h2>
			<form action="create/input" method="get">
				<input type="submit" value="追加" class="btn"> 新規ガチャを準備する為、ガチャマスターの登録を行ないます。
			</form>
				
			{foreach from=$app.list item="row" key="i"}
				<div class="row-fluid" style="border-top: solid 1px; border-left: solid 1px; border-right: solid 1px; padding-right: 10px; padding-left: 5px;">
					<div class="text-center">
						{if $row.disp_sts == constant("Jm_ShopManager::GACHA_DISP_STS_NORMAL")}
							{if $row.status == "waiting"}
								<span class="content-status-waiting">公開予定</span>
								<form action="sts/exec" method="post">
									<input type="hidden" name="gacha_id" value="{$row.gacha_id}">
									<input type="hidden" name="disp_sts" value="{"Jm_ShopManager::GACHA_DISP_STS_TEST"|constant}">
									<input type="submit" value="テスト公開" class="btn sts-btn" disabled>
									<i class="icon-question-sign" data-original-title="テスト公開は機能しません。（未実装です）"></i>
								</form>
							{elseif $row.status == "active"}
								<span class="content-status-active">公開中</span>
								<form action="sts/exec" method="post">
									<input type="hidden" name="gacha_id" value="{$row.gacha_id}">
									<input type="hidden" name="disp_sts" value="{"Jm_ShopManager::GACHA_DISP_STS_PAUSE"|constant}">
									<input type="submit" value="公開一時停止" class="btn sts-btn">
								</form>
							{/if}
						{elseif $row.disp_sts == constant("Jm_ShopManager::GACHA_DISP_STS_TEST")}
							<span class="content-status-flag">テスト公開中</span>
							<form action="sts/exec" method="post">
								<input type="hidden" name="gacha_id" value="{$row.gacha_id}">
								<input type="hidden" name="disp_sts" value="{"Jm_ShopManager::GACHA_DISP_STS_NORMAL"|constant}">
								<input type="submit" value="テスト公開停止" class="btn sts-btn">
							</form>
						{elseif $row.disp_sts == constant("Jm_ShopManager::GACHA_DISP_STS_PAUSE")}
							<span class="content-status-flag">公開一時停止</span>
							<form action="sts/exec" method="post">
								<input type="hidden" name="gacha_id" value="{$row.gacha_id}">
								<input type="hidden" name="disp_sts" value="{"Jm_ShopManager::GACHA_DISP_STS_NORMAL"|constant}">
								<input type="submit" value="公開" class="btn sts-btn">
							</form>
						{/if}
					</div>

					<div class="row-fluid">
				        <div class="span1">
							<form action="update/input" method="get">
								<input type="hidden" name="gacha_id" value="{$row.gacha_id}">
								<input type="submit" value="修正" class="btn">
							</form>
						</div>
						<div class="span11">
							<div class="row-fluid">
								<div class="span6">
								</div>
								<div class="span2">
									{form_name name="ua"}
								</div>
								<div class="span4">
									{$app.form_template.ua.option[$row.ua]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="gacha_id"}
								</div>
								<div class="span4">
									{$row.gacha_id}
								</div>
								<div class="span2">
									{form_name name="type"}
								</div>
								<div class="span4">
									{$app.form_template.type.option[$row.type]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="sort_list"}
								</div>
								<div class="span4">
									{$app.form_template.sort_list.option[$row.sort_list]}
								</div>
								<div class="span2">
									{form_name name="price"}
								</div>
								<div class="span4">
									{$row.price}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="comment"}
								</div>
								<div class="span10">
									{$row.comment}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span8 text-right">
									<img src="image?gacha_id={$row.gacha_id}">
								</div>
								<div class="span4">
{*
									<div class="row-fluid">
										<div class="span6">
											総ガチャ数
										</div>
										<div class="span6">
											{$row.gacha_order_info.gacha_cnt}
										</div>
									</div>
									<div class="row-fluid">
										<div class="span6">
											本日のガチャ数
										</div>
										<div class="span6">
											{$row.gacha_cnt_today}
										</div>
									</div>
*}
									{a href="../weight/index?gacha_id=`$row.gacha_id`" class="btn"}ウェイト設定{/a}
									{if $row.extra_gacha.is_exists == true}
										{a href="../weightextra/index?gacha_id=`$row.gacha_id`" class="btn"}おまけウェイト設定{/a}
									{/if}
									<br>
									<br>
{*
									{a href="../order/index?gacha_id=`$row.gacha_id`" class="btn"}オーダーリスト{/a}<br>
*}
									{a href="../draw/index?gacha_id=`$row.gacha_id`" class="btn"}ドローリスト{/a}<br>
									<br>

									{if $row.transaction_info.is_exists == true}
										<div class="row-fluid">
											<div class="span6">
												トランザクション情報<br>作成日時
											</div>
											<div class="span6">
												{$row.transaction_info.date_created}
											</div>
										</div>
										{if $row.transaction_info.is_broken == true}
											<p class="text-warning">
											    <div style="color:red"><i class="icon-warning-sign"></i>トランザクション情報が壊れています！<br>クリアして再作成してください。</style>
											</p>
										{/if}
										<form action="clear/exec" method="post">
											<input type="hidden" name="gacha_id" value="{$row.gacha_id}">
											<input type="submit" value="ガチャ情報クリア" class="btn clear-btn">
											<i class="icon-question-sign" data-original-title="トランザクション情報をクリアします。"></i>
										</form>
									{else}
										<p class="text-warning">
										    <div style="color:red"><i class="icon-warning-sign"></i>ガチャのトランザクション情報がありません！</style>
										</p>
										<form action="renew/exec" method="post">
											<input type="hidden" name="gacha_id" value="{$row.gacha_id}">
											<input type="hidden" name="extra_gacha" value="{$row.extra_gacha.is_exists}">
											<input type="submit" value="ガチャ情報再作成" class="btn renew-btn">
											<i class="icon-question-sign" data-original-title="トランザクション情報を再作成します。"></i>
										</form>
									{/if}
								</div>
							</div>
							<div class="row-fluid">
								&nbsp;
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="banner_type"}
								</div>
								<div class="span4">
									{$app.form_template.banner_type.option[$row.banner_type]}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="banner_url"}
								</div>
								<div class="span10">
									{$row.banner_url}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="width"}
								</div>
								<div class="span4">
									{$row.width}
								</div>
								<div class="span2">
									{form_name name="height"}
								</div>
								<div class="span4">
									{$row.height}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="position_x"}
								</div>
								<div class="span4">
									{$row.position_x}
								</div>
								<div class="span2">
									{form_name name="position_y"}
								</div>
								<div class="span4">
									{$row.position_y}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span2">
									{form_name name="date_start"}
								</div>
								<div class="span4">
									{$row.date_start}
								</div>
								<div class="span2">
									{form_name name="date_end"}
								</div>
								<div class="span4">
									{$row.date_end}
								</div>
							</div>
							<div class="row-fluid">
								<div class="span10">
								</div>
								<div class="span1">
									<form action="create/input" method="get">
										<input type="hidden" name="gacha_id" value="{$row.gacha_id}">
										<input type="submit" value="複製" class="btn copy-btn"><i class="icon-question-sign" data-original-title="ガチャIDを除く各種データがコピーされデータが生成されます。ガチャIDは新規付与されます。ウェイト設定については全て新IDに紐ついて複製されます"></i>
									</form>
								</div>
								<div class="span1">
									<form action="end/exec" method="post">
										<input type="hidden" name="gacha_id" value="{$row.gacha_id}">
										<input type="submit" value="公開&#13;&#10;終了" class="btn btn-mini end-btn"><i class="icon-question-sign" data-original-title="終了日時を、現在の時刻に設定し表示を終了します。"></i>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			{/foreach}
			<div class="row-fluid" style="border-top: solid 1px; padding-right: 10px; padding-left: 5px;">
				&nbsp;
			</div>

			<div class="text-right">{a href="log/view"}＞ 操作ログ閲覧{/a}&nbsp;&nbsp;{a href="history"}＞ ログ閲覧{/a}</div>
			
        </div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
{literal}
<script>
	$(function(){
		$('.copy-btn').click(function() {
			return window.confirm('複製しますがよろしいですか？');
		});
		
//		$('.stop-btn').click(function() {
//			return window.confirm('再構築停止しますがよろしいですか？');
//		});
		
		$('input.end-btn').click(function() {
			return window.confirm('公開終了しますがよろしいですか？');
		});
		
		$('input.sts-btn').click(function() {
			return window.confirm('表示ステータスを変更します。よろしいですか？');
		});

		$('input.clear-btn').click(function() {
			return window.confirm('ガチャのトランザクション情報をクリアします。よろしいですか？');
		});
		$('input.renew-btn').click(function() {
			return window.confirm('ガチャのマスタ情報を元にトランザクション情報を作成します。よろしいですか？');
		});

	});
</script>
{/literal}
</body>
</html>