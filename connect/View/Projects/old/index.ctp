<!DOCTYPE HTML>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>ITエンジニアの案件・webデザイナーの求人、フリーランス向け仕事募集情報が集まるサイト</title>
	<meta name="discription" content="">
	<meta name="keywords" content="">
	<link rel="stylesheet" href="http://yui.yahooapis.com/3.17.2/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="css/hacks_list.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/jquery.totemticker.min.js"></script>
</head>
<body>
	<?php foreach ($project as $key) {
		var_dump($key);
	}?>
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
			<a href="" alt="">
				<img src="img/projecthacks22_02.png" width="200" height="50" alt="hacks-FreeLance-">
			</a>
		</div>
		<div class="count">
			<p>現在掲載中の案件/求人情報：<span><?php echo h($count); ?></span>件</p>
		</div>
		<div class="register">
			<a href="/convert">新規無料登録はコチラ！</a>
		</div>
	</div>
	<div class="change">
		<div class="change_info">
			<div class="change_logo">
				<img src="img/projecthacks25_02.png" width="200" height="50" alt="hacks-FreeLance-">
			</div>
			<div class="change_count">
				<p>現在掲載中の案件/求人情報：<span><?php echo h($count); ?></span>件</p>
			</div>
			<div class="change_register">
				<a href="/convert">新規無料登録はコチラ！</a>
			</div>
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
				<li><a href="">ITエンジニア案件・webデザイナー求人、フリーランス向け仕事募集情報</a>></li>
				<li>スキルから案件を探す</li>
		</div>
	</div><!-- header -->
	<?php // echo $this->element('sql_dump'); ?>
	<div id="main">
			<div class="about">
				<div class="explain">
					<span>あなたに合ったピッタリな案件をご紹介致します！</span>
					<p>掲載されている案件はほんの一部！ほとんどが非公開となっているんです。<br>
						あなたのスキルやご希望条件などをヒヤリングさせていただき、あなたにピッタリな案件をご紹介させていただきます。
						また、専属のコンサルタントがしっかりとサポートしますので、フリーランスが初めての方も安心してご相談ください。素朴な疑問にもしっかりとお答えさせていただきます！
						まずは<a href="" alt="無料でご登録">無料でご登録！</a>
					</p>
				</div>
				<div class="flow">
					<img src="img/flow4_02.png" width="100%" height="170" alt="支援サービスの流れ「step1ご登録、step2カウンセリング、step3案件ご紹介、step4面談/参画決定、step5サポート」">
				</div>
			</div>
			<div class="main_contents">
				<div class="contents_title">
					<h2>＊ Javaの案件募集情報</h2>
					<p><?php echo $this->Paginator->counter('検索結果：<span>{:count}件</span>{:start} 〜 {:end} 件目を表示中'); ?></p>
				</div>
				<?php foreach($project as $key ) : ?>
				<?php $value = $key['Project']; ?>
				<div class="project">
						<h3><a href="/projects/<?php echo h($value['id']); ?>" alt=""><?php echo h($value['title']); ?></a></h3>
					<div class="project_content">
						<ul>
							<li>
								<span>最寄駅</span>
								<p><?php echo h($value['station']); ?></p>
							</li>
							<li>
								<span>単価</span>
								<p><?php echo h($value['min_price']); ?> 〜 <?php echo h($value['max_price']); ?> 万円</p>
							</li>
							<li>
								<span>清算</span>
								<p><?php echo h($key['Liquidation']['name']); ?></p>
							</li>
							<li>
								<span>面談回数</span>
								<p><?php echo h($value['meeting']); ?></p>
							</li>
							<li>
								<span>必須スキル</span>
								<p><?php echo h($value['must_skill']); ?></p>
							</li>
							<li>
								<span>契約形態</span>
								<p>業務委託</p>
							</li>
						</ul>
						<div class="text">
							<p>
								テストテストテストテストテストテストテストテストテストテスト
							</p>
						</div>
					</div>
					<div class="btn">
						<div class="more_read">
							<a href="/projects/<?php echo h($value['id']); ?>">詳細はこちらから</a>
						</div>
						<div class="convert">
							<a href="convert" alt="">エントリーする</a>
						</div>
					</div>
				<!-- project --></div>
				<?php endforeach; ?>
			</div><!-- main_contents -->
			<div class="sub_contents">
				<div class="like">
				<div class="fb-like" data-href="https://www.facebook.com/pages/HacksInc/298735600320352?ref=hl" data-width="225" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
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
			</div><!-- sub_contents -->
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
	$content = $(".search"), //#content部分
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
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/ja_JP/sdk.js#xfbml=1&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

</head>
