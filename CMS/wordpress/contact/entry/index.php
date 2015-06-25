<?php

// word press 読み込み
require('../../wp-blog-header.php');

session_start();

// セッション初期化
if( isset($_SESSION) ) {
	$_SESSION = array();
	session_destroy();
	session_start();
}

// ランダムな数字を生成　⇒　次ページで照合する
$ticket = md5(uniqid(mt_rand(), TRUE));
$_SESSION['ticket'] = $ticket;

// 文字列エスケープ
function h($string) {
 	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}


// word pressのheader.php読み込み
get_header();
?>
<div class="content">
	<h2>お問い合わせ</h2>
	<div class="errorbox"><p></p></div>
	<div class="form_area">
	<form method="post" action="" enctype="multipart/form-data">
	<table>
		<tr>
			<th>氏名</th>
			<td><input name="name" type="text" id="name" placeholder="山田　太郎"　required="required" value="<?php echo h($_SESSION['name']);?>" /></td>
		</tr>
		<tr>
			<th>氏名（カナ）</th>
			<td><input name="nameKana" type="text" id="nameKana" placeholder="ヤマダ　タロウ"　required="required"　value="<?php echo h($_SESSION['nameKana']);?>" /></td>
		</tr>
		<tr>
			<th>メールアドレス</th>
			<td>
				<input type="email" name="email" id="email" placeholder="example@example.co.jp" required="required" value="<?php echo h($_SESSION['email']);?>" />
				<div id="emailErr" class="errorbox"><p></p></div>
			</td>
		</tr>
		<tr>
			<th>電話番号</th>
			<td>
				<input type="tel" name="tel" id="tel" placeholder="080-1234-5678" value="<?php echo h($_SESSION['tel']);?>" />
				<div id="telErr" class="errorbox"><p></p></div>
			</td>
		</tr>
		<tr>
			<th>内容<br />（500文字以内）</th>
			<td><textarea name="body" id="body" rows="10"><?php echo h($_SESSION['body']);?></textarea></td>
		</tr>
		<tr>
			<th>備考</th>
			<td><textarea name="other" id="other" rows="8"><?php echo h($_SESSION['other']); ?></textarea></td>
		</tr>
	</table>
	<div class="SubmitForm">
		<script>
			// ランダム生成した数値をPOSTで
			$(function(){
				var key = '<?php echo h($ticket); ?>';
				$('#ticket').val(key);
			});
		</script>
		<input type="hidden" id="ticket" name="ticket">
		<input  class="submit" type="submit" value="同意して確認画面へ進む"/>
	</div>
	</form>
	<!-- form_area --></div>
<!-- content --></div>
<?php get_footer(); ?>