<?php
/**
 * WordPress の基本設定
 *
 * このファイルは、MySQL、テーブル接頭辞、秘密鍵、ABSPATH の設定を含みます。
 * より詳しい情報は {@link http://wpdocs.sourceforge.jp/wp-config.php_%E3%81%AE%E7%B7%A8%E9%9B%86 
 * wp-config.php の編集} を参照してください。MySQL の設定情報はホスティング先より入手できます。
 *
 * このファイルはインストール時に wp-config.php 作成ウィザードが利用します。
 * ウィザードを介さず、このファイルを "wp-config.php" という名前でコピーして直接編集し値を
 * 入力してもかまいません。
 *
 * @package WordPress
 */

// 注意: 
// Windows の "メモ帳" でこのファイルを編集しないでください !
// 問題なく使えるテキストエディタ
// (http://wpdocs.sourceforge.jp/Codex:%E8%AB%87%E8%A9%B1%E5%AE%A4 参照)
// を使用し、必ず UTF-8 の BOM なし (UTF-8N) で保存してください。

// ** MySQL 設定 - この情報はホスティング先から入手してください。 ** //
/** WordPress のためのデータベース名 */
define('DB_NAME', 'wp_cavesystems');

/** MySQL データベースのユーザー名 */
define('DB_USER', 'cvsys_admin');

/** MySQL データベースのパスワード */
define('DB_PASSWORD', 'VtgS29vR');

/** MySQL のホスト名 */
define('DB_HOST', 'localhost');

/** データベースのテーブルを作成する際のデータベースの文字セット */
define('DB_CHARSET', 'utf8');

/** データベースの照合順序 (ほとんどの場合変更する必要はありません) */
define('DB_COLLATE', '');

/**#@+
 * 認証用ユニークキー
 *
 * それぞれを異なるユニーク (一意) な文字列に変更してください。
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org の秘密鍵サービス} で自動生成することもできます。
 * 後でいつでも変更して、既存のすべての cookie を無効にできます。これにより、すべてのユーザーを強制的に再ログインさせることになります。
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '>gmwn.U=W_,Fk:mMSg@9S+~#)- |K[U*<.jkl-asl70=RI.I<RG$lvs:p1trT6rW');
define('SECURE_AUTH_KEY',  'ctTUH]W&_{R/m3t7J=4IbNO6hksVHZx9;-w=J-L1/2nMOo(*E.`1@zW+vRC)vo6m');
define('LOGGED_IN_KEY',    '>1N?{:[M4hum@=m~/PNEU6/-^cT@MXW+aWJgJ8~h!Zw{+|3gemppiKl|c):-+|Pt');
define('NONCE_KEY',        'LTAZ?m2{5<NV-?A0T.GP|om8/xz0^p~)VO#8bf=mrp0v~PH{XE0oo.DSJ,OO$C8X');
define('AUTH_SALT',        '%+W{I^)?C<U8js2zMTc:7%9BP*V-t0>9WR!vbL@$V!!*?Sxv$/9RnxL3-5?oL`$|');
define('SECURE_AUTH_SALT', 'IplvQ0FvBr][n$De~h+1|A;v_K0BGp.t1PINYW}tejA?]&%1S$Nw~c63Fn_=`th|');
define('LOGGED_IN_SALT',   'AWv1Kstk*-t+Jy_teku~eGD=y[H1*ex+THiqUK/3V{KYwCqK(.2V;+zoe$%6?-Tn');
define('NONCE_SALT',       'X+a|x-e],aVAEIj7Q8T!GC&7_}}A-LwBmC<cJHc~HM&+CrE+K3z26KfO(,?V76f+');

/**#@-*/

/**
 * WordPress データベーステーブルの接頭辞
 *
 * それぞれにユニーク (一意) な接頭辞を与えることで一つのデータベースに複数の WordPress を
 * インストールすることができます。半角英数字と下線のみを使用してください。
 */
$table_prefix  = 'cvsys_';

/**
 * 開発者へ: WordPress デバッグモード
 *
 * この値を true にすると、開発中に注意 (notice) を表示します。
 * テーマおよびプラグインの開発者には、その開発環境においてこの WP_DEBUG を使用することを強く推奨します。
 */
define('WP_DEBUG', false);

/* 編集が必要なのはここまでです ! WordPress でブログをお楽しみください。 */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
