<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="ユーザデータ制御 - サイコパス管理ページ"}

<body>
{include file="admin/common/navbar.tpl"}
<div class="container-fluid">
	{include file="admin/common/breadcrumb.tpl"}

	<div class="row-fluid">
		{include file="admin/common/sidebar.tpl"}

		<div class="span9">
			<h2>ユーザデータ制御　更新確認</h2>

			<table border="0" cellpadding="6">
				<tr>
					<td>ユーザID</td>
					<td>{$app.base.pp_id}</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td>{$form.name}</td>
				</tr>
				<tr>
					<td>ユーザ属性</td>
					<td>{if $form.attr==10}10:通常{elseif $form.attr==21}21:開発スタッフ{elseif $form.attr==26}26:外部協力会社{/if}</td>
				</tr>
				<tr>
					<td>ログイン禁止解除日時</td>
					<td>{$form.ban_limit}</td>
				</tr>
				<tr>
					<td>最終アクセス日時</td>
					<td>{$app.base.last_login}</td>
				</tr>
				<tr>
					<td>登録日時</td>
					<td>{$app.base.date_created}</td>
				</tr>
				<tr>
					<td>OS</td>
					<td>{if $app.base.device_type==1}iOS{elseif $app.base.device_type==2}Android{/if}</td>
				</tr>
				<tr>
					<td>年齢認証</td>
					<td>{if $app.base.age_verification==0}18歳未満{elseif $app.base.age_verification==1}18歳以上{else}未チェック{/if}</td>
				</tr>
				<tr>
					<td>当月購入金額</td>
					<td align="right">{$app.base.ma_purchase}</td>
				</tr>
				<tr>
					<td></td>
					<td align="center">
						<form action="listchg" method="post" class="form-horizontal">
							<input type="hidden" name="name" value="{$form.name}">
							<input type="hidden" name="attr" value="{$form.attr}">
							<input type="hidden" name="ban_limit" value="{$form.ban_limit}">
							<input type="hidden" name="id" value="{$app.base.pp_id}">
							<input type="submit" value="更新" class="btn end-btn" />
						</form>
					</td>
				</tr>
			</table>

		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl" datepicker="jquery"}
{literal}
<script>
	$(function(){
		$('#select-lu').change(function(){
			$('#form-lu').submit();
		});

		$('input.end-btn').click(function() {
			return window.confirm('設定しますがよろしいですか？');
		});

		$('a.pop-news-content').popover({
			html: true,
			trigger: 'click',
			placement: 'left',
			content: function(){
				var content = $(this).data('news-date') + ' '
				            + $(this).data('news-title') + '<br />'
				            + $(this).data('news-body');
				return content;
			}
		});

		$('a.pop-news-content').click(function(){
			return false;
		});

	});
</script>

{/literal}
</body>
</html>
