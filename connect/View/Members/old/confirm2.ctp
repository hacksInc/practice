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
					<img src="img/projecthacks22_02.png" width="200" height="50" alt="hacks-FreeLance-">
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
				echo $this->Form->create('Member', array('action' => 'complete'));
			?>
			<table>
				<tbody>
					<tr class="must">
						<th>氏名</th>
						<td><?php echo $member['sei'].' '.$member['mei']; ?></td>
					</tr>
					<tr class="must">
						<th>氏名（カナ）</th>
						<td><?php echo $member['sei_kana'].' '.$member['mei_kana']; ?></td>
					</tr>
					<tr class="must">
						<th>性別</th>
						<td><?php echo $member['sex'] == 1 ? '男性' : '女性' ; ?></td>
					</tr>
					<tr class="must">
						<th>生年月日</th>
						<td><?php echo $member['year'].'年 '.$member['month'].'月 '.$member['day'].'日'; ?></td>
					</tr>
					<tr class="must">
						<th>メールアドレス</th>
						<td><?php echo $member['email']; ?></td>
					</tr>
					<tr class="must">
						<th>電話番号</th>
						<td><?php echo $member['tel']; ?></td>
					</tr>
					<tr class="must">
						<th>最寄駅</th>
						<td><?php echo $member['station']; ?></td>
					</tr>
					<tr class="better">
						<th>スキルシート</th>
						<td class="skillsheet"><?php echo $member['File']['name']; ?></td>
					</tr>
					<tr class="better">
						<th>
							得意分野/スキル
							<br>
							ポートフォリオ(URL)等
						</th>
						<td><?php echo $member['have_skill']; ?></td>
					</tr>
					<tr class="better">
						<th>ご希望の案件 / 条件等</th>
						<td><?php echo $member['hope']; ?></td>
					</tr>
					<tr class="better">
						<th>その他</th>
						<td><?php echo $member['other']; ?></td>
					</tr>
				</tbody>
			</table>
			<a href="javascript:void(0);" onClick="history.back();">戻る</a>
			<?php echo $this->Form->submit('この内容で応募する', array('class' => 'submit', 'div' => 'register', 'name' => 'complete')); ?>
			<?php echo $this->Form->end(); ?>
		</div>
	</div>
	<div id="footer">
		<p>Copyright(C) hacks Co.,Ltd All Rights Reserved.</p>
	</div>
</body>
</html>