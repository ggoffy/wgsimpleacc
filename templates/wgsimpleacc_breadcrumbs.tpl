<ol class='breadcrumb'>
    <li class='breadcrumb-item'><a href='<{xoAppUrl 'index.php'}>' title='home'><i class="fa fa-home"></i></a></li>
    <{if $xoBreadcrumbs|default:''}>
        <{foreach item=itm from=$xoBreadcrumbs name=bcloop}>
        <li class='breadcrumb-item'>
            <{if $itm.link|default:''}>
                <a href='<{$itm.link}>' title='<{$itm.title}>'><{$itm.title}></a>
            <{else}>
                <{$itm.title}>
            <{/if}>
        </li>
        <{/foreach}>
    <{/if}>
</ol>
