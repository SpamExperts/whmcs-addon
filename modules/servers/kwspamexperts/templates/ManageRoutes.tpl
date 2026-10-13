<div class="card">
    <div class="card-body">
        <h3 class="card-title">{$lang.header}</h3>
        {if $_result}
            <div class="alert {if $_result.code===1}alert-success{else}alert-danger{/if}">{$_result.msg|escape}</div>
        {/if}
        {if $_status && $_status.code===2}
            <div class="alert alert-danger">{$_status.msg|escape}</div>
        {else}
            {if $_status}
                <div class="alert {if $_status.code===1}alert-success{else}alert-danger{/if}">{$_status.msg|escape}</div>
            {/if}
            <form action="clientarea.php?action=productdetails&id={$serviceid}&manage=ManageRoutes" method="post" id="se-add-route">
                <input type="hidden" name="token" value="{$token}" />
                <input type="hidden" name="_action" value="addroute" />
            </form>
            <form action="clientarea.php?action=productdetails&id={$serviceid}&manage=ManageRoutes" method="post" id="se-delete-route">
                <input type="hidden" name="token" value="{$token}" />
                <input type="hidden" name="_action" value="deleteroute" />
                <input type="hidden" name="routetodel" value="" />
                <input type="hidden" name="routeporttodel" value="" />
            </form>
            <form action="clientarea.php?action=productdetails&id={$serviceid}&manage=ManageRoutes" method="post">
                <input type="hidden" name="token" value="{$token}" />
                <input type="hidden" name="_action" value="deleteroute" />
                <table class="table table-striped se-table">
                    <thead>
                        <tr>
                            <th width="20"><input type="checkbox" class="selectall" title="{$lang.selectall}" /></th>
                            <th>{$lang.destination}</th>
                            <th width="120">{$lang.port}</th>
                            <th width="50">{$lang.action}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $domainroutes as $domainroute}
                            {if $domainroute.0}
                                <tr>
                                    <td><input type="checkbox" class="select-all" name="routes[old_list][]" value="{$domainroute.0|escape}::{$domainroute.2|escape}" /></td>
                                    <td><a href="https://{$domainroute.0|escape}" target="_blank" rel="noopener noreferrer">{$domainroute.0|escape}</a></td>
                                    <td>{$domainroute.2|escape}</td>
                                    <td>
                                        {if $domainroutes|count ne 1}
                                            <button type="button" onclick="onDelete('{$domainroute.0|escape:'javascript'|escape}', '{$domainroute.2|escape:'javascript'|escape}');" class="btn btn-danger btn-sm">{$lang.delete}</button>
                                        {else}
                                            <i class="fas fa-info-circle" title="{$lang.remove_info}"></i>
                                        {/if}
                                    </td>
                                </tr>
                            {/if}
                        {foreachelse}
                            <tr>
                                <td colspan="4" class="text-center">{$lang.nothing}</td>
                            </tr>
                        {/foreach}
                        <tr>
                            <td></td>
                            <td><input type="text" form="se-add-route" class="form-control" name="route[hostname]" value="" placeholder="{$lang.hostname}" title="{$lang.add_route}" /></td>
                            <td><input type="text" form="se-add-route" class="form-control" name="route[port]" value="" placeholder="25" title="{$lang.port_optional}" /></td>
                            <td><button type="submit" form="se-add-route" name="submit" class="btn btn-primary btn-sm">{$lang.add}</button></td>
                        </tr>
                    </tbody>
                </table>
                {if $domainroutes|count > 0}
                    <button type="submit" name="delete-all" onclick="return confirm('{$lang.confirm_delete|escape:'javascript'|escape}');" class="btn btn-danger btn-sm">{$lang.delete}</button>
                {/if}
            </form>
        {/if}
    </div>
</div>

<style>
    {literal}
        .table.se-table > tbody > tr > td {vertical-align: middle;}
    {/literal}
</style>

<script type="text/javascript">
    {literal}
    function onDelete(route, port) {
        if (confirm("{/literal}{$lang.confirm_delete|escape:'javascript'}{literal}")) {
            var form = document.getElementById('se-delete-route');
            form.elements['routetodel'].value = route;
            form.elements['routeporttodel'].value = port;
            form.submit();
        }
    }
    jQuery(function() {
        jQuery("input.selectall").change(function() {
            jQuery("input.select-all").prop("checked", this.checked);
        });
    });
    {/literal}
</script>
