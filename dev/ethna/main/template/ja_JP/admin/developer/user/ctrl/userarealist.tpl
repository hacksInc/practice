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
			<h2>ユーザ制御　エリア情報</h2>
			<table border="0" cellpadding="4">
				<tr>
					<td>ユーザID</td>
					<td>{$app.base.user_id}</td>
				</tr>
				<tr>
					<td>ニックネーム</td>
					<td>{$app.base.name}</td>
				</tr>
			</table>

			
			<font color="#ff0000">{$app.err_msg}<br /></font>
			
			エリア強制進行<br />
			<form action="userareaproceed" method="post">
				<input type="hidden" name="id" value="{$app.base.user_id}">
				
				<select name="area_id">
				{foreach from=$app.area_normal_all key=k item=v}
					<option value="{$v.area_id}" {if $v.area_id==$app.area_last}selected{/if}>{$v.area_id}:{$v.name}</option>
				{/foreach}
				</select>
				
				<input type="submit" name="update" value="進める" class="btn end-btn" />
			</form>
			
				ノーマルクエスト<br />
				<table border="1">
				<tr>
					<td align="center">　クエストID　</td>
					<td align="center" colspan=2>　クエスト名　</td>
					<td align="center">　エリアID　</td>
					<td align="center" width=150>　エリア名　</td>
					<td align="center">　ステータス　</td>
				</tr>
			{foreach from=$app.area_list key=k item=v}
				<tr>
					{if $v.no==1}
					<td align="center" rowspan={$v.a_cnt}>{$v.quest_id}</td>
					<td align="center" rowspan={$v.a_cnt} width=200>{$v.q_name}</td>
					{/if}
					<td align="center">{$v.no}</td>
					<td align="center">{$v.area_id}</td>
					<td align="center">{$v.name}</td>
					<td align="center">{if $v.status==2}2:クリア済{elseif $v.status==1}1:未クリア{else}0:未プレイ{/if}</td>
				</tr>
				{if $v.area_id==$app.area_last}
				</table>
				イベントクエスト<br />
				<table border="1">
				<tr>
					<td align="center">　クエストID　</td>
					<td align="center" colspan=2>　クエスト名　</td>
					<td align="center">　エリアID　</td>
					<td align="center" width=150>　エリア名　</td>
					<td align="center">　ステータス　</td>
				</tr>
				{/if}
			{/foreach}
				</table>
			
			<br />
			
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
			return window.confirm('エリアを進行させますがよろしいですか？');
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
