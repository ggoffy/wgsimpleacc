<{if $showList|default:''}>
    <{if $tratemplatesCount|default:0 > 0}>
        <h3><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATES_LIST}></h3>
        <div class='table-responsive'>
            <table class='table table-striped'>
                <thead>
                <tr>
                    <th><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_NAME}></th>
                    <th><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_DESC}></th>
                    <th><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_ALLID}></th>
                    <th><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_ACCID}></th>
                    <th><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_ASID}></th>
                    <{if $useClients|default:''}>
                    <th scope="col"><{$smarty.const._MA_WGSIMPLEACC_TRANSACTION_CLIID}></th>
                    <{/if}>
                    <th><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_CLASS}></th>
                    <th><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_AMOUNTIN}></th>
                    <th><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_AMOUNTOUT}></th>
                    <th><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_ONLINE}></th>
                    <th><{$smarty.const._MA_WGSIMPLEACC_SUBMITTER}></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <{foreach item=template from=$tratemplates}>
                    <{include file='db:wgsimpleacc_tratemplates_list.tpl' }>
                    <{/foreach}>
                </tbody>
            </table>
        </div>
    <{else}>
        <{$smarty.const._MA_WGSIMPLEACC_THEREARENT_TRATEMPLATES}>
    <{/if}>
<{/if}>
