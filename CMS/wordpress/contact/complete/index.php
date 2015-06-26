<?php
// word press テンプレートの読み込み
require('../../wp-blog-header.php')

get_header('contact');
?>
<div class="complete">
	<p>
		<strong>お問い合わせありがとうございます。</strong><br />
		送信が完了しました。<br />
		後日、担当者よりご連絡いたします。<br /><br />
		※後ほど自動返信メールが送られます。<br />
		時間が経っても届かない場合、お手数ですが再度お問い合わせ頂きますようお願い致します。
	</p>
<!-- complete --></div>

<?php get_footer(); ?>