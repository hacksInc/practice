<?php
/**
 *  Admin/Developer/Ranking/Prize/Upload/Regist.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_ranking_prize_upload_regist Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingPrizeUploadRegist extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
		'ranking_id' => array(
			'required' => true,
		),
		'csv' => array(
			// フォームの定義
			'type'      => VAR_TYPE_STRING,	// 入力値型
			'form_type' => FORM_TYPE_TEXT,	// フォーム型
			'name'      => 'CSVデータ',		// 表示名
			'required'  => true,			// 必須オプション(true/false)
            'custom'   => 'checkCSV',
		),
    );

    function checkCSV( $name )
    {
		$ranking_prize_m = $this->backend->getManager( 'AdminRankingPrize' );

		// 行のカラム数を取得
		$column_num = count( $ranking_prize_m->LOAD_PRIZE_COLUMNS );

		// CSVファイル読み込み
		$csv = $this->get( $name );
		$rows = explode( "\r\n", $csv );
		foreach( $rows as $i => $row )
		{
			if(( $i === 0 )||( empty( $row ) === true ))
			{	// １行目はヘッダー行なのでスルー、空行は無視
				continue;
			}

			$temp = explode( ',', $row );	// 区切りごとに分ける
			if( count( $temp ) < $column_num )
			{	// カラム数が足りない（多い分にはOK？）
				$this->ae->add( null, "カラム数が足りません" );
				return 'admin_error_400';
			}

			// 数値以外の値が設定されていないかをチェック
			foreach( $temp as $k => $v )
			{
				if( strlen( $v ) === 0 )
				{	// 空のカラムならチェックしない
					continue;
				}
				if( ctype_digit( $v ) === false )
				{
					echo "error[$k]<br>";
					$this->ae->add( null, "数字（整数）以外のパラメータが設定されています" );
					return 'admin_error_400';
				}
			}
		}
    }
}

/**
 *  admin_developer_ranking_prize_upload_regist action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingPrizeUploadRegist extends Pp_AdminActionClass
{
    /**
     *  admin_developer_ranking_prize_upload_regist action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
	function perform()
	{
		$ranking_prize_m =& $this->backend->getManager( 'AdminRankingPrize' );

		$ranking_id = $this->af->get( 'ranking_id' );
		$csv = $this->af->get( 'csv' );

		// トランザクション開始
		$db =& $this->backend->getDB();
		$db->begin();

		$rows = explode( "\n", $csv );		// １行毎に分割
		foreach( $rows as $i => $row )
		{
			if(( $i === 0 )||( empty( $row ) === true ))
			{	// 不要な行は読み飛ばす
				continue;
			}

			$temp = explode( ',', $row );	// 区切りごとに分ける
			$columns = array(
				'ranking_id'       => $ranking_id,
				'distribute_start' => $temp[0],
				'distribute_end'   => $temp[1],
				'prize_type'       => $temp[2],
				'prize_id'         => $temp[3],
				'lv'               => $temp[4],
				'number'           => $temp[5]
			);

			// DBに追加
			$ret = $ranking_prize_m->insertRankingPrize( $columns );
			if( !$ret || Ethna::isError( $ret ))
			{	// エラーならロールバックして終了
				$db->rollback();
				return 'admin_error_500';
			}
		}

		// 問題がなければコミット
		$db->commit();

		$this->af->setApp( 'ranking_id', $ranking_id );

		return 'admin_developer_ranking_prize_upload_regist';
	}
}

?>