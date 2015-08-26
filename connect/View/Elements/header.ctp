<header class="header">
	<div class="header_inner">
		<div class="header_title">
			<h1><?php echo $h1; ?></h1>
			<p class="header_title_logo">
				<a href="/" alt="Connect">Connect</a>
			</p>
			<p class="header_menu_toggle">MENU</p>
		</div>
		<ul class="header_menu">
			<li class="header_menu_list">
				<a class="header_menu_list_title" href="/Projects/" alt="案件を探す">案件を探す</a>
				<ul class="header_menu_list_detail">
					<li><a href="/Projects/" alt="とりあえず見る">とりあえず見る</a></li>
					<li><a href="/projects?Skill=2" alt="LAMPエンジニア">LAMPエンジニア</a></li>
					<li><a href="/projects?Skill=1" alt="Rubyエンジニア">Rubyエンジニア</a></li>
					<li><a href="/projects?Skill=4" alt="Javaエンジニア">Javaエンジニア</a></li>
					<li><a href="/projects?Position=5" alt="フロントエンド">フロントエンド</a></li>
					<li><a href="/projects?Position=6" alt="webデザイナー">webデザイナー</a></li>
					<li><a href="/projects?Position=9" alt="webディレクター">webディレクター</a></li>
					<li><a href="/projects?Position=16" alt="インフラ(サーバー)">インフラ(サーバー)</a></li>
					<li><a href="/projects?Position=17" alt="インフラ(ネットワーク)">インフラ(ネットワーク)</a></li>
					<li><a href="/projects?Position=15" alt="コンサル/PM/PMO">コンサル/PM/PMO</a></li>
				</ul>
			</li>
			<li class="header_menu_list">
				<a class="header_menu_list_title keep_list" href="/keeps/">☆気になる！<span class="keep_count">(<?php echo h($keep_count); ?>件)</span></a>
			</li>
			<li class="register">
				<a href="/members/">新規無料登録</a>
			</li>
		</ul>
	</div>
</header>