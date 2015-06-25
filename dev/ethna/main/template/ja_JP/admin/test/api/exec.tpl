<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
	{include file="admin/common/head.tpl" title="API検証"}
	<body>
		{include file="admin/common/navbar.tpl"}
		<div class="container-fluid">
		<h1>API検証</h1>
		
		<h2>url</h2>
		<pre>{$app_ne.url}</pre>
		
		<h2>request headers</h2>
		<pre>{$app_ne.headers}</pre>
		
		<h2>request postfields</h2>
		<pre>{$app_ne.postfields}</pre>
		
		<h2>response</h2>
		<pre>{$app_ne.response}</pre>
		
		<h2>response body</h2>
		<h3>json</h3>
		<pre>{$app_ne.response_body_json}</pre>
		<h3>var</h3>
		<pre>{$app_ne.response_body_var}</pre>
		
		<h2>info</h2>
		<pre>{$app_ne.info}</pre>
		
		<hr>
		{include file="admin/common/footer.tpl"}
		</div><!--/.fluid-container-->
		{include file="admin/common/script.tpl"}
	</body>
</html>
