<?php

// word press 読み込み
require('../../../wp-blog-header.php');

// 入力値チェック
function checkInput($var) {
	if (is_array($var)) {
		return array_map('checkInput', $var);
	} else {
		if (get_magic_quotes_gpc()) {
			$var = stripslashes($var);
		}
		if (preg_match('/\0/', $var)) {
			die('不正な入力です。');
		}
	return $var;
	}
}

// 文字列エスケープ
function h($string) {
	return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// 長文文字列の改行を有効に
function hbr($str){
	echo nl2br(htmlspecialchars($str, ENT_QUOTES, 'UTF-8'));
}

session_start();

$_POST = checkInput($_POST);

// ランダム生成した数値のセッション値とPOST値の照合で不正アクセスチェック
if (isset($_POST['ticket']) && isset($_SESSION['ticket'])) {
	$ticket = $_POST['ticket'];
	if ($ticket != $_SESSION['ticket']) {
		die('不正なアクセスの疑いがあります。');
	}
} else {
	die('不正なアクセスの疑いがあります。');
}


foreach($_POST as $key => $value){
	if(is_array($value)){
		$value = implode("　",$value);
	}
	$_SESSION[$key] = trim($value);
}

// header.phpの読み込み
get_header();
?>
<div class="content">
	<h2>お問い合わせ</h2>
	<div class="form_area">
	<form method="post" action="">
	<table>
			<th>氏名</th>
			<td><?php echo h($_SESSION['name']);?></td>
		</tr>
		<tr>
			<th>氏名（カナ）</th>
			<td><?php echo h($_SESSION['nameKana']);?></td>
		</tr>
		<tr>
			<th>メールアドレス</th>
			<td><?php echo h($_SESSION['email']);?></td>
		</tr>
		<tr>
			<th>電話番号</th>
			<td><?php echo h($_SESSION['tel']);?></td>
		</tr>
		<tr>
			<th>内容</th>
			<td><?php echo hbr($_SESSION['body']);?></td>
		</tr>
		<tr>
			<th>備考</th>
			<td><?php echo hbr($_SESSION['other']);?></td>
		</tr>
	</table>
	<div class="rewrite"><a href="javascript:history.back();">修正する</a></div>
	<div class="confirm">
		<script>
			$(function(){
				var key = '<?php echo h($ticket); ?>';
				$('#ticket').val(key);
			});
		</script>
		<input type="hidden" id="ticket" name="ticket" value="<?php echo h($ticket);?>">
		<input  class="submit" type="submit" value="送信する"/>
	</div>
	</form>
	<!-- form_area --></div>
<!-- content --></div>
<?php get_footer(); ?>