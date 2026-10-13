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
            <form action="clientarea.php?action=productdetails&id={$serviceid}&manage=managedomains" method="post" id="se-bind-domain">
                <input type="hidden" name="token" value="{$token}" />
                <input type="hidden" name="_action" value="bind" />
            </form>
            <form action="clientarea.php?action=productdetails&id={$serviceid}&manage=managedomains" method="post">
                <input type="hidden" name="token" value="{$token}" />
                <input type="hidden" name="_action" value="unbind" />
                <table class="table table-striped se-table">
                    <thead>
                        <tr>
                            <th>{$lang.domain}</th>
                            <th width="60">{$lang.action}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $domains_list as $entry}
                            <tr>
                                <td><a href="https://{$entry|escape}" target="_blank" rel="noopener noreferrer">{$entry|escape}</a></td>
                                <td><button type="submit" name="unbind-domain" value="{$entry|escape}" class="btn btn-danger btn-sm">{$lang.unbind}</button></td>
                            </tr>
                        {foreachelse}
                            <tr>
                                <td colspan="2" class="text-center">{$lang.no_domains}</td>
                            </tr>
                        {/foreach}
                        <tr>
                            <td><input type="text" form="se-bind-domain" class="form-control" name="bind[domain]" value="" placeholder="{$lang.example}" title="{$lang.bind_domain}" /></td>
                            <td><button type="submit" form="se-bind-domain" name="submit" class="btn btn-primary btn-sm">{$lang.bind}</button></td>
                        </tr>
                    </tbody>
                </table>
            </form>
        {/if}
    </div>
</div>

<style>
    {literal}
        .table.se-table > tbody > tr > td {vertical-align: middle;}
    {/literal}
</style>
