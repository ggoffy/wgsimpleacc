<!-- Header -->
<{include file='db:wgsimpleacc_admin_header.tpl' }>

<{if $tratemplates_list|default:''}>
	<table class='table table-bordered'>
		<thead>
			<tr class='head'>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_ID}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_NAME}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_DESC}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_ACCID}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_ALLID}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_ASID}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_AMOUNTIN}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_AMOUNTOUT}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_TRATEMPLATE_ONLINE}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_DATECREATED}></th>
				<th class="center"><{$smarty.const._MA_WGSIMPLEACC_SUBMITTER}></th>
				<th class="center width5"><{$smarty.const._MA_WGSIMPLEACC_FORM_ACTION}></th>
			</tr>
		</thead>
		<{if $tratemplates_count|default:0}>
			<tbody>
				<{foreach item=template from=$tratemplates_list}>
					<tr class='<{cycle values='odd, even'}>'>
						<td class='center'><{$template.id}></td>
						<td class='center'><{$template.name}></td>
						<td class='center'><{$template.desc}></td>
						<td class='center'><{$template.accid}></td>
						<td class='center'><{$template.allid}></td>
						<td class='center'><{$template.asid}></td>
						<td class='center'><{$template.amountin}></td>
						<td class='center'><{$template.amountout}></td>
						<td class='center'><{$template.online}></td>
						<td class='center'><{$template.datecreated}></td>
						<td class='center'><{$template.submitter}></td>
						<td class="center  width5">
							<a href="tratemplates.php?op=edit&amp;ttpl_id=<{$template.id}>" title="<{$smarty.const._EDIT}>"><img src="<{xoModuleIcons16 edit.png}>" alt="<{$smarty.const._EDIT}> templates" /></a>
							<a href="tratemplates.php?op=delete&amp;ttpl_id=<{$template.id}>" title="<{$smarty.const._DELETE}>"><img src="<{xoModuleIcons16 delete.png}>" alt="<{$smarty.const._DELETE}> templates" /></a>
						</td>
					</tr>
				<{/foreach}>
			</tbody>
		<{/if}>
	</table>
	<div class="clear">&nbsp;</div>
	<{if $pagenav|default:''}>
		<div class="xo-pagenav floatright"><{$pagenav}></div>
		<div class="clear spacer"></div>
	<{/if}>
<{/if}>
<{if $form|default:''}>
	<{$form}>
<{/if}>
<{if $error|default:''}>
	<div class="errorMsg"><strong><{$error}></strong></div>
<{/if}>

<!-- Footer -->
<{include file='db:wgsimpleacc_admin_footer.tpl' }>
