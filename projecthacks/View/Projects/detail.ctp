
<!DOCTYPE HTML>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>ITエンジニアの案件・webデザイナーの求人、フリーランス向け仕事募集情報が集まるサイト</title>
	<meta name="discription" content="">
	<meta name="keywords" content="">
	<link rel="stylesheet" href="http://yui.yahooapis.com/3.17.2/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="/css/anken.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
</head>
<body>
	<div id="header">
		<div class="menu">
			<ul class="menu_bar">
				<li>
					<a href="" alt="">WEB系エンジニア案件</a>
					<ul class="menu_list">
						<li><a href="" alt="">WEB系スキルの一覧を見る</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
					</ul>
				</li>
				<li>
					<a href="" alt="">業務系エンジニア案件</a>
					<ul class="menu_list">
						<li><a href="" alt="">業務系スキルの一覧を見る</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
					</ul>
				</li>
				<li>
					<a href="" alt="">インフラ案件</a>
					<ul class="menu_list">
						<li><a href="" alt="">インフラ系スキルの一覧を見る</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
					</ul>
				</li>
				<li>
					<a href="" alt="">クリエイティブ案件</a>
					<ul class="menu_list">
						<li><a href="" alt="">クリエイティブ系スキルの一覧を見る</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
					</ul>
				</li>
				<li>
					<a href="" alt="">PM/PMO/その他案件</a>
					<ul class="menu_list">
						<li><a href="" alt="">PM/PMO/その他のスキル一覧を見る</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
						<li><a href="" alt="">テストテスト</a></li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="info">
			<div class="logo">
				<a href="/" alt="Projecthacks">
					<img src="/img/projecthacks22_02.png" width="200" height="50" alt="hacks-FreeLance-">
				</a>
			</div>
			<div class="count">
				<p>現在掲載中の案件/求人情報：<span><?php echo h($count); ?></span>件</p>
			</div>
			<div class="register">
				<a href="">新規無料登録はコチラ！</a>
			</div>
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
						<?php echo $this->Form->input('price', array( 'empty' => '単価下限を選択', 'type' => 'select', 'options' => array($price) )); ?>
				</li>
				<li class="freeword">
					<?php echo $this->Form->input('freeword', array('placeholder' => 'フリーワードを入力', 'class' => 'freeword')); ?>
				</li>
				<li>
					<?php echo $this->Form->submit('検索', array('div' => false, 'class' => 'submit')); ?>
					<?php echo $this->Form->end(); ?>
				</li>
			</ul>
			</form>
		</div><!-- search -->
		<div class="breadcrumb">
			<ul>
				<li><a href="/">ITエンジニア案件・webデザイナー求人、フリーランス向け仕事募集情報</a>></li>
				<li><?php echo h($project['Project']['title']); ?></li>
		</div>
	</div><!-- header -->

	<div id="main">
		<h1><?php echo h($project['Project']['title']); ?></h1>
		<div class="explain">
			<p><?php echo h($project['Project']['content']); ?></p>
		</div><!-- explain -->
		<div class="conditions">
			<h2>＊ 案件概要</h2>
			<table>
				<tbody>
				<tr>
					<th>場所</th>
					<td><?php echo h($project['Project']['station']); ?></td>
				</tr>
				<tr>
					<th>期間</th>
					<td><?php echo h($project['Project']['term']); ?></td>
				</tr>
				<tr>
					<th>単価</th>
					<td><?php echo h($project['Project']['min_price']); ?>〜<?php echo h($project['Project']['max_price']); ?>万円</td>
				</tr>
				<tr>
					<th>清算</th>
					<td>140h〜180h<a href="" alt="">＊清算とは？</a></td>
				</tr>
				<tr>
					<th>服装</th>
					<td><?php echo h($project['Project']['clothes']); ?></td>
				</tr>
				<tr>
					<th>面談回数</th>
					<td><?php echo h($project['Project']['meeting']); ?></td>
				</tr>
				<?php if ($project['Project']['duty_hours'] != null) : ?>
				<tr>
					<th>勤務時間</th>
					<td><?php echo h($project['Project']['duty_hours']); ?></td>
				</tr>
				<?php endif; ?>
				<tr>
					<th>必須スキル</th>
					<td><?php echo nl2br(h($project['Project']['must_skill'])); ?></td>
				</tr>
				<tr>
					<th>尚可スキル</th>
					<td><?php echo nl2br(h($project['Project']['more_skill'])); ?></td>
				</tr>
				<tr>
					<th>備考</th>
					<td><?php echo h($project['Project']['other']); ?></td>
				</tr>
				</tbody>
			</table>
		</div><!-- conditions -->
		<div class="convert">
			<a href="">話を聞きにいく！</a>
		</div>
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
//********** facebook.js  ***********//
//***********************************//
</script>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

</head>
