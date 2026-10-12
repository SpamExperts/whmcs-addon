<div class="card">
    <div class="card-body">
        <h3 class="card-title">{$lang.header}</h3>
        {if $_status && $_status.code==2}
            <div class="alert alert-danger">{$_status.msg|escape}</div>
        {else}
            {if $_status}
                <div class="alert {if $_status.code==1}alert-success{else}alert-danger{/if}">{$_status.msg|escape}</div>
            {/if}
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{$lang.domain}</th>
                        <th>{$lang.incoming_bandwidth_daily}</th>
                        <th>{$lang.incoming_bandwidth_yearly}</th>
                        <th>{$lang.outgoing_bandwidth_daily}</th>
                        <th>{$lang.outgoing_bandwidth_yearly}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $data as $entry}
                        <tr>
                            <td>{$entry[0]|escape}</td>
                            <td>{$entry[2]|escape} B</td>
                            <td>{$entry[3]|escape} B</td>
                            <td>{$entry[4]|escape} B</td>
                            <td>{$entry[5]|escape} B</td>
                        </tr>
                    {foreachelse}
                        <tr>
                            <td colspan="5" class="text-center">{$lang.nothing_to_display}</td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {/if}
    </div>
</div>
