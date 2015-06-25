<!DOCTYPE html>
<html lang="ja">
{include file="admin/common/head.tpl" title="SVN比較 - サイコパス管理ページ"}
<body style="padding-top: 10px;">
<link type="text/css" rel="stylesheet" href="/js/mergely-3.3.6/lib/codemirror.css" />
<link type="text/css" rel="stylesheet" href="/js/mergely-3.3.6/lib/mergely.css" />

<div class="container-fluid" style="width: 1150px;">
	<div style="display: inline-block; width: 550px; padding-left: 10px; margin-right: 20px;">
		{$app.file1}
	</div>
	<div style="display: inline-block; width: 550px; padding-left: 10px;">
		{$app.file2}
	</div>
	
	<div class="row-fluid">
		<div id="compare"></div>
	</div>
	
	<div class="row-fluid">
		<div style="width: 1120px; text-align: center; margin-top: 10px;">
			<button onclick="window.close()" class="btn">閉じる</button>
		</div>
	</div>
</div><!--/.fluid-container-->
{include file="admin/common/script.tpl"}

<script type="text/javascript" src="/js/mergely-3.3.6/lib/codemirror.js"></script>
<script type="text/javascript" src="/js/mergely-3.3.6/lib/mergely.js"></script>
{literal}
<script>
$(document).ready(function () {
	var lhs_value = {/literal}{$app_ne.lhs_value|json_encode}{literal};
	var rhs_value = {/literal}{$app_ne.rhs_value|json_encode}{literal};
	
	$('#compare').mergely({
		cmsettings: { readOnly: true, lineNumbers: true },
		editor_width: '550px', editor_height: '700px', 
		lhs: function(setValue) {
			setValue(lhs_value);
		},
		rhs: function(setValue) {
			setValue(rhs_value);
		},
        height: 700
	});
});
</script>
{/literal}
</body>
</html>
