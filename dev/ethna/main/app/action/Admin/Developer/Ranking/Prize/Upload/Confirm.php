<?php
/**
 *  Admin/Developer/Raking/Prize/Upload/Confirm.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_AdminActionClass.php';

/**
 *  admin_developer_ranking_prize_upload_confirm Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_AdminDeveloperRankingPrizeUploadConfirm extends Pp_AdminActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
        'ranking_id' => array(
            'required' => true,             // Required Option(true/false)
        ),
        'csv' => array(
            'type'     => VAR_TYPE_FILE,    // 入力値型
            'name'     => 'CSVファイル',    // 表示名
            'required' => true,             // Required Option(true/false)
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
		$contents = file_get_contents( $csv['tmp_name'] );
		$rows = explode( "\r\n", $contents );

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

    /**
     *  Form input value convert filter : sample
     *
     *  @access protected
     *  @param  mixed   $value  Form Input Value
     *  @return mixed           Converted result.
     */
    /*
    function _filter_sample($value)
    {
        //  convert to upper case.
        return strtoupper($value);
    }
    */
}

/**
 *  admin_developer_ranking_prize_upload_confirm action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_AdminDeveloperRankingPrizeUploadConfirm extends Pp_AdminActionClass
{
    /**
     *  preprocess of admin_developer_ranking_prize_upload_confirm Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
/*
    function prepare()
    {
        // アクセス権限チェックがあるので必ず基底クラスを呼ぶ事
		$ret = parent::prepare();
		if ($ret) {
			return $ret;
		}

		// 各アクション固有の処理は基底クラスを呼んだ後に行なう
		// ここまで来るとvalidate＆アクセス権限チェック済み
    }
*/

    /**
     *  admin_developer_ranking_prize_upload_confirm action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		return 'admin_developer_ranking_prize_upload_confirm';
    }
}

?>