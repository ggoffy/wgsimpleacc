<tr>
	<td scope="col"><{$file.name}></td>
	<td scope="col"><{$file.desc}></td>
	<td scope="col"><{$file.type}></td>
	<td scope="col"><img class='wgsa-files-preview' src='<{$wgsimpleacc_upload_url}>/files/<{$file.name}>' alt='files' /></td>
	<td scope="col"><{$file.datecreated}></td>
	<td scope="col"><{$file.submitter}></td>
	<td scope="col">
		<a class='btn btn-success right' href='files.php?op=show&amp;fil_id=<{$file.fil_id}>&amp;fil_traid=<{$file.fil_traid}>' title='<{$smarty.const._MA_WGSIMPLEACC_DETAILS}>'><{$smarty.const._MA_WGSIMPLEACC_DETAILS}></a>
		<a class='btn btn-primary right' href='files.php?op=edit&amp;fil_id=<{$file.fil_id}>' title='<{$smarty.const._EDIT}>'><{$smarty.const._EDIT}></a>
		<a class='btn btn-danger right' href='files.php?op=delete&amp;fil_id=<{$file.fil_id}>' title='<{$smarty.const._DELETE}>'><{$smarty.const._DELETE}></a>
	</td>
</tr>

