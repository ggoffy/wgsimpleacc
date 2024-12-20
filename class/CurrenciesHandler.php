<?php

namespace XoopsModules\Wgsimpleacc;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * wgSimpleAcc module for xoops
 *
 * @copyright      2020 XOOPS Project (https://xooops.org)
 * @license        GPL 2.0 or later
 * @package        wgsimpleacc
 * @author         Goffy - XOOPS Development Team - Email:<webmaster@wedega.com> - Website:<https://xoops.wedega.com>
 */

use XoopsModules\Wgsimpleacc;


/**
 * Class Object Handler Currencies
 */
class CurrenciesHandler extends \XoopsPersistableObjectHandler
{
    /**
     * Constructor
     *
     * @param \XoopsDatabase $db
     */
    public function __construct(\XoopsDatabase $db)
    {
        parent::__construct($db, 'wgsimpleacc_currencies', Currencies::class, 'cur_id', 'cur_code');
    }

    /**
     * @param bool $isNew
     *
     * @return object
     */
    public function create($isNew = true)
    {
        return parent::create($isNew);
    }

    /**
     * retrieve a field
     *
     * @param int $id field id
     * @param null fields
     * @return \XoopsObject|null reference to the {@link Get} object
     */
    public function get($id = null, $fields = null)
    {
        return parent::get($id, $fields);
    }

    /**
     * get inserted id
     *
     * @return int reference to the {@link Get} object
     */
    public function getInsertId()
    {
        return $this->db->getInsertId();
    }

    /**
     * Get Count Currencies in the database
     * @param int    $start
     * @param int    $limit
     * @param string $sort
     * @param string $order
     * @return int
     */
    public function getCountCurrencies($start = 0, $limit = 0, $sort = 'cur_id ASC, cur_code', $order = 'ASC')
    {
        $crCountCurrencies = new \CriteriaCompo();
        $crCountCurrencies = $this->getCurrenciesCriteria($crCountCurrencies, $start, $limit, $sort, $order);
        return $this->getCount($crCountCurrencies);
    }

    /**
     * Get All Currencies in the database
     * @param int    $start
     * @param int    $limit
     * @param string $sort
     * @param string $order
     * @return array
     */
    public function getAllCurrencies($start = 0, $limit = 0, $sort = 'cur_id ASC, cur_code', $order = 'ASC')
    {
        $crAllCurrencies = new \CriteriaCompo();
        $crAllCurrencies = $this->getCurrenciesCriteria($crAllCurrencies, $start, $limit, $sort, $order);
        return $this->getAll($crAllCurrencies);
    }

    /**
     * Get Criteria Currencies
     * @param \CriteriaCompo $crCurrencies
     * @param int $start
     * @param int $limit
     * @param string $sort
     * @param string $order
     * @return \CriteriaCompo
     */
    private function getCurrenciesCriteria($crCurrencies, $start, $limit, $sort, $order)
    {
        $crCurrencies->setStart($start);
        $crCurrencies->setLimit($limit);
        $crCurrencies->setSort($sort);
        $crCurrencies->setOrder($order);
        return $crCurrencies;
    }

    /**
     * Set given currency as primary
     * @param int $curId
     * @return bool
     */
    public function setPrimaryCurrencies($curId)
    {
        $helper  = \XoopsModules\Wgsimpleacc\Helper::getInstance();
        $currenciesObj = null;
        $currenciesHandler = $helper->getHandler('Currencies');
        if (isset($curId)) {
            $currenciesObj = $currenciesHandler->get($curId);
        } else {
            \redirect_header('currencies.php', 3, 'missing Id');
        }

        // reset all
        $strSQL = 'UPDATE ' . $GLOBALS['xoopsDB']->prefix('wgsimpleacc_currencies') . ' SET ' . $GLOBALS['xoopsDB']->prefix('wgsimpleacc_currencies') . '.cur_primary = 0';
        $GLOBALS['xoopsDB']->queryF($strSQL);
        // Set Vars
        $currenciesObj->setVar('cue_primary', 1);
        // Insert Data
        if ($currenciesHandler->insert($currenciesObj)) {
            return true;
        }
        return false;

    }

    /**
     * Get primary currency
     * @return int
     */
    public function getPrimaryCurrency()
    {
        $curId = 0;
        $crCurrencies = new \CriteriaCompo();
        $crCurrencies->add(new \Criteria('cur_primary', 1));
        $crCurrencies->setStart();
        $crCurrencies->setLimit(1);
        $currenciesCount = $this->getCount($crCurrencies);
        $currenciesAll = $this->getAll($crCurrencies);
        if ($currenciesCount > 0) {
            foreach (\array_keys($currenciesAll) as $i) {
                $curId = $currenciesAll[$i]->getVar('cur_id');
            }
        }

        return $curId;

    }
}
