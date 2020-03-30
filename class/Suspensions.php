<?php

namespace XoopsModules\Yogurt;

// Suspensions.php,v 1
//  ---------------------------------------------------------------- //
// Author: Bruno Barthez                                               //
// ----------------------------------------------------------------- //

include_once XOOPS_ROOT_PATH . '/kernel/object.php';

/**
 * Suspensions class.
 * $this class is responsible for providing data access mechanisms to the data source
 * of XOOPS user class objects.
 */
class Suspensions extends \XoopsObject
{
    public $db;

    // constructor

    /**
     * Suspensions constructor.
     * @param null $id
     */
    public function __construct($id = null)
    {
        $this->db = \XoopsDatabaseFactory::getDatabaseConnection();
        $this->initVar('uid', XOBJ_DTYPE_INT, null, false, 10);
        $this->initVar('old_pass', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('old_email', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('old_signature', XOBJ_DTYPE_TXTBOX, null, false);
        $this->initVar('suspension_time', XOBJ_DTYPE_INT, null, false, 10);
        if (!empty($id)) {
            if (is_array($id)) {
                $this->assignVars($id);
            } else {
                $this->load((int)$id);
            }
        } else {
            $this->setNew();
        }
    }

    /**
     * @param $id
     */
    public function load($id)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('yogurt_suspensions') . ' WHERE uid=' . $id;
        $myrow = $this->db->fetchArray($this->db->query($sql));
        $this->assignVars($myrow);
        if (!$myrow) {
            $this->setNew();
        }
    }

    /**
     * @param array  $criteria
     * @param bool   $asobject
     * @param string $sort
     * @param string $order
     * @param int    $limit
     * @param int    $start
     * @return array
     */
    public function getAllyogurt_suspensionss($criteria = [], $asobject = false, $sort = 'uid', $order = 'ASC', $limit = 0, $start = 0)
    {
        $db = \XoopsDatabaseFactory::getDatabaseConnection();
        $ret = [];
        $where_query = '';
        if (is_array($criteria) && count($criteria) > 0) {
            $where_query = ' WHERE';
            foreach ($criteria as $c) {
                $where_query .= " $c AND";
            }
            $where_query = mb_substr($where_query, 0, -4);
        } elseif (!is_array($criteria) && $criteria) {
            $where_query = ' WHERE ' . $criteria;
        }
        if (!$asobject) {
            $sql = 'SELECT uid FROM ' . $db->prefix('yogurt_suspensions') . "$where_query ORDER BY $sort $order";
            $result = $db->query($sql, $limit, $start);
            while (false !== ($myrow = $db->fetchArray($result))) {
                $ret[] = $myrow['yogurt_suspensions_id'];
            }
        } else {
            $sql = 'SELECT * FROM ' . $db->prefix('yogurt_suspensions') . "$where_query ORDER BY $sort $order";
            $result = $db->query($sql, $limit, $start);
            while (false !== ($myrow = $db->fetchArray($result))) {
                $ret[] = new self($myrow);
            }
        }

        return $ret;
    }
}
