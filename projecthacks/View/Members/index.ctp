<!DOCTYPE HTML>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>ITエンジニアの案件・webデザイナーの求人、フリーランス向け仕事募集情報が集まるサイト</title>
	<meta name="discription" content="">
	<meta name="keywords" content="">
	<link rel="stylesheet" href="http://yui.yahooapis.com/3.17.2/build/cssreset/cssreset-min.css">
	<link rel="stylesheet" href="/css/hacks_convert.css">
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="js/jquery.totemticker.min.js"></script>
</head>
<body>
	<div id="header">
		<div class="header_title">
			<h1>
				ITエンジニアの案件・webデザイナーの求人、フリーランス向け仕事募集情報が集まるサイト
			</h1>
		</div>
		<div class="info">
			<div class="logo">
				<a href="" alt="">
					<img src="/img/projecthacks22_02.png" width="200" height="50" alt="hacks-FreeLance-">
				</a>
			</div>
			<div class="count">
				<p>新規無料登録</p>
			</div>
		</div>
	</div><!-- header -->
	<div id="main">
		<h2>新規無料登録</h2>
		<div class="steps">
		</div>

		<div class="contents">
			<?php
				echo $this->Form->create('Member', array('action'=>'confirm','enctype' => 'multipart/form-data',));
				$this->Form->inputDefaults(array(
			        'div' => false,
			        'label' => false,
			        'id' => false,
			        'legend' => false
			    ));
			?>
			<table>
				<tbody>
					<tr class="must">
						<th>氏名</th>
						<td>
							<?php echo $this->Form->input('sei', array('class' => 'sei', 'placeholder' => '白洲')); ?>
							<?php echo $this->Form->input('mei', array('class' => 'mei', 'placeholder' => '太郎')); ?>
						</td>
					</tr>
					<tr class="must">
						<th>氏名（カナ）</th>
						<td>
							<?php echo $this->Form->input('sei_kana', array('class' => 'sei', 'placeholder' => 'ハクス')); ?>
							<?php echo $this->Form->input('mei_kana', array('class' => 'mei', 'placeholder' => 'タロウ')); ?>
						</td>
					</tr>
					<tr class="must">
						<th>性別</th>
						<td>
							<?php echo $this->Form->input('sex', array('type'=>'radio', 'options' => array(1 => '男性','女性'), 'default' => 1)); ?>
						</td>
					</tr>
					<tr class="must">
						<th>生年月日</th>
						<td>
							
							<?php echo $this->Form->input('year', array('type' => 'select', 'class' => 'year', 'options' => array($year), 'empty' => '年')); ?>
							<?php echo $this->Form->input('month', array('type' => 'select', 'class' => 'month', 'options' => array($month), 'empty' => '月')); ?>
							<?php echo $this->Form->input('day', array('type' => 'select', 'class' => 'day', 'options' => array($day), 'empty' => '日')); ?>
						</td>
					</tr>
					<tr class="must">
						<th>メールアドレス</th>
						<td>
							<?php echo $this->Form->input('email', array('class' => "email", 'placeholder' => 'hogehoge@example.com')); ?>
						</td>
					</tr>
					<tr class="must">
						<th>電話番号</th>
						<td>
							<?php echo $this->Form->input('tel', array('class' => 'tel', 'placeholder' => '08011112222')); ?>
						</td>
					</tr>
					<tr class="must">
						<th>最寄駅</th>
						<td>
							<?php echo $this->Form->input('station', array('class' => 'tel', 'placeholder' => '渋谷駅')); ?>
						</td>
					</tr>
					<tr class="better">
						<th>スキルシート</th>
						<td class="skillsheet">
							<span>
								<?php echo $this->Form->input('File', array('type' => 'file')); ?>
							</span>
							<div>
								スキルシートをお持ちでない方はこちらをご利用下さい。<br>
								<a href="">エンジニア</a>
								|
								<a href="">クリエイター</a>
								|
								<a href="">その他</a>
							</div>
						</td>
					</tr>
					<tr class="better">
						<th>
							得意分野/スキル
							<br>
							ポートフォリオ(URL)等
						</th>
						<td>
							<?php echo $this->Form->input('have_skill', array('type' => 'textarea', 'class' => 'mySkill', 'placeholder' => '例) LAMP経験3年/Cake/CodeIgniter。詳細設計〜可能。ECサイトが得意。リーダー経験あり等', 'rows' => 5)); ?>
						</td>
					</tr>
					<tr class="better">
						<th>ご希望の案件 / 条件等</th>
						<td>
							<?php echo $this->Form->input('hope', array('type' => 'textarea', 'class' => 'necessary', 'placeholder' => '例) 希望する勤務地 / 単価 / 稼働開始日 / 案件内容 / お顔合わせ希望日など', 'rows' => 5)); ?>
						</td>
					</tr>
					<tr class="better">
						<th>その他</th>
						<td>
							<?php echo $this->Form->input('other', array('type' => 'textarea', 'class' => 'necessary','rows' => 3)); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<div class="regulation">
				<h3>個人情報保護方針（プライバシーポリシー）</h3>
				株式会社hacks（以下「当社」）における個人情報の取り扱いについて、下記の内容をご一読頂いた上で、個人情報をご提供頂きますようお願い致します。
				<h4>1.個人情報の定義について</h4>
				個人情報とは、個人に関する情報であって、その情報を構成する氏名、住所、電話番号、メールアドレス、勤務先、生年月日その他の記述等により個人を特定できるものをいいます。
				<h4>2.個人情報の取得と目的について</h4>
				個人情報の取得と利用の目的及び活用範囲は以下のとおりです。<br>
				・当社による当社サービス提供<br>
				・お問い合わせに対する当社からの回答<br>
				・当社サービス利用企業への個人情報提供<br>
				・当社が提供するサービスのご案内や資料の送付<br>
				・お客様により良いサービスをご提供するための調査分析およびアンケート調査実施<br>
				・その他、上記業務に関連又は付随する業務<br>
				　※お預かりした書類については、一部お返しできないことがありますのでご了承ください。<br>


				<h4>3.個人情報を提供しなかった場合に生じる結果について</h4>
				必要となる項目を入力いただかない場合は、本サービスを受けられないことがあります。<br>

				<h4>4.個人情報の第三者提供について</h4>

				(1)<br>
				取得した個人情報について、ご本人の同意を得ずに第三者に提供することは、原則いたしません。提供先および提供する内容を特定した上で、ご本人の同意を得た場合に限り、提供いたします。
				なお、本サービス内で本サービス利用企業の案件に申込みを行った場合は、個人情報の第三者への提供に関して、ご本人の同意を得たものと見なします。提供する個人情報の項目、提供の手段、情報提供を受ける本サービス利用企業の種類、本サービス利用企業との個人情報の取り扱いに関する契約締結状況は以下のとおりです。
				当社が本サービス利用企業に提供するユーザーの個人情報の項目は、氏名、性別、住所、電話番号、メールアドレス、生年月日、ご経験及びご経歴等です。
				書面、電話での口頭伝達、FAX、電磁的記録媒体の受渡し又は電子メール等の電磁的通信手段等で提供を行います。<br>
				(2)<br>
				当社は、本サービス利用に係る債権・債務の特定、支払及び回収のため、ユーザーの氏名、電話番号、口座情報等を必要な範囲で電磁的通信手段等により、金融機関に提供いたします。<br>
				(3)<br>
				以下の場合は、ご本人の同意なく個人情報を提供することがあります。<br>
				・法令に基づく場合<br>
				・人の生命、身体又は財産の保護のために必要がある場合であって、ご本人の同意を得ることが困難である場合<br>
				・公衆衛生の向上又は児童の健全な育成の推進のために特に必要がある場合であって、ご本人の同意を得ることが困難である場合<br>
				・国の機関若しくは地方公共団体又はその委託を受けた者が法令の定める事務を遂行することに協力する必要がある場合であって、ご本人の同意を得ることにより、その事務の遂行に支障を及ぼすおそれがあると当社が判断した場合<br>
				・裁判所、検察庁、警察、弁護士会、消費者センター又はこれらに準じた権限を有する機関から、個人情報についての開示を求められた場合<br>
				・ご本人から明示的に第三者への提供を求められた場合<br>
				・合併その他の事由による事業の承継に伴って個人情報が提供される場合<br>

				<h4>5.個人情報の委託について</h4>
				当社は利用目的の達成に必要な範囲内で、個人情報の取り扱いの全部又は一部を委託する場合があります。なお、個人情報の取り扱いを委託する場合は適切な委託先を選定し、個人情報が安全に管理されるよう適切に監督いたします。<br>

				<h4>6.機微情報の収集制限について</h4>
				当社は、原則として、以下各号に定める機微な情報（以下「機微情報」といいます。）を収集しません。ただし、ご本人自ら、当社に対して機微情報を提供した場合は、当社が当該機微情報を取得すること、及び適切な案件情報提供のために必要な範囲内において当該機微情報を第三者に提供することにつき、ご本人の同意があったものとみなします。<br>
				思想、信条又は宗教に関する事項<br>
				人種、民族、門地、本籍地（所在都道府県に関する情報を除く。）、身体・精神障害、犯罪歴その他社会的差別の原因となる事項<br>
				勤労者の団結権、団体交渉その他団体行動の行為に関する事項<br>
				集団示威行為への参加、請願権の講師その他の政治的権利の行使に関する事項<br>
				保健医療又は性生活に関する事項<br>

				<h4>7.情報の加工について</h4>
				当社は提供を受けた個人情報をもとに、個人を特定できないよう加工した統計データを作成することがあります。個人を特定できない統計データについては、当社は何ら制限なく利用することができるものとします。
				また当社は、取得した個人情報のうち、個人を特定できる情報以外の情報を加工し、当社が編集又は発行する各種媒体その他において利用できるものとします。この場合、各種媒体その他で利用された当該情報に関する著作権その他一切の財産的権利は、当社に帰属します。

				<h4>8. 個人情報の管理について</h4>
				当社では、お客様によって入力された個人情報が傍受・妨害または改ざんされることを防ぐためにSSL（Secure Sockets Layer）技術を使用し、情報を暗号化して通信しております。また当社は、お客様の個人情報を保護・管理するにあたり、外部からの不正なアクセス、個人情報の紛失・破壊・改ざん・漏えいなどを防ぐための適切な安全対策を行っております。

				<h4>9. クッキー（Cookies）について</h4>
				当社のウェブサイトでは、お客様に当社のサービスを便利にご利用いただくためにクッキーと呼ばれる機能を使用することがあります。この機能はお客様のプライバシーを侵害するものではなく、またお客さまのコンピューターへ悪影響を及ぼすこともありません。

				<h4>10. 個人情報保護の取り組みについて</h4>
				当社は、法令等の改正や社会情勢の変化に応じ適宜見直しを行い、改善を図ってまいります。

				<h4>11. お問合わせ先について</h4>
				お客様の個人情報の管理等についてのお問い合わせは、info@hacks.co.jpまでお願い致します。
			</div>
			<?php echo $this->Form->submit('同意して登録する', array('class' => 'submit', 'div' => 'register')); ?>
			<?php echo $this->Form->end(); ?>
		</div>
	</div>
	<div id="footer">
		<p>Copyright(C) hacks Co.,Ltd All Rights Reserved.</p>
	</div>
</body>
</html>