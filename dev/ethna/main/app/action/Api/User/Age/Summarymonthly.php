<?php
/**
 *  Api/User/Age/Summarymonthly.php
 *
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 */

/**
 *  api_user_age_summarymonthly Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Form_ApiUserAgeSummarymonthly extends Pp_ApiActionForm
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
 *  api_user_age_summarymonthly action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Action_ApiUserAgeSummarymonthly extends Pp_ApiActionClass
{
    /**
     *  preprocess of api_user_age_summarymonthly Action.
     *
     *  @access public
     *  @return string    forward name(null: success.
     *                                false: in case you want to exit.)
     */
    function prepare()
    {
        if ($this->af->validate() > 0) {
            return 'error_400';
        }

        return null;
    }

    /**
     *  api_user_age_summarymonthly action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {
		$user_id = $this->getAuthenticatedBasicAuth('user');
		$user_m =& $this->backend->getManager('User');
		
		$user_base = $user_m->getUserBase($user_id);
		//ƒGƒ‰[
		if ($user_base === false) {
			$this->af->setApp('status_detail_code', SDC_DB_ERROR, true);
			return 'error_500';
		}
		//‘¶Ý‚µ‚È‚¢
		if (count($user_base) == 0) {
			$this->af->setApp('status_detail_code', SDC_USER_NONEXISTENCE, true);
			return 'error_500';
		}

		$payment_m =& $this->backend->getManager('Payment');
		$summary = $payment_m->requestPaymentSummary(
			$user_base['puid'],
			$user_base['app_id'],
			Pp_PaymentManager::PAYMENT_TYPE_IN,
			date('Y-m-d', $_SERVER['REQUEST_TIME']),
			Pp_PaymentManager::SUMMARY_DURATION_TYPE_MONTHLY
		);
		$medal_month = 0;
		if ($summary === false) {
		//	$this->af->setApp('status_detail_code', SDC_PAYMENT_SVR_STATUS_ERROR, true);
			return 'error_500';
		}
		if (!empty($summary)) $medal_month = $summary['coin'];

		$this->af->setApp('medal_month', $medal_month, true);
		
		return 'api_json_encrypt';
    }
}

?>