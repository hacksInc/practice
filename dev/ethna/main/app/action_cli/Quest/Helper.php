<?php
/**
 *  助っ人一時データ作成スクリプト
 * 
 *  @author     {$author}
 *  @package    Pp
 *  @version    $Id$
 *  @see https://sites.google.com/a/cave.co.jp/jugmonserver/feature/cron/quest_helper
 */

require_once 'Pp_CliActionClass.php';

/**
 *  QuestHelper Form implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Form_QuestHelper extends Pp_ActionForm
{
    /**
     *  @access private
     *  @var    array   form definition.
     */
    var $form = array(
    );
}


/**
 *  quest_helper action implementation.
 *
 *  @author     {$author}
 *  @access     public
 *  @package    Pp
 */
class Pp_Cli_Action_QuestHelper extends Pp_CliActionClass
{
    /**
     *  preprocess of quest_helper Action.
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
     *  quest_helper action implementation.
     *
     *  @access public
     *  @return string  forward name.
     */
    function perform()
    {

        $this->backend->logger->log(LOG_INFO, "tmp_helper_users作成バッチ START");
        $quest_m = $this->backend->getManager('AdminQuest');
        $ret = $quest_m->makeHelperOthersListBatch();
        if ($ret && Ethna::isError($ret)) {
            $this->backend->logger->log(LOG_ERR, $ret->getMessage());
            $this->backend->logger->log(LOG_ERR, "tmp_helper_users作成バッチ エラー終了");
            $quest_m->rollbackTransaction();
            return;
        }
        $quest_m->commitTransaction();
        $this->backend->logger->log(LOG_INFO, "tmp_helper_users作成バッチ 正常終了");
    }

}

?>