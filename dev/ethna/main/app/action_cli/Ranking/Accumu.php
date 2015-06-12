<?php
/**
 *  累計ランキング集計
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

require_once 'Pp_CliActionClass.php';

/**
 *  ranking_accumu Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_RankingAccumu extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
       /*
        *  TODO: Write form definition which this action uses.
        *  @see http://ethna.jp/ethna-document-dev_guide-form.html
        *
        *  Example(You can omit all elements except for "type" one) :
        *
        *  'sample' => array(
        *      // Form definition
        *      'type'        => VAR_TYPE_INT,    // Input type
        *      'form_type'   => FORM_TYPE_TEXT,  // Form type
        *      'name'        => 'Sample',        // Display name
        *  
        *      //  Validator (executes Validator by written order.)
        *      'required'    => true,            // Required Option(true/false)
        *      'min'         => null,            // Minimum value
        *      'max'         => null,            // Maximum value
        *      'regexp'      => null,            // String by Regexp
        *      'mbregexp'    => null,            // Multibype string by Regexp
        *      'mbregexp_encoding' => 'UTF-8',   // Matching encoding when using mbregexp 
        *
        *      //  Filter
        *      'filter'      => 'sample',        // Optional Input filter to convert input
        *      'custom'      => null,            // Optional method name which
        *                                        // is defined in this(parent) class.
        *  ),
        */
    );

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
 *  ranking_accumu action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_RankingAccumu extends Pp_CliActionClass
{
    /**
     *  preprocess of ranking_accumu Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        return null;
    }

    /**
     *  ranking_accumu action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$type = 1;
		
		if ( $GLOBALS['argc'] < 3 ) {
			// パラメータ不足
			Ethna::raiseError( 'Too few parameter.', E_GENERAL );
			return;
		} else {
			// 第2引数を格納する
			$type	= $GLOBALS['argv'][2];
		}
		
		$ranking_m = $this->backend->getManager('Ranking');
		
		if ($type == 0) {
			$ranking_m->truncateAccumuRanking();
			return null;
		}
		$ranking_cnt = $ranking_m->getCountFromUserbase();
		echo("Rank All=$ranking_cnt");
		
		$ranking_list = array();
		if ($type == 1)
			$ranking_list = $ranking_m->getLampTotalRankingFromUserbase();
		if ($type == 2)
			$ranking_list = $ranking_m->getMonsterTotalRankingFromUserbase();
		
		$rank_row = $rank_disp = 0;
		$score_before = 0;
		
		foreach($ranking_list as $key => $val) {
			echo("Rank=$rank_row ".print_r($val,true));
			$rank_row++;
			//$rank_disp = $rank_row;
			if ($score_before != $val['val1']) {
				$score_before = $val['val1'];
				$rank_disp = $rank_row;
			}
			$columns = array(
				'user_id'   => $val['user_id'],
				'type'      => $type,
				'rank_row'  => $rank_row,
				'rank_disp' => $rank_disp,
				'name'      => (strlen($val['name']) > 0 ? $val['name'] : '774'),
				'val1'      => $val['val1'],
				'val2'      => ($type == 1 ? $val['val2'] : 0),
			);
			$ret = $ranking_m->setAccumuRanking($columns);
		}
		
		return null;
    }
	
}

?>