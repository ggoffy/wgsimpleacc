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

use Xmf\Request;
use XoopsModules\Wgsimpleacc;
use XoopsModules\Wgsimpleacc\{
    Constants,
    Utility,
    Export\Simplexlsxgen,
    Export\Simplecsv,
};

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

require __DIR__ . '/header.php';
require_once \XOOPS_ROOT_PATH . '/header.php';
$GLOBALS['xoopsTpl']->assign('template_sub', 'db:wgsimpleacc_outputs.tpl');

foreach ($styles as $style) {
    $GLOBALS['xoTheme']->addStylesheet($style, null);
}

// Permissions
if (!$permissionsHandler->getPermGlobalView()) {
    \redirect_header('index.php', 0);
}

$op        = Request::getCmd('op', 'none');
$traId     = Request::getInt('tra_id');
$traType   = Request::getInt('tra_type');
$allId     = Request::getInt('all_id');
$allSubs   = Request::getInt('allSubs');
$accId     = Request::getInt('acc_id');
$asId      = Request::getInt('as_id');
$cliId     = Request::getInt('cli_id');
$outType   = Request::getString('output_type', 'none');

$GLOBALS['xoopsTpl']->assign('displayfilter', 1);

switch ($op) {
    case 'none':
    default:
        break;
    case 'balances':
        $GLOBALS['xoTheme']->addScript('browse.php?Frameworks/jquery/jquery.js');
        $GLOBALS['xoTheme']->addStylesheet(\WGSIMPLEACC_URL . '/assets/css/nestedcheckboxes.css', null);
        $formFilter = $outputsHandler::getFormBalancesSelect();
        $GLOBALS['xoopsTpl']->assign('formFilter', $formFilter->render());

        // Breadcrumbs
        $xoBreadcrumbs[] = ['title' => \_MA_WGSIMPLEACC_OUTPUTS];
        $xoBreadcrumbs[] = ['title' => \_MA_WGSIMPLEACC_BALANCES];
        break;
    case 'bal_output':
        $GLOBALS['xoopsTpl']->assign('displayBalOutput', 1);
        $balIds       = Request::getArray('balIds');
        if (0 == \count($balIds)) {
            $crBalances = new \CriteriaCompo();
            $balanceFrom = Request::getInt('balanceFrom');
            $balanceTo   = Request::getInt('balanceTo');
            if (0 == $balanceFrom || 0 == $balanceTo) {
                \redirect_header('index.php?op=list', 3, \_MA_WGSIMPLEACC_INVALID_PARAM);
            }
            $crBalances->add(new \Criteria('bal_from', $balanceFrom));
            $crBalances->add(new \Criteria('bal_to', $balanceTo));
            $balancesCount = $balancesHandler->getCount($crBalances);
            if ($balancesCount > 0) {
                $balancesAll = $balancesHandler->getAll($crBalances);
                foreach (\array_keys($balancesAll) as $i) {
                    $balIds[] = $i;
                }
            }
        }
        if (0 == \count($balIds)) {
            \redirect_header('index.php?op=list', 3, \_MA_WGSIMPLEACC_INVALID_PARAM);
        }
        $levelAlloc   = Request::getInt('level_alloc', $helper->getConfig('balance_level_alloc'));
        $levelAccount = Request::getInt('level_account', $helper->getConfig('balance_level_acc'));
        $GLOBALS['xoopsTpl']->assign('buttonBalPdf', true);
        $GLOBALS['xoopsTpl']->assign('balIds', \implode(',', $balIds));
        $GLOBALS['xoopsTpl']->assign('level_alloc', $levelAlloc);
        $GLOBALS['xoopsTpl']->assign('level_account', $levelAccount);

        $balances = $outputsHandler->getListBalances($balIds);
        $sumTotal = 0;
        $sumAmountin = 0;
        $sumAmountout = 0;
        foreach ($balances as $balance) {
            $sumTotal += ($balance['bal_amountend'] - $balance['bal_amountstart']);
            $sumAmountin += $balance['bal_amountstart'];
            $sumAmountout += $balance['bal_amountend'];
        }
        $GLOBALS['xoopsTpl']->assign('balancesTotal', Utility::FloatToString($sumTotal));
        $GLOBALS['xoopsTpl']->assign('balancesAmountIn', Utility::FloatToString($sumAmountin));
        $GLOBALS['xoopsTpl']->assign('balancesAmountOut', Utility::FloatToString($sumAmountout));
        $GLOBALS['xoopsTpl']->assign('balancesCount', \count($balances));
        $GLOBALS['xoopsTpl']->assign('balances', $balances);

        if ($levelAccount > 0) {
            $accounts = $outputsHandler->getListAccountsValues($balIds);
            $sumTotal = 0;
            $sumAmountin = 0;
            $sumAmountout = 0;
            foreach ($accounts as $account) {
                $sumTotal += $account['total_val'];
                $sumAmountin += $account['amountin_val'];
                $sumAmountout += $account['amountout_val'];
            }
            $GLOBALS['xoopsTpl']->assign('accountsTotal', Utility::FloatToString($sumTotal));
            $GLOBALS['xoopsTpl']->assign('accountsAmountIn', Utility::FloatToString($sumAmountin));
            $GLOBALS['xoopsTpl']->assign('accountsAmountOut', Utility::FloatToString($sumAmountout));
            $GLOBALS['xoopsTpl']->assign('accountsCount', \count($accounts));
            $GLOBALS['xoopsTpl']->assign('accounts', $accounts);
        }

        $allocations = [];
        if (Constants::BALANCES_OUT_LEVEL_ALLOC1 === $levelAlloc) {
            $allocations = $outputsHandler->getLevelAllocations($balIds);
        } elseif (Constants::BALANCES_OUT_LEVEL_ALLOC2 === $levelAlloc) {
            $allocations = $outputsHandler->getListAllocationsValues($balIds);
        }
        if ($levelAlloc > 0) {
            $sumTotal = 0;
            $sumAmountin = 0;
            $sumAmountout = 0;
            foreach ($allocations as $allocation) {
                $sumTotal += $allocation['total_val'];
                $sumAmountin += $allocation['amountin_val'];
                $sumAmountout += $allocation['amountout_val'];
            }
            $GLOBALS['xoopsTpl']->assign('allocationsTotal', Utility::FloatToString($sumTotal));
            $GLOBALS['xoopsTpl']->assign('allocationsAmountIn', Utility::FloatToString($sumAmountin));
            $GLOBALS['xoopsTpl']->assign('allocationsAmountOut', Utility::FloatToString($sumAmountout));
            $GLOBALS['xoopsTpl']->assign('allocationsCount', \count($allocations));
            $GLOBALS['xoopsTpl']->assign('allocations', $allocations);
            $GLOBALS['xoopsTpl']->assign('table_type', $helper->getConfig('table_type'));
        }
        break;
    case 'transactions';
        $GLOBALS['xoTheme']->addScript(\WGSIMPLEACC_URL . '/assets/js/forms.js');
        $dateFrom   = \time() - 60*60*24*365;
        $dateTo     = \time();
        $formFilter = $transactionsHandler::getFormFilter($dateFrom, $dateTo, 0, 0, 0, 0, 'tra_output', 0, [], '', 0, 0);
        $GLOBALS['xoopsTpl']->assign('formFilter', $formFilter->render());

        // Breadcrumbs
        $xoBreadcrumbs[] = ['title' => \_MA_WGSIMPLEACC_OUTPUTS];
        $xoBreadcrumbs[] = ['title' => \_MA_WGSIMPLEACC_TRANSACTIONS];
        break;
    case 'tra_output';
        switch ($outType) {
            case 'csv':
            case 'xlsx':
                //$creator = ('' != $GLOBALS['xoopsUser']->getVar('name')) ? $GLOBALS['xoopsUser']->getVar('name') : $GLOBALS['xoopsUser']->getVar('uname');
                $filename  = date('Ymd_H_i_s_', \time()) . \_MA_WGSIMPLEACC_TRANSACTIONS . '.' . $outType;
                $traStatus = Request::getArray('tra_status');
                $traDesc   = Request::getString('tra_desc');

                // Add data
                $crTransactions = new \CriteriaCompo();
                if ($traId > 0) {
                    $crTransactions->add(new \Criteria('tra_id', $traId));
                } else {
                    $dateFrom = \DateTime::createFromFormat(\_SHORTDATESTRING, Request::getString('filterFrom'))->getTimestamp();
                    $dateTo = \DateTime::createFromFormat(\_SHORTDATESTRING, Request::getString('filterTo'))->getTimestamp();
                    $crTransactions->add(new \Criteria('tra_date', $dateFrom, '>='));
                    $crTransactions->add(new \Criteria('tra_date', $dateTo, '<='));
                }
                if ($allId > 0) {
                    if ($allSubs) {
                        $subAllIds = $allocationsHandler->getSubsOfAllocations($allId);
                        $critAllIds = '(' . \implode(',', $subAllIds) . ')';
                        $crTransactions->add(new \Criteria('tra_allid', $critAllIds, 'IN'));
                    } else {
                        $crTransactions->add(new \Criteria('tra_allid', $allId));
                    }
                }
                if ($asId > 0) {
                    $crTransactions->add(new \Criteria('tra_asid', $asId));
                }
                if ($accId > 0) {
                    $crTransactions->add(new \Criteria('tra_accid', $accId));
                }
                if (\count($traStatus) > 0 && '' !== (string)$traStatus[0]) {
                    $critStatus = '(' . \implode(',', $traStatus) . ')';
                    $crTransactions->add(new \Criteria('tra_status', $critStatus, 'IN'));
                } else {
                    $crTransactions->add(new \Criteria('tra_status', Constants::TRASTATUS_DELETED, '>'));
                }
                if ('' != $traDesc) {
                    $crTransactions->add(new \Criteria('tra_desc', $traDesc, 'LIKE'));
                }
                $transactionsCount = $transactionsHandler->getCount($crTransactions);
                $GLOBALS['xoopsTpl']->assign('transactionsCount', $transactionsCount);
                $crTransactions->setSort('tra_id');
                $crTransactions->setOrder('DESC');
                $transactionsAll = $transactionsHandler->getAll($crTransactions);
                if ($transactionsCount > 0) {
                    //add field names
                    if ('xlsx' === $outType) {
                        $data[] = [\_MA_WGSIMPLEACC_TRANSACTION_YEARNB, \_MA_WGSIMPLEACC_TRANSACTION_DESC, \_MA_WGSIMPLEACC_TRANSACTION_REFERENCE,
                            \_MA_WGSIMPLEACC_TRANSACTION_ACCID, \_MA_WGSIMPLEACC_TRANSACTION_ALLID, \_MA_WGSIMPLEACC_TRANSACTION_DATE,
                            \_MA_WGSIMPLEACC_TRANSACTION_AMOUNTIN, \_MA_WGSIMPLEACC_TRANSACTION_AMOUNTOUT, \_MA_WGSIMPLEACC_TRANSACTION_ASID,  \_MA_WGSIMPLEACC_TRANSACTION_STATUS];
                    } else {
                        $data[] = [
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_YEARNB . '"',
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_DESC . '"',
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_REFERENCE . '"',
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_ACCID . '"',
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_ALLID . '"',
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_DATE . '"',
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_AMOUNTIN . '"',
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_AMOUNTOUT . '"',
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_ASID . '"',
                            '"' . \_MA_WGSIMPLEACC_TRANSACTION_STATUS . '"'
                        ];
                    }

                    $transactions = [];
                    // Get All Transactions
                    foreach (\array_keys($transactionsAll) as $i) {
                        $transactions[$i] = $transactionsAll[$i]->getValuesTransactions();
                        if ('xlsx' === $outType) {
                            $data[] = [
                                $transactions[$i]['year'] . '/' . $transactions[$i]['nb'],
                                cleanOutputXlsx($transactions[$i]['desc']),
                                $transactions[$i]['reference'],
                                $transactions[$i]['account'],
                                $transactions[$i]['allocation'],
                                $transactions[$i]['date'],
                                $transactions[$i]['tra_amountin'],
                                $transactions[$i]['tra_amountout'],
                                $transactions[$i]['asset'],
                                $transactions[$i]['status_text']
                            ];
                        } else {
                            $data[] = [
                                '"' . $transactions[$i]['year'] . '/' . $transactions[$i]['nb'] . '"',
                                '"' . cleanOutputCsv($transactions[$i]['desc']) . '"',
                                '"' . cleanOutputCsv($transactions[$i]['reference']) . '"',
                                '"' . $transactions[$i]['account'] . '"',
                                '"' . $transactions[$i]['allocation'] . '"',
                                $transactions[$i]['date'],
                                $transactions[$i]['amountin'],
                                $transactions[$i]['amountout'],
                                '"' . $transactions[$i]['asset'] . '"',
                                '"' . $transactions[$i]['status_text'] . '"'
                            ];
                        }

                    }
                    unset($transactions);
                }
                if ('xlsx' === $outType) {
                    $xlsx = Simplexlsxgen\SimpleXLSXGen::fromArray($data);
                    $xlsx->downloadAs($filename);
                } else {
                    $csv = Simplecsv\SimpleCSV::downloadAs( $data, $filename);
                }
                break;
            case 'none':
            default:
                break;
        }
        break;
}

require __DIR__ . '/footer.php';

/**
 * function to clean output for csv
 *
 * @param $text
 * @return string
 */
function cleanOutputCsv ($text) {
    //replace possible column break in output
    $cleanText = \str_replace(';', ',', $text);

    //convert to utf8
    \mb_convert_encoding($cleanText, 'UTF-8');
    foreach(\mb_list_encodings() as $chr){
        $cleanText = \mb_convert_encoding($cleanText, 'UTF-8', $chr);
    }

    return $cleanText;
}

/**
 * function to clean output for xlsx
 *
 * @param $text
 * @return string
 */
function cleanOutputXlsx ($text) {
    //replace line breaks by blank space
    $cleanText = \str_replace(['<br>', '</p>'], ' ', $text);
    //replace html code by clean char
    return \html_entity_decode($cleanText);
}
