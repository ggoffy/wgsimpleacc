<{include file='db:wgsimpleacc_header.tpl' }>

<div class="wgsa-content">
        <div class="row wgsa-startminheader">
            <div class="col-sm-2 wgsa-startminheader-brand">
                <a class="" href="index.php"><img class="img-responsive" src="<{xoImgUrl}>images/logo.png" alt="Logo"></a>
            </div>
            <{if $indexHeader|default:''}>
                <div class="col-sm-5"><h3><{$indexHeader}></h3></div>
                <div class="col-sm-5">
            <{else}>
                <div class="col-sm-10">
            <{/if}>
                <div class="pull-right">
                    <{if $xoops_isuser|default:false}>
                        <{if !empty($xoops_isadmin)}>
                            <a class="wgsa-startminheader-link" href="<{$xoops_url}>/admin.php"><span class="glyphicon glyphicon-wrench" alt="<{$smarty.const._MA_WGSIMPLEACC_MENUADMIN}>" title="<{$smarty.const._MA_WGSIMPLEACC_MENUADMIN}>"></span></a>
                        <{/if}>
                        <a class="wgsa-startminheader-link" href="<{$xoops_url}>/user.php"><span class="glyphicon glyphicon-user" alt="<{$smarty.const._MA_WGSIMPLEACC_MENUUSER}>" title="<{$smarty.const._MA_WGSIMPLEACC_MENUUSER}>"> <{$currentUser}></span></a>
                        <a class="wgsa-startminheader-link" href="<{$xoops_url}>/notifications.php"><span class="glyphicon glyphicon-info-sign" alt="<{$smarty.const._MA_WGSIMPLEACC_MENUNOTIF}>" title="<{$smarty.const._MA_WGSIMPLEACC_MENUNOTIF}>"></span></a>
                        <{xoInboxCount assign="unreadCount"}> <a class="wgsa-startminheader-link <{if $unreadCount > 0}>wgsa-startminheader-link-important<{/if}>" href="<{$xoops_url}>/viewpmsg.php"><span class="glyphicon glyphicon-envelope" alt="<{$smarty.const._MA_WGSIMPLEACC_MENUINBOX}>" title="<{$smarty.const._MA_WGSIMPLEACC_MENUINBOX}>"></span> <{if $unreadCount > 0}><span class="badge wgsa-startminheader-bagde"><{$unreadCount}></span><{/if}></a>
                        <a class="wgsa-startminheader-link" href="<{$xoops_url}>/user.php?op=logout"><span class="glyphicon glyphicon-off" alt="<{$smarty.const._LOGOUT}>" title="<{$smarty.const._LOGOUT}>"></span></a>
                    <{/if}>
                </div>
            </div>
        </div>
    <div class="wgsa-startmincontent">
        <{if $formLogin|default:''}>
            <div class="clear"></div>
            <div class="row">
                <div class="hidden-xs col-sm-4">&nbsp;</div>
                <div class="col-xs-12 col-sm-4">
                    <div class="errorMsg"><strong><{$errorPerm|default:'no permission'}></strong></div>
                    <{include file='db:system_block_login.tpl'}>
                </div>
                <div class="hidden-xs col-sm-4">&nbsp;</div>
            </div>
        <{else}>
            <{if $show_breadcrumbs|default:''}>
                <div class="row">
                    <{include file='db:wgsimpleacc_breadcrumbs.tpl'}>
                </div>
            <{/if}>
            <div class="row">
                <{if $displayStartminNav|default:false == 'left'}>
                    <div class="col-sm-4 col-md-4 col-lg-4 col-xl-4 wgsa-mainnav"><{include file='db:wgsimpleacc_navbar_startmin.tpl'}></div>
                    <div class="col-sm-8 col-md-8 col-lg-8 col-xl-8 wgsa-maincontent">
                <{else}>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 wgsa-maincontent">
                <{/if}>
                    <{include file=$template_sub}>
                    <{if !empty($error)}>
                        <div class="errorMsg"><strong><{$error}></strong></div>
                    <{/if}>
                    <{if !empty($form)}>
                        <{$form}>
                    <{/if}>
                </div>
            </div>
        <{/if}>
    </div>
</div>


<{if $adv|default:''}>
    <div class="row">
        <div class="col-sm-12"><{$adv}></div>
    </div>
<{/if}>

<{include file='db:wgsimpleacc_footer.tpl' }>
