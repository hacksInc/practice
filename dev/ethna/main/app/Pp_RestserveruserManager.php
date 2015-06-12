<?php

require_once 'Pp_RestserverManager.php';

class Pp_RestserveruserManager extends Pp_RestserverManager
{
	/** コンストラクタ */
	function __construct(&$backend)
	{
		parent::__construct($backend);

		$this->db_m =& $this->backend->getDB();
		$this->db_m_r =& $this->backend->getDB();
	}

	protected function getWhereClause($table, $id)
	{
		$adminuser_m = $this->backend->getManager('AdminUser');

		$pieces = array();
		$ids = explode(',', urldecode($id));
		$primary_keys = $adminuser_m->getPrimaryKeys($table);
		$cnt = count($primary_keys);

		for ($i = 0; $i < $cnt; $i++)
		{
			if ($adminuser_m->isDateColumnName($primary_keys[$i]))
			{
				$ids[$i] = date('Y-m-d H:i:s', $ids[$i]);
				if (!$ids[$i])
				{
					error_log('ERROR:' . __FILE__ . ':' . __LINE__);
					return false;
				}
			}
			elseif ($adminuser_m->isStringColumnName($primary_keys[$i]))
			{
				if (strlen($ids[$i]) == 0)
				{
					error_log('ERROR:' . __FILE__ . ':' . __LINE__);
					return false;
				}
			}
			else
			{
				if (!is_numeric($ids[$i]))
				{
					error_log('ERROR:' . __FILE__ . ':' . __LINE__ . ':' . $ids[$i]);
					return false;
				}
			}

			$pieces[] = $primary_keys[$i] . " = '" . $ids[$i] . "'";
		}

		$where = implode(' AND ', $pieces);

		return $where;
	}
}
