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
 * Class Object Templates
 */
class Tratemplates extends \XoopsObject
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->initVar('ttpl_id', \XOBJ_DTYPE_INT);
        $this->initVar('ttpl_name', \XOBJ_DTYPE_TXTBOX);
        $this->initVar('ttpl_desc', \XOBJ_DTYPE_OTHER);
        $this->initVar('ttpl_accid', \XOBJ_DTYPE_INT);
        $this->initVar('ttpl_allid', \XOBJ_DTYPE_INT);
        $this->initVar('ttpl_asid', \XOBJ_DTYPE_INT);
        $this->initVar('ttpl_cliid', \XOBJ_DTYPE_INT);
        $this->initVar('ttpl_class', \XOBJ_DTYPE_INT);
        $this->initVar('ttpl_amountin', \XOBJ_DTYPE_DECIMAL);
        $this->initVar('ttpl_amountout', \XOBJ_DTYPE_DECIMAL);
        $this->initVar('ttpl_online', \XOBJ_DTYPE_INT);
        $this->initVar('ttpl_datecreated', \XOBJ_DTYPE_INT);
        $this->initVar('ttpl_submitter', \XOBJ_DTYPE_INT);
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
     * @return int
     */
    public function getNewInsertedIdTratemplates()
    {
        return $GLOBALS['xoopsDB']->getInsertId();
    }

    /**
     * @public function getForm
     * @param bool $action
     * @return \XoopsThemeForm
     */
    public function getFormTratemplates($action = false)
    {
        $helper = \XoopsModules\Wgsimpleacc\Helper::getInstance();
        if (!$action) {
            $action = $_SERVER['REQUEST_URI'];
        }
        $isAdmin = $GLOBALS['xoopsUser']->isAdmin($GLOBALS['xoopsModule']->mid());
        // Title
        $title = $this->isNew() ? \_MA_WGSIMPLEACC_TRATEMPLATE_ADD : \_MA_WGSIMPLEACC_TRATEMPLATE_EDIT;
        // Get Theme Form
        \xoops_load('XoopsFormLoader');
        $form = new \XoopsThemeForm($title, 'form', $action, 'post', true);
        $form->setExtra('enctype="multipart/form-data"');
        // Form Text tplName
        $form->addElement(new \XoopsFormText(\_MA_WGSIMPLEACC_TRATEMPLATE_NAME, 'ttpl_name', 50, 255, $this->getVar('ttpl_name')));
        // Form Text tplDesc
        $editorConfigs = [];
        if ($isAdmin) {
            $editor = $helper->getConfig('editor_admin');
        } else {
            $editor = $helper->getConfig('editor_user');
        }
        $editorConfigs['name'] = 'ttpl_desc';
        $editorConfigs['value'] = $this->getVar('ttpl_desc', 'e');
        $editorConfigs['rows'] = 5;
        $editorConfigs['cols'] = 40;
        $editorConfigs['width'] = '100%';
        $editorConfigs['height'] = '400px';
        $editorConfigs['editor'] = $editor;
        $traDesc = new \XoopsFormEditor(\_MA_WGSIMPLEACC_TRATEMPLATE_DESC, 'ttpl_desc', $editorConfigs);
        $form->addElement($traDesc);
        // Form Table allocations
        $allocationsHandler = $helper->getHandler('Allocations');
        $tplAllidSelect = new \XoopsFormSelect(\_MA_WGSIMPLEACC_TRATEMPLATE_ALLID, 'ttpl_allid', $this->getVar('ttpl_allid'), 15);
        $allocations = $allocationsHandler->getSelectTreeOfAllocations();
        foreach ($allocations as $allocation) {
            $tplAllidSelect->addOption($allocation['id'], $allocation['text']);
        }
        $form->addElement($tplAllidSelect);
        // Form Table accounts
        $accountsHandler = $helper->getHandler('Accounts');
        $tplAccidSelect = new \XoopsFormSelect(\_MA_WGSIMPLEACC_TRATEMPLATE_ACCID, 'ttpl_accid', $this->getVar('ttpl_accid'), 15);
        $accounts = $accountsHandler->getSelectTreeOfAccounts(Constants::CLASS_BOTH);
        foreach ($accounts as $account) {
            $tplAccidSelect->addOption($account['id'], $account['text']);
        }
        $form->addElement($tplAccidSelect);
        // Form Table assets
        $assetsHandler = $helper->getHandler('Assets');
        $ttplAsidSelect = new \XoopsFormSelect(\_MA_WGSIMPLEACC_TRATEMPLATE_ASID, 'ttpl_asid', $this->getVar('ttpl_asid'));
        $ttplAsidSelect->addOptionArray($assetsHandler->getList());
        $form->addElement($ttplAsidSelect);
        // Form Table clients
        if ($helper->getConfig('use_clients')) {
            $clientsHandler = $helper->getHandler('Clients');
            $crClients = new \CriteriaCompo();
            $crClients->setSort('cli_name');
            $crClients->setOrder('ASC');
            $ttplCliidSelect = new \XoopsFormSelect(\_MA_WGSIMPLEACC_TRANSACTION_CLIID, 'ttpl_cliid', $this->getVar('ttpl_cliid'));
            $ttplCliidSelect->addOption(0, ' ');
            $clientsAll = $clientsHandler->getAll($crClients);
            foreach ($clientsAll as $client) {
                $ttplCliidSelect->addOption($client->getVar('cli_id'), strip_tags($client->getVar('cli_name')));
            }
            $form->addElement($ttplCliidSelect);
        }
        // Form Select ttplClass
        $ttplClass = $this->isNew() ? Constants::CLASS_BOTH : $this->getVar('ttpl_class');
        $traClassSelect = new \XoopsFormRadio(\_MA_WGSIMPLEACC_TRANSACTION_CLASS, 'ttpl_class', $ttplClass);
        $traClassSelect->addOption(Constants::CLASS_BOTH, \_MA_WGSIMPLEACC_CLASS_BOTH);
        $traClassSelect->addOption(Constants::CLASS_EXPENSES, \_MA_WGSIMPLEACC_CLASS_EXPENSES);
        $traClassSelect->addOption(Constants::CLASS_INCOME, \_MA_WGSIMPLEACC_CLASS_INCOME);
        $form->addElement($traClassSelect);
        // Form Text tplAmountin
        $ttplAmountin = Utility::FloatToString($this->getVar('ttpl_amountin'));
        $form->addElement(new \XoopsFormText(\_MA_WGSIMPLEACC_TRATEMPLATE_AMOUNTIN, 'ttpl_amountin', 20, 150, $ttplAmountin));
        // Form Text tplAmountout
        $ttplAmountout = Utility::FloatToString($this->getVar('ttpl_amountout'));
        $form->addElement(new \XoopsFormText(\_MA_WGSIMPLEACC_TRATEMPLATE_AMOUNTOUT, 'ttpl_amountout', 20, 150, $ttplAmountout));
        // Form Radio Yes/No asOnline
        $ttplOnline = $this->isNew() ?: $this->getVar('ttpl_online');
        $form->addElement(new \XoopsFormRadioYN(\_MA_WGSIMPLEACC_TRATEMPLATE_ONLINE, 'ttpl_online', $ttplOnline));
        // Form Text Date Select tplDatecreated
        $ttplDatecreated = $this->isNew() ?: $this->getVar('ttpl_datecreated');
        $form->addElement(new \XoopsFormTextDateSelect(\_MA_WGSIMPLEACC_DATECREATED, 'ttpl_datecreated', '', $ttplDatecreated));
        // Form Select User tplSubmitter
        $form->addElement(new \XoopsFormSelectUser(\_MA_WGSIMPLEACC_SUBMITTER, 'ttpl_submitter', false, $GLOBALS['xoopsUser']->uid()));
        // To Save
        $form->addElement(new \XoopsFormHidden('op', 'save'));
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
    public function getValuesTratemplates($keys = null, $format = null, $maxDepth = null)
    {
        $helper  = \XoopsModules\Wgsimpleacc\Helper::getInstance();
        $ret = $this->getValues($keys, $format, $maxDepth);
        $ret['id']          = $this->getVar('ttpl_id');
        $ret['name']        = $this->getVar('ttpl_name');
        $ret['desc']        = $this->getVar('ttpl_desc', 'e');

        $accountsHandler = $helper->getHandler('Accounts');
        $accountsObj = $accountsHandler->get($this->getVar('ttpl_accid'));
        $accKey    = '';
        $accName   = '';
        $accOnline = 0;
        if (\is_object($accountsObj)) {
            $accKey    = $accountsObj->getVar('acc_key');
            $accName   = $accountsObj->getVar('acc_name');
            $accOnline = (int)(Constants::ONOFF_ONLINE === (int)$accountsObj->getVar('acc_online'));
        }
        $ret['accid']         = $accKey;
        $ret['accname']       = $accName;
        $ret['acconline']     = $accOnline;
        $ret['acconlinetext'] = $accOnline > 0 ? \_MA_WGSIMPLEACC_ONLINE : \_MA_WGSIMPLEACC_OFFLINE;
        $allocationsHandler = $helper->getHandler('Allocations');
        $allocationsObj = $allocationsHandler->get($this->getVar('ttpl_allid'));
        $allName   = '';
        $allOnline = 0;
        if (\is_object($allocationsObj)) {
            $allName   = $allocationsObj->getVar('all_name');
            $allOnline = (int)(Constants::ONOFF_ONLINE === (int)$allocationsObj->getVar('all_online'));
        }
        $ret['allid']         = $allName;
        $ret['allonline']     = $allOnline;
        $ret['allonlinetext'] = $allOnline > 0 ? \_MA_WGSIMPLEACC_ONLINE : \_MA_WGSIMPLEACC_OFFLINE;
        $assetsHandler = $helper->getHandler('Assets');
        $assetsObj = $assetsHandler->get($this->getVar('ttpl_asid'));
        $asName   = '';
        $asOnline = 0;
        if (\is_object($assetsObj)) {
            $asName   = $assetsObj->getVar('as_name');
            $asOnline = (int)(Constants::ONOFF_ONLINE === (int)$assetsObj->getVar('as_online'));
        }
        $ret['asid']         = $asName;
        $ret['asonline']     = $asOnline;
        $ret['asonlinetext'] = $asOnline > 0 ? \_MA_WGSIMPLEACC_ONLINE : \_MA_WGSIMPLEACC_OFFLINE;
        $clientsHandler = $helper->getHandler('Clients');
        $clientsObj = $clientsHandler->get($this->getVar('ttpl_cliid'));
        $cliName   = '';
        $cliOnline = 0;
        if (\is_object($clientsObj)) {
            $cliName   = $clientsObj->getVar('cli_name');
            $cliOnline = (int)(Constants::ONOFF_ONLINE === (int)$clientsObj->getVar('cli_online'));
        }
        $ret['cliid']         = $cliName;
        $ret['clionline']     = $cliOnline;
        $ret['clionlinetext'] = $cliOnline > 0 ? \_MA_WGSIMPLEACC_ONLINE : \_MA_WGSIMPLEACC_OFFLINE;
        $ttplClass        = $this->getVar('ttpl_class');
        $ret['class']     = $ttplClass;
        switch ($ttplClass) {
            case Constants::CLASS_BOTH:
            default:
                $class_text = \_MA_WGSIMPLEACC_CLASS_BOTH;
                break;
            case Constants::CLASS_EXPENSES:
                $class_text = \_MA_WGSIMPLEACC_CLASS_EXPENSES;
                break;
            case Constants::CLASS_INCOME:
                $class_text = \_MA_WGSIMPLEACC_CLASS_INCOME;
                break;
        }
        $ret['class_text']  = $class_text;
        $ret['amountin']    =  Utility::FloatToString($this->getVar('ttpl_amountin'));
        $ret['amountout']   =  Utility::FloatToString($this->getVar('ttpl_amountout'));
        $ret['online']      = (int)$this->getVar('ttpl_online') > 0 ? \_YES : \_NO;
        $ret['datecreated'] = \formatTimestamp($this->getVar('ttpl_datecreated'), 's');
        $ret['submitter']   = \XoopsUser::getUnameFromId($this->getVar('ttpl_submitter'));

        return $ret;
    }

    /**
     * Returns an array representation of the object
     *
     * @return array
     */
    public function toArrayTratemplates()
    {
        $ret = [];
        $vars = $this->getVars();
        foreach (\array_keys($vars) as $var) {
            $ret[$var] = $this->getVar('"{$var}"');
        }

        return $ret;
    }
}
