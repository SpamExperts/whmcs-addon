<h1 style="margin-bottom:5px;">{$lang.header}</h1>
<p style="margin-bottom: 8px;"><a href="clientarea.php?action=productdetails&id={$serviceid}" class="btn btn-small"><i class="icon-arrow-left"></i> {$main_lang.back}</a></p>
{if $_status && $_status.code==2}
    <div class="alert alert-danger">{$_status.msg|escape}</div>
{else}
{if $_status}
    <div class="alert {if $_status.code==1}alert-success{else}alert-danger{/if}">{$_status.msg|escape}</div>
{/if}

<table class="table table-striped table-bordered">
    <tbody>
        <tr>
            <th>{$lang.domain}</th>
            <th>{$lang.incoming_bandwidth_daily}</th>
            <th>{$lang.incoming_bandwidth_yearly}</th>
            <th>{$lang.outgoing_bandwidth_daily}</th>
            <th>{$lang.outgoing_bandwidth_yearly}</th>
        </tr>
    </tbody>
    {foreach $data as $entry}
        <tr>
            <td>{$entry[0]|escape}</td>
            <td>{$entry[2]} B</td>
            <td>{$entry[3]} B</td>
            <td>{$entry[4]} B</td>
            <td>{$entry[5]} B</td>
        </tr>
    {foreachelse}
        <tr>
            <td colspan="5" style="text-align:center;">{$lang.nothing_to_display}</td>
        </tr>
    {/foreach}
</table>
{/if}
