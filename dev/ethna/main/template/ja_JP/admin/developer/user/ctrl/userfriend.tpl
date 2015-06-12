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
			<h2>ユーザ制御　フレンド情報</h2>

			<table border="0" cellpadding="5">
				<tr>
					<td>ユーザID</td>
					<td>{$app.base.user_id}</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td>{$app.base.name}</td>
				</tr>
				<tr>
					<td>フレンド数(承認済み＋申請中)</td>
					<td>{$app.friend_cnt.1+$app.friend_cnt.2}／{$app.base.friend_max+$app.base.friend_expand}</td>
				</tr>
				<tr>
					<td>申請受信数</td>
					<td>{$app.friend_cnt.3}</td>
				</tr>
				<tr>
					<td>ブロック数</td>
					<td>{$app.friend_cnt.4}</td>
				</tr>
			</table>
			
			<br />
			<form action="userfriendapply" method="post">
				<input type="hidden" name="id" value="{$app.base.user_id}" />
				フレンドにしたいユーザID<input type="text" name="friend_id" value="" />
				<input type="submit" name="submit" value="申請する" />
			</form>
			
			<table border="1" rules="none">
				<tr>
					<td colspan="5">フレンド承認済　一覧</td>
				</tr>
				<tr>
					<td align="center" width="50">　No.　</td>
					<td align="center" width="100">　フレンドID　</td>
					<td align="center" width="200">　フレンド名　</td>
					<td align="center" width="200">　連れて行けるようになる時間　</td>
					<td align="center" width="50">
						<form action="userfriendtimereset" method="post">
							<input type="hidden" name="id" value="{$app.base.user_id}" />
							<input type="submit" name="reset" value="時間リセット" class="btn end-btn" />
						</form>
					</td>
				</tr>
				{foreach from=$app.friend_list.2 key=k item=v}
				<tr>
					<td align="center">{$k+1}</td>
					<td align="center">{a href="userfriend&id=`$v.friend_id`"}{$v.friend_id}{/a}</td>
					<td align="center">{$v.name}</td>
					<td align="center">{$v.date_bring}</td>
					<td align="center">
						<form action="userfrienddel" method="post">
							<input type="hidden" name="id" value="{$app.base.user_id}" />
							<input type="hidden" name="friend_id" value="{$v.friend_id}" />
							<input type="submit" name="delete" value="外す" class="btn end-btn" />
						</form>
					</td>
				</tr>
				{/foreach}
			</table>
			
			<br />
			<table border="1" rules="none">
				<tr>
					<td colspan="5">フレンド申請中　一覧</td>
				</tr>
				<tr>
					<td align="center" width="50">　No.　</td>
					<td align="center" width="100">　フレンドID　</td>
					<td align="center" width="200">　フレンド名　</td>
					<td align="center" width="200"></td>
					<td align="center" width="50"></td>
				</tr>
				{foreach from=$app.friend_list.1 key=k item=v}
				<tr>
					<td align="center">{$k+1}</td>
					<td align="center">{a href="userfriend&id=`$v.friend_id`"}{$v.friend_id}{/a}</td>
					<td align="center">{$v.name}</td>
					<td align="center"></td>
					<td align="center">
						<form action="userfrienddel" method="post">
							<input type="hidden" name="id" value="{$app.base.user_id}" />
							<input type="hidden" name="friend_id" value="{$v.friend_id}" />
							<input type="submit" name="delete" value="解除" class="btn end-btn" />
						</form>
					</td>
				</tr>
				{/foreach}
			</table>
			
			<br />
			<table border="1" rules="none">
				<tr>
					<td colspan="5">フレンド申請受信　一覧</td>
				</tr>
				<tr>
					<td align="center" width="50">　No.　</td>
					<td align="center" width="100">　フレンドID　</td>
					<td align="center" width="200">　フレンド名　</td>
					<td align="center" width="200"></td>
					<td align="center" width="50"></td>
				</tr>
				{foreach from=$app.friend_list.3 key=k item=v}
				<tr>
					<td align="center">{$k+1}</td>
					<td align="center">{a href="userfriend&id=`$v.friend_id`"}{$v.friend_id}{/a}</td>
					<td align="center">{$v.name}</td>
					<td align="center"></td>
					<td align="center">
						<form action="userfrienddel" method="post">
							<input type="hidden" name="id" value="{$app.base.user_id}" />
							<input type="hidden" name="friend_id" value="{$v.friend_id}" />
							<input type="submit" name="delete" value="解除" class="btn end-btn" />
						</form>
						<form action="userfriendapprove" method="post">
							<input type="hidden" name="id" value="{$app.base.user_id}" />
							<input type="hidden" name="friend_id" value="{$v.friend_id}" />
							<input type="submit" name="approve" value="承認" class="btn end-btn" />
						</form>
					</td>
				</tr>
				{/foreach}
			</table>
			
			<br />
			<table border="1" rules="none">
				<tr>
					<td colspan="5">ブロック　一覧</td>
				</tr>
				<tr>
					<td align="center" width="50">　No.　</td>
					<td align="center" width="100">　フレンドID　</td>
					<td align="center" width="200">　フレンド名　</td>
					<td align="center" width="200"></td>
					<td align="center" width="50"></td>
				</tr>
				{foreach from=$app.friend_list.4 key=k item=v}
				<tr>
					<td align="center">{$k+1}</td>
					<td align="center">{a href="userfriend&id=`$v.friend_id`"}{$v.friend_id}{/a}</td>
					<td align="center">{$v.name}</td>
					<td align="center"></td>
					<td align="center">
						<form action="userfrienddel" method="post">
							<input type="hidden" name="id" value="{$app.base.user_id}" />
							<input type="hidden" name="friend_id" value="{$v.friend_id}" />
							<input type="submit" name="delete" value="解除" class="btn end-btn" />
						</form>
					</td>
				</tr>
				{/foreach}
			</table>
			
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
