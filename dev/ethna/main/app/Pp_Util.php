<?php
/**
 *  Pp_Util.php
 *
 *  @author
 *  @license
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  ユーティリティクラス
 *
 *  @author
 *  @access     public
 *  @package    Ethna
 */
class Pp_Util extends Ethna_Util
{
	/**
	 * 配列から一つ以上の要素の値をランダムに取得する
	 *
	 * エントリを一つだけ取得する場合、ランダムなエントリの値を返します。その他の場合は、ランダムなエントリの値の配列を返します。
	 * @see http://php.net/manual/ja/function.array-rand.php
	 * @param array $array 入力の配列
	 * @param int $num 取得するエントリの数
	 * @return mixed ランダムなエントリ
	 */
	static function arrayRandValues($array, $num = 1)
	{
		$rand = array_rand($array, $num);
		if ($rand === null) {
			return null;
		}

		if ($num == 1) {
			return $array[$rand];
		} else {
			$values = array();
			foreach ($rand as $key) {
				$values[] = $array[$key];
			}

			return $values;
		}
	}

	/**
	 * 入力配列から複数のカラムの値を返す
	 *
	 * @param array $array 値を取り出したい多次元配列 (レコードセット)。
	 * @param array $keys 値を返したいカラムの配列。取得したいカラムの番号を整数値で指定することもできるし、連想配列のキーの名前を指定することもできます。
	 * @return array 入力配列の複数のカラムを表す連想配列の配列を返します。
	 */
	static function arrayMultiColumns($array, $column_keys)
	{
		$trans = array_flip($column_keys);

		$array_subset = array();
		foreach ($array as $array_key => $array_value) {
			$array_subset[$array_key] = array_intersect_key($array_value, $trans);
		}

		return $array_subset;
	}

	/**
	 * カンマ区切りの文字列を数値の配列に置き換える
	 *
	 * 引数が空文字列or偽値の場合は空配列が返る
	 * @param string $csv カンマ区切りの文字列
	 * @return array 数値の配列
	 */
	static function convertCsvToIntArray($csv)
	{
		if (strlen($csv) > 0) {
			$tmp = array_map('intval', explode(',', $csv));
		} else {
			$tmp = array();
		}

		return $tmp;
	}

	/**
	 * 開始日と終了日の日数チェックを行う
	 *
	 * @param string $check_date_from
	 * @param string $check_date_to
	 * @param string $term
	 * @return boolean
	 */
	public function checkDateRange ($check_date_from, $check_date_to, $term){

		$start_timestamp = strtotime($check_date_from);
		$end_timestamp = strtotime($check_date_to);

		$tmp = $end_timestamp - $start_timestamp;
		$max_time = 60 * 60 * 24 * intval($term);

		if($tmp > $max_time){
			return false;
		}
		return true;
	}

	/**
	 * 開始日と終了日の反転チェックを行う
	 *
	 * @param string $check_date_from
	 * @param string $check_date_to
	 * @return boolean
	 */
	public function checkDateReversal ($check_date_from, $check_date_to){

		$in_f = strtotime($check_date_from);
		$in_t = strtotime($check_date_to);

		if ( $in_f > $in_t ){
			return false;
		}
		return true;
	}

	/**
	 * ディレクトリ作成
	 *
	 * @param string $file_path
	 * @return boolean
	 */
	public function mkdir( $file_path )
	{
		if ( !file_exists( $file_path ))
		{
			@mkdir( $file_path, 0777, true );
			@chmod( $file_path, 0777 );
		}
	}
}
