<!DOCTYPE HTML>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ITエンジニアの案件・webデザイナーの求人、フリーランス向け仕事募集情報が集まるサイト</title>
<meta name="discription" content="">
<meta name="keywords" content="">
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
<link rel="stylesheet" href="http://yui.yahooapis.com/3.17.2/build/cssreset/cssreset-min.css">
<link rel="stylesheet" href="css/top.css">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script src="js/jquery.totemticker.min.js"></script>
</head>
<body>
<div id="header">
	<div class="header_title">
		<h1>ITエンジニアの案件・webデザイナーの求人、フリーランス向け仕事募集情報が集まるサイト</h1>
	</div>
	<div class="info">
		<div class="logo">
			<a href="/" alt="トップへ戻る">
				<img src="img/logo.png" width="200" height="50" alt="hacks-FreeLance-">
			</a>
		</div>
		<div class="count">
			<p>現在掲載中の案件/求人情報：<span><?php echo h($count); ?></span>件</p>
		</div>
		<div class="register">
			<a href="/members">新規無料登録はコチラ！</a>
		</div>
	<!-- info --></div>
	<div class="change">
		<div class="change_info">
			<div class="change_logo">
				<a href="/" alt="トップへ戻る">
					<img src="img/logo2.png" width="200" height="50" alt="hacks-FreeLance-">
				</a>
			</div>
			<div class="change_count">
				<p>現在掲載中の案件/求人情報：<span><?php echo h($count); ?></span>件</p>
			</div>
			<div class="change_register">
				<a href="/members">新規無料登録はコチラ！</a>
			</div>
		</div>
	<!-- change --></div>
	<div class="menu">
		<ul class="menu_bar">
			<li>
				<a href="" alt="">スキルで探す</a>
				<ul class="menu_list">
					<?php foreach ($skill as $key => $value) : ?>
						<li><a href="/projects?skill=<?php echo $key; ?>" alt="<?php echo $value; ?>"><?php echo $value; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</li>
			<li>
				<a href="" alt="">ポジションで探す</a>
				<ul class="menu_list">
					<?php foreach ($position as $key => $value) : ?>
						<li><a href="/projects?position=<?php echo $key; ?>" alt="<?php echo $value; ?>"><?php echo $value; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</li>
			<li>
				<a href="" alt="">業種で探す</a>
				<ul class="menu_list">
					<?php foreach ($service as $key => $value) : ?>
						<li><a href="/projects?service=<?php echo $key; ?>" alt="<?php echo $value; ?>"><?php echo $value; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</li>
			<li>
				<a href="" alt="">金額で探す</a>
				<ul class="menu_list">
					<?php foreach ($price as $key => $value) : ?>
						<li><a href="/projects?price=<?php echo $key; ?>" alt="<?php echo $value; ?>万円以上"><?php echo $value; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</li>
			<li>
				<a href="" alt="">その他お問い合わせ</a>
				<ul class="menu_list">
					<li><a href="" alt="">お問い合わせ</a></li>
					<li><a href="" alt="">運営会社</a></li>
					<li><a href="" alt="">プライバシーポリシー</a></li>
					<li><a href="" alt="">よくある質問</a></li>
				</ul>
			</li>
		</ul>
	<!-- menu --></div>
	<div class="top_img">
		<img src="img/topimage.png" alt="" width="100%" height="300" alt="top_img">
	</div>
	<div class="search">
		<?php
			echo $this->Form->create('Project', array('type' => 'get', 'controller' => 'projects', 'action' => 'index'));
			$this->Form->inputDefaults(array('div' => false,'label' => false,'id' => false));
		?>
		<ul>
			<li class="select">
				<?php echo $this->Form->select('skill', array($skill), array('empty' => 'スキルを選択')); ?>
			</li>
			<li class="select">
				<?php echo $this->Form->select('position', array($position), array('empty' => 'ポシションを選択', 'id' => false)); ?>
			</li>
			<li class="select">
					<?php echo $this->Form->input('price', array( 'empty' => '単価下限を選択', 'type' => 'select', 'options' => array($price))); ?>
			</li>
			<li class="freeword">
				<?php echo $this->Form->input('freeword', array('placeholder' => 'フリーワードを入力', 'class' => 'freeword')); ?>
			</li>
			<li>
				<?php echo $this->Form->submit('検索', array('div' => false, 'class' => 'submit')); ?>				
			</li>
		</ul>
		<?php echo $this->Form->end(); ?>
	<!-- search --></div>
<!-- header --></div>
<div id="main">
	<div class="content">
		<div class="about">
			<div class="explain">
				<span>hacksはIT業界の個人事業主様をトータルにサポート！</span>
				<p>IT/WEB業界のフリーランスに特化した案件/求人情報を公開しております。<br>
					キャリア相談〜案件紹介、アフターフォローまであなたをトータルにサポート！<br>
					WEB系/業務系エンジニア、インフラエンジニアに限らず、デザイナー/ディレクターなどのクリエイティブ案件、プロジェクトマネジメント/ヘルプデスクなどの管理系/事務系案件など幅広く取り扱っております。<br>専属コンサルタントがあなたにピッタリの案件をご紹介致します！
				</p>
			<!-- explain --></div>
			<div class="flow">
				<img src="img/flow.png" width="100%" height="170" alt="支援サービスの流れ「step1ご登録、step2カウンセリング、step3案件ご紹介、step4面談/参画決定、step5サポート」">
			</div>
		<!-- about --></div>
		<div class="main_contents">
			<div class="new_projects">
				<div class="new_title">
					<p>＊ 新着案件</p>
				</div>
				<ul class="tab_menu">
					<li class="tab on"><a href="javascript:void()">WEB系エンジニア案件</a></li>
					<li class="tab"><a href="javascript:void()">業務系エンジニア案件</a></li>
					<li class="tab"><a href="javascript:void()">インフラ案件</a></li>
					<li class="tab"><a href="javascript:void()">クリエイティブ案件</a></li>
					<li class="tab"><a href="javascript:void()">PM/PMO/その他案件</a></li>
				</ul>
				<ul class="new_project select">
					<?php
						foreach($web_project as $key) :
							$value = $key['Project'];
					?>
					<li>
						<a href="/projects/<?php echo h($value['id']); ?>">
							<span class="project_time">
								<span>NEW</span>
								<span><?php echo h(date('n/d', strtotime($value['modified']))); ?>更新</span>
							</span>
							<span class="project_name"><?php echo h($value['title']); ?></span>
							<span class="project_content">
								<span class="project_place"><?php echo h($value['station']); ?></span>
								<span class="project_payment"><?php echo h($value['min_price']); ?>万円〜<?php echo h($value['max_price']); ?>万円</span>
								<span class="project_skill"></span>
							</span>
						</a>
					</li>
					<?php endforeach; ?>
				</ul>
					<ul class="new_project">
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
					</ul>
					<ul class="new_project">
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
					</ul>
					<ul class="new_project">
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
					</ul>
					<ul class="new_project">
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
						<li>
							<a href="">
								<span class="project_time">
									<span>NEW</span>
									<span>12/1更新</span>
								</span>
								<span class="project_name">大手不動産会社様向けWebサイト再構築</span>
								<span class="project_content">
									<span class="project_place">渋谷駅</span>
									<span class="project_payment">40万円〜60万円</span>
									<span class="project_skill">Java、Oracle、Struts</span>
								</span>
							</a>
						</li>
					</ul>
					<div class="btn1">
						<a href="/members" alt="代わりに案件を探してもらう">代わりに案件を探してもらう</a>
					</div>
					<div class="btn2">
						<ul>
							<li class="navi act">
								<a href="" alt="">すべてのWEB系エンジニア案件を見る</a>
							</li>
							<li class="navi">
								<a href="" alt="">すべての業務系エンジニア案件を見る</a>
							</li>
							<li class="navi">
								<a href="" alt="">すべてのインフラ案件を見る</a>
							</li>
							<li class="navi">
								<a href="" alt="">すべてのクリエイティブ案件を見る</a>
							</li>
							<li class="navi">
								<a href="" alt="">すべてのPM/PMO/その他案件を見る</a>
							</li>
						</ul>
					</div>
				<!-- new_projects --></div>
				<div class="more_projects">
					<div class="more_title">
						<p>＊ 案件 / 求人を探す</p>
					</div>
					<div class="more_project left">
						<p>言語で探す</p>
						<ul>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
						</ul>
						<a href="" alt="">言語一覧を見る →</a>
					</div>
					<div class="more_project">
						<p>ジャンルで探す</p>
						<ul>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
						</ul>
						<a href="" alt="">ジャンル一覧を見る →</a>
					</div>
					<div class="more_project left">
						<p>ポジションで探す</p>
						<ul>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
						</ul>
						<a href="" alt="">ポジション一覧を見る →</a>
					</div>
					<div class="more_project">
						<p>金額で探す</p>
						<ul>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
							<li><a href="" alt="">Java</a></li>
						</ul>
						<a href="" alt="">金額一覧を見る →</a>
					</div>
				</div>
			</div><!-- main_contents -->
			<div class="sub_contents">
				<div class="like">
				<div class="fb-like" data-href="https://www.facebook.com/pages/HacksInc/298735600320352?ref=hl" data-width="225" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
				</div>
				<div class="partner">
					<a href="/members" alt="ビジネスパートナー募集中"><img src="img/partner.png" alt="ビジネスパートナー募集中" width="217" height="90"></a>
				</div>
				<div class="recomend">
					<p>＊ 注目！人気のキーワード</p>
					<ul>
						<li><a href="" alt="">Java 求人案件</a></li>
						<li><a href="" alt="">PHP 求人案件</a></li>
						<li><a href="" alt="">ソーシャルゲーム開発</a></li>
						<li><a href="" alt="">ネイティブアプリ開発</a></li>
						<li><a href="" alt="">RubyonRails 高単価</a></li>
						<li><a href="" alt="">Unity 案件</a></li>
						<li><a href="" alt="">Java</a></li>
						<li><a href="" alt="">テストテスト 案件</a></li>
						<li><a href="" alt="">Java</a></li>
					</ul>
				</div>
				<div class="recomend">
					<p>＊ オススメの案件特集！</p>
					<ul>
						<li><a href="" alt="">高単価案件一覧</a></li>
						<li><a href="" alt="">ソーシャルゲーム特集</a></li>
						<li><a href="" alt="">金融系</a></li>
						<li><a href="" alt="">Java</a></li>
						<li><a href="" alt="">Java</a></li>
						<li><a href="" alt="">Java</a></li>
						<li><a href="" alt="">Java</a></li>
						<li><a href="" alt="">Java</a></li>
						<li><a href="" alt="">テストテスト 案件</a></li>
					</ul>
				</div>
			</div>
		</div><!-- content -->
	</div><!-- main -->
	<div id="facebook">
		<div class="fb-like-box" data-href="https://www.facebook.com/pages/HacksInc/298735600320352?ref=hl" data-width="1500" data-height="200" data-colorscheme="light" data-show-faces="true" data-header="true" data-stream="false" data-show-border="true"></div>
	</div>
	<div id="subfooter">
		<ul>
			<li>エンジニア</li>
			<li><a href="" alt="">Java</a></li>
			<li><a href="" alt="">PHP</a></li>
			<li><a href="" alt="">Ruby</a></li>
			<li><a href="" alt="">Objectiv-C</a></li>
			<li><a href="" alt="">Android</a></li>
			<li><a href="" alt="">インフラエンジニア</a></li>
			<li><a href="" alt="">業務系エンジニア</a></li>
			<li><a href="" alt="">WEB系エンジニア</a></li>
		</ul>
		<ul>
			<li>クリエイティブ</li>
			<li><a href="" alt="">UIデザイナー</a></li>
			<li><a href="" alt="">WEBディレクター</a></li>
			<li><a href="" alt="">スマホデザイナー</a></li>
			<li><a href="" alt="">WEBデザイナー</a></li>
			<li><a href="" alt="">アートディレクター</a></li>
			<li><a href="" alt="">イラストレーター</a></li>
			<li><a href="" alt="">コーダー</a></li>
			<li><a href="" alt="">フロントエンド</a></li>
		</ul>
		<ul>
			<li>管理/その他</li>
			<li><a href="" alt="">PM</a></li>
			<li><a href="" alt="">PMO</a></li>
			<li><a href="" alt="">コンサルタント</a></li>
			<li><a href="" alt="">ヘルプデスク</a></li>
			<li><a href="" alt="">サーバー運用保守</a></li>
			<li><a href="" alt="">ネットワーク運用保守</a></li>
			<li><a href="" alt="">テスター</a></li>
			<li><a href="" alt="">初級者案件</a></li>
		</ul>
		<ul>
			<li>ジャンルで探す</li>
			<li><a href="" alt="">金融系</a></li>
			<li><a href="" alt="">広告</a></li>
			<li><a href="" alt="">流通系</a></li>
			<li><a href="" alt="">ECサイト</a></li>
			<li><a href="" alt="">ソーシャル系</a></li>
			<li><a href="" alt="">生損保</a></li>
			<li><a href="" alt="">WEB系</a></li>
			<li><a href="" alt="">SAP/ERP</a></li>
		</ul>
		<ul>
			<li>金額で探す</li>
			<li><a href="" alt="">30万円〜</a></li>
			<li><a href="" alt="">40万円〜</a></li>
			<li><a href="" alt="">50万円〜</a></li>
			<li><a href="" alt="">60万円〜</a></li>
			<li><a href="" alt="">70万円〜</a></li>
			<li><a href="" alt="">80万円〜</a></li>
			<li><a href="" alt="">90万円〜</a></li>
			<li><a href="" alt="">100万円〜</a></li>
		</ul>
		<ul>
			<li>hacksについて</li>
			<li><a href="" alt="">法人の企業様</a></li>
			<li><a href="" alt="">ビジネスパートナー様</a></li>
			<li><a href="" alt="">フリーランスの方</a></li>
			<li><a href="" alt="">プライバシーポリシー</a></li>
			<li><a href="" alt="">会社概要</a></li>
			<li><a href="" alt="">お問い合わせ</a></li>
		</ul>
	</div><!-- subfooter -->
	<div id="footer">
		<p>Copyright(C) hacks Co.,Ltd All Rights Reserved.</p>
	</div>
</body>
<script>


	//***********************************//
	//***********  header.js  ***********//
	//***********************************//

		//スティッキーヘッダー
		var $window = $(window), //ウィンドウを指定
		$content = $(".menu"), //#content部分
		$chenge = $(".change"), //#change部分
		topContent = $content.offset().top; //#contentの位置を取得
		 
		var sticky = false;
		 
		 $window.on("scroll", function () {
		      if ($window.scrollTop() > topContent) {　//scroll位置が#contentの上にある場合
		           if ( sticky === false ){
		                $chenge.slideDown();　//#change部分が上がる。
		                sticky = true;
		           }
		      } else {
		           if ( sticky === true ){　//scroll位置が下にある場合
		                $chenge.slideUp();//#change部分が降りてくる。
		                sticky = false;
		           }
		      }
		 });
		 $window.trigger("scroll");



	//***********************************//
	//*********  menu.js  ***********//
	//***********************************//

		//メニューバーをホバーしたらサブウィンドウが出現する
		$(function(){
			$(".menu_bar li").hover(function(){
				$(".menu_list",this).show();
			},

			function(){
				$(".menu_list",this).hide();
			});
		});


	//***********************************//
	//********  new_projects.js  ********//
	//***********************************//

		$(function() {
			$('.tab_menu li').click(function() {

				var num = $('.tab_menu li').index(this);

				$('.tab_menu li').removeClass('on');
				$(this).addClass('on');

				$('.new_project').removeClass('select');
				$('.new_project').eq(num).addClass('select');

				$('.navi').removeClass('act');
				$('.navi').eq(num).addClass('act');

			});
		});

		$(function(){
		    $('.new_project').totemticker({
		        row_height: '55px',
		        speed: 800,
		        interval: 3000,
		        max_items: 5,
		        mousestop: true,
		    });
		});


	//***********************************//
	//********** facebook.js  ***********//
	//***********************************//
</script>
<div id="fb-root"></div>
<script>
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.0";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>

</head>
