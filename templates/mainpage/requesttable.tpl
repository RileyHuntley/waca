<table class="table table-striped sortable">
	<thead>
		<tr>
			<th data-defaultsort="asc"><span class="hidden-phone">#</span></th>
			<td><!-- zoom --></td>
			<td><!-- comment --></td>
			<th>Request state</th>
			<th><span class="visible-desktop">Email address</span><span class="visible-tablet">Email and IP</span><span class="visible-phone">Request details</span></th>
			<th><span class="visible-desktop">IP address</span></th>
			<th><span class="hidden-phone">Username</span></th>
			<td><!-- ban --></td>
			<td><!-- reserve status --></td>
			<td><!--reserve button--></td>
		</tr>
	</thead>
	<tbody>
		{foreach from=$requests item="r"}
			{include file="request-entry.tpl" request=$r}
		{/foreach}
	</tbody>
</table>