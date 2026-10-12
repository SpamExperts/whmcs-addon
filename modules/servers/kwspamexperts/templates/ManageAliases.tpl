<div class="card">
    <div class="card-body">
        <h3 class="card-title">{$lang.header}</h3>
        {if $_result}
            <div class="alert {if $_result.code==1}alert-success{else}alert-danger{/if}">{$_result.msg|escape}</div>
        {/if}
        {if $_status && $_status.code==2}
            <div class="alert alert-danger">{$_status.msg|escape}</div>
        {else}
            {if $_status}
                <div class="alert {if $_status.code==1}alert-success{else}alert-danger{/if}">{$_status.msg|escape}</div>
            {/if}
            <form action="clientarea.php?action=productdetails&id={$serviceid}&manage=ManageAliases" method="post" id="se-add-alias">
                <input type="hidden" name="token" value="{$token}" />
                <input type="hidden" name="_action" value="addalias" />
            </form>
            <form action="clientarea.php?action=productdetails&id={$serviceid}&manage=ManageAliases" method="post">
                <input type="hidden" name="token" value="{$token}" />
                <input type="hidden" name="_action" value="deletealias" />
                <table class="table table-striped se-table">
                    <thead>
                        <tr>
                            <th width="20"><input type="checkbox" class="selectall" title="{$lang.selectall}" /></th>
                            <th>{$lang.alias}</th>
                            <th width="50">{$lang.action}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $domainaliases as $domainalias}
                            <tr>
                                <td><input type="checkbox" class="select-all" name="alias[old_list][]" value="{$domainalias|escape}" /></td>
                                <td><a href="https://{$domainalias|escape}" target="_blank" rel="noopener noreferrer">{$domainalias|escape}</a></td>
                                <td><button type="submit" name="delete-item[{$domainalias|escape}]" onclick="return confirm('{$lang.confirm_delete|escape:'javascript'|escape}');" class="btn btn-danger btn-sm">{$lang.delete}</button></td>
                            </tr>
                        {foreachelse}
                            <tr>
                                <td colspan="3" class="text-center">{$lang.nothing}</td>
                            </tr>
                        {/foreach}
                        <tr>
                            <td></td>
                            <td><input type="text" form="se-add-alias" class="form-control" name="alias[new]" value="" placeholder="exampledomain.com" title="{$lang.add_alias}" /></td>
                            <td><button type="submit" form="se-add-alias" name="submit" class="btn btn-primary btn-sm">{$lang.add}</button></td>
                        </tr>
                    </tbody>
                </table>
                {if $domainaliases|count > 0}
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
    jQuery(function() {
        jQuery("input.selectall").change(function() {
            jQuery("input.select-all").prop("checked", this.checked);
        });
    });
    {/literal}
</script>
