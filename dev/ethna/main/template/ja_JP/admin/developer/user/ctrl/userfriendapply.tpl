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
			<h2>ユーザ制御　フレンド申請確認</h2>

		<table border="0">
			<tr>
			<td>
			<table border="1" cellpadding="5">
				<tr>
					<td>ユーザID</td>
					<td>{$app.base.user_id}</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td>{$app.base.name}</td>
				</tr>
				<tr>
					<td>フレンド数</td>
					<td>{$app.src_friend_cnt.1+$app.src_friend_cnt.2}／{$app.base.friend_max+$app.base.friend_expand}</td>
				</tr>
				<tr>
					<td>申請受信数</td>
					<td>{$app.src_friend_cnt.3}</td>
				</tr>
				<tr>
					<td>ブロック数</td>
					<td>{$app.src_friend_cnt.4}</td>
				</tr>
			</table>
			</td>
			<td>
			{if $app.status_detail_code==null}
				<form action="userfriendapplyreg" method="post">
					<input type="hidden" name="id" value="{$app.base.user_id}" />
					<input type="hidden" name="friend_id" value="{$app.friend.user_id}" />
					　→<input type="submit" name="submit" value="申請" class="btn end-btn" />→　
				</form>
			{else}
				　→申請不可→　
			{/if}
			</td>
			<td>
			<table border="1" cellpadding="5">
				<tr>
					<td>フレンドID</td>
					<td>{$app.friend.user_id}</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td>{$app.friend.name}</td>
				</tr>
				<tr>
					<td>フレンド数</td>
					<td>{$app.dst_friend_cnt.1+$app.dst_friend_cnt.2}／{$app.friend.friend_max+$app.friend.friend_expand}</td>
				</tr>
				<tr>
					<td>申請受信数</td>
					<td>{$app.dst_friend_cnt.3}</td>
				</tr>
				<tr>
					<td>ブロック数</td>
					<td>{$app.dst_friend_cnt.4}</td>
				</tr>
			</table>
			</td>
			</tr>
		</table>
			
			<br />
			{if $app.status_detail_code==5101}
				フレンド人数が上限に達しています<br />
			{elseif $app.status_detail_code==5106}
				相手のフレンド人数が上限に達しています<br />
			{/if}
			
			<p>
				<br />
				{a href="list?by=id&id=`$form.id`"}戻る{/a}
			</p>
		</div><!--/span-->
	</div><!--/row-->

	<hr>
	{include file="admin/common/footer.tpl"}
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}
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
