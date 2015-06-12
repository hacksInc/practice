<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <title>サイコパス管理</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="/psychopass_game/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">{literal}
      body {
        padding-top: 40px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
      }

      .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    {/literal}</style>
    <link href="/psychopass_game/css/bootstrap-responsive.min.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="/js/html5shiv.js"></script>
    <![endif]-->

  </head>

  <body>
    <div class="container">
	  <div id="msieWarning" class="alert alert-block" style="display: none;">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<i class="icon-warning-sign"></i>この管理ページはMicrosoft Internet Explorerには対応しておりません。<br>
		以下のブラウザをご利用下さい。<br>
		・Google Chrome （バージョン28以上）<br>
		・Mozilla Firefox （バージョン22以上）<br>
	  </div>

      <form class="form-signin" action="/psychopass_game/admin/login" method="post">
        <h2 class="form-signin-heading">サイコパス管理</h2>
		{if count($errors) > 0}
			<p class="text-error">
			{foreach from=$errors item=error}
				{$error|nl2br}<br />
			{/foreach}
			</p>
		{/if}
        ID<input type="text" class="input-block-level" placeholder="ID" name="lid">
        Password<input type="password" class="input-block-level" placeholder="Password" name="lpw">
        Unit
		<select name="unit" class="input-block-level" placeholder="Unit" >
			{html_options values=$app.units output=$app.units}
		</select>
        <button class="btn btn-large btn-primary" type="submit">ログイン</button>
		{if $app.loginpath}
		<input type="hidden" name="loginpath" value="{$app.loginpath}" />
		{/if}
      </form>

    </div> <!-- /container -->

    <!-- Le javascript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
	<script src="/psychopass_game/js/jquery-1.9.1.min.js"></script>
    <script src="/psychopass_game/js/bootstrap.min.js"></script>
	<script>{literal}
		$(function(){
			if (navigator.appName == 'Microsoft Internet Explorer') {
				$('#msieWarning').css('display', 'block');
			}
	    });
	{/literal}</script>

  </body>
</html>
