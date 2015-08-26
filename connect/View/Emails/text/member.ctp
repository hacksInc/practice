
------------------------------------------------
<?php echo h($message).PHP_EOL; ?>
------------------------------------------------

■ご応募内容
氏名：<?php echo h($sei).h($mei).PHP_EOL; ?>
氏名(カナ)：<?php echo h($sei_kana).h($mei_kana).PHP_EOL; ?>
性別：<?php echo $sex == 1 ? '男性' : '女性'.PHP_EOL; ?>
生年月日：<?php echo h($birth).PHP_EOL; ?>
メールアドレス：<?php echo h($email).PHP_EOL; ?>
電話番号：<?php echo h($tel).PHP_EOL; ?>
最寄駅：<?php echo h($station).PHP_EOL; ?>

得意分野/スキル、ポートフォリオ(URL)等
<?php echo h($have_skill).PHP_EOL; ?>

ご希望の案件/条件など
<?php echo h($hope).PHP_EOL; ?>

その他
<?php echo h($other).PHP_EOL; ?>
------------------------------------------------