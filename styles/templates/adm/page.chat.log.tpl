{block name="content"}
<form action="admin.php?page=chat&mode=log" method="post" id="form">
<input type="hidden" name="side" value="{$page}" id="side">
<table class="table table-dark table-striped table-sm fs-12 my-5 mx-auto">
	<tr>
		<th colspan="6">Chat Log</th>
	</tr>
	<tr>
		<td style="width:8%">Username</td>
		<td style="width:17%"><input type="text" name="username" value="{$username}" class="form-control bg-dark text-white border-0"></td>
		<td style="width:8%">Channel</td>
        <td style="width:17%"><input type="text" name="channel" value="{$channel}" class="form-control bg-dark text-white border-0"></td>
		<td style="width:10%">Date Range</td>
		<td style="width:40%">
			<input value="{$dateStart.day|default:''}" type="text" name="dateStart[day]" style="width:25px" maxlength="2" placeholder="dd">.<input value="{$dateStart.month|default:''}" type="text" name="dateStart[month]" style="width:25px" maxlength="2" placeholder="mm">.<input value="{$dateStart.year|default:''}" type="text" name="dateStart[year]" style="width:35px" maxlength="4" placeholder="yyyy">
			&nbsp;-&nbsp;
			<input value="{$dateEnd.day|default:''}" type="text" name="dateEnd[day]" style="width:25px" maxlength="2" placeholder="dd">.<input value="{$dateEnd.month|default:''}" type="text" name="dateEnd[month]" style="width:25px" maxlength="2" placeholder="mm">.<input value="{$dateEnd.year|default:''}" type="text" name="dateEnd[year]" style="width:35px" maxlength="4" placeholder="yyyy">
		</td>
	</tr>
	<tr>
		<th colspan="6" class="center">
			<input type="submit" value="Search">
		</th>
	</tr>
</table>
<table class="table table-dark table-striped table-sm fs-12 my-5 mx-auto">
	<tr>
		<th colspan="5">Chat Log</th>
	</tr>
	<tr style="height: 20px;">
		<td class="right" colspan="5">Page: {if $page != 1}<a href="#" onclick="gotoPage({$page - 1});return false;">&laquo;</a> {/if}{for $site=1 to $maxPage}<a href="#" onclick="gotoPage({$site});return false;">{if $site == $page}<span style="color:orange"><b>[{$site}]</b></span>{else}[{$site}]{/if}</a>{if $site != $maxPage} {/if}{/for}{if $page != $maxPage} <a href="#" onclick="gotoPage({$page + 1});return false;">&raquo;</a>{/if}</td>
	</tr>
	<tr>
		<th style="width:4%">ID</th>
		<th style="width:15%">Time</th>
		<th style="width:15%">Username</th>
		<th style="width:10%">Channel</th>
		<th>Message</th>
	</tr>
	{foreach $logList as $logRow}
	<tr>
		<td>{$logRow.id}</td>
		<td>{$logRow.time}</td>
		<td>{$logRow.username}</td>
		<td>{$logRow.channel}</td>
		<td>{$logRow.text}</td>
	</tr>
	{/foreach}
</table>
</form>
<script>
function gotoPage(page) {
	document.getElementById('side').value = page;
	document.getElementById('form').submit();
}
</script>
{/block}
