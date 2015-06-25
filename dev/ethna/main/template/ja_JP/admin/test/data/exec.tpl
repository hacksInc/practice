<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <h1>テストデータ生成</h1>
		生成しました。<br />
		{if $form.user_type == "create"}
			uid: {$app.uid}<br />
			uipw: {$app.uipw}<br />
			dmpw: {$app.dmpw}<br />
		{/if}
    </body>
</html>
