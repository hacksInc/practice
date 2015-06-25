  <head>
    <meta charset="utf-8">
    <title>{$title}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="/psychopass_game/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">{literal}
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }

      @media (max-width: 980px) {
        /* Enable use of floated navbar text */
        .navbar-text.pull-right {
          float: none;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
    {/literal}</style>
    <link href="/psychopass_game/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link href="/psychopass_game/css/datepicker.css" rel="stylesheet">
    <link href="/psychopass_game/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="/psychopass_game/css/tablesorter/style.css" rel="stylesheet">

    {*
        Free HTML5 Bootstrap Admin Template
        http://usman.it/themes/charisma/index.html
    
		Charisma v1.0.0

		Copyright 2012 Muhammad Usman
		Licensed under the Apache License v2.0
		http://www.apache.org/licenses/LICENSE-2.0
    *}
    <link href="/psychopass_game/css/opa-icons.css" rel="stylesheet">
    
    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="/psychopass_game/js/html5shiv.js"></script>
    <![endif]-->

	<link href="/psychopass_game/css/smoothness/jquery-ui-1.10.3.custom.css" rel="stylesheet">
	<link href="/psychopass_game/css/jquery-ui-timepicker-addon.css" rel="stylesheet">
	{literal}
	<style>
		button.ui-datepicker-current { display: none; }
	</style>	
	{/literal}
	
	<link rel="stylesheet" href="/psychopass_game/css/editablegrid-2.0.1.css" type="text/css" media="screen">
	{literal}
	<style>
		/* body { font-family:'lucida grande', tahoma, verdana, arial, sans-serif; font-size:11px; } */
		/* h1 { font-size: 15px; } */
		/* a { color: #548dc4; text-decoration: none; } */
		/* a:hover { text-decoration: underline; } */
		/* table.grid1 { border-collapse: collapse; border: 1px solid #CCB; width: 800px; } */
		table.grid1 { border-collapse: collapse; border: 1px solid #CCB; }
		table.grid1 td, table.grid1 th { padding: 5px; border: 1px solid #999; }
		table.grid1 th { background: #CCC; text-align: left; }
		input.invalid { background: red; color: #FDFDFD; }
	</style>	
	{/literal}

	{literal}
	<style>
		.dl-horizontal.role1 dt {
			text-align: left;
			width:40px;
		}
		.dl-horizontal.role1 dd {
			margin-left:20px;
		}

		.environment-dependent {
			color: {/literal}{strip}
				{if Util::getEnv() == "dev"}
					green
				{elseif Util::getEnv() == "stg"}
					#cc0
				{elseif Util::getEnv() == "pro"}
					red
				{/if}
			{/strip}{literal};
		}
		
		.environment-dependent-inverse {
			background-color: {/literal}{strip}
				{if Util::getEnv() == "dev"}
					#468847
				{elseif Util::getEnv() == "stg"}
					#f89406
				{elseif Util::getEnv() == "pro"}
					#b94a48
				{/if}
			{/strip}{literal};
		}
		
		input.file-drop {
			border: 1px dashed #bbb;
			border-radius: 3px;
		}
		
		form.one-button-only {
			margin: 0px;
			padding: 0px;
		}
	</style>	
	{/literal}
  </head>
