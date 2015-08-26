
------------------------------------------------
<?php echo h($message).PHP_EOL; ?>
------------------------------------------------

■お問い合わせ内容
<?php echo h($type).PHP_EOL; ?>
貴社名：<?php echo h($company).PHP_EOL; ?>
貴社名(カナ)：<?php echo h($company_kana).PHP_EOL; ?>
ご担当者名：<?php echo h($sei).h($mei).PHP_EOL; ?>
ご担当者名(カナ)：<?php echo h($sei_kana).h($mei_kana).PHP_EOL; ?>
メールアドレス：<?php echo h($email).PHP_EOL; ?>
電話番号：<?php echo h($tel).PHP_EOL; ?>
url：<?php echo h($url).PHP_EOL; ?>

お問い合わせ内容
<?php echo h($content).PHP_EOL; ?>
------------------------------------------------