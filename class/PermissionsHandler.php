<?php

declare(strict_types=1);

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
use XoopsModules\Wgsimpleacc\Constants;

\defined('\XOOPS_ROOT_PATH') || die('Restricted access');

/**
 * Class Object PermissionsHandler
 */
class PermissionsHandler extends \XoopsPersistableObjectHandler
{
    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @private function getPermSubmit
     * returns right to submit for given perm
     * @param int $constantPerm
     * @return bool
     */
    private function getPerm($constantPerm)
    {
        global $xoopsUser;

        $moduleDirName = \basename(\dirname(__DIR__));
        /** @var XoopsModuleHandler $module_handler */
        $module_handler = xoops_getHandler('module');
        $xoopsModule    = $module_handler->getByDirname($moduleDirName);

        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }

        $grouppermHandler = \xoops_getHandler('groupperm');
        $mid = $xoopsModule->mid();
        $memberHandler = \xoops_getHandler('member');
        if (0 == $currentuid) {
            $my_group_ids = [\XOOPS_GROUP_ANONYMOUS];
        } else {
            $my_group_ids = $memberHandler->getGroupsByUser($currentuid);
        }
        if ($grouppermHandler->checkRight('wgsimpleacc_ac', $constantPerm, $my_group_ids, $mid)) {
            return true;
        }

        return false;
    }

    /**
     * @private function getPermSubmit
     * returns right to submit for given perm
     * @param int $constantPerm
     * @return bool
     */
    private function getPermSubmit($constantPerm)
    {
        if ($this->getPermGlobalSubmit()) {
            return true;
        }

        return $this->getPerm($constantPerm);
    }

    /**
     * @private function getPermView
     * returns right for view of given perm
     * @param int $constantPerm
     * @return bool
     */
    private function getPermView($constantPerm)
    {
        if ($constantPerm !== Constants::PERM_GLOBAL_VIEW && $this->getPermGlobalView()) {
            return true;
        }

        return $this->getPerm($constantPerm);
    }

    /**
     * @public function permGlobalApprove
     * returns right for global approve
     * @return bool
     */
    public function getPermGlobalApprove()
    {
        return $this->getPerm(Constants::PERM_GLOBAL_APPROVE);
    }

    /**
     * @public function permGlobalSubmit
     * returns right for global submit
     * @return bool
     */
    public function getPermGlobalSubmit()
    {
        return $this->getPerm(Constants::PERM_GLOBAL_SUBMIT);
    }

    /**
     * @public function permGlobalView
     * returns right for global view
     * @return bool
     */
    public function getPermGlobalView()
    {
        if ($this->getPermGlobalSubmit()) {
            return true;
        }

        return $this->getPermView(Constants::PERM_GLOBAL_VIEW);
    }

    /**
     * @public function getPermTransactionsApprove
     * returns right for approve transactions
     * @return bool
     */
    public function getPermTransactionsApprove()
    {
        if ($this->getPermGlobalApprove()) {
            return true;
        }

        return $this->getPerm(Constants::PERM_TRANSACTIONS_APPROVE);

    }

    /**
     * @public function getPermTransactionsSubmit
     * returns right for submit transactions
     * @return bool
     */
    public function getPermTransactionsSubmit()
    {
        if ($this->getPermTransactionsApprove()) {
            return true;
        }

        return $this->getPermSubmit(Constants::PERM_TRANSACTIONS_SUBMIT);
    }

    /**
     * @public function getPermTransactionsEdit
     * returns right for edit/delete transactions
     *  - User must have perm to submit and must be owner
     *  - transaction is not closed
     * @param int $traSubmitter
     * @param int $traStatus
     * @param int $traBalId
     * @return bool
     */
    public function getPermTransactionsEdit($traSubmitter, $traStatus, $traBalId)
    {
        global $xoopsUser, $xoopsModule;

        if ((int)$traBalId > 0) {
            // transaction locked by balance
            return false;
        }

        if ($this->getPermGlobalApprove()) {
            return true;
        }

        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }
        if ($this->getPermTransactionsApprove()) {
            return true;
        }
        if ($this->getPermTransactionsSubmit() && $currentuid == $traSubmitter && Constants::TRASTATUS_SUBMITTED == $traStatus) {
            return true;
        }

        return false;
    }

    /**
     * @public function getPermTransactionsView
     * returns right for view Transactions
     * @return bool
     */
    public function getPermTransactionsView()
    {
        return $this->getPermView(Constants::PERM_TRANSACTIONS_VIEW);
    }

    /**
     * @public function getPermAllocationsSubmit
     * returns right for submit allocations
     * @return bool
     */
    public function getPermAllocationsSubmit()
    {
        return $this->getPermSubmit(Constants::PERM_ALLOCATIONS_SUBMIT);
    }

    /**
     * @public function getPermAllocationsEdit
     * returns right for edit/delete allocations
     *  - User must have perm to submit and must be owner
     * @param int $allSubmitter
     * @return bool
     */
    public function getPermAllocationsEdit($allSubmitter)
    {
        global $xoopsUser, $xoopsModule;

        if ($this->getPermGlobalSubmit()) {
            return true;
        }
        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }
        if ($this->getPermAllocationsSubmit() && $currentuid == $allSubmitter) {
            return true;
        }

        return false;
    }

    /**
     * @public function getPermAllocationsView
     * returns right for view Allocations
     * @return bool
     */
    public function getPermAllocationsView()
    {
        return $this->getPermView(Constants::PERM_ALLOCATIONS_VIEW);
    }

    /**
     * @public function getPermAccountsSubmit
     * returns right for submit accounts
     * @return bool
     */
    public function getPermAccountsSubmit()
    {
        return $this->getPermSubmit(Constants::PERM_ACCOUNTS_SUBMIT);
    }

    /**
     * @public function getPermAccountsEdit
     * returns right for edit/delete accounts
     * - User must have perm to submit and must be owner
     * @param int $accSubmitter
     * @return bool
     */
    public function getPermAccountsEdit($accSubmitter)
    {
        global $xoopsUser, $xoopsModule;

        if ($this->getPermGlobalSubmit()) {
            return true;
        }
        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }
        if ($this->getPermAccountsSubmit() && $currentuid == $accSubmitter) {
            return true;
        }

        return false;
    }

    /**
     * @public function getPermAccountsView
     * returns right for view accounts
     * @return bool
     */
    public function getPermAccountsView()
    {
        return $this->getPermView(Constants::PERM_ACCOUNTS_VIEW);
    }

    /**
     * @public function getPermAssetsSubmit
     * returns right for submit assets
     * @return bool
     */
    public function getPermAssetsSubmit()
    {
        return $this->getPermSubmit(Constants::PERM_ASSETS_SUBMIT);
    }

    /**
     * @public function getPermAssetsEdit
     * returns right for edit/delete assets
     * - User must have perm to submit and must be owner
     * @param int $asSubmitter
     * @return bool
     */
    public function getPermAssetsEdit($asSubmitter)
    {
        global $xoopsUser, $xoopsModule;

        if ($this->getPermGlobalSubmit()) {
            return true;
        }
        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }
        if ($this->getPermAssetsSubmit() && $currentuid == $asSubmitter) {
            return true;
        }

        return false;
    }

    /**
     * @public function getPermAssetsView
     * returns right for view assets
     * @return bool
     */
    public function getPermAssetsView()
    {
        return $this->getPermView(Constants::PERM_ASSETS_VIEW);
    }

    /**
     * @public function getPermFilesEdit
     * returns right for edit/delete files
     * - User must have perm to submit and must be owner
     * @param int $asSubmitter
     * @return bool
     */
    public function getPermFilesEdit($asSubmitter)
    {
        global $xoopsUser, $xoopsModule;

        if ($this->getPermGlobalSubmit()) {
            return true;
        }
        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }
        if ($this->getPermTransactionsSubmit() && $currentuid == $asSubmitter) {
            return true;
        }

        return false;
    }

    /**
     * @public function getPermAssetsView
     * returns right for view assets
     * @return bool
     */
    public function getPermFilesView()
    {
        return $this->getPermView(Constants::PERM_TRANSACTIONS_VIEW);
    }

    /**
     * @public function getPermBalancesSubmit
     * returns right for create balances
     * @return bool
     */
    public function getPermBalancesSubmit()
    {
        return $this->getPermSubmit(Constants::PERM_BALANCES_SUBMIT);
    }

    /**
     * @public function getPermBalancesView
     * returns right for view balances
     * @return bool
     */
    public function getPermBalancesView()
    {
        return $this->getPermView(Constants::PERM_BALANCES_VIEW);
    }

    /**
     * @public function getPermTratemplatesApprove
     * returns right for edit/delete all tratemplates
     * @return bool
     */
    public function getPermTratemplatesApprove()
    {
        if ($this->getPermGlobalApprove()) {
            return true;
        }

        return $this->getPerm(Constants::PERM_TRATEMPLATES_APPROVE);
    }

    /**
     * @public function getPermTratemplatesSubmit
     * returns right for submit tratemplate
     * @return bool
     */
    public function getPermTratemplatesSubmit()
    {
        if ($this->getPermTratemplatesApprove()) {
            return true;
        }

        return $this->getPermSubmit(Constants::PERM_TRATEMPLATES_SUBMIT);
    }

    /**
     * @public function getPermTratemplatesEdit
     * returns right for edit/delete tratemplate
     * - User must have perm to submit and must be owner
     * @param int $tplSubmitter
     * @return bool
     */
    public function getPermTratemplatesEdit($tplSubmitter)
    {
        global $xoopsUser, $xoopsModule;

        if ($this->getPermGlobalSubmit()) {
            return true;
        }
        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }
        if ($this->getPermTratemplatesSubmit() && $currentuid == $tplSubmitter) {
            return true;
        }

        return false;
    }

    /**
     * @public function getPermTratemplatesSubmit
     * returns right for submit tratemplate
     * @return bool
     */
    public function getPermTratemplatesView()
    {
        return $this->getPermView(Constants::PERM_TRATEMPLATES_VIEW);
    }

    /**
     * @public function getPermOuttemplatesApprove
     * returns right for edit/delete all outtemplates
     * @return bool
     */
    public function getPermOuttemplatesApprove()
    {
        if ($this->getPermGlobalApprove()) {
            return true;
        }

        return $this->getPerm(Constants::PERM_OUTTEMPLATES_APPROVE);
    }

    /**
     * @public function getPermOuttemplatesSubmit
     * returns right for submit outtemplate
     * @return bool
     */
    public function getPermOuttemplatesSubmit()
    {
        if ($this->getPermOuttemplatesApprove()) {
            return true;
        }

        return $this->getPermSubmit(Constants::PERM_OUTTEMPLATES_SUBMIT);
    }

    /**
     * @public function getPermOutemplatesEdit
     * returns right for edit/delete own outtemplates
     * - User must have perm to submit and must be owner
     * @param int $tplSubmitter
     * @return bool
     */
    public function getPermOuttemplatesEdit($tplSubmitter)
    {
        global $xoopsUser, $xoopsModule;

        if ($this->getPermGlobalSubmit()) {
            return true;
        }
        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }
        if ($this->getPermOuttemplatesSubmit() && $currentuid == $tplSubmitter) {
            return true;
        }

        return false;
    }

    /**
     * @public function getPermOuttemplatesView
     * returns right for view outtemplate
     * @return bool
     */
    public function getPermOuttemplatesView()
    {
        return $this->getPermView(Constants::PERM_OUTTEMPLATES_VIEW);
    }

    /**
     * @public function getPermClientsAdmin
     * returns right for edit/delete all Clients
     *  - User must have perm to admin
     *
     * @return bool
     */
    public function getPermClientsAdmin()
    {
        if ($this->getPermGlobalApprove()) {
            return true;
        }

        return $this->getPermView(Constants::PERM_CLIENTS_ADMIN);
    }

    /**
     * @public function getPermClientsSubmit
     * returns right for submit Clients
     * @return bool
     */
    public function getPermClientsSubmit()
    {
        if ($this->getPermGlobalApprove()) {
            return true;
        }

        return $this->getPermSubmit(Constants::PERM_CLIENTS_SUBMIT);
    }

    /**
     * @public function getPermClientsEdit
     * returns right for edit/delete Clients
     *  - User must have perm to submit and must be owner
     * @param int $cliSubmitter
     * @return bool
     */
    public function getPermClientsEdit($cliSubmitter)
    {
        global $xoopsUser, $xoopsModule;

        if ($this->getPermGlobalApprove()) {
            return true;
        }
        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }
        if ($this->getPermClientsSubmit() && $currentuid == $cliSubmitter) {
            return true;
        }

        return false;
    }

    /**
     * @public function getPermClientsView
     * returns right for view Clients
     * @return bool
     */
    public function getPermClientsView()
    {
        return $this->getPermView(Constants::PERM_TRANSACTIONS_VIEW);
    }

    /**
     * @public function getPermFileDirAdmin
     * returns right for edit/delete files in file dir
     *  - User must have perm to admin
     *
     * @return bool
     */
    public function getPermFileDirAdmin()
    {
        if ($this->getPermGlobalApprove()) {
            return true;
        }

        return $this->getPerm(Constants::PERM_FILEDIR_ADMIN);
    }

    /**
     * @public function getPermFileDirSubmit
     * returns right for submit files to file dir
     * @param null
     * @return bool
     */
    public function getPermFileDirSubmit()
    {
        if ($this->getPermGlobalApprove()) {
            return true;
        }

        return $this->getPermSubmit(Constants::PERM_FILEDIR_SUBMIT);
    }

    /**
     * @public function getPermFileDirEdit
     * returns right for edit/delete files in file dir
     *  - User must have perm to submit and must be owner
     * @param int $filSubmitter
     * @return bool
     */
    public function getPermFileDirEdit($filSubmitter)
    {
        global $xoopsUser, $xoopsModule;

        if ($this->getPermGlobalApprove()) {
            return true;
        }
        $currentuid = 0;
        if (isset($xoopsUser) && \is_object($xoopsUser)) {
            if ($xoopsUser->isAdmin($xoopsModule->mid())) {
                return true;
            }
            $currentuid = $xoopsUser->uid();
        }
        return $this->getPermFileDirSubmit() && $currentuid == $filSubmitter;
    }

    /**
     * @public function getPermFileDirView
     * returns right for view files from file dir
     * @return bool
     */
    public function getPermFileDirView()
    {
        return $this->getPermView(Constants::PERM_FILEDIR_VIEW);
    }
}
