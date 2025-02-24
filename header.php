<?php
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
use XoopsModules\Wgsimpleacc\Helper;

require \dirname(__DIR__, 2) . '/mainfile.php';
require __DIR__ . '/include/common.php';

global $xoTheme;
$moduleDirName = \basename(__DIR__);

$helper = Helper::getInstance();

// Breadcrumbs
$xoBreadcrumbs = [];
$mname = $helper->getConfig('mname_breadcrumbs');
if ('' !== $mname) {
    $xoBreadcrumbs[] = ['title' => $mname, 'link' => \WGSIMPLEACC_URL . '/'];
}
// Get instance of module
$accountsHandler = $helper->getHandler('Accounts');
$transactionsHandler = $helper->getHandler('Transactions');
$allocationsHandler = $helper->getHandler('Allocations');
$assetsHandler = $helper->getHandler('Assets');
$currenciesHandler = $helper->getHandler('Currencies');
$taxesHandler = $helper->getHandler('Taxes');
$filesHandler = $helper->getHandler('Files');
$permissionsHandler = $helper->getHandler('Permissions');
$balancesHandler = $helper->getHandler('Balances');
$tratemplatesHandler = $helper->getHandler('Tratemplates');
$outtemplatesHandler = $helper->getHandler('Outtemplates');
$outputsHandler = $helper->getHandler('Outputs');
$trahistoriesHandler = $helper->getHandler('Trahistories');
$clientsHandler = $helper->getHandler('Clients');
$processingHandler = $helper->getHandler('Processing');
// 
$myts = MyTextSanitizer::getInstance();

// Smarty Default
$sysPathIcon16   = $GLOBALS['xoopsModule']->getInfo('sysicons16');
$sysPathIcon32   = $GLOBALS['xoopsModule']->getInfo('sysicons32');
$pathModuleAdmin = $GLOBALS['xoopsModule']->getInfo('dirmoduleadmin');
$modPathIcon16   = $GLOBALS['xoopsModule']->getInfo('modicons16');
$modPathIcon32   = $GLOBALS['xoopsModule']->getInfo('modicons16');
// Load Languages
\xoops_loadLanguage('main');
\xoops_loadLanguage('modinfo');

$styles  = [];
$scripts = [];
// Default Css Style
$styles[] = \WGSIMPLEACC_URL . '/assets/css/style.css';
$styles[] = \WGSIMPLEACC_URL . '/assets/css/wgsa_default.css';

$displayStartminNav = (string)$helper->getConfig('displayStartminNav');
if ('left' === $displayStartminNav) {
    $GLOBALS['xoopsOption']['template_main'] = 'wgsimpleacc_main_startmin.tpl';
} else {
    $GLOBALS['xoopsOption']['template_main'] = 'wgsimpleacc_main.tpl';
}

