<?php
/**
 *  Api/Resource/Check.php
 *
 *  @author    {$author}
 *  @package   Pp
 *  @version   $Id$
 */

/**
 *  api_resource_check form implementation
 *
 *  @author    {$author}
 *  @access    public
 *  @package   Pp
 */

class Pp_Form_ApiResourceCheck extends Pp_ApiActionForm
{
	/**
	 *  @access private
	 *  @var    array   form definition.
	 */
	var $form = array(
		'c'
	);
}

/**
 *  api_resource_check action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiResourceCheck extends Pp_ApiActionClass
{
	/**
	 *  preprocess api_resource_check action.
	 *
	 *  @access    public
	 *  @return    string  Forward name (null if no errors.)
	 */
	function prepare()
	{
		if( $this->af->validate() > 0 )
		{
			return 'error_400';
		}
		return null;
	}

	/**
	 *  Check action implementation.
	 *
	 *  @access    public
	 *  @return    string  Forward Name.
	 */
	function perform()
	{
		$asset_bundle_data = array();

		// マネージャのインスタンスを取得
		$asset_m =& $this->backend->getManager( 'Assetbundle' );

		try
		{
			$date = date( 'Y-m-d H:i:s', $_SERVER['REQUEST_TIME'] );
			$data = $asset_m->getAssetBundle( $date );
			if( $data === false )
			{	// 取得エラー
				throw new Exception();
			}
			if( empty( $data ))
			{	// 取得データなし
				return array();
			}

			// 最新のバージョンを返す
			$asset_bundle_data = array();
			$temp = array();
			foreach( $data as $v )
			{
				$check_key = $v['dir']."_".$v['file_name'];
				if( !isset( $temp[$check_key] ))
				{	// 新規登録
					$temp[$check_key] = $v;
					continue;
				}

				// 登録済みならバージョンの比較
				if( $v['version'] >= $temp[$check_key]['version'] )
				{	// 以前のバージョンより新しければ差し替える
					$temp[$check_key] = $v;
				}
			}
			$asset_bundle_data = array_values( $temp );

			$result_data['total'] = ( string )count( $asset_bundle_data );
			$result_data['resource'] = $asset_bundle_data;

			$this->af->setApp( 'result_data', $result_data, true );
		}
		catch( Exception $e )
		{
			// 結果データ
			$result_data = array(
				'total' => '0',
				'resource' => '',
			);
			$this->af->setApp( 'result_data', $result_data, true );
		}

		return 'api_json_encrypt';
	}
}

?>
