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

\defined('\XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class Object Allocations
 */
class Allocations extends \XoopsObject
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initVar('all_id', \XOBJ_DTYPE_INT);
        $this->initVar('all_pid', \XOBJ_DTYPE_INT);
        $this->initVar('all_name', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('all_desc', \XOBJ_DTYPE_TXTAREA);
        $this->initVar('all_online', \XOBJ_DTYPE_INT);
        $this->initVar('all_accounts', \XOBJ_DTYPE_OTHER);
        $this->initVar('all_weight', \XOBJ_DTYPE_INT);
        $this->initVar('all_level', \XOBJ_DTYPE_INT);
        $this->initVar('all_datecreated', \XOBJ_DTYPE_INT);
        $this->initVar('all_submitter', \XOBJ_DTYPE_INT);
    }

    /**
     * @static function &getInstance
     */
    public static function getInstance()
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self();
        }
    }

    /**
     * The new inserted $Id
     * @return integer
     */
    public function getNewInsertedIdAllocations()
    {
        return $GLOBALS['xoopsDB']->getInsertId();
    }

    /**
     * @public function getForm
     * @param int $start
     * @param int $limit
     * @param bool $action
     * @param bool $admin
     * @param string $redir
     * @return \XoopsThemeForm
     */
    public function getFormAllocations($start, $limit, $redir = '', $action = false, $admin = false)
    {
        $helper = \XoopsModules\Wgsimpleacc\Helper::getInstance();
        if (!$action) {
            $action = $_SERVER['REQUEST_URI'];
        }
        // Title
        $title = $this->isNew() ? \_MA_WGSIMPLEACC_ALLOCATION_ADD : \_MA_WGSIMPLEACC_ALLOCATION_EDIT;
        // Get Theme Form
        \xoops_load('XoopsFormLoader');
        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        // Form Table allocations
        $allocationsHandler = $helper->getHandler('Allocations');
        $allPidSelect = new \XoopsFormSelect(\_MA_WGSIMPLEACC_ALLOCATION_PID, 'all_pid', $this->getVar('all_pid'));
        $allPidSelect->addOption(0, ' ');
        $allPidSelect->addOptionArray($allocationsHandler->getList());
        $form->addElement($allPidSelect);
        // Form Text allName
        $form->addElement(new \XoopsFormText(\_MA_WGSIMPLEACC_ALLOCATION_NAME, 'all_name', 50, 255, $this->getVar('all_name')), true);
        // Form Editor TextArea allDesc
        $form->addElement(new \XoopsFormTextArea(\_MA_WGSIMPLEACC_ALLOCATION_DESC, 'all_desc', $this->getVar('all_desc', 'e'), 4, 47));
        // Form Radio Yes/No allOnline
        $allOnline = $this->isNew() ?: $this->getVar('all_online');
        if ($admin) {
            $allOnlineSelect = new \XoopsFormSelect(\_MA_WGSIMPLEACC_ALLOCATION_ONLINE, 'all_online', $allOnline, 3);
            $allOnlineSelect->addOption(Constants::ONOFF_OFFLINE, \_MA_WGSIMPLEACC_ONOFF_OFFLINE);
            $allOnlineSelect->addOption(Constants::ONOFF_ONLINE, \_MA_WGSIMPLEACC_ONOFF_ONLINE);
            $allOnlineSelect->addOption(Constants::ONOFF_HIDDEN, \_MA_WGSIMPLEACC_ONOFF_HIDDEN);
            $form->addElement($allOnlineSelect);
        } else {
            $form->addElement(new \XoopsFormRadioYN(\_MA_WGSIMPLEACC_ALLOCATION_ONLINE, 'all_online', $allOnline));
        }
        if ((bool)$helper->getConfig('use_cascadingacc')) {
            // Form Select allAccounts
            $accountsHandler = $helper->getHandler('Accounts');
            $allAccounts = $this->isNew() ? [] : \unserialize($this->getVar('all_accounts'), ['allowed_classes' => false]);
            $allAccountsSelect = new \XoopsFormSelect(\_MA_WGSIMPLEACC_ALLOCATION_ACCOUNTS, 'all_accounts', $allAccounts, 15, true);
            $allAccountsSelect->setDescription(\_MA_WGSIMPLEACC_ALLOCATION_ACCOUNTS_DESC);
            $accounts = $accountsHandler->getSelectTreeOfAccounts(Constants::CLASS_BOTH);
            foreach ($accounts as $account) {
                $allAccountsSelect->addOption($account['id'], $account['text']);
            }
            $form->addElement($allAccountsSelect, true);
        }

        // Form Text allLevel
        $allLevel = $this->isNew() ? 99 : $this->getVar('all_level');
        if ($admin) {
            $form->addElement(new \XoopsFormText(\_MA_WGSIMPLEACC_ALLOCATION_LEVEL, 'all_level', 50, 255, $allLevel));
        } else {
            $form->addElement(new \XoopsFormHidden('all_level', $allLevel));
        }
        // Form Text allWeight
        $allWeight = $this->isNew() ? 99 : $this->getVar('all_weight');
        // Form Text Date Select allDatecreated
        $allDatecreated = $this->isNew() ?: $this->getVar('all_datecreated');
        // Form Select User allSubmitter
        $allSubmitter = $this->isNew() ? $GLOBALS['xoopsUser']->uid() : $this->getVar('all_submitter');
        if ($admin) {
            $form->addElement(new \XoopsFormText(\_MA_WGSIMPLEACC_ALLOCATION_WEIGHT, 'all_weight', 50, 255, $allWeight));
            $form->addElement(new \XoopsFormTextDateSelect(\_MA_WGSIMPLEACC_DATECREATED, 'all_datecreated', '', $allDatecreated));
            $form->addElement(new \XoopsFormSelectUser(\_MA_WGSIMPLEACC_SUBMITTER, 'all_submitter', false, $allSubmitter));
        } else {
            $form->addElement(new \XoopsFormHidden('all_weight', $allWeight));
            $form->addElement(new \XoopsFormHidden('all_datecreated', $allDatecreated));
            $form->addElement(new \XoopsFormHidden('all_submitter', $allSubmitter));
        }
        // To Save
        $form->addElement(new \XoopsFormHidden('op', 'save'));
        $form->addElement(new \XoopsFormHidden('redir', $redir));
        $form->addElement(new \XoopsFormHidden('start', $start));
        $form->addElement(new \XoopsFormHidden('limit', $limit));
        $form->addElement(new \XoopsFormButtonTray('', \_SUBMIT, 'submit', '', false));
        return $form;
    }

    /**
     * Get Values
     * @param null $keys
     * @param null $format
     * @param null $maxDepth
     * @return array
     */
    public function getValuesAllocations($keys = null, $format = null, $maxDepth = null)
    {
        $helper  = \XoopsModules\Wgsimpleacc\Helper::getInstance();
        $utility = new \XoopsModules\Wgsimpleacc\Utility();
        $ret = $this->getValues($keys, $format, $maxDepth);
        $ret['id']          = $this->getVar('all_id');
        $allocationsHandler = $helper->getHandler('Allocations');
        $allocationsObj = $allocationsHandler->get($this->getVar('all_pid'));
        $ret['pid']         = $allocationsObj->getVar('all_name');
        $ret['name']        = $this->getVar('all_name');
        $ret['desc']        = \strip_tags($this->getVar('all_desc', 'e'));
        $editorMaxchar = $helper->getConfig('editor_maxchar');
        $ret['desc_short']  = $utility::truncateHtml($ret['desc'], $editorMaxchar);
        switch ((int)$this->getVar('all_online')) {
            case Constants::ONOFF_OFFLINE:
            default:
                $ret['online'] = \_NO;
                break;
            case Constants::ONOFF_ONLINE:
                $ret['online'] = \_YES;
                break;
            case Constants::ONOFF_HIDDEN:
                $ret['online'] = \_MA_WGSIMPLEACC_ONOFF_HIDDEN;
                break;
        }
        $ret['level']       = $this->getVar('all_level');
        $ret['weight']      = $this->getVar('all_weight');
        $ret['datecreated'] = \formatTimestamp($this->getVar('all_datecreated'), 's');
        $ret['submitter']   = \XoopsUser::getUnameFromId($this->getVar('all_submitter'));
        return $ret;
    }

    /**
     * Returns an array representation of the object
     *
     * @return array
     */
    public function toArrayAllocations()
    {
        $ret = [];
        $vars = $this->getVars();
        foreach (\array_keys($vars) as $var) {
            $ret[$var] = $this->getVar('"{$var}"');
        }
        return $ret;
    }
}
