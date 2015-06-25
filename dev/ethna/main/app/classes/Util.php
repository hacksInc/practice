<?php

/**
 * Utilities
 *
 */
class Util {

	/**
	 * CSV組み立て
	 *
	 * @param array $table  データ配列
	 * @param string $csv  CSVデータ
	 */
	static function assembleCsv($table){
		ob_start();
		$fp = fopen('php://output', 'w');
		foreach ($table as $fields) {
			fputcsv($fp, $fields);
		}
		fclose($fp);
		$csv = ob_get_contents();
		ob_end_clean();

        // 「Excelは'～.csv'がSYLKファイルであることを確認しましたが、読み込むことができません」を回避
        // 1行目の1列目が「ID」だった場合、ダブルクォートで囲む
        if (strncmp($csv, 'ID,', 3) === 0) { // 3はstrlen('ID,')の意
            $csv = '"ID",' . substr($csv, 3);
        }

		// クライアントPC用に改行コードと文字コードを変換
		$csv = str_replace(PHP_EOL, "\r\n", $csv);
		$csv = mb_convert_encoding($csv, 'SJIS');

		return $csv;
	}

	/**
	 * 環境名を取得する
	 *
	 * @return string 環境名("dev" or "stg" or "pro")
	 */
	static function getEnv()
	{
		$server_name = $_SERVER['SERVER_NAME'];

		$server_name = preg_replace('/^main\./',   '', $server_name);
		$server_name = preg_replace('/^review\./', '', $server_name);

		if (strcmp($server_name, 'jmja.jugmon.net') === 0) {
			$env = 'pro';
		} else if (strcmp($server_name, 'mgr.jmja.jugmon.net') === 0) {
			$env = 'pro';
		} else if (strncmp($server_name, 'stg.', 4) === 0) { // 4 == strlen('stg.')
			$env = 'stg';
		} else if (strncmp($server_name, 'dev.', 4) === 0) { // 4 == strlen('dev.')
			$env = 'dev';
		} else if (strncmp($server_name, '192.168.56.', 11) === 0) { // VirtualBoxローカル開発環境
			$env = 'dev';
		} else if ($server_name == "49.212.204.84") { // VirtualBoxローカル開発環境
			$env = 'dev';
		}

		return $env;
	}

    /**
     * 環境名の表示用ラベルを取得する
     *
	 * @return string ラベル("開発環境" or "ステージング環境" or "商用環境")
     */
    static function getEnvLabel($env = null)
    {
        if ($env === null) {
            $env = self::getEnv();
        }

        switch ($env) {
            case 'dev':
                $label = '開発環境';
                break;

            case 'stg':
                $label = 'ステージング環境';
                break;

            case 'pro':
                $label = '商用環境';
                break;

            default:
                $label = null;
        }

        return $label;
    }

	/**
	 * アプリバージョン切り替え用の環境名を取得する
	 *
	 * @return string 環境名("main" or "review")
	 */
	static function getAppverEnv()
	{
		$server_name = $_SERVER['SERVER_NAME'];

error_log( "server_name:" . $server_name );

		if (strncmp($server_name, 'main.', 5) === 0) { // 5 == strlen('main.')
			$appver_env = 'main';
		} else if (strncmp($server_name, 'review.', 7) === 0) { // 7 == strlen('review.')
			$appver_env = 'review';
		} else {
			$appver_env = null;
		}

		return $appver_env;
	}

    /**
     * 管理画面用HTTPホスト名からリソース用HTTPホスト名を取得する
     *
     * 「リソース」は、お知らせなどのアプリから取得されるリソースの事
     * @param string $admin_http_host 管理画面用HTTPホスト名（省略可）
     * @return string リソース用HTTPホスト名
     */
    static function getResourceHttpHostFromAdminHttpHost($admin_http_host = null)
    {
        if ($admin_http_host === null) {
            if (isset($_SERVER['HTTP_HOST'])) {
                $admin_http_host = $_SERVER['HTTP_HOST'];
            }
        }

        $resource_http_host = str_replace(
            array('main.', 'review.', 'mgr.', ':10443'),
            '',
            $admin_http_host
        );

        return $resource_http_host;
    }

	/**
	 * 入力配列から単一のカラムの値の合計を計算する
	 *
	 * @param array $array 値を合計したい多次元配列 (レコードセット)
	 * @param int|string $column_key 値を合計したいカラム (カラムの番号またはキーの名前)
	 * @return int 値の合計
	 */
	static function arrayColumnSum(&$array, $column_key)
	{
		$sum = 0;
		foreach ($array as $assoc) {
			$sum += $assoc[$column_key];
		}

		return $sum;
	}
}

/**
 * スクリプトのパスが一致するか
 *
 * 引数で指定されたパスが、$_SERVER['SCRIPT_NAME']と一致するか判定する。
 * @param string $path スクリプトのパス
 * @param bool $strlen_flg true:strncmpで$pathの長さまでチェックする, false:strcmpで完全一致でチェックする  省略すると、$pathの末尾が"/"ならtrue,"/"以外ならfalseを指定した事になる
 * @param string $script_name $_SERVER['SCRIPT_NAME']以外で判定したい場合はここに指定する
 * @return bool 真否
 */
function script_match($path, $strlen_flg = null, $script_name = null)
{
	if ($strlen_flg === null) {
		$strlen_flg = (substr($path, -1) === '/');
	}

	if ($script_name === null) {
		$script_name = $_SERVER['SCRIPT_URL'];
	}

	if ($strlen_flg) {
		$is_match = (strncmp($script_name, $path, strlen($path)) === 0);
	} else {
		$is_match = (strcmp($script_name, $path) === 0);
	}

	return $is_match;
}
