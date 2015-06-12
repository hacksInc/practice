<?php
/**
 * item_therapyのバリデータ用プラグイン
 */
class Pp_Plugin_Validator_Itemtherapy extends Ethna_Plugin_Validator
{
	function validate ( $name, $var, $params )
	{
		$chara_id = $var[0];
		
error_log( print_r( $var, 1 ) );
		
		if ( !isset( $chara_id['chara_id'] ) ) {
			return Ethna::raiseNotice( 'chara_idが存在しません。', E_FORM_INVALIDVALUE );
		}
		
		if ( !is_int( $chara_id['chara_id'] ) || $var['chara_id'] < 0 ) {
			return Ethna::raiseNotice( 'chara_idは正の整数である必要があります。', E_FORM_INVALIDVALUE );
		}
		
		return true;
	}
}
?>